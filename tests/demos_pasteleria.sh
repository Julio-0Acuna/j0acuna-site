#!/usr/bin/env bash
# Pruebas del rubro pastelería (demos/pasteleria/): levanta `php -S` con el router y revisa con curl.
# Incluye el encargo de tortas de la Multi-page y el flujo de la Plataforma web con cookies:
# registro → login → carrito → pedido con día y mensaje → el dueño lo ve en su panel.
# Uso: bash tests/demos_pasteleria.sh
cd "$(dirname "$0")/.." || exit 1

PORT=$(( 8111 + ${PUERTO_DESFASE:-0} ))
BASE="http://127.0.0.1:$PORT"
R="/demos/pasteleria"
TIENDA="$R/plataforma/"
# Ruta absoluta: el router hace chdir a la carpeta de cada página.
export PORTFOLIO_SITE_FILE="$(pwd)/${SITE_JSON:-tests/fixtures/site.json}"
export PORTFOLIO_SITE_URL="$BASE"
TMP="$(mktemp -d)"

tools/php.sh -S 127.0.0.1:$PORT tools/router.php > "$TMP/server.log" 2>&1 &
SERVER_PID=$!
trap 'kill $SERVER_PID 2>/dev/null; rm -rf "$TMP"; powershell -NoProfile -Command "Get-NetTCPConnection -LocalPort '"$PORT"' -State Listen -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id \$_.OwningProcess -Force }" >/dev/null 2>&1' EXIT
for _ in 1 2 3 4 5 6 7 8 9 10; do curl -s -o /dev/null "$BASE/" && break; sleep 0.3; done

# Base de la tienda recién creada (mismo comando que el cron).
tools/php.sh demos/pasteleria/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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
debe_contener    /demos/ "href=\"$R/\""
debe_responder   $R/ 200
debe_contener    $R/ 'Página web para pastelerías en Santiago'
debe_contener    $R/ '"@type":"Service"'
debe_contener    $R/ '"@type":"BreadcrumbList"'
debe_contener    $R/ "href=\"$R/simple/\""
debe_contener    $R/ "href=\"$R/multi/\""
debe_contener    $R/ "href=\"$R/plataforma/\""
debe_contener    $R/ 'Quiero esta'
debe_contener    $R/ 'Perfil de Empresa en Google'
no_debe_contener $R/ 'noindex'
for img in simple multi plataforma; do debe_responder "$R/img/$img.webp" 200; done
grep -q 'unsplash.com' demos/pasteleria/CREDITOS.md && pasa 'CREDITOS.md del rubro con fotos y autores' || falla 'CREDITOS.md del rubro sin fotos'

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$R/simple/ $R/multi/ $R/multi/tortas.php $R/multi/carta.php $R/multi/encargo.php $R/multi/nosotros.php $R/multi/contacto.php $R/multi/pedido.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" "href=\"$R/\""
  debe_contener    "$p" '<meta name="robots" content="noindex, follow">'
  debe_contener    "$p" '56973537642'
  no_debe_contener "$p" 'LocalBusiness'
  no_debe_contener "$p" '"@type":"Restaurant"'
  no_debe_contener "$p" 'AggregateRating'
  no_debe_contener "$p" 'application/ld+json'
done

# Sección Google en cada versión
for p in $R/simple/ $R/multi/ $R/multi/contacto.php "$TIENDA"; do
  debe_contener "$p" 'Encuéntranos en Google'
  debe_contener "$p" 'https://www.google.com/maps?q='
  debe_contener "$p" 'output=embed'
  debe_contener "$p" 'Reseña de ejemplo'
  debe_contener "$p" 'Déjanos tu reseña'
done

# Simple-page: carta con QR, sin 3D; vitrina del día y tortas por encargo por WhatsApp con la nota
debe_contener    $R/simple/ 'data-demo-qr="#carta"'
debe_contener    $R/simple/ 'id="carta"'
debe_contener    $R/simple/ 'id="vitrina"'
debe_coincidir   $R/simple/ 'class="de-mano">(Hoy|Mañana|El [^<]+) [^< ]+ en la vitrina<'
debe_contener    $R/simple/ 'qrcode-generator/1.4.4/qrcode.min.js" integrity="sha384-'
debe_contener    $R/simple/ '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20pasteler%C3%ADa%20%E2%80%93%20Simple-page%29'
debe_contener    $R/simple/ 'para%2015%20personas%20%28%2429.900%29'
debe_contener    $R/simple/ 'href="tel:+56973537642"'
no_debe_contener $R/simple/ 'model-viewer'
no_debe_contener $R/simple/ 'demo-3d-btn'

