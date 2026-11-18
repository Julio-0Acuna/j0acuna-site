#!/usr/bin/env bash
# Pruebas del rubro comida peruana (demos/peruana/): levanta `php -S` con el router y revisa con curl.
# Incluye la reserva por WhatsApp de la Multi-page y el flujo de la Plataforma web con cookies:
# registro → login → carrito → pedido programado para un día → panel del dueño, y cuentas de empresa.
# Uso: bash tests/demos_peruana.sh
cd "$(dirname "$0")/.." || exit 1

PORT=$(( 8112 + ${PUERTO_DESFASE:-0} ))
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/peruana"
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
tools/php.sh demos/peruana/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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
debe_contener    "$RUBRO/" 'Página web para restaurantes peruanos en Santiago'
debe_contener    "$RUBRO/" '"@type":"Service"'
debe_contener    "$RUBRO/" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO/" "href=\"$SIMPLE\""
debe_contener    "$RUBRO/" "href=\"$MULTI\""
debe_contener    "$RUBRO/" "href=\"$TIENDA\""
debe_contener    "$RUBRO/" 'Quiero esta'
debe_contener    "$RUBRO/" 'Perfil de Empresa en Google'
no_debe_contener "$RUBRO/" 'noindex'
for img in simple multi plataforma; do debe_responder "$RUBRO/img/$img.webp" 200; done
[ -f demos/peruana/CREDITOS.md ] && pasa "CREDITOS.md del rubro existe" || falla "falta demos/peruana/CREDITOS.md"

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}carta.php ${MULTI}reservas.php ${MULTI}nosotros.php ${MULTI}contacto.php ${MULTI}pedido.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
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

# --- Simple-page: menú del día (el de hoy destacado), carta con QR sin 3D, promos y pedido por WhatsApp
debe_contener    "$SIMPLE" 'data-demo-qr="#carta"'
debe_contener    "$SIMPLE" 'id="carta"'
debe_contener    "$SIMPLE" 'id="menu-del-dia"'
debe_contener    "$SIMPLE" 'aria-current="date"'
debe_contener    "$SIMPLE" 'qrcode-generator/1.4.4/qrcode.min.js" integrity="sha384-'
debe_contener    "$SIMPLE" '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20comida%20peruana%20%E2%80%93%20Simple-page%29'
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
debe_contener    "$SIMPLE" 'men%C3%BA%20del%20d%C3%ADa'
debe_contener    "$SIMPLE" 'Jueves cevichero'
debe_contener    "$SIMPLE" 'Sour de maracuyá sin alcohol'
debe_contener    "$SIMPLE" 'Lomo saltado'
no_debe_contener "$SIMPLE" 'model-viewer'
no_debe_contener "$SIMPLE" 'demo-3d-btn'
# Hoy (o mañana lunes, si es domingo) aparece en la tarjeta destacada.
# Fechas con la zona horaria de las demos (Git Bash no siempre trae la base de zonas para `date`).
fecha_cl() { tools/php.sh -r 'date_default_timezone_set("America/Santiago"); echo (new DateTimeImmutable($argv[1]))->format($argv[2]);' "$1" "$2"; }
HOY=$(fecha_cl now N)
NOMBRES=(x lunes martes miércoles jueves viernes sábado)
if [ "$HOY" = "7" ]; then ESPERADO='ro-hoy__dia">Mañana, lunes'; else ESPERADO="ro-hoy__dia\">Hoy, ${NOMBRES[$HOY]}"; fi
curl -s "$BASE$SIMPLE" | grep -qF "$ESPERADO" && pasa "el menú del día destaca el de hoy ($ESPERADO)" || falla "el menú del día no destaca $ESPERADO"

