#!/usr/bin/env bash
# Pruebas del rubro veterinaria (demos/veterinaria/): levanta `php -S` con el router y revisa con curl.
# Incluye el flujo de la Plataforma web con cookies: registro → login → carrito → hora con día y hora → panel de la dueña.
# Uso: bash tests/demos_veterinaria.sh   (puerto 8105; otro con VET_PORT=…)
cd "$(dirname "$0")/.." || exit 1

PORT=${VET_PORT:-8105}
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/veterinaria"
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
tools/php.sh demos/veterinaria/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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
debe_contener    "$RUBRO/" 'Página web para veterinarias en Santiago'
debe_contener    "$RUBRO/" '"@type":"Service"'
debe_contener    "$RUBRO/" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO/" "href=\"$SIMPLE\""
debe_contener    "$RUBRO/" "href=\"$MULTI\""
debe_contener    "$RUBRO/" "href=\"$TIENDA\""
debe_contener    "$RUBRO/" 'Quiero esta'
debe_contener    "$RUBRO/" 'Perfil de Empresa en Google'
no_debe_contener "$RUBRO/" 'noindex'
for img in simple multi plataforma; do debe_responder "$RUBRO/img/$img.webp" 200; done
[ -f demos/veterinaria/CREDITOS.md ] && pasa "CREDITOS.md del rubro existe" || falla "falta demos/veterinaria/CREDITOS.md"

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}especialidades.php ${MULTI}equipo.php ${MULTI}preguntas.php ${MULTI}contacto.php ${MULTI}reserva.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" "href=\"$RUBRO/\""
  debe_contener    "$p" '<meta name="robots" content="noindex, follow">'
  debe_contener    "$p" '56973537642'
  no_debe_contener "$p" 'LocalBusiness'
  no_debe_contener "$p" 'VeterinaryCare'
  no_debe_contener "$p" 'AggregateRating'
  no_debe_contener "$p" 'application/ld+json'
  # Veterinaria: sin menú QR ni 3D
  no_debe_contener "$p" 'model-viewer'
  no_debe_contener "$p" 'demo-3d-btn'
  no_debe_contener "$p" 'data-demo-qr'
  # Sin recetas ni dosis en los textos
  no_debe_contener "$p" 'mg/kg'
done

# Sección Google en cada versión
for p in "$SIMPLE" "$MULTI" "${MULTI}contacto.php" "$TIENDA"; do
  debe_contener "$p" 'Encuéntranos en Google'
  debe_contener "$p" 'https://www.google.com/maps?q='
  debe_contener "$p" 'output=embed'
  debe_contener "$p" 'Reseña de ejemplo'
  debe_contener "$p" 'Déjanos tu reseña'
done

# Urgencias con horario claro (ninguna versión promete 24 horas)
debe_contener    "$SIMPLE" 'No somos una clínica 24 horas.'
debe_contener    "$MULTI" 'No somos una clínica 24 horas.'
debe_contener    "${MULTI}contacto.php" 'No somos una clínica 24 horas.'
debe_contener    "$TIENDA" 'No somos 24 horas.'