# Multi-page: carta con QR, buscador, tortas en 3D (model-viewer 4.4.0 con SRI), carrito y encargo
debe_contener    $R/multi/carta.php "data-demo-qr=\"$R/multi/carta.php\""
debe_contener    $R/multi/carta.php 'class="demo-3d-btn'
debe_contener    $R/multi/carta.php 'data-3d-modelo="/demos/_comun/3d/torta.glb"'
debe_contener    $R/multi/carta.php '<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js" integrity="sha256-GLLnEQNapHBe4oFTClXcIzj/Be7T1wrhbVQQUoKnXtU=" crossorigin="anonymous"></script>'
debe_contener    $R/multi/carta.php 'data-buscador'
debe_coincidir   "$R/multi/carta.php?q=macarons" 'id="plato-macarons" data-buscable data-texto="[^"]*">'
debe_coincidir   "$R/multi/carta.php?q=macarons" 'id="plato-espresso" data-buscable data-texto="[^"]*" hidden>'
debe_contener    $R/multi/tortas.php 'data-3d-modelo="/demos/_comun/3d/torta.glb"'
debe_contener    "$R/multi/tortas.php?ocasion=matrimonio" 'id="torta-flores-blancas"'
no_debe_contener "$R/multi/tortas.php?ocasion=matrimonio" 'id="torta-pavlova"'
debe_contener    $R/multi/pedido.php 'data-carrito-form'
debe_contener    $R/multi/pedido.php 'name="fecha"'
# Fecha y mensaje van al WhatsApp con data-carrito-extra y el comentario es la nota: sin JS propio en el pedido
debe_contener    $R/multi/pedido.php 'name="fecha" data-carrito-extra="Fecha"'
debe_contener    $R/multi/pedido.php 'data-carrito-extra="Mensaje en la torta"'
debe_contener    $R/multi/pedido.php '<textarea id="nota" name="nota"'
no_debe_contener $R/multi/pedido.php 'type="hidden" name="nota"'
no_debe_contener $R/multi/pedido.php "$R/multi/encargo.js"
debe_contener    $R/multi/encargo.php "$R/multi/encargo.js"
no_debe_contener $R/multi/encargo.js 'data-carrito-form'
debe_contener    $R/multi/ 'id="demo-carrito-config"'
debe_contener    $R/multi/ '(Pedido de prueba desde la demo de pastelería – Multi-page)'
debe_contener    $R/multi/ '"textos":{"retiro":"Entrega: retiro en el atelier","despacho":"Entrega: despacho refrigerado a {direccion}, {comuna}"}'
debe_contener    "$R/multi/encargo.php?torta=pavlova" '<option value="pavlova" data-precios'

# Encargo de torta (POST, sin guardar nada): válido → WhatsApp armado; inválido → errores
FECHA=$(date -d "+5 days" +%F); while [ "$(date -d "$FECHA" +%u)" = 1 ]; do FECHA=$(date -d "$FECHA +1 day" +%F); done
ENC=$(curl -s --data-urlencode "torta=higos" --data-urlencode "tamano=14 porciones" --data-urlencode "fecha=$FECHA" --data-urlencode "entrega=despacho" --data-urlencode "comuna=Las Condes" --data-urlencode "mensaje=Feliz cumple Ana" --data-urlencode "nombre=Prueba" --data-urlencode "extras[]=velas" "$BASE$R/multi/encargo.php")
printf '%s' "$ENC" | grep -qF 'Torta%3A%20Higos%20y%20miel%20de%20ulmo%20%C2%B7%2014%20porciones' && pasa "encargo arma el mensaje con torta y tamaño" || falla "encargo sin torta y tamaño en el mensaje"
printf '%s' "$ENC" | grep -qF '%2454.900' && pasa "encargo con precio de referencia (\$54.900)" || falla "encargo sin precio"
printf '%s' "$ENC" | grep -qF 'Mensaje%20en%20la%20torta%3A%20%C2%ABFeliz%20cumple%20Ana%C2%BB' && pasa "encargo con el mensaje para la torta" || falla "encargo sin mensaje"
printf '%s' "$ENC" | grep -qF '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20pasteler%C3%ADa%20%E2%80%93%20Multi-page%29' && pasa "encargo con la nota de prueba" || falla "encargo sin la nota de prueba"
ENC=$(curl -s --data-urlencode "torta=higos" --data-urlencode "tamano=50 porciones" --data-urlencode "fecha=2020-01-01" --data-urlencode "entrega=despacho" --data-urlencode "comuna=Arica" --data-urlencode "nombre=P" "$BASE$R/multi/encargo.php")
for campo in tamano fecha comuna nombre; do
  printf '%s' "$ENC" | grep -qF "id=\"err-$campo\"" && pasa "encargo rechaza $campo inválido" || falla "encargo acepta $campo inválido"
