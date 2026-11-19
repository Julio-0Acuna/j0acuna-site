#!/usr/bin/env bash
# Pruebas del rubro peluquería (demos/peluqueria/): levanta `php -S` con el router y revisa con curl.
# Incluye el flujo de la Plataforma web con cookies: registro → login → carrito → reserva con día, hora y estilista → panel de la dueña.
# Uso: bash tests/demos_peluqueria.sh
cd "$(dirname "$0")/.." || exit 1

PORT=$(( 8103 + ${PUERTO_DESFASE:-0} ))
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/peluqueria"
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
tools/php.sh demos/peluqueria/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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
debe_contener    "$RUBRO/" 'Página web para peluquerías en Santiago'
debe_contener    "$RUBRO/" '"@type":"Service"'
debe_contener    "$RUBRO/" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO/" "href=\"$SIMPLE\""
debe_contener    "$RUBRO/" "href=\"$MULTI\""
debe_contener    "$RUBRO/" "href=\"$TIENDA\""
debe_contener    "$RUBRO/" 'Quiero esta'
debe_contener    "$RUBRO/" 'Perfil de Empresa en Google'
no_debe_contener "$RUBRO/" 'noindex'
for img in simple multi plataforma; do debe_responder "$RUBRO/img/$img.webp" 200; done
[ -f demos/peluqueria/CREDITOS.md ] && pasa "CREDITOS.md del rubro existe" || falla "falta demos/peluqueria/CREDITOS.md"

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}servicios.php ${MULTI}estudio.php ${MULTI}preguntas.php ${MULTI}contacto.php ${MULTI}reserva.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
# Páginas con productos en 3D (bloque «Modelos 3D» más abajo); en las demás, nada de model-viewer.
CON_3D=" ${MULTI}servicios.php $TIENDA "
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" "href=\"$RUBRO/\""
  debe_contener    "$p" '<meta name="robots" content="noindex, follow">'
  debe_contener    "$p" '56973537642'
  no_debe_contener "$p" 'LocalBusiness'
  no_debe_contener "$p" 'HairSalon'
  no_debe_contener "$p" 'BeautySalon'
  no_debe_contener "$p" 'AggregateRating'
  no_debe_contener "$p" 'application/ld+json'
  # Peluquería: sin menú QR; 3D solo en las páginas con productos (CON_3D)
  case "$CON_3D" in *" $p "*) ;; *)
    no_debe_contener "$p" 'model-viewer'
    no_debe_contener "$p" 'demo-3d-btn' ;;
  esac
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

