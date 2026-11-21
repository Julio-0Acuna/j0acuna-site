#!/usr/bin/env bash
# Pruebas del rubro comida variada (demos/variada/): levanta `php -S` con el router y revisa con curl.
# Incluye el flujo de la Plataforma web con cookies: registro → login → carrito → pedido con fecha → panel del dueño.
# Uso: bash tests/demos_variada.sh
cd "$(dirname "$0")/.." || exit 1

PORT=$(( 8115 + ${PUERTO_DESFASE:-0} ))
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/variada"
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
tools/php.sh demos/variada/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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
debe_contener    "$RUBRO/" 'Página web para restaurantes de comida casera en Santiago'
debe_contener    "$RUBRO/" '"@type":"Service"'
debe_contener    "$RUBRO/" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO/" "href=\"$SIMPLE\""
debe_contener    "$RUBRO/" "href=\"$MULTI\""
debe_contener    "$RUBRO/" "href=\"$TIENDA\""
debe_contener    "$RUBRO/" 'Quiero esta'
debe_contener    "$RUBRO/" 'Perfil de Empresa en Google'
no_debe_contener "$RUBRO/" 'noindex'
for img in simple multi plataforma; do debe_responder "$RUBRO/img/$img.webp" 200; done
for img in simple multi plataforma; do
  T=$(stat -c %s "demos/variada/img/$img.webp" 2>/dev/null || echo 999999)
  [ "$T" -le 204800 ] && pasa "img/$img.webp pesa $T bytes (≤ 200 KB)" || falla "img/$img.webp pesa $T bytes"
done
[ -f demos/variada/CREDITOS.md ] && pasa "CREDITOS.md del rubro existe" || falla "falta demos/variada/CREDITOS.md"

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}carta.php ${MULTI}plancha.php ${MULTI}preguntas.php ${MULTI}contacto.php ${MULTI}pedido.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
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

# --- Simple-page: menú del día de la semana, carta con QR (sin 3D), oficinas y pedido por WhatsApp
debe_contener    "$SIMPLE" 'data-demo-qr="#carta"'
debe_contener    "$SIMPLE" 'id="carta"'
debe_contener    "$SIMPLE" 'id="menu-del-dia"'
debe_contener    "$SIMPLE" 'qrcode-generator/1.4.4/qrcode.min.js" integrity="sha384-'
debe_contener    "$SIMPLE" '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20comida%20variada%20%E2%80%93%20Simple-page%29'
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
debe_contener    "$SIMPLE" 'La semana en la olla'
for plato in 'Porotos con riendas' 'Cazuela de vacuno' 'Pastel de choclo' 'Charquicán con huevo' 'Reineta frita'; do debe_contener "$SIMPLE" "$plato"; done
debe_contener    "$SIMPLE" 'aria-current="date"'
debe_contener    "$SIMPLE" 'Cotizar para mi oficina'
debe_contener    "$SIMPLE" 'Lomo a lo pobre'
debe_contener    "$SIMPLE" 'Chorrillana para dos'
# El menú destacado es el del día (de lunes a viernes) o el del lunes (fin de semana).
DIA=$(tools/php.sh -r "date_default_timezone_set('America/Santiago'); echo date('N');")
case "$DIA" in 1) HOY='Porotos con riendas';; 2) HOY='Cazuela de vacuno';; 3) HOY='Pastel de choclo';; 4) HOY='Charquicán con huevo';; 5) HOY='Reineta frita';; *) HOY='Porotos con riendas';; esac
curl -s "$BASE$SIMPLE" | grep -F 'id="hoy-titulo"' | grep -qF "$HOY" && pasa "el menú destacado es el de hoy ($HOY)" || falla "el menú destacado no es $HOY"
no_debe_contener "$SIMPLE" 'model-viewer'
no_debe_contener "$SIMPLE" 'demo-3d-btn'
no_debe_contener "$SIMPLE" 'Doble Palta'

