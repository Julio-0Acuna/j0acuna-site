#!/usr/bin/env bash
# Corre todas las baterías del sitio, una tras otra, y muestra un resumen.
# Uso: bash tests/todas.sh [filtro]   ej. bash tests/todas.sh demos_   (solo las de rubros)
# Con otros agentes probando a la vez: PUERTO_DESFASE=1000 bash tests/todas.sh (todos los puertos +1000).
# Ojo: dos corridas a la vez del MISMO rubro comparten su base SQLite y pueden pisarse.
cd "$(dirname "$0")/.." || exit 1
filtro="${1:-}"
fallas=0
correr() {
    local nombre="$1"; shift
    case "$nombre" in *"$filtro"*) ;; *) return 0 ;; esac
    local salida
    salida=$("$@" 2>&1)
    local ultima
    ultima=$(printf '%s\n' "$salida" | tail -1)
    printf '%-22s %s\n' "$nombre" "$ultima"
    case "$ultima" in *" 0 fallas"*) ;; *) fallas=$((fallas + 1)); printf '%s\n' "$salida" | grep -E 'FALLA|Fatal|Warning' | head -10 | sed 's/^/    /' ;; esac
}
correr unit bash tools/php.sh tests/run.php
correr pages bash tests/pages.sh
correr pages-real env SITE_JSON=data/site.json bash tests/pages.sh
correr blog bash tests/blog.sh
correr contacto bash tests/contacto.sh
correr demos bash tests/demos.sh
for f in tests/demos_*.sh; do correr "$(basename "$f" .sh)" bash "$f"; done
echo
if [ "$fallas" -eq 0 ]; then echo "TODAS OK"; else echo "BATERÍAS CON FALLAS: $fallas"; fi
exit $(( fallas > 0 ))
