#!/usr/bin/env bash
# Pruebas del rubro farmacia (demos/farmacia/): levanta `php -S` con el router y revisa con curl.
# Incluye la reserva de recetas (Simple-page y Multi-page, sin carrito) y el flujo de la Plataforma web con cookies:
# registro → login → carrito → pedido mínimo del despacho → pedido con receta para cotizar → el dueño lo ve en el panel.
# Uso: bash tests/demos_farmacia.sh   (puerto 8118; otro con FARMACIA_PORT=…)
cd "$(dirname "$0")/.." || exit 1

PORT=${FARMACIA_PORT:-$(( 8118 + ${PUERTO_DESFASE:-0} ))}
BASE="http://127.0.0.1:$PORT"
RUBRO="/demos/farmacia"
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
tools/php.sh demos/farmacia/plataforma/reset.php > /dev/null || { echo "FALLA reset.php"; exit 1; }

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
debe_contener    "$RUBRO/" 'Página web para farmacias en Santiago'
debe_contener    "$RUBRO/" '"@type":"Service"'
debe_contener    "$RUBRO/" '"@type":"BreadcrumbList"'
debe_contener    "$RUBRO/" "href=\"$SIMPLE\""
debe_contener    "$RUBRO/" "href=\"$MULTI\""
debe_contener    "$RUBRO/" "href=\"$TIENDA\""
debe_contener    "$RUBRO/" 'Quiero esta'
debe_contener    "$RUBRO/" 'Perfil de Empresa en Google'
no_debe_contener "$RUBRO/" 'noindex'
for img in simple multi plataforma; do debe_responder "$RUBRO/img/$img.webp" 200; done
[ -f demos/farmacia/CREDITOS.md ] && pasa "CREDITOS.md del rubro existe" || falla "falta demos/farmacia/CREDITOS.md"

# --- Reglas comunes a las 3 versiones (y a todas las páginas de la Multi-page)
PAGINAS="$SIMPLE $MULTI ${MULTI}tienda.php ${MULTI}receta.php ${MULTI}farmacia.php ${MULTI}contacto.php ${MULTI}pedido.php $TIENDA ${TIENDA}?r=ingresar ${TIENDA}?r=registro ${TIENDA}?r=carrito"
for p in $PAGINAS; do
  debe_responder   "$p" 200
  debe_contener    "$p" 'Sitio de demostración hecho por <a href="https://j0acuna.site">Julio Acuña</a> · negocio ficticio'
  debe_contener    "$p" "href=\"$RUBRO/\""
  debe_contener    "$p" '<meta name="robots" content="noindex, follow">'
  debe_contener    "$p" '56973537642'
  debe_contener    "$p" 'Consulta a tu químico farmacéutico.'
  debe_contener    "$p" 'Lee las instrucciones antes de usar un medicamento.'
  no_debe_contener "$p" 'LocalBusiness'
  no_debe_contener "$p" '"Pharmacy"'
  no_debe_contener "$p" 'AggregateRating'
  no_debe_contener "$p" 'application/ld+json'
  # Farmacia: sin productos en 3D (pedido de Julio) y sin menú QR
  no_debe_contener "$p" 'model-viewer'
  no_debe_contener "$p" 'demo-3d-btn'
  no_debe_contener "$p" 'menu3d.js'
  no_debe_contener "$p" 'data-demo-qr'
  # Sin marcas comerciales de medicamentos, cosméticos ni cadenas de farmacias
  MARCAS=$(curl -s "$BASE$p" | grep -oiE 'panadol|tapsin|kitadol|aspirina|zyrtec|clarityne|la roche|eucerin|cetaphil|bioderma|vichy|av[eè]ne|isdin|huggies|pampers|babysec|nivea|colgate|cruz verde|salcobrand|ahumada|dr\. simi' | head -3 | tr '\n' ' ')
  [ -z "$MARCAS" ] && pasa "$p sin marcas comerciales" || falla "$p nombra marcas: $MARCAS"
done

