#!/usr/bin/env bash
# Pruebas del rubro botillería (demos/botilleria/): levanta `php -S` con el router y revisa con curl.
# Incluye la mayoría de edad en las 3 versiones y el flujo de la Plataforma web con cookies:
# registro → login → carrito por caja → pedido con fecha de reparto → panel del dueño → negocios.
# Uso: bash tests/demos_botilleria.sh
cd "$(dirname "$0")/.." || exit 1

PORT=$(( 8107 + ${PUERTO_DESFASE:-0} ))
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/botilleria"
SIMPLE="$RUBRO/simple/"
MULTI="$RUBRO/multi/"
TIENDA="$RUBRO/plataforma/"
AVISO_EDAD='Venta solo a mayores de 18 años; se verifica la edad en la entrega'
NOTA_ENC='%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20botiller%C3%ADa%20%E2%80%93%20'
# Ruta absoluta: el router hace chdir a la carpeta de cada página.
export PORTFOLIO_SITE_FILE="$(pwd)/${SITE_JSON:-tests/fixtures/site.json}"
export PORTFOLIO_SITE_URL="$BASE"
TMP="$(mktemp -d)"

tools/php.sh -S 127.0.0.1:$PORT tools/router.php > "$TMP/server.log" 2>&1 &
SERVER_PID=$!
trap 'kill $SERVER_PID 2>/dev/null; rm -rf "$TMP"; powershell -NoProfile -Command "Get-NetTCPConnection -LocalPort '"$PORT"' -State Listen -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id \$_.OwningProcess -Force }" >/dev/null 2>&1' EXIT
for _ in 1 2 3 4 5 6 7 8 9 10; do curl -s -o /dev/null "$BASE/" && break; sleep 0.3; done

# Base de la tienda recién creada (mismo comando que el cron).
tools/php.sh demos/botilleria/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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
debe_contener    "$RUBRO/" 'Página web para botillerías en Santiago'
debe_contener    "$RUBRO/" '"@type":"Service"'
debe_contener    "$RUBRO/" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO/" "href=\"$SIMPLE\""
debe_contener    "$RUBRO/" "href=\"$MULTI\""
debe_contener    "$RUBRO/" "href=\"$TIENDA\""
debe_contener    "$RUBRO/" 'Quiero esta'
debe_contener    "$RUBRO/" 'Perfil de Empresa en Google'
debe_contener    "$RUBRO/" 'Botillería La Pasada'
debe_contener    "$RUBRO/" 'Cava Ocre'
debe_contener    "$RUBRO/" 'Palé Distribuidora'
no_debe_contener "$RUBRO/" 'noindex'
for img in simple multi plataforma; do debe_responder "$RUBRO/img/$img.webp" 200; done
for img in simple multi plataforma; do
  T=$(stat -c %s "demos/botilleria/img/$img.webp" 2>/dev/null || echo 0)
  [ "$T" -gt 0 ] && [ "$T" -le 204800 ] && pasa "img/$img.webp pesa $T bytes (≤ 200 KB)" || falla "img/$img.webp pesa $T bytes"
done
[ -f demos/botilleria/CREDITOS.md ] && pasa "CREDITOS.md del rubro existe" || falla "falta demos/botilleria/CREDITOS.md"
[ ! -e demos/botilleria/_tmp ] && pasa "sin carpeta de trabajo _tmp" || falla "quedó demos/botilleria/_tmp"
debe_responder   "$RUBRO/edad.js" 200
debe_responder   "$RUBRO/edad.css" 200

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}catalogo.php ${MULTI}catas.php ${MULTI}preguntas.php ${MULTI}contacto.php ${MULTI}pedido.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" "href=\"$RUBRO/\""
  debe_contener    "$p" '<meta name="robots" content="noindex, follow">'
  debe_contener    "$p" '56973537642'
  debe_contener    "$p" "$AVISO_EDAD"
  debe_contener    "$p" 'data-edad="demo-botilleria-'
  debe_contener    "$p" '¿Eres mayor de 18 años?'
  debe_contener    "$p" '/demos/botilleria/edad.js?v='
  no_debe_contener "$p" 'LocalBusiness'
  no_debe_contener "$p" '"@type":"Restaurant"'
  no_debe_contener "$p" 'AggregateRating'
  no_debe_contener "$p" 'application/ld+json'
  # No es rubro de comida: sin menú 3D.
  no_debe_contener "$p" 'model-viewer'
  no_debe_contener "$p" 'demo-3d-btn'
