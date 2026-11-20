#!/usr/bin/env bash
# Pruebas del rubro almacén y minimarket (demos/almacen/): levanta `php -S` con el router y revisa con curl.
# Incluye la lista de la Simple-page (POST → WhatsApp) y el flujo de la Plataforma web con cookies:
# registro → login → carrito → pedido con día de reparto → pedido mínimo → panel del dueño → negocios.
# Uso: bash tests/demos_almacen.sh
cd "$(dirname "$0")/.." || exit 1

PORT=${ALMACEN_PORT:-$(( 8101 + ${PUERTO_DESFASE:-0} ))}
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/almacen"
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
tools/php.sh demos/almacen/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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
debe_contener    "$RUBRO/" 'Página web para almacenes y minimarkets en Santiago'
debe_contener    "$RUBRO/" '"@type":"Service"'
debe_contener    "$RUBRO/" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO/" "href=\"$SIMPLE\""
debe_contener    "$RUBRO/" "href=\"$MULTI\""
debe_contener    "$RUBRO/" "href=\"$TIENDA\""
debe_contener    "$RUBRO/" 'Quiero esta'
debe_contener    "$RUBRO/" 'Perfil de Empresa en Google'
debe_contener    "$RUBRO/" 'href="/blog/tienda-online-para-pymes-chile"'
no_debe_contener "$RUBRO/" 'noindex'
for img in simple multi plataforma; do debe_responder "$RUBRO/img/$img.webp" 200; done
[ -f demos/almacen/CREDITOS.md ] && pasa "CREDITOS.md del rubro existe" || falla "falta demos/almacen/CREDITOS.md"

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}catalogo.php ${MULTI}nosotros.php ${MULTI}preguntas.php ${MULTI}contacto.php ${MULTI}pedido.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
# Páginas con productos en 3D (bloque «Modelos 3D» más abajo); en las demás, nada de model-viewer.
CON_3D=" ${MULTI}catalogo.php $TIENDA "
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" "href=\"$RUBRO/\""
  debe_contener    "$p" '<meta name="robots" content="noindex, follow">'
  debe_contener    "$p" '56973537642'
  no_debe_contener "$p" 'LocalBusiness'
  no_debe_contener "$p" 'GroceryStore'
  no_debe_contener "$p" 'AggregateRating'
  no_debe_contener "$p" 'application/ld+json'
  # Almacén: sin menú QR; 3D solo en las páginas con productos (CON_3D)
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

