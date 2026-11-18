#!/usr/bin/env bash
# Pruebas del rubro sushi (demos/sushi/): levanta `php -S` con el router en el puerto 8113 y revisa con curl.
# Mismo alcance que tests/demos.sh (pizzería), más reglas propias del rubro:
# fotos con créditos, pedido mínimo y costo de despacho por comuna en la Plataforma web.
# Uso: bash tests/demos_sushi.sh
cd "$(dirname "$0")/.." || exit 1

PORT=$(( 8113 + ${PUERTO_DESFASE:-0} ))
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/sushi/"
SIMPLE="${RUBRO}simple/"
MULTI="${RUBRO}multi/"
TIENDA="${RUBRO}plataforma/"
# Ruta absoluta: el router hace chdir a la carpeta de cada página.
export PORTFOLIO_SITE_FILE="$(pwd)/${SITE_JSON:-tests/fixtures/site.json}"
export PORTFOLIO_SITE_URL="$BASE"
TMP="$(mktemp -d)"

tools/php.sh -S 127.0.0.1:$PORT tools/router.php > "$TMP/server.log" 2>&1 &
SERVER_PID=$!
trap 'kill $SERVER_PID 2>/dev/null; rm -rf "$TMP"; powershell -NoProfile -Command "Get-NetTCPConnection -LocalPort '"$PORT"' -State Listen -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id \$_.OwningProcess -Force }" >/dev/null 2>&1' EXIT
for _ in 1 2 3 4 5 6 7 8 9 10; do curl -s -o /dev/null "$BASE/" && break; sleep 0.3; done

# Base de la tienda recién creada (mismo comando que el cron).
tools/php.sh demos/sushi/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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

# --- Página del portafolio sobre el rubro (indexable) y listado /demos/
debe_contener    /demos/ 'href="/demos/sushi/"'
debe_responder   "$RUBRO" 200
debe_contener    "$RUBRO" 'Página web para restaurantes de sushi en Santiago'
debe_contener    "$RUBRO" '"@type":"Service"'
debe_contener    "$RUBRO" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO" "href=\"$SIMPLE\""
debe_contener    "$RUBRO" "href=\"$MULTI\""
debe_contener    "$RUBRO" "href=\"$TIENDA\""
debe_contener    "$RUBRO" 'Quiero esta'
debe_contener    "$RUBRO" 'Perfil de Empresa en Google'
no_debe_contener "$RUBRO" 'noindex'
for img in simple multi plataforma; do
  debe_responder "${RUBRO}img/$img.webp" 200
  F="demos/sushi/img/$img.webp"
  if [ -f "$F" ] && [ "$(wc -c < "$F")" -le 204800 ]; then pasa "$F pesa ≤ 200 KB"; else falla "$F falta o pesa más de 200 KB"; fi
done

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}menu.php ${MULTI}nosotros.php ${MULTI}preguntas.php ${MULTI}contacto.php ${MULTI}pedido.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" "href=\"$RUBRO\""
  debe_contener    "$p" '<meta name="robots" content="noindex, follow">'
  debe_contener    "$p" '56973537642'
  no_debe_contener "$p" 'LocalBusiness'
  no_debe_contener "$p" '"@type":"Restaurant"'
  no_debe_contener "$p" 'AggregateRating'
  no_debe_contener "$p" 'application/ld+json'
done

# Sección Google en cada versión
for p in "$SIMPLE" "$MULTI" "${MULTI}contacto.php" "$TIENDA"; do
  debe_contener "$p" 'Encuéntranos en Google'
  debe_contener "$p" 'https://www.google.com/maps?q='
  debe_contener "$p" 'output=embed'
  debe_contener "$p" 'Reseña de ejemplo'
  debe_contener "$p" 'Déjanos tu reseña'
done

# Tres negocios ficticios distintos, cada uno en su comuna
debe_contener "$SIMPLE" 'Maki Neón'
debe_contener "$SIMPLE" 'La Florida'
debe_contener "$MULTI" 'Kumo'
debe_contener "$MULTI" 'Vitacura'
debe_contener "$TIENDA" 'Nori Club'
debe_contener "$TIENDA" 'Las Condes'