done
# La respuesta de edad se guarda solo en sessionStorage (con try/catch), nunca en localStorage ni cookies.
grep -q 'sessionStorage' demos/botilleria/edad.js && ! grep -q 'localStorage\|document.cookie' demos/botilleria/edad.js && pasa "edad.js guarda solo en sessionStorage" || falla "edad.js usa otro almacenamiento"
[ "$(grep -c 'try {' demos/botilleria/edad.js)" -ge 2 ] && pasa "edad.js protege sessionStorage con try/catch" || falla "edad.js sin try/catch"
debe_contener "$SIMPLE" 'sessionStorage.getItem("demo-botilleria-simple-edad")'
debe_contener "$MULTI" 'sessionStorage.getItem("demo-botilleria-multi-edad")'
debe_contener "$TIENDA" 'sessionStorage.getItem("demo-botilleria-plataforma-edad")'

# Sección Google en cada versión
for p in "$SIMPLE" "$MULTI" "${MULTI}contacto.php" "$TIENDA"; do
  debe_contener "$p" 'Encuéntranos en Google'
  debe_contener "$p" 'https://www.google.com/maps?q='
  debe_contener "$p" 'output=embed'
  debe_contener "$p" 'Reseña de ejemplo'
  debe_contener "$p" 'Déjanos tu reseña'
done

# --- Simple-page: lista de precios con QR, packs, promos de fin de semana, pedido por cantidad
debe_contener    "$SIMPLE" 'data-demo-qr="#precios"'
debe_contener    "$SIMPLE" 'id="precios"'
debe_contener    "$SIMPLE" 'qrcode-generator/1.4.4/qrcode.min.js" integrity="sha384-'
debe_contener    "$SIMPLE" "${NOTA_ENC}Simple-page%29"
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
debe_contener    "$SIMPLE" 'Pack Asado'
debe_contener    "$SIMPLE" 'Pack Cumpleaños'
debe_contener    "$SIMPLE" 'Pack Familia'
debe_contener    "$SIMPLE" 'Promos de fin de semana'
debe_contener    "$SIMPLE" 'Hielo en cubos 2 kg'
debe_contener    "$SIMPLE" 'name="cantidad"'
debe_contener    "$SIMPLE" 'Lo Hermida'
R=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" "$BASE${SIMPLE}?pedir=lager-6&cantidad=2")
case "$R" in "303 https://wa.me/56973537642?text="*2%20%C3%97%20Cerveza%20lager%206%20latas%20470%20ml%20%E2%80%93%20%2413.980*Simple-page%29) pasa "pedir 2 packs arma el WhatsApp con cantidad, total y nota";; *) falla "pedido por cantidad: $R";; esac
R=$(curl -s -o /dev/null -w "%{http_code}" "$BASE${SIMPLE}?pedir=lager-6&cantidad=40")
[ "$R" = "200" ] && pasa "cantidad fuera de rango no redirige" || falla "cantidad inválida: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" "$BASE${SIMPLE}?pedir=../../etc&cantidad=1")
[ "$R" = "200" ] && pasa "producto inexistente no redirige" || falla "producto inválido: $R"

