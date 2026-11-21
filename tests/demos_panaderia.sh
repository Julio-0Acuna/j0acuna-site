#!/usr/bin/env bash
# Pruebas del rubro panadería (demos/panaderia/): levanta `php -S` con el router y revisa con curl.
# Incluye el flujo de la Plataforma web con cookies: registro → login → carrito → pedido → panel del dueño.
# Uso: bash tests/demos_panaderia.sh
cd "$(dirname "$0")/.." || exit 1

PORT=$(( 8110 + ${PUERTO_DESFASE:-0} ))
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/panaderia"
SIMPLE="$RUBRO/simple/"
MULTI="$RUBRO/multi/"
TIENDA="$RUBRO/plataforma/"
# Ruta absoluta: el router hace chdir a la carpeta de cada página.
export PORTFOLIO_SITE_FILE="$(pwd)/${SITE_JSON:-tests/fixtures/site.json}"
export PORTFOLIO_SITE_URL="$BASE"
TMP="$(mktemp -d)"

tools/php.sh -S 127.0.0.1:$PORT tools/router.php > "$TMP/server.log" 2>&1 &
SERVER_PID=$!
trap 'kill $SERVER_PID 2>/dev/null; rm -rf "$TMP"; powershell -NoProfile -Command "Get-NetTCPConnection -LocalPort '"$PORT"' -State Listen -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id \$_.OwningProcess -Force }" >/dev/null 2>&1' EXIT
for _ in 1 2 3 4 5 6 7 8 9 10; do curl -s -o /dev/null "$BASE/" && break; sleep 0.3; done

# Base de la tienda recién creada (mismo comando que el cron).
tools/php.sh demos/panaderia/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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
debe_coincidir() {
  curl -s "$BASE$1" | grep -qE -- "$2" && pasa "$1 coincide con /$2/" || falla "$1 no coincide con /$2/"
}

# --- Páginas del portafolio sobre el rubro (indexables)
debe_contener    /demos/ 'href="/demos/panaderia/"'
debe_responder   "$RUBRO/" 200
debe_contener    "$RUBRO/" 'Página web para panaderías en Santiago'
debe_contener    "$RUBRO/" '"@type":"Service"'
debe_contener    "$RUBRO/" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO/" "href=\"$SIMPLE\""
debe_contener    "$RUBRO/" "href=\"$MULTI\""
debe_contener    "$RUBRO/" "href=\"$TIENDA\""
debe_contener    "$RUBRO/" 'Quiero esta'
debe_contener    "$RUBRO/" 'Perfil de Empresa en Google'
debe_contener    "$RUBRO/" 'Panadería Las Tres Hornadas'
debe_contener    "$RUBRO/" 'Miga Lenta'
debe_contener    "$RUBRO/" 'Panificadora Alba'
no_debe_contener "$RUBRO/" 'noindex'
for img in simple multi plataforma; do
  debe_responder "$RUBRO/img/$img.webp" 200
  TAM=$(wc -c < "demos/panaderia/img/$img.webp" 2>/dev/null || echo 999999)
  [ "$TAM" -le 204800 ] && pasa "img/$img.webp pesa ${TAM} B (≤ 200 KB)" || falla "img/$img.webp pesa ${TAM} B"
done
# php -S ejecuta los .php sueltos: la protección de las partes internas la da demos/.htaccess.
grep -qF '<FilesMatch "^_.*\.php$">' demos/.htaccess && pasa 'demos/.htaccess niega los _*.php (datos y plantillas)' || falla 'demos/.htaccess sin regla para _*.php'

# Créditos: cada foto de Unsplash usada en el rubro aparece en demos/panaderia/CREDITOS.md
[ -f demos/panaderia/CREDITOS.md ] && pasa "CREDITOS.md del rubro existe" || falla "falta demos/panaderia/CREDITOS.md"
FALTAN=0
for id in $(grep -rhoE "'1[0-9]{9,12}-[0-9a-f]{12}'" demos/panaderia --include='*.php' | tr -d "'" | sort -u); do
  grep -qF "$id" demos/panaderia/CREDITOS.md || { FALTAN=$((FALTAN + 1)); echo "      sin crédito: $id"; }
