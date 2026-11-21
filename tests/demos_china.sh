#!/usr/bin/env bash
# Pruebas del rubro comida china (demos/china/): levanta `php -S` con el router en el puerto 8109 y revisa con curl.
# Mismo alcance que tests/demos.sh (pizzería), más reglas propias del rubro: carta numerada y menús para 2, 4 y 6
# en la Simple-page, reservas de mesa validadas en el servidor en la Multi-page, y en la Plataforma web pedido
# mínimo, despacho por comuna y cuentas de empresas y eventos (bandejas de cóctel y colaciones).
# Uso: bash tests/demos_china.sh
cd "$(dirname "$0")/.." || exit 1

PORT=$(( 8109 + ${PUERTO_DESFASE:-0} ))
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/china/"
SIMPLE="${RUBRO}simple/"
MULTI="${RUBRO}multi/"
TIENDA="${RUBRO}plataforma/"
NOTA_COD='%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20comida%20china%20%E2%80%93%20'
# Ruta absoluta: el router hace chdir a la carpeta de cada página.
export PORTFOLIO_SITE_FILE="$(pwd)/${SITE_JSON:-tests/fixtures/site.json}"
export PORTFOLIO_SITE_URL="$BASE"
TMP="$(mktemp -d)"

tools/php.sh -S 127.0.0.1:$PORT tools/router.php > "$TMP/server.log" 2>&1 &
SERVER_PID=$!
trap 'kill $SERVER_PID 2>/dev/null; rm -rf "$TMP"; powershell -NoProfile -Command "Get-NetTCPConnection -LocalPort '"$PORT"' -State Listen -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id \$_.OwningProcess -Force }" >/dev/null 2>&1' EXIT
for _ in 1 2 3 4 5 6 7 8 9 10; do curl -s -o /dev/null "$BASE/" && break; sleep 0.3; done

# Base de la tienda recién creada (mismo comando que el cron).
tools/php.sh demos/china/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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

# --- Página del portafolio sobre el rubro (indexable) y listado /demos/
debe_contener    /demos/ 'href="/demos/china/"'
debe_responder   "$RUBRO" 200
debe_contener    "$RUBRO" 'Página web para restaurantes chinos en Santiago'
debe_contener    "$RUBRO" '"@type":"Service"'
debe_contener    "$RUBRO" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO" "href=\"$SIMPLE\""
debe_contener    "$RUBRO" "href=\"$MULTI\""
debe_contener    "$RUBRO" "href=\"$TIENDA\""
debe_contener    "$RUBRO" 'Quiero esta'
debe_contener    "$RUBRO" 'Perfil de Empresa en Google'
no_debe_contener "$RUBRO" 'noindex'
for img in simple multi plataforma; do
  debe_responder "${RUBRO}img/$img.webp" 200
  F="demos/china/img/$img.webp"
  if [ -f "$F" ] && [ "$(wc -c < "$F")" -le 204800 ]; then pasa "$F pesa ≤ 200 KB"; else falla "$F falta o pesa más de 200 KB"; fi
done

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}carta.php ${MULTI}reservas.php ${MULTI}nosotros.php ${MULTI}contacto.php ${MULTI}pedido.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" "href=\"$RUBRO\""
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

# Tres negocios ficticios distintos, cada uno en su comuna
debe_contener "$SIMPLE" 'Farol Rojo'
debe_contener "$SIMPLE" 'La Cisterna'
debe_contener "$MULTI" 'Río Perla'
debe_contener "$MULTI" 'Lo Barnechea'
debe_contener "$TIENDA" 'Wokazo'
debe_contener "$TIENDA" 'Puente Alto'