# --- Simple-page: servicios, carnet de vacunas, esterilización y ficha de reserva
debe_contener    "$SIMPLE" 'id="servicios"'
debe_contener    "$SIMPLE" 'id="vacunas"'
debe_contener    "$SIMPLE" 'id="esterilizacion"'
debe_contener    "$SIMPLE" 'id="reservar"'
debe_contener    "$SIMPLE" '<small>desde</small> $18.000'
debe_contener    "$SIMPLE" '<dd>$125.000</dd>'
debe_contener    "$SIMPLE" 'name="mascota"'
debe_contener    "$SIMPLE" 'name="especie" value="gato"'
debe_contener    "$SIMPLE" '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20veterinaria%20%E2%80%93%20Simple-page%29'
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
debe_contener    "${SIMPLE}?servicio=peluqueria" '<option value="peluqueria" selected>'
PAG_SIMPLE=$(curl -s "$BASE$SIMPLE")
DIA=$(printf '%s' "$PAG_SIMPLE" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | head -1 | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')
SABADO=$(printf '%s' "$PAG_SIMPLE" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}' | while read -r d; do [ "$(date -d "$d" +%u)" = "6" ] && echo "$d"; done | head -1)
DOMINGOS=$(printf '%s' "$PAG_SIMPLE" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}' | while read -r d; do date -d "$d" +%u; done | grep -c '^7$')
[ -n "$DIA" ] && [ "$DOMINGOS" = "0" ] && pasa "días de la reserva sin domingos (primero: $DIA)" || falla "días de la reserva: '$DIA', domingos $DOMINGOS"
R=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" --data-urlencode "servicio=vacuna-gato" --data-urlencode "mascota=Luna" --data-urlencode "especie=gato" --data-urlencode "edad=3 anos" --data-urlencode "dia=$DIA" --data-urlencode "bloque=manana" --data-urlencode "motivo=Control anual" "$BASE$SIMPLE")
case "$R" in "303 https://wa.me/56973537642?text="*Servicio%3A%20Vacuna%20triple%20felina%20%28desde%20%2418.000%29*Mascota%3A%20Luna*Especie%3A%20Gato*Edad%3A%203%20anos*Bloque%3A%20Ma%C3%B1ana*Simple-page%29) pasa "la ficha arma el WhatsApp con servicio, mascota, especie, edad, día, bloque y nota";; *) falla "ficha de reserva: $R";; esac
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "servicio=consulta" --data-urlencode "mascota=" --data-urlencode "especie=perro" --data-urlencode "dia=$DIA" --data-urlencode "bloque=manana" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'Cuéntanos cómo se llama tu mascota.' "$TMP/simple.html" && pasa "sin nombre de mascota: 422 con el error junto al campo" || falla "mascota vacía: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "servicio=../../etc" --data-urlencode "mascota=Luna" --data-urlencode "especie=gato" --data-urlencode "dia=$DIA" --data-urlencode "bloque=manana" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "servicio fuera de la lista no redirige" || falla "servicio inválido: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "servicio=consulta" --data-urlencode "mascota=Luna" --data-urlencode "especie=dragon" --data-urlencode "dia=$DIA" --data-urlencode "bloque=manana" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "especie fuera de la lista no redirige" || falla "especie inválida: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "servicio=consulta" --data-urlencode "mascota=Luna" --data-urlencode "especie=gato" --data-urlencode "dia=2020-01-06" --data-urlencode "bloque=manana" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "día fuera de la lista no redirige" || falla "día inválido: $R"
if [ -n "$SABADO" ]; then
  R=$(curl -s -o "$TMP/sabado.html" -w "%{http_code}" --data-urlencode "servicio=consulta" --data-urlencode "mascota=Luna" --data-urlencode "especie=gato" --data-urlencode "dia=$SABADO" --data-urlencode "bloque=tarde" "$BASE$SIMPLE")
  [ "$R" = "422" ] && grep -qF 'Los sábados atendemos solo en la mañana.' "$TMP/sabado.html" && pasa "sábado en la tarde se rechaza ($SABADO)" || falla "sábado en la tarde aceptado: $R"
else
  falla "no hay sábados en la lista de días"
fi

# --- Multi-page: especialidades con buscador y especie, reserva con datos de la mascota
debe_contener    "${MULTI}especialidades.php" 'data-buscador'
debe_contener    "${MULTI}especialidades.php" 'class="demo-agregar cp-agregar" data-id="vacuna"'
debe_contener    "${MULTI}especialidades.php" '<option value="Gato">Gato · $23.000</option>'
debe_contener    "${MULTI}especialidades.php?q=conejo" 'id="s-conejo" data-buscable'
curl -s "$BASE${MULTI}especialidades.php?q=conejo" | grep -F 'id="s-consulta"' | grep -qF ' hidden>' && pasa "el buscador sin JS oculta lo que no coincide" || falla "el buscador sin JS no oculta"
debe_contener    "${MULTI}equipo.php" 'Dra. Camila Ortiz'
debe_contener    "${MULTI}reserva.php" 'data-carrito-form'
for extra in Mascota Especie Edad 'Peso aprox.' 'Primera visita' Día Bloque Profesional Atención; do
  debe_contener  "${MULTI}reserva.php" "data-carrito-extra=\"$extra\""