# --- Simple-page: ofertas del día, lista de la semana (sin JavaScript) y canastas
debe_contener    "$SIMPLE" 'id="ofertas"'
debe_contener    "$SIMPLE" 'id="lista"'
debe_contener    "$SIMPLE" 'id="canastas"'
debe_contener    "$SIMPLE" 'id="delivery"'
debe_contener    "$SIMPLE" 'class="is-hoy" aria-current="date"'
debe_contener    "$SIMPLE" 'Oferta de hoy'
debe_contener    "$SIMPLE" 'name="c[lentejas]" form="qs-form"'
debe_contener    "$SIMPLE" 'data-buscador'
debe_contener    "$SIMPLE" '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20almac%C3%A9n%20%E2%80%93%20Simple-page%29'
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
debe_contener    "${SIMPLE}?agregar=perro" 'name="c[perro]" form="qs-form" inputmode="numeric" min="0" max="20" step="1" placeholder="0" value="1"'
curl -s "$BASE${SIMPLE}?q=lentejas" | grep -F 'id="p-arroz"' | grep -qF ' hidden>' && pasa "el buscador sin JS oculta lo que no coincide" || falla "el buscador sin JS no oculta"
curl -s "$BASE${SIMPLE}?q=lentejas" | grep -F 'id="p-lentejas"' | grep -qF ' hidden>' && falla "el buscador oculta lo que sí coincide" || pasa "el buscador deja visible lo que coincide"
# Lentejas, limpiador y perro no están en ninguna oferta del día: 2 × 2.190 + 1.990 + 7.990 = 14.360 + delivery 1.000
lista() { curl -s -o "$TMP/simple.html" -w "%{http_code} %{redirect_url}" "$@" "$BASE$SIMPLE"; }
R=$(lista --data-urlencode "c[lentejas]=2" --data-urlencode "c[limpiador]=1" --data-urlencode "c[perro]=1" --data-urlencode "c[arroz]=" --data-urlencode "entrega=despacho" --data-urlencode "direccion=Calle de Prueba 123" --data-urlencode "bloque=tarde" --data-urlencode "nombre=Vecina Prueba" --data-urlencode "pago=transferencia" --data-urlencode "otros=1 kg de porotos")
case "$R" in "303 https://wa.me/56973537642?text="*2%20%C3%97%20Lentejas%20%281%20kg%29%20%E2%80%93%20%244.380*Subtotal%3A%20%2414.360*Delivery%20en%20el%20barrio%3A%20%241.000*Total%3A%20%2415.360*Tarde%2C%20de%2017%3A00%20a%2020%3A30*Pago%3A%20Transferencia*Adem%C3%A1s%3A%201%20kg%20de%20porotos*Nombre%3A%20Vecina%20Prueba*Simple-page%29) pasa "la lista arma el WhatsApp con cantidades, total, delivery, bloque, pago y nota";; *) falla "lista de la semana: $R";; esac
R=$(lista --data-urlencode "c[canasta-mes]=1" --data-urlencode "entrega=despacho" --data-urlencode "direccion=Calle de Prueba 123" --data-urlencode "bloque=manana" --data-urlencode "nombre=Vecina" --data-urlencode "pago=efectivo")
case "$R" in 303*Delivery%20en%20el%20barrio%3A%20gratis*Total%3A%20%2445.990*) pasa "delivery gratis sobre \$25.000 (canasta del mes)";; *) falla "delivery gratis: $R";; esac
R=$(lista --data-urlencode "c[lentejas]=1" --data-urlencode "entrega=retiro" --data-urlencode "nombre=Vecina" --data-urlencode "pago=efectivo")
case "$R" in 303*Entrega%3A%20retiro%20en%20el%20almac%C3%A9n*) pasa "retiro en el almacén sin pedido mínimo";; *) falla "retiro: $R";; esac
R=$(lista --data-urlencode "c[lentejas]=2" --data-urlencode "entrega=despacho" --data-urlencode "direccion=Calle de Prueba 123" --data-urlencode "bloque=tarde" --data-urlencode "nombre=Vecina" --data-urlencode "pago=efectivo")
[ "${R%% *}" = "422" ] && grep -qF 'el pedido mínimo es $8.000 (te faltan $3.620)' "$TMP/simple.html" && pasa "delivery bajo el mínimo: 422 con lo que falta" || falla "pedido mínimo: $R"
R=$(lista --data-urlencode "entrega=retiro" --data-urlencode "nombre=Vecina" --data-urlencode "pago=efectivo")
[ "${R%% *}" = "422" ] && grep -qF 'Tu lista está vacía' "$TMP/simple.html" && pasa "lista vacía: 422" || falla "lista vacía: $R"
R=$(lista --data-urlencode "c[../../x]=1" --data-urlencode "entrega=retiro" --data-urlencode "nombre=Vecina" --data-urlencode "pago=efectivo")
[ "${R%% *}" = "422" ] && pasa "producto fuera de la lista no redirige" || falla "producto inválido: $R"
R=$(lista --data-urlencode "c[lentejas]=99" --data-urlencode "entrega=retiro" --data-urlencode "nombre=Vecina" --data-urlencode "pago=efectivo")
[ "${R%% *}" = "422" ] && pasa "cantidad sobre 20 no redirige" || falla "cantidad inválida: $R"
R=$(lista --data-urlencode "c[lentejas]=5" --data-urlencode "entrega=despacho" --data-urlencode "bloque=noche" --data-urlencode "nombre=Vecina" --data-urlencode "pago=efectivo")
[ "${R%% *}" = "422" ] && grep -qF 'Escribe la calle' "$TMP/simple.html" && pasa "delivery sin dirección ni bloque válido: 422 con el error junto al campo" || falla "dirección/bloque inválidos: $R"
R=$(lista --data-urlencode "c[lentejas]=1" --data-urlencode "entrega=retiro" --data-urlencode "nombre=Vecina" --data-urlencode "pago=bitcoin")
[ "${R%% *}" = "422" ] && pasa "medio de pago fuera de la lista no redirige" || falla "pago inválido: $R"

