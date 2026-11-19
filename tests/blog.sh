#!/usr/bin/env bash
# Pruebas de /blog y /blog/<slug> con artículos de prueba (tests/fixtures/blog).
cd "$(dirname "$0")/.." || exit 1

PORT=$(( 8094 + ${PUERTO_DESFASE:-0} ))
BASE="http://127.0.0.1:$PORT"
export PORTFOLIO_SITE_FILE="tests/fixtures/site.json"
export PORTFOLIO_SITE_URL="$BASE"
export PORTFOLIO_BLOG_DIR="tests/fixtures/blog"

tools/php.sh -S 127.0.0.1:$PORT tools/router.php > /tmp/blog_server.log 2>&1 &
SERVER_PID=$!
trap 'kill $SERVER_PID 2>/dev/null; powershell -NoProfile -Command "Get-NetTCPConnection -LocalPort '"$PORT"' -State Listen -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id \$_.OwningProcess -Force }" >/dev/null 2>&1' EXIT
for _ in 1 2 3 4 5 6 7 8 9 10; do curl -s -o /dev/null "$BASE/" && break; sleep 0.3; done

OK=0; FAIL=0
pasa() { OK=$((OK + 1)); echo "  ok   $*"; }
falla() { FAIL=$((FAIL + 1)); echo "  FALLA $*"; }
debe_responder() { local c; c=$(curl -s -o /dev/null -w "%{http_code}" "$BASE$1"); [ "$c" = "$2" ] && pasa "$1 responde $2" || falla "$1 responde $c (esperado $2)"; }
debe_contener() { curl -s "$BASE$1" | grep -qF -- "$2" && pasa "$1 contiene $2" || falla "$1 no contiene $2"; }
no_debe_contener() { curl -s "$BASE$1" | grep -qF -- "$2" && falla "$1 contiene $2 y no debería" || pasa "$1 no contiene $2"; }

debe_responder  /blog  200
debe_contener   /blog  'href="/blog/segundo-articulo"'
debe_contener   /blog  'href="/blog/primer-articulo"'
no_debe_contener /blog 'borrador-oculto'
debe_contener   /blog  '"@type":"BreadcrumbList"'
debe_contener   /blog  'aria-current="page"'
debe_contener   /blog  '<h1>'
debe_responder  /blog/primer-articulo 200
debe_contener   /blog/primer-articulo '<h1>Página web para restaurantes en Chile</h1>'
debe_contener   /blog/primer-articulo '"@type":"BlogPosting"'
debe_contener   /blog/primer-articulo '"keywords":"página web para restaurantes"'
debe_contener   /blog/primer-articulo '<title>Página web para restaurantes en Chile | Julio Acuña</title>'
debe_contener   /blog/primer-articulo 'name="description" content="Qué debe tener la página web'
debe_contener   /blog/primer-articulo '10 de noviembre de 2026'
debe_contener   /blog/primer-articulo '&lt;script&gt;'
no_debe_contener /blog/primer-articulo '<script>alert'
debe_contener   /blog/primer-articulo 'href="/blog/segundo-articulo"'
debe_contener   /blog/primer-articulo 'https://wa.me/56973537642'
no_debe_contener /blog/primer-articulo 'href="assets/'
debe_responder  /blog/borrador-oculto 404
debe_responder  /blog/no-existe 404
debe_contener   /blog/no-existe 'href="/blog"'

# --- 18/11: temas con filtro sin JS (canonical a /blog), palabras clave, preguntas frecuentes y relacionados
for tema in paginas-web tiendas-y-plataformas restaurantes-y-delivery reservas-y-servicios comercio google-y-seo sistemas-e-ia mi-historia; do
  debe_contener /blog "href=\"/blog?tema=$tema\""
done
debe_contener   /blog  'href="/blog" aria-current="page">Todos'
debe_responder  '/blog?tema=paginas-web' 200
debe_contener   '/blog?tema=paginas-web' 'href="/blog/primer-articulo"'
no_debe_contener '/blog?tema=paginas-web' 'href="/blog/segundo-articulo"'
debe_contener   '/blog?tema=paginas-web' 'href="/blog?tema=paginas-web" aria-current="page"'
debe_contener   '/blog?tema=paginas-web' "<link rel=\"canonical\" href=\"$BASE/blog\">"
debe_contener   '/blog?tema=paginas-web' '<title>Blog: Páginas web'
debe_responder  '/blog?tema=mi-historia' 200
debe_contener   '/blog?tema=mi-historia' 'Pronto publicaré artículos sobre este tema'
no_debe_contener '/blog?tema=mi-historia' 'href="/blog/primer-articulo"'
debe_contener   '/blog?tema=no-existe' 'href="/blog/primer-articulo"'
debe_contener   '/blog?tema=no-existe' 'href="/blog/segundo-articulo"'
debe_contener   '/blog?tema=no-existe' "<link rel=\"canonical\" href=\"$BASE/blog\">"
debe_contener   /blog  'class="blog-chip">Restaurantes y delivery'
debe_contener   /blog/segundo-articulo '"keywords":"menú QR, menú digital, carta QR"'
debe_contener   /blog/segundo-articulo '"articleSection":"Restaurantes y delivery"'
debe_contener   /blog/segundo-articulo '"@type":"FAQPage"'
debe_contener   /blog/segundo-articulo '"name":"¿Funciona sin internet?"'
debe_contener   /blog/segundo-articulo '"text":"Sí, desde el panel. Lee la guía de pedidos."'
no_debe_contener /blog/segundo-articulo '¿Pregunta sin respuesta?"'
debe_contener   /blog/segundo-articulo 'id="preguntas-frecuentes"'
debe_contener   /blog/segundo-articulo 'href="/blog?tema=restaurantes-y-delivery"'
no_debe_contener /blog/primer-articulo '"@type":"FAQPage"'
debe_contener   /blog/primer-articulo 'href="/blog?tema=paginas-web"'
debe_contener   /blog/segundo-articulo 'href="/blog/primer-articulo"'
for ruta in /blog '/blog?tema=paginas-web' '/blog?tema=mi-historia' '/blog?tema=%3Cx%3E' /blog/primer-articulo /blog/segundo-articulo; do
  if curl -s "$BASE$ruta" | grep -qE '(Warning|Notice|Deprecated|Fatal error)(</b>)?: '; then falla "$ruta muestra un aviso de PHP"; else pasa "$ruta sin avisos de PHP"; fi
done
# Artículo sin imagen (segundo-articulo): sin recuadro de iniciales; destacado solo con texto y logo JA en las listas
debe_contener   '/blog?tema=restaurantes-y-delivery' 'class="blog-principal blog-principal--texto'
no_debe_contener '/blog?tema=restaurantes-y-delivery' 'blog-principal-media'
no_debe_contener '/blog?tema=restaurantes-y-delivery' 'tile-placeholder'
debe_contener   /blog  'class="blog-item-media blog-item-media--logo"><svg aria-hidden="true" focusable="false"><use href="#i-logo"/>'
no_debe_contener /blog 'tile-placeholder'
debe_contener   /blog/primer-articulo 'blog-item-media--logo'
no_debe_contener /blog/primer-articulo 'tile-placeholder'
no_debe_contener /blog/segundo-articulo 'articulo-imagen'
debe_contener   /blog/segundo-articulo 'og-inicio.jpg'
debe_responder  '/blog?tema[]=x' 200
no_debe_contener '/blog?tema=%3Cx%3E' '<x>'

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
