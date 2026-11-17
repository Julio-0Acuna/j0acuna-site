#!/usr/bin/env bash
# Pruebas del rubro carnicería (demos/carniceria/): levanta `php -S` con el router y revisa con curl.
# Incluye el flujo de la Plataforma web con cookies: registro → login → carrito por kilo → pedido → panel del dueño.
# Uso: bash tests/demos_carniceria.sh
cd "$(dirname "$0")/.." || exit 1

PORT=8090
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/carniceria"
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
tools/php.sh demos/carniceria/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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

# --- Páginas del portafolio sobre el rubro (indexables)
debe_contener    /demos/ "href=\"$RUBRO/\""
debe_responder   "$RUBRO/" 200
debe_contener    "$RUBRO/" 'Página web para carnicerías en Santiago'
debe_contener    "$RUBRO/" '"@type":"Service"'
debe_contener    "$RUBRO/" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO/" "href=\"$SIMPLE\""
debe_contener    "$RUBRO/" "href=\"$MULTI\""
debe_contener    "$RUBRO/" "href=\"$TIENDA\""
debe_contener    "$RUBRO/" 'Quiero esta'
debe_contener    "$RUBRO/" 'Perfil de Empresa en Google'
no_debe_contener "$RUBRO/" 'noindex'
for img in simple multi plataforma; do debe_responder "$RUBRO/img/$img.webp" 200; done
[ -f demos/carniceria/CREDITOS.md ] && pasa "CREDITOS.md del rubro existe" || falla "falta demos/carniceria/CREDITOS.md"

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}cortes.php ${MULTI}camara.php ${MULTI}preguntas.php ${MULTI}contacto.php ${MULTI}pedido.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" "href=\"$RUBRO/\""
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

# --- Simple-page: lista de precios con QR, sin 3D; cortes con uso y selector de gramos; pedido por WhatsApp
debe_contener    "$SIMPLE" 'data-demo-qr="#precios"'
debe_contener    "$SIMPLE" 'id="precios"'
debe_contener    "$SIMPLE" 'qrcode-generator/1.4.4/qrcode.min.js" integrity="sha384-'
debe_contener    "$SIMPLE" '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20carnicer%C3%ADa%20%E2%80%93%20Simple-page%29'
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
debe_contener    "$SIMPLE" 'name="gramos"'
debe_contener    "$SIMPLE" 'data-uso="parrilla sarten"'
debe_contener    "$SIMPLE" 'Packs para asado'
debe_contener    "$SIMPLE" 'Pack para</small> 12'
no_debe_contener "$SIMPLE" 'model-viewer'
no_debe_contener "$SIMPLE" 'demo-3d-btn'
R=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" "$BASE${SIMPLE}?pedir=lomo-vetado&gramos=1500")
case "$R" in "303 https://wa.me/56973537642?text="*1%2C5%20kg%20de%20Lomo%20vetado*%2426.985*Simple-page%29) pasa "pedido de 1,5 kg arma el WhatsApp con peso, total y nota";; *) falla "pedido por gramos: $R";; esac
R=$(curl -s -o /dev/null -w "%{http_code}" "$BASE${SIMPLE}?pedir=lomo-vetado&gramos=1234")
[ "$R" = "200" ] && pasa "gramos fuera de la lista no redirigen" || falla "gramos inválidos: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" "$BASE${SIMPLE}?pedir=../../etc&gramos=500")
[ "$R" = "200" ] && pasa "corte inexistente no redirige" || falla "corte inválido: $R"

