#!/usr/bin/env bash
# Pruebas del rubro tienda de repuestos de auto (demos/repuestos/): levanta `php -S` con el router y revisa con curl.
# Incluye: cotización por patente de la Simple-page (sin JS, POST → WhatsApp), buscador por vehículo y
# campos extra del carrito de la Multi-page, y en la Plataforma web la búsqueda por patente de ejemplo,
# por vehículo y por código, más el flujo con cookies: registro → login → carrito → pedido con despacho
# (día y hora) → panel del dueño, talleres asociados (mayoristas) y CSRF.
# Uso: bash tests/demos_repuestos.sh
cd "$(dirname "$0")/.." || exit 1

PORT=$(( 8106 + ${PUERTO_DESFASE:-0} ))
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/repuestos"
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
tools/php.sh demos/repuestos/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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
debe_contener    "$RUBRO/" 'Página web para tiendas de repuestos en Santiago'
debe_contener    "$RUBRO/" '"@type":"Service"'
debe_contener    "$RUBRO/" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO/" "href=\"$SIMPLE\""
debe_contener    "$RUBRO/" "href=\"$MULTI\""
debe_contener    "$RUBRO/" "href=\"$TIENDA\""
debe_contener    "$RUBRO/" 'Quiero esta'
debe_contener    "$RUBRO/" 'Perfil de Empresa en Google'
no_debe_contener "$RUBRO/" 'noindex'
for img in simple multi plataforma; do debe_responder "$RUBRO/img/$img.webp" 200; done
[ -f demos/repuestos/CREDITOS.md ] && pasa "CREDITOS.md del rubro existe" || falla "falta demos/repuestos/CREDITOS.md"

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}catalogo.php ${MULTI}guia.php ${MULTI}nosotros.php ${MULTI}contacto.php ${MULTI}cotizar.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" "href=\"$RUBRO/\""
  debe_contener    "$p" '<meta name="robots" content="noindex, follow">'
  debe_contener    "$p" '56973537642'
  no_debe_contener "$p" 'LocalBusiness'
  no_debe_contener "$p" 'AutoPartsStore'
  no_debe_contener "$p" 'AggregateRating'
  no_debe_contener "$p" 'application/ld+json'
  # Repuestos: no es rubro de comida, sin menú QR ni 3D
  no_debe_contener "$p" 'model-viewer'
  no_debe_contener "$p" 'demo-3d-btn'
  no_debe_contener "$p" 'data-demo-qr'
done

# Productos genéricos: ninguna marca de autos ni de fabricantes de repuestos en el texto de las páginas
MARCAS='\b(toyota|chevrolet|nissan|hyundai|kia|suzuki|mazda|peugeot|renault|volkswagen|citro[eë]n|honda|mitsubishi|subaru|chery|geely|great wall|mg motor|bmw|mercedes|audi|fiat|jeep|ram|dodge|bosch|brembo|castrol|mobil|shell|liqui moly|michelin|monroe|valeo|ngk|denso|mann-filter|mahle|sachs|luk|gates|trw|ferodo|energizer|varta|wix)\b'
MARCADAS=$(for p in $SIMPLE $MULTI ${MULTI}catalogo.php ${MULTI}guia.php ${MULTI}nosotros.php $TIENDA "${TIENDA}?patente=KXPL47"; do curl -s "$BASE$p" | sed -e 's/<[^>]*>/ /g'; done | grep -oiE "$MARCAS" | sort -u | tr '\n' ' ')
[ -z "$MARCADAS" ] && pasa "sin marcas de autos ni de repuestos en los textos" || falla "marcas en los textos: $MARCADAS"

# Sección Google en cada versión
for p in "$SIMPLE" "$MULTI" "${MULTI}contacto.php" "$TIENDA"; do
  debe_contener "$p" 'Encuéntranos en Google'
  debe_contener "$p" 'https://www.google.com/maps?q='
  debe_contener "$p" 'output=embed'
  debe_contener "$p" 'Reseña de ejemplo'
  debe_contener "$p" 'Déjanos tu reseña'
done

