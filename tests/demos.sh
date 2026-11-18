#!/usr/bin/env bash
# Pruebas de los ejemplos por rubro (demos/): levanta `php -S` con el router y revisa con curl.
# Incluye el flujo de la Plataforma web con cookies: registro → login → carrito → pedido → panel del dueño.
# Uso: bash tests/demos.sh  (con agentes en paralelo: DEMOS_PORT=81xx DEMOS_PORT2=81yy bash tests/demos.sh)
cd "$(dirname "$0")/.." || exit 1

PORT=${DEMOS_PORT:-$(( 8098 + ${PUERTO_DESFASE:-0} ))}
# Segundo servidor: tienda de prueba del motor con fecha de entrega y textos propios (fuera de demos/).
PORT2=${DEMOS_PORT2:-$(( 8089 + ${PUERTO_DESFASE:-0} ))}
BASE="http://127.0.0.1:$PORT"
TIENDA="/demos/pizzeria/plataforma/"
# Ruta absoluta: el router hace chdir a la carpeta de cada página.
export PORTFOLIO_SITE_FILE="$(pwd)/${SITE_JSON:-tests/fixtures/site.json}"
export PORTFOLIO_SITE_URL="$BASE"
TMP="$(mktemp -d)"
SERVER2_PID=""

tools/php.sh -S 127.0.0.1:$PORT tools/router.php > "$TMP/server.log" 2>&1 &
SERVER_PID=$!
trap 'kill $SERVER_PID $SERVER2_PID 2>/dev/null; rm -rf "$TMP"; powershell -NoProfile -Command "Get-NetTCPConnection -LocalPort '"$PORT,$PORT2"' -State Listen -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id \$_.OwningProcess -Force }" >/dev/null 2>&1' EXIT
for _ in 1 2 3 4 5 6 7 8 9 10; do curl -s -o /dev/null "$BASE/" && break; sleep 0.3; done

# Base de la tienda recién creada (mismo comando que el cron).
tools/php.sh demos/pizzeria/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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

# --- Páginas del portafolio sobre los ejemplos (indexables)
debe_responder   /demos/ 200
debe_contener    /demos/ 'href="/demos/pizzeria/"'
debe_contener    /demos/ '"@type":"BreadcrumbList"'
no_debe_contener /demos/ 'noindex'
debe_responder   /demos/pizzeria/ 200
debe_contener    /demos/pizzeria/ 'Página web para pizzerías en Santiago'
debe_contener    /demos/pizzeria/ '"@type":"Service"'
debe_contener    /demos/pizzeria/ 'href="/demos/pizzeria/simple/"'
debe_contener    /demos/pizzeria/ 'href="/demos/pizzeria/multi/"'
debe_contener    /demos/pizzeria/ 'href="/demos/pizzeria/plataforma/"'
debe_contener    /demos/pizzeria/ 'Quiero esta'
debe_contener    /demos/pizzeria/ 'Perfil de Empresa en Google'
no_debe_contener /demos/pizzeria/ 'noindex'
for img in simple multi plataforma; do debe_responder "/demos/pizzeria/img/$img.webp" 200; done
debe_responder   /demos/creditos.php 200
debe_contener    /demos/creditos.php 'Ivan Torres'
debe_contener    /demos/creditos.php 'id="creditos-sushi"'
no_debe_contener /demos/creditos.php 'id="creditos-pizzeria"'

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="/demos/pizzeria/simple/ /demos/pizzeria/multi/ /demos/pizzeria/multi/menu.php /demos/pizzeria/multi/nosotros.php /demos/pizzeria/multi/preguntas.php /demos/pizzeria/multi/contacto.php /demos/pizzeria/multi/pedido.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" 'href="/demos/pizzeria/"'
  debe_contener    "$p" '<meta name="robots" content="noindex, follow">'
  debe_contener    "$p" '56973537642'
  no_debe_contener "$p" 'LocalBusiness'
  no_debe_contener "$p" '"@type":"Restaurant"'
  no_debe_contener "$p" 'AggregateRating'
  no_debe_contener "$p" 'application/ld+json'
done