# --- Simple-page: precios con duración, promos, equipo y reserva validada en el servidor
for id in servicios promos reserva equipo galeria productos; do debe_contener "$SIMPLE" "id=\"$id\""; done
debe_contener    "$SIMPLE" '<small>desde</small> $15.000'
debe_contener    "$SIMPLE" '1 h 15 min'
debe_contener    "$SIMPLE" 'Brushing a $12.000'
debe_contener    "$SIMPLE" 'name="con" value="karen"'
debe_contener    "$SIMPLE" 'Shampoo sin sulfatos 500 ml'
debe_contener    "$SIMPLE" '%28Reserva%20de%20prueba%20desde%20la%20demo%20de%20peluquer%C3%ADa%20%E2%80%93%20Simple-page%29'
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
debe_contener    "${SIMPLE}?servicio=trenzas" '<option value="trenzas" selected>'
DIA=$(curl -s "$BASE$SIMPLE" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | head -1 | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')
DOMINGOS=$(curl -s "$BASE$SIMPLE" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}' | while read -r d; do date -d "$d" +%u; done | grep -c '^7$')
[ -n "$DIA" ] && [ "$DOMINGOS" = "0" ] && pasa "días para reservar sin domingos (primero: $DIA)" || falla "días de la Simple-page: primero '$DIA', domingos $DOMINGOS"
R=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" --data-urlencode "servicio=brushing" --data-urlencode "con=yanet" --data-urlencode "dia=$DIA" --data-urlencode "bloque=tarde" --data-urlencode "nombre=Clienta Prueba" --data-urlencode "comentario=Pelo bajo los hombros" "$BASE$SIMPLE")
case "$R" in "303 https://wa.me/56973537642?text="*Servicio%3A%20Brushing%20dominicano%20%28desde%20%2415.000%20%C2%B7%201%20h%2015%20min%29*Con%3A%20Yanet*Bloque%3A%20Tarde*Nombre%3A%20Clienta%20Prueba*Comentario%3A%20Pelo%20bajo*%28Reserva%20de%20prueba*Simple-page%29) pasa "la ficha arma el WhatsApp con servicio, duración, estilista, día, bloque y nombre";; *) falla "ficha de reserva: $R";; esac
R=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" --data-urlencode "servicio=cejas" --data-urlencode "dia=$DIA" --data-urlencode "bloque=manana" --data-urlencode "nombre=Ana" "$BASE$SIMPLE")
case "$R" in 303*Con%3A%20quien%20est%C3%A9%20libre*) pasa "sin estilista elegida: «quien esté libre»";; *) falla "reserva sin estilista: $R";; esac
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "servicio=brushing" --data-urlencode "dia=$DIA" --data-urlencode "bloque=tarde" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'Escribe tu nombre para anotarte en la agenda.' "$TMP/simple.html" && pasa "sin nombre: 422 con el error junto al campo" || falla "sin nombre: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "servicio=brushing" --data-urlencode "dia=2020-01-06" --data-urlencode "bloque=tarde" --data-urlencode "nombre=Ana" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "día fuera de la lista no redirige" || falla "día inválido: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "servicio=../../etc" --data-urlencode "dia=$DIA" --data-urlencode "bloque=tarde" --data-urlencode "nombre=Ana" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "servicio fuera de la lista no redirige" || falla "servicio inválido: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "servicio=brushing" --data-urlencode "con=otra" --data-urlencode "dia=$DIA" --data-urlencode "bloque=tarde" --data-urlencode "nombre=Ana" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "estilista fuera del equipo no redirige" || falla "estilista inválida: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "servicio=brushing" --data-urlencode "dia=$DIA" --data-urlencode "bloque=noche" --data-urlencode "nombre=Ana" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "bloque fuera de la lista no redirige" || falla "bloque inválido: $R"

# --- Multi-page: carta con buscador y precio por largo, reserva con colorista, día, hora e historia del pelo
debe_contener    "$MULTI" 'class="mz-mechon"'
debe_contener    "${MULTI}servicios.php" 'data-buscador'
debe_contener    "${MULTI}servicios.php" 'class="demo-agregar mz-agregar" data-id="balayage"'
debe_contener    "${MULTI}servicios.php" '<option value="Pelo medio">Pelo medio · $119.990</option>'
debe_contener    "${MULTI}servicios.php" '3 h 30 min'
debe_contener    "${MULTI}servicios.php?q=canas" 'id="s-canas" data-buscable'
curl -s "$BASE${MULTI}servicios.php?q=canas" | grep -F 'id="s-balayage"' | grep -qF ' hidden>' && pasa "el buscador sin JS oculta lo que no coincide" || falla "el buscador sin JS no oculta"
debe_contener    "${MULTI}reserva.php" 'data-carrito-form'
for extra in Colorista Día 'Hora de inicio' 'Historia del pelo' 'Prueba de mechón' 'Duración estimada'; do
  debe_contener  "${MULTI}reserva.php" "data-carrito-extra=\"$extra\""