# Simple-page: carta numerada con QR, sin 3D; menús para 2, 4 y 6; pedido por WhatsApp con la nota
debe_contener    "$SIMPLE" 'data-demo-qr="#menu"'
debe_contener    "$SIMPLE" 'id="menu"'
debe_contener    "$SIMPLE" 'qrcode-generator/1.4.4/qrcode.min.js" integrity="sha384-'
debe_contener    "$SIMPLE" "${NOTA_COD}Simple-page%29"
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
debe_contener    "$SIMPLE" 'Pedir el 14'
debe_contener    "$SIMPLE" 'quiero%20pedir%20el%20N.%C2%BA%2014%3A%20Pollo%20mongoliano'
for m in 2 4 6; do debe_contener "$SIMPLE" "Pedir menú para $m"; done
debe_contener    "$SIMPLE" 'Viernes y sábado, hasta la 1:30'
no_debe_contener "$SIMPLE" 'model-viewer'
no_debe_contener "$SIMPLE" 'demo-3d-btn'

# Multi-page: carta con QR, buscador, 3D del wantán (model-viewer 4.4.0 con SRI), carrito con campos extra
debe_contener    "${MULTI}carta.php" "data-demo-qr=\"${MULTI}carta.php\""
debe_contener    "${MULTI}carta.php" 'class="demo-3d-btn'
debe_contener    "${MULTI}carta.php" 'data-3d-modelo="/demos/_comun/3d/wantan-frito.glb"'
debe_contener    "${MULTI}carta.php" '<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js" integrity="sha256-GLLnEQNapHBe4oFTClXcIzj/Be7T1wrhbVQQUoKnXtU=" crossorigin="anonymous"></script>'
debe_contener    "${MULTI}carta.php" 'data-buscador'
debe_contener    "${MULTI}carta.php" 'Banquete para 6'
debe_contener    "${MULTI}carta.php?q=pato" 'id="plato-pato" data-buscable'
curl -s "$BASE${MULTI}carta.php?q=pato" | grep -oE '<li[^>]*id="plato-pato"[^>]*>' | grep -q ' hidden>' && falla "buscador: «pato» quedó oculto" || pasa "buscador sin JavaScript muestra el pato laqueado"
curl -s "$BASE${MULTI}carta.php?q=pato" | grep -oE '<li[^>]*id="plato-har-gow"[^>]*>' | grep -q ' hidden>' && pasa "buscador sin JavaScript oculta lo que no coincide" || falla "buscador no oculta el har gow"
debe_contener    "${MULTI}pedido.php" 'data-carrito-form'
debe_contener    "${MULTI}pedido.php" 'data-carrito-extra="Salsas"'
debe_contener    "${MULTI}pedido.php" 'data-carrito-extra="Palillos"'
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '(Pedido de prueba desde la demo de comida china – Multi-page)'
debe_contener    "$MULTI" "href=\"${MULTI}reservas.php\""
debe_contener    "${MULTI}contacto.php" '¿Tienen salón privado?'