# --- Multi-page: carta con QR, buscador, estilos, hamburguesas en 3D (model-viewer 4.4.0 con SRI), comanda
debe_contener    "${MULTI}carta.php" 'data-demo-qr="/demos/variada/multi/carta.php"'
debe_contener    "${MULTI}carta.php" 'class="demo-3d-btn'
debe_contener    "${MULTI}carta.php" 'data-3d-modelo="/demos/_comun/3d/hamburguesa-tocino-cebolla.glb"'
# Cada hamburguesa con 3D tiene su propio modelo: la casera (con papas) es solo de la Plataforma
no_debe_contener "${MULTI}carta.php" 'data-3d-modelo="/demos/_comun/3d/hamburguesa.glb"'
debe_contener    "${MULTI}carta.php" '<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js" integrity="sha256-GLLnEQNapHBe4oFTClXcIzj/Be7T1wrhbVQQUoKnXtU=" crossorigin="anonymous"></script>'
debe_contener    "${MULTI}carta.php" 'data-buscador'
debe_contener    "${MULTI}carta.php" '<option value="Chacarero">Chacarero · $7.790</option>'
debe_contener    "${MULTI}carta.php" 'Completos y as'
debe_contener    "${MULTI}carta.php?q=chacarero" 'id="plato-churrasco" data-buscable'
curl -s "$BASE${MULTI}carta.php?q=chacarero" | grep -F 'id="plato-barros-jarpa"' | grep -qF ' hidden>' && pasa "el buscador sin JS oculta lo que no coincide" || falla "el buscador sin JS no oculta"
curl -s "$BASE${MULTI}carta.php?q=zzzz" | grep -F 'data-sin-resultados' | grep -qvF ' hidden' && pasa "búsqueda sin resultados muestra el aviso" || falla "búsqueda sin resultados sin aviso"
N3D=$(curl -s "$BASE${MULTI}carta.php" | grep -oE 'data-3d-modelo="[^"]+"' | wc -l)
N3D_DISTINTOS=$(curl -s "$BASE${MULTI}carta.php" | grep -oE 'data-3d-modelo="[^"]+"' | sort -u | wc -l)
[ "$N3D" -ge 1 ] && [ "$N3D" -eq "$N3D_DISTINTOS" ] && pasa "$N3D plato(s) con «Ver en 3D», cada uno con su modelo" || falla "$N3D botones 3D y $N3D_DISTINTOS modelos distintos"
debe_contener    "${MULTI}pedido.php" 'data-carrito-form'
debe_contener    "${MULTI}pedido.php" 'data-carrito-extra="Pan"'
debe_contener    "${MULTI}pedido.php" 'data-carrito-extra="Cubiertos y servilletas"'
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '(Pedido de prueba desde la demo de comida variada – Multi-page)'
debe_contener    "$MULTI" '"La Doble Palta","variantes":{"Simple":7990,"Doble carne":9990}'
debe_contener    "$MULTI" 'Elige el estilo'
no_debe_contener "$MULTI" 'Doña Ernestina'
grep -q 'Require all denied' demos/variada/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/variada/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -q 'config|seed|reset' demos/variada/plataforma/.htaccess && pasa 'plataforma/.htaccess niega config, seed y reset' || falla 'plataforma/.htaccess no protege config/seed/reset'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "${MULTI}carta.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# Fotos: todas de images.unsplash.com con w= y q=70
FOTOS=$(for p in "$SIMPLE" "$MULTI" "${MULTI}carta.php" "${MULTI}plancha.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE 'src="https://[^"]+"' | grep -v 'cdn.jsdelivr\|cdnjs\|google.com' | grep -vc 'images.unsplash.com/photo-[0-9a-f-]*?w=[0-9]*&amp;q=70&amp;auto=format')
[ "$FOTOS" = "0" ] && pasa "fotos enlazadas desde Unsplash con w y q=70" || falla "$FOTOS fotos con otra fuente o formato"

# --- Plataforma web: base protegida, menú de la semana, 3D, empresas
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
no_debe_contener "${TIENDA}config.php" 'empresa123'
debe_contener    "$TIENDA" 'class="demo-3d-btn'
debe_contener    "$TIENDA" 'data-3d-modelo="/demos/_comun/3d/hamburguesa.glb"'
debe_contener    "$TIENDA" 'data-demo-qr="/demos/variada/plataforma/#menu"'
debe_contener    "$TIENDA" 'El menú de los próximos días'
debe_contener    "$TIENDA" 'Próximo reparto:'
debe_contener    "$TIENDA" 'Colación casera del día'
debe_contener    "$TIENDA" 'Crear cuenta para empresas'
no_debe_contener "$TIENDA" 'Pack 20 colaciones caseras'
NDIAS=$(curl -s "$BASE$TIENDA" | grep -c 'class="md-dia"')
[ "$NDIAS" = "5" ] && pasa "el calendario muestra 5 días hábiles" || falla "el calendario muestra $NDIAS días"
curl -s "$BASE$TIENDA" | grep -A1 'class="md-dia__fecha"' | grep -qiE '(sáb|dom) [0-9]' && falla "el calendario incluye fin de semana" || pasa "el calendario no incluye sábados ni domingos"
debe_contener    "${TIENDA}?q=pollo" 'Ensalada completa con pollo'
no_debe_contener "${TIENDA}?q=pollo" 'Flan casero'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'empresa@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_contener    "${TIENDA}?r=registro" 'Oficinas, colegios, faenas y equipos de trabajo.'
debe_contener    "${TIENDA}?r=registro" '(solo empresas)'
debe_responder   "${TIENDA}?r=no-existe" 404

# --- Flujo con cookies (textos ASCII o ya codificados: Git Bash en Windows no pasa bien UTF-8 a curl.exe)
J1="$TMP/cliente.txt"; J2="$TMP/dueno.txt"; J3="$TMP/empresa.txt"
csrf() { curl -s -b "$1" -c "$1" "$BASE$2" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/'; }
post() { local jar=$1 url=$2; shift 2; curl -s -o "$TMP/ultima.html" -b "$jar" -c "$jar" -w "%{http_code} %{redirect_url}" "$@" "$BASE$url"; }
pid_de() { grep -B4 -F "aria-label=\"Agregar $2" <<< "$1" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+'; }

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

# Dos colaciones caseras ($5.990 c/u) desde el botón del menú de la semana
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(pid_de "$INICIO" 'Colación casera del día')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=2")
case "$R" in 303*) pasa "agregar 2 colaciones al carrito ($R)";; *) falla "agregar al carrito: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=carrito" | grep -qF 'Colación casera del día' && pasa "el carrito tiene la colación" || falla "carrito vacío"

# Checkout: el día de entrega sale de la lista del servidor (solo días hábiles)
CHECK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
printf '%s' "$CHECK" | grep -qF '¿Para qué día?' && pasa "checkout pide el día de entrega" || falla "checkout sin día de entrega"
printf '%s' "$CHECK" | grep -qF 'A las 12:30' && pasa "checkout ofrece horas de reparto" || falla "checkout sin horas de reparto"
FECHAS=$(printf '%s' "$CHECK" | grep -oE '<option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')
FECHA=$(printf '%s\n' "$FECHAS" | head -1)
FINDE=0; for f in $FECHAS; do d=$(date -d "$f" +%u); [ "$d" -ge 6 ] && FINDE=1; done
[ -n "$FECHA" ] && [ "$FINDE" = "0" ] && pasa "el checkout solo ofrece días hábiles (primero: $FECHA)" || falla "fechas del checkout: $FECHAS"
HOY=$(tools/php.sh -r "date_default_timezone_set('America/Santiago'); echo date('Y-m-d');")
printf '%s\n' "$FECHAS" | grep -qx "$HOY" && falla "el checkout ofrece hoy (pide con un día de anticipación)" || pasa "el checkout no ofrece el mismo día"
T=$(printf '%s' "$CHECK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
SABADO=$(date -d "next saturday" +%F)
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=al_recibir" --data-urlencode "fecha_entrega=$SABADO")
case "$R" in 422*) pasa "un sábado ($SABADO) se rechaza (422)";; *) falla "sábado aceptado: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=al_recibir" --data-urlencode "fecha_entrega=2020-01-02")
case "$R" in 422*) pasa "una fecha pasada se rechaza (422)";; *) falla "fecha pasada aceptada: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Vitacura" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=12:30" --data-urlencode "pago=al_recibir" --data-urlencode "nota=Rotular con nombres")
case "$R" in 303*r=pedido*) pasa "checkout crea el pedido ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'MD-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página del pedido muestra $CODIGO" || falla "página del pedido sin código"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "pedido ofrece enviar resumen por WhatsApp" || falla "pedido sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20comida%20variada%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
printf '%s' "$PAGINA" | grep -qF 'Para%20el%20' && pasa "el resumen de WhatsApp lleva el día de entrega" || falla "el resumen no lleva el día"
printf '%s' "$PAGINA" | grep -qF 'a las 12:30' && pasa "el pedido muestra la hora de entrega (12:30)" || falla "el pedido no muestra la hora"
printf '%s' "$PAGINA" | grep -qF '¡Colación agendada!' && pasa "título del pedido propio del rubro" || falla "sin título propio del pedido"
# $5.990 × 2 + despacho a Vitacura $1.990 (gratis recién sobre $30.000) = $13.970
printf '%s' "$PAGINA" | grep -qF '$13.970' && pasa "total con despacho a Vitacura (\$13.970)" || falla "total del pedido inesperado"