done
debe_contener    "${MULTI}reserva.php" 'name="entrega" value="despacho"'
debe_contener    "${MULTI}reserva.php" '<option value="Providencia">Providencia · $2.990</option>'
debe_contener    "${MULTI}reserva.php" 'id="mz-duraciones"'
debe_contener    "${MULTI}reserva.php" '"balayage":240'
debe_responder   "${MULTI}reserva.js" 200
DOMINGOS=$(curl -s "$BASE${MULTI}reserva.php" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}' | while read -r d; do date -d "$d" +%u; done | grep -cE '^(1|7)$')
[ "$DOMINGOS" = "0" ] && pasa "la Multi-page no ofrece lunes ni domingos (estudio cerrado)" || falla "la Multi-page ofrece $DOMINGOS días cerrados"
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '(Reserva de prueba desde la demo de peluquería – Multi-page)'
debe_contener    "$MULTI" '"saludo":"Hola, quiero reservar hora en {negocio}:"'
debe_contener    "$MULTI" '"retiro":"Lugar: en el estudio","despacho":"Productos: despacho a {direccion}, {comuna}"'
debe_contener    "$MULTI" '"Balayage a mano alzada","variantes":{"Pelo corto":89990,"Pelo medio":119990,"Pelo largo":149990}'
grep -q 'Require all denied' demos/peluqueria/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/peluqueria/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -q 'config|seed|reset' demos/peluqueria/plataforma/.htaccess && pasa 'plataforma/.htaccess niega config, seed y reset' || falla 'plataforma/.htaccess no protege config/seed/reset'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "${MULTI}servicios.php" "${MULTI}reserva.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# Fotos: todas de images.unsplash.com con w= y q=70
FOTOS=$(for p in "$SIMPLE" "$MULTI" "${MULTI}servicios.php" "${MULTI}estudio.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE 'src="https://[^"]+"' | grep -v 'cdn.jsdelivr\|cdnjs\|google.com' | grep -vc 'images.unsplash.com/photo-[0-9a-f-]*?w=[0-9]*&amp;q=70&amp;auto=format')
[ "$FOTOS" = "0" ] && pasa "fotos enlazadas desde Unsplash con w y q=70" || falla "$FOTOS fotos con otra fuente o formato"

# --- Plataforma web: base protegida, servicios y tienda, estilistas
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
no_debe_contener "${TIENDA}config.php" 'dueno123'
[ ! -e demos/peluqueria/plataforma/reserva.js ] && pasa "sin reserva.js: sede y estilista son campos del checkout del motor" || falla "sigue demos/peluqueria/plataforma/reserva.js"
debe_contener    "$TIENDA" 'Corte y brushing'
debe_contener    "$TIENDA" 'Aceite de argán 50 ml'
debe_contener    "$TIENDA" 'Primera hora libre'
debe_contener    "$TIENDA" 'Elige a tu estilista'
debe_contener    "$TIENDA" 'href="/demos/peluqueria/plataforma/?estilista=catalina&amp;sede=las-condes#menu"'
no_debe_contener "$TIENDA" 'data-on-con'
no_debe_contener "$TIENDA" 'reserva.js'
debe_contener    "$TIENDA" '¿Eres estilista, manicurista o tienes tu salón?'
no_debe_contener "$TIENDA" 'Oxidante en crema 20 vol. 1 L'
no_debe_contener "$TIENDA" 'id="on-estilistas"'
debe_contener    "${TIENDA}?q=balayage" 'Balayage</h3>'
no_debe_contener "${TIENDA}?q=balayage" 'Aceite de argán'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'mayorista@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_contener    "${TIENDA}?r=registro" 'Estilistas, manicuristas, lashistas y salones.'
debe_contener    "${TIENDA}?r=registro" '<strong>Profesional</strong>'
debe_responder   "${TIENDA}?r=no-existe" 404

# --- Flujo con cookies (textos ASCII o ya codificados: Git Bash en Windows no pasa bien UTF-8 a curl.exe)
J1="$TMP/cliente.txt"; J2="$TMP/duena.txt"; J3="$TMP/pro.txt"
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

# Primer destacado: Corte y brushing ($24.990)
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1")
case "$R" in 303*) pasa "agregar un servicio a la reserva ($R)";; *) falla "agregar al carrito: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF '<strong>Corte y brushing</strong>' && pasa "el carrito tiene el servicio" || falla "carrito vacío"