# --- Multi-page: catálogo con QR y buscador, catas con fecha, bolsa con regalo y edad
debe_contener    "${MULTI}catalogo.php" 'data-demo-qr="/demos/botilleria/multi/catalogo.php"'
debe_contener    "${MULTI}catalogo.php" 'data-buscador'
debe_contener    "${MULTI}catalogo.php" '<option value="Caja de 6">Caja de 6 · $53.890</option>'
debe_contener    "${MULTI}catalogo.php?q=itata" 'id="vino-pais-itata" data-buscable'
curl -s "$BASE${MULTI}catalogo.php?q=itata" | grep -F 'id="vino-cabernet-maipo"' | grep -qF ' hidden>' && pasa "el buscador sin JS oculta lo que no coincide" || falla "el buscador sin JS no oculta"
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '(Pedido de prueba desde la demo de botillería – Multi-page)'
debe_contener    "$MULTI" '"carmenere-cachapoal":{"nombre":"Carménère reserva","variantes":{"Botella":9990,"Caja de 6":53890}}'
debe_contener    "$MULTI" '"cata-carmenere":{"nombre":"Cata «Carménère: la cepa que volvió», 1 persona","variantes":{"'
debe_contener    "$MULTI" 'Próxima cata'
debe_contener    "${MULTI}catas.php" 'Reservar un cupo'
debe_contener    "${MULTI}catas.php" 'Quedan 2 de 18 cupos'
debe_contener    "${MULTI}pedido.php" 'data-carrito-form'
debe_contener    "${MULTI}pedido.php" 'name="mayor" required data-carrito-extra="Edad"'
debe_contener    "${MULTI}pedido.php" 'data-carrito-extra="Mensaje de la tarjeta"'
# Las catas nunca quedan en el pasado: la primera fecha es posterior a hoy.
HOY=$(TZ=America/Santiago date +%Y%m%d)
PRIMERA=$(tools/php.sh -r 'require "demos/botilleria/multi/_plantilla.php"; echo co_catas()[0]["fecha"]->format("Ymd");')
[ "$PRIMERA" -gt "$HOY" ] && pasa "la próxima cata ($PRIMERA) es posterior a hoy ($HOY)" || falla "cata en el pasado: $PRIMERA"
grep -q 'Require all denied' demos/botilleria/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/botilleria/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -q 'config|seed|reset' demos/botilleria/plataforma/.htaccess && pasa 'plataforma/.htaccess niega config, seed y reset' || falla 'plataforma/.htaccess no protege config/seed/reset'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "${MULTI}catalogo.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# Fotos: todas de images.unsplash.com con w= y q=70
FOTOS=$(for p in "$SIMPLE" "$MULTI" "${MULTI}catalogo.php" "${MULTI}catas.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE 'src="https://[^"]+"' | grep -v 'cdn.jsdelivr\|cdnjs\|google.com' | grep -vc 'images.unsplash.com/photo-[0-9a-f-]*?w=[0-9]*&amp;q=70&amp;auto=format')
[ "$FOTOS" = "0" ] && pasa "fotos enlazadas desde Unsplash con w y q=70" || falla "$FOTOS fotos con otra fuente o formato"

# --- Plataforma web: base protegida, catálogo por caja, textos de negocio
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
debe_contener    "$TIENDA" 'data-demo-qr="/demos/botilleria/plataforma/#menu"'
debe_contener    "$TIENDA" 'El catálogo, pegado en tu bodega'
debe_contener    "$TIENDA" 'Cerveza lager lata 470 ml · caja 24'
debe_contener    "$TIENDA" 'PD-0001'
# Código de bodega en el campo 'codigo' del motor (seed): «Cód. PD-0001» y búsqueda con o sin guion.
debe_contener    "$TIENDA" '<span>Cód. PD-0001</span><span class="pd-stock">Stock 80</span>'
for q in 'PD-0002' 'pd0002'; do
  debe_contener    "${TIENDA}?q=$q" 'Cerveza lager lata 350 ml · pack 6</h3>'
  no_debe_contener "${TIENDA}?q=$q" 'Cerveza lager lata 470 ml · caja 24</h3>'
done
DUPLICADOS=$(curl -s "$BASE$TIENDA" | grep -oE ' id="[^"]+"' | sort | uniq -d | wc -l)
[ "$DUPLICADOS" = "0" ] && pasa "portada sin id repetidos (destacados con prefijo)" || falla "$DUPLICADOS id repetidos en la portada"
debe_contener    "$TIENDA" 'Venta responsable'
debe_contener    "$TIENDA" 'Crear cuenta de negocio'
no_debe_contener "$TIENDA" 'Pisco 35° 1 L · caja 12'
debe_contener    "${TIENDA}?q=hielo" 'Hielo en cubos · saco 10 kg'
no_debe_contener "${TIENDA}?q=hielo" 'Gin seco 700 ml'
debe_contener    "${TIENDA}?cat=eventos" 'Barril de cerveza lager 30 L'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'mayorista@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_contener    "${TIENDA}?r=registro" 'Almacenes, botillerías, restaurantes y productoras de eventos.'
debe_contener    "${TIENDA}?r=registro" 'Al crear una cuenta declaras ser mayor de 18 años.'
debe_responder   "${TIENDA}?r=no-existe" 404

# --- Flujo con cookies (textos ASCII o ya codificados: Git Bash en Windows no pasa bien UTF-8 a curl.exe)
J1="$TMP/cliente.txt"; J2="$TMP/dueno.txt"; J3="$TMP/negocio.txt"
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

# Primer destacado: cerveza lager caja 24 ($23.990). Se agregan 2 cajas con el selector de cantidad.
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
printf '%s' "$INICIO" | grep -qF "id=\"cant-d$PID\"" && printf '%s' "$INICIO" | grep -qF "id=\"cant-c$PID\"" && pasa "selector de cantidad con id único en destacados y catálogo" || falla "ids de cantidad repetidos o ausentes"
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=2")
case "$R" in 303*) pasa "agregar 2 cajas al carrito ($R)";; *) falla "agregar al carrito: $R";; esac
CARRO=$(curl -s -b "$J1" "$BASE${TIENDA}?r=carrito")
printf '%s' "$CARRO" | grep -qF 'Cerveza lager lata 470 ml · caja 24' && pasa "el carrito tiene el producto" || falla "carrito vacío"
printf '%s' "$CARRO" | grep -qF '$47.980' && pasa "el carrito suma 2 cajas (\$47.980)" || falla "subtotal del carrito inesperado"
printf '%s' "$CARRO" | grep -qF "$AVISO_EDAD" && pasa "el carrito recuerda la verificación de edad" || falla "carrito sin aviso de edad"

