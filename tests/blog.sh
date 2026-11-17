#!/usr/bin/env bash
# Pruebas de /blog y /blog/<slug> con artículos de prueba (tests/fixtures/blog).
cd "$(dirname "$0")/.." || exit 1

PORT=8094
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

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