# "Reservar con Catalina" (?estilista=&sede=) deja ambas opciones en la sesión; una que no existe se ignora
curl -s -b "$J1" -c "$J1" -o /dev/null "$BASE${TIENDA}?estilista=catalina&sede=las-condes"
curl -s -b "$J1" -c "$J1" -o /dev/null "$BASE${TIENDA}?estilista=..%2Fintrusa"
# Checkout: día de la hora calculado en el servidor (sin domingos ni feriados), sede y estilista del motor
CHECK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
printf '%s' "$CHECK" | grep -qF 'Día de tu hora' && pasa "checkout pide el día de la hora" || falla "checkout sin selector de día"
printf '%s' "$CHECK" | grep -qF 'En el salón' && pasa "checkout con «En el salón»" || falla "checkout sin texto de retiro del rubro"
printf '%s' "$CHECK" | grep -qF '<h1>Reservar hora</h1>' && printf '%s' "$CHECK" | grep -qF 'Confirmar reserva</button>' && pasa "checkout con textos de reserva (Reservar hora, Confirmar reserva)" || falla "checkout sin los textos de reserva"
printf '%s' "$CHECK" | grep -qF 'Pagar en el salón' && pasa "checkout con «Pagar en el salón»" || falla "checkout sin el pago del rubro"
printf '%s' "$CHECK" | grep -qF '<p class="t-subtitulo">¿Con quién te atiendes?</p>' && pasa "campos de sede y estilista con su título" || falla "checkout sin el título de sede y estilista"
printf '%s' "$CHECK" | grep -qF '<div class="t-campo t-campo--extra" data-solo-entrega="retiro">' && printf '%s' "$CHECK" | grep -qF '<select id="extra-sede" name="extra[sede]" data-requerido>' && pasa "la sede se pide solo al atenderse en el salón" || falla "checkout sin el campo Sede solo para retiro"
printf '%s' "$CHECK" | grep -qF '<option value="las-condes" selected>Las Condes</option>' && printf '%s' "$CHECK" | grep -qF '<option value="catalina" selected>Catalina · color y balayage (Las Condes)</option>' && pasa "«Reservar con Catalina» deja preseleccionadas la sede y la estilista" || falla "sede o estilista no preseleccionadas"
printf '%s' "$CHECK" | grep -qF 'id="on-estilistas"' && falla "el layout aún inserta su selector" || pasa "sin el selector propio del layout"
curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout&estilista=valentina" | grep -qF '<option value="valentina" selected>' && pasa "precarga ?estilista= en la URL del checkout" || falla "?estilista= en el checkout no preselecciona"
curl -s -b "$J1" -c "$J1" -o /dev/null "$BASE${TIENDA}?estilista=catalina"
FECHA=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | head -1 | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')
DOMINGOS=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}' | while read -r d; do date -d "$d" +%u; done | grep -c '^7$')
[ -n "$FECHA" ] && [ "$DOMINGOS" = "0" ] && pasa "días ofrecidos sin domingos (primero: $FECHA)" || falla "días ofrecidos: primero '$FECHA', domingos $DOMINGOS"
printf '%s' "$CHECK" | grep -qF 'option value="2026-12-25"' && falla "se ofrece Navidad" || pasa "no se ofrece el 25 de diciembre"
T=$(printf '%s' "$CHECK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=2020-01-06" --data-urlencode "cuando=11:00" --data-urlencode "pago=al_recibir")
case "$R" in 422*) pasa "un día fuera de la lista se rechaza (422)";; *) falla "día inválido aceptado: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=21:00" --data-urlencode "pago=al_recibir" --data-urlencode "extra[sede]=nunoa" --data-urlencode "extra[estilista]=cualquiera")
case "$R" in 422*) pasa "una hora fuera del horario se rechaza (422)";; *) falla "hora inválida aceptada: $R";; esac
# Campo requerido ausente (sede al atenderse en el salón) y estilista que no existe: 422 con su error
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=al_recibir" --data-urlencode "extra[estilista]=cualquiera")
case "$R" in 422*) grep -qF '<p class="t-error" id="err-extra_sede">Elige en qué sede te atendemos.</p>' "$TMP/ultima.html" && pasa "en el salón sin sede: 422 con su error" || falla "sin sede: 422 sin el error";; *) falla "reserva sin sede aceptada: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=al_recibir" --data-urlencode "extra[sede]=nunoa" --data-urlencode "extra[estilista]=karen")
case "$R" in 422*) grep -qF '<p class="t-error" id="err-extra_estilista">Elige una opción en «Estilista».</p>' "$TMP/ultima.html" && pasa "una estilista que no existe: 422 con su error" || falla "estilista inexistente: 422 sin el error";; *) falla "estilista inexistente aceptada: $R";; esac
# La reserva se completa con los campos que trae el HTML del checkout (name leídos del formulario)
EXTRAS=$(printf '%s' "$CHECK" | grep -oE 'name="extra\[[a-z_]+\]"' | sed -E 's/name="([^"]+)"/\1/' | tr '\n' ' ')
FALTAN=$(for k in csrf entrega direccion_id calle comuna fecha_entrega cuando pago nota; do printf '%s' "$CHECK" | grep -qF "name=\"$k\"" || echo "$k"; done)
[ "$EXTRAS" = "extra[sede] extra[estilista] " ] && [ -z "$FALTAN" ] && pasa "el formulario del checkout trae todos los campos que se envían ($EXTRAS)" || falla "al formulario del checkout le faltan: $FALTAN (extras: '$EXTRAS')"
EXTRA_ESTILISTA=$(printf '%s' "$EXTRAS" | grep -oE 'extra\[estilista\]')
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Macul" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=11:00" --data-urlencode "pago=al_recibir" --data-urlencode "$EXTRA_ESTILISTA=catalina" --data-urlencode "nota=Peinado para un matrimonio")
case "$R" in 303*r=pedido*) pasa "checkout crea la reserva ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'ON-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página de la reserva muestra $CODIGO" || falla "página de la reserva sin código"
printf '%s' "$PAGINA" | grep -qF '¡Hora reservada!' && pasa "título propio del rubro (¡Hora reservada!)" || falla "sin el título del rubro"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "reserva ofrece enviar resumen por WhatsApp" || falla "reserva sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20peluquer%C3%ADa%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
printf '%s' "$PAGINA" | grep -qF 'a%20las%2011%3A00' && pasa "resumen con el día y la hora" || falla "resumen sin la hora"
printf '%s' "$PAGINA" | grep -qF 'Estilista%3A%20Catalina%20%C2%B7%20color%20y%20balayage%20%28Las%20Condes%29' && printf '%s' "$PAGINA" | grep -qF 'Nota%3A%20Peinado%20para%20un%20matrimonio' && pasa "WhatsApp con «Estilista: Catalina…» y la nota" || falla "resumen sin la estilista"
printf '%s' "$PAGINA" | grep -qF 'Sede%3A' && falla "a domicilio no debería pedir la sede" || pasa "a domicilio no se guarda la sede (solo en el salón)"
printf '%s' "$PAGINA" | grep -qF '<dt>Estilista</dt><dd>Catalina · color y balayage (Las Condes)</dd>' && pasa "la reserva muestra la estilista" || falla "la reserva no muestra la estilista"
printf '%s' "$PAGINA" | grep -qF 'Reserva ON-' && pasa "la página dice «Reserva ON-…»" || falla "la página sin «Reserva ON-…»"
# $24.990 + traslado a domicilio en Macul $6.990 = $31.980
printf '%s' "$PAGINA" | grep -qF '$31.980' && pasa "total con servicio a domicilio en Macul (\$31.980)" || falla "total de la reserva inesperado"