# Químico farmacéutico director técnico (nombre inventado) en cada versión
debe_contener    "$SIMPLE" 'Q.F. Hernán Cárcamo Ríos'
debe_contener    "$SIMPLE" 'Químico farmacéutico director técnico'
for p in "$MULTI" "${MULTI}tienda.php" "${MULTI}pedido.php"; do debe_contener "$p" 'Directora técnica: Q.F. Josefina Ureta Lagos'; done
debe_contener    "${MULTI}farmacia.php" 'Química farmacéutica directora técnica'
debe_contener    "$TIENDA" 'Químico farmacéutico director técnico: Q.F. Andrés Pizarro Leiva'
debe_contener    "${TIENDA}?r=carrito" 'Químico farmacéutico director técnico: Q.F. Andrés Pizarro Leiva'

# Sección Google en cada versión
for p in "$SIMPLE" "$MULTI" "${MULTI}contacto.php" "$TIENDA"; do
  debe_contener "$p" 'Encuéntranos en Google'
  debe_contener "$p" 'https://www.google.com/maps?q='
  debe_contener "$p" 'output=embed'
  debe_contener "$p" 'Reseña de ejemplo'
  debe_contener "$p" 'Déjanos tu reseña'
done

# Horario a la vista en cada versión
debe_contener    "$SIMPLE" '<dt>Lun a Vie</dt><dd>09:00 – 21:00</dd>'
debe_contener    "${MULTI}contacto.php" '<dt>Lun a Vie</dt><dd>09:00 – 20:30</dd>'
debe_contener    "$TIENDA" '<dt>Lun a Dom</dt><dd>24 horas</dd>'

