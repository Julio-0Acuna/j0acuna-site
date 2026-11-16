#!/usr/bin/env bash
# Pruebas de páginas: levanta `php -S` con el router y revisa el HTML con curl.
# Uso: bash tests/pages.sh            (datos de prueba)
#      SITE_JSON=data/site.json bash tests/pages.sh   (contenido real)
cd "$(dirname "$0")/.." || exit 1

PORT=8099
BASE="http://127.0.0.1:$PORT"
export PORTFOLIO_SITE_FILE="${SITE_JSON:-tests/fixtures/site.json}"
export PORTFOLIO_SITE_URL="$BASE"

tools/php.sh -S 127.0.0.1:$PORT tools/router.php > /tmp/pages_server.log 2>&1 &
SERVER_PID=$!
trap 'kill $SERVER_PID 2>/dev/null; powershell -NoProfile -Command "Get-NetTCPConnection -LocalPort '"$PORT"' -State Listen -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id \$_.OwningProcess -Force }" >/dev/null 2>&1' EXIT
for _ in 1 2 3 4 5 6 7 8 9 10; do curl -s -o /dev/null "$BASE/" && break; sleep 0.3; done

OK=0; FAIL=0
pasa() { OK=$((OK + 1)); echo "  ok   $*"; }
falla() { FAIL=$((FAIL + 1)); echo "  FALLA $*"; }

debe_responder() {
  local code; code=$(curl -s -o /dev/null -w "%{http_code}" "$BASE$1")
  [ "$code" = "$2" ] && pasa "$1 responde $2" || falla "$1 responde $code (esperado $2)"
}
debe_contener() {
  curl -s "$BASE$1" | grep -qF -- "$2" && pasa "$1 contiene $2" || falla "$1 no contiene $2"
}
no_debe_contener() {
  curl -s "$BASE$1" | grep -qF -- "$2" && falla "$1 contiene $2 y no debería" || pasa "$1 no contiene $2"
}

# --- Portada (Tarea 5)
debe_responder  /  200
debe_contener   /  'id="servicios"'
debe_contener   /  'id="destacados"'
debe_contener   /  'data-filtro="restaurantes"'
no_debe_contener / 'data-filtro="ejemplos"'
no_debe_contener / 'admin/login.php'
no_debe_contener / 'href="assets/'
debe_contener   /  'https://wa.me/56973537642'
debe_contener   /  'Carnicería «Don Ramón» &amp; Cía'
no_debe_contener / 'Proyecto oculto'

# --- Detalle de proyecto (Tarea 6)
debe_responder  /proyecto/restaurante-a  200
debe_contener   /proyecto/restaurante-a  'Carnicería «Don Ramón» &amp; Cía'
debe_contener   /proyecto/restaurante-a  'text=Hola%20Julio%2C%20vi%20el%20proyecto%20%C2%AB'
debe_contener   /proyecto/restaurante-a  'El problema'
debe_contener   /proyecto/restaurante-a  'Don Ramón</'
no_debe_contener /proyecto/restaurante-a 'href="assets/'
no_debe_contener /proyecto/restaurante-a 'src="assets/'
debe_contener   /proyecto/gestion-b      'Cliente confidencial'
no_debe_contener /proyecto/gestion-b     'El problema'
debe_responder  /proyecto/oculto        404
debe_responder  /proyecto/no-existe     404
debe_contener   /proyecto/no-existe     'href="/"'
debe_responder  /data/site.json         403

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