CHECK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
T=$(printf '%s' "$CHECK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
FECHA=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | head -1 | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')
DOMINGOS=0
for f in $(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}'); do
  [ "$(date -d "$f" +%u)" = "7" ] && DOMINGOS=$((DOMINGOS + 1))
done
[ -n "$FECHA" ] && [ "$DOMINGOS" = "0" ] && [ "$(date -d "$FECHA" +%Y%m%d)" -gt "$(TZ=America/Santiago date +%Y%m%d)" ] && pasa "reparto desde mañana y sin domingos (primero: $FECHA)" || falla "días de reparto: primero '$FECHA', domingos $DOMINGOS"
printf '%s' "$CHECK" | grep -qF 'Reparto en camión' && pasa "checkout con textos propios del rubro" || falla "checkout sin textos propios"
# Mayoría de edad: casilla requerida del motor ('checkout_campos'), validada en el servidor.
printf '%s' "$CHECK" | grep -qF '<input type="checkbox" id="extra-mayor" name="extra[mayor]" value="1" aria-describedby="ayuda-extra-mayor" required> Confirmo que soy mayor de 18 años</label>' && pasa "checkout con la casilla requerida «Confirmo que soy mayor de 18 años»" || falla "checkout sin la casilla de edad"
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Quilicura" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir")
case "$R" in 422*) grep -qF 'Debes marcar «Confirmo que soy mayor de 18 años».' "$TMP/ultima.html" && pasa "sin marcar la mayoría de edad el pedido se rechaza (422 con el aviso)" || falla "422 sin el aviso de edad";; *) falla "pedido sin confirmar la edad: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Quilicura" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir" --data-urlencode "extra[mayor]=0")
case "$R" in 422*) pasa "la casilla con valor 0 no cuenta como marcada (422)";; *) falla "casilla con valor 0 aceptada: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Quilicura" --data-urlencode "fecha_entrega=2020-01-06" --data-urlencode "pago=al_recibir" --data-urlencode "extra[mayor]=1")
case "$R" in 422*) pasa "fecha de reparto fuera de la lista se rechaza";; *) falla "fecha inválida aceptada: $R";; esac
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Quilicura" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir" --data-urlencode "nota=Recibe la conserjeria" --data-urlencode "extra[mayor]=1")
case "$R" in 303*r=pedido*) pasa "checkout crea el pedido ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'PD-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página del pedido muestra $CODIGO" || falla "página del pedido sin código"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "pedido ofrece enviar resumen por WhatsApp" || falla "pedido sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF "${NOTA_ENC}Plataforma%20web%29" && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
# 2 × $23.990 + despacho Quilicura $4.990 (gratis recién sobre $60.000) = $52.970
printf '%s' "$PAGINA" | grep -qF '$52.970' && pasa "total con despacho a Quilicura (\$52.970)" || falla "total del pedido inesperado"
printf '%s' "$PAGINA" | grep -qF 'Recibe%20la%20conserjeria' && pasa "la nota del cliente va en el resumen de WhatsApp" || falla "resumen sin la nota del cliente"
printf '%s' "$PAGINA" | grep -qF '<div><dt>Confirmo que soy mayor de 18 años</dt><dd>Sí</dd></div>' && pasa "el pedido muestra la confirmación de edad" || falla "pedido sin la confirmación de edad"
printf '%s' "$PAGINA" | grep -qF 'Confirmo%20que%20soy%20mayor%20de%2018%20a%C3%B1os%3A%20S%C3%AD' && pasa "la confirmación de edad va en el resumen de WhatsApp" || falla "resumen sin la confirmación de edad"