done
printf '%s' "$ENC" | grep -qF 'wa.me/56973537642?text=Hola%2C%20quiero%20encargar' && falla "encargo inválido igual arma WhatsApp" || pasa "encargo inválido no arma WhatsApp"
LUNES=$(date -d "+7 days" +%F); while [ "$(date -d "$LUNES" +%u)" != 1 ]; do LUNES=$(date -d "$LUNES +1 day" +%F); done
curl -s --data-urlencode "torta=pavlova" --data-urlencode "tamano=8 porciones" --data-urlencode "fecha=$LUNES" --data-urlencode "nombre=Prueba" "$BASE$R/multi/encargo.php" | grep -qF 'el atelier está cerrado' && pasa "encargo no acepta un lunes (cerrado)" || falla "encargo acepta un día cerrado"

grep -q 'Require all denied' demos/pasteleria/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'config|seed|reset' demos/pasteleria/plataforma/.htaccess && pasa 'plataforma/.htaccess bloquea config, seed y reset' || falla 'plataforma/.htaccess incompleto'
grep -q 'Require all denied' demos/pasteleria/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
for f in _encargo.php vistas/checkout.php vistas/pedido.php; do
  [ ! -e "demos/pasteleria/plataforma/$f" ] && pasa "sin $f propio: día y mensaje con las opciones del motor" || falla "sigue demos/pasteleria/plataforma/$f"
done

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in $R/simple/ $R/multi/carta.php $R/multi/tortas.php "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# --- Plataforma web: base protegida, catálogo, 3D
debe_responder   $R/plataforma/data/tienda.sqlite 403
debe_responder   $R/plataforma/reset.php 404
debe_responder   $R/plataforma/_encargo.php 404
debe_contener    "$TIENDA" 'class="demo-3d-btn'
debe_contener    "$TIENDA" 'data-3d-modelo="/demos/_comun/3d/torta-arcoiris.glb"'
debe_contener    "$TIENDA" "data-demo-qr=\"$TIENDA#menu\""
debe_contener    "$TIENDA" 'Torta Frutilla Pop'
no_debe_contener "$TIENDA" 'Brownies en bandeja'
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
RR=$(post "$J1" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Cliente Prueba" --data-urlencode "email=$EMAIL" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=particular")
case "$RR" in 303*r=cuenta*) pasa "registro crea la cuenta ($RR)";; *) falla "registro: $RR";; esac

T=$(csrf "$J1" "${TIENDA}?r=cuenta")
post "$J1" "${TIENDA}?r=salir" --data-urlencode "csrf=$T" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=ingresar")
RR=$(post "$J1" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=$EMAIL" --data-urlencode "clave=otra-clave")
case "$RR" in 401*) pasa "login con clave mala se rechaza";; *) falla "login con clave mala: $RR";; esac
T=$(csrf "$J1" "${TIENDA}?r=ingresar")
RR=$(post "$J1" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=$EMAIL" --data-urlencode "clave=prueba1234")
case "$RR" in 303*) pasa "login correcto ($RR)";; *) falla "login: $RR";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=cuenta" | grep -qF 'Hola, Cliente Prueba' && pasa "sesión iniciada" || falla "sin sesión después del login"

INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
RR=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=2")
case "$RR" in 303*) pasa "agregar al carrito ($RR)";; *) falla "agregar al carrito: $RR";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF 'Torta Frutilla Pop' && pasa "el carrito tiene el producto" || falla "carrito vacío"