# Reservas: se validan en el servidor y arman el WhatsApp (no se guarda nada)
DIA=$(date -d "+3 days" +%F); while [ "$(date -d "$DIA" +%u)" = "1" ]; do DIA=$(date -d "$DIA +1 day" +%F); done
LUNES=$(date -d "next monday" +%F)
R=$(curl -s -X POST "$BASE${MULTI}reservas.php" --data-urlencode "fecha=$DIA" --data-urlencode "hora=13:00" --data-urlencode "personas=4" --data-urlencode "zona=salon" --data-urlencode "nombre=Reserva Prueba" --data-urlencode "nota=Silla para bebe")
printf '%s' "$R" | grep -qF 'Enviar reserva por WhatsApp' && pasa "reserva válida arma el WhatsApp" || falla "reserva válida sin botón de WhatsApp"
printf '%s' "$R" | grep -qF 'quiero%20reservar%20una%20mesa%20en%20R%C3%ADo%20Perla' && pasa "mensaje de reserva con el negocio" || falla "mensaje de reserva sin el negocio"
printf '%s' "$R" | grep -qF "Personas%3A%204%0A" && pasa "mensaje de reserva con las personas" || falla "mensaje de reserva sin personas"
printf '%s' "$R" | grep -qF "${NOTA_COD}Multi-page%29" && pasa "reserva con la nota de prueba" || falla "reserva sin la nota de prueba"
R=$(curl -s -X POST "$BASE${MULTI}reservas.php" --data-urlencode "fecha=$LUNES" --data-urlencode "hora=13:00" --data-urlencode "personas=2" --data-urlencode "zona=salon" --data-urlencode "nombre=Prueba")
printf '%s' "$R" | grep -qF 'los lunes no abrimos' && pasa "reserva un lunes se rechaza (cerrado)" || falla "reserva un lunes aceptada"
R=$(curl -s -X POST "$BASE${MULTI}reservas.php" --data-urlencode "fecha=$DIA" --data-urlencode "hora=13:00" --data-urlencode "personas=4" --data-urlencode "zona=privado" --data-urlencode "nombre=Prueba")
printf '%s' "$R" | grep -qF 'El salón privado es para grupos de 8 a 12 personas.' && pasa "salón privado exige 8 a 12 personas" || falla "salón privado aceptado para 4"
R=$(curl -s -X POST "$BASE${MULTI}reservas.php" --data-urlencode "fecha=2020-01-01" --data-urlencode "hora=99:99" --data-urlencode "personas=40" --data-urlencode "zona=terraza" --data-urlencode "ocasion=<script>")
N=$(printf '%s' "$R" | grep -c 'class="rp-error"')
[ "$N" -ge 5 ] && pasa "reserva con datos inválidos muestra $N errores" || falla "reserva inválida: $N errores"
printf '%s' "$R" | grep -qF '<script>' && falla "la reserva devuelve HTML sin escapar" || pasa "la reserva escapa lo que recibe"
printf '%s' "$R" | grep -qF 'Enviar reserva por WhatsApp' && falla "reserva inválida arma el WhatsApp" || pasa "reserva inválida no arma el WhatsApp"

grep -qF 'AddType model/gltf-binary .glb' demos/_comun/3d/.htaccess && pasa '_comun/3d/.htaccess declara .glb' || falla '_comun/3d/.htaccess sin AddType .glb'
grep -q 'Require all denied' demos/china/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/china/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -qE '\(config\|seed\|reset\)' demos/china/plataforma/.htaccess && pasa 'plataforma/.htaccess protege config, seed y reset' || falla 'plataforma/.htaccess no protege config/seed/reset'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "${MULTI}carta.php" "$MULTI" "${MULTI}reservas.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# Fotos: solo Unsplash enlazado (nada descargado) y todas con su crédito en demos/china/CREDITOS.md
FOTOS_LOCALES=$(find demos/china -type f \( -iname '*.jpg' -o -iname '*.jpeg' -o -iname '*.png' \) | wc -l)
[ "$FOTOS_LOCALES" = "0" ] && pasa "sin fotos descargadas en demos/china" || falla "$FOTOS_LOCALES fotos guardadas en demos/china"
SIN_CREDITO=0
for id in $(grep -rhoE '1[0-9]{9,12}-[0-9a-f]{12}' --include='*.php' demos/china | sort -u); do
  grep -qF "$id" demos/china/CREDITOS.md || { SIN_CREDITO=$((SIN_CREDITO + 1)); echo "    sin crédito: $id"; }
done
[ "$SIN_CREDITO" = "0" ] && pasa "todas las fotos tienen crédito" || falla "$SIN_CREDITO fotos sin crédito"

