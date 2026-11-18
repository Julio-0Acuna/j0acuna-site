#!/usr/bin/env bash
# Pruebas del rubro barbería (demos/barberia/): levanta `php -S` con el router y revisa con curl.
# Incluye el flujo de la Plataforma web con cookies: registro → login → carrito → reserva con barbero,
# día y hora → panel del dueño, más las reservas por WhatsApp de la Simple-page.
# Uso: bash tests/demos_barberia.sh
cd "$(dirname "$0")/.." || exit 1

PORT=${DEMOS_BARBERIA_PORT:-8104}
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/barberia"
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
tools/php.sh demos/barberia/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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
debe_contener    "$RUBRO/" 'Página web para barberías en Santiago'
debe_contener    "$RUBRO/" '"@type":"Service"'
debe_contener    "$RUBRO/" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO/" "href=\"$SIMPLE\""
debe_contener    "$RUBRO/" "href=\"$MULTI\""
debe_contener    "$RUBRO/" "href=\"$TIENDA\""
debe_contener    "$RUBRO/" 'Quiero esta'
debe_contener    "$RUBRO/" 'Perfil de Empresa en Google'
debe_contener    "$RUBRO/" 'href="/blog/aparecer-en-google-maps-perfil-de-empresa"'
no_debe_contener "$RUBRO/" 'noindex'
for img in simple multi plataforma; do debe_responder "$RUBRO/img/$img.webp" 200; done
[ -f demos/barberia/CREDITOS.md ] && pasa "CREDITOS.md del rubro existe" || falla "falta demos/barberia/CREDITOS.md"

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}servicios.php ${MULTI}la-casa.php ${MULTI}preguntas.php ${MULTI}contacto.php ${MULTI}reserva.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" "href=\"$RUBRO/\""
  debe_contener    "$p" '<meta name="robots" content="noindex, follow">'
  debe_contener    "$p" '56973537642'
  no_debe_contener "$p" 'LocalBusiness'
  no_debe_contener "$p" 'HairSalon'
  no_debe_contener "$p" 'AggregateRating'
  no_debe_contener "$p" 'application/ld+json'
  # Barbería: sin menú QR ni 3D, y sin servicios para niños
  no_debe_contener "$p" 'model-viewer'
  no_debe_contener "$p" 'demo-3d-btn'
  no_debe_contener "$p" 'data-demo-qr'
  no_debe_contener "$p" 'infantil'
  no_debe_contener "$p" 'niños'
done

# Sección Google en cada versión
for p in "$SIMPLE" "$MULTI" "${MULTI}contacto.php" "$TIENDA"; do
  debe_contener "$p" 'Encuéntranos en Google'
  debe_contener "$p" 'https://www.google.com/maps?q='
  debe_contener "$p" 'output=embed'
  debe_contener "$p" 'Reseña de ejemplo'
  debe_contener "$p" 'Déjanos tu reseña'
done

