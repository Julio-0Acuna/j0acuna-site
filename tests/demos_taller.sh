#!/usr/bin/env bash
# Pruebas del rubro taller mecánico (demos/taller/): levanta `php -S` con el router y revisa con curl.
# Incluye el flujo de la Plataforma web con cookies: registro → login → carrito → reserva con día y hora → panel del dueño.
# Uso: bash tests/demos_taller.sh
cd "$(dirname "$0")/.." || exit 1

PORT=$(( 8114 + ${PUERTO_DESFASE:-0} ))
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/taller"
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
tools/php.sh demos/taller/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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
debe_contener    "$RUBRO/" 'Página web para talleres mecánicos en Santiago'
debe_contener    "$RUBRO/" '"@type":"Service"'
debe_contener    "$RUBRO/" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO/" "href=\"$SIMPLE\""
debe_contener    "$RUBRO/" "href=\"$MULTI\""
debe_contener    "$RUBRO/" "href=\"$TIENDA\""
debe_contener    "$RUBRO/" 'Quiero esta'
debe_contener    "$RUBRO/" 'Perfil de Empresa en Google'
debe_contener    "$RUBRO/" 'href="/blog/pagina-web-para-talleres-mecanicos"'
no_debe_contener "$RUBRO/" 'noindex'
for img in simple multi plataforma; do debe_responder "$RUBRO/img/$img.webp" 200; done
[ -f demos/taller/CREDITOS.md ] && pasa "CREDITOS.md del rubro existe" || falla "falta demos/taller/CREDITOS.md"

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}servicios.php ${MULTI}taller.php ${MULTI}preguntas.php ${MULTI}contacto.php ${MULTI}reserva.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
# Páginas con productos en 3D (bloque «Modelos 3D» más abajo); en las demás, nada de model-viewer.
CON_3D=" ${MULTI}servicios.php $TIENDA "
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" "href=\"$RUBRO/\""
  debe_contener    "$p" '<meta name="robots" content="noindex, follow">'
  debe_contener    "$p" '56973537642'
  no_debe_contener "$p" 'LocalBusiness'
  no_debe_contener "$p" 'AutoRepair'
  no_debe_contener "$p" 'AggregateRating'
  no_debe_contener "$p" 'application/ld+json'
  # Taller: sin menú QR; 3D solo en las páginas con productos (CON_3D)
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

# --- Simple-page: servicios con precio desde, pauta por kilometraje y ficha de presupuesto
debe_contener    "$SIMPLE" 'id="servicios"'
debe_contener    "$SIMPLE" 'id="kilometraje"'
debe_contener    "$SIMPLE" 'id="presupuesto"'
debe_contener    "$SIMPLE" '<small>desde</small> $219.990'
debe_contener    "$SIMPLE" 'aria-label="100.000 kilómetros"'
debe_contener    "$SIMPLE" 'name="patente"'
debe_contener    "$SIMPLE" '%28Cotizaci%C3%B3n%20de%20prueba%20desde%20la%20demo%20de%20taller%20mec%C3%A1nico%20%E2%80%93%20Simple-page%29'
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
debe_contener    "${SIMPLE}?servicio=embrague" '<option value="embrague" selected>'
R=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" --data-urlencode "patente=abcd-12" --data-urlencode "modelo=Sedan mediano" --data-urlencode "anio=2015" --data-urlencode "km=84.000" --data-urlencode "servicio=frenos" --data-urlencode "problema=Chirria al frenar" "$BASE$SIMPLE")
case "$R" in "303 https://wa.me/56973537642?text="*Servicio%3A%20Frenos%20%28desde%20%2439.990%29*Patente%3A%20ABCD12*Kilometraje%3A%2084.000%20km*Simple-page%29) pasa "la ficha arma el WhatsApp con servicio, patente normalizada, kilometraje y nota";; *) falla "ficha de presupuesto: $R";; esac
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "patente=12" --data-urlencode "modelo=X" --data-urlencode "servicio=frenos" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'Escribe la patente con sus 6 caracteres' "$TMP/simple.html" && pasa "patente inválida: 422 con el error junto al campo" || falla "patente inválida: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "patente=ABCD12" --data-urlencode "modelo=Sedan" --data-urlencode "servicio=../../etc" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "servicio fuera de la lista no redirige" || falla "servicio inválido: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "patente=ABCD12" --data-urlencode "modelo=Sedan" --data-urlencode "servicio=otro" --data-urlencode "anio=1850" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "año fuera de rango no redirige" || falla "año inválido: $R"