# --- Multi-page: pasillos con buscador, variantes, ofertas y pedido con pedido mínimo
debe_contener    "$MULTI" 'Recorre los pasillos'
debe_contener    "$MULTI" 'Ofertas de la semana'
debe_contener    "${MULTI}catalogo.php" 'data-buscador'
debe_contener    "${MULTI}catalogo.php" 'class="demo-agregar tn-agregar" data-id="arroz"'
debe_contener    "${MULTI}catalogo.php" '<option value="5 kg">5 kg · $6.990</option>'
debe_contener    "${MULTI}catalogo.php?q=hielo" 'id="p-hielo" data-buscable'
curl -s "$BASE${MULTI}catalogo.php?q=hielo" | grep -F 'id="p-arroz"' | grep -qF ' hidden>' && pasa "el buscador del catálogo sin JS oculta lo que no coincide" || falla "el buscador del catálogo sin JS no oculta"
debe_contener    "${MULTI}catalogo.php?cat=bebidas" 'Hielo en cubos'
no_debe_contener "${MULTI}catalogo.php?cat=bebidas" 'id="p-arroz"'
debe_contener    "${MULTI}catalogo.php?cat=../../etc" 'Todos los pasillos'
debe_contener    "${MULTI}pedido.php" 'data-carrito-form'
for extra in 'Cuándo' 'Depto. o casa' 'Si falta algo' 'Pago'; do
  debe_contener  "${MULTI}pedido.php" "data-carrito-extra=\"$extra\""
done
debe_contener    "${MULTI}pedido.php" 'name="entrega" value="despacho"'
debe_contener    "${MULTI}pedido.php" '<option value="Providencia">Providencia · $1.990</option>'
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '(Pedido de prueba desde la demo de almacén – Multi-page)'
debe_contener    "$MULTI" '"minimo":10000'
debe_contener    "$MULTI" '"Bebida cola","variantes":{"1,5 L":1890,"3 L":2790}'
debe_contener    "$MULTI" 'turno.js'
# Pedido mínimo del despacho con el carrito común (carrito.js): aviso en [data-carrito-minimo], sin lógica propia.
debe_contener    "$MULTI" '"minimo_solo_despacho":true'
debe_contener    "$MULTI" '"falta_minimo":"Te faltan {falta} para el despacho (mínimo {minimo}). Agrega algo más o elige retiro en el local."'
debe_contener    "${MULTI}pedido.php" '<p class="tn-minimo" data-carrito-minimo aria-live="polite" hidden></p>'
no_debe_contener "${MULTI}pedido.php" 'data-minimo-barra'
# La barra de avance hacia el mínimo vuelve, dibujada por CSS con --avance (lo escribe carrito.js en [data-carrito-minimo]).
grep -qE '^\.tn-minimo::before \{.*var\(--avance' demos/almacen/multi/estilo.css && pasa "Turno 24 dibuja la barra del mínimo con --avance" || falla "Turno 24 sin barra de avance (--avance)"
! grep -qE 'setCustomValidity|DemoCarrito|data-minimo' demos/almacen/multi/turno.js && pasa "turno.js ya no calcula el pedido mínimo (lo hace carrito.js)" || falla "turno.js sigue con lógica del pedido mínimo"
# Abierto las 24 horas: ['00:00', '24:00'] en el horario (sección Google con «24 horas», no 00:00 – 23:59).
for p in "$MULTI" "${MULTI}contacto.php"; do
  debe_contener    "$p" '<p class="demo-google__estado is-abierto">Abierto las 24 horas</p>'
  debe_contener    "$p" '<dt>Lun a Dom</dt><dd>24 horas</dd>'
  no_debe_contener "$p" '23:59'
