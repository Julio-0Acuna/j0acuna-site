#!/usr/bin/env bash
# Pruebas del rubro peluquería canina (demos/peluqueria-canina/): levanta `php -S` con el router y revisa con curl.
# Incluye el flujo de la Plataforma web con cookies: registro → login → carrito → reserva del furgón con día, hora
# y ficha del perro → "Tus perros" en Mi cuenta → panel de la dueña.
# Uso: bash tests/demos_peluqueria-canina.sh   (puerto 8117; otro con CANINA_PORT=…)
cd "$(dirname "$0")/.." || exit 1

PORT=${CANINA_PORT:-$(( 8117 + ${PUERTO_DESFASE:-0} ))}
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/peluqueria-canina"
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
tools/php.sh demos/peluqueria-canina/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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
debe_contener    "$RUBRO/" 'Página web para peluquerías caninas en Santiago'
debe_contener    "$RUBRO/" '"@type":"Service"'
debe_contener    "$RUBRO/" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO/" "href=\"$SIMPLE\""
debe_contener    "$RUBRO/" "href=\"$MULTI\""
debe_contener    "$RUBRO/" "href=\"$TIENDA\""
debe_contener    "$RUBRO/" 'Quiero esta'
debe_contener    "$RUBRO/" 'Perfil de Empresa en Google'
no_debe_contener "$RUBRO/" 'noindex'
for img in simple multi plataforma; do debe_responder "$RUBRO/img/$img.webp" 200; done
[ -f demos/peluqueria-canina/CREDITOS.md ] && pasa "CREDITOS.md del rubro existe" || falla "falta demos/peluqueria-canina/CREDITOS.md"

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}rituales.php ${MULTI}casa.php ${MULTI}preguntas.php ${MULTI}contacto.php ${MULTI}reserva.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" "href=\"$RUBRO/\""
  debe_contener    "$p" '<meta name="robots" content="noindex, follow">'
  debe_contener    "$p" '56973537642'
  no_debe_contener "$p" 'LocalBusiness'
  no_debe_contener "$p" 'AggregateRating'
  no_debe_contener "$p" 'application/ld+json'
  # Peluquería canina: sin productos en 3D ni menú QR (pedido de Julio)
  no_debe_contener "$p" 'model-viewer'
  no_debe_contener "$p" 'demo-3d-btn'
  no_debe_contener "$p" 'data-demo-qr'
done
grep -q "modelos3d\|modelo3d" demos/peluqueria-canina/plataforma/config.php demos/peluqueria-canina/plataforma/seed.php && falla "la Plataforma declara modelos 3D" || pasa "la Plataforma no declara modelos 3D"

# Sección Google en cada versión
for p in "$SIMPLE" "$MULTI" "${MULTI}contacto.php" "$TIENDA"; do
  debe_contener "$p" 'Encuéntranos en Google'
  debe_contener "$p" 'https://www.google.com/maps?q='
  debe_contener "$p" 'output=embed'
  debe_contener "$p" 'Reseña de ejemplo'
  debe_contener "$p" 'Déjanos tu reseña'
done