# --- Plataforma web: base protegida, catálogo, 3D, textos de empresas
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
debe_responder   "${TIENDA}config.php" 200
no_debe_contener "${TIENDA}config.php" 'cuentas_prueba'
debe_contener    "$TIENDA" 'class="demo-3d-btn'
debe_contener    "$TIENDA" 'data-3d-modelo="/demos/_comun/3d/wantan-frito.glb"'
debe_contener    "$TIENDA" "data-demo-qr=\"${TIENDA}#menu\""
debe_contener    "$TIENDA" 'Menú para 2'
debe_contener    "$TIENDA" 'Menú para 4'
debe_contener    "$TIENDA" 'Menú para 6'
debe_contener    "$TIENDA" 'Crear cuenta para empresas'
debe_contener    "$TIENDA" '<figcaption>Código QR de la carta</figcaption>'
no_debe_contener "$TIENDA" 'Bandeja cóctel'
debe_contener    "${TIENDA}?q=mongoliano" 'Pollo mongoliano'
no_debe_contener "${TIENDA}?q=mongoliano" 'Té helado'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'mayorista@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_contener    "${TIENDA}?r=registro" '<strong>Empresa</strong>'
debe_contener    "${TIENDA}?r=registro" '(solo empresas)'
debe_responder   "${TIENDA}?r=no-existe" 404

# --- Flujo con cookies (textos ASCII o ya codificados: Git Bash en Windows no pasa bien UTF-8 a curl.exe)
J1="$TMP/cliente.txt"; J2="$TMP/dueno.txt"; J3="$TMP/empresa.txt"
csrf() { curl -s -b "$1" -c "$1" "$BASE$2" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/'; }
post() { local jar=$1 url=$2; shift 2; curl -s -o "$TMP/ultima.html" -b "$jar" -c "$jar" -w "%{http_code} %{redirect_url}" "$@" "$BASE$url"; }
id_de() { curl -s -b "$1" "$BASE${TIENDA}?q=$2" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+'; }

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

# El primer producto de la portada es el Menú para 2 ($18.990)
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=2")
case "$R" in 303*) pasa "agregar al carrito ($R)";; *) falla "agregar al carrito: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF 'Menú para 2' && pasa "el carrito tiene el Menú para 2" || falla "carrito sin el Menú para 2"
curl -s -b "$J1" "$BASE$TIENDA" | grep -qF 'class="wk-comanda"' && pasa "comanda fija «Ver carrito» con productos" || falla "sin comanda del carrito"

CHK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
printf '%s' "$CHK" | grep -qE '>Hoy a las [0-9]{2}:[0-9]{2}</option>|<option value="">Lo antes posible</option>' && pasa "checkout ofrece «Hoy a las …» (modo hoy)" || falla "checkout sin «Hoy a las …»"
printf '%s' "$CHK" | grep -qF '¿Para cuándo lo quieres?' && pasa "checkout con texto propio «¿Para cuándo lo quieres?»" || falla "checkout sin texto propio"
printf '%s' "$CHK" | grep -qF 'name="fecha_entrega"' && falla "el delivery pide fecha de entrega y no debería" || pasa "el delivery no pide fecha de entrega"
# Horas reales del motor que pasan la medianoche: el servicio de cada noche llega hasta su cierre
# (lun a jue 00:30, vie y sáb 02:00, dom 00:00) y a las 00:40 del sábado sigue el del viernes. Cuando al servicio
# de anoche ya no le quedan horas (martes 00:10: cierre 00:30 y margen 30), se ofrece el de hoy completo.
HORAS_CHINA='require "demos/_motor/motor.php"; $c = require "demos/china/plataforma/config.php"; $t = new DateTimeImmutable($argv[1], new DateTimeZone("America/Santiago")); $c["entrega"]["horas"] = wk_horas_servicio($c["negocio"]["horario"], $t); echo implode(",", m_horas_entrega($c, "", $t->getTimestamp()));'
for caso in '2026-11-21 00:40|01:30,02:00' '2026-11-20 23:00|23:30,00:00,00:30,01:00,01:30,02:00' '2026-11-22 22:00|22:30,23:00,23:30,00:00' '2026-11-19 23:50|00:30' '2026-11-17 00:10|12:30,13:00,13:30,14:00,14:30,15:00,15:30,16:00,16:30,17:00,17:30,18:00,18:30,19:00,19:30,20:00,20:30,21:00,21:30,22:00,22:30,23:00,23:30,00:00,00:30'; do
  CUANDO="${caso%%|*}"; ESPERADO="${caso#*|}"
  OBTENIDO=$(tools/php.sh -r "$HORAS_CHINA" "$CUANDO")
  [ "$OBTENIDO" = "$ESPERADO" ] && pasa "horas de delivery a las $CUANDO: «$ESPERADO»" || falla "horas a las $CUANDO: «$OBTENIDO» (esperado «$ESPERADO»)"