# --- Multi-page: servicios con buscador y tipo de vehículo, reserva con ficha del vehículo
debe_contener    "${MULTI}servicios.php" 'data-buscador'
debe_contener    "${MULTI}servicios.php" 'class="demo-agregar vg-agregar" data-id="frenos"'
debe_contener    "${MULTI}servicios.php" '<option value="SUV">SUV · $94.990</option>'
debe_contener    "${MULTI}servicios.php?q=embrague" 'id="s-embrague" data-buscable'
curl -s "$BASE${MULTI}servicios.php?q=embrague" | grep -F 'id="s-frenos"' | grep -qF ' hidden>' && pasa "el buscador sin JS oculta lo que no coincide" || falla "el buscador sin JS no oculta"
debe_contener    "${MULTI}reserva.php" 'data-carrito-form'
for extra in Patente Vehículo Año Kilometraje Día Bloque 'Espera en la sala'; do
  debe_contener  "${MULTI}reserva.php" "data-carrito-extra=\"$extra\""
done
debe_contener    "${MULTI}reserva.php" 'name="entrega" value="despacho"'
debe_contener    "${MULTI}reserva.php" '<option value="Vitacura">Vitacura · $4.990</option>'
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '(Reserva de prueba desde la demo de taller mecánico – Multi-page)'
# Carrito común con textos de reserva (el mensaje lo arma carrito.js con estos textos)
debe_contener    "$MULTI" '"saludo":"Hola, quiero reservar hora en {negocio}:"'
debe_contener    "${MULTI}reserva.php" '"retiro":"Entrega: llevo el auto al taller"'
debe_contener    "${MULTI}reserva.php" '"despacho":"Entrega: retiro del auto en {direccion}, {comuna}"'
debe_contener    "${MULTI}reserva.php" '"total":"Total desde: {total}"'
debe_contener    "$MULTI" '"Mantención 10.000 km","variantes":{"Auto":79990,"SUV":94990,"Camioneta":99990}'
grep -q 'Require all denied' demos/taller/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/taller/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -q 'config|seed|reset' demos/taller/plataforma/.htaccess && pasa 'plataforma/.htaccess niega config, seed y reset' || falla 'plataforma/.htaccess no protege config/seed/reset'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "${MULTI}servicios.php" "${MULTI}reserva.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# Fotos: todas de images.unsplash.com con w= y q=70
FOTOS=$(for p in "$SIMPLE" "$MULTI" "${MULTI}servicios.php" "${MULTI}taller.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE 'src="https://[^"]+"' | grep -v 'cdn.jsdelivr\|cdnjs\|google.com' | grep -vc 'images.unsplash.com/photo-[0-9a-f-]*?w=[0-9]*&amp;q=70&amp;auto=format')
[ "$FOTOS" = "0" ] && pasa "fotos enlazadas desde Unsplash con w y q=70" || falla "$FOTOS fotos con otra fuente o formato"

# --- Plataforma web: base protegida, catálogo de servicios y accesorios, reserva de hora
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
no_debe_contener "${TIENDA}config.php" 'dueno123'
debe_contener    "$TIENDA" 'Cambio de aceite y filtro'
debe_contener    "$TIENDA" 'Cámara para auto Full HD'
debe_contener    "$TIENDA" 'Próxima hora disponible'
debe_contener    "$TIENDA" '¿Tu empresa tiene flota?'
no_debe_contener "$TIENDA" 'Mantención de furgón 10.000 km'
debe_contener    "${TIENDA}?q=frenos" 'Pastillas de freno delanteras'
no_debe_contener "${TIENDA}?q=frenos" 'Cámara para auto'
# Búsqueda por palabras sueltas, en cualquier orden (el taller no usa códigos de producto)
debe_contener    "${TIENDA}?q=aceite+cambio" 'Cambio de aceite y filtro</h3>'
no_debe_contener "${TIENDA}?q=aceite+cambio" 'Pastillas de freno delanteras</h3>'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'mayorista@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_contener    "${TIENDA}?r=registro" 'Empresas con furgones, camionetas o autos de trabajo.'
debe_contener    "${TIENDA}?r=registro" '<strong>Flota</strong>'
debe_responder   "${TIENDA}?r=no-existe" 404

# --- Flujo con cookies (textos ASCII o ya codificados: Git Bash en Windows no pasa bien UTF-8 a curl.exe)
J1="$TMP/cliente.txt"; J2="$TMP/dueno.txt"; J3="$TMP/flota.txt"
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

# Primer destacado: Cambio de aceite y filtro ($39.990)
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1")
case "$R" in 303*) pasa "agregar un servicio a la reserva ($R)";; *) falla "agregar al carrito: $R";; esac
CARRO=$(curl -s -b "$J1" "$BASE${TIENDA}?r=carrito")
printf '%s' "$CARRO" | grep -qF '<strong>Cambio de aceite y filtro</strong>' && pasa "el carrito tiene el servicio" || falla "carrito vacío"
printf '%s' "$CARRO" | grep -qF 'Tu reserva</h1>' && printf '%s' "$CARRO" | grep -qF 'Elegir día y hora' && pasa "carrito con textos de reserva (config textos)" || falla "carrito sin los textos de reserva"