done
grep -q 'Require all denied' demos/almacen/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/almacen/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -q 'config|seed|reset' demos/almacen/plataforma/.htaccess && pasa 'plataforma/.htaccess niega config, seed y reset' || falla 'plataforma/.htaccess no protege config/seed/reset'

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "${MULTI}catalogo.php" "${MULTI}pedido.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# Fotos: todas de images.unsplash.com con w= y q=70
FOTOS=$(for p in "$SIMPLE" "$MULTI" "${MULTI}catalogo.php" "${MULTI}nosotros.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE 'src="https://[^"]+"' | grep -v 'cdn.jsdelivr\|cdnjs\|google.com' | grep -vc 'images.unsplash.com/photo-[0-9a-f-]*?w=[0-9]*&amp;q=70&amp;auto=format')
[ "$FOTOS" = "0" ] && pasa "fotos enlazadas desde Unsplash con w y q=70" || falla "$FOTOS fotos con otra fuente o formato"

# --- Plataforma web: base protegida, catálogo, negocios y día de reparto
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
no_debe_contener "${TIENDA}config.php" 'dueno123'
debe_contener    "$TIENDA" 'Canasta familiar de la semana'
debe_contener    "$TIENDA" 'Detergente líquido 3 L'
debe_contener    "$TIENDA" 'Próximo reparto'
debe_contener    "$TIENDA" '¿Compras para un almacén, colegio u oficina?'
no_debe_contener "$TIENDA" 'Arroz grado 1, saco de 25 kg'
debe_contener    "${TIENDA}?q=detergente" 'Detergente líquido 3 L'
no_debe_contener "${TIENDA}?q=detergente" 'Leche entera 1 L'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'mayorista@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_contener    "${TIENDA}?r=registro" 'Almacenes, colegios, jardines y oficinas que compran por caja.'
debe_contener    "${TIENDA}?r=registro" '<strong>Negocio</strong>'
debe_responder   "${TIENDA}?r=no-existe" 404

# --- Flujo con cookies (textos ASCII o ya codificados: Git Bash en Windows no pasa bien UTF-8 a curl.exe)
J1="$TMP/cliente.txt"; J2="$TMP/dueno.txt"; J3="$TMP/negocio.txt"
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

# Primer destacado: Canasta familiar de la semana ($32.990)
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1")
case "$R" in 303*) pasa "agregar la canasta al carrito ($R)";; *) falla "agregar al carrito: $R";; esac
CARRO=$(curl -s -b "$J1" "$BASE${TIENDA}?r=carrito")
printf '%s' "$CARRO" | grep -qF '<strong>Canasta familiar de la semana</strong>' && pasa "el carrito tiene la canasta" || falla "carrito vacío"
printf '%s' "$CARRO" | grep -qF '<p class="t-pequeno t-falta-gratis" style="--avance: 54" data-falta-gratis>Con $27.010 más, el reparto es gratis.</p>' && pasa "el carrito del motor avisa cuánto falta para el reparto gratis (con --avance 54: \$32.990 de \$60.000)" || falla "carrito sin aviso de reparto gratis"
[ "$(printf '%s' "$CARRO" | grep -o 'el reparto es gratis' | wc -l)" = "1" ] && pasa "el aviso del reparto gratis sale una sola vez en el carrito" || falla "aviso del reparto gratis repetido"
printf '%s' "$CARRO" | grep -qF 'bs-minimo' && falla "el layout repite el aviso en el carrito" || pasa "el layout ya no repite el aviso del motor en el carrito"
printf '%s' "$CARRO" | grep -qF 'El reparto se calcula según tu comuna en el siguiente paso (gratis desde $60.000).' && pasa "nota del reparto con el texto del rubro" || falla "nota del reparto sin el texto del rubro"

