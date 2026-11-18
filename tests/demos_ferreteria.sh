#!/usr/bin/env bash
# Pruebas del rubro ferretería (demos/ferreteria/): levanta `php -S` con el router y revisa con curl.
# Incluye el flujo de la Plataforma web con cookies: registro → login → carrito → pedido con despacho a obra
# (día y hora) → panel del dueño, más maestros y empresas (mayoristas) y CSRF.
# Uso: bash tests/demos_ferreteria.sh
cd "$(dirname "$0")/.." || exit 1

PORT=8102
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/ferreteria"
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
tools/php.sh demos/ferreteria/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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
debe_contener    "$RUBRO/" 'Página web para ferreterías en Santiago'
debe_contener    "$RUBRO/" '"@type":"Service"'
debe_contener    "$RUBRO/" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO/" "href=\"$SIMPLE\""
debe_contener    "$RUBRO/" "href=\"$MULTI\""
debe_contener    "$RUBRO/" "href=\"$TIENDA\""
debe_contener    "$RUBRO/" 'Quiero esta'
debe_contener    "$RUBRO/" 'Perfil de Empresa en Google'
no_debe_contener "$RUBRO/" 'noindex'
for img in simple multi plataforma; do debe_responder "$RUBRO/img/$img.webp" 200; done
[ -f demos/ferreteria/CREDITOS.md ] && pasa "CREDITOS.md del rubro existe" || falla "falta demos/ferreteria/CREDITOS.md"

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}catalogo.php ${MULTI}colores.php ${MULTI}asesoria.php ${MULTI}contacto.php ${MULTI}cotizacion.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" "href=\"$RUBRO/\""
  debe_contener    "$p" '<meta name="robots" content="noindex, follow">'
  debe_contener    "$p" '56973537642'
  no_debe_contener "$p" 'LocalBusiness'
  no_debe_contener "$p" 'HardwareStore'
  no_debe_contener "$p" 'AggregateRating'
  no_debe_contener "$p" 'application/ld+json'
  # Ferretería: no es rubro de comida, sin menú QR ni 3D
  no_debe_contener "$p" 'model-viewer'
  no_debe_contener "$p" 'demo-3d-btn'
  no_debe_contener "$p" 'data-demo-qr'
done

# Sección Google en cada versión
for p in "$SIMPLE" "$MULTI" "${MULTI}contacto.php" "$TIENDA"; do
  debe_contener "$p" 'Encuéntranos en Google'
  debe_contener "$p" 'https://www.google.com/maps?q='
  debe_contener "$p" 'output=embed'
  debe_contener "$p" 'Reseña de ejemplo'
  debe_contener "$p" 'Déjanos tu reseña'
done