# Sección Google en cada versión
for p in /demos/pizzeria/simple/ /demos/pizzeria/multi/ /demos/pizzeria/multi/contacto.php "$TIENDA"; do
  debe_contener "$p" 'Encuéntranos en Google'
  debe_contener "$p" 'https://www.google.com/maps?q='
  debe_contener "$p" 'output=embed'
  debe_contener "$p" 'Reseña de ejemplo'
  debe_contener "$p" 'Déjanos tu reseña'
done

# Simple-page: menú con QR, sin 3D; pedido por WhatsApp con la nota
debe_contener    /demos/pizzeria/simple/ 'data-demo-qr="#menu"'
debe_contener    /demos/pizzeria/simple/ 'id="menu"'
debe_contener    /demos/pizzeria/simple/ 'qrcode-generator/1.4.4/qrcode.min.js" integrity="sha384-'
debe_contener    /demos/pizzeria/simple/ '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20pizzer%C3%ADa%20%E2%80%93%20Simple-page%29'
debe_contener    /demos/pizzeria/simple/ 'href="tel:+56973537642"'
no_debe_contener /demos/pizzeria/simple/ 'model-viewer'
no_debe_contener /demos/pizzeria/simple/ 'demo-3d-btn'

# Multi-page: menú con QR, buscador, 3D (model-viewer 4.4.0 con SRI), carrito
debe_contener    /demos/pizzeria/multi/menu.php 'data-demo-qr="/demos/pizzeria/multi/menu.php"'
debe_contener    /demos/pizzeria/multi/menu.php 'class="demo-3d-btn'
debe_contener    /demos/pizzeria/multi/menu.php 'data-3d-modelo="/demos/_comun/3d/pizza.glb"'
debe_contener    /demos/pizzeria/multi/menu.php '<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js" integrity="sha256-GLLnEQNapHBe4oFTClXcIzj/Be7T1wrhbVQQUoKnXtU=" crossorigin="anonymous"></script>'
debe_contener    /demos/pizzeria/multi/menu.php 'data-buscador'
debe_contener    '/demos/pizzeria/multi/menu.php?q=bufala' 'id="plato-bufala" data-buscable'
debe_contener    '/demos/pizzeria/multi/menu.php?q=bufala' 'id="plato-diavola" data-buscable data-texto="Diavola'
debe_contener    /demos/pizzeria/multi/pedido.php 'data-carrito-form'
debe_contener    /demos/pizzeria/multi/ 'id="demo-carrito-config"'
debe_contener    /demos/pizzeria/multi/ '(Pedido de prueba desde la demo de pizzería – Multi-page)'
grep -qF 'AddType model/gltf-binary .glb' demos/_comun/3d/.htaccess && pasa '_comun/3d/.htaccess declara .glb' || falla '_comun/3d/.htaccess sin AddType .glb'
grep -q 'Require all denied' demos/pizzeria/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/_motor/.htaccess && pasa '_motor/.htaccess niega todo' || falla '_motor/.htaccess no niega'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in /demos/pizzeria/simple/ /demos/pizzeria/multi/menu.php "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# --- Plataforma web: base protegida, catálogo, 3D
debe_responder   /demos/pizzeria/plataforma/data/tienda.sqlite 403
debe_responder   /demos/pizzeria/plataforma/reset.php 404
debe_contener    "$TIENDA" 'class="demo-3d-btn'
debe_contener    "$TIENDA" 'data-demo-qr="/demos/pizzeria/plataforma/#menu"'
debe_contener    "$TIENDA" 'Pepperoni Cohete'
no_debe_contener "$TIENDA" 'Bollos de masa'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'mayorista@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_responder   "${TIENDA}?r=no-existe" 404
# Textos del motor sin configurar: los de siempre
debe_contener    "$TIENDA" 'Crear cuenta mayorista'
debe_contener    "$TIENDA" '<figcaption>Código QR del menú</figcaption>'
debe_contener    "${TIENDA}?r=registro" '<strong>Mayorista</strong>'
debe_contener    "${TIENDA}?r=registro" '(solo mayoristas)'

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
curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF 'Pepperoni Cohete' && pasa "el carrito tiene el producto" || falla "carrito vacío"