# --- Simple-page: cajonera de venta directa, turnos y reserva de recetas (sin JavaScript)
debe_contener    "$SIMPLE" 'id="cajonera"'
debe_contener    "$SIMPLE" 'id="receta"'
debe_contener    "$SIMPLE" 'id="turnos"'
debe_contener    "$SIMPLE" '<span class="em-sello">Venta directa</span>'
debe_contener    "$SIMPLE" '<strong>Paracetamol 500 mg</strong>'
debe_contener    "$SIMPLE" '<span class="em-item__precio">$990</span>'
debe_contener    "$SIMPLE" '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20farmacia%20%E2%80%93%20Simple-page%29'
debe_contener    "$SIMPLE" 'href="tel:+56973537642"'
debe_contener    "$SIMPLE" 'la revisa el químico farmacéutico'
curl -s "$BASE$SIMPLE" | grep -qE 'Próximo turno: del lunes|Esta semana estamos de turno' && pasa "la Simple-page dice el turno de noche (en curso o próximo)" || falla "sin el turno de noche"
debe_contener    "$SIMPLE" '<option value="retenida">Receta retenida</option>'
no_debe_contener "$SIMPLE" 'type="file"'
R=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" --data-urlencode "medicamento=Losartan 50 mg, 30 comprimidos" --data-urlencode "cajas=2" --data-urlencode "tipo=retenida" --data-urlencode "entrega=despacho" --data-urlencode "comuna=San Miguel" --data-urlencode "nombre=Rosa" --data-urlencode "nota=Paso en la tarde" "$BASE$SIMPLE")
case "$R" in "303 https://wa.me/56973537642?text="*Medicamento%3A%20Losartan%2050%20mg%2C%2030%20comprimidos*Cantidad%3A%202%20cajas*Receta%3A%20Receta%20retenida*Entrega%3A%20Despacho%20en%20el%20barrio%20en%20San%20Miguel*Nombre%3A%20Rosa*Nota%3A%20Paso%20en%20la%20tarde*qu%C3%ADmico%20farmac%C3%A9utico*%28Reserva%20de%20prueba%20desde%20la%20demo%20de%20farmacia%20%E2%80%93%20Simple-page%29) pasa "la reserva arma el WhatsApp con medicamento, cajas, receta, entrega, nombre, nota y nota de prueba";; *) falla "reserva de receta: $R";; esac
R=$(curl -s -o "$TMP/simple.html" -w "%{http_code}" --data-urlencode "medicamento=" --data-urlencode "cajas=1" --data-urlencode "tipo=simple" --data-urlencode "entrega=retiro" --data-urlencode "nombre=<script>x</script>" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'Escribe el medicamento como aparece en tu receta.' "$TMP/simple.html" && pasa "sin medicamento: 422 con el error junto al campo" || falla "medicamento vacío: $R"
grep -qF 'value="&lt;script&gt;x&lt;/script&gt;"' "$TMP/simple.html" && ! grep -qF '<script>x</script>' "$TMP/simple.html" && pasa "lo escrito vuelve escapado" || falla "el nombre no se escapa"
R=$(curl -s -o "$TMP/simple2.html" -w "%{http_code}" --data-urlencode "medicamento=Losartan 50 mg" --data-urlencode "cajas=1" --data-urlencode "tipo=simple" --data-urlencode "entrega=despacho" --data-urlencode "comuna=Vitacura" --data-urlencode "nombre=Rosa" "$BASE$SIMPLE")
[ "$R" = "422" ] && grep -qF 'Elige una comuna de la lista.' "$TMP/simple2.html" && pasa "despacho fuera del barrio: 422" || falla "comuna fuera de la lista: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "medicamento=Losartan 50 mg" --data-urlencode "cajas=9" --data-urlencode "tipo=simple" --data-urlencode "entrega=retiro" --data-urlencode "nombre=Rosa" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "más de 6 cajas no redirige" || falla "cajas inválidas: $R"
R=$(curl -s -o /dev/null -w "%{http_code}" --data-urlencode "medicamento=Losartan 50 mg" --data-urlencode "cajas=1" --data-urlencode "tipo=cheque" --data-urlencode "entrega=retiro" --data-urlencode "nombre=Rosa" "$BASE$SIMPLE")
[ "$R" = "422" ] && pasa "tipo de receta fuera de la lista no redirige" || falla "tipo inválido: $R"

# --- Multi-page: tienda con buscador y códigos, recetas aparte y carrito por WhatsApp
debe_contener    "${MULTI}tienda.php" 'data-buscador'
debe_contener    "${MULTI}tienda.php" 'class="demo-agregar nc-agregar" data-id="protector-facial"'
debe_contener    "${MULTI}tienda.php" '<span class="nc-ficha__codigo">NC-301</span>'
debe_contener    "${MULTI}tienda.php" '<option value="400 ml">400 ml · $10.990</option>'
debe_contener    "${MULTI}tienda.php?q=nc301" 'id="p-serum-niacinamida" data-buscable'
curl -s "$BASE${MULTI}tienda.php?q=nc301" | grep -oE '<article[^>]*id="p-agua-micelar"[^>]*>' | grep -qF ' hidden>' && pasa "el buscador sin JS encuentra por código y oculta lo demás" || falla "el buscador por código no filtra"
curl -s "$BASE${MULTI}tienda.php?q=nc301" | grep -oE '<article[^>]*id="p-serum-niacinamida"[^>]*>' | grep -qF ' hidden>' && falla "NC-301 oculto al buscar nc301" || pasa "NC-301 visible al buscar nc301"
FICHAS=$(curl -s "$BASE${MULTI}tienda.php?cat=botiquin" | grep -o 'Lee las instrucciones antes de usar un medicamento.</p>' | wc -l)
[ "$FICHAS" -ge 3 ] && pasa "los medicamentos de venta directa avisan leer las instrucciones ($FICHAS)" || falla "medicamentos sin aviso ($FICHAS)"
debe_contener    "${MULTI}tienda.php?cat=botiquin" '<span class="nc-ficha__sello">Venta directa</span>'
debe_contener    "${MULTI}pedido.php" 'data-carrito-form'
for extra in 'Receta para cotizar' 'Tipo de piel (para muestras)' 'Pago' 'Depto. o referencia'; do
  debe_contener  "${MULTI}pedido.php" "data-carrito-extra=\"$extra\""
done
debe_contener    "${MULTI}pedido.php" 'name="entrega" value="despacho"'
debe_contener    "${MULTI}pedido.php" '<option value="Providencia">Providencia · $2.490</option>'
debe_contener    "${MULTI}pedido.php" 'data-carrito-minimo'
debe_contener    "${MULTI}pedido.php" 'data-carrito-gratis'
debe_contener    "$MULTI" 'id="demo-carrito-config"'
debe_contener    "$MULTI" '(Pedido de prueba desde la demo de farmacia – Multi-page)'
debe_contener    "$MULTI" '"minimo":10000,"minimo_solo_despacho":true,"gratis_desde":35000'
debe_contener    "$MULTI" '"Agua micelar","variantes":{"100 ml":4990,"400 ml":10990}'
debe_contener    "${MULTI}receta.php" '<option value="retenida">Receta retenida</option>'
no_debe_contener "${MULTI}receta.php" 'type="file"'
R=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" --data-urlencode "medicamento=Levotiroxina 50 mcg, 30 comprimidos" --data-urlencode "cajas=1" --data-urlencode "tipo=simple" --data-urlencode "entrega=despacho" --data-urlencode "comuna=Providencia" --data-urlencode "nombre=Antonia" "$BASE${MULTI}receta.php")
case "$R" in "303 https://wa.me/56973537642?text="*cotizar*Medicamento%3A%20Levotiroxina%2050%20mcg*Cantidad%3A%201%20caja%0A*Entrega%3A%20Despacho%20a%20domicilio%20en%20Providencia*%28Cotizaci%C3%B3n%20de%20prueba%20desde%20la%20demo%20de%20farmacia%20%E2%80%93%20Multi-page%29) pasa "la cotización de la Multi-page arma el WhatsApp con la nota de prueba";; *) falla "cotización Multi-page: $R";; esac
R=$(curl -s -o "$TMP/multi.html" -w "%{http_code}" --data-urlencode "medicamento=Le" --data-urlencode "cajas=1" --data-urlencode "tipo=simple" --data-urlencode "entrega=retiro" --data-urlencode "nombre=Antonia" "$BASE${MULTI}receta.php")
[ "$R" = "422" ] && grep -qF 'Escribe el medicamento como aparece en tu receta.' "$TMP/multi.html" && pasa "cotización incompleta: 422 con su error" || falla "cotización incompleta: $R"
grep -q 'Require all denied' demos/farmacia/plataforma/data/.htaccess && pasa 'data/.htaccess niega todo' || falla 'data/.htaccess no niega'
grep -q 'Require all denied' demos/farmacia/plataforma/vistas/.htaccess && pasa 'vistas/.htaccess niega todo' || falla 'vistas/.htaccess no niega'
grep -q 'config|seed|reset' demos/farmacia/plataforma/.htaccess && pasa 'plataforma/.htaccess niega config, seed y reset' || falla 'plataforma/.htaccess no protege config/seed/reset'
grep -qE "modelo3?d?'|modelos3d" demos/farmacia/plataforma/config.php demos/farmacia/plataforma/seed.php demos/farmacia/multi/_datos.php demos/farmacia/simple/_datos.php && falla "el rubro declara modelos 3D" || pasa "sin modelos 3D en datos ni config"