# --- Multi-page: carta con QR, buscador, porciones, 3D (model-viewer 4.4.0 con SRI), carrito con extras
debe_contener    "${MULTI}carta.php" 'data-demo-qr="/demos/peruana/multi/carta.php"'
debe_contener    "${MULTI}carta.php" 'class="demo-3d-btn'
debe_contener    "${MULTI}carta.php" 'data-3d-modelo="/demos/_comun/3d/ceviche.glb"'
debe_contener    "${MULTI}carta.php" '<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js" integrity="sha256-GLLnEQNapHBe4oFTClXcIzj/Be7T1wrhbVQQUoKnXtU=" crossorigin="anonymous"></script>'
debe_contener    "${MULTI}carta.php" 'data-buscador'
debe_contener    "${MULTI}carta.php" '<option value="Para compartir">Para compartir · $25.900</option>'
debe_contener    "${MULTI}carta.php?q=nikkei" 'id="plato-ceviche-nikkei" data-buscable'
curl -s "$BASE${MULTI}carta.php?q=nikkei" | grep -F 'id="plato-lomo-saltado"' | grep -qF ' hidden>' && pasa "el buscador sin JS oculta lo que no coincide" || falla "el buscador sin JS no oculta"
debe_contener    "$MULTI" 'data-3d-modelo="/demos/_comun/3d/ceviche.glb"'
debe_contener    "${MULTI}pedido.php" 'data-carrito-form'
debe_contener    "${MULTI}pedido.php" 'data-carrito-extra="Picante"'
debe_contener    "${MULTI}pedido.php" 'data-carrito-extra="Cubiertos"'
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '(Pedido de prueba desde la demo de comida peruana – Multi-page)'
debe_contener    "$MULTI" '"Ceviche Muña","variantes":{"Plato":14900,"Para compartir":25900}'

# Reservas: el servidor arma el mensaje y redirige a WhatsApp; valida día, hora y personas.
SABADO=$(fecha_cl "next saturday" Y-m-d)
LUNES=$(fecha_cl "next monday" Y-m-d)
DOMINGO=$(fecha_cl "next sunday" Y-m-d)
reservar() { curl -s -o "$TMP/reserva.html" -w "%{http_code} %{redirect_url}" "$@" "$BASE${MULTI}reservas.php"; }
R=$(reservar --data-urlencode "fecha=$SABADO" --data-urlencode "hora=20:30" --data-urlencode "personas=4" --data-urlencode "zona=barra" --data-urlencode "ocasion=aniversario" --data-urlencode "nombre=Ana Prueba" --data-urlencode "comentario=Sin cebolla")
case "$R" in "303 https://wa.me/56973537642?text="*Personas%3A%204*Barra%20de%20ceviches*Aniversario*Ana%20Prueba*Multi-page%29) pasa "reserva válida arma el WhatsApp con personas, zona, ocasión y nota";; *) falla "reserva válida: $R";; esac
R=$(reservar --data-urlencode "fecha=$LUNES" --data-urlencode "hora=13:00" --data-urlencode "personas=2" --data-urlencode "zona=salon" --data-urlencode "nombre=Ana")
[ "${R%% *}" = "422" ] && grep -qF 'los lunes descansamos' "$TMP/reserva.html" && pasa "reserva un lunes se rechaza (cerrado)" || falla "reserva un lunes: $R"
R=$(reservar --data-urlencode "fecha=$DOMINGO" --data-urlencode "hora=21:00" --data-urlencode "personas=2" --data-urlencode "zona=salon" --data-urlencode "nombre=Ana")
[ "${R%% *}" = "422" ] && grep -qF 'solo al almuerzo' "$TMP/reserva.html" && pasa "cena el domingo se rechaza" || falla "cena el domingo: $R"
R=$(reservar --data-urlencode "fecha=2020-01-01" --data-urlencode "hora=99:99" --data-urlencode "personas=40" --data-urlencode "zona=x" --data-urlencode "nombre=")
[ "${R%% *}" = "422" ] && pasa "reserva con datos inválidos se rechaza (422)" || falla "reserva inválida: $R"
for campo in fecha hora personas zona nombre; do grep -qF "id=\"err-$campo\"" "$TMP/reserva.html" && pasa "error visible en $campo" || falla "sin error en $campo"; done
debe_contener    "${MULTI}reservas.php?ocasion=degustacion" '<option value="degustacion" selected>'