# --- Simple-page: pizarra de precios por tamaño, tarjeta de sellos, galería y ficha para pedir hora
for id in precios servicios sellos visita galeria reservar horario; do debe_contener "$SIMPLE" "id=\"$id\""; done
debe_contener    "$SIMPLE" '<td data-tam="Pequeño"><a href="?servicio=bano&amp;tamano=pequeno#reservar"'
debe_contener    "$SIMPLE" '$34.990</a>'
debe_contener    "$SIMPLE" '<span class="lc-no-aplica" title="No aplica">—</span>'
debe_contener    "$SIMPLE" 'name="perro"'
debe_contener    "$SIMPLE" 'name="tamano" value="gato"'
debe_contener    "$SIMPLE" '%28Reserva%20de%20prueba%20desde%20la%20demo%20de%20peluquer%C3%ADa%20canina%20%E2%80%93%20Simple-page%29'
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
PREVIO=$(curl -s "$BASE${SIMPLE}?servicio=corte&tamano=mediano")
printf '%s' "$PREVIO" | grep -qF '<option value="corte" selected>' && printf '%s' "$PREVIO" | grep -qF 'value="mediano" checked' && pasa "?servicio=corte&tamano=mediano deja la ficha elegida" || falla "la ficha no se precarga desde la URL"
PAG_SIMPLE=$(curl -s "$BASE$SIMPLE")
FECHAS_SIMPLE=$(printf '%s' "$PAG_SIMPLE" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')
DIA=$(printf '%s\n' "$FECHAS_SIMPLE" | while read -r d; do [ "$(date -d "$d" +%u)" != "2" ] && [ "$(date -d "$d" +%u)" != "6" ] && echo "$d"; done | head -1)
SABADO=$(printf '%s\n' "$FECHAS_SIMPLE" | while read -r d; do [ "$(date -d "$d" +%u)" = "6" ] && echo "$d"; done | head -1)
MARTES=$(printf '%s\n' "$FECHAS_SIMPLE" | while read -r d; do [ "$(date -d "$d" +%u)" = "2" ] && echo "$d"; done | head -1)
DOMINGOS=$(printf '%s\n' "$FECHAS_SIMPLE" | while read -r d; do date -d "$d" +%u; done | grep -c '^7$')
[ -n "$DIA" ] && [ "$DOMINGOS" = "0" ] && pasa "días de la ficha sin domingos (un día de semana: $DIA)" || falla "días de la ficha: '$DIA', domingos $DOMINGOS"
R=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" --data-urlencode "servicio=corte" --data-urlencode "tamano=mediano" --data-urlencode "pelo=largo" --data-urlencode "perro=Bruno" --data-urlencode "raza=Schnauzer" --data-urlencode "dia=$DIA" --data-urlencode "bloque=manana" --data-urlencode "detalle=Le da miedo el secador" "$BASE$SIMPLE")
case "$R" in "303 https://wa.me/56973537642?text="*Servicio%3A%20Ba%C3%B1o%20y%20corte%20de%20raza*Tama%C3%B1o%3A%20Mediano%20%2810%20a%2025%20kg%29*Pelo%3A%20Largo*Precio%20seg%C3%BAn%20la%20tabla%3A%20%2423.990*Perro%3A%20Bruno*Raza%3A%20Schnauzer*Bloque%3A%20Ma%C3%B1ana*C%C3%B3mo%20es%3A%20Le%20da%20miedo%20el%20secador*%28Reserva%20de%20prueba*Simple-page%29) pasa "la ficha arma el WhatsApp con servicio, tamaño, pelo, precio de la tabla (\$21.990 + \$2.000), perro, raza, día y bloque";; *) falla "ficha de hora: $R";; esac
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "servicio=bano" --data-urlencode "tamano=pequeno" --data-urlencode "perro=" --data-urlencode "dia=$DIA" --data-urlencode "bloque=manana" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'Cuéntanos cómo se llama.' "$TMP/simple.html" && pasa "sin nombre del perro: 422 con el error junto al campo" || falla "perro vacío: $R"
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "servicio=bano" --data-urlencode "tamano=mediano" --data-urlencode "perro=<script>x</script>" --data-urlencode "dia=2020-01-06" --data-urlencode "bloque=manana" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'value="&lt;script&gt;x&lt;/script&gt;"' "$TMP/simple.html" && ! grep -qF '<script>x</script>' "$TMP/simple.html" && pasa "día fuera de la lista: 422 y el nombre vuelve escapado" || falla "día inválido o nombre sin escapar: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "servicio=../../etc" --data-urlencode "tamano=mediano" --data-urlencode "perro=Bruno" --data-urlencode "dia=$DIA" --data-urlencode "bloque=manana" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "servicio fuera de la lista no redirige" || falla "servicio inválido: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "servicio=bano" --data-urlencode "tamano=elefante" --data-urlencode "perro=Bruno" --data-urlencode "dia=$DIA" --data-urlencode "bloque=manana" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "tamaño fuera de la lista no redirige" || falla "tamaño inválido: $R"
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "servicio=deslanado" --data-urlencode "tamano=pequeno" --data-urlencode "perro=Bruno" --data-urlencode "dia=$DIA" --data-urlencode "bloque=manana" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'El deslanado es para perros de doble manto, de tamaño mediano o más grandes.' "$TMP/simple.html" && pasa "deslanado de perro pequeño (sin precio en la tabla) se rechaza" || falla "deslanado pequeño aceptado: $R"
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "servicio=bano" --data-urlencode "tamano=gato" --data-urlencode "perro=Mishi" --data-urlencode "dia=$DIA" --data-urlencode "bloque=manana" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'A los gatos los atendemos los martes en la mañana' "$TMP/simple.html" && pasa "gato fuera del martes en la mañana se rechaza ($DIA)" || falla "gato en otro día aceptado: $R"
if [ -n "$MARTES" ]; then
  R=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" --data-urlencode "servicio=bano" --data-urlencode "tamano=gato" --data-urlencode "perro=Mishi" --data-urlencode "dia=$MARTES" --data-urlencode "bloque=manana" "$BASE$SIMPLE")
  case "$R" in "303 https://wa.me/"*Tama%C3%B1o%3A%20Gato*%2414.990*Gato%3A%20Mishi*) pasa "gato el martes en la mañana: WhatsApp con su precio (\$14.990)";; *) falla "gato el martes: $R";; esac