# --- Multi-page: carta con QR, buscador, peso a elegir, 3D (model-viewer 4.4.0 con SRI), carrito
debe_contener    "${MULTI}cortes.php" 'data-demo-qr="/demos/carniceria/multi/cortes.php"'
debe_contener    "${MULTI}cortes.php" 'class="demo-3d-btn'
debe_contener    "${MULTI}cortes.php" 'data-3d-modelo="/demos/_comun/3d/carne.glb"'
debe_contener    "${MULTI}cortes.php" '<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js" integrity="sha256-GLLnEQNapHBe4oFTClXcIzj/Be7T1wrhbVQQUoKnXtU=" crossorigin="anonymous"></script>'
debe_contener    "${MULTI}cortes.php" 'data-buscador'
debe_contener    "${MULTI}cortes.php" '<option value="1,2 kg">1,2 kg · $57.480</option>'
debe_contener    "${MULTI}cortes.php" 'Cajas parrilleras'
debe_contener    "${MULTI}cortes.php?q=tomahawk" 'id="corte-tomahawk-35" data-buscable'
debe_contener    "${MULTI}cortes.php?q=tomahawk" 'id="corte-lomo-vetado-28" data-buscable data-texto="Lomo vetado 28 días'
curl -s "$BASE${MULTI}cortes.php?q=tomahawk" | grep -F 'id="corte-lomo-vetado-28"' | grep -qF ' hidden>' && pasa "el buscador sin JS oculta lo que no coincide" || falla "el buscador sin JS no oculta"
debe_contener    "${MULTI}pedido.php" 'data-carrito-form'
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '(Pedido de prueba desde la demo de carnicería – Multi-page)'
debe_contener    "$MULTI" '"Caja para 8","variantes":{"Única":169900}'
no_debe_contener "$SIMPLE" 'Veta'
grep -q 'Require all denied' demos/carniceria/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/carniceria/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -q 'config|seed|reset' demos/carniceria/plataforma/.htaccess && pasa 'plataforma/.htaccess niega config, seed y reset' || falla 'plataforma/.htaccess no protege config/seed/reset'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "${MULTI}cortes.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# Fotos: todas de images.unsplash.com con w= y q=70
FOTOS=$(for p in "$SIMPLE" "$MULTI" "${MULTI}cortes.php" "${MULTI}camara.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE 'src="https://[^"]+"' | grep -v 'cdn.jsdelivr\|cdnjs\|google.com' | grep -vc 'images.unsplash.com/photo-[0-9a-f-]*?w=[0-9]*&amp;q=70&amp;auto=format')
[ "$FOTOS" = "0" ] && pasa "fotos enlazadas desde Unsplash con w y q=70" || falla "$FOTOS fotos con otra fuente o formato"

# --- Plataforma web: base protegida, catálogo por kilo, 3D
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
debe_contener    "$TIENDA" 'class="demo-3d-btn'
debe_contener    "$TIENDA" 'data-3d-modelo="/demos/_comun/3d/carne.glb"'
debe_contener    "$TIENDA" 'data-demo-qr="/demos/carniceria/plataforma/#menu"'
debe_contener    "$TIENDA" 'Lomo vetado'
debe_contener    "$TIENDA" '$17.980</strong> <span class="kj-precio__unidad">el kg'
debe_contener    "$TIENDA" '<option value="3">1,5 kg · $26.970</option>'
debe_contener    "$TIENDA" 'Pack asado para 12'
no_debe_contener "$TIENDA" 'Lomo vetado al vacío'
debe_contener    "${TIENDA}?q=pollo" 'Pechuga deshuesada'
no_debe_contener "${TIENDA}?q=pollo" 'Lomo liso'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'mayorista@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_contener    "${TIENDA}?r=registro" 'Restaurantes, picadas, casinos y banquetería.'
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

# Lomo vetado (unidades de 500 g): 2 unidades = 1 kg
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=2")
case "$R" in 303*) pasa "agregar 1 kg al carrito ($R)";; *) falla "agregar al carrito: $R";; esac
CARRO=$(curl -s -b "$J1" "$BASE${TIENDA}?r=carrito")
printf '%s' "$CARRO" | grep -qF '<strong>Lomo vetado</strong>' && pasa "el carrito tiene el producto" || falla "carrito vacío"
printf '%s' "$CARRO" | grep -qF '>1 kg</span>' && pasa "el carrito muestra el peso (1 kg)" || falla "el carrito no muestra el peso"