grep -q 'Require all denied' demos/peruana/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/peruana/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -q 'config|seed|reset' demos/peruana/plataforma/.htaccess && pasa 'plataforma/.htaccess niega config, seed y reset' || falla 'plataforma/.htaccess no protege config/seed/reset'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "$MULTI" "${MULTI}carta.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# Fotos: todas de images.unsplash.com con w= y q=70
FOTOS=$(for p in "$SIMPLE" "$MULTI" "${MULTI}carta.php" "${MULTI}nosotros.php" "${MULTI}reservas.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE 'src="https://[^"]+"' | grep -v 'cdn.jsdelivr\|cdnjs\|google.com' | grep -vc 'images.unsplash.com/photo-[0-9a-f-]*?w=[0-9]*&amp;q=70&amp;auto=format')
[ "$FOTOS" = "0" ] && pasa "fotos enlazadas desde Unsplash con w y q=70" || falla "$FOTOS fotos con otra fuente o formato"

# --- Plataforma web: base protegida, catálogo, 3D, textos de empresa y entrega con fecha
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
debe_contener    "$TIENDA" 'class="demo-3d-btn'
debe_contener    "$TIENDA" 'data-3d-modelo="/demos/_comun/3d/ceviche.glb"'
debe_contener    "$TIENDA" 'data-demo-qr="/demos/peruana/plataforma/#menu"'
debe_contener    "$TIENDA" 'Ceviche clásico'
debe_contener    "$TIENDA" 'Colación ejecutiva'
debe_contener    "$TIENDA" 'Crear cuenta para empresas'
no_debe_contener "$TIENDA" 'Pack 10 colaciones ejecutivas'
debe_contener    "${TIENDA}?q=chaufa" 'Arroz chaufa de pollo'
no_debe_contener "${TIENDA}?q=chaufa" 'Lomo saltado'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'mayorista@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'Entrar como empresa'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_contener    "${TIENDA}?r=registro" 'Oficinas, obras y equipos que piden colaciones o bandejas.'
debe_contener    "${TIENDA}?r=registro" '<strong>Empresa</strong>'
debe_contener    "${TIENDA}?r=registro" '(solo empresas)'
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

# Ceviche clásico ($8.990): el primer producto con "Agregar" es el primer destacado
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=2")
case "$R" in 303*) pasa "agregar 2 ceviches al carrito ($R)";; *) falla "agregar al carrito: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF '<strong>Ceviche clásico</strong>' && pasa "el carrito tiene el producto" || falla "carrito vacío"

# Checkout con día de entrega: el primer día de la lista (lo calcula el servidor)
CHECK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
printf '%s' "$CHECK" | grep -qF '¿Para qué día?' && pasa "checkout pregunta el día (modo fecha)" || falla "checkout sin selector de día"
DIA=$(printf '%s' "$CHECK" | grep -oE '<option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | head -1 | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')
NDIAS=$(printf '%s' "$CHECK" | grep -oE '<option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | wc -l)
[ "$NDIAS" -ge 5 ] && [ "$NDIAS" -le 7 ] && pasa "checkout ofrece $NDIAS días (hasta 6 adelante, sin domingos)" || falla "checkout ofrece $NDIAS días"
for d in $(printf '%s' "$CHECK" | grep -oE '<option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}'); do
  [ "$(fecha_cl "$d" N)" = "7" ] && falla "checkout ofrece un domingo ($d)"
done
HORA=$(printf '%s' "$CHECK" | grep -oE "<option value=\"$DIA\" data-horas=\"[0-9]{2}:[0-9]{2}" | grep -oE '[0-9]{2}:[0-9]{2}$')
[ -n "$HORA" ] || HORA=13:00
T=$(printf '%s' "$CHECK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Quinta Normal" --data-urlencode "fecha_entrega=1999-01-01" --data-urlencode "cuando=13:00" --data-urlencode "pago=al_recibir")
case "$R" in 422*) grep -qF 'id="err-fecha_entrega"' "$TMP/ultima.html" && pasa "un día fuera de la lista se rechaza (422)" || falla "día inválido sin error visible";; *) falla "día inválido aceptado: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Quinta Normal" --data-urlencode "fecha_entrega=$DIA" --data-urlencode "cuando=$HORA" --data-urlencode "pago=al_recibir" --data-urlencode "nota=Tocar el timbre")
case "$R" in 303*r=pedido*) pasa "checkout crea el pedido para el $DIA ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'CC-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página del pedido muestra $CODIGO" || falla "página del pedido sin código"
printf '%s' "$PAGINA" | grep -qF 'Ya prendimos el wok' && pasa "título de pedido propio del rubro" || falla "pedido sin el título del rubro"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "pedido ofrece enviar resumen por WhatsApp" || falla "pedido sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20comida%20peruana%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
printf '%s' "$PAGINA" | grep -qF 'Para%20el%20' && printf '%s' "$PAGINA" | grep -qF "a%20las%20${HORA/:/%3A}" && pasa "resumen de WhatsApp con día y hora de entrega" || falla "resumen sin día y hora"
printf '%s' "$PAGINA" | grep -qF 'Nota%3A%20Tocar%20el%20timbre' && pasa "resumen con la nota del cliente" || falla "resumen sin la nota del cliente"
# $8.990 × 2 + despacho Quinta Normal $1.990 (gratis recién sobre $25.000) = $19.970
printf '%s' "$PAGINA" | grep -qF '$19.970' && pasa "total con despacho a Quinta Normal (\$19.970)" || falla "total del pedido inesperado"