done
[ "$FALTAN" = "0" ] && pasa "todas las fotos del rubro están en CREDITOS.md" || falla "$FALTAN fotos sin crédito"

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}panes.php ${MULTI}proceso.php ${MULTI}preguntas.php ${MULTI}contacto.php ${MULTI}pedido.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" 'href="/demos/panaderia/"'
  debe_contener    "$p" '<meta name="robots" content="noindex, follow">'
  debe_contener    "$p" '56973537642'
  no_debe_contener "$p" 'LocalBusiness'
  no_debe_contener "$p" '"@type":"Restaurant"'
  no_debe_contener "$p" '"@type":"Bakery"'
  no_debe_contener "$p" 'AggregateRating'
  no_debe_contener "$p" 'application/ld+json'
done
curl -s -D - -o /dev/null "$BASE$TIENDA" | grep -qi '^X-Robots-Tag: noindex' && pasa "la tienda envía X-Robots-Tag: noindex" || falla "la tienda sin X-Robots-Tag"

# Sección Google en cada versión
for p in "$SIMPLE" "$MULTI" "${MULTI}contacto.php" "$TIENDA"; do
  debe_contener "$p" 'Encuéntranos en Google'
  debe_contener "$p" 'https://www.google.com/maps?q='
  debe_contener "$p" 'output=embed'
  debe_contener "$p" 'Reseña de ejemplo'
  debe_contener "$p" 'Déjanos tu reseña'
done

# Simple-page: lista de precios con QR (sin 3D), hornadas, pan por kilo y pedido por WhatsApp con la nota
debe_contener    "$SIMPLE" 'data-demo-qr="#precios"'
debe_contener    "$SIMPLE" 'id="precios"'
debe_contener    "$SIMPLE" 'id="hornadas"'
debe_contener    "$SIMPLE" 'qrcode-generator/1.4.4/qrcode.min.js" integrity="sha384-'
debe_contener    "$SIMPLE" '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20panader%C3%ADa%20%E2%80%93%20Simple-page%29'
debe_contener    "$SIMPLE" 'quiero%201%20kg%20de%20marraqueta%20%28%242.390%29'
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
no_debe_contener "$SIMPLE" 'model-viewer'
no_debe_contener "$SIMPLE" 'demo-3d-btn'

# Multi-page: lista con QR, buscador, panes en 3D (model-viewer 4.4.0 con SRI), carrito
debe_contener    "${MULTI}panes.php" "data-demo-qr=\"${MULTI}panes.php\""
debe_contener    "${MULTI}panes.php" 'class="demo-3d-btn'
debe_contener    "${MULTI}panes.php" 'data-3d-modelo="/demos/_comun/3d/hogaza-rustica.glb"'
debe_contener    "${MULTI}panes.php" '<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js" integrity="sha256-GLLnEQNapHBe4oFTClXcIzj/Be7T1wrhbVQQUoKnXtU=" crossorigin="anonymous"></script>'
debe_contener    "${MULTI}panes.php" 'data-buscador'
debe_coincidir   "${MULTI}panes.php?q=centeno" 'id="producto-centeno" data-buscable data-texto="[^"]*">'
debe_coincidir   "${MULTI}panes.php?q=centeno" 'id="producto-croissant" data-buscable data-texto="[^"]*" hidden>'
debe_contener    "${MULTI}pedido.php" 'data-carrito-form'
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '"clave":"demo-panaderia-multi-carrito"'
debe_contener    "$MULTI" '(Pedido de prueba desde la demo de panadería – Multi-page)'
grep -q 'Require all denied' demos/panaderia/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/panaderia/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -q 'config|seed|reset' demos/panaderia/plataforma/.htaccess && pasa 'plataforma/.htaccess protege config, seed y reset' || falla 'plataforma/.htaccess sin protección'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "${MULTI}panes.php" "$MULTI" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# --- Plataforma web: base protegida, catálogo, 3D
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
debe_contener    "$TIENDA" 'class="demo-3d-btn'
debe_contener    "$TIENDA" 'data-3d-modelo="/demos/_comun/3d/hogaza-rustica.glb"'
debe_contener    "$TIENDA" "data-demo-qr=\"${TIENDA}#menu\""
debe_contener    "$TIENDA" 'Marraqueta (1 kg)'
debe_contener    "$TIENDA" 'Así viaja tu pan'
no_debe_contener "$TIENDA" 'Marraqueta por mayor'
debe_contener    "${TIENDA}?q=empanada" 'Empanada de pino al horno'
no_debe_contener "${TIENDA}?q=empanada" 'Hallulla (1 kg)'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'mayorista@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_responder   "${TIENDA}?r=no-existe" 404