# --- Simple-page: pizarra de precios con duración, planes, galería y reserva por WhatsApp
debe_contener    "$SIMPLE" 'id="precios"'
debe_contener    "$SIMPLE" 'id="reservar"'
debe_contener    "$SIMPLE" 'id="trabajos"'
debe_contener    "$SIMPLE" '<span class="dl-precio__valor">$11.000</span>'
debe_contener    "$SIMPLE" '40 min · Bajo, medio o alto'
debe_contener    "$SIMPLE" 'aria-label="Reservar Degradado / fade"'
debe_contener    "$SIMPLE" 'Plan Vecino'
debe_contener    "$SIMPLE" 'name="barbero" value="nico"'
debe_contener    "$SIMPLE" '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20barber%C3%ADa%20%E2%80%93%20Simple-page%29'
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
debe_contener    "${SIMPLE}?servicio=fade" '<option value="fade" selected>'
DIAS=$(curl -s "$BASE$SIMPLE" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')
LUNES=$(for d in $DIAS; do date -d "$d" +%u; done | grep -c '^1$')
[ -n "$DIAS" ] && [ "$LUNES" = "0" ] && pasa "días de reserva sin lunes (cerrado)" || falla "días de reserva: '$DIAS', lunes $LUNES"
DIA2=$(printf '%s\n' $DIAS | sed -n 2p)
DOMINGO=$(for d in $DIAS; do [ "$(date -d "$d" +%u)" = "7" ] && echo "$d"; done | head -1)
R=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" --data-urlencode "servicio=fade" --data-urlencode "barbero=nico" --data-urlencode "dia=$DIA2" --data-urlencode "hora=11:00" --data-urlencode "nombre=Kevin" "$BASE$SIMPLE")
case "$R" in "303 https://wa.me/56973537642?text="*Servicio%3A%20Degradado%20%2F%20fade%20%2840%20min%20%C2%B7%20%2411.000%29*Barbero%3A%20Nico*Hora%3A%2011%3A00*Nombre%3A%20Kevin*Simple-page%29) pasa "la reserva arma el WhatsApp con servicio, duración, barbero, hora y nota";; *) falla "reserva Simple-page: $R";; esac
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "servicio=fade" --data-urlencode "barbero=nico" --data-urlencode "dia=2020-01-07" --data-urlencode "hora=11:00" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'Elige uno de los días de la lista.' "$TMP/simple.html" && pasa "día fuera de la lista: 422 con el error junto al campo" || falla "día inválido: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "servicio=../../etc" --data-urlencode "barbero=nico" --data-urlencode "dia=$DIA2" --data-urlencode "hora=11:00" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "servicio fuera de la lista no redirige" || falla "servicio inválido: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "servicio=fade" --data-urlencode "barbero=otro" --data-urlencode "dia=$DIA2" --data-urlencode "hora=11:00" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "barbero fuera de la lista no redirige" || falla "barbero inválido: $R"
if [ -n "$DOMINGO" ]; then
  R=$(curl -s -o "$TMP/simple2.html" -w "%{http_code}" --data-urlencode "servicio=clasico" --data-urlencode "barbero=lalo" --data-urlencode "dia=$DOMINGO" --data-urlencode "hora=19:00" "$BASE$SIMPLE")
  [ "$R" = "422" ] && grep -qF 'Ese día atendemos de 10:00 a 14:00' "$TMP/simple2.html" && pasa "hora fuera del horario del domingo se rechaza" || falla "hora de domingo: $R"
else
  falla "no hay domingo en la lista de días"
fi

# --- Multi-page: carta con buscador y opciones, reserva con barbero, día, bloque y bebida
debe_contener    "${MULTI}servicios.php" 'data-buscador'
debe_contener    "${MULTI}servicios.php" 'class="demo-agregar eg-agregar" data-id="degradado"'
debe_contener    "${MULTI}servicios.php" '<option value="Pelo largo">Pelo largo · $21.000</option>'
debe_contener    "${MULTI}servicios.php" 'Pomada mate fijación media 100 g'
debe_contener    "${MULTI}servicios.php?q=pomada" 'id="s-pomada" data-buscable'
curl -s "$BASE${MULTI}servicios.php?q=pomada" | grep -F 'id="s-degradado"' | grep -qF ' hidden>' && pasa "el buscador sin JS oculta lo que no coincide" || falla "el buscador sin JS no oculta"
debe_contener    "${MULTI}reserva.php" 'data-carrito-form'
for extra in Barbero Día Bloque 'Bebida de cortesía' 'Primera visita'; do
  debe_contener  "${MULTI}reserva.php" "data-carrito-extra=\"$extra\""