# Checkout: día de la hora calculado en el servidor (sin domingos ni feriados)
CHECK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
printf '%s' "$CHECK" | grep -qF 'Día de tu hora' && pasa "checkout pide el día de la hora" || falla "checkout sin selector de día"
printf '%s' "$CHECK" | grep -qF 'Llevo mi auto al taller' && pasa "checkout con «Llevo mi auto al taller»" || falla "checkout sin texto de retiro del rubro"
for t in 'Reservar hora</h1>' '¿Dónde dejas el auto?' 'Confirmar reserva' 'Tu auto</p>' '¿Qué le notas al auto?' 'Pagar en el taller'; do
  printf '%s' "$CHECK" | grep -qF "$t" && pasa "checkout de reserva: $t" || falla "checkout sin el texto: $t"
done
for c in patente vehiculo anio km; do
  printf '%s' "$CHECK" | grep -qF "name=\"extra[$c]\"" && pasa "checkout pide extra[$c] (checkout_campos)" || falla "checkout sin el campo extra[$c]"
done
printf '%s' "$CHECK" | grep -qF 'En la nota escribe la patente' && falla "la ayuda aún pide la patente en la nota" || pasa "la patente ya no se pide en la nota"
FECHA=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | head -1 | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')
DOMINGOS=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}' | while read -r d; do date -d "$d" +%u; done | grep -c '^7$')
[ -n "$FECHA" ] && [ "$DOMINGOS" = "0" ] && pasa "días ofrecidos sin domingos (primero: $FECHA)" || falla "días ofrecidos: primero '$FECHA', domingos $DOMINGOS"
T=$(printf '%s' "$CHECK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
# Ficha del auto válida (la patente se normaliza: "abcd-12" → ABCD12; el kilometraje acepta puntos)
AUTO=(--data-urlencode "extra[patente]=abcd-12" --data-urlencode "extra[vehiculo]=Sedan mediano" --data-urlencode "extra[anio]=2015" --data-urlencode "extra[km]=84.000")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=2020-01-06" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir" "${AUTO[@]}")
case "$R" in 422*) pasa "un día fuera de la lista se rechaza (422)";; *) falla "día inválido aceptado: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=03:00" --data-urlencode "pago=al_recibir" "${AUTO[@]}")
case "$R" in 422*) pasa "una hora fuera del horario se rechaza (422)";; *) falla "hora inválida aceptada: $R";; esac
# Sin la ficha del auto: 422 con el error de cada campo junto a él
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir")
case "$R" in 422*) pasa "reserva sin la ficha del auto se rechaza (422)";; *) falla "reserva sin ficha del auto aceptada: $R";; esac
for t in 'id="err-extra_patente"' 'Escribe la patente con sus 6 caracteres' 'Completa «Vehículo».' 'Completa «Año».' 'Completa «Kilometraje».'; do
  grep -qF "$t" "$TMP/ultima.html" && pasa "error del campo extra: $t" || falla "falta el error: $t"