# --- Flujo con cookies (textos ASCII o ya codificados: Git Bash en Windows no pasa bien UTF-8 a curl.exe)
J1="$TMP/cliente.txt"; J2="$TMP/dueno.txt"; J3="$TMP/mayorista.txt"
csrf() { curl -s -b "$1" -c "$1" "$BASE$2" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/'; }
post() { local jar=$1 url=$2; shift 2; curl -s -o "$TMP/ultima.html" -b "$jar" -c "$jar" -w "%{http_code} %{redirect_url}" "$@" "$BASE$url"; }

EMAIL="prueba$RANDOM$RANDOM@demo.cl"
T=$(csrf "$J1" "${TIENDA}?r=registro")
[ -n "$T" ] && pasa "registro entrega token CSRF" || falla "registro sin token CSRF"
R=$(post "$J1" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Cliente Prueba" --data-urlencode "email=$EMAIL" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=particular")
case "$R" in 303*r=cuenta*) pasa "registro crea la cuenta ($R)";; *) falla "registro: $R";; esac

T=$(csrf "$J1" "${TIENDA}?r=cuenta")
post "$J1" "${TIENDA}?r=salir" --data-urlencode "csrf=$T" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=ingresar")
R=$(post "$J1" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=$EMAIL" --data-urlencode "clave=otra-clave")
case "$R" in 401*) pasa "login con clave mala se rechaza";; *) falla "login con clave mala: $R";; esac
T=$(csrf "$J1" "${TIENDA}?r=ingresar")
R=$(post "$J1" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=$EMAIL" --data-urlencode "clave=prueba1234")
case "$R" in 303*) pasa "login correcto ($R)";; *) falla "login: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=cuenta" | grep -qF 'Hola, Cliente Prueba' && pasa "sesión iniciada" || falla "sin sesión después del login"

INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=2")
case "$R" in 303*) pasa "agregar al carrito ($R)";; *) falla "agregar al carrito: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF 'Marraqueta (1 kg)' && pasa "el carrito tiene la marraqueta" || falla "carrito vacío"

CHK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
T=$(printf '%s' "$CHK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
HORA=$(printf '%s' "$CHK" | grep -oE '<option value="[0-9]{2}:[0-9]{2}"' | head -1 | grep -oE '[0-9]{2}:[0-9]{2}')
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Quilicura" --data-urlencode "pago=al_recibir" --data-urlencode "cuando=$HORA" --data-urlencode "nota=Tocar el timbre")
case "$R" in 303*r=pedido*) pasa "checkout crea el pedido ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'AL-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página del pedido muestra $CODIGO" || falla "página del pedido sin código"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "pedido ofrece enviar resumen por WhatsApp" || falla "pedido sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20panader%C3%ADa%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
# $2.290 × 2 + despacho Quilicura $1.490 (bajo $25.000 no es gratis) = $6.070
printf '%s' "$PAGINA" | grep -qF '$6.070' && pasa "total con despacho a Quilicura (\$6.070)" || falla "total del pedido inesperado"

# Pedido mínimo $4.000: una sola marraqueta ($2.290) no alcanza
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=al_recibir")
grep -qF 'pedido mínimo' "$TMP/ultima.html" && pasa "pedido bajo el mínimo se rechaza" || { curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF 'pedido mínimo' && pasa "pedido bajo el mínimo se rechaza" || falla "pedido bajo el mínimo aceptado: $R"; }

# Pago de prueba: checkout → pantalla simulada → aprobar
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=prueba")
case "$R" in 303*r=pago*) pasa "pago de prueba lleva a la pantalla simulada";; *) falla "pago de prueba: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=pago" | grep -qF 'PAGO DE PRUEBA' && pasa "pantalla de pago marcada como prueba" || falla "pantalla de pago sin aviso"
T=$(csrf "$J1" "${TIENDA}?r=pago")
R=$(post "$J1" "${TIENDA}?r=pago" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar")
case "$R" in 303*r=pedido*) pasa "aprobar pago de prueba crea el pedido";; *) falla "aprobar pago: $R";; esac

# Repetir pedido desde el historial
T=$(csrf "$J1" "${TIENDA}?r=cuenta")
PEDIDO_ID=$(curl -s -b "$J1" "$BASE${TIENDA}?r=cuenta" | grep -oE 'name="pedido_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
R=$(post "$J1" "${TIENDA}?r=cuenta" --data-urlencode "csrf=$T" --data-urlencode "accion=repetir" --data-urlencode "pedido_id=$PEDIDO_ID")
case "$R" in 303*r=carrito*) pasa "repetir pedido vuelve a llenar el carrito";; *) falla "repetir: $R";; esac