# Pedido mínimo $5.990: un agua ($990) no alcanza
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
AGUA=$(pid_de "$INICIO" 'Agua mineral')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$AGUA" --data-urlencode "cantidad=1" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=al_recibir" --data-urlencode "fecha_entrega=$FECHA")
case "$R" in 422*) pasa "bajo el mínimo no se crea el pedido (422)";; *) falla "pedido bajo el mínimo: $R";; esac
grep -qF 'para el pedido mínimo de $5.990.' "$TMP/ultima.html" && pasa "el checkout avisa el pedido mínimo" || falla "sin aviso de pedido mínimo"

# Pago de prueba: checkout → pantalla simulada → aprobar
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "pago=prueba" --data-urlencode "fecha_entrega=$FECHA")
case "$R" in 303*r=pago*) pasa "pago de prueba lleva a la pantalla simulada";; *) falla "pago de prueba: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=pago" | grep -qF 'PAGO DE PRUEBA' && pasa "pantalla de pago marcada como prueba" || falla "pantalla de pago sin aviso"
T=$(csrf "$J1" "${TIENDA}?r=pago")
R=$(post "$J1" "${TIENDA}?r=pago" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar")
case "$R" in 303*r=pedido*) pasa "aprobar pago de prueba crea el pedido";; *) falla "aprobar pago: $R";; esac
curl -s -b "$J1" "${R#* }" | grep -qF 'Pago de prueba aprobado! Tu pedido quedó agendado.' && pasa "mensaje de éxito propio del rubro" || falla "sin mensaje de éxito propio"