# Pedido mínimo $6.000: una limonada ($2.290) no alcanza
LIMO=$(curl -s -b "$J1" "$BASE${TIENDA}?q=limonada" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$LIMO" --data-urlencode "cantidad=1" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$DIA" --data-urlencode "pago=al_recibir")
case "$R" in 303*r=carrito*) pasa "bajo el mínimo no se crea el pedido";; *) falla "pedido bajo el mínimo: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF 'El pedido mínimo es $6.000' && pasa "el carrito avisa el pedido mínimo" || falla "sin aviso de pedido mínimo"

# Pago de prueba: checkout → pantalla simulada → aprobar
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$DIA" --data-urlencode "pago=prueba")
case "$R" in 303*r=pago*) pasa "pago de prueba lleva a la pantalla simulada";; *) falla "pago de prueba: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=pago" | grep -qF 'PAGO DE PRUEBA' && pasa "pantalla de pago marcada como prueba" || falla "pantalla de pago sin aviso"
T=$(csrf "$J1" "${TIENDA}?r=pago")
R=$(post "$J1" "${TIENDA}?r=pago" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar")
case "$R" in 303*r=pedido*) pasa "aprobar pago de prueba crea el pedido";; *) falla "aprobar pago: $R";; esac
curl -s -b "$J1" "${R#* }" | grep -qF 'Tu pedido quedó agendado' && pasa "mensaje de pago aprobado propio del rubro" || falla "sin mensaje de pago aprobado del rubro"

# Repetir pedido desde el historial
T=$(csrf "$J1" "${TIENDA}?r=cuenta")
CUENTA=$(curl -s -b "$J1" "$BASE${TIENDA}?r=cuenta")
printf '%s' "$CUENTA" | grep -qF 'para el ' && pasa "el historial muestra el día de entrega" || falla "historial sin día de entrega"
PEDIDO_ID=$(printf '%s' "$CUENTA" | grep -oE 'name="pedido_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
R=$(post "$J1" "${TIENDA}?r=cuenta" --data-urlencode "csrf=$T" --data-urlencode "accion=repetir" --data-urlencode "pedido_id=$PEDIDO_ID")
case "$R" in 303*r=carrito*) pasa "repetir pedido vuelve a llenar el carrito";; *) falla "repetir: $R";; esac