# El dueño ve el pedido en su panel
T=$(csrf "$J2" "${TIENDA}?r=ingresar")
R=$(post "$J2" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=dueno@demo.cl" --data-urlencode "clave=dueno123")
case "$R" in 303*r=panel*) pasa "dueño entra a su panel";; *) falla "login dueño: $R";; esac
PANEL=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=pedidos")
printf '%s' "$PANEL" | grep -qF "$CODIGO" && pasa "el dueño ve $CODIGO en el panel" || falla "el dueño no ve el pedido"
printf '%s' "$PANEL" | grep -qF 'Cliente Prueba' && pasa "el panel muestra al cliente" || falla "el panel no muestra al cliente"
curl -s -b "$J2" "$BASE${TIENDA}?r=panel" | grep -qF 'Ventas de hoy' && pasa "panel con ventas del día" || falla "panel sin ventas del día"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "un cliente no entra al panel (403)" || falla "un cliente entra al panel"

# Estado: nuevo → preparando
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">Preparando' && pasa "el cliente ve su pedido en «Preparando»" || falla "estado del pedido no cambió ($R)"

# Mayorista: precio especial y productos para negocios; pendiente hasta aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
MAY=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$MAY" | grep -qF 'Marraqueta por mayor' && pasa "mayorista ve productos para negocios" || falla "mayorista no ve productos para negocios"
printf '%s' "$MAY" | grep -qF '$1.790' && pasa "mayorista ve precio mayorista (\$1.790 la marraqueta)" || falla "mayorista sin precio mayorista"
printf '%s' "$MAY" | grep -qF 'Modo mayorista' && pasa "mayorista ve el aviso de modo mayorista" || falla "sin aviso de modo mayorista"
J4="$TMP/nuevo-mayorista.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Almacen Prueba" --data-urlencode "email=may$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Almacen de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Marraqueta por mayor' && falla "mayorista pendiente ve productos para negocios" || pasa "mayorista pendiente aún no ve precios de negocio"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Almacen de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Marraqueta por mayor' && pasa "el dueño aprueba al mayorista y ya ve sus productos" || falla "aprobación de mayorista no funcionó"

# Panel: crear producto y ocultarlo
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Pan de Prueba" --data-urlencode "descripcion=Creado por el test" --data-urlencode "categoria_id=1" --data-urlencode "precio=1990" --data-urlencode "precio_mayorista=1490" --data-urlencode "stock=5" --data-urlencode "foto=1641736047680-49aa1fb95d53" --data-urlencode "visible=1")
case "$R" in 303*) pasa "el dueño crea un producto";; *) falla "crear producto: $R";; esac
debe_contener "$TIENDA" 'Pan de Prueba'
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=$(printf 'x%.0s' $(seq 1 81))" --data-urlencode "categoria_id=1" --data-urlencode "precio=1" --data-urlencode "stock=1")
case "$R" in 422*) pasa "nombre de más de 80 caracteres se rechaza";; *) falla "texto largo aceptado: $R";; esac

# CSRF: POST sin token o con token falso → 403
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "accion=vaciar")
case "$R" in 403*) pasa "POST sin CSRF rechazado (403)";; *) falla "POST sin CSRF: $R";; esac
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=0000" --data-urlencode "accion=aprobar" --data-urlencode "id=1")
case "$R" in 403*) pasa "POST con CSRF falso rechazado (403)";; *) falla "POST con CSRF falso: $R";; esac
R=$(post "$TMP/vacio.txt" "${TIENDA}?r=registro" --data-urlencode "nombre=X" --data-urlencode "email=x@demo.cl" --data-urlencode "clave=prueba1234")
case "$R" in 403*) pasa "registro sin CSRF rechazado (403)";; *) falla "registro sin CSRF: $R";; esac

# Límite de registros por IP: 5 por hora (ya hubo 2 en esta prueba)
for i in 1 2 3; do
  J="$TMP/r$i.txt"; T=$(csrf "$J" "${TIENDA}?r=registro")
  post "$J" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Extra $i" --data-urlencode "email=extra$i$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=particular" > /dev/null
done
J="$TMP/r6.txt"; T=$(csrf "$J" "${TIENDA}?r=registro")
R=$(post "$J" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Sexto" --data-urlencode "email=sexto$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=particular")
case "$R" in 422*) grep -qF 'límite de registros' "$TMP/ultima.html" && pasa "6.º registro por IP en una hora se bloquea" || falla "6.º registro: sin mensaje de límite";; *) falla "6.º registro por IP aceptado: $R";; esac

# Deja la base limpia para quien la mire después.
tools/php.sh demos/panaderia/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