else
  falla "no hay martes en la lista de días"
fi
if [ -n "$SABADO" ]; then
  R=$(curl -s -o "$TMP/sabado.html" -w "%{http_code}" --data-urlencode "servicio=bano" --data-urlencode "tamano=grande" --data-urlencode "perro=Bruno" --data-urlencode "dia=$SABADO" --data-urlencode "bloque=tarde" "$BASE$SIMPLE")
  [ "$R" = "422" ] && grep -qF 'Los sábados atendemos solo en la mañana.' "$TMP/sabado.html" && pasa "sábado en la tarde se rechaza ($SABADO)" || falla "sábado en la tarde aceptado: $R"
else
  falla "no hay sábados en la lista de días"
fi

# --- Multi-page: carta con buscador y tamaños, la casa, reserva con la ficha del perro y traslado
debe_contener    "${MULTI}rituales.php" 'data-buscador'
debe_contener    "${MULTI}rituales.php" 'class="demo-agregar br-agregar" data-id="ritual-bruma"'
debe_contener    "${MULTI}rituales.php" '<option value="Mediano">Mediano · $31.990</option>'
debe_contener    "${MULTI}rituales.php?q=gato" 'id="s-gato-bano" data-buscable'
curl -s "$BASE${MULTI}rituales.php?q=gato" | grep -F 'id="s-ritual-bruma"' | grep -qF ' hidden>' && pasa "el buscador sin JS oculta lo que no coincide" || falla "el buscador sin JS no oculta"
debe_contener    "${MULTI}casa.php" 'Antonia Valdés'
debe_contener    "${MULTI}preguntas.php" '¿Usan jaulas de secado?'
debe_contener    "${MULTI}reserva.php" 'data-carrito-form'
for extra in Perro Raza Tamaño Pelo Carácter 'Piel sensible' Día Hora Groomer; do
  debe_contener  "${MULTI}reserva.php" "data-carrito-extra=\"$extra\""