# Sin CDN sin versión: todo <script src=https> lleva integrity
SIN_SRI=$(for p in "$SIMPLE" "${MULTI}tienda.php" "${MULTI}pedido.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE '<script[^>]+src="https://[^"]+"[^>]*>' | grep -vc 'integrity=')
[ "$SIN_SRI" = "0" ] && pasa "scripts de CDN con SRI" || falla "$SIN_SRI scripts de CDN sin SRI"

# Fotos: todas de images.unsplash.com con w= y q=70
FOTOS=$(for p in "$SIMPLE" "$MULTI" "${MULTI}tienda.php" "${MULTI}farmacia.php" "${MULTI}receta.php" "$TIENDA"; do curl -s "$BASE$p"; done | grep -oE 'src="https://[^"]+"' | grep -v 'cdn.jsdelivr\|cdnjs\|google.com' | grep -vc 'images.unsplash.com/photo-[0-9a-f-]*?w=[0-9]*&amp;q=70&amp;auto=format')
[ "$FOTOS" = "0" ] && pasa "fotos enlazadas desde Unsplash con w y q=70" || falla "$FOTOS fotos con otra fuente o formato"

# --- Plataforma web: base protegida, catálogo de venta directa, convenios
debe_responder   "${TIENDA}data/tienda.sqlite" 403
debe_responder   "${TIENDA}reset.php" 404
no_debe_contener "${TIENDA}config.php" 'dueno123'
debe_contener    "$TIENDA" 'Siempre <span>de turno.</span>'
debe_contener    "$TIENDA" 'Hora del turno'
debe_contener    "$TIENDA" '¿Qué necesitas?'
debe_contener    "$TIENDA" 'Paracetamol 500 mg, 16 comprimidos'
debe_contener    "$TIENDA" 'Cód. LU-101'
debe_contener    "$TIENDA" '<span class="t-sello lu-sello-directa">Venta directa</span>'
debe_contener    "$TIENDA" '¿Tu empresa o colegio repone botiquines?'
no_debe_contener "$TIENDA" 'Guantes de nitrilo talla M'
no_debe_contener "$TIENDA" 'Losart'
DIRECTA=$(curl -s "$BASE${TIENDA}?cat=sin-receta" | grep -o '<p class="lu-instrucciones">Lee las instrucciones.</p>' | wc -l)
[ "$DIRECTA" -ge 6 ] && pasa "cada medicamento sin receta avisa leer las instrucciones ($DIRECTA)" || falla "medicamentos sin aviso ($DIRECTA)"
debe_contener    "${TIENDA}?q=lu101" 'Paracetamol 500 mg, 16 comprimidos'
no_debe_contener "${TIENDA}?q=lu101" 'Ibuprofeno 400 mg'
debe_contener    "${TIENDA}?q=termometro" 'Termómetro digital'
debe_contener    "${TIENDA}?r=ingresar" 'cliente@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'mayorista@demo.cl'
debe_contener    "${TIENDA}?r=ingresar" 'dueno@demo.cl'
debe_contener    "${TIENDA}?r=registro" 'No ingreses datos reales; esta demo se borra cada hora.'
debe_contener    "${TIENDA}?r=registro" '<strong>Empresa en convenio</strong>'
debe_contener    "${TIENDA}?r=registro" 'Empresa o institución <small>(solo convenio)</small>'
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

# Primer destacado: Paracetamol 500 mg, 16 comprimidos ($1.090)
INICIO=$(curl -s -b "$J1" -c "$J1" "$BASE$TIENDA")
PID=$(printf '%s' "$INICIO" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$INICIO" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=1")
case "$R" in 303*) pasa "agregar un producto al carrito ($R)";; *) falla "agregar al carrito: $R";; esac
CARRO=$(curl -s -b "$J1" "$BASE${TIENDA}?r=carrito")
printf '%s' "$CARRO" | grep -qF '<strong>Paracetamol 500 mg, 16 comprimidos</strong>' && pasa "el carrito tiene el paracetamol" || falla "carrito vacío"
printf '%s' "$CARRO" | grep -qF 'Para despacho, el pedido mínimo es $6.000: te faltan $4.910. El retiro en el local no tiene mínimo.' && pasa "el carrito avisa el mínimo del despacho (el retiro no tiene)" || falla "carrito sin aviso del mínimo de despacho"