# --- Simple-page: repuestos con código y precio, y «Cotiza con tu patente» que arma el WhatsApp
debe_contener    "$SIMPLE" 'id="cotiza"'
debe_contener    "$SIMPLE" 'id="repuestos"'
debe_contener    "$SIMPLE" 'id="calidades"'
debe_contener    "$SIMPLE" 'id="horario"'
debe_contener    "$SIMPLE" 'Pastillas de freno delanteras cerámicas'
debe_contener    "$SIMPLE" 'RP-0101'
debe_contener    "$SIMPLE" '$24.990'
debe_contener    "$SIMPLE" 'City car y hatchback 1.2–1.6 · 2012–2020'
debe_contener    "$SIMPLE" 'name="patente"'
debe_contener    "$SIMPLE" '<option value="Conchalí">Conchalí · $2.500</option>'
debe_contener    "$SIMPLE" '%28Cotizaci%C3%B3n%20de%20prueba%20desde%20la%20demo%20de%20repuestos%20de%20auto%20%E2%80%93%20Simple-page%29'
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
R=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" --data-urlencode "patente=kx-pl 47" --data-urlencode "repuesto=pastillas de freno delanteras" --data-urlencode "entrega=retiro" "$BASE$SIMPLE")
case "$R" in "303 https://wa.me/56973537642?text="*Repuesto%3A%20pastillas%20de%20freno%20delanteras*Patente%3A%20KX%C2%B7PL%C2%B747*Entrega%3A%20retiro%20en%20el%20mes%C3%B3n*Simple-page%29) pasa "la patente se normaliza (KX·PL·47) y arma el WhatsApp con repuesto, retiro y la nota";; *) falla "cotiza con patente: $R";; esac
R=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" --data-urlencode "repuesto=kit de embrague" --data-urlencode "vehiculo=Sedan familiar" --data-urlencode "anio=2016" --data-urlencode "motor=1.6" --data-urlencode "entrega=despacho" --data "comuna=Conchal%C3%AD" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "nombre=Ana" "$BASE$SIMPLE")
case "$R" in "303 https://wa.me/56973537642?text="*Veh%C3%ADculo%3A%20Sedan%20familiar*A%C3%B1o%3A%202016*Motor%3A%201.6*Despacho%3A%20Calle%20de%20Prueba%20123%2C%20Conchal%C3%AD%20%28%242.500%29*Nombre%3A%20Ana*) pasa "sin patente: vehículo, año, motor y despacho con costo";; *) falla "cotiza sin patente: $R";; esac
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "patente=KXPL47" --data-urlencode "repuesto= " --data-urlencode "entrega=retiro" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'qué repuesto necesitas' "$TMP/simple.html" && pasa "sin repuesto: 422 con el error junto al campo" || falla "sin repuesto: $R"
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "patente=ABC" --data-urlencode "repuesto=bujias" --data-urlencode "entrega=retiro" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'no tiene el formato chileno' "$TMP/simple.html" && pasa "patente con formato inválido se rechaza" || falla "patente inválida: $R"
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "patente=AEIO12" --data-urlencode "repuesto=bujias" --data-urlencode "entrega=retiro" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "patente nueva con vocales (AEIO12) se rechaza" || falla "patente con vocales: $R"
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "repuesto=bujias" --data-urlencode "entrega=retiro" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'Escribe la patente o' "$TMP/simple.html" && pasa "sin patente ni vehículo: pide uno de los dos" || falla "sin patente ni vehículo: $R"
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "patente=KXPL47" --data-urlencode "repuesto=bujias" --data-urlencode "anio=20x6" --data-urlencode "entrega=retiro" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'año con 4 números' "$TMP/simple.html" && pasa "año inválido se rechaza" || falla "año inválido: $R"
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "patente=KXPL47" --data-urlencode "repuesto=bujias" --data-urlencode "entrega=despacho" --data-urlencode "comuna=../../etc" --data-urlencode "calle=Calle 1" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'Elige una comuna de la lista' "$TMP/simple.html" && pasa "comuna fuera de la lista no redirige" || falla "comuna inválida: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "patente=KXPL47" --data-urlencode "repuesto=bujias" --data-urlencode "entrega=despacho" --data "comuna=Conchal%C3%AD" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "despacho sin dirección no redirige" || falla "despacho sin dirección: $R"
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode 'repuesto=<script>alert(1)</script>' --data-urlencode 'vehiculo="><img src=x>' --data-urlencode "entrega=x" "$BASE$SIMPLE")
[ "$R" = "422" ] && ! grep -qF '<script>alert(1)</script>' "$TMP/simple.html" && ! grep -qF '"><img src=x>' "$TMP/simple.html" && pasa "lo enviado vuelve escapado en el formulario" || falla "escapado del formulario: $R"

