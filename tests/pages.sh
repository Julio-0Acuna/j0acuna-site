#!/usr/bin/env bash
# Pruebas de páginas: levanta `php -S` con el router y revisa el HTML con curl.
# Uso: bash tests/pages.sh            (datos de prueba)
#      SITE_JSON=data/site.json bash tests/pages.sh   (contenido real)
cd "$(dirname "$0")/.." || exit 1

PORT=8099
BASE="http://127.0.0.1:$PORT"
export PORTFOLIO_SITE_FILE="${SITE_JSON:-tests/fixtures/site.json}"
export PORTFOLIO_SITE_URL="$BASE"

tools/php.sh -S 127.0.0.1:$PORT tools/router.php > /tmp/pages_server.log 2>&1 &
SERVER_PID=$!
trap 'kill $SERVER_PID 2>/dev/null; powershell -NoProfile -Command "Get-NetTCPConnection -LocalPort '"$PORT"' -State Listen -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id \$_.OwningProcess -Force }" >/dev/null 2>&1' EXIT
for _ in 1 2 3 4 5 6 7 8 9 10; do curl -s -o /dev/null "$BASE/" && break; sleep 0.3; done

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
# Cuenta las veces que aparece un texto: debe_contar URL TEXTO MINIMO, debe_contar_exacto URL TEXTO N
contar() { curl -s "$BASE$1" | grep -oF -- "$2" | wc -l | tr -d ' '; }
debe_contar() {
  local n; n=$(contar "$1" "$2")
  [ "$n" -ge "$3" ] && pasa "$1 tiene $n veces $2 (>= $3)" || falla "$1 tiene $n veces $2 (esperado >= $3)"
}
debe_contar_exacto() {
  local n; n=$(contar "$1" "$2")
  [ "$n" -eq "$3" ] && pasa "$1 tiene $n veces $2" || falla "$1 tiene $n veces $2 (esperado $3)"
}

# --- Generales: valen con datos de prueba y con el contenido real
debe_responder  /  200
# Portada C (17/11): los rubros reemplazan a los tres caminos; carruseles propios, sin Bootstrap
debe_contener   /  'id="rubros"'
debe_contener   /  'href="/demos/pizzeria/"'
debe_contener   /  'id="destacados"'
debe_contener   /  'Santiago'
debe_contener   /  'Desarrollador web en Santiago'
debe_contener   /  '"@type":"ProfessionalService"'
debe_contener   /  'href="/servicios"'
debe_contener   /  'href="/proyectos"'
debe_contener   /  'href="/contacto"'
debe_contener   /  'aria-current="page"'
no_debe_contener / 'data-filtro'
no_debe_contener / 'hero-mosaic'
debe_contener   /  'data-rbx'
debe_contener   /  'aria-roledescription="carrusel"'
no_debe_contener / 'bootstrap'
no_debe_contener / 'Todos los proyectos'
no_debe_contener / 'admin/login.php'
debe_responder  /servicios  200
debe_contener   /servicios  'id="preguntas"'
debe_contener   /servicios  '"@type":"FAQPage"'
debe_contener   /servicios  '"@type":"Service"'
debe_contener   /servicios  'Cotizar'
debe_contener   /servicios  'todo Chile'
debe_responder  /proyectos  200
debe_contener   /proyectos  'href="/proyectos/restaurantes"'
debe_contener   /proyectos  '"@type":"BreadcrumbList"'
debe_responder  /proyectos/restaurantes 200
debe_contener   /proyectos/restaurantes '"@type":"BreadcrumbList"'
debe_contener   /proyectos/restaurantes 'href="/proyectos"'
debe_responder  /proyectos/no-existe 404
debe_responder  /contacto  200
debe_contener   /contacto  'Cómo trabajo'
debe_contener   /contacto  '"@type":"ProfessionalService"'
debe_contener   /contacto  'https://wa.me/56973537642'
debe_responder  /llms.txt   200
debe_responder  /robots.txt 200
no_debe_contener / 'href="assets/'
no_debe_contener / 'src="assets/'
debe_contener   /  'https://wa.me/56973537642'
debe_responder  /proyecto/no-existe     404
debe_contener   /proyecto/no-existe     'href="/"'
debe_responder  /data/site.json         403
debe_responder  /restaurantes  200
debe_contener   /restaurantes  'Presencia'
debe_contener   /restaurantes  'Vende directo'
debe_contener   /restaurantes  'id="pruebas"'
debe_contener   /restaurantes  'href="/proyectos"'
debe_contener   /restaurantes  'Página web para restaurantes'
no_debe_contener /restaurantes 'integración directa'
debe_contener   /restaurantes  'og-restaurantes.jpg'
# Rediseño 17/11: servicios en carrusel con tecnologías, mosaicos en proyectos, escalera en restaurantes
debe_contener   /servicios  'data-carrusel'
no_debe_contener /servicios 'bootstrap'
debe_contener   /servicios  'aria-roledescription="carrusel"'
debe_contener   /servicios  'data-pausa'
debe_contener   /servicios  'id="tecnologias"'
debe_contener   /servicios  'Lenguajes y tecnologías que uso'
no_debe_contener /servicios 'class="service-card'
no_debe_contener /proyectos 'class="path-card'
no_debe_contener /proyectos/restaurantes 'class="project-card'
debe_contener   /proyectos/restaurantes '<a class="tile'
no_debe_contener /restaurantes 'class="service-card'
no_debe_contener /restaurantes 'class="project-card'
debe_contener   /restaurantes  'class="stairs"'
debe_contener   /restaurantes  'href="/servicios#'
debe_contener   /restaurantes  'text=Hola%20Julio%2C%20tengo%20un%20local%20y%20quiero%20vender%20m%C3%A1s.'