# --- Simple-page: productos con precio, arriendo por día y "Cotiza tu lista" que arma el WhatsApp
debe_contener    "$SIMPLE" 'id="productos"'
debe_contener    "$SIMPLE" 'id="arriendo"'
debe_contener    "$SIMPLE" 'id="cotiza"'
debe_contener    "$SIMPLE" 'Taladro percutor 650 W'
debe_contener    "$SIMPLE" '$34.990'
debe_contener    "$SIMPLE" 'Rotomartillo 800 W'
debe_contener    "$SIMPLE" '<option value="Macul">Macul · $2.500</option>'
debe_contener    "$SIMPLE" '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20ferreter%C3%ADa%20%E2%80%93%20Simple-page%29'
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
R=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" --data-urlencode $'lista=2 sacos de cemento\n- 10 tarugos 8 mm\n\n1 llave de paso' --data-urlencode "entrega=despacho" --data-urlencode "comuna=Macul" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "nombre=Ana" "$BASE$SIMPLE")
case "$R" in "303 https://wa.me/56973537642?text="*%E2%80%A2%202%20sacos%20de%20cemento%0A%E2%80%A2%2010%20tarugos%208%20mm%0A%E2%80%A2%201%20llave%20de%20paso*Despacho%3A%20Calle%20de%20Prueba%20123%2C%20Macul%20%28%242.500%29*Simple-page%29) pasa "la lista arma el WhatsApp con un ítem por línea, despacho con costo y la nota";; *) falla "cotiza tu lista: $R";; esac
R=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" --data-urlencode "lista=1 candado" --data-urlencode "entrega=retiro" "$BASE$SIMPLE")
case "$R" in "303 https://wa.me/56973537642?text="*Entrega%3A%20retiro%20en%20el%20local*) pasa "lista con retiro en el local";; *) falla "lista con retiro: $R";; esac
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "lista=   " --data-urlencode "entrega=retiro" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'Escribe al menos un producto' "$TMP/simple.html" && pasa "lista vacía: 422 con el error junto al campo" || falla "lista vacía: $R"
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "lista=1 saco de cemento" --data-urlencode "entrega=despacho" --data-urlencode "comuna=../../etc" --data-urlencode "calle=Calle 1" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'Elige una comuna de la lista' "$TMP/simple.html" && pasa "comuna fuera de la lista no redirige" || falla "comuna inválida: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "lista=1 saco de cemento" --data-urlencode "entrega=despacho" --data-urlencode "comuna=Macul" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "despacho sin dirección no redirige" || falla "despacho sin dirección: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "lista=$(for i in $(seq 1 31); do printf 'item %s\n' "$i"; done)" --data-urlencode "entrega=retiro" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "lista de más de 30 líneas se rechaza" || falla "lista larga: $R"
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode 'lista=<script>alert(1)</script>' --data-urlencode "entrega=x" "$BASE$SIMPLE")
[ "$R" = "422" ] && ! grep -qF '<script>alert(1)</script>' "$TMP/simple.html" && pasa "lo enviado vuelve escapado en el formulario" || falla "escapado del formulario: $R"

# --- Multi-page: catálogo con buscador y formatos, carta de colores, calculadora y cotización
debe_contener    "${MULTI}catalogo.php" 'data-buscador'
debe_contener    "${MULTI}catalogo.php" 'class="demo-agregar pg-agregar" data-id="latex-mate"'
debe_contener    "${MULTI}catalogo.php" '<option value="Tineta 4 galones">Tineta 4 galones · $47.990</option>'
debe_contener    "${MULTI}catalogo.php?q=barniz" 'id="p-barniz" data-buscable'
curl -s "$BASE${MULTI}catalogo.php?q=barniz" | grep -F 'id="p-latex-mate"' | grep -qF ' hidden>' && pasa "el buscador sin JS oculta lo que no coincide" || falla "el buscador sin JS no oculta"
debe_contener    "${MULTI}colores.php" 'PG-301'
debe_contener    "${MULTI}colores.php" 'href="/demos/ferreteria/multi/cotizacion.php?color=PG-301"'
debe_contener    "${MULTI}colores.php?largo=12&alto=2,4&puertas=1&ventanas=1&manos=2" '1,5 <small>galones</small>'
debe_contener    "${MULTI}colores.php?largo=12&alto=2,4&puertas=1&ventanas=1&manos=2" 'llevar <strong>2 galones</strong>'
debe_contener    "${MULTI}colores.php?largo=100&alto=2,5&puertas=0&ventanas=0&manos=2" 'llevar <strong>4 tinetas de 4 galones</strong>'
debe_contener    "${MULTI}colores.php?largo=abc&alto=2,4&puertas=1&ventanas=1&manos=2" 'Revisa los valores marcados.'
debe_contener    "${MULTI}cotizacion.php" 'data-carrito-form'
for extra in Color Superficie 'Metros cuadrados' 'Visita de colorista'; do
  debe_contener  "${MULTI}cotizacion.php" "data-carrito-extra=\"$extra\""