# --- Multi-page: buscador por vehículo y por código, fichas con medidas y cotización con campos extra
debe_contener    "${MULTI}catalogo.php" 'data-buscador-raiz'
debe_contener    "${MULTI}catalogo.php" 'data-vehiculo'
debe_contener    "${MULTI}catalogo.php" 'class="demo-agregar cb-agregar" data-id="pastilla-ciudad"'
debe_contener    "${MULTI}catalogo.php" 'data-tipos="city hatch"'
debe_contener    "${MULTI}catalogo.php" 'data-cc="1.2 1.6"'
debe_contener    "${MULTI}catalogo.php" 'data-anios="2012 2020"'
debe_contener    "${MULTI}catalogo.php" '<option value="Par">Par · $52.990</option>'
debe_contener    "${MULTI}catalogo.php" 'Ø 256 mm · espesor 22 mm'
F="${MULTI}catalogo.php?tipo=suv&anio=2017&cc=2.0"
curl -s "$BASE$F" | grep -F 'id="p-pastilla-suv"' | grep -qvF ' hidden>' && pasa "SUV 2.0 2017: muestra las pastillas para SUV" || falla "filtro por vehículo no muestra lo que calza"
curl -s "$BASE$F" | grep -A3 -F 'id="p-pastilla-ciudad"' | grep -qF ' hidden>' && pasa "SUV 2.0 2017: oculta las pastillas de city car (sin JS)" || falla "filtro por vehículo no oculta lo que no calza"
curl -s "$BASE$F" | grep -A3 -F 'id="p-dot4"' | grep -qF ' hidden>' && falla "el líquido de frenos (universal) quedó oculto" || pasa "los repuestos universales siempre calzan"
debe_contener    "$F" 'repuestos calzan con SUV · motor 2.0 · año 2017'
debe_contener    "${MULTI}catalogo.php?q=CB2101" '<strong>1</strong> repuesto calza para «CB2101»'
debe_contener    "${MULTI}catalogo.php?tipo=../../x&anio=abcd&cc=9.9" '<strong>24</strong> repuestos en el catálogo'
debe_contener    "${MULTI}cotizar.php" 'data-carrito-form'
for extra in Patente 'Vehículo' 'Año' Motor VIN; do
  debe_contener  "${MULTI}cotizar.php" "data-carrito-extra=\"$extra\""