# Checkout: día de reparto calculado en el servidor (sin domingos ni feriados)
CHECK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
printf '%s' "$CHECK" | grep -qF 'Día de reparto o retiro' && pasa "checkout pide el día de reparto" || falla "checkout sin selector de día"
printf '%s' "$CHECK" | grep -qF 'Pedido mínimo cumplido. Con $27.010 más, el reparto es gratis.' && printf '%s' "$CHECK" | grep -qF 'style="width: 55%"' && pasa "al pagar, barra de avance hacia el reparto gratis (55 %)" || falla "checkout sin barra del reparto gratis"
printf '%s' "$CHECK" | grep -qF 'Retiro en bodega' && printf '%s' "$CHECK" | grep -qF 'Reparto en camión' && pasa "checkout con «Retiro en bodega» y «Reparto en camión»" || falla "checkout sin textos de entrega del rubro"
FECHA=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | head -1 | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')
DOMINGOS=$(printf '%s' "$CHECK" | grep -oE 'option value="[0-9]{4}-[0-9]{2}-[0-9]{2}"' | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}' | while read -r d; do date -d "$d" +%u; done | grep -c '^7$')
HOY=$(tools/php.sh -r 'date_default_timezone_set("America/Santiago"); echo date("Y-m-d");')
[ -n "$FECHA" ] && [ "$DOMINGOS" = "0" ] && [ "$FECHA" != "$HOY" ] && pasa "días ofrecidos desde mañana y sin domingos (primero: $FECHA)" || falla "días ofrecidos: primero '$FECHA', domingos $DOMINGOS"
T=$(printf '%s' "$CHECK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$HOY" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir")
case "$R" in 422*) pasa "reparto para hoy se rechaza (422)";; *) falla "día de hoy aceptado: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=21:00" --data-urlencode "pago=al_recibir")
case "$R" in 422*) pasa "una hora fuera del horario de reparto se rechaza (422)";; *) falla "hora inválida aceptada: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Quilicura" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "cuando=10:00" --data-urlencode "pago=al_recibir" --data-urlencode "nota=Tocar el timbre de la reja")
case "$R" in 303*r=pedido*) pasa "checkout crea el pedido ($R)";; *) falla "checkout: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'BS-[0-9]+' | head -1)
[ -n "$CODIGO" ] && pasa "página del pedido muestra $CODIGO" || falla "página del pedido sin código"
printf '%s' "$PAGINA" | grep -qF '¡Pedido en bodega!' && pasa "título propio del rubro (¡Pedido en bodega!)" || falla "sin el título del rubro"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "pedido ofrece enviar resumen por WhatsApp" || falla "pedido sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20almac%C3%A9n%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
printf '%s' "$PAGINA" | grep -qF 'a%20las%2010%3A00' && pasa "resumen con el día y la hora" || falla "resumen sin la hora"
printf '%s' "$PAGINA" | grep -qF 'Nota%3A%20Tocar%20el%20timbre' && pasa "resumen con la nota del cliente" || falla "resumen sin la nota del cliente"
# $32.990 + reparto a Quilicura $3.990 = $36.980
printf '%s' "$PAGINA" | grep -qF '$36.980' && pasa "total con reparto a Quilicura (\$36.980)" || falla "total del pedido inesperado"