done
debe_contener    "${MULTI}cotizacion.php?color=PG-301" '<option value="PG-301" selected>'
no_debe_contener "${MULTI}cotizacion.php?color=XX-999" ' selected>PG-'
debe_contener    "${MULTI}cotizacion.php" 'name="entrega" value="despacho"'
debe_contener    "${MULTI}cotizacion.php" '<option value="La Reina">La Reina · $2.990</option>'
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '(Pedido de prueba desde la demo de ferretería – Multi-page)'
debe_contener    "$MULTI" '"Látex interior mate","variantes":{"1 galón":13990,"Tineta 4 galones":47990}'
grep -q 'Require all denied' demos/ferreteria/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/ferreteria/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -q 'config|seed|reset' demos/ferreteria/plataforma/.htaccess && pasa 'plataforma/.htaccess niega config, seed y reset' || falla 'plataforma/.htaccess no protege config/seed/reset'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "${MULTI}catalogo.php" "${MULTI}cotizacion.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# Fotos: todas de images.unsplash.com con w= y q=70
FOTOS=$(for p in "$SIMPLE" "$MULTI" "${MULTI}catalogo.php" "${MULTI}colores.php" "${MULTI}asesoria.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE 'src="https://[^"]+"' | grep -v 'cdn.jsdelivr\|cdnjs\|google.com' | grep -vc 'images.unsplash.com/photo-[0-9a-f-]*?w=[0-9]*&amp;q=70&amp;auto=format')
[ "$FOTOS" = "0" ] && pasa "fotos enlazadas desde Unsplash con w y q=70" || falla "$FOTOS fotos con otra fuente o formato"

# --- Plataforma web: base protegida, catálogo con buscador por código, precios para maestros
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
no_debe_contener "${TIENDA}config.php" 'dueno123'
debe_contener    "$TIENDA" 'Cemento corriente saco 25 kg'
debe_contener    "$TIENDA" 'Cód. 10101'
debe_contener    "$TIENDA" 'Próximo despacho disponible'
debe_contener    "$TIENDA" '¿Eres maestro o constructora?'
debe_contener    "$TIENDA" 'Betonera 150 L · arriendo por día'
no_debe_contener "$TIENDA" 'Pallet de cemento (60 sacos de 25 kg)'
debe_contener    "${TIENDA}?q=10105" 'Fierro estriado 8 mm × 6 m'
no_debe_contener "${TIENDA}?q=10105" 'Cemento corriente saco 25 kg'
debe_contener    "${TIENDA}?q=yeso" 'Plancha yeso-cartón 10 mm 1,2 × 2,4 m'
debe_contener    "${TIENDA}?cat=arriendo" 'por día'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'mayorista@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_contener    "${TIENDA}?r=registro" 'Maestros, contratistas y constructoras con RUT.'
debe_contener    "${TIENDA}?r=registro" '<strong>Maestro o empresa</strong>'
debe_responder   "${TIENDA}?r=no-existe" 404

# --- Flujo con cookies (textos ASCII o ya codificados: Git Bash en Windows no pasa bien UTF-8 a curl.exe)
J1="$TMP/cliente.txt"; J2="$TMP/dueno.txt"; J3="$TMP/maestro.txt"
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

# Primer destacado: cemento ($4.890), 10 sacos
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=10")
case "$R" in 303*) pasa "agregar 10 sacos de cemento al pedido ($R)";; *) falla "agregar al carrito: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF '<strong>Cemento corriente saco 25 kg</strong>' && pasa "el carrito tiene el cemento" || falla "carrito vacío"