done
debe_contener    "${MULTI}cotizar.php" 'name="entrega" value="despacho"'
debe_contener    "${MULTI}cotizar.php" '<option value="Santiago">Santiago · $2.990</option>'
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '(Cotización de prueba desde la demo de repuestos de auto – Multi-page)'
# Carrito común con textos de cotización (el mensaje lo arma carrito.js con estos textos)
debe_contener    "$MULTI" '"saludo":"Hola, quiero cotizar en {negocio}:"'
debe_contener    "${MULTI}cotizar.php" '"ver_pedido":"Ver tu cotización ({n} repuestos)"'
debe_contener    "$MULTI" '"CB-2101 Pastillas de freno delanteras cerámicas","variantes":{"Juego eje delantero":24990}'
debe_contener    "$MULTI" '/demos/repuestos/multi/calibre.js?v='
debe_responder   "${MULTI}calibre.js" 200
debe_contener    "${MULTI}guia.php" 'MIN TH'
debe_contener    "${MULTI}guia.php" '¿Cambio discos y pastillas juntos?'
grep -q 'Require all denied' demos/repuestos/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/repuestos/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -q 'config|seed|reset' demos/repuestos/plataforma/.htaccess && pasa 'plataforma/.htaccess niega config, seed y reset' || falla 'plataforma/.htaccess no protege config/seed/reset'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "${MULTI}catalogo.php" "${MULTI}cotizar.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# Fotos: todas de images.unsplash.com con w= y q=70
FOTOS=$(for p in "$SIMPLE" "$MULTI" "${MULTI}catalogo.php" "${MULTI}guia.php" "${MULTI}nosotros.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE 'src="https://[^"]+"' | grep -v 'cdn.jsdelivr\|cdnjs\|google.com' | grep -vc 'images.unsplash.com/photo-[0-9a-f-]*?w=[0-9]*&amp;q=70&amp;auto=format')
[ "$FOTOS" = "0" ] && pasa "fotos enlazadas desde Unsplash con w y q=70" || falla "$FOTOS fotos con otra fuente o formato"

# --- Plataforma web: base protegida, búsqueda por patente, por vehículo y por código
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
no_debe_contener "${TIENDA}config.php" 'dueno123'
debe_contener    "$TIENDA" 'Pastillas de freno delanteras cerámicas'
debe_contener    "$TIENDA" '<span class="cd-sku">CD-1101</span>'
no_debe_contener "$TIENDA" 'CD-1101 · '
debe_contener    "$TIENDA" 'City car, hatchback · 1.2–1.6 · 2012–2020</span>'
debe_contener    "$TIENDA" 'En bodega: 38'
debe_contener    "$TIENDA" 'Quedan 3'
debe_contener    "$TIENDA" 'Por patente'
debe_contener    "$TIENDA" 'Próximo despacho'
debe_contener    "$TIENDA" '¿Tienes un taller?'
no_debe_contener "$TIENDA" 'Caja de 24 filtros de aceite'
debe_contener    "${TIENDA}?patente=kx-pl-47" 'que calzan con: SUV · 2.0 bencina · 2017'
debe_contener    "${TIENDA}?patente=KXPL47" 'Pastillas de freno delanteras para SUV</h3>'
no_debe_contener "${TIENDA}?patente=KXPL47" 'Pastillas de freno delanteras cerámicas</h3>'
debe_contener    "${TIENDA}?patente=KXPL47" 'Líquido de frenos DOT 4, 1 litro</h3>'
debe_contener    "${TIENDA}?patente=KXPL47" 'Calza con tu vehículo'
debe_contener    "${TIENDA}?patente=AB12" 'no tiene el formato chileno'
debe_contener    "${TIENDA}?patente=ZZZZ99" 'solo reconocemos las patentes de ejemplo'
debe_contener    "${TIENDA}?patente=ZZZZ99" 'patente%20ZZ%C2%B7ZZ%C2%B799'
debe_contener    "${TIENDA}?tipo=city&anio=2015&cc=1.2" 'que calzan con: City car · 1.2 · 2015'
debe_contener    "${TIENDA}?tipo=city&anio=2015&cc=1.2" 'Kit de embrague 3 piezas</h3>'
no_debe_contener "${TIENDA}?tipo=city&anio=2015&cc=1.2" 'Batería 12 V 90 Ah para diésel</h3>'
debe_contener    "${TIENDA}?q=cd1101" 'Pastillas de freno delanteras cerámicas</h3>'
no_debe_contener "${TIENDA}?q=cd1101" 'Batería 12 V 60 Ah</h3>'
debe_contener    "${TIENDA}?q=CD-4201" 'Batería 12 V 60 Ah</h3>'
debe_contener    "${TIENDA}?q=cd+4201" 'Batería 12 V 60 Ah</h3>'
debe_contener    "${TIENDA}?q=freno+pastillas" 'Pastillas de freno delanteras para SUV</h3>'
no_debe_contener "${TIENDA}?q=freno+pastillas" 'Par de discos de freno ventilados 280 mm</h3>'
# Destacados con prefijo en el id: ningún id se repite en la portada
REPETIDOS=$(curl -s "$BASE$TIENDA" | grep -oE ' id="[^"]+"' | sort | uniq -d | wc -l)
[ "$REPETIDOS" = "0" ] && pasa "portada sin ids repetidos" || falla "portada con $REPETIDOS ids repetidos"
# Columna de categorías: «Para talleres» solo para talleres asociados (m_categorias_visibles)
no_debe_contener "$TIENDA" 'cat=talleres#menu'
debe_contener    "${TIENDA}?cat=encendido" 'Juego de 4 bujías de iridio'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'mayorista@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_contener    "${TIENDA}?r=registro" 'Talleres mecánicos, lubricentros y flotas con RUT.'
debe_contener    "${TIENDA}?r=registro" '<strong>Taller asociado</strong>'
debe_responder   "${TIENDA}?r=no-existe" 404

# --- Flujo con cookies (textos ASCII o ya codificados: Git Bash en Windows no pasa bien UTF-8 a curl.exe)
J1="$TMP/cliente.txt"; J2="$TMP/dueno.txt"; J3="$TMP/taller.txt"; J5="$TMP/vehiculo.txt"
csrf() { curl -s -b "$1" -c "$1" "$BASE$2" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/'; }
post() { local jar=$1 url=$2; shift 2; curl -s -o "$TMP/ultima.html" -b "$jar" -c "$jar" -w "%{http_code} %{redirect_url}" "$@" "$BASE$url"; }

# El vehículo elegido queda en la sesión: sigue filtrando al cambiar de categoría, hasta quitarlo
curl -s -b "$J5" -c "$J5" "$BASE${TIENDA}?patente=KXPL47" > /dev/null
curl -s -b "$J5" -c "$J5" "$BASE${TIENDA}?cat=frenos" | grep -qF 'que calzan con: SUV · 2.0 bencina · 2017' && pasa "el vehículo sigue filtrando en otra categoría" || falla "el vehículo no quedó en la sesión"
curl -s -b "$J5" -c "$J5" "$BASE${TIENDA}?r=carrito" | grep -qF 'class="cd-chip-auto"' && pasa "la cabecera muestra el vehículo elegido" || falla "la cabecera no muestra el vehículo"
curl -s -b "$J5" -c "$J5" "$BASE${TIENDA}?vehiculo=quitar" | grep -qF 'que calzan con:' && falla "quitar vehículo no lo borra" || pasa "quitar vehículo vuelve al catálogo completo"

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

# Primer destacado: pastillas delanteras ($24.990), 2 juegos
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=2")
case "$R" in 303*) pasa "agregar 2 juegos de pastillas al carro ($R)";; *) falla "agregar al carrito: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF '<strong>Pastillas de freno delanteras cerámicas</strong>' && pasa "el carro tiene las pastillas" || falla "carro vacío"