done
debe_contener    "${MULTI}reserva.php" 'name="entrega" value="despacho"'
debe_contener    "${MULTI}reserva.php" '<option value="Vitacura">Vitacura · $7.990</option>'
debe_contener    "${MULTI}reserva.php" 'data-carrito-gratis'
no_debe_contener "${MULTI}reserva.php" '>Domingo '
no_debe_contener "${MULTI}reserva.php" '>Lunes '
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '(Reserva de prueba desde la demo de peluquería canina – Multi-page)'
debe_contener    "$MULTI" '"saludo":"Hola, quiero reservar en {negocio}:"'
debe_contener    "$MULTI" '"retiro":"Llegada: lo llevo yo al spa","despacho":"Llegada: traslado ida y vuelta desde {direccion}, {comuna}"'
debe_contener    "$MULTI" '"Ritual Bruma","variantes":{"Pequeño":24990,"Mediano":31990,"Grande":39990,"Gigante":49990}'
debe_contener    "$MULTI" '"gratis_desde":70000'
grep -q 'Require all denied' demos/peluqueria-canina/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/peluqueria-canina/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -q 'config|seed|reset' demos/peluqueria-canina/plataforma/.htaccess && pasa 'plataforma/.htaccess niega config, seed y reset' || falla 'plataforma/.htaccess no protege config/seed/reset'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "${MULTI}rituales.php" "${MULTI}reserva.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# Fotos: todas de images.unsplash.com con w= y q=70
FOTOS=$(for p in "$SIMPLE" "$MULTI" "${MULTI}rituales.php" "${MULTI}casa.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE 'src="https://[^"]+"' | grep -v 'cdn.jsdelivr\|cdnjs\|google.com' | grep -vc 'images.unsplash.com/photo-[0-9a-f-]*?w=[0-9]*&amp;q=70&amp;auto=format')
[ "$FOTOS" = "0" ] && pasa "fotos enlazadas desde Unsplash con w y q=70" || falla "$FOTOS fotos con otra fuente o formato"

# --- Plataforma web: base protegida, portada con ruta y precios por tamaño, tienda y convenios
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
no_debe_contener "${TIENDA}config.php" 'dueno123'
debe_contener    "$TIENDA" 'La peluquería <span>llega a tu puerta.</span>'
debe_contener    "$TIENDA" 'La ruta de la semana'
debe_contener    "$TIENDA" 'Elige el servicio y el tamaño de tu perro'
debe_contener    "$TIENDA" '<strong class="tr-tabla__precio">$19.990</strong>'
debe_contener    "$TIENDA" '<span class="tr-tabla__no">No aplica</span>'
debe_contener    "$TIENDA" 'Baño · perro mediano'
debe_contener    "$TIENDA" 'Shampoo de avena 500 ml'
debe_contener    "$TIENDA" '¿Administras un condominio, un criadero o una guardería?'
no_debe_contener "$TIENDA" 'Jornada del furgón en tu condominio'
debe_contener    "${TIENDA}?q=panoleta" 'Pañoleta de algodón'
no_debe_contener "${TIENDA}?q=panoleta" 'Baño · perro gigante'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'mayorista@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_contener    "${TIENDA}?r=registro" 'Condominios, criaderos, guarderías y hoteles caninos.'
debe_contener    "${TIENDA}?r=registro" '<strong>Convenio</strong>'
debe_responder   "${TIENDA}?r=no-existe" 404

# --- Flujo con cookies (textos ASCII o ya codificados: Git Bash en Windows no pasa bien UTF-8 a curl.exe)
J1="$TMP/cliente.txt"; J2="$TMP/duena.txt"; J3="$TMP/convenio.txt"; J5="$TMP/demo.txt"
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

# Primer botón de la tabla de precios: Baño · perro pequeño ($14.990)
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1")
case "$R" in 303*r=carrito*) pasa "agregar desde la tabla de precios lleva a la reserva ($R)";; *) falla "agregar al carrito: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF '<strong>Baño · perro pequeño</strong>' && pasa "la reserva tiene el baño de perro pequeño" || falla "carrito vacío"