done
OBTENIDO=$(tools/php.sh -r "$HORAS_CHINA" '2026-11-18 11:00')
case "$OBTENIDO" in 12:30,13:00,*,23:30,00:00,00:30) pasa "un miércoles se ofrece de 12:30 a 00:30 (pasa la medianoche)";; *) falla "horas del miércoles: $OBTENIDO";; esac
# El checkout de ahora ofrece justo las horas que quedan del servicio en curso (las mismas que valida).
AHORA_HORAS=$(tools/php.sh -r 'require "demos/_motor/motor.php"; $c = m_config(require "demos/china/plataforma/config.php"); echo implode(",", m_horas_entrega($c));')
EN_CHECKOUT=$(printf '%s' "$CHK" | grep -oE '<option value="[0-9]{2}:[0-9]{2}"' | grep -oE '[0-9]{2}:[0-9]{2}' | paste -sd, -)
[ "$EN_CHECKOUT" = "$AHORA_HORAS" ] && pasa "el checkout ofrece las horas del servicio en curso (${EN_CHECKOUT:-solo «Lo antes posible»})" || falla "horas del checkout «$EN_CHECKOUT», esperado «$AHORA_HORAS»"
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "cuando=03:00" --data-urlencode "pago=al_recibir")
case "$R" in 422*) pasa "una hora después del cierre (03:00) se rechaza (422)";; *) falla "hora 03:00 aceptada: $R";; esac
T=$(printf '%s' "$CHK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data "comuna=Maip%C3%BA" --data-urlencode "pago=al_recibir")
case "$R" in 422*) pasa "despacho a una comuna fuera de la lista se rechaza";; *) falla "comuna fuera de la lista: $R";; esac
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Puente Alto" --data-urlencode "pago=al_recibir" --data-urlencode "nota=Sin cebollin")
case "$R" in 303*r=pedido*) pasa "checkout crea el pedido ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'WK-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página del pedido muestra $CODIGO" || falla "página del pedido sin código"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "pedido ofrece enviar resumen por WhatsApp" || falla "pedido sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF "${NOTA_COD}Plataforma%20web%29" && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
printf '%s' "$PAGINA" | grep -qF 'Nota%3A%20Sin%20cebollin' && pasa "resumen de WhatsApp con la nota del cliente" || falla "resumen sin la nota del cliente"
printf '%s' "$PAGINA" | grep -qF '¡Tu pedido ya está en el wok!' && pasa "título propio del pedido" || falla "pedido sin título propio"
# $18.990 × 2 = $37.980 + despacho Puente Alto $0 (gratis sobre $22.000)
printf '%s' "$PAGINA" | grep -qF '$37.980' && pasa "total con despacho gratis sobre \$22.000" || falla "total del pedido inesperado"

# Pedido mínimo $6.000: una bebida sola no alcanza
BEB=$(id_de "$J1" sabores)
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$BEB" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=al_recibir")
case "$R" in 422*) grep -qF 'para el pedido mínimo de $6.000.' "$TMP/ultima.html" && pasa "pedido bajo \$6.000 se rechaza en el checkout (422 con aviso)" || falla "pedido mínimo: 422 sin aviso";; *) falla "pedido bajo el mínimo: $R";; esac

# Despacho con costo: chaulafán de pollo × 2 ($11.980) + La Florida $2.490 = $14.470
T=$(csrf "$J1" "${TIENDA}?r=carrito")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=vaciar" > /dev/null
CHA=$(id_de "$J1" porcion)
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$CHA" --data-urlencode "cantidad=2" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 456" --data-urlencode "comuna=La Florida" --data-urlencode "pago=al_recibir")
case "$R" in 303*r=pedido*) curl -s -b "$J1" "${R#* }" | grep -qF '$14.470' && pasa "despacho a La Florida suma \$2.490 (total \$14.470)" || falla "costo de despacho por comuna inesperado";; *) falla "pedido con despacho: $R";; esac