# Simple-page: menú con QR, sin 3D; pedido por WhatsApp con la nota
debe_contener    "$SIMPLE" 'data-demo-qr="#menu"'
debe_contener    "$SIMPLE" 'id="menu"'
debe_contener    "$SIMPLE" 'qrcode-generator/1.4.4/qrcode.min.js" integrity="sha384-'
debe_contener    "$SIMPLE" '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20sushi%20%E2%80%93%20Simple-page%29'
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
no_debe_contener "$SIMPLE" 'model-viewer'
no_debe_contener "$SIMPLE" 'demo-3d-btn'

# Multi-page: carta con QR, buscador, 3D (model-viewer 4.4.0 con SRI), carrito
debe_contener    "${MULTI}menu.php" "data-demo-qr=\"${MULTI}menu.php\""
debe_contener    "${MULTI}menu.php" 'class="demo-3d-btn'
debe_contener    "${MULTI}menu.php" 'data-3d-modelo="/demos/_comun/3d/sushi.glb"'
debe_contener    "${MULTI}menu.php" '<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js" integrity="sha256-GLLnEQNapHBe4oFTClXcIzj/Be7T1wrhbVQQUoKnXtU=" crossorigin="anonymous"></script>'
debe_contener    "${MULTI}menu.php" 'data-buscador'
debe_contener    "${MULTI}menu.php?q=toro" 'id="plato-toro" data-buscable'
curl -s "$BASE${MULTI}menu.php?q=toro" | grep -oE '<li[^>]*id="plato-toro"[^>]*>' | grep -q ' hidden>' && falla "buscador: «toro» quedó oculto" || pasa "buscador sin JavaScript muestra el toro aburi"
curl -s "$BASE${MULTI}menu.php?q=toro" | grep -oE '<li[^>]*id="plato-sake"[^>]*>' | grep -q ' hidden>' && pasa "buscador sin JavaScript oculta lo que no coincide" || falla "buscador no oculta el nigiri de salmón"
debe_contener    "${MULTI}pedido.php" 'data-carrito-form'
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '(Pedido de prueba desde la demo de sushi – Multi-page)'
debe_contener    "$MULTI" 'Reservar en la barra'
grep -qF 'AddType model/gltf-binary .glb' demos/_comun/3d/.htaccess && pasa '_comun/3d/.htaccess declara .glb' || falla '_comun/3d/.htaccess sin AddType .glb'
grep -q 'Require all denied' demos/sushi/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/sushi/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -qE '\(config\|seed\|reset\)' demos/sushi/plataforma/.htaccess && pasa 'plataforma/.htaccess protege config, seed y reset' || falla 'plataforma/.htaccess no protege config/seed/reset'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "${MULTI}menu.php" "$MULTI" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# Fotos: solo Unsplash enlazado (nada descargado) y todas con su crédito en demos/sushi/CREDITOS.md
FOTOS_LOCALES=$(find demos/sushi -type f \( -iname '*.jpg' -o -iname '*.jpeg' -o -iname '*.png' \) | wc -l)
[ "$FOTOS_LOCALES" = "0" ] && pasa "sin fotos descargadas en demos/sushi" || falla "$FOTOS_LOCALES fotos guardadas en demos/sushi"
SIN_CREDITO=0
for id in $(grep -rhoE '1[0-9]{9,12}-[0-9a-f]{12}' --include='*.php' demos/sushi | sort -u); do
  grep -qF "$id" demos/sushi/CREDITOS.md || { SIN_CREDITO=$((SIN_CREDITO + 1)); echo "    sin crédito: $id"; }
done
[ "$SIN_CREDITO" = "0" ] && pasa "todas las fotos tienen crédito" || falla "$SIN_CREDITO fotos sin crédito"

# --- Plataforma web: base protegida, catálogo, 3D
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
debe_contener    "$TIENDA" 'class="demo-3d-btn'
debe_contener    "$TIENDA" 'data-3d-modelo="/demos/_comun/3d/sushi.glb"'
debe_contener    "$TIENDA" "data-demo-qr=\"${TIENDA}#menu\""
debe_contener    "$TIENDA" 'Combo Dúo · 2 personas'
debe_contener    "$TIENDA" 'Combo Familia · 4 personas'
no_debe_contener "$TIENDA" 'Bandeja Oficina'
debe_contener    "${TIENDA}?q=ramen" 'Ramen tonkotsu'
no_debe_contener "${TIENDA}?q=ramen" 'Rainbow de atún'
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