done
debe_contener    "${MULTI}reserva.php" 'name="entrega" value="despacho"'
debe_contener    "${MULTI}reserva.php" '<option value="Ñuñoa">Ñuñoa · $9.990</option>'
no_debe_contener "${MULTI}reserva.php" '>Domingo '
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '(Pedido de prueba desde la demo de veterinaria – Multi-page)'
debe_contener    "$MULTI" '"Vacuna anual","variantes":{"Perro":24000,"Gato":23000,"Antirrábica":15000}'
grep -q 'Require all denied' demos/veterinaria/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/veterinaria/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -q 'config|seed|reset' demos/veterinaria/plataforma/.htaccess && pasa 'plataforma/.htaccess niega config, seed y reset' || falla 'plataforma/.htaccess no protege config/seed/reset'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "${MULTI}especialidades.php" "${MULTI}reserva.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# Fotos: todas de images.unsplash.com con w= y q=70
FOTOS=$(for p in "$SIMPLE" "$MULTI" "${MULTI}especialidades.php" "${MULTI}equipo.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE 'src="https://[^"]+"' | grep -v 'cdn.jsdelivr\|cdnjs\|google.com' | grep -vc 'images.unsplash.com/photo-[0-9a-f-]*?w=[0-9]*&amp;q=70&amp;auto=format')
[ "$FOTOS" = "0" ] && pasa "fotos enlazadas desde Unsplash con w y q=70" || falla "$FOTOS fotos con otra fuente o formato"

# --- Plataforma web: base protegida, tienda y clínica, farmacia responsable
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
no_debe_contener "${TIENDA}config.php" 'dueno123'
debe_contener    "$TIENDA" 'Alimento perro adulto raza mediana 15 kg'
debe_contener    "$TIENDA" 'Arena sanitaria aglomerante 10 kg'
debe_contener    "$TIENDA" 'Cama acolchada talla M'
debe_contener    "$TIENDA" 'Vacuna anual del perro'
debe_contener    "$TIENDA" 'Próxima hora en la clínica'
debe_contener    "$TIENDA" '¿Tienes un refugio o un criadero?'
no_debe_contener "$TIENDA" 'pack de 3 sacos'
FARMA=$(curl -s "$BASE${TIENDA}?cat=farmacia" | grep -o 'Requiere evaluación veterinaria' | wc -l)
[ "$FARMA" -ge 3 ] && pasa "cada producto de farmacia dice «Requiere evaluación veterinaria» ($FARMA)" || falla "farmacia sin aviso de evaluación veterinaria ($FARMA)"
debe_contener    "${TIENDA}?q=arena" 'Arena sanitaria aglomerante 10 kg'
no_debe_contener "${TIENDA}?q=arena" 'Vacuna triple felina'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'mayorista@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_contener    "${TIENDA}?r=registro" 'Refugios, fundaciones y criaderos registrados.'
debe_contener    "${TIENDA}?r=registro" '<strong>Refugio o criadero</strong>'
debe_responder   "${TIENDA}?r=no-existe" 404

# --- Flujo con cookies (textos ASCII o ya codificados: Git Bash en Windows no pasa bien UTF-8 a curl.exe)
J1="$TMP/cliente.txt"; J2="$TMP/dueno.txt"; J3="$TMP/refugio.txt"
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

# Primer destacado: Consulta veterinaria ($20.000)
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1")
case "$R" in 303*) pasa "agregar un servicio al carrito ($R)";; *) falla "agregar al carrito: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF '<strong>Consulta veterinaria</strong>' && pasa "el carrito tiene la consulta" || falla "carrito vacío"

# Checkout: día de la hora o del despacho calculado en el servidor (sin domingos ni feriados)
CHECK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
printf '%s' "$CHECK" | grep -qF 'Día de tu hora o del despacho' && pasa "checkout pide el día de la hora o del despacho" || falla "checkout sin selector de día"
printf '%s' "$CHECK" | grep -qF 'Voy a la tienda' && pasa "checkout con «Voy a la tienda»" || falla "checkout sin texto de retiro del rubro"
printf '%s' "$CHECK" | grep -qF 'el nombre, la especie y la edad de tu mascota' && pasa "checkout pide los datos de la mascota en la nota" || falla "checkout sin ayuda de la mascota"
FECHA=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | head -1 | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')
DOMINGOS=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}' | while read -r d; do date -d "$d" +%u; done | grep -c '^7$')
[ -n "$FECHA" ] && [ "$DOMINGOS" = "0" ] && pasa "días ofrecidos sin domingos (primero: $FECHA)" || falla "días ofrecidos: primero '$FECHA', domingos $DOMINGOS"
T=$(printf '%s' "$CHECK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=2020-01-06" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir")
case "$R" in 422*) pasa "un día fuera de la lista se rechaza (422)";; *) falla "día inválido aceptado: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=03:00" --data-urlencode "pago=al_recibir")
case "$R" in 422*) pasa "una hora fuera del horario se rechaza (422)";; *) falla "hora inválida aceptada: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Cerrillos" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir" --data-urlencode "nota=Luna gata 3 anos control a domicilio")
case "$R" in 303*r=pedido*) pasa "checkout agenda la hora ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'HO-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página del pedido muestra $CODIGO" || falla "página del pedido sin código"
printf '%s' "$PAGINA" | grep -qF '¡Todo agendado!' && pasa "título propio del rubro (¡Todo agendado!)" || falla "sin el título del rubro"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "el pedido ofrece enviar resumen por WhatsApp" || falla "pedido sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20veterinaria%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
printf '%s' "$PAGINA" | grep -qF 'a%20las%2010%3A00' && pasa "resumen con el día y la hora" || falla "resumen sin la hora"
printf '%s' "$PAGINA" | grep -qF 'Nota%3A%20Luna%20gata%203%20anos' && pasa "resumen con la nota del cliente (mascota)" || falla "resumen sin la nota del cliente"
# $20.000 + despacho a Cerrillos $3.990 = $23.990
printf '%s' "$PAGINA" | grep -qF '$23.990' && pasa "total con visita a domicilio en Cerrillos (\$23.990)" || falla "total del pedido inesperado"

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
curl -s -b "$J1" "${R#* }" | grep -qF 'Tu pedido y tu hora quedaron agendados' && pasa "mensaje de éxito propio del rubro" || falla "sin mensaje de éxito del rubro"