# Checkout: día de despacho calculado en el servidor (sin domingos ni feriados)
CHECK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
printf '%s' "$CHECK" | grep -qF 'Día de despacho o retiro' && pasa "checkout pide el día de despacho" || falla "checkout sin selector de día"
printf '%s' "$CHECK" | grep -qF 'Despacho en camioneta' && printf '%s' "$CHECK" | grep -qF 'Retiro en bodega' && pasa "checkout con «Despacho en camioneta» y «Retiro en bodega»" || falla "checkout sin textos del rubro"
printf '%s' "$CHECK" | grep -qF 'Desde las 09:00' && pasa "horas «Desde las …»" || falla "horas sin el texto del rubro"
printf '%s' "$CHECK" | grep -qF 'escribe la patente o el VIN' && falla "la ayuda aún pide la patente en la nota" || pasa "la patente ya no se pide en la nota"
printf '%s' "$CHECK" | grep -qF 'Tu vehículo</p>' && pasa "checkout con el título «Tu vehículo»" || falla "checkout sin el título de los datos del vehículo"
for c in patente vehiculo anio motor vin; do
  printf '%s' "$CHECK" | grep -qF "name=\"extra[$c]\"" && pasa "checkout pide extra[$c] (checkout_campos)" || falla "checkout sin el campo extra[$c]"