# Pago de prueba: checkout → pantalla simulada → aprobar
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=prueba" --data-urlencode "extra[sede]=nunoa" --data-urlencode "extra[estilista]=cualquiera")
case "$R" in 303*r=pago*) pasa "pago de prueba lleva a la pantalla simulada";; *) falla "pago de prueba: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=pago" | grep -qF 'PAGO DE PRUEBA' && pasa "pantalla de pago marcada como prueba" || falla "pantalla de pago sin aviso"
T=$(csrf "$J1" "${TIENDA}?r=pago")
R=$(post "$J1" "${TIENDA}?r=pago" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar")
case "$R" in 303*r=pedido*) pasa "aprobar pago de prueba crea la reserva";; *) falla "aprobar pago: $R";; esac
PAGADA=$(curl -s -b "$J1" "${R#* }")
printf '%s' "$PAGADA" | grep -qF 'Tu hora quedó reservada' && pasa "mensaje de éxito propio del rubro" || falla "sin mensaje de éxito del rubro"
printf '%s' "$PAGADA" | grep -qF '<dt>Sede</dt><dd>Ñuñoa</dd>' && printf '%s' "$PAGADA" | grep -qF '<dt>Estilista</dt><dd>Quien tenga la hora libre</dd>' && pasa "el pago de prueba conserva sede y estilista" || falla "el pago de prueba perdió sede o estilista"