# Pago de prueba: checkout → pantalla simulada → aprobar
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=prueba")
case "$R" in 303*r=pago*) pasa "pago de prueba lleva a la pantalla simulada";; *) falla "pago de prueba: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=pago" | grep -qF 'PAGO DE PRUEBA' && pasa "pantalla de pago marcada como prueba" || falla "pantalla de pago sin aviso"
T=$(csrf "$J1" "${TIENDA}?r=pago")
R=$(post "$J1" "${TIENDA}?r=pago" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar")
case "$R" in 303*r=pedido*) pasa "aprobar pago de prueba crea el pedido";; *) falla "aprobar pago: $R";; esac
curl -s -b "$J1" "${R#* }" | grep -qF 'Tu pedido ya está en el wok.' && pasa "mensaje de éxito propio (pago de prueba)" || falla "sin mensaje de éxito del pago de prueba"

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
curl -s -b "$J2" "$BASE${TIENDA}?r=panel" | grep -qF 'empresa(s) esperan aprobación' && pasa "panel avisa empresas por aprobar" || falla "panel sin aviso de empresas"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "un cliente no entra al panel (403)" || falla "un cliente entra al panel"

# Estado: nuevo → preparando
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">Preparando' && pasa "el cliente ve su pedido en «Preparando»" || falla "estado del pedido no cambió ($R)"

# Empresas y eventos (mayoristas): bandejas y precio por volumen; pendiente hasta aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
EMP=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$EMP" | grep -qF 'Bandeja cóctel: 50 wantanes' && pasa "empresa ve bandejas para eventos" || falla "empresa no ve bandejas para eventos"
printf '%s' "$EMP" | grep -qF '$16.990' && pasa "empresa ve precio por volumen del Menú para 2 (\$16.990)" || falla "empresa sin precio por volumen"
printf '%s' "$EMP" | grep -qF 'Cuenta empresa' && pasa "aviso de cuenta empresa en la tienda" || falla "sin aviso de cuenta empresa"
printf '%s' "$EMP" | grep -qF 'Precio empresa' && pasa "etiqueta «Precio empresa»" || falla "sin etiqueta «Precio empresa»"
J4="$TMP/nueva-empresa.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Oficina Prueba" --data-urlencode "email=emp$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Empresa de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Bandeja cóctel' && falla "empresa pendiente ve bandejas para eventos" || pasa "empresa pendiente aún no ve bandejas ni precios por volumen"
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Tu cuenta de empresa está pendiente de aprobación' && pasa "aviso de cuenta de empresa pendiente" || falla "sin aviso de cuenta pendiente"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Empresa de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Bandeja cóctel' && pasa "el dueño aprueba a la empresa y ya ve sus bandejas" || falla "aprobación de empresa no funcionó"

# Panel: crear producto (foto de la lista, sin subida de archivos) y validar largo
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Chaulafan de Prueba" --data-urlencode "descripcion=Creado por el test" --data-urlencode "categoria_id=2" --data-urlencode "precio=6990" --data-urlencode "precio_mayorista=5990" --data-urlencode "stock=5" --data-urlencode "foto=1788603907442-253dd51b1aec" --data-urlencode "modelo3d=wantan" --data-urlencode "visible=1")
case "$R" in 303*) pasa "el dueño crea un producto";; *) falla "crear producto: $R";; esac
debe_contener "$TIENDA" 'Chaulafan de Prueba'
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Plato Foto Ajena" --data-urlencode "categoria_id=2" --data-urlencode "precio=1" --data-urlencode "stock=1" --data-urlencode "foto=https://ejemplo.invalid/x.jpg")
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
tools/php.sh demos/china/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