# Pedido mínimo $15.000: un pack de 6 latas ($5.490) no alcanza
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
printf '%s' "$INICIO" | grep -qF 'PD-0002' && pasa "el pack de 6 latas tiene código PD-0002" || falla "sin código PD-0002"
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=2" --data-urlencode "cantidad=1" > /dev/null
CARRO=$(curl -s -b "$J1" "$BASE${TIENDA}?r=carrito")
printf '%s' "$CARRO" | grep -qF 'data-falta-minimo>Te faltan $9.510 para el pedido mínimo de $15.000.</p>' && pasa "el carrito del motor avisa el pedido mínimo y cuánto falta" || falla "sin aviso de pedido mínimo"
[ "$(printf '%s' "$CARRO" | grep -o 'Te faltan $9.510' | wc -l)" = "1" ] && pasa "el aviso del pedido mínimo sale una sola vez" || falla "aviso del pedido mínimo repetido"
printf '%s' "$CARRO" | grep -qF 'aria-disabled="true" aria-describedby="falta-minimo">Continuar</a>' && pasa "bajo el mínimo el carrito no deja continuar" || falla "bajo el mínimo el carrito deja continuar"
printf '%s' "$CARRO" | grep -qF 'Te faltan $54.510 para el reparto gratis.' && pasa "el carrito dice cuánto falta para el reparto gratis" || falla "carrito sin aviso de reparto gratis"
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=al_recibir" --data-urlencode "extra[mayor]=1")
case "$R" in 422*) grep -qF 'Te faltan $9.510 para el pedido mínimo de $15.000.' "$TMP/ultima.html" && pasa "bajo el mínimo el checkout no crea el pedido (422 con el aviso del carrito)" || falla "bajo el mínimo: 422 sin el aviso";; *) falla "pedido bajo el mínimo: $R";; esac