done
FECHA=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | head -1 | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')
DOMINGOS=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}' | while read -r d; do date -d "$d" +%u; done | grep -c '^7$')
[ -n "$FECHA" ] && [ "$DOMINGOS" = "0" ] && pasa "días ofrecidos sin domingos (primero: $FECHA)" || falla "días ofrecidos: primero '$FECHA', domingos $DOMINGOS"
HOY=$(TZ=America/Santiago date +%F)
printf '%s' "$CHECK" | grep -qF "option value=\"$HOY\"" && falla "ofrece despacho hoy (dias_min = 1)" || pasa "no ofrece despacho el mismo día"
T=$(printf '%s' "$CHECK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
# Datos del vehículo válidos: patente y VIN opcionales (se normalizan: "kx-pl 47" → KXPL47)
AUTO=(--data-urlencode "extra[patente]=kx-pl 47" --data-urlencode "extra[vehiculo]=SUV mediano" --data-urlencode "extra[anio]=2017" --data-urlencode "extra[motor]=2.0 bencina")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=2020-01-06" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir" "${AUTO[@]}")
case "$R" in 422*) pasa "un día fuera de la lista se rechaza (422)";; *) falla "día inválido aceptado: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=19:00" --data-urlencode "pago=al_recibir" "${AUTO[@]}")
case "$R" in 422*) pasa "una hora fuera del horario se rechaza (422)";; *) falla "hora inválida aceptada: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Narnia" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=al_recibir" "${AUTO[@]}")
case "$R" in 422*) pasa "despacho a una comuna sin cobertura se rechaza (422)";; *) falla "comuna inválida aceptada: $R";; esac
# Sin los datos del vehículo: 422 con el error de cada campo requerido (patente y VIN son opcionales)
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=al_recibir")
case "$R" in 422*) pasa "pedido sin datos del vehículo se rechaza (422)";; *) falla "pedido sin datos del vehículo aceptado: $R";; esac
for t in 'Completa «Vehículo».' 'Completa «Año».' 'Completa «Motor».'; do
  grep -qF "$t" "$TMP/ultima.html" && pasa "error del campo extra: $t" || falla "falta el error: $t"
done
grep -qF 'id="err-extra_patente"' "$TMP/ultima.html" && falla "la patente (opcional) dio error vacía" || pasa "la patente es opcional"
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=al_recibir" --data-urlencode "extra[patente]=AEIO12" --data-urlencode "extra[vin]=123" --data-urlencode "extra[vehiculo]=SUV" --data-urlencode "extra[anio]=2017" --data-urlencode "extra[motor]=2.0")
case "$R" in 422*) grep -qF 'no tiene el formato chileno' "$TMP/ultima.html" && grep -qF 'El VIN tiene 17 letras y números' "$TMP/ultima.html" && pasa "patente con vocales y VIN corto: 422 con sus errores" || falla "patente o VIN inválidos sin mensaje";; *) falla "patente o VIN inválidos aceptados: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Cerrillos" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir" --data-urlencode "nota=Recibe Juan" --data-urlencode "extra[vin]=1hg-cm82633a004352" "${AUTO[@]}")
case "$R" in 303*r=pedido*) pasa "checkout crea el pedido con los datos del vehículo ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'CR-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página del pedido muestra $CODIGO" || falla "página del pedido sin código"
printf '%s' "$PAGINA" | grep -qF '¡Pedido en bodega!' && pasa "título propio del rubro (¡Pedido en bodega!)" || falla "sin el título del rubro"
printf '%s' "$PAGINA" | grep -qF ', desde las 10:00' && pasa "el pedido muestra «…, desde las 10:00»" || falla "el pedido sin el día y la hora"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "pedido ofrece enviar resumen por WhatsApp" || falla "pedido sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20repuestos%20de%20auto%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
printf '%s' "$PAGINA" | grep -qF 'a%20las%2010%3A00' && pasa "resumen con el día y la hora" || falla "resumen sin la hora"
printf '%s' "$PAGINA" | grep -qF 'Nota%3A%20Recibe%20Juan' && pasa "resumen con la nota del cliente" || falla "resumen sin la nota del cliente"
for x in 'Patente%3A%20KXPL47' 'Veh%C3%ADculo%3A%20SUV%20mediano' 'A%C3%B1o%3A%202017' 'Motor%3A%202.0%20bencina' 'VIN%3A%201HGCM82633A004352'; do
  printf '%s' "$PAGINA" | grep -qF "$x" && pasa "WhatsApp con $x" || falla "WhatsApp sin $x"