CHK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
printf '%s' "$CHK" | grep -qF '>Hoy a las 12:30</option>' && pasa "checkout de la pizzería ofrece «Hoy a las …» (modo hoy)" || falla "checkout de la pizzería sin «Hoy a las …»"
printf '%s' "$CHK" | grep -qF '¿Para cuándo?' && pasa "checkout de la pizzería pregunta «¿Para cuándo?»" || falla "checkout de la pizzería sin «¿Para cuándo?»"
printf '%s' "$CHK" | grep -qF 'name="fecha_entrega"' && falla "la pizzería pide fecha de entrega y no debería" || pasa "la pizzería no pide fecha de entrega"
T=$(printf '%s' "$CHK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data "comuna=Maip%C3%BA" --data-urlencode "pago=al_recibir" --data-urlencode "nota=Sin cebolla")
case "$R" in 303*r=pedido*) pasa "checkout crea el pedido ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'CP-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página del pedido muestra $CODIGO" || falla "página del pedido sin código"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "pedido ofrece enviar resumen por WhatsApp" || falla "pedido sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20pizzer%C3%ADa%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
# $10.990 × 2 + despacho Maipú $0 (gratis sobre $20.000) = $21.980
printf '%s' "$PAGINA" | grep -qF '$21.980' && pasa "total con despacho gratis sobre \$20.000" || falla "total del pedido inesperado"
printf '%s' "$PAGINA" | grep -qF 'Pago%3A%20al%20recibir%0ANota%3A%20Sin%20cebolla' && pasa "resumen de WhatsApp con la nota del cliente" || falla "resumen de WhatsApp sin la nota del cliente"
printf '%s' "$PAGINA" | grep -qF '¡Pedido recibido! Pagas al recibir.' && pasa "mensaje de éxito de siempre (pago al recibir)" || falla "sin mensaje de éxito del pedido"
printf '%s' "$PAGINA" | grep -qF '<dt>Para</dt><dd>Lo antes posible</dd>' && pasa "pedido sin hora: «Lo antes posible»" || falla "pedido sin «Lo antes posible»"

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
curl -s -b "$J1" "${R#* }" | grep -qF 'Tu pedido ya está en la cocina.' && pasa "mensaje de éxito de siempre (pago de prueba)" || falla "sin mensaje de éxito del pago de prueba"

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
printf '%s' "$MAY" | grep -qF 'Bollos de masa' && pasa "mayorista ve productos para negocios" || falla "mayorista no ve productos para negocios"
printf '%s' "$MAY" | grep -qF '$8.990' && pasa "mayorista ve precio mayorista (\$8.990)" || falla "mayorista sin precio mayorista"
J4="$TMP/nuevo-mayorista.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Resto Prueba" --data-urlencode "email=may$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Restoran de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Bollos de masa' && falla "mayorista pendiente ve productos para negocios" || pasa "mayorista pendiente aún no ve precios de negocio"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Restoran de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Bollos de masa' && pasa "el dueño aprueba al mayorista y ya ve sus productos" || falla "aprobación de mayorista no funcionó"

# Panel: crear producto y ocultarlo
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Pizza de Prueba" --data-urlencode "descripcion=Creada por el test" --data-urlencode "categoria_id=1" --data-urlencode "precio=9990" --data-urlencode "precio_mayorista=7990" --data-urlencode "stock=5" --data-urlencode "foto=1542282811-943ef1a977c3" --data-urlencode "visible=1")
case "$R" in 303*) pasa "el dueño crea un producto";; *) falla "crear producto: $R";; esac
debe_contener "$TIENDA" 'Pizza de Prueba'
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
tools/php.sh demos/pizzeria/plataforma/reset.php > /dev/null