# Pago de prueba: checkout → pantalla simulada → aprobar
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=prueba" --data-urlencode "extra[mayor]=1")
case "$R" in 303*r=pago*) pasa "pago de prueba lleva a la pantalla simulada";; *) falla "pago de prueba: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=pago" | grep -qF 'PAGO DE PRUEBA' && pasa "pantalla de pago marcada como prueba" || falla "pantalla de pago sin aviso"
T=$(csrf "$J1" "${TIENDA}?r=pago")
R=$(post "$J1" "${TIENDA}?r=pago" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar")
case "$R" in 303*r=pedido*) pasa "aprobar pago de prueba crea el pedido";; *) falla "aprobar pago: $R";; esac
curl -s -b "$J1" "${R#* }" | grep -qF 'Tu pedido quedó agendado para reparto.' && pasa "mensaje de éxito propio del rubro" || falla "sin mensaje de éxito propio"

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
printf '%s' "$PANEL" | grep -qF 'Confirmo que soy mayor de 18 años: Sí' && pasa "el panel muestra la confirmación de edad del pedido" || falla "el panel no muestra la confirmación de edad"
RESUMEN=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel")
printf '%s' "$RESUMEN" | grep -qF 'Ventas de hoy' && pasa "panel con ventas del día" || falla "panel sin ventas del día"
printf '%s' "$RESUMEN" | grep -qF 'Stock bajo: Limones malla 3 kg' && pasa "panel avisa stock bajo" || falla "panel sin aviso de stock bajo"
printf '%s' "$RESUMEN" | grep -qF 'Negocios (1)' && pasa "panel cuenta los negocios por aprobar con el texto del rubro" || falla "panel sin pestaña de negocios"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "un cliente no entra al panel (403)" || falla "un cliente entra al panel"

# Estado: nuevo → preparando
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">Preparando' && pasa "el cliente ve su pedido en «Preparando»" || falla "estado del pedido no cambió ($R)"

# Negocio (mayorista): precio negocio y formato almacén; pendiente hasta aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
NEG=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$NEG" | grep -qF 'Pisco 35° 1 L · caja 12' && pasa "el negocio ve el formato almacén" || falla "el negocio no ve el formato almacén"
printf '%s' "$NEG" | grep -qF '<strong>$19.990</strong>' && printf '%s' "$NEG" | grep -qF '<s aria-label="Precio normal">$23.990</s> <span class="t-etiqueta">Precio negocio</span>' && pasa "el negocio ve precio negocio (\$19.990 la caja 24)" || falla "el negocio sin precio negocio"
printf '%s' "$NEG" | grep -qF 'Modo negocio' && pasa "aviso de modo negocio" || falla "sin aviso de modo negocio"
J4="$TMP/nuevo-negocio.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Almacen Prueba" --data-urlencode "email=neg$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Almacen de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Pisco 35° 1 L · caja 12' && falla "negocio pendiente ve el formato almacén" || pasa "negocio pendiente aún no ve precios de negocio"
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'patente de alcoholes' && pasa "el negocio pendiente ve por qué espera" || falla "sin aviso de cuenta pendiente"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Almacen de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Pisco 35° 1 L · caja 12' && pasa "el dueño aprueba al negocio y ya ve el formato almacén" || falla "aprobación del negocio no funcionó"

# Panel: crear un producto y validar el largo del nombre
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Hielo de Prueba saco 5 kg" --data-urlencode "descripcion=Creado por el test" --data-urlencode "categoria_id=5" --data-urlencode "precio=2990" --data-urlencode "precio_mayorista=2490" --data-urlencode "stock=5" --data-urlencode "foto=1758001969632-c67b15c89f1f" --data-urlencode "visible=1")
case "$R" in 303*) pasa "el dueño crea un producto";; *) falla "crear producto: $R";; esac
curl -s "$BASE$TIENDA" | grep -qF 'Hielo de Prueba saco 5 kg</h3>' && pasa "el producto nuevo aparece en la tienda" || falla "el producto nuevo no aparece"
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
tools/php.sh demos/botilleria/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