# Checkout: día de despacho calculado en el servidor (sin domingos ni feriados)
CHECK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
printf '%s' "$CHECK" | grep -qF 'Día de despacho o retiro' && pasa "checkout pide el día de despacho" || falla "checkout sin selector de día"
printf '%s' "$CHECK" | grep -qF 'Despacho a obra' && printf '%s' "$CHECK" | grep -qF 'Retiro en el patio de carga' && pasa "checkout con «Despacho a obra» y «Retiro en el patio de carga»" || falla "checkout sin textos del rubro"
printf '%s' "$CHECK" | grep -qF 'Desde las 08:00' && pasa "horas «Desde las …»" || falla "horas sin el texto del rubro"
FECHA=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | head -1 | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')
DOMINGOS=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}' | while read -r d; do date -d "$d" +%u; done | grep -c '^7$')
[ -n "$FECHA" ] && [ "$DOMINGOS" = "0" ] && pasa "días ofrecidos sin domingos (primero: $FECHA)" || falla "días ofrecidos: primero '$FECHA', domingos $DOMINGOS"
T=$(printf '%s' "$CHECK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=2020-01-06" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir")
case "$R" in 422*) pasa "un día fuera de la lista se rechaza (422)";; *) falla "día inválido aceptado: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=19:00" --data-urlencode "pago=al_recibir")
case "$R" in 422*) pasa "una hora fuera del horario se rechaza (422)";; *) falla "hora inválida aceptada: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Narnia" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=al_recibir")
case "$R" in 422*) pasa "despacho a una comuna sin cobertura se rechaza (422)";; *) falla "comuna inválida aceptada: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Renca" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir" --data-urlencode "nota=Obra en el patio, recibe Juan")
case "$R" in 303*r=pedido*) pasa "checkout crea el pedido ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'AN-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página del pedido muestra $CODIGO" || falla "página del pedido sin código"
printf '%s' "$PAGINA" | grep -qF '¡Pedido ingresado!' && pasa "título propio del rubro (¡Pedido ingresado!)" || falla "sin el título del rubro"
printf '%s' "$PAGINA" | grep -qF ', desde las 10:00' && pasa "el pedido muestra «…, desde las 10:00»" || falla "el pedido sin el día y la hora"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "pedido ofrece enviar resumen por WhatsApp" || falla "pedido sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20ferreter%C3%ADa%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
printf '%s' "$PAGINA" | grep -qF 'a%20las%2010%3A00' && pasa "resumen con el día y la hora" || falla "resumen sin la hora"
printf '%s' "$PAGINA" | grep -qF 'Nota%3A%20Obra%20en%20el%20patio' && pasa "resumen con la nota del cliente" || falla "resumen sin la nota del cliente"
# 10 × $4.890 = $48.900 + despacho a Renca $12.990 = $61.890
printf '%s' "$PAGINA" | grep -qF '$61.890' && pasa "total con despacho a obra en Renca (\$61.890)" || falla "total del pedido inesperado"

# Pago de prueba: checkout → pantalla simulada → aprobar
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=prueba")
case "$R" in 303*r=pago*) pasa "pago de prueba lleva a la pantalla simulada";; *) falla "pago de prueba: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=pago" | grep -qF 'PAGO DE PRUEBA' && pasa "pantalla de pago marcada como prueba" || falla "pantalla de pago sin aviso"
T=$(csrf "$J1" "${TIENDA}?r=pago")
R=$(post "$J1" "${TIENDA}?r=pago" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar")
case "$R" in 303*r=pedido*) pasa "aprobar pago de prueba crea el pedido";; *) falla "aprobar pago: $R";; esac
curl -s -b "$J1" "${R#* }" | grep -qF 'Tu pedido ya está en el patio de carga' && pasa "mensaje de éxito propio del rubro" || falla "sin mensaje de éxito del rubro"

# Repetir desde el historial
T=$(csrf "$J1" "${TIENDA}?r=cuenta")
PEDIDO_ID=$(curl -s -b "$J1" "$BASE${TIENDA}?r=cuenta" | grep -oE 'name="pedido_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
R=$(post "$J1" "${TIENDA}?r=cuenta" --data-urlencode "csrf=$T" --data-urlencode "accion=repetir" --data-urlencode "pedido_id=$PEDIDO_ID")
case "$R" in 303*r=carrito*) pasa "repetir un pedido vuelve a llenar el carrito";; *) falla "repetir: $R";; esac