# Checkout: día y hora de llegada del furgón calculados en el servidor, ficha del perro como campos del motor
CHECK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
printf '%s' "$CHECK" | grep -qF 'Agendar el furgón</h1>' && pasa "checkout «Agendar el furgón»" || falla "checkout sin título del rubro"
printf '%s' "$CHECK" | grep -qF 'Lo llevo a la base de Peñalolén' && printf '%s' "$CHECK" | grep -qF 'El furgón va a mi casa' && pasa "checkout con base y furgón a domicilio" || falla "checkout sin los textos de entrega del rubro"
printf '%s' "$CHECK" | grep -qF 'Hora de llegada del furgón' && pasa "checkout pide la hora de llegada del furgón" || falla "checkout sin hora de llegada"
printf '%s' "$CHECK" | grep -qF 'Cuéntanos de tu perro' && pasa "checkout con la ficha del perro" || falla "checkout sin la ficha del perro"
printf '%s' "$CHECK" | grep -qF '<option value="productos">Solo compro productos</option>' && pasa "tamaño con la opción «Solo compro productos»" || falla "tamaño sin la opción de solo productos"
printf '%s' "$CHECK" | grep -qF 'Confirmar y agendar</button>' && pasa "checkout con «Confirmar y agendar»" || falla "checkout sin el botón del rubro"
EXTRAS=$(printf '%s' "$CHECK" | grep -oE 'name="extra\[[a-z_]+\]"' | sed -E 's/name="([^"]+)"/\1/' | tr '\n' ' ')
FALTAN=$(for k in csrf entrega direccion_id calle comuna fecha_entrega cuando pago nota; do printf '%s' "$CHECK" | grep -qF "name=\"$k\"" || echo "$k"; done)
[ "$EXTRAS" = "extra[perro] extra[tamano] extra[raza] extra[pelo] extra[estacionar] " ] && [ -z "$FALTAN" ] && pasa "el formulario del checkout trae todos los campos que se envían ($EXTRAS)" || falla "al formulario le faltan: $FALTAN (extras: '$EXTRAS')"
FECHA=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | head -1 | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')
DOMINGOS=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}' | while read -r d; do date -d "$d" +%u; done | grep -c '^7$')
[ -n "$FECHA" ] && [ "$DOMINGOS" = "0" ] && pasa "días ofrecidos sin domingos (primero: $FECHA)" || falla "días ofrecidos: primero '$FECHA', domingos $DOMINGOS"
T=$(printf '%s' "$CHECK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=2020-01-06" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir" --data-urlencode "extra[tamano]=pequeno")
case "$R" in 422*) pasa "un día fuera de la lista se rechaza (422)";; *) falla "día inválido aceptado: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=03:00" --data-urlencode "pago=al_recibir" --data-urlencode "extra[tamano]=pequeno")
case "$R" in 422*) pasa "una hora fuera del horario se rechaza (422)";; *) falla "hora inválida aceptada: $R";; esac
# Sin tamaño (ni "solo productos") y furgón a domicilio sin decir dónde estacionar: 422 con sus errores; el nombre vuelve escapado
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=La Reina" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=al_recibir" --data-urlencode "extra[perro]=<script>x</script>" --data-urlencode "extra[tamano]=elefante")
case "$R" in 422*) grep -qF '<p class="t-error" id="err-extra_tamano">Cuéntanos el tamaño de tu perro o elige «Solo compro productos».</p>' "$TMP/ultima.html" && grep -qF '<p class="t-error" id="err-extra_estacionar">Cuéntanos dónde se puede estacionar el furgón.</p>' "$TMP/ultima.html" && pasa "tamaño inexistente y sin lugar para el furgón: 422 con sus errores" || falla "tamaño o estacionamiento: 422 sin los errores";; *) falla "tamaño inexistente aceptado: $R";; esac
grep -qF 'value="&lt;script&gt;x&lt;/script&gt;"' "$TMP/ultima.html" && ! grep -qF '<script>x</script>' "$TMP/ultima.html" && pasa "el nombre del perro vuelve escapado" || falla "el nombre del perro no se escapa"
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=La Reina" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir" --data-urlencode "extra[perro]=Toby" --data-urlencode "extra[tamano]=mediano" --data-urlencode "extra[raza]=Beagle" --data-urlencode "extra[pelo]=corto" --data-urlencode "extra[estacionar]=calle" --data-urlencode "nota=Le gusta el agua tibia")
case "$R" in 303*r=pedido*) pasa "checkout agenda el furgón ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'TR-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página de la reserva muestra $CODIGO" || falla "página de la reserva sin código"
printf '%s' "$PAGINA" | grep -qF '¡Furgón agendado!' && pasa "título propio del rubro (¡Furgón agendado!)" || falla "sin el título del rubro"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "la reserva ofrece enviar resumen por WhatsApp" || falla "reserva sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Reserva%20de%20prueba%20desde%20la%20demo%20de%20peluquer%C3%ADa%20canina%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de reserva de prueba" || falla "resumen sin la nota de prueba"
printf '%s' "$PAGINA" | grep -qF 'a%20las%2010%3A00' && printf '%s' "$PAGINA" | grep -qF 'D%C3%B3nde%3A%20el%20furg%C3%B3n%20va%20a%20La%20Reina' && pasa "resumen con el día, la hora y la comuna del furgón" || falla "resumen sin la hora o la comuna"
printf '%s' "$PAGINA" | grep -qF 'Mascota%3A%20Toby%0ATama%C3%B1o%3A%20Mediano%20%2810%20a%2025%20kg%29%0ARaza%3A%20Beagle%0APelo%3A%20Corto%0AD%C3%B3nde%20estacionar%3A%20En%20la%20calle%2C%20frente%20a%20la%20casa' && printf '%s' "$PAGINA" | grep -qF 'Nota%3A%20Le%20gusta%20el%20agua%20tibia' && pasa "WhatsApp con la ficha del perro y la nota" || falla "resumen sin la ficha del perro"
printf '%s' "$PAGINA" | grep -qF '<dt>Mascota</dt><dd>Toby</dd>' && printf '%s' "$PAGINA" | grep -qF '<dt>Tamaño</dt><dd>Mediano (10 a 25 kg)</dd>' && pasa "la reserva muestra la ficha del perro" || falla "la reserva sin la ficha del perro"
printf '%s' "$PAGINA" | grep -qF '<li class="is-hecho is-actual" aria-current="step">Agendado</li>' && pasa "primer paso «Agendado»" || falla "sin el paso «Agendado»"
# $14.990 + traslado a La Reina $3.990 = $18.980
printf '%s' "$PAGINA" | grep -qF '$18.980' && pasa "total con traslado del furgón a La Reina (\$18.980)" || falla "total de la reserva inesperado"