done
debe_contener    "${MULTI}reserva.php" '<option value="19:00 – 20:00">19:00 – 20:00</option>'
no_debe_contener "${MULTI}reserva.php" 'value="despacho"'
debe_contener    "${MULTI}la-casa.php" 'La carta de bebidas'
debe_contener    "${MULTI}la-casa.php" 'Todas son sin alcohol'
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '(Pedido de prueba desde la demo de barbería – Multi-page)'
debe_contener    "$MULTI" '"Corte de caballero","variantes":{"Pelo corto":18000,"Pelo largo":21000}'
grep -q 'Require all denied' demos/barberia/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/barberia/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -q 'config|seed|reset|_barberos' demos/barberia/plataforma/.htaccess && pasa 'plataforma/.htaccess niega config, seed, reset y _barberos' || falla 'plataforma/.htaccess no protege config/seed/reset/_barberos'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "${MULTI}servicios.php" "${MULTI}reserva.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# Fotos: todas de images.unsplash.com con w= y q=70
FOTOS=$(for p in "$SIMPLE" "$MULTI" "${MULTI}servicios.php" "${MULTI}la-casa.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE 'src="https://[^"]+"' | grep -v 'cdn.jsdelivr\|cdnjs\|google.com' | grep -vc 'images.unsplash.com/photo-[0-9a-f-]*?w=[0-9]*&amp;q=70&amp;auto=format')
[ "$FOTOS" = "0" ] && pasa "fotos enlazadas desde Unsplash con w y q=70" || falla "$FOTOS fotos con otra fuente o formato"

# --- Plataforma web: base protegida, agenda por barbero, catálogo, empresas
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
no_debe_contener "${TIENDA}config.php" 'dueno123'
no_debe_contener "${TIENDA}_barberos.php" 'function'
debe_contener    "$TIENDA" 'Corte clásico'
debe_contener    "$TIENDA" 'Pomada mate fijación media 100 g'
debe_contener    "$TIENDA" 'Próxima hora disponible'
debe_contener    "$TIENDA" 'Elige a tu barbero'
debe_contener    "$TIENDA" 'aria-label="Reservar con Benjamín en Ñuñoa"'
debe_contener    "$TIENDA" '¿Quieres Filo para tu equipo?'
no_debe_contener "$TIENDA" 'Jornada de barbería en tu oficina'
debe_contener    "${TIENDA}?q=barba" 'Perfilado y diseño de barba'
no_debe_contener "${TIENDA}?q=barba" 'Pomada mate'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'mayorista@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_contener    "${TIENDA}?r=registro" 'Empresas con convenio para sus colaboradores.'
debe_contener    "${TIENDA}?r=registro" '<strong>Empresa</strong>'
debe_responder   "${TIENDA}?r=no-existe" 404

# --- Flujo con cookies (textos ASCII o ya codificados: Git Bash en Windows no pasa bien UTF-8 a curl.exe)
J1="$TMP/cliente.txt"; J2="$TMP/dueno.txt"; J3="$TMP/empresa.txt"
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

# Elegir barbero en la portada (?con=) lo deja anotado en la sesión
curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?con=benjamin" | grep -qF 'Reservando con <strong>Benjamín</strong>' && pasa "elegir barbero en la portada lo muestra como elegido" || falla "?con=benjamin no queda elegido"

# Primer destacado: Corte clásico ($13.990)
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1")
case "$R" in 303*) pasa "agregar un servicio a la reserva ($R)";; *) falla "agregar al carrito: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF '<strong>Corte clásico</strong>' && pasa "el carrito tiene el servicio" || falla "carrito vacío"

# Checkout: día de la hora calculado en el servidor (sin domingos ni feriados) y selector de barbero
CHECK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
printf '%s' "$CHECK" | grep -qF 'Día de tu hora' && pasa "checkout pide el día de la hora" || falla "checkout sin selector de día"
printf '%s' "$CHECK" | grep -qF 'En la sucursal' && pasa "checkout con «En la sucursal»" || falla "checkout sin texto de retiro del rubro"
printf '%s' "$CHECK" | grep -qF 'Barbero a domicilio u oficina' && pasa "checkout con barbero a domicilio u oficina" || falla "checkout sin despacho del rubro"
printf '%s' "$CHECK" | grep -qF '<optgroup label="Sucursal Ñuñoa">' && pasa "checkout con barberos agrupados por sucursal" || falla "checkout sin selector de barbero"
printf '%s' "$CHECK" | grep -qF '<option value="benjamin" selected>' && pasa "el barbero elegido en la portada viene preseleccionado" || falla "barbero no preseleccionado"
FECHA=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | head -1 | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')
DOMINGOS=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}' | while read -r d; do date -d "$d" +%u; done | grep -c '^7$')
[ -n "$FECHA" ] && [ "$DOMINGOS" = "0" ] && pasa "días ofrecidos sin domingos (primero: $FECHA)" || falla "días ofrecidos: primero '$FECHA', domingos $DOMINGOS"
T=$(printf '%s' "$CHECK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=2020-01-06" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir" --data-urlencode "barbero=matias" --data-urlencode "nota=Hola")
case "$R" in 422*) pasa "un día fuera de la lista se rechaza (422)";; *) falla "día inválido aceptado: $R";; esac
grep -qF '<option value="matias" selected>' "$TMP/ultima.html" && grep -qF '>Hola</textarea>' "$TMP/ultima.html" && pasa "al volver con error se mantiene el barbero y la nota queda sin el prefijo" || falla "el formulario con error pierde el barbero o duplica el prefijo"
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=03:00" --data-urlencode "pago=al_recibir")
case "$R" in 422*) pasa "una hora fuera del horario se rechaza (422)";; *) falla "hora inválida aceptada: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Macul" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir" --data-urlencode "barbero=benjamin" --data-urlencode "nota=Degradado bajo")
case "$R" in 303*r=pedido*) pasa "checkout crea la reserva ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'FB-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página de la reserva muestra $CODIGO" || falla "página de la reserva sin código"
printf '%s' "$PAGINA" | grep -qF '¡Hora reservada!' && pasa "título propio del rubro (¡Hora reservada!)" || falla "sin el título del rubro"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "reserva ofrece enviar resumen por WhatsApp" || falla "reserva sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20barber%C3%ADa%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
printf '%s' "$PAGINA" | grep -qF 'a%20las%2010%3A00' && pasa "resumen con el día y la hora" || falla "resumen sin la hora"
printf '%s' "$PAGINA" | grep -qF 'Nota%3A%20Barbero%3A%20Benjam%C3%ADn%20%28%C3%91u%C3%B1oa%29.%20Degradado%20bajo' && pasa "resumen con el barbero validado en el servidor y la nota del cliente" || falla "resumen sin el barbero"
# $13.990 + barbero a domicilio en Macul $8.990 = $22.980
printf '%s' "$PAGINA" | grep -qF '$22.980' && pasa "total con barbero a domicilio en Macul (\$22.980)" || falla "total de la reserva inesperado"