# Checkout del motor: día de entrega (modo 'fecha') y mensaje en la torta (campo extra del checkout)
CHK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
printf '%s' "$CHK" | grep -qF '<input id="extra-mensaje_torta" name="extra[mensaje_torta]" maxlength="40"' && pasa "checkout pide el mensaje para la torta" || falla "checkout sin mensaje para la torta"
printf '%s' "$CHK" | grep -qF '<select id="fecha_entrega" name="fecha_entrega" required>' && pasa "checkout con el día de entrega del motor" || falla "checkout sin el día de entrega del motor"
printf '%s' "$CHK" | grep -qF 'hoy solo despachamos lo que hay en vitrina' && pasa "checkout con el aviso de las tortas enteras" || falla "checkout sin el aviso de la vitrina"
DIA=$(printf '%s' "$CHK" | grep -oE '<option value="20[0-9]{2}-[0-9]{2}-[0-9]{2}"' | sed -n 2p | grep -oE '20[0-9-]+')
[ -n "$DIA" ] && pasa "checkout ofrece días de entrega ($DIA)" || falla "checkout sin días de entrega"
T=$(printf '%s' "$CHK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
# Mensaje de más de 40 caracteres: 422 con su error junto al campo
RR=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=al_recibir" --data-urlencode "fecha_entrega=$DIA" --data-urlencode "extra[mensaje_torta]=$(printf 'x%.0s' $(seq 1 41))")
case "$RR" in 422*) grep -qF '<p class="t-error" id="err-extra_mensaje_torta">«Mensaje en la torta» puede tener hasta 40 caracteres.</p>' "$TMP/ultima.html" && pasa "mensaje de más de 40 caracteres: 422 con su error" || falla "mensaje largo: 422 sin el error";; *) falla "mensaje largo aceptado: $RR";; esac
# El pedido se completa con los campos que trae el HTML del checkout (name leídos del formulario)
EXTRA=$(printf '%s' "$CHK" | grep -oE '<input id="extra-mensaje_torta" name="[^"]+"' | sed -E 's/.*name="([^"]+)"/\1/')
FALTAN=$(for k in csrf entrega direccion_id calle comuna pago fecha_entrega cuando nota; do printf '%s' "$CHK" | grep -qF "name=\"$k\"" || echo "$k"; done)
[ "$EXTRA" = "extra[mensaje_torta]" ] && [ -z "$FALTAN" ] && pasa "el formulario del checkout trae todos los campos que se envían ($EXTRA)" || falla "al formulario del checkout le faltan: $FALTAN (mensaje: '$EXTRA')"
RR=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Macul" --data-urlencode "pago=al_recibir" --data-urlencode "fecha_entrega=$DIA" --data-urlencode "cuando=17:00" --data-urlencode "$EXTRA=Feliz cumple Ana" --data-urlencode "nota=Velas de numero 7")
case "$RR" in 303*r=pedido*) pasa "checkout crea el pedido ($RR)";; *) falla "checkout: $RR";; esac
PEDIDO_URL="${RR#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'MC-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página del pedido muestra $CODIGO" || falla "página del pedido sin código"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "pedido ofrece enviar resumen por WhatsApp" || falla "pedido sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20pasteler%C3%ADa%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
printf '%s' "$PAGINA" | grep -qF '<dt>Mensaje en la torta</dt><dd>Feliz cumple Ana</dd>' && pasa "el pedido guarda el mensaje en la torta" || falla "el pedido no muestra el mensaje en la torta"
printf '%s' "$PAGINA" | grep -qF '<dt>Nota</dt><dd>Velas de numero 7</dd>' && pasa "la nota queda aparte del mensaje" || falla "la nota no se ve aparte"
printf '%s' "$PAGINA" | grep -qE '<dt>Para</dt><dd>[^<]+ [0-9]+ de [a-z]+, a las 17:00</dd>' && pasa "el pedido muestra el día y la hora de entrega" || falla "el pedido no muestra el día de entrega"
printf '%s' "$PAGINA" | grep -qF 'Mensaje%20en%20la%20torta%3A%20Feliz%20cumple%20Ana' && printf '%s' "$PAGINA" | grep -qF 'Nota%3A%20Velas%20de%20numero%207' && pasa "el resumen de WhatsApp lleva «Mensaje en la torta: …» y la nota" || falla "el resumen de WhatsApp no lleva el mensaje"
printf '%s' "$PAGINA" | grep -qE 'Para%20el%20[a-z%0-9A-F]+%20[0-9]+%20de%20[a-z]+%20a%20las%2017%3A00' && pasa "el resumen de WhatsApp lleva el día y la hora" || falla "el resumen de WhatsApp sin el día"
# $27.990 × 2 + despacho Macul $0 (gratis sobre $35.000) = $55.980
printf '%s' "$PAGINA" | grep -qF '$55.980' && pasa "total con despacho gratis sobre \$35.000" || falla "total del pedido inesperado"