# Repetir desde el historial
T=$(csrf "$J1" "${TIENDA}?r=cuenta")
PEDIDO_ID=$(curl -s -b "$J1" "$BASE${TIENDA}?r=cuenta" | grep -oE 'name="pedido_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
R=$(post "$J1" "${TIENDA}?r=cuenta" --data-urlencode "csrf=$T" --data-urlencode "accion=repetir" --data-urlencode "pedido_id=$PEDIDO_ID")
case "$R" in 303*r=carrito*) pasa "repetir una reserva vuelve a llenar el carrito";; *) falla "repetir: $R";; esac

# La dueña ve la reserva en su panel
T=$(csrf "$J2" "${TIENDA}?r=ingresar")
R=$(post "$J2" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=dueno@demo.cl" --data-urlencode "clave=dueno123")
case "$R" in 303*r=panel*) pasa "la dueña entra a su panel";; *) falla "login dueña: $R";; esac
PANEL=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=pedidos")
printf '%s' "$PANEL" | grep -qF "$CODIGO" && pasa "la dueña ve $CODIGO en el panel" || falla "la dueña no ve la reserva"
printf '%s' "$PANEL" | grep -qF 'Cliente Prueba' && pasa "el panel muestra a la clienta" || falla "el panel no muestra a la clienta"
printf '%s' "$PANEL" | grep -qF ' · para el ' && pasa "el panel muestra el día de cada reserva" || falla "el panel sin el día de la reserva"
printf '%s' "$PANEL" | grep -qF '<p class="t-pequeno t-pedido-panel__extras">Estilista: Catalina · color y balayage (Las Condes)</p>' && pasa "la lista del panel muestra «Estilista: Catalina…»" || falla "la lista del panel sin la estilista"
printf '%s' "$PANEL" | grep -qF 'Sede: Las Condes · Estilista: Tomás · cortes y lavado spa (Las Condes)' && pasa "las reservas de ejemplo traen sede y estilista como campos" || falla "las reservas de ejemplo sin sede ni estilista"
printf '%s' "$PANEL" | grep -qF '>Confirmar hora</button>' && pasa "el panel avanza con «Confirmar hora»" || falla "el panel sin el texto de avance de reserva"
curl -s -b "$J2" "$PEDIDO_URL" | grep -qF '<dt>Estilista</dt><dd>Catalina · color y balayage (Las Condes)</dd>' && pasa "la dueña ve la estilista elegida en el detalle de la reserva" || falla "el detalle de la reserva sin la estilista"
RESUMEN=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel")
printf '%s' "$RESUMEN" | grep -qF 'Ventas de hoy' && pasa "panel con ventas del día" || falla "panel sin ventas del día"
printf '%s' "$RESUMEN" | grep -qF 'Stock bajo: Extensiones de pestañas (3)' && pasa "panel avisa cupos bajos" || falla "panel sin aviso de cupos bajos"
printf '%s' "$RESUMEN" | grep -qF '1 cuenta(s) profesional(es) esperan aprobación' && pasa "panel avisa cuentas profesionales por aprobar" || falla "panel sin aviso de profesionales"
printf '%s' "$RESUMEN" | grep -qF '>Profesionales (1)</a>' && pasa "pestaña «Profesionales» con el nombre del rubro" || falla "pestaña de mayoristas sin el nombre del rubro"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "una clienta no entra al panel (403)" || falla "una clienta entra al panel"

# Estado: nuevo → preparando
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">Confirmada' && pasa "la clienta ve su reserva «Confirmada»" || falla "estado de la reserva no cambió ($R)"
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF '<li class="is-hecho">Hora reservada</li>' && pasa "primer paso «Hora reservada»" || falla "sin el paso «Hora reservada»"