# --- Motor: fecha de entrega y textos propios, en una tienda de prueba (config en $TMP, otro puerto).
# No cambia ningún rubro: así probaría su config un rubro con 'entrega' => ['modo' => 'fecha', ...].
F="$TMP/fecha"; B2="http://127.0.0.1:$PORT2"
mkdir -p "$F"
RAIZ="$(pwd -W 2>/dev/null || pwd)"
printf "<?php\nrequire '%s/demos/_motor/motor.php';\nm_ejecutar(require __DIR__ . '/config.php');\n" "$RAIZ" > "$F/index.php"
cat > "$F/config.php" <<'PHP'
<?php
return [
    'demo' => true,
    'rubro' => ['rubro_nombre' => 'prueba', 'url_rubro' => '/demos/'],
    'url_base' => '/',
    'db' => __DIR__ . '/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'reinicio_segundos' => 0,
    'sesion' => 'demo_prueba_fecha',
    'prefijo_pedido' => 'PF',
    'negocio' => ['nombre' => 'Tienda Fecha'],
    'mayoristas' => true,
    'comunas' => ['Macul' => 2000],
    'horas_entrega' => ['10:00', '18:00'],
    'menu_qr' => true,
    'entrega' => ['modo' => 'fecha', 'dias_min' => 1, 'dias_max' => 10, 'cerrado' => [1]],
    'textos' => [
        'mayorista' => 'Empresa',
        'mayoristas' => 'Empresas',
        'crear_cuenta_mayorista' => 'Crear cuenta para empresas',
        'mayoristas_aprobados' => 'Empresas aprobadas',
        'qr_pie' => 'QR de la tienda de prueba',
        'hora_entrega_fecha' => 'Retiro a las {hora}',
        'pedido_exito_al_recibir' => 'Encargo recibido: te esperamos.',
        'pedido_exito_prueba' => 'Pago de prueba aprobado: encargo agendado.',
    ],
];
PHP
cat > "$F/seed.php" <<'PHP'
<?php
return [
    'categorias' => [['slug' => 'tortas', 'nombre' => 'Tortas'], ['slug' => 'empresas', 'nombre' => 'Para empresas']],
    'productos' => [
        ['slug' => 'torta', 'categoria' => 'tortas', 'nombre' => 'Torta de prueba', 'precio' => 20000, 'stock' => 50],
        ['slug' => 'bandeja', 'categoria' => 'empresas', 'nombre' => 'Bandeja de prueba', 'precio' => 50000, 'precio_mayorista' => 40000, 'stock' => 10, 'solo_mayorista' => true],
    ],
    'usuarios' => [
        ['email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Cliente Fecha'],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Duena', 'rol' => 'dueno'],
        ['email' => 'pendiente@demo.cl', 'clave' => 'pendiente123', 'nombre' => 'Pendiente', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Oficina X'],
    ],
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 30, 'estado' => 'nuevo', 'entrega' => 'retiro', 'pago' => 'al_recibir', 'fecha_entrega' => 3, 'cuando' => '11:00', 'items' => [['torta', 1]]],
    ],
];
PHP
tools/php.sh -S 127.0.0.1:$PORT2 -t "$F" > "$TMP/server2.log" 2>&1 &
SERVER2_PID=$!
for _ in 1 2 3 4 5 6 7 8 9 10; do curl -s -o /dev/null "$B2/" && break; sleep 0.3; done