# Repetir desde el historial
T=$(csrf "$J1" "${TIENDA}?r=cuenta")
PEDIDO_ID=$(curl -s -b "$J1" "$BASE${TIENDA}?r=cuenta" | grep -oE 'name="pedido_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
R=$(post "$J1" "${TIENDA}?r=cuenta" --data-urlencode "csrf=$T" --data-urlencode "accion=repetir" --data-urlencode "pedido_id=$PEDIDO_ID")
case "$R" in 303*r=carrito*) pasa "repetir un pedido vuelve a llenar el carrito";; *) falla "repetir: $R";; esac

# La dueña ve el pedido en su panel
T=$(csrf "$J2" "${TIENDA}?r=ingresar")
R=$(post "$J2" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=dueno@demo.cl" --data-urlencode "clave=dueno123")
case "$R" in 303*r=panel*) pasa "la dueña entra a su panel";; *) falla "login dueña: $R";; esac
PANEL=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=pedidos")
printf '%s' "$PANEL" | grep -qF "$CODIGO" && pasa "la dueña ve $CODIGO en el panel" || falla "la dueña no ve el pedido"
printf '%s' "$PANEL" | grep -qF 'Cliente Prueba' && pasa "el panel muestra al cliente" || falla "el panel no muestra al cliente"
printf '%s' "$PANEL" | grep -qF ' · para el ' && pasa "el panel muestra el día de cada hora o despacho" || falla "el panel sin el día"
RESUMEN=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel")
printf '%s' "$RESUMEN" | grep -qF 'Ventas de hoy' && pasa "panel con ventas del día" || falla "panel sin ventas del día"
printf '%s' "$RESUMEN" | grep -qF 'Stock bajo: Peluquería perro grande (3)' && pasa "panel avisa cupos bajos" || falla "panel sin aviso de cupos bajos"
printf '%s' "$RESUMEN" | grep -qF '1 refugio(s) o criadero(s) esperan aprobación' && pasa "panel avisa refugios por aprobar" || falla "panel sin aviso de refugios"
printf '%s' "$RESUMEN" | grep -qF '>Refugios y criaderos (1)</a>' && pasa "pestaña «Refugios y criaderos» con el nombre del rubro" || falla "pestaña de mayoristas sin el nombre del rubro"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "un cliente no entra al panel (403)" || falla "un cliente entra al panel"

# Estado: nuevo → preparando
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">Preparando' && pasa "el cliente ve su pedido en «Preparando»" || falla "estado del pedido no cambió ($R)"

# Refugio: precios por volumen y packs; pendiente hasta aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
REFUGIO=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$REFUGIO" | grep -qF 'Alimento perro adulto 20 kg (pack de 3 sacos)' && pasa "el refugio ve los packs por volumen" || falla "el refugio no ve los packs"
printf '%s' "$REFUGIO" | grep -qF '<strong>$42.990</strong>' && printf '%s' "$REFUGIO" | grep -qF '<s aria-label="Precio normal">$49.990</s> <span class="t-etiqueta">Precio por volumen</span>' && pasa "el refugio ve precio por volumen (\$42.990 en vez de \$49.990)" || falla "el refugio sin precio por volumen"
J4="$TMP/nuevo-refugio.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Refugio Prueba" --data-urlencode "email=refugio$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Refugio de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'pack de 3 sacos' && falla "refugio pendiente ve los packs" || pasa "refugio pendiente aún no ve los packs"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Refugio de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'pack de 3 sacos' && pasa "la dueña aprueba el refugio y ya ve los packs" || falla "aprobación de refugio no funcionó"

# Panel: crear un producto con una foto de la galería
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Producto de Prueba" --data-urlencode "descripcion=Creado por el test" --data-urlencode "categoria_id=3" --data-urlencode "precio=12345" --data-urlencode "precio_mayorista=10000" --data-urlencode "stock=5" --data-urlencode "foto=1764249453874-46864677b10e" --data-urlencode "visible=1")
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
tools/php.sh demos/veterinaria/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