done
printf '%s' "$PAGINA" | grep -qF '<dt>Patente</dt><dd>KXPL47</dd>' && pasa "la página del pedido muestra la patente normalizada" || falla "la página del pedido sin la patente"
# 2 × $24.990 = $49.980 + despacho a Cerrillos $2.990 = $52.970
printf '%s' "$PAGINA" | grep -qF '$52.970' && pasa "total con despacho a Cerrillos (\$52.970)" || falla "total del pedido inesperado"

# Pago de prueba: checkout → pantalla simulada → aprobar
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=prueba" "${AUTO[@]}")
case "$R" in 303*r=pago*) pasa "pago de prueba lleva a la pantalla simulada";; *) falla "pago de prueba: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=pago" | grep -qF 'PAGO DE PRUEBA' && pasa "pantalla de pago marcada como prueba" || falla "pantalla de pago sin aviso"
T=$(csrf "$J1" "${TIENDA}?r=pago")
R=$(post "$J1" "${TIENDA}?r=pago" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar")
case "$R" in 303*r=pedido*) pasa "aprobar pago de prueba crea el pedido";; *) falla "aprobar pago: $R";; esac
curl -s -b "$J1" "${R#* }" | grep -qF 'Ya estamos preparando tu pedido en bodega' && pasa "mensaje de éxito propio del rubro" || falla "sin mensaje de éxito del rubro"

# Repetir desde el historial
T=$(csrf "$J1" "${TIENDA}?r=cuenta")
PEDIDO_ID=$(curl -s -b "$J1" "$BASE${TIENDA}?r=cuenta" | grep -oE 'name="pedido_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
R=$(post "$J1" "${TIENDA}?r=cuenta" --data-urlencode "csrf=$T" --data-urlencode "accion=repetir" --data-urlencode "pedido_id=$PEDIDO_ID")
case "$R" in 303*r=carrito*) pasa "repetir un pedido vuelve a llenar el carro";; *) falla "repetir: $R";; esac

# El dueño ve el pedido en su panel
T=$(csrf "$J2" "${TIENDA}?r=ingresar")
R=$(post "$J2" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=dueno@demo.cl" --data-urlencode "clave=dueno123")
case "$R" in 303*r=panel*) pasa "dueño entra a su panel";; *) falla "login dueño: $R";; esac
PANEL=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=pedidos")
printf '%s' "$PANEL" | grep -qF "$CODIGO" && pasa "el dueño ve $CODIGO en el panel" || falla "el dueño no ve el pedido"
printf '%s' "$PANEL" | grep -qF 'Cliente Prueba' && pasa "el panel muestra al cliente" || falla "el panel no muestra al cliente"
printf '%s' "$PANEL" | grep -qF ' · para el ' && pasa "el panel muestra el día de cada despacho" || falla "el panel sin el día del despacho"
printf '%s' "$PANEL" | grep -qF 'Patente: KXPL47 · Vehículo: SUV mediano · Año: 2017 · Motor: 2.0 bencina · VIN: 1HGCM82633A004352' && pasa "el panel muestra los datos del vehículo (Etiqueta: valor)" || falla "el panel sin los datos del vehículo"
printf '%s' "$PANEL" | grep -qF 'Patente: BBCD12' && pasa "el panel muestra el vehículo de los pedidos de ejemplo (seed)" || falla "pedidos de ejemplo sin datos del vehículo"
curl -s -b "$J1" "$BASE${TIENDA}?r=cuenta" | grep -qF 'Patente: KXPL47' && pasa "Mi cuenta muestra el vehículo de cada pedido" || falla "Mi cuenta sin los datos del vehículo"
RESUMEN=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel")
printf '%s' "$RESUMEN" | grep -qF 'Ventas de hoy' && pasa "panel con ventas del día" || falla "panel sin ventas del día"
printf '%s' "$RESUMEN" | grep -qF 'Stock bajo: Zapatas de freno traseras (3)' && pasa "panel avisa stock bajo" || falla "panel sin aviso de stock bajo"
printf '%s' "$RESUMEN" | grep -qF '1 taller(es) esperan aprobación' && pasa "panel avisa talleres por aprobar" || falla "panel sin aviso de talleres"
printf '%s' "$RESUMEN" | grep -qF '>Talleres asociados (1)</a>' && pasa "pestaña «Talleres asociados» con el nombre del rubro" || falla "pestaña de mayoristas sin el nombre del rubro"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "un cliente no entra al panel (403)" || falla "un cliente entra al panel"