done
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir" --data-urlencode "extra[patente]=AB-12" --data-urlencode "extra[vehiculo]=Sedan" --data-urlencode "extra[anio]=1850" --data-urlencode "extra[km]=muchos")
case "$R" in 422*) grep -qF 'Escribe la patente con sus 6 caracteres' "$TMP/ultima.html" && grep -qF '«Año» debe ser un número entre 1970 y' "$TMP/ultima.html" && grep -qF '«Kilometraje» debe ser un número' "$TMP/ultima.html" && pasa "patente corta, año 1850 y kilometraje sin números: 422 con sus errores" || falla "errores de formato sin mensaje";; *) falla "ficha inválida aceptada: $R";; esac
grep -qF 'value="AB-12"' "$TMP/ultima.html" && pasa "el checkout conserva lo escrito en la ficha" || falla "el checkout pierde lo escrito en la ficha"
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Macul" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir" --data-urlencode "nota=Vibra el volante" "${AUTO[@]}")
case "$R" in 303*r=pedido*) pasa "checkout crea la reserva con la ficha del auto ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'VR-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página de la reserva muestra $CODIGO" || falla "página de la reserva sin código"
printf '%s' "$PAGINA" | grep -qF '¡Hora reservada!' && pasa "título propio del rubro (¡Hora reservada!)" || falla "sin el título del rubro"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "reserva ofrece enviar resumen por WhatsApp" || falla "reserva sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Reserva%20de%20prueba%20desde%20la%20demo%20de%20taller%20mec%C3%A1nico%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de reserva de prueba" || falla "resumen sin la nota de prueba"
printf '%s' "$PAGINA" | grep -qF "Reserva $CODIGO" && pasa "la página dice «Reserva $CODIGO» (config textos)" || falla "la página no dice «Reserva $CODIGO»"
printf '%s' "$PAGINA" | grep -qF 'a%20las%2010%3A00' && pasa "resumen con el día y la hora" || falla "resumen sin la hora"
printf '%s' "$PAGINA" | grep -qF 'Nota%3A%20Vibra%20el%20volante' && pasa "resumen con la nota del cliente" || falla "resumen sin la nota del cliente"
for x in 'Patente%3A%20ABCD12' 'Veh%C3%ADculo%3A%20Sedan%20mediano' 'A%C3%B1o%3A%202015' 'Kilometraje%3A%2084000'; do
  printf '%s' "$PAGINA" | grep -qF "$x" && pasa "WhatsApp con $x" || falla "WhatsApp sin $x"
done
printf '%s' "$PAGINA" | grep -qF '<dt>Patente</dt><dd>ABCD12</dd>' && pasa "la página de la reserva muestra la patente normalizada" || falla "la página de la reserva sin la patente"
# $39.990 + retiro a domicilio en Macul $5.990 = $45.980
printf '%s' "$PAGINA" | grep -qF '$45.980' && pasa "total con retiro a domicilio en Macul (\$45.980)" || falla "total de la reserva inesperado"

# Pago de prueba: checkout → pantalla simulada → aprobar
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=prueba" "${AUTO[@]}")
case "$R" in 303*r=pago*) pasa "pago de prueba lleva a la pantalla simulada";; *) falla "pago de prueba: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=pago" | grep -qF 'PAGO DE PRUEBA' && pasa "pantalla de pago marcada como prueba" || falla "pantalla de pago sin aviso"
T=$(csrf "$J1" "${TIENDA}?r=pago")
R=$(post "$J1" "${TIENDA}?r=pago" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar")
case "$R" in 303*r=pedido*) pasa "aprobar pago de prueba crea la reserva";; *) falla "aprobar pago: $R";; esac
curl -s -b "$J1" "${R#* }" | grep -qF 'Tu hora quedó reservada' && pasa "mensaje de éxito propio del rubro" || falla "sin mensaje de éxito del rubro"

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
printf '%s' "$PANEL" | grep -qF 'Patente: ABCD12 · Vehículo: Sedan mediano · Año: 2015 · Kilometraje: 84000' && pasa "el panel muestra la ficha del auto (Etiqueta: valor)" || falla "el panel sin la ficha del auto"
printf '%s' "$PANEL" | grep -qF 'Patente: PQRS78' && pasa "el panel muestra la ficha de las reservas de ejemplo (seed)" || falla "reservas de ejemplo sin ficha del auto"
printf '%s' "$PANEL" | grep -qF '>Reservas<' && pasa "panel con la pestaña «Reservas»" || falla "panel sin la pestaña «Reservas»"
curl -s -b "$J1" "$BASE${TIENDA}?r=cuenta" | grep -qF 'Patente: ABCD12' && pasa "Mi cuenta muestra la ficha del auto de cada reserva" || falla "Mi cuenta sin la ficha del auto"
RESUMEN=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel")
printf '%s' "$RESUMEN" | grep -qF 'Ventas de hoy' && pasa "panel con ventas del día" || falla "panel sin ventas del día"
printf '%s' "$RESUMEN" | grep -qF 'Stock bajo: Pre-revisión técnica (3)' && pasa "panel avisa cupos bajos" || falla "panel sin aviso de cupos bajos"
printf '%s' "$RESUMEN" | grep -qF '1 flota(s) esperan aprobación' && pasa "panel avisa flotas por aprobar" || falla "panel sin aviso de flotas"
printf '%s' "$RESUMEN" | grep -qF '>Flotas (1)</a>' && pasa "pestaña «Flotas» con el nombre del rubro" || falla "pestaña de mayoristas sin el nombre del rubro"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "un cliente no entra al panel (403)" || falla "un cliente entra al panel"