# Solo productos, con retiro en la base: no pide dónde estacionar
T=$(csrf "$J1" "$TIENDA")
PROD=$(curl -s -b "$J1" "$BASE${TIENDA}?cat=tienda" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PROD" --data-urlencode "cantidad=1" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=prueba" --data-urlencode "extra[tamano]=productos")
case "$R" in 303*r=pago*) pasa "solo productos con retiro en la base: va al pago de prueba sin pedir el estacionamiento";; *) falla "solo productos: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=pago" | grep -qF 'PAGO DE PRUEBA' && pasa "pantalla de pago marcada como prueba" || falla "pantalla de pago sin aviso"
T=$(csrf "$J1" "${TIENDA}?r=pago")
R=$(post "$J1" "${TIENDA}?r=pago" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar")
case "$R" in 303*r=pedido*) pasa "aprobar pago de prueba crea la reserva";; *) falla "aprobar pago: $R";; esac
curl -s -b "$J1" "${R#* }" | grep -qF '¡Pago de prueba aprobado! El furgón quedó agendado.' && pasa "mensaje de éxito propio del rubro" || falla "sin mensaje de éxito del rubro"

# Mi cuenta: "Tus perros" con la ficha armada desde las reservas, y "Agendar de nuevo"
CUENTA=$(curl -s -b "$J1" "$BASE${TIENDA}?r=cuenta")
printf '%s' "$CUENTA" | grep -qF 'Tus perros' && printf '%s' "$CUENTA" | grep -qF '<h3>Toby</h3>' && printf '%s' "$CUENTA" | grep -qF 'Beagle · Mediano (10 a 25 kg) · pelo corto' && pasa "Mi cuenta muestra la ficha de Toby" || falla "Mi cuenta sin «Tus perros»"
T=$(csrf "$J1" "${TIENDA}?r=cuenta")
PEDIDO_ID=$(printf '%s' "$CUENTA" | grep -oE 'name="pedido_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
R=$(post "$J1" "${TIENDA}?r=cuenta" --data-urlencode "csrf=$T" --data-urlencode "accion=repetir" --data-urlencode "pedido_id=$PEDIDO_ID")
case "$R" in 303*r=carrito*) pasa "agendar de nuevo vuelve a llenar la reserva";; *) falla "repetir: $R";; esac
T=$(csrf "$J5" "${TIENDA}?r=ingresar")
post "$J5" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=cliente@demo.cl" --data-urlencode "clave=cliente123" > /dev/null
DEMO=$(curl -s -b "$J5" "$BASE${TIENDA}?r=cuenta")
printf '%s' "$DEMO" | grep -qF '<h3>Rocco</h3>' && printf '%s' "$DEMO" | grep -qF '<h3>Luna</h3>' && printf '%s' "$DEMO" | grep -qF 'Próximo baño sugerido' && printf '%s' "$DEMO" | grep -qF 'Agendar lo mismo para Rocco' && pasa "la cuenta de prueba trae las fichas de Rocco y Luna con su próximo baño" || falla "la cuenta de prueba sin las fichas de sus perros"