# Checkout: hoy, lo antes posible; medicamento con receta para cotizar (campo del motor); la orden de compra es solo de empresas
CHECK=$(curl -s -b "$J1" -c "$J1" "$BASE${TIENDA}?r=checkout")
printf '%s' "$CHECK" | grep -qF 'A cualquier hora, las 24 horas. Lo dejamos listo en 15 minutos.' && pasa "checkout con el retiro a cualquier hora" || falla "checkout sin el texto de retiro"
printf '%s' "$CHECK" | grep -qE 'Lo antes posible \(despacho en 60 a 90 min\)|Mañana desde las 08:00 \(retiro: cuando quieras\)' && pasa "checkout con «lo antes posible» del rubro" || falla "checkout sin «lo antes posible»"
printf '%s' "$CHECK" | grep -qF 'name="extra[receta]" maxlength="120"' && printf '%s' "$CHECK" | grep -qF 'Medicamento con receta para cotizar <small>(opcional)</small>' && pasa "checkout con el medicamento con receta para cotizar (opcional)" || falla "checkout sin el campo de receta"
printf '%s' "$CHECK" | grep -qF 'name="extra[orden]"' && falla "un particular ve la orden de compra" || pasa "la orden de compra no se le pide a un particular"
T=$(printf '%s' "$CHECK" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=La Florida" --data-urlencode "cuando=" --data-urlencode "pago=al_recibir")
case "$R" in 422*) grep -qF 'Para despacho, el pedido mínimo es $6.000' "$TMP/ultima.html" && pasa "despacho bajo el mínimo: 422 con el aviso" || falla "despacho bajo el mínimo: 422 sin aviso";; *) falla "despacho bajo el mínimo aceptado: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "cuando=03:00" --data-urlencode "pago=al_recibir")
case "$R" in 422*) pasa "una hora fuera del horario de entrega se rechaza (422)";; *) falla "hora inválida aceptada: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "cuando=" --data-urlencode "pago=al_recibir" --data-urlencode "extra[receta]=$(printf 'x%.0s' $(seq 1 121))")
case "$R" in 422*) grep -qF '«Medicamento con receta para cotizar» puede tener hasta 120 caracteres.' "$TMP/ultima.html" && pasa "receta demasiado larga: 422 con su error" || falla "receta larga: 422 sin su error";; *) falla "receta larga aceptada: $R";; esac
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=retiro" --data-urlencode "cuando=" --data-urlencode "pago=al_recibir" --data-urlencode "extra[receta]=Losartan 50 mg, 30 comprimidos" --data-urlencode "nota=Paso de noche")
case "$R" in 303*r=pedido*) pasa "retiro sin mínimo crea el pedido ($R)";; *) falla "checkout retiro: $R";; esac
PEDIDO_URL="${R#* }"
PAGINA=$(curl -s -b "$J1" "$PEDIDO_URL")
CODIGO=$(printf '%s' "$PAGINA" | grep -oE 'LU-[0-9]{4,}' | head -1)
[ -n "$CODIGO" ] && pasa "página del pedido muestra $CODIGO" || falla "página del pedido sin código"
printf '%s' "$PAGINA" | grep -qF '¡Pedido recibido!' && pasa "título propio del rubro" || falla "sin el título del rubro"
printf '%s' "$PAGINA" | grep -qF '<dt>Medicamento con receta para cotizar</dt><dd>Losartan 50 mg, 30 comprimidos</dd>' && pasa "el pedido muestra la receta para cotizar" || falla "el pedido sin la receta para cotizar"
printf '%s' "$PAGINA" | grep -qF 'Enviar resumen por WhatsApp' && pasa "el pedido ofrece enviar resumen por WhatsApp" || falla "pedido sin botón de WhatsApp"
printf '%s' "$PAGINA" | grep -qF '%28Pedido%20de%20prueba%20desde%20la%20demo%20de%20farmacia%20%E2%80%93%20Plataforma%20web%29' && pasa "resumen con la nota de prueba" || falla "resumen sin la nota de prueba"
printf '%s' "$PAGINA" | grep -qF 'Medicamento%20con%20receta%20para%20cotizar%3A%20Losartan%2050%20mg%2C%2030%20comprimidos' && printf '%s' "$PAGINA" | grep -qF 'Nota%3A%20Paso%20de%20noche' && pasa "WhatsApp con la receta para cotizar y la nota" || falla "resumen sin la receta o la nota"
printf '%s' "$PAGINA" | grep -qF '$1.090' && pasa "total del retiro (\$1.090, sin despacho)" || falla "total del retiro inesperado"