# Una fecha que no está en la lista se rechaza (422) y no crea el pedido
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
RR=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=al_recibir" --data-urlencode "fecha_entrega=1999-01-01")
case "$RR" in 422*) grep -qF 'id="err-fecha_entrega"' "$TMP/ultima.html" && pasa "fecha fuera de la lista: 422 con su error" || falla "fecha inválida: 422 sin el error";; *) falla "fecha inválida aceptada ($RR)";; esac

# Pago de prueba: checkout → pantalla simulada → aprobar
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
RR=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=prueba" --data-urlencode "fecha_entrega=$DIA")
case "$RR" in 303*r=pago*) pasa "pago de prueba lleva a la pantalla simulada";; *) falla "pago de prueba: $RR";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=pago" | grep -qF 'PAGO DE PRUEBA' && pasa "pantalla de pago marcada como prueba" || falla "pantalla de pago sin aviso"
T=$(csrf "$J1" "${TIENDA}?r=pago")
RR=$(post "$J1" "${TIENDA}?r=pago" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar")
case "$RR" in 303*r=pedido*) pasa "aprobar pago de prueba crea el pedido";; *) falla "aprobar pago: $RR";; esac
curl -s -b "$J1" "${RR#* }" | grep -qE '<dt>Para</dt><dd>[^<]+ [0-9]+ de [a-z]+, durante el día</dd>' && pasa "el pago de prueba conserva el día de entrega" || falla "el pago de prueba perdió el día"

# Repetir pedido desde el historial
T=$(csrf "$J1" "${TIENDA}?r=cuenta")
PEDIDO_ID=$(curl -s -b "$J1" "$BASE${TIENDA}?r=cuenta" | grep -oE 'name="pedido_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
RR=$(post "$J1" "${TIENDA}?r=cuenta" --data-urlencode "csrf=$T" --data-urlencode "accion=repetir" --data-urlencode "pedido_id=$PEDIDO_ID")
case "$RR" in 303*r=carrito*) pasa "repetir pedido vuelve a llenar el carrito";; *) falla "repetir: $RR";; esac

# El dueño ve el pedido en su panel
T=$(csrf "$J2" "${TIENDA}?r=ingresar")
RR=$(post "$J2" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=dueno@demo.cl" --data-urlencode "clave=dueno123")
case "$RR" in 303*r=panel*) pasa "dueño entra a su panel";; *) falla "login dueño: $RR";; esac
PANEL=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=pedidos")
printf '%s' "$PANEL" | grep -qF "$CODIGO" && pasa "el dueño ve $CODIGO en el panel" || falla "el dueño no ve el pedido"
printf '%s' "$PANEL" | grep -qF 'Cliente Prueba' && pasa "el panel muestra al cliente" || falla "el panel no muestra al cliente"
curl -s -b "$J2" "$PEDIDO_URL" | grep -qF '<dt>Mensaje en la torta</dt><dd>Feliz cumple Ana</dd>' && pasa "el dueño ve el mensaje para la torta" || falla "el dueño no ve el mensaje"
printf '%s' "$PANEL" | grep -qF '<p class="t-pequeno t-pedido-panel__extras">Mensaje en la torta: Feliz cumple Ana</p>' && pasa "la lista del panel muestra «Mensaje en la torta: …»" || falla "la lista del panel sin el mensaje"
printf '%s' "$PANEL" | grep -qF 'Mensaje en la torta: Felicidades, Mati' && pasa "los pedidos de ejemplo traen el mensaje como campo" || falla "los pedidos de ejemplo sin el mensaje"
printf '%s' "$PANEL" | grep -qF '>Pasar al horno</button>' && pasa "el panel avanza con «Pasar al horno»" || falla "el panel sin «Pasar al horno»"
curl -s -b "$J2" "$BASE${TIENDA}?r=panel" | grep -qF 'Ventas de hoy' && pasa "panel con ventas del día" || falla "panel sin ventas del día"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "un cliente no entra al panel (403)" || falla "un cliente entra al panel"