# Profesional: precios de distribuidor e insumos; pendiente hasta aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
PRO=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$PRO" | grep -qF 'Oxidante en crema 20 vol. 1 L' && pasa "la profesional ve los insumos profesionales" || falla "la profesional no ve los insumos"
printf '%s' "$PRO" | grep -qF '<strong>$9.490</strong>' && printf '%s' "$PRO" | grep -qF '<s aria-label="Precio normal">$12.990</s> <span class="t-etiqueta">Precio profesional</span>' && pasa "la profesional ve precio profesional (\$9.490 en vez de \$12.990)" || falla "la profesional sin precio profesional"
J4="$TMP/nueva-pro.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Pro Prueba" --data-urlencode "email=pro$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Salon de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Oxidante en crema 20 vol. 1 L' && falla "cuenta profesional pendiente ve los insumos" || pasa "cuenta profesional pendiente aún no ve los insumos"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Salon de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Oxidante en crema 20 vol. 1 L' && pasa "la dueña aprueba la cuenta profesional y ya ve los insumos" || falla "aprobación de cuenta profesional no funcionó"

# Panel: crear un servicio con una foto de la galería
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Servicio de Prueba" --data-urlencode "descripcion=Creado por el test" --data-urlencode "categoria_id=1" --data-urlencode "precio=12345" --data-urlencode "stock=5" --data-urlencode "foto=1700760934268-8aa0ef52ce0a" --data-urlencode "visible=1")
case "$R" in 303*) pasa "la dueña crea un servicio";; *) falla "crear servicio: $R";; esac
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
tools/php.sh demos/peluqueria/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

# --- Modelos 3D (demos/_comun/3d/, ver tests/demos_3d.sh): botón «Ver en 3D» con el estilo del rubro,
# model-viewer 4.4.0 con SRI, menu3d.js y data-3d-objeto="producto" (el visor no habla de «plato»). La Simple-page, sin 3D.
debe_contener    "${MULTI}servicios.php" 'data-3d-modelo="/demos/_comun/3d/shampoo.glb"'
debe_contener    "${MULTI}servicios.php" 'class="demo-3d-btn mz-btn-3d"'
debe_contener    "${MULTI}servicios.php" 'data-3d-modelo="/demos/_comun/3d/secador.glb"'
debe_contener    "${MULTI}servicios.php" 'data-3d-modelo="/demos/_comun/3d/tijeras.glb"'
debe_contener    "$TIENDA" 'data-3d-modelo="/demos/_comun/3d/shampoo.glb"'
debe_contener    "$TIENDA" 'class="demo-3d-btn t-btn-3d"'
debe_contener    "$TIENDA" 'data-3d-modelo="/demos/_comun/3d/peineta.glb"'
debe_contener    "${MULTI}servicios.php" '<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js" integrity="sha256-GLLnEQNapHBe4oFTClXcIzj/Be7T1wrhbVQQUoKnXtU=" crossorigin="anonymous"></script>'
debe_contener    "${MULTI}servicios.php" '/demos/_comun/js/menu3d.js?v='
debe_contener    "${MULTI}servicios.php" 'data-3d-objeto="producto"'
debe_contener    "$TIENDA" '<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js" integrity="sha256-GLLnEQNapHBe4oFTClXcIzj/Be7T1wrhbVQQUoKnXtU=" crossorigin="anonymous"></script>'
debe_contener    "$TIENDA" '/demos/_comun/js/menu3d.js?v='
debe_contener    "$TIENDA" 'data-3d-objeto="producto"'
for m in $(grep -rhoE "'modelo3?d?' => '[a-z0-9-]+'" "demos/peluqueria/multi/_datos.php" "demos/peluqueria/plataforma/seed.php" | sed -E "s/.*=> '([a-z0-9-]+)'/\1/" | sort -u); do
  [ -f "demos/_comun/3d/$m.glb" ] && pasa "$m.glb existe" || falla "falta demos/_comun/3d/$m.glb"
done
no_debe_contener "$SIMPLE" 'model-viewer'
no_debe_contener "$SIMPLE" 'demo-3d-btn'

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