# El dueño ve el pedido en su panel
T=$(csrf "$J2" "${TIENDA}?r=ingresar")
R=$(post "$J2" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=dueno@demo.cl" --data-urlencode "clave=dueno123")
case "$R" in 303*r=panel*) pasa "dueño entra a su panel";; *) falla "login dueño: $R";; esac
PANEL=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=pedidos")
printf '%s' "$PANEL" | grep -qF "$CODIGO" && pasa "el dueño ve $CODIGO en el panel" || falla "el dueño no ve el pedido"
printf '%s' "$PANEL" | grep -qF 'Cliente Prueba' && pasa "el panel muestra al cliente" || falla "el panel no muestra al cliente"
printf '%s' "$PANEL" | grep -qF ' · para el ' && pasa "el panel muestra el día de cada despacho" || falla "el panel sin el día del despacho"
RESUMEN=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel")
printf '%s' "$RESUMEN" | grep -qF 'Ventas de hoy' && pasa "panel con ventas del día" || falla "panel sin ventas del día"
printf '%s' "$RESUMEN" | grep -qF 'Stock bajo: Fieltro asfáltico 15 lb rollo 40 m² (3)' && pasa "panel avisa stock bajo" || falla "panel sin aviso de stock bajo"
printf '%s' "$RESUMEN" | grep -qF '1 cuenta(s) de maestro o empresa esperan aprobación' && pasa "panel avisa cuentas de maestro por aprobar" || falla "panel sin aviso de maestros"
printf '%s' "$RESUMEN" | grep -qF '>Maestros y empresas (1)</a>' && pasa "pestaña «Maestros y empresas» con el nombre del rubro" || falla "pestaña de mayoristas sin el nombre del rubro"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "un cliente no entra al panel (403)" || falla "un cliente entra al panel"

# Estado: nuevo → preparando
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">Preparando' && pasa "el cliente ve su pedido en «Preparando»" || falla "estado del pedido no cambió ($R)"

# Maestro o empresa: precio maestro y packs por pallet; cuenta pendiente hasta la aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
MAESTRO=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$MAESTRO" | grep -qF 'Pallet de cemento (60 sacos de 25 kg)' && pasa "el maestro ve los packs por pallet" || falla "el maestro no ve los packs"
printf '%s' "$MAESTRO" | grep -qF '<strong>$4.290</strong>' && printf '%s' "$MAESTRO" | grep -qF '<s aria-label="Precio normal">$4.890</s> <span class="t-etiqueta">Precio maestro</span>' && pasa "el maestro ve precio maestro (\$4.290 en vez de \$4.890)" || falla "el maestro sin precio maestro"
printf '%s' "$MAESTRO" | grep -qF 'Precio maestro activo' && pasa "aviso de precio maestro activo" || falla "sin aviso de precio maestro"
J4="$TMP/nuevo-maestro.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Maestro Prueba" --data-urlencode "email=maestro$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Construcciones de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Pallet de cemento (60 sacos de 25 kg)' && falla "maestro pendiente ve los packs" || pasa "maestro pendiente aún no ve los packs"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Construcciones de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Pallet de cemento (60 sacos de 25 kg)' && pasa "el dueño aprueba al maestro y ya ve los packs" || falla "aprobación de maestro no funcionó"

# Panel: crear un producto con una foto de la galería
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Producto de Prueba" --data-urlencode "descripcion=Cod. 99999 creado por el test" --data-urlencode "categoria_id=1" --data-urlencode "precio=12345" --data-urlencode "precio_mayorista=10000" --data-urlencode "stock=5" --data-urlencode "foto=1710141530542-f792450e736e" --data-urlencode "visible=1")
case "$R" in 303*) pasa "el dueño crea un producto";; *) falla "crear producto: $R";; esac
curl -s "$BASE$TIENDA" | grep -qF 'Producto de Prueba</h3>' && pasa "el producto nuevo aparece en la tienda" || falla "el producto nuevo no aparece"
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
tools/php.sh demos/ferreteria/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