T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Quilicura" --data-urlencode "pago=al_recibir" --data-urlencode "nota=Bifes gruesos")
case "$R" in 303*r=pedido*) pasa "checkout crea el pedido ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'KJ-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página del pedido muestra $CODIGO" || falla "página del pedido sin código"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "pedido ofrece enviar resumen por WhatsApp" || falla "pedido sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20carnicer%C3%ADa%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
# $8.990 × 2 + despacho Quilicura $1.990 (gratis recién sobre $35.000) = $19.970
printf '%s' "$PAGINA" | grep -qF '$19.970' && pasa "total con despacho a Quilicura (\$19.970)" || falla "total del pedido inesperado"

# Pedido mínimo $10.000: 500 g de lomo vetado ($8.990) no alcanza
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1" > /dev/null
curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF 'El pedido mínimo es $10.000' && pasa "el carrito avisa el pedido mínimo" || falla "sin aviso de pedido mínimo"
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=al_recibir")
case "$R" in 303*r=carrito*) pasa "bajo el mínimo no se crea el pedido";; *) falla "pedido bajo el mínimo: $R";; esac

# Pago de prueba: checkout → pantalla simulada → aprobar
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1" > /dev/null
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
curl -s -b "$J2" "$BASE${TIENDA}?r=panel" | grep -qF 'Stock bajo: Costillar de cerdo (500 g)' && pasa "panel avisa stock bajo" || falla "panel sin aviso de stock bajo"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "un cliente no entra al panel (403)" || falla "un cliente entra al panel"

# Estado: nuevo → preparando
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">Preparando' && pasa "el cliente ve su pedido en «Preparando»" || falla "estado del pedido no cambió ($R)"

# Mayorista: precio especial y cajas para restaurantes; pendiente hasta aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
MAY=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$MAY" | grep -qF 'Lomo vetado al vacío' && pasa "mayorista ve las cajas para restaurantes" || falla "mayorista no ve las cajas para restaurantes"
printf '%s' "$MAY" | grep -qF '$7.690 cada 500 g' && pasa "mayorista ve precio mayorista (\$7.690 cada 500 g)" || falla "mayorista sin precio mayorista"
printf '%s' "$MAY" | grep -qF '$15.380</strong>' && pasa "mayorista ve el kilo a \$15.380" || falla "mayorista sin precio por kilo mayorista"
J4="$TMP/nuevo-mayorista.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Resto Prueba" --data-urlencode "email=may$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Restoran de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Lomo vetado al vacío' && falla "mayorista pendiente ve las cajas" || pasa "mayorista pendiente aún no ve precios de negocio"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Restoran de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Lomo vetado al vacío' && pasa "el dueño aprueba al mayorista y ya ve las cajas" || falla "aprobación de mayorista no funcionó"

# Panel: crear un corte por peso (el nombre con "(500 g)" activa el selector de gramos)
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Lomo de Prueba (500 g)" --data-urlencode "descripcion=Creado por el test" --data-urlencode "categoria_id=1" --data-urlencode "precio=5000" --data-urlencode "precio_mayorista=4000" --data-urlencode "stock=5" --data-urlencode "foto=1690983322025-aab4f95a0269" --data-urlencode "visible=1")
case "$R" in 303*) pasa "el dueño crea un producto";; *) falla "crear producto: $R";; esac
NUEVO=$(curl -s "$BASE$TIENDA")
printf '%s' "$NUEVO" | grep -qF 'Lomo de Prueba</h3>' && pasa "el producto nuevo aparece en la tienda" || falla "el producto nuevo no aparece"
printf '%s' "$NUEVO" | grep -qF '$10.000</strong> <span class="kj-precio__unidad">el kg' && pasa "el producto nuevo se vende por kilo (\$10.000 el kg)" || falla "el producto nuevo no muestra precio por kilo"
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
tools/php.sh demos/carniceria/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