# El primer producto de la portada es el Combo Dúo ($16.990)
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=2")
case "$R" in 303*) pasa "agregar al carrito ($R)";; *) falla "agregar al carrito: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF 'Combo Dúo · 2 personas' && pasa "el carrito tiene el Combo Dúo" || falla "carrito sin el Combo Dúo"
curl -s -b "$J1" "$BASE$TIENDA" | grep -qF 'class="nc-flotante"' && pasa "botón flotante «Ver carrito» con productos" || falla "sin botón flotante del carrito"

T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Pudahuel" --data-urlencode "pago=al_recibir")
case "$R" in 422*) pasa "despacho a una comuna fuera de la lista se rechaza";; *) falla "comuna fuera de la lista: $R";; esac
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Las Condes" --data-urlencode "pago=al_recibir" --data-urlencode "nota=Sin sesamo")
case "$R" in 303*r=pedido*) pasa "checkout crea el pedido ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'NC-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página del pedido muestra $CODIGO" || falla "página del pedido sin código"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "pedido ofrece enviar resumen por WhatsApp" || falla "pedido sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20sushi%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
# $16.990 × 2 = $33.980 + despacho Las Condes $0 (gratis sobre $25.000)
printf '%s' "$PAGINA" | grep -qF '$33.980' && pasa "total con despacho gratis sobre \$25.000" || falla "total del pedido inesperado"

# Pedido mínimo $8.000: una bebida sola no alcanza
BEB=$(curl -s -b "$J1" "$BASE${TIENDA}?q=lata" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$BEB" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=al_recibir")
case "$R" in 303*r=carrito*) curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF 'El pedido mínimo es $8.000' && pasa "pedido bajo \$8.000 se rechaza con aviso" || falla "pedido mínimo sin aviso";; *) falla "pedido bajo el mínimo: $R";; esac

# Despacho con costo: gyozas × 2 ($8.980) + Vitacura $2.490 = $11.470
T=$(csrf "$J1" "${TIENDA}?r=carrito")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=vaciar" > /dev/null
GYO=$(curl -s -b "$J1" "$BASE${TIENDA}?q=ponzu" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$GYO" --data-urlencode "cantidad=2" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 456" --data-urlencode "comuna=Vitacura" --data-urlencode "pago=al_recibir")
case "$R" in 303*r=pedido*) curl -s -b "$J1" "${R#* }" | grep -qF '$11.470' && pasa "despacho a Vitacura suma \$2.490 (total \$11.470)" || falla "costo de despacho por comuna inesperado";; *) falla "pedido con despacho: $R";; esac

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

# Empresas (mayoristas): precio por volumen y bandejas para eventos; pendiente hasta aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
MAY=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$MAY" | grep -qF 'Bandeja Oficina' && pasa "empresa ve bandejas para eventos" || falla "empresa no ve bandejas para eventos"
printf '%s' "$MAY" | grep -qF '$6.690' && pasa "empresa ve precio por volumen del rainbow (\$6.690)" || falla "empresa sin precio por volumen"
printf '%s' "$MAY" | grep -qF 'Cuenta empresa' && pasa "aviso de cuenta empresa en la tienda" || falla "sin aviso de cuenta empresa"
J4="$TMP/nueva-empresa.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Oficina Prueba" --data-urlencode "email=emp$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Empresa de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Bandeja Oficina' && falla "empresa pendiente ve bandejas para eventos" || pasa "empresa pendiente aún no ve precios por volumen"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Empresa de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Bandeja Oficina' && pasa "el dueño aprueba a la empresa y ya ve sus bandejas" || falla "aprobación de empresa no funcionó"

# Panel: crear producto (foto de la lista, sin subida de archivos) y validar largo
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Roll de Prueba" --data-urlencode "descripcion=Creado por el test" --data-urlencode "categoria_id=2" --data-urlencode "precio=6990" --data-urlencode "precio_mayorista=5990" --data-urlencode "stock=5" --data-urlencode "foto=1780291117619-c58ee7aa740e" --data-urlencode "modelo3d=sushi" --data-urlencode "visible=1")
case "$R" in 303*) pasa "el dueño crea un producto";; *) falla "crear producto: $R";; esac
debe_contener "$TIENDA" 'Roll de Prueba'
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Roll Foto Ajena" --data-urlencode "categoria_id=2" --data-urlencode "precio=1" --data-urlencode "stock=1" --data-urlencode "foto=https://ejemplo.invalid/x.jpg")
case "$R" in 422*) pasa "foto fuera de la lista se rechaza";; *) falla "foto externa aceptada: $R";; esac
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
tools/php.sh demos/sushi/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