# Pedido mínimo ($15.000): un paquete de fideos no alcanza
FIDEOS=$(curl -s -b "$J1" "$BASE${TIENDA}?q=spaghetti" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$FIDEOS" --data-urlencode "cantidad=1" > /dev/null
CARRO=$(curl -s -b "$J1" "$BASE${TIENDA}?r=carrito")
printf '%s' "$CARRO" | grep -qF 'data-falta-minimo>Te faltan $14.110 para el pedido mínimo de $15.000.</p>' && pasa "el carrito del motor avisa cuánto falta para el pedido mínimo" || falla "carrito sin aviso de pedido mínimo"
[ "$(printf '%s' "$CARRO" | grep -o 'para el pedido mínimo de' | wc -l)" = "1" ] && pasa "el aviso del pedido mínimo sale una sola vez" || falla "aviso del pedido mínimo repetido"
printf '%s' "$CARRO" | grep -qF 'aria-disabled="true" aria-describedby="falta-minimo">Continuar</a>' && pasa "bajo el mínimo el carrito no deja continuar (retiro y reparto)" || falla "bajo el mínimo el carrito deja continuar"
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=al_recibir")
case "$R" in 422*) grep -qF 'role="alert">Te faltan $14.110 para el pedido mínimo de $15.000.</p>' "$TMP/ultima.html" && pasa "un retiro bajo el mínimo: el checkout lo rechaza (422 con el aviso del carrito)" || falla "pedido mínimo: 422 sin el aviso";; *) falla "retiro bajo el mínimo: $R";; esac
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=Renca" --data-urlencode "fecha_entrega=$FECHA" --data-urlencode "pago=al_recibir")
case "$R" in 422*) grep -qF 'Te faltan $14.110 para el pedido mínimo de $15.000.' "$TMP/ultima.html" && pasa "un reparto bajo el mínimo tampoco se crea (422 con el aviso)" || falla "reparto bajo el mínimo: 422 sin el aviso";; *) falla "reparto bajo el mínimo: $R";; esac
T=$(csrf "$J1" "${TIENDA}?r=carrito")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=vaciar" > /dev/null

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
curl -s -b "$J1" "${R#* }" | grep -qF 'Tu pedido ya está en preparación en la bodega' && pasa "mensaje de éxito propio del rubro" || falla "sin mensaje de éxito del rubro"

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
printf '%s' "$PANEL" | grep -qF ' · para el ' && pasa "el panel muestra el día de reparto de cada pedido" || falla "el panel sin el día de reparto"
RESUMEN=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel")
printf '%s' "$RESUMEN" | grep -qF 'Ventas de hoy' && pasa "panel con ventas del día" || falla "panel sin ventas del día"
printf '%s' "$RESUMEN" | grep -qF 'Stock bajo: Desengrasante de cocina 1 L (3)' && pasa "panel avisa stock bajo" || falla "panel sin aviso de stock bajo"
printf '%s' "$RESUMEN" | grep -qF '1 negocio(s) esperan aprobación' && pasa "panel avisa negocios por aprobar" || falla "panel sin aviso de negocios"
printf '%s' "$RESUMEN" | grep -qF '>Negocios (1)</a>' && pasa "pestaña «Negocios» con el nombre del rubro" || falla "pestaña de mayoristas sin el nombre del rubro"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "un cliente no entra al panel (403)" || falla "un cliente entra al panel"

# Estado: nuevo → preparando
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">Preparando' && pasa "el cliente ve su pedido en «Preparando»" || falla "estado del pedido no cambió ($R)"

# Negocio: precios por mayor y productos por caja; pendiente hasta aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
NEGOCIO=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$NEGOCIO" | grep -qF 'Arroz grado 1, saco de 25 kg' && pasa "el negocio ve los productos por caja y saco" || falla "el negocio no ve los productos por caja"
printf '%s' "$NEGOCIO" | grep -qF '<strong>$29.990</strong>' && printf '%s' "$NEGOCIO" | grep -qF '<s aria-label="Precio normal">$32.990</s> <span class="t-etiqueta">Precio por mayor</span>' && pasa "el negocio ve precio por mayor (\$29.990 en vez de \$32.990)" || falla "el negocio sin precio por mayor"
J4="$TMP/nuevo-negocio.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Negocio Prueba" --data-urlencode "email=negocio$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Almacen de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Arroz grado 1, saco de 25 kg' && falla "negocio pendiente ve los productos por caja" || pasa "negocio pendiente aún no ve los productos por caja"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Almacen de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Arroz grado 1, saco de 25 kg' && pasa "el dueño aprueba el negocio y ya ve los productos por caja" || falla "aprobación del negocio no funcionó"

