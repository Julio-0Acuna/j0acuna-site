#!/usr/bin/env bash
# Pruebas del formulario de /contacto con php -S. El correo se escribe en un directorio temporal.
cd "$(dirname "$0")/.." || exit 1

PORT=8097
BASE="http://127.0.0.1:$PORT"
TMP=$(mktemp -d)
export PORTFOLIO_SITE_FILE="tests/fixtures/site.json"
export PORTFOLIO_SITE_URL="$BASE"
export CONTACTO_MODO=archivo
export CONTACTO_DIR="$TMP/correos"
export CONTACTO_LIMITE="$TMP/limite.json"

tools/php.sh -S 127.0.0.1:$PORT tools/router.php > /tmp/contacto_server.log 2>&1 &
SERVER_PID=$!
trap 'kill $SERVER_PID 2>/dev/null; powershell -NoProfile -Command "Get-NetTCPConnection -LocalPort '"$PORT"' -State Listen -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id \$_.OwningProcess -Force }" >/dev/null 2>&1; rm -rf "$TMP"' EXIT
for _ in 1 2 3 4 5 6 7 8 9 10; do curl -s -o /dev/null "$BASE/" && break; sleep 0.3; done

OK=0; FAIL=0
pasa() { OK=$((OK + 1)); echo "  ok   $*"; }
falla() { FAIL=$((FAIL + 1)); echo "  FALLA $*"; }
revisa() { if eval "$2"; then pasa "$1"; else falla "$1"; fi; }

JAR="$TMP/cookies.txt"
HTML=$(curl -s -c "$JAR" -b "$JAR" "$BASE/contacto")
revisa 'formulario visible' '[[ "$HTML" == *"id=\"formulario\""* ]]'
revisa 'campo acepto' '[[ "$HTML" == *"name=\"acepto\""* ]]'
revisa 'trampa oculta' '[[ "$HTML" == *"name=\"sitio_web\""* ]]'
revisa 'aviso de privacidad' '[[ "$HTML" == *"Uso tus datos solo para responderte"* ]]'
TOKEN=$(printf '%s' "$HTML" | grep -oE 'name="csrf_token" value="[a-f0-9]+"' | sed -E 's/.*value="([a-f0-9]+)"/\1/')
revisa 'token CSRF presente' '[ -n "$TOKEN" ]'

CODE=$(curl -s -o /dev/null -w "%{http_code}" -b "$JAR" -c "$JAR" --data-urlencode "nombre=Ana" --data-urlencode "correo=ana@example.cl" --data-urlencode "mensaje=Hola" --data-urlencode "acepto=1" "$BASE/contacto")
revisa 'POST sin token rechazado (403)' '[ "$CODE" = "403" ]'

CODE=$(curl -s -o /dev/null -w "%{http_code}" -b "$JAR" -c "$JAR" --data-urlencode "csrf_token=$TOKEN" --data-urlencode "nombre=Ana" --data-urlencode "correo=ana@example.cl" --data-urlencode "mensaje=Hola" --data-urlencode "acepto=1" "$BASE/contacto")
revisa 'envio demasiado rapido no manda correo' '[ ! -d "$CONTACTO_DIR" ] || [ -z "$(ls -A "$CONTACTO_DIR")" ]'

sleep 3
RESP=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" -b "$JAR" -c "$JAR" --data-urlencode "csrf_token=$TOKEN" --data-urlencode "nombre=Ana Pérez" --data-urlencode "negocio=Pizzería" --data-urlencode "correo=ana@example.cl" --data-urlencode "mensaje=Quiero una web" --data-urlencode "acepto=1" --data-urlencode "sitio_web=" "$BASE/contacto")
revisa 'envio valido redirige (303)' '[[ "$RESP" == 303* ]]'
revisa 'redirige a enviado=1' '[[ "$RESP" == *"enviado=1"* ]]'
revisa 'correo escrito' '[ "$(ls "$CONTACTO_DIR" 2>/dev/null | wc -l)" = "1" ]'
revisa 'correo va a Julio' 'grep -q "To: prueba@example.com" "$CONTACTO_DIR"/*.eml'
revisa 'correo trae el mensaje' 'grep -q "Quiero una web" "$CONTACTO_DIR"/*.eml'

OKHTML=$(curl -s -b "$JAR" "$BASE/contacto?enviado=1")
revisa 'mensaje de exito' '[[ "$OKHTML" == *"Mensaje enviado"* ]]'

ERRHTML=$(curl -s -b "$JAR" -c "$JAR" --data-urlencode "csrf_token=$TOKEN" --data-urlencode "nombre=" --data-urlencode "correo=malo" --data-urlencode "mensaje=" "$BASE/contacto")
revisa 'errores visibles' '[[ "$ERRHTML" == *"Escribe un correo válido."* ]]'
revisa 'conserva lo escrito y escapa' '! [[ "$ERRHTML" == *"<script>"* ]]'

SPAM=$(curl -s -o /dev/null -w "%{http_code}" -b "$JAR" -c "$JAR" --data-urlencode "csrf_token=$TOKEN" --data-urlencode "nombre=Bot" --data-urlencode "correo=bot@example.cl" --data-urlencode "mensaje=spam" --data-urlencode "acepto=1" --data-urlencode "sitio_web=http://spam" "$BASE/contacto")
revisa 'spam no genera correo nuevo' '[ "$(ls "$CONTACTO_DIR" | wc -l)" = "1" ]'

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