# Un barbero que no está en la lista no se escribe en la nota
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=al_recibir" --data-urlencode "barbero=../../intruso" --data-urlencode "nota=Sin barbero")
OTRA=$(curl -s -b "$J1" "${R#* }")
printf '%s' "$OTRA" | grep -qF 'Nota%3A%20Sin%20barbero' && ! printf '%s' "$OTRA" | grep -qF 'Barbero%3A' && pasa "un barbero fuera de la lista se ignora" || falla "barbero inválido: $R"

# Pago de prueba: checkout → pantalla simulada → aprobar
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=prueba" --data-urlencode "barbero=cualquiera-maipu")
case "$R" in 303*r=pago*) pasa "pago de prueba lleva a la pantalla simulada";; *) falla "pago de prueba: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=pago" | grep -qF 'PAGO DE PRUEBA' && pasa "pantalla de pago marcada como prueba" || falla "pantalla de pago sin aviso"
T=$(csrf "$J1" "${TIENDA}?r=pago")
R=$(post "$J1" "${TIENDA}?r=pago" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar")
case "$R" in 303*r=pedido*) pasa "aprobar pago de prueba crea la reserva";; *) falla "aprobar pago: $R";; esac
PAGADO=$(curl -s -b "$J1" "${R#* }")
printf '%s' "$PAGADO" | grep -qF 'Tu hora quedó reservada' && pasa "mensaje de éxito propio del rubro" || falla "sin mensaje de éxito del rubro"
printf '%s' "$PAGADO" | grep -qF 'Barbero: el primero disponible (Maipú).' && pasa "«el primero disponible» de una sucursal queda en la nota" || falla "sin el primero disponible en la nota"

# Repetir desde el historial
T=$(csrf "$J1" "${TIENDA}?r=cuenta")
PEDIDO_ID=$(curl -s -b "$J1" "$BASE${TIENDA}?r=cuenta" | grep -oE 'name="pedido_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
R=$(post "$J1" "${TIENDA}?r=cuenta" --data-urlencode "csrf=$T" --data-urlencode "accion=repetir" --data-urlencode "pedido_id=$PEDIDO_ID")
case "$R" in 303*r=carrito*) pasa "repetir una reserva vuelve a llenar el carrito";; *) falla "repetir: $R";; esac