csrf2() { curl -s -b "$1" -c "$1" "$B2$2" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/'; }
post2() { local jar=$1 url=$2; shift 2; curl -s -o "$TMP/ultima2.html" -b "$jar" -c "$jar" -w "%{http_code} %{redirect_url}" "$@" "$B2$url"; }

INI2=$(curl -s "$B2/")
printf '%s' "$INI2" | grep -qF 'Crear cuenta para empresas' && pasa "textos propios: botón «Crear cuenta para empresas»" || falla "textos propios: falta el botón de cuenta para empresas"
printf '%s' "$INI2" | grep -qF 'Crear cuenta mayorista' && falla "textos propios: sigue el botón «Crear cuenta mayorista»" || pasa "textos propios: reemplazan «Crear cuenta mayorista»"
printf '%s' "$INI2" | grep -qF '<figcaption>QR de la tienda de prueba</figcaption>' && pasa "textos propios: pie del QR" || falla "textos propios: pie del QR sin cambiar"
curl -s "$B2/?r=registro" | grep -qF '<strong>Empresa</strong>' && pasa "textos propios: registro ofrece cuenta «Empresa»" || falla "textos propios: registro sin «Empresa»"

K1="$TMP/f-cliente.txt"; K2="$TMP/f-dueno.txt"
T=$(csrf2 "$K1" "/?r=ingresar")
post2 "$K1" "/?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=cliente@demo.cl" --data-urlencode "clave=cliente123" > /dev/null
INI2=$(curl -s -b "$K1" -c "$K1" "$B2/")
PID2=$(printf '%s' "$INI2" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INI2" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
post2 "$K1" "/?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID2" > /dev/null

CHK2=$(curl -s -b "$K1" -c "$K1" "$B2/?r=checkout")
printf '%s' "$CHK2" | grep -qF '<select id="fecha_entrega" name="fecha_entrega" required>' && pasa "modo fecha: el checkout pide el día" || falla "modo fecha: el checkout no pide el día"
printf '%s' "$CHK2" | grep -qF '>Retiro a las 10:00</option>' && pasa "modo fecha: horas con texto propio" || falla "modo fecha: horas sin texto propio"
printf '%s' "$CHK2" | grep -qF 'Hoy a las' && falla "modo fecha: aparece «Hoy a las …»" || pasa "modo fecha: sin «Hoy a las …»"
DIAS=$(printf '%s' "$CHK2" | grep -oE '<option value="20[0-9]{2}-[0-9]{2}-[0-9]{2}"' | grep -oE '20[0-9-]+')
[ "$(printf '%s\n' "$DIAS" | grep -c .)" -ge 8 ] && pasa "modo fecha: ofrece los días de mañana a 10 días ($(printf '%s\n' "$DIAS" | grep -c .))" || falla "modo fecha: pocos días ofrecidos"
printf '%s\n' "$DIAS" | grep -qx "$(date +%F)" && falla "modo fecha: ofrece hoy con dias_min 1" || pasa "modo fecha: dias_min 1 no ofrece hoy"
LUNES_OFRECIDO=0; for d in $DIAS; do [ "$(date -d "$d" +%u)" = 1 ] && LUNES_OFRECIDO=1; done
[ "$LUNES_OFRECIDO" = 0 ] && pasa "modo fecha: no ofrece los lunes (cerrado)" || falla "modo fecha: ofrece un lunes cerrado"
DIA=$(printf '%s\n' "$DIAS" | sed -n 2p)
DIA_NUM=$(date -d "$DIA" +%-d); DIA_CORTO=$(date -d "$DIA" +%-d/%-m)
LUNES=$(date -d "+1 day" +%F); while [ "$(date -d "$LUNES" +%u)" != 1 ]; do LUNES=$(date -d "$LUNES +1 day" +%F); done
T=$(printf '%s' "$CHK2" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
for MALA in "" "1999-01-01" "$LUNES" "$(date +%F)" "<b>x</b>"; do
  RR=$(post2 "$K1" "/?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=al_recibir" --data-urlencode "fecha_entrega=$MALA")
  case "$RR" in 422*) grep -qF 'id="err-fecha_entrega"' "$TMP/ultima2.html" && pasa "modo fecha: rechaza la fecha «$MALA»" || falla "modo fecha: «$MALA» sin error en el campo";; *) falla "modo fecha: acepta la fecha «$MALA» ($RR)";; esac
done
RR=$(post2 "$K1" "/?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=al_recibir" --data-urlencode "fecha_entrega=$DIA" --data-urlencode "cuando=13:00" --data-urlencode "nota=Sin nueces")
case "$RR" in 303*r=pedido*) pasa "modo fecha: checkout crea el pedido para el $DIA";; *) falla "modo fecha: checkout: $RR";; esac
PED2_URL="${RR#* }"
PED2=$(curl -s -b "$K1" "$PED2_URL")
printf '%s' "$PED2" | grep -qF 'Encargo recibido: te esperamos.' && pasa "textos propios: mensaje de éxito del pedido" || falla "textos propios: mensaje de éxito sin cambiar"
printf '%s' "$PED2" | grep -qE "<dt>Para</dt><dd>[^<]+ $DIA_NUM de [a-z]+, a las 13:00</dd>" && pasa "modo fecha: la página del pedido muestra el día y la hora" || falla "modo fecha: la página del pedido no muestra el día"
printf '%s' "$PED2" | grep -qE "Para%20el%20[^\"]*%20${DIA_NUM}%20de%20[a-z]+%20a%20las%2013%3A00%0APago%3A%20al%20recibir%0ANota%3A%20Sin%20nueces" && pasa "modo fecha: el resumen de WhatsApp lleva el día y la nota" || falla "modo fecha: el resumen de WhatsApp sin el día o la nota"
T=$(csrf2 "$K1" "/")
post2 "$K1" "/?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID2" > /dev/null
T=$(csrf2 "$K1" "/?r=checkout")
RR=$(post2 "$K1" "/?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=prueba" --data-urlencode "fecha_entrega=$DIA")
case "$RR" in 303*r=pago*) pasa "modo fecha: pago de prueba lleva a la pantalla simulada";; *) falla "modo fecha: pago de prueba: $RR";; esac
T=$(csrf2 "$K1" "/?r=pago")
RR=$(post2 "$K1" "/?r=pago" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar")
PED3=$(curl -s -b "$K1" "${RR#* }")
printf '%s' "$PED3" | grep -qE "<dt>Para</dt><dd>[^<]+ $DIA_NUM de [a-z]+, durante el d" && pasa "modo fecha: el pago de prueba conserva el día" || falla "modo fecha: el pago de prueba perdió el día ($RR)"
printf '%s' "$PED3" | grep -qF 'Pago de prueba aprobado: encargo agendado.' && pasa "textos propios: mensaje del pago de prueba" || falla "textos propios: mensaje del pago de prueba sin cambiar"

T=$(csrf2 "$K2" "/?r=ingresar")
post2 "$K2" "/?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=dueno@demo.cl" --data-urlencode "clave=dueno123" > /dev/null
PANEL2=$(curl -s -b "$K2" "$B2/?r=panel&ver=pedidos")
printf '%s' "$PANEL2" | grep -qE "· para el [^ <]+ $DIA_CORTO a las 13:00 ·" && pasa "modo fecha: el panel del dueño muestra el día ($DIA_CORTO)" || falla "modo fecha: el panel del dueño no muestra el día"
printf '%s' "$PANEL2" | grep -qE "· para el [^ <]+ $(date -d '+3 days' +%-d/%-m) a las 11:00 ·" && pasa "seed: pedido de ejemplo con fecha_entrega en 3 días" || falla "seed: pedido de ejemplo sin fecha"
printf '%s' "$PANEL2" | grep -qF '>Empresas (1)</a>' && pasa "textos propios: pestaña «Empresas» del panel" || falla "textos propios: pestaña del panel sin cambiar"
curl -s -b "$K2" "$B2/?r=panel" | grep -qF '1 empresa(s) esperan aprobación' && pasa "textos propios: aviso de empresas por aprobar" || falla "textos propios: aviso de mayoristas sin cambiar"
curl -s -b "$K2" "$B2/?r=panel&ver=mayoristas" | grep -qF '<h2>Empresas aprobadas</h2>' && pasa "textos propios: «Empresas aprobadas»" || falla "textos propios: «Mayoristas aprobados» sin cambiar"

# --- carrito.js (Multi-page): campos [data-carrito-extra] en el mensaje de WhatsApp (sin navegador, con node)
if command -v node > /dev/null 2>&1; then
  NODE_OUT=$(node - demos/_comun/js/carrito.js 2>&1 <<'JS'
var fs = require('fs');
var src = fs.readFileSync(process.argv[process.argv.length - 1], 'utf8');
function campo(attrs, props) {
  return Object.assign({
    getAttribute: function (n) { return Object.prototype.hasOwnProperty.call(attrs, n) ? attrs[n] : null; },
    closest: function () { return props.oculto ? {} : null; }
  }, props);
}
var extras = [
  campo({ 'data-carrito-extra': 'Mensaje en la torta', maxlength: '40' }, { tagName: 'INPUT', type: 'text', name: 'mensaje_torta', value: '  Feliz   cumple, Ana  ' }),
  campo({ 'data-carrito-extra': 'Día' }, { tagName: 'SELECT', type: 'select-one', selectedIndex: 1, options: [{ value: '', text: 'Elige' }, { value: '2026-11-21', text: 'sáb 21/11' }] }),
  campo({ 'data-carrito-extra': 'Velas' }, { tagName: 'INPUT', type: 'checkbox', checked: true, value: 'on' }),
  campo({ 'data-carrito-extra': 'Tarjeta' }, { tagName: 'INPUT', type: 'checkbox', checked: false, value: 'on' }),
  campo({ 'data-carrito-extra': 'Vacío' }, { tagName: 'INPUT', type: 'text', value: '   ' }),
  campo({ 'data-carrito-extra': 'Solo despacho' }, { tagName: 'INPUT', type: 'text', value: 'x', oculto: true }),
  campo({ 'data-carrito-extra': 'Sin elegir' }, { tagName: 'SELECT', selectedIndex: 0, options: [{ value: '', text: 'Elige' }] })
];
var elementos = { entrega: { value: 'retiro' }, nombre: { value: 'Ana' }, nota: { value: 'Sin flores' }, hora: { value: '17:00' } };
var conExtras = { elements: elementos, querySelectorAll: function (s) { return s === '[data-carrito-extra]' ? extras : []; } };
var sinExtras = { elements: elementos, querySelectorAll: function () { return []; } };
var config = { clave: 'k', negocio: 'Prueba', nota: '(Pedido de prueba)', numero: '569', comunas: {}, catalogo: { t1: { nombre: 'Torta', variantes: { 'Única': 10000 } } } };
var documento = {
  getElementById: function (id) { return id === 'demo-carrito-config' ? { textContent: JSON.stringify(config) } : null; },
  querySelector: function () { return null; }, querySelectorAll: function () { return []; }, addEventListener: function () {}
};
var ventana = { Demo: { clp: function (n) { return '$' + n; }, almacen: { leer: function () { return [{ id: 't1', variante: 'Única', cantidad: 1 }]; }, guardar: function () {} }, waUrl: function (n, t) { return t; } }, addEventListener: function () {} };
new Function('window', 'document', src)(ventana, documento);
function revisar(cond, nombre) { console.log((cond ? 'ok ' : 'FALLA ') + nombre); }
var m = ventana.DemoCarrito.mensaje(conExtras);
revisar(m.indexOf('Entrega: retiro en el local a las 17:00\nMensaje en la torta: Feliz cumple, Ana\nDía: sáb 21/11\nVelas: Sí\nNombre: Ana\nNota: Sin flores\n(Pedido de prueba)') !== -1, 'carrito.js agrega los campos extra con su etiqueta');
revisar(!/Tarjeta|Vacío|Solo despacho|Sin elegir/.test(m), 'carrito.js omite extras vacíos, sin marcar u ocultos');
revisar(ventana.DemoCarrito.mensaje(sinExtras) === 'Hola, quiero hacer un pedido en Prueba:\n• 1 × Torta – $10000\nSubtotal: $10000\nTotal: $10000\nEntrega: retiro en el local a las 17:00\nNombre: Ana\nNota: Sin flores\n(Pedido de prueba)', 'carrito.js sin extras arma el mismo mensaje de antes');
JS
)
  [ -n "$NODE_OUT" ] || falla "carrito.js: la prueba con node no imprimió nada"
  while IFS= read -r l; do
    case "$l" in "ok "*) pasa "${l#ok }";; "") ;; *) falla "${l#FALLA }";; esac
  done <<< "$NODE_OUT"
else
  echo "  (sin node: se omite la prueba de carrito.js)"
fi

for log in "$TMP/server.log" "$TMP/server2.log"; do
  if grep -qiE 'fatal|warning|deprecated|notice' "$log"; then
    falla "el servidor registró errores de PHP ($(basename "$log")):"; grep -iE 'fatal|warning|deprecated|notice' "$log" | head -5
  else
    pasa "sin errores de PHP en el registro del servidor ($(basename "$log"))"
  fi
done

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