# Estado: nuevo → preparando
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">Preparando' && pasa "el cliente ve su pedido en «Preparando»" || falla "estado del pedido no cambió ($R)"

# Taller asociado: precio taller y packs por caja; cuenta pendiente hasta la aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
TALLER=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$TALLER" | grep -qF 'Caja de 24 filtros de aceite rosca 3/4-16' && pasa "el taller ve los packs por caja" || falla "el taller no ve los packs"
printf '%s' "$TALLER" | grep -qF '<strong>$20.990</strong>' && printf '%s' "$TALLER" | grep -qF '<s aria-label="Precio normal">$24.990</s> <span class="t-etiqueta">Precio taller</span>' && pasa "el taller ve precio taller (\$20.990 en vez de \$24.990)" || falla "el taller sin precio taller"
printf '%s' "$TALLER" | grep -qF 'Precio taller activo' && pasa "aviso de precio taller activo" || falla "sin aviso de precio taller"
printf '%s' "$TALLER" | grep -qF 'cat=talleres#menu">Para talleres</a>' && pasa "el taller ve la categoría «Para talleres» en la columna" || falla "el taller no ve la categoría «Para talleres»"
J4="$TMP/nuevo-taller.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Taller Prueba" --data-urlencode "email=taller$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Mecanica de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Caja de 24 filtros de aceite' && falla "taller pendiente ve los packs" || pasa "taller pendiente aún no ve los packs"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Mecanica de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Caja de 24 filtros de aceite' && pasa "el dueño aprueba al taller y ya ve los packs" || falla "aprobación de taller no funcionó"

# Panel: crear un producto con una foto de la galería (sin código ni compatibilidad: queda como universal)
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Producto de Prueba" --data-urlencode "codigo=CD-7777" --data-urlencode "descripcion=Creado por el test" --data-urlencode "categoria_id=1" --data-urlencode "precio=12345" --data-urlencode "precio_mayorista=10000" --data-urlencode "stock=5" --data-urlencode "foto=1613214150384-14921ff659b2" --data-urlencode "visible=1")
case "$R" in 303*) pasa "el dueño crea un producto";; *) falla "crear producto: $R";; esac
curl -s "$BASE$TIENDA" | grep -qF 'Producto de Prueba</h3>' && pasa "el producto nuevo aparece en la tienda" || falla "el producto nuevo no aparece"
curl -s "$BASE${TIENDA}?patente=GZWX38" | grep -qF 'Producto de Prueba</h3>' && pasa "un producto sin compatibilidad se muestra para cualquier vehículo" || falla "producto sin compatibilidad oculto"
curl -s "$BASE${TIENDA}?q=cd7777" | grep -qF 'Producto de Prueba</h3>' && pasa "el código puesto en el panel se busca sin guion (cd7777)" || falla "el código del panel no se encuentra"
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Foto ajena" --data-urlencode "categoria_id=1" --data-urlencode "precio=1" --data-urlencode "stock=1" --data-urlencode "foto=https://ejemplo.invalid/foto.jpg")
case "$R" in 422*) pasa "foto fuera de la galería se rechaza";; *) falla "foto ajena aceptada: $R";; esac
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
tools/php.sh demos/repuestos/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