# Estado: nuevo → preparando
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">Preparando' && pasa "el cliente ve su reserva en «Preparando»" || falla "estado de la reserva no cambió ($R)"

# Flota: precios de flota y planes para furgones; pendiente hasta aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
FLOTA=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$FLOTA" | grep -qF 'Mantención de furgón 10.000 km' && pasa "la flota ve los planes para furgones" || falla "la flota no ve los planes"
printf '%s' "$FLOTA" | grep -qF '<strong>$34.990</strong>' && printf '%s' "$FLOTA" | grep -qF '<s aria-label="Precio normal">$39.990</s> <span class="t-etiqueta">Precio flota</span>' && pasa "la flota ve precio flota (\$34.990 en vez de \$39.990)" || falla "la flota sin precio flota"
J4="$TMP/nueva-flota.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Flota Prueba" --data-urlencode "email=flota$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Reparto de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Mantención de furgón 10.000 km' && falla "flota pendiente ve los planes" || pasa "flota pendiente aún no ve los planes"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Reparto de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Mantención de furgón 10.000 km' && pasa "el dueño aprueba la flota y ya ve los planes" || falla "aprobación de flota no funcionó"

# Panel: crear un servicio con una foto de la galería
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Servicio de Prueba" --data-urlencode "descripcion=Creado por el test" --data-urlencode "categoria_id=1" --data-urlencode "precio=12345" --data-urlencode "precio_mayorista=10000" --data-urlencode "stock=5" --data-urlencode "foto=1777118947168-b6e806cb80cf" --data-urlencode "visible=1")
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
tools/php.sh demos/taller/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

# --- Modelos 3D (demos/_comun/3d/, ver tests/demos_3d.sh): botón «Ver en 3D» con el estilo del rubro,
# model-viewer 4.4.0 con SRI, menu3d.js y data-3d-objeto="producto" (el visor no habla de «plato»). La Simple-page, sin 3D.
# Los servicios del taller no tienen 3D (no se compra un disco ni una batería suelta): la página no carga el visor
no_debe_contener "${MULTI}servicios.php" 'demo-3d-btn'
no_debe_contener "${MULTI}servicios.php" 'model-viewer'
debe_contener    "$TIENDA" 'data-3d-modelo="/demos/_comun/3d/neumatico.glb"'
debe_contener    "$TIENDA" 'class="demo-3d-btn t-btn-3d"'
debe_contener    "$TIENDA" 'data-3d-modelo="/demos/_comun/3d/extintor.glb"'
no_debe_contener "${MULTI}servicios.php" '/demos/_comun/js/menu3d.js?v='
debe_contener    "$TIENDA" '<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js" integrity="sha256-GLLnEQNapHBe4oFTClXcIzj/Be7T1wrhbVQQUoKnXtU=" crossorigin="anonymous"></script>'
debe_contener    "$TIENDA" '/demos/_comun/js/menu3d.js?v='
debe_contener    "$TIENDA" 'data-3d-objeto="producto"'
for m in $(grep -rhoE "'modelo3?d?' => '[a-z0-9-]+'" "demos/taller/multi/_datos.php" "demos/taller/plataforma/seed.php" | sed -E "s/.*=> '([a-z0-9-]+)'/\1/" | sort -u); do
  [ -f "demos/_comun/3d/$m.glb" ] && pasa "$m.glb existe" || falla "falta demos/_comun/3d/$m.glb"
done
no_debe_contener "$SIMPLE" 'model-viewer'
no_debe_contener "$SIMPLE" 'demo-3d-btn'

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