# Estado: nuevo → en el horno (preparando)
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
RR=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">En el horno' && pasa "el cliente ve su pedido «En el horno»" || falla "estado del pedido no cambió ($RR)"

# Mayorista (cafetería): precio especial y productos para cafeterías; pendiente hasta aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
MAY=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$MAY" | grep -qF 'Brownies en bandeja' && pasa "mayorista ve productos para cafeterías" || falla "mayorista no ve productos para cafeterías"
printf '%s' "$MAY" | grep -qF '$22.990' && pasa "mayorista ve precio mayorista (\$22.990)" || falla "mayorista sin precio mayorista"
J4="$TMP/nuevo-mayorista.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Cafe Prueba" --data-urlencode "email=may$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Cafeteria de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Brownies en bandeja' && falla "mayorista pendiente ve productos para cafeterías" || pasa "mayorista pendiente aún no ve precios de cafetería"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Cafeteria de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Brownies en bandeja' && pasa "el dueño aprueba al mayorista y ya ve sus productos" || falla "aprobación de mayorista no funcionó"

# Panel: crear producto
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
RR=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Torta de Prueba" --data-urlencode "descripcion=Creada por el test" --data-urlencode "categoria_id=1" --data-urlencode "precio=19990" --data-urlencode "precio_mayorista=15990" --data-urlencode "stock=5" --data-urlencode "foto=1621303837174-89787a7d4729" --data-urlencode "modelo3d=torta-arcoiris" --data-urlencode "visible=1")
case "$RR" in 303*) pasa "el dueño crea un producto";; *) falla "crear producto: $RR";; esac
debe_contener "$TIENDA" 'Torta de Prueba'
RR=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=$(printf 'x%.0s' $(seq 1 81))" --data-urlencode "categoria_id=1" --data-urlencode "precio=1" --data-urlencode "stock=1")
case "$RR" in 422*) pasa "nombre de más de 80 caracteres se rechaza";; *) falla "texto largo aceptado: $RR";; esac

# CSRF: POST sin token o con token falso → 403
RR=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "accion=vaciar")
case "$RR" in 403*) pasa "POST sin CSRF rechazado (403)";; *) falla "POST sin CSRF: $RR";; esac
RR=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=0000" --data-urlencode "accion=aprobar" --data-urlencode "id=1")
case "$RR" in 403*) pasa "POST con CSRF falso rechazado (403)";; *) falla "POST con CSRF falso: $RR";; esac
RR=$(post "$TMP/vacio.txt" "${TIENDA}?r=registro" --data-urlencode "nombre=X" --data-urlencode "email=x@demo.cl" --data-urlencode "clave=prueba1234")
case "$RR" in 403*) pasa "registro sin CSRF rechazado (403)";; *) falla "registro sin CSRF: $RR";; esac
RR=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=0000" --data-urlencode "entrega=retiro" --data-urlencode "pago=al_recibir")
case "$RR" in 403*) pasa "checkout con CSRF falso rechazado (403)";; *) falla "checkout con CSRF falso: $RR";; esac

# Límite de registros por IP: 5 por hora (ya hubo 2 en esta prueba)
for i in 1 2 3; do
  J="$TMP/r$i.txt"; T=$(csrf "$J" "${TIENDA}?r=registro")
  post "$J" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Extra $i" --data-urlencode "email=extra$i$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=particular" > /dev/null
done
J="$TMP/r6.txt"; T=$(csrf "$J" "${TIENDA}?r=registro")
RR=$(post "$J" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Sexto" --data-urlencode "email=sexto$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=particular")
case "$RR" in 422*) grep -qF 'límite de registros' "$TMP/ultima.html" && pasa "6.º registro por IP en una hora se bloquea" || falla "6.º registro: sin mensaje de límite";; *) falla "6.º registro por IP aceptado: $RR";; esac

# Deja la base limpia para quien la mire después.
tools/php.sh demos/pasteleria/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