# Pedido recurrente: repetir el pedido desde el historial
CUENTA=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=cuenta")
printf '%s' "$CUENTA" | grep -qF 'para el ' && pasa "el historial muestra el día de entrega" || falla "el historial sin día de entrega"
T=$(printf '%s' "$CUENTA" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
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
printf '%s' "$PANEL" | grep -qF ' a las 12:30' && pasa "el panel muestra día y hora de entrega" || falla "el panel sin día y hora"
curl -s -b "$J2" "$BASE${TIENDA}?r=panel" | grep -qF 'Ventas de hoy' && pasa "panel con ventas del día" || falla "panel sin ventas del día"
curl -s -b "$J2" "$BASE${TIENDA}?r=panel" | grep -qF 'Stock bajo: Crema de zapallo (500 cc)' && pasa "panel avisa stock bajo" || falla "panel sin aviso de stock bajo"
curl -s -b "$J2" "$BASE${TIENDA}?r=panel" | grep -qF 'Empresas' && pasa "el panel nombra a los mayoristas como Empresas" || falla "el panel no usa el texto Empresas"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "un cliente no entra al panel (403)" || falla "un cliente entra al panel"

# Estado: nuevo → preparando
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">Preparando' && pasa "el cliente ve su pedido en «Preparando»" || falla "estado del pedido no cambió ($R)"

# Empresa: precio empresa y packs; una empresa nueva queda pendiente hasta la aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=empresa@demo.cl" --data-urlencode "clave=empresa123" > /dev/null
EMP=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$EMP" | grep -qF 'Pack 20 colaciones caseras' && pasa "la empresa ve los packs para reuniones" || falla "la empresa no ve los packs"
printf '%s' "$EMP" | grep -qF '$4.990</strong>' && pasa "la empresa ve precio empresa (\$4.990)" || falla "la empresa sin precio empresa"
printf '%s' "$EMP" | grep -qF 'Precio empresa' && pasa "etiqueta «Precio empresa»" || falla "sin etiqueta «Precio empresa»"
printf '%s' "$EMP" | grep -qF 'Repetir el pedido de la semana' && pasa "la portada ofrece repetir el pedido a la empresa" || falla "la portada no ofrece repetir el pedido"
J4="$TMP/nueva-empresa.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Oficina Prueba" --data-urlencode "email=emp$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Oficina de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Pack 20 colaciones caseras' && falla "empresa pendiente ve los packs" || pasa "empresa pendiente aún no ve precios ni packs"
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Tu cuenta de empresa está pendiente de aprobación' && pasa "la empresa pendiente ve el aviso" || falla "sin aviso de cuenta pendiente"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Oficina de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Pack 20 colaciones caseras' && pasa "el dueño aprueba la empresa y ya ve los packs" || falla "aprobación de empresa no funcionó"

# Panel: crear un producto con foto de la lista (no hay subida de archivos)
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Lasana de Prueba" --data-urlencode "descripcion=Creada por el test" --data-urlencode "categoria_id=1" --data-urlencode "precio=6500" --data-urlencode "precio_mayorista=5500" --data-urlencode "stock=8" --data-urlencode "foto=1616645258469-ec681c17f3ee" --data-urlencode "visible=1")
case "$R" in 303*) pasa "el dueño crea un producto";; *) falla "crear producto: $R";; esac
curl -s "$BASE$TIENDA" | grep -qF 'Lasana de Prueba</h3>' && pasa "el producto nuevo aparece en la tienda" || falla "el producto nuevo no aparece"
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
tools/php.sh demos/variada/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