# Despacho a La Florida sobre el mínimo: 6 × $1.090 = $6.540 + $2.990 = $9.530
T=$(csrf "$J1" "$TIENDA")
post "$J1" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$PID" --data-urlencode "cantidad=6" > /dev/null
T=$(csrf "$J1" "${TIENDA}?r=checkout")
R=$(post "$J1" "${TIENDA}?r=checkout" --data-urlencode "csrf=$T" --data-urlencode "entrega=despacho" --data-urlencode "direccion_id=0" --data-urlencode "calle=Calle de Prueba 123" --data-urlencode "comuna=La Florida" --data-urlencode "cuando=" --data-urlencode "pago=prueba")
case "$R" in 303*r=pago*) pasa "despacho sobre el mínimo con pago de prueba";; *) falla "despacho con pago de prueba: $R";; esac
curl -s -b "$J1" "$BASE${TIENDA}?r=pago" | grep -qF 'PAGO DE PRUEBA' && pasa "pantalla de pago marcada como prueba" || falla "pantalla de pago sin aviso"
T=$(csrf "$J1" "${TIENDA}?r=pago")
R=$(post "$J1" "${TIENDA}?r=pago" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar")
case "$R" in 303*r=pedido*) pasa "aprobar pago de prueba crea el pedido";; *) falla "aprobar pago: $R";; esac
PAGINA2=$(curl -s -b "$J1" "${R#* }")
printf '%s' "$PAGINA2" | grep -qF 'Ya estamos preparando tu pedido.' && pasa "mensaje de éxito propio del rubro" || falla "sin mensaje de éxito del rubro"
printf '%s' "$PAGINA2" | grep -qF '$9.530' && pasa "total con despacho a La Florida (\$9.530)" || falla "total con despacho inesperado"