# Peso de /restaurantes sin contar Bootstrap: HTML + CSS/JS propios + imágenes locales < 300 KB
peso_total() {
  local html total url
  html=$(curl -s "$BASE$1")
  total=${#html}
  for url in $(printf '%s' "$html" | grep -oE '(src|href)="/[^"]+\.(css|js|webp|jpg|png|svg)"' | sed -E 's/^(src|href)="//; s/"$//' | sort -u); do
    total=$((total + $(curl -s "$BASE$url" | wc -c)))
  done
  echo "$total"
}
PESO=$(peso_total /restaurantes)
[ "$PESO" -lt 307200 ] && pasa "/restaurantes pesa $PESO bytes (< 300 KB)" || falla "/restaurantes pesa $PESO bytes (>= 300 KB)"

if [ -z "${SITE_JSON:-}" ]; then
# --- Solo con datos de prueba (tests/fixtures/site.json)
debe_contener   /  'Carnicería «Don Ramón» &amp; Cía'
no_debe_contener / 'Proyecto oculto'
debe_responder  /proyecto/restaurante-a  200
debe_contener   /proyecto/restaurante-a  'Carnicería «Don Ramón» &amp; Cía'
debe_contener   /proyecto/restaurante-a  'text=Hola%20Julio%2C%20vi%20el%20proyecto%20%C2%AB'
debe_contener   /proyecto/restaurante-a  'El problema'
debe_contener   /proyecto/restaurante-a  'Don Ramón</'
no_debe_contener /proyecto/restaurante-a 'href="assets/'
no_debe_contener /proyecto/restaurante-a 'src="assets/'
debe_contener   /proyecto/gestion-b      'Cliente confidencial'
no_debe_contener /proyecto/gestion-b     'El problema'
debe_responder  /proyecto/oculto        404
no_debe_contener /restaurantes 'Apps de delivery'
debe_contener   /restaurantes  'href="/proyecto/restaurante-a"'
no_debe_contener /restaurantes 'Servicio empresas'
no_debe_contener /restaurantes 'href="/demos/'
debe_contar_exacto /servicios 'class="srv__slide' 3
debe_contar_exacto /servicios 'data-srv-tab' 3
debe_responder  /proyectos/ejemplos 404
no_debe_contener /proyectos 'href="/proyectos/ejemplos"'
else
# --- Solo con el contenido real (data/site.json)
debe_responder  /proyecto/ridelsip-cl    200
debe_contener   /proyecto/ridelsip-cl    '"@type":"CreativeWork"'
debe_contener   /proyecto/ridelsip-cl    'href="/proyectos/gestion"'
debe_responder  /proyectos/ia            200
debe_responder  /proyectos/formacion     200
debe_contener   /proyecto/ridelsip-cl    'RIDEL-SIP SpA'
no_debe_contener /proyecto/ridelsip-cl   'src="assets/'
debe_responder  /proyecto/alertaoficial  404
debe_responder  /proyecto/mibusca-brasil 404
no_debe_contener /                       'AlertaOficial'
debe_contener   /restaurantes  'Apps de delivery'
debe_contener   /restaurantes  'Premium'
debe_contener   /restaurantes  'href="/proyecto/sistema-pedidos-multi-app"'
no_debe_contener /restaurantes 'Sistemas a medida'
no_debe_contener /proyecto/intranet-agencia           'MAVT'
no_debe_contener /proyecto/paneles-duenos-restaurantes 'MAVT'
no_debe_contener /proyecto/marca-propia-agencia       'MAVT'
debe_contener   /proyecto/sistema-pedidos-multi-app   'Desarrollado para MAVT'
debe_responder  /sitemap.xml  200
debe_responder  /robots.txt   200
debe_responder  /favicon.svg  200
debe_contener   /restaurantes  'href="/demos/"'
debe_responder  /proyectos/ejemplos 200
debe_contener   /proyectos/ejemplos 'href="/proyecto/ejemplo-pizzeria"'
debe_contener   /proyecto/ejemplo-pizzeria 'href="/demos/pizzeria/"'
debe_contener   /sitemap.xml   'https://j0acuna.site/demos/pizzeria/'
debe_contener   /llms.txt      'https://j0acuna.site/demos/'
debe_contener   /  'href="/proyecto/ridelsip-cl"'
debe_contener   /  'href="/proyecto/intranet-agencia"'
debe_contener   /  'href="/proyecto/saturnus-aura"'
debe_contener   /  'href="/proyecto/suitechile-cl"'
debe_contener   /  'href="/proyecto/obrascivilesmys-cl"'
debe_contener   /  'data-carrusel'
# Servicios: 9 diapositivas, una imagen de referencia por servicio, tecnologías y ejemplo de cada uno
debe_contar_exacto /servicios 'class="srv__slide' 9
debe_contar_exacto /servicios 'data-srv-tab' 9
debe_contar_exacto /servicios 'alt="Ejemplo de referencia:' 9
debe_contar     /servicios  'class="tech-chips"' 14
debe_contener   /servicios  'src="/assets/img/servicios/menu-qr-3d.webp"'
debe_contener   /servicios  'src="/assets/img/servicios/google-perfil.webp"'
debe_contener   /servicios  'href="/demos/pizzeria/plataforma/"'
debe_contener   /servicios  'id="inteligencia-artificial"'
debe_contener   /servicios  'Python'
debe_contener   /servicios  '¿El dominio queda a mi nombre?'
debe_contener   /servicios  'en mi hosting'
debe_responder  /assets/img/servicios/tienda-online.webp 200
# Proyectos: muchos ejemplos con imagen a la vista, en miniatura
debe_contar     /proyectos  'src="/assets/img/mini/projects/' 15
debe_contar_exacto /proyectos/formacion '<a class="tile' 9
debe_contener   /proyectos/ia 'src="/assets/img/mini/projects/saturnus-aura.webp"'
# Restaurantes: las 3 versiones de la pizzería, foto de encabezado y escalera con 4 niveles
debe_contener   /restaurantes  'href="/demos/pizzeria/simple/"'
debe_contener   /restaurantes  'href="/demos/pizzeria/multi/"'
debe_contener   /restaurantes  'href="/demos/pizzeria/plataforma/"'
debe_contener   /restaurantes  'src="/assets/img/mini/demos/pizzeria/img/simple.webp"'
debe_contener   /restaurantes  'Pronto:'
debe_contener   /restaurantes  'images.unsplash.com/photo-1424847651672-bf20a4b0982b'
debe_contar_exacto /restaurantes 'class="stair stair-' 4
debe_contener   /restaurantes  'href="/servicios#menu-qr-3d"'
fi

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