# La dueña ve la reserva en su panel
T=$(csrf "$J2" "${TIENDA}?r=ingresar")
R=$(post "$J2" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=dueno@demo.cl" --data-urlencode "clave=dueno123")
case "$R" in 303*r=panel*) pasa "la dueña entra a su panel";; *) falla "login dueña: $R";; esac
PANEL=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=pedidos")
printf '%s' "$PANEL" | grep -qF "$CODIGO" && pasa "la dueña ve $CODIGO en el panel" || falla "la dueña no ve la reserva"
printf '%s' "$PANEL" | grep -qF 'Cliente Prueba' && pasa "el panel muestra al cliente" || falla "el panel no muestra al cliente"
printf '%s' "$PANEL" | grep -qF ' · para el ' && pasa "el panel muestra el día de cada reserva" || falla "el panel sin el día"
printf '%s' "$PANEL" | grep -qF '<p class="t-pequeno t-pedido-panel__extras">Mascota: Toby · Tamaño: Mediano (10 a 25 kg) · Raza: Beagle · Pelo: Corto · Dónde estacionar: En la calle, frente a la casa</p>' && pasa "la lista del panel muestra la ficha del perro" || falla "la lista del panel sin la ficha"
printf '%s' "$PANEL" | grep -qF 'Mascota: Rocco · Tamaño: Mediano (10 a 25 kg) · Raza: Schnauzer' && pasa "las reservas de ejemplo traen la ficha como campos" || falla "las reservas de ejemplo sin la ficha"
RESUMEN=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel")
printf '%s' "$RESUMEN" | grep -qF 'Ventas de hoy' && pasa "panel con ventas del día" || falla "panel sin ventas del día"
printf '%s' "$RESUMEN" | grep -qF 'Stock bajo: Baño · perro gigante (3)' && pasa "panel avisa cupos bajos" || falla "panel sin aviso de cupos bajos"
printf '%s' "$RESUMEN" | grep -qF '1 convenio(s) esperan aprobación' && pasa "panel avisa convenios por aprobar" || falla "panel sin aviso de convenios"
printf '%s' "$RESUMEN" | grep -qF '>Convenios (1)</a>' && pasa "pestaña «Convenios» con el nombre del rubro" || falla "pestaña de mayoristas sin el nombre del rubro"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "un cliente no entra al panel (403)" || falla "un cliente entra al panel"

# Estado: agendado → confirmado
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">Confirmado' && pasa "el cliente ve su reserva «Confirmado»" || falla "estado de la reserva no cambió ($R)"

# Convenio: precio convenio y jornadas del furgón; pendiente hasta la aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
CONV=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$CONV" | grep -qF 'Jornada del furgón en tu condominio (6 baños)' && pasa "el convenio ve la jornada del furgón" || falla "el convenio no ve la jornada"
printf '%s' "$CONV" | grep -qF '<strong>$12.990</strong>' && printf '%s' "$CONV" | grep -qF '<s aria-label="Precio normal">$14.990</s> <span class="t-etiqueta">Precio convenio</span>' && pasa "el convenio ve precio convenio (\$12.990 en vez de \$14.990)" || falla "el convenio sin precio convenio"
J4="$TMP/nuevo-convenio.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Convenio Prueba" --data-urlencode "email=convenio$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Condominio de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Jornada del furgón en tu condominio' && falla "convenio pendiente ve la jornada" || pasa "convenio pendiente aún no ve la jornada"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Condominio de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Jornada del furgón en tu condominio' && pasa "la dueña aprueba el convenio y ya ve la jornada" || falla "aprobación de convenio no funcionó"

# Panel: crear un producto con una foto de la galería
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Producto de Prueba" --data-urlencode "descripcion=Creado por el test" --data-urlencode "categoria_id=6" --data-urlencode "precio=12345" --data-urlencode "precio_mayorista=10000" --data-urlencode "stock=5" --data-urlencode "foto=1783268287883-527863dac66a" --data-urlencode "visible=1")
case "$R" in 303*) pasa "la dueña crea un producto";; *) falla "crear producto: $R";; esac
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
tools/php.sh demos/peluqueria-canina/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