# Repetir desde el historial
T=$(csrf "$J1" "${TIENDA}?r=cuenta")
PEDIDO_ID=$(curl -s -b "$J1" "$BASE${TIENDA}?r=cuenta" | grep -oE 'name="pedido_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
R=$(post "$J1" "${TIENDA}?r=cuenta" --data-urlencode "csrf=$T" --data-urlencode "accion=repetir" --data-urlencode "pedido_id=$PEDIDO_ID")
case "$R" in 303*r=carrito*) pasa "repetir un pedido vuelve a llenar el carrito";; *) falla "repetir: $R";; esac

# El dueño ve el pedido en su panel
T=$(csrf "$J2" "${TIENDA}?r=ingresar")
R=$(post "$J2" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=dueno@demo.cl" --data-urlencode "clave=dueno123")
case "$R" in 303*r=panel*) pasa "el dueño entra a su panel";; *) falla "login dueño: $R";; esac
PANEL=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=pedidos")
printf '%s' "$PANEL" | grep -qF "$CODIGO" && pasa "el dueño ve $CODIGO en el panel" || falla "el dueño no ve el pedido"
printf '%s' "$PANEL" | grep -qF 'Cliente Prueba' && pasa "el panel muestra al cliente" || falla "el panel no muestra al cliente"
printf '%s' "$PANEL" | grep -qF 'Medicamento con receta para cotizar: Losartan 50 mg, 30 comprimidos' && pasa "el panel muestra la receta para cotizar" || falla "el panel sin la receta para cotizar"
printf '%s' "$PANEL" | grep -qF 'Orden de compra: OC-2310' && pasa "los pedidos de ejemplo de empresas traen la orden de compra" || falla "pedido de empresa sin orden de compra"
RESUMEN=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel")
printf '%s' "$RESUMEN" | grep -qF 'Panel de la farmacia' && pasa "panel con título del rubro" || falla "panel sin título del rubro"
printf '%s' "$RESUMEN" | grep -qF 'Ventas de hoy' && pasa "panel con ventas del día" || falla "panel sin ventas del día"
printf '%s' "$RESUMEN" | grep -qF 'Stock bajo: Tensiómetro digital de brazo (3)' && pasa "panel avisa stock bajo" || falla "panel sin aviso de stock bajo"
printf '%s' "$RESUMEN" | grep -qF '1 empresa(s) esperan aprobación del convenio' && pasa "panel avisa empresas por aprobar" || falla "panel sin aviso de empresas"
printf '%s' "$RESUMEN" | grep -qF '>Empresas en convenio (1)</a>' && pasa "pestaña «Empresas en convenio» con el nombre del rubro" || falla "pestaña de mayoristas sin el nombre del rubro"
debe_responder "${TIENDA}?r=panel" 303
curl -s -b "$J1" -o /dev/null -w "%{http_code}" "$BASE${TIENDA}?r=panel" | grep -qx 403 && pasa "un cliente no entra al panel (403)" || falla "un cliente entra al panel"