# El dueño ve la reserva en su panel
T=$(csrf "$J2" "${TIENDA}?r=ingresar")
R=$(post "$J2" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=dueno@demo.cl" --data-urlencode "clave=dueno123")
case "$R" in 303*r=panel*) pasa "dueño entra a su panel";; *) falla "login dueño: $R";; esac
PANEL=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=pedidos")
printf '%s' "$PANEL" | grep -qF "$CODIGO" && pasa "el dueño ve $CODIGO en el panel" || falla "el dueño no ve la reserva"
printf '%s' "$PANEL" | grep -qF 'Cliente Prueba' && pasa "el panel muestra al cliente" || falla "el panel no muestra al cliente"
printf '%s' "$PANEL" | grep -qF ' · para el ' && pasa "el panel muestra el día de cada reserva" || falla "el panel sin el día de la reserva"
curl -s -b "$J2" "$PEDIDO_URL" | grep -qF 'Barbero: Benjamín (Ñuñoa). Degradado bajo' && pasa "el dueño ve el barbero en el detalle de la reserva" || falla "el dueño no ve el barbero"
RESUMEN=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel")
printf '%s' "$RESUMEN" | grep -qF 'Ventas de hoy' && pasa "panel con ventas del día" || falla "panel sin ventas del día"
printf '%s' "$RESUMEN" | grep -qF 'Stock bajo: Tinte de barba (4)' && pasa "panel avisa cupos bajos" || falla "panel sin aviso de cupos bajos"
printf '%s' "$RESUMEN" | grep -qF '1 empresa(s) esperan aprobación' && pasa "panel avisa empresas por aprobar" || falla "panel sin aviso de empresas"
printf '%s' "$RESUMEN" | grep -qF '>Empresas (1)</a>' && pasa "pestaña «Empresas» con el nombre del rubro" || falla "pestaña de mayoristas sin el nombre del rubro"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "un cliente no entra al panel (403)" || falla "un cliente entra al panel"

# Estado: nuevo → preparando
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">Preparando' && pasa "el cliente ve su reserva en «Preparando»" || falla "estado de la reserva no cambió ($R)"

# Empresa: precios de convenio y servicios para empresas; pendiente hasta aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
EMPRESA=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$EMPRESA" | grep -qF 'Jornada de barbería en tu oficina' && pasa "la empresa ve los servicios para empresas" || falla "la empresa no ve los servicios para empresas"
printf '%s' "$EMPRESA" | grep -qF '<strong>$11.990</strong>' && printf '%s' "$EMPRESA" | grep -qF '<s aria-label="Precio normal">$13.990</s> <span class="t-etiqueta">Precio empresa</span>' && pasa "la empresa ve precio empresa (\$11.990 en vez de \$13.990)" || falla "la empresa sin precio empresa"
J4="$TMP/nueva-empresa.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Empresa Prueba" --data-urlencode "email=empresa$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Oficina de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Jornada de barbería en tu oficina' && falla "empresa pendiente ve los servicios para empresas" || pasa "empresa pendiente aún no ve los servicios para empresas"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Oficina de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Jornada de barbería en tu oficina' && pasa "el dueño aprueba la empresa y ya ve sus servicios" || falla "aprobación de empresa no funcionó"

# Panel: crear un servicio con una foto de la galería
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Servicio de Prueba" --data-urlencode "descripcion=Creado por el test" --data-urlencode "categoria_id=1" --data-urlencode "precio=12345" --data-urlencode "precio_mayorista=10000" --data-urlencode "stock=5" --data-urlencode "foto=1647140655214-e4a2d914971f" --data-urlencode "visible=1")
case "$R" in 303*) pasa "el dueño crea un servicio";; *) falla "crear servicio: $R";; esac
curl -s "$BASE$TIENDA" | grep -qF 'Servicio de Prueba</h3>' && pasa "el servicio nuevo aparece en la tienda" || falla "el servicio nuevo no aparece"
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Foto ajena" --data-urlencode "categoria_id=1" --data-urlencode "precio=1" --data-urlencode "stock=1" --data-urlencode "foto=https://ejemplo.invalid/foto.jpg")
case "$R" in 422*) pasa "foto fuera de la galería se rechaza";; *) falla "foto ajena aceptada: $R";; esac
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=$(printf 'x%.0s' $(seq 1 81))" --data-urlencode "categoria_id=1" --data-urlencode "precio=1" --data-urlencode "stock=1")
case "$R" in 422*) pasa "nombre de más de 80 caracteres se rechaza";; *) falla "texto largo aceptado: $R";; esac

# CSRF: POST sin token o con token falso → 403
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "accion=vaciar")
case "$R" in 403*) pasa "POST sin CSRF rechazado (403)";; *) falla "POST sin CSRF: $R";; esac
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=0000" --data-urlencode "accion=aprobar" --data-urlencode "id=1")
case "$R" in 403*) pasa "POST con CSRF falso rechazado (403)";; *) falla "POST con CSRF falso: $R";; esac
R=$(post "$TMP/vacio.txt" "${TIENDA}?r=checkout" --data-urlencode "barbero=benjamin" --data-urlencode "nota=x")
case "$R" in 403*|303*) pasa "checkout sin CSRF ni sesión no crea nada ($R)";; *) falla "checkout sin CSRF: $R";; esac
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
tools/php.sh demos/barberia/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