# El dueño ve el pedido en su panel, con el día de entrega
T=$(csrf "$J2" "${TIENDA}?r=ingresar")
R=$(post "$J2" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=dueno@demo.cl" --data-urlencode "clave=dueno123")
case "$R" in 303*r=panel*) pasa "dueño entra a su panel";; *) falla "login dueño: $R";; esac
PANEL=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=pedidos")
printf '%s' "$PANEL" | grep -qF "$CODIGO" && pasa "el dueño ve $CODIGO en el panel" || falla "el dueño no ve el pedido"
printf '%s' "$PANEL" | grep -qF 'Cliente Prueba' && pasa "el panel muestra al cliente" || falla "el panel no muestra al cliente"
printf '%s' "$PANEL" | grep -qF ' · para el ' && pasa "el panel muestra el día de entrega" || falla "el panel sin día de entrega"
printf '%s' "$PANEL" | grep -qF 'Constructora Los Aromos (ficticia)' && pasa "el panel muestra el pedido de la empresa de ejemplo" || falla "el panel sin pedido de empresa"
curl -s -b "$J2" "$BASE${TIENDA}?r=panel" | grep -qF 'Ventas de hoy' && pasa "panel con ventas del día" || falla "panel sin ventas del día"
curl -s -b "$J2" "$BASE${TIENDA}?r=panel" | grep -qF 'Stock bajo: Conchitas a la parmesana (6 u.)' && pasa "panel avisa stock bajo" || falla "panel sin aviso de stock bajo"
curl -s -b "$J2" "$BASE${TIENDA}?r=panel" | grep -qF '1 empresa(s) esperan aprobación' && pasa "panel avisa empresas por aprobar" || falla "panel sin aviso de empresas por aprobar"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "un cliente no entra al panel (403)" || falla "un cliente entra al panel"

# Estado: nuevo → preparando
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">Preparando' && pasa "el cliente ve su pedido en «Preparando»" || falla "estado del pedido no cambió ($R)"

# Empresas (mayoristas): precio de colación y productos para oficinas; pendiente hasta aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
EMP=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$EMP" | grep -qF 'Pack 10 colaciones ejecutivas' && pasa "empresa ve el pack de 10 colaciones" || falla "empresa no ve productos para oficinas"
printf '%s' "$EMP" | grep -qF 'Precio empresa' && pasa "empresa ve la etiqueta «Precio empresa»" || falla "empresa sin etiqueta de precio"
printf '%s' "$EMP" | grep -qF '$5.490' && pasa "empresa ve la colación ejecutiva a \$5.490" || falla "empresa sin precio de colación"
printf '%s' "$EMP" | grep -qF 'Cuenta empresa' && pasa "aviso de cuenta empresa en la tienda" || falla "sin aviso de cuenta empresa"
printf '%s' "$EMP" | grep -qF 'Solo empresas' && pasa "sello «Solo empresas» en productos para oficinas" || falla "sin sello de solo empresas"
J4="$TMP/nueva-empresa.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
R=$(post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Oficina Prueba" --data-urlencode "email=emp$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Empresa de Prueba")
curl -s -b "$J4" "$BASE${TIENDA}?r=cuenta" | grep -qF 'Tu cuenta de empresa queda pendiente' && pasa "registro de empresa avisa que queda pendiente" || falla "registro de empresa sin aviso de pendiente ($R)"
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Pack 10 colaciones ejecutivas' && falla "empresa pendiente ve productos para oficinas" || pasa "empresa pendiente aún no ve precios de empresa"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Empresa de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
APROB=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas")
printf '%s' "$APROB" | grep -qF 'Empresa aprobada: ya ve sus precios.' && printf '%s' "$APROB" | grep -qF 'Empresas aprobadas' && pasa "el panel usa «Empresa(s)» en vez de «Mayorista(s)»" || falla "el panel no usa los textos de empresa"
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Pack 10 colaciones ejecutivas' && pasa "el dueño aprueba a la empresa y ya ve sus productos" || falla "aprobación de empresa no funcionó"

# Panel: crear producto (foto de la lista, sin subida de archivos) y validar largo
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Tiradito de Prueba" --data-urlencode "descripcion=Creado por el test" --data-urlencode "categoria_id=1" --data-urlencode "precio=9990" --data-urlencode "precio_mayorista=8490" --data-urlencode "stock=5" --data-urlencode "foto=1562707774-553917f561df" --data-urlencode "modelo3d=ceviche" --data-urlencode "visible=1")
case "$R" in 303*) pasa "el dueño crea un producto";; *) falla "crear producto: $R";; esac
debe_contener "$TIENDA" 'Tiradito de Prueba'
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Foto Ajena" --data-urlencode "categoria_id=1" --data-urlencode "precio=1" --data-urlencode "stock=1" --data-urlencode "foto=https://ejemplo.invalid/x.jpg")
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
tools/php.sh demos/peruana/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