# Estado: nuevo → preparando
ID_NUM=$(printf '%s' "$PEDIDO_URL" | grep -oE 'id=[0-9]+' | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=pedidos")
R=$(post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=avanzar" --data-urlencode "id=$ID_NUM" --data-urlencode "ver=pedidos")
curl -s -b "$J1" "$PEDIDO_URL" | grep -qF 'aria-current="step">Preparando' && pasa "el cliente ve su pedido en «Preparando»" || falla "estado del pedido no cambió ($R)"

# Empresa en convenio: precio convenio, botiquines por caja y orden de compra; pendiente hasta la aprobación
T=$(csrf "$J3" "${TIENDA}?r=ingresar")
post "$J3" "${TIENDA}?r=ingresar" --data-urlencode "csrf=$T" --data-urlencode "email=mayorista@demo.cl" --data-urlencode "clave=mayorista123" > /dev/null
EMPRESA=$(curl -s -b "$J3" "$BASE$TIENDA")
printf '%s' "$EMPRESA" | grep -qF 'Guantes de nitrilo talla M, 10 cajas de 100' && pasa "la empresa ve los insumos por caja" || falla "la empresa no ve los insumos por caja"
printf '%s' "$EMPRESA" | grep -qF '<strong>$890</strong>' && printf '%s' "$EMPRESA" | grep -qF '<s aria-label="Precio normal">$1.090</s> <span class="t-etiqueta">Precio convenio</span>' && pasa "la empresa ve precio convenio (\$890 en vez de \$1.090)" || falla "la empresa sin precio convenio"
printf '%s' "$EMPRESA" | grep -qF 'Convenio activo' && pasa "la empresa ve su convenio activo" || falla "sin aviso de convenio activo"
EPID=$(printf '%s' "$EMPRESA" | grep -oE 'name="producto_id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(printf '%s' "$EMPRESA" | grep -oE 'name="csrf" value="[0-9a-f]+"' | head -1 | sed -E 's/.*value="([0-9a-f]+)"/\1/')
post "$J3" "${TIENDA}?r=carrito" --data-urlencode "csrf=$T" --data-urlencode "accion=agregar" --data-urlencode "producto_id=$EPID" --data-urlencode "cantidad=1" > /dev/null
curl -s -b "$J3" "$BASE${TIENDA}?r=checkout" | grep -qF 'name="extra[orden]"' && pasa "la empresa en convenio ve la orden de compra en el checkout" || falla "la empresa sin orden de compra"
J4="$TMP/nueva-empresa.txt"
T=$(csrf "$J4" "${TIENDA}?r=registro")
post "$J4" "${TIENDA}?r=registro" --data-urlencode "csrf=$T" --data-urlencode "nombre=Empresa Prueba" --data-urlencode "email=empresa$RANDOM$RANDOM@demo.cl" --data-urlencode "clave=prueba1234" --data-urlencode "tipo=mayorista" --data-urlencode "empresa=Jardin de Prueba" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Guantes de nitrilo talla M' && falla "empresa pendiente ve los insumos por caja" || pasa "empresa pendiente aún no ve los insumos por caja"
MID=$(curl -s -b "$J2" "$BASE${TIENDA}?r=panel&ver=mayoristas" | grep -B2 -A6 'Jardin de Prueba' | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
T=$(csrf "$J2" "${TIENDA}?r=panel&ver=mayoristas")
post "$J2" "${TIENDA}?r=panel" --data-urlencode "csrf=$T" --data-urlencode "accion=aprobar" --data-urlencode "id=$MID" --data-urlencode "ver=mayoristas" > /dev/null
curl -s -b "$J4" "$BASE$TIENDA" | grep -qF 'Guantes de nitrilo talla M' && pasa "el dueño aprueba el convenio y la empresa ya ve los insumos por caja" || falla "aprobación del convenio no funcionó"

# Panel: crear un producto con una foto de la galería
T=$(csrf "$J2" "${TIENDA}?r=panel_producto")
R=$(post "$J2" "${TIENDA}?r=panel_producto" --data-urlencode "csrf=$T" --data-urlencode "nombre=Producto de Prueba" --data-urlencode "descripcion=Creado por el test" --data-urlencode "categoria_id=7" --data-urlencode "precio=12345" --data-urlencode "precio_mayorista=10000" --data-urlencode "stock=5" --data-urlencode "codigo=LU-799" --data-urlencode "foto=1609725236589-d987ffc8133a" --data-urlencode "visible=1")
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
tools/php.sh demos/farmacia/plataforma/reset.php > /dev/null

if grep -qiE 'fatal|warning|deprecated|notice' "$TMP/server.log"; then
  falla "el servidor registró errores de PHP:"; grep -iE 'fatal|warning|deprecated|notice' "$TMP/server.log" | head -5
else
  pasa "sin errores de PHP en el registro del servidor"
fi

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