# Panel: crear un producto con una foto de la galería
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Producto de Prueba" --data-urlencode "descripcion=Creado por el test" --data-urlencode "categoria_id=2" --data-urlencode "precio=12345" --data-urlencode "precio_mayorista=10000" --data-urlencode "stock=5" --data-urlencode "foto=1586201375761-83865001e31c" --data-urlencode "visible=1")
case "$R" in 303*) pasa "el dueño crea un producto";; *) falla "crear producto: $R";; esac
curl -s "$BASE$TIENDA" | grep -qF 'Producto de Prueba</h3>' && pasa "el producto nuevo aparece en la tienda" || falla "el producto nuevo no aparece"
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Foto ajena" --data-urlencode "categoria_id=2" --data-urlencode "precio=1" --data-urlencode "stock=1" --data-urlencode "foto=https://ejemplo.invalid/foto.jpg")
case "$R" in 422*) pasa "foto fuera de la galería se rechaza";; *) falla "foto ajena aceptada: $R";; esac
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=$(printf 'x%.0s' $(seq 1 81))" --data-urlencode "categoria_id=2" --data-urlencode "precio=1" --data-urlencode "stock=1")
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
tools/php.sh demos/almacen/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

# --- Modelos 3D (demos/_comun/3d/, ver tests/demos_3d.sh): botón «Ver en 3D» con el estilo del rubro,
# model-viewer 4.4.0 con SRI, menu3d.js y data-3d-objeto="producto" (el visor no habla de «plato»). La Simple-page, sin 3D.
debe_contener    "${MULTI}catalogo.php" 'data-3d-modelo="/demos/_comun/3d/leche.glb"'
debe_contener    "${MULTI}catalogo.php" 'class="demo-3d-btn tn-btn-3d"'
debe_contener    "${MULTI}catalogo.php" 'data-3d-modelo="/demos/_comun/3d/aceite.glb"'
debe_contener    "${MULTI}catalogo.php" 'data-3d-modelo="/demos/_comun/3d/huevos.glb"'
debe_contener    "${MULTI}catalogo.php" 'data-3d-modelo="/demos/_comun/3d/bebida.glb"'
debe_contener    "$TIENDA" 'data-3d-modelo="/demos/_comun/3d/canasta.glb"'
debe_contener    "$TIENDA" 'class="demo-3d-btn t-btn-3d"'
debe_contener    "${MULTI}catalogo.php" '<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js" integrity="sha256-GLLnEQNapHBe4oFTClXcIzj/Be7T1wrhbVQQUoKnXtU=" crossorigin="anonymous"></script>'
debe_contener    "${MULTI}catalogo.php" '/demos/_comun/js/menu3d.js?v='
debe_contener    "${MULTI}catalogo.php" 'data-3d-objeto="producto"'
debe_contener    "$TIENDA" '<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js" integrity="sha256-GLLnEQNapHBe4oFTClXcIzj/Be7T1wrhbVQQUoKnXtU=" crossorigin="anonymous"></script>'
debe_contener    "$TIENDA" '/demos/_comun/js/menu3d.js?v='
debe_contener    "$TIENDA" 'data-3d-objeto="producto"'
for m in $(grep -rhoE "'modelo3?d?' => '[a-z0-9-]+'" "demos/almacen/multi/_datos.php" "demos/almacen/plataforma/seed.php" | sed -E "s/.*=> '([a-z0-9-]+)'/\1/" | sort -u); do
  [ -f "demos/_comun/3d/$m.glb" ] && pasa "$m.glb existe" || falla "falta demos/_comun/3d/$m.glb"
done
no_debe_contener "$SIMPLE" 'model-viewer'
no_debe_contener "$SIMPLE" 'demo-3d-btn'

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
