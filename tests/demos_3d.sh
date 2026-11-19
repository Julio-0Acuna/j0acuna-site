#!/usr/bin/env bash
# Pruebas de los modelos 3D de los ejemplos (demos/_comun/3d/): todo .glb que pide algún rubro existe,
# es un GLB 2 válido (magic glTF, versión 2, largo del encabezado = largo del archivo, chunk JSON primero),
# pesa ≤ 1,5 MB, está en CREDITOS.md y el servidor lo entrega con 200 y model/gltf-binary.
# También revisa que toda página con un botón "Ver en 3D" cargue model-viewer (con SRI) y menu3d.js,
# y que las Simple-page sigan sin 3D.
# Uso: bash tests/demos_3d.sh
cd "$(dirname "$0")/.." || exit 1

PORT=$(( 8116 + ${PUERTO_DESFASE:-0} ))
BASE="http://127.0.0.1:$PORT"
DIR3D="demos/_comun/3d"
MAX_BYTES=$(( 1536 * 1024 ))
export PORTFOLIO_SITE_FILE="$(pwd)/${SITE_JSON:-tests/fixtures/site.json}"
export PORTFOLIO_SITE_URL="$BASE"
TMP="$(mktemp -d)"

tools/php.sh -S 127.0.0.1:$PORT tools/router.php > "$TMP/server.log" 2>&1 &
SERVER_PID=$!
trap 'kill $SERVER_PID 2>/dev/null; rm -rf "$TMP"; powershell -NoProfile -Command "Get-NetTCPConnection -LocalPort '"$PORT"' -State Listen -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id \$_.OwningProcess -Force }" >/dev/null 2>&1' EXIT
for _ in 1 2 3 4 5 6 7 8 9 10; do curl -s -o /dev/null "$BASE/" && break; sleep 0.3; done

OK=0; FAIL=0
pasa() { OK=$((OK + 1)); echo "  ok   $*"; }
falla() { FAIL=$((FAIL + 1)); echo "  FALLA $*"; }

# --- Modelos que pide algún rubro: 'modelo' => 'x' (Multi-page), 'modelo3d' => 'x' (seed) y 'modelos3d' => [...] (config)
grep -rhoE "'modelo3?d?' => '[a-z0-9-]+'" demos/*/multi/*.php demos/*/plataforma/seed.php \
  | sed -E "s/.*=> '([a-z0-9-]+)'/\1/" > "$TMP/pedidos.txt"
grep -rhE "'modelos3d' => \[" demos/*/plataforma/config.php \
  | grep -oE "'[a-z0-9-]+'" | grep -v "^'modelos3d'$" | tr -d "'" >> "$TMP/pedidos.txt"
sort -u "$TMP/pedidos.txt" -o "$TMP/pedidos.txt"
N_PEDIDOS=$(wc -l < "$TMP/pedidos.txt" | tr -d ' ')
[ "$N_PEDIDOS" -ge 30 ] && pasa "los rubros piden $N_PEDIDOS modelos distintos" || falla "solo $N_PEDIDOS modelos pedidos (¿cambió el formato de _datos/seed?)"

# Los 10 platos que pedían los rubros de comida siguen en la lista
for m in pizza sushi torta pan carne wantan ceviche hamburguesa helado calzone; do
  grep -qx "$m" "$TMP/pedidos.txt" && pasa "un rubro pide $m.glb" || falla "ningún rubro pide $m.glb"
done

# --- Validez de cada .glb (PHP: lee el encabezado y el primer chunk)
tools/php.sh -r '
$dir = $argv[1]; $max = (int) $argv[2];
foreach (file($argv[3], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $m) {
    $f = "$dir/$m.glb";
    if (!is_file($f)) { echo "FALLA $m.glb no existe\n"; continue; }
    $tam = filesize($f);
    $d = file_get_contents($f);
    $h = unpack("Vmagic/Vver/Vlargo/Vclen/Vctipo", substr($d, 0, 20));
    $ok = $h["magic"] === 0x46546C67 && $h["ver"] === 2 && $h["largo"] === $tam && $h["ctipo"] === 0x4E4F534A;
    $js = $ok ? json_decode(substr($d, 20, $h["clen"]), true) : null;
    $ok = $ok && is_array($js) && ($js["asset"]["version"] ?? "") === "2.0" && !empty($js["meshes"]);
    echo ($ok ? "ok" : "FALLA"), " $m.glb es GLB 2 válido (magic glTF, versión 2, largo $tam = encabezado)\n";
    echo ($tam <= $max ? "ok" : "FALLA"), " $m.glb pesa " . round($tam / 1024) . " KB (máximo " . ($max / 1024) . " KB)\n";
    // Sin extensiones obligatorias (Draco, cuantización...): Scene Viewer y Quick Look las pueden rechazar
    echo (empty($js["extensionsRequired"]) ? "ok" : "FALLA"), " $m.glb sin extensionsRequired\n";
    // Sin imágenes externas: todo va dentro del .glb
    $externas = array_filter($js["images"] ?? [], static fn ($i) => isset($i["uri"]));
    echo ($externas ? "FALLA" : "ok"), " $m.glb sin texturas externas\n";
}
' "$DIR3D" "$MAX_BYTES" "$TMP/pedidos.txt" > "$TMP/glb.txt"
while IFS= read -r linea; do
  case "$linea" in ok\ *) pasa "${linea#ok }" ;; *) falla "${linea#FALLA }" ;; esac
done < "$TMP/glb.txt"

# --- Cada .glb de la carpeta tiene créditos y lo sirve el servidor con su tipo
[ -f "$DIR3D/CREDITOS.md" ] && pasa "$DIR3D/CREDITOS.md existe" || falla "falta $DIR3D/CREDITOS.md"
grep -qF 'AddType model/gltf-binary .glb' "$DIR3D/.htaccess" && pasa "$DIR3D/.htaccess declara model/gltf-binary" || falla "$DIR3D/.htaccess sin AddType .glb"
grep -qF 'AddType model/gltf-binary .glb' .htaccess && pasa ".htaccess raíz declara model/gltf-binary" || falla ".htaccess raíz sin AddType .glb"
for f in "$DIR3D"/*.glb; do
  m=$(basename "$f" .glb)
  grep -qF "\`$m.glb\`" "$DIR3D/CREDITOS.md" && pasa "$m.glb está en CREDITOS.md" || falla "$m.glb no está en CREDITOS.md"
  grep -qx "$m" "$TMP/pedidos.txt" && pasa "$m.glb lo usa algún rubro" || falla "$m.glb no lo usa ningún rubro (sobra)"
  r=$(curl -s -o "$TMP/bajado.glb" -w "%{http_code} %{content_type}" "$BASE/demos/_comun/3d/$m.glb")
  case "$r" in "200 model/gltf-binary"*) pasa "/demos/_comun/3d/$m.glb responde 200 model/gltf-binary" ;; *) falla "/demos/_comun/3d/$m.glb responde $r" ;; esac
  cmp -s "$f" "$TMP/bajado.glb" && pasa "$m.glb llega completo" || falla "$m.glb llega distinto"
done
r=$(curl -s -o /dev/null -w "%{http_code}" "$BASE/demos/_comun/3d/no-existe.glb")
[ "$r" = "404" ] && pasa "un .glb que no existe responde 404 (el visor muestra la foto)" || falla "un .glb que no existe responde $r"

# --- Páginas: si hay botón 3D, también model-viewer 4.4.0 con SRI y menu3d.js
RUBROS="pizzeria sushi pasteleria panaderia carniceria china peruana variada taller botilleria almacen ferreteria peluqueria barberia veterinaria repuestos"
NO_COMIDA="taller botilleria almacen ferreteria peluqueria barberia veterinaria repuestos"
for r in $RUBROS; do
  tools/php.sh "demos/$r/plataforma/reset.php" > /dev/null || falla "reset.php de $r"
done
MV='<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js" integrity="sha256-GLLnEQNapHBe4oFTClXcIzj/Be7T1wrhbVQQUoKnXtU=" crossorigin="anonymous"></script>'
for r in $RUBROS; do
  CON_3D=0
  PAGINAS="/demos/$r/plataforma/"
  for f in demos/$r/multi/*.php; do
    case "$(basename "$f")" in _*) continue ;; esac
    PAGINAS="$PAGINAS /demos/$r/multi/$(basename "$f")"
  done
  for p in $PAGINAS; do
    curl -s "$BASE$p" > "$TMP/pagina.html"
    if grep -qF 'class="demo-3d-btn' "$TMP/pagina.html"; then
      CON_3D=$((CON_3D + 1))
      grep -qF "$MV" "$TMP/pagina.html" && grep -qF '/demos/_comun/js/menu3d.js?v=' "$TMP/pagina.html" \
        && pasa "$p: botón 3D con model-viewer 4.4.0 (SRI) y menu3d.js" || falla "$p: botón 3D sin model-viewer o sin menu3d.js"
      # Cada botón apunta a un modelo que existe
      for m in $(grep -oE 'data-3d-modelo="/demos/_comun/3d/[a-z0-9-]+\.glb"' "$TMP/pagina.html" | sed -E 's#.*/3d/([a-z0-9-]+)\.glb"#\1#' | sort -u); do
        [ -f "$DIR3D/$m.glb" ] && pasa "$p: $m.glb existe" || falla "$p: pide $m.glb y no existe"
      done
    fi
  done
  [ "$CON_3D" -ge 1 ] && pasa "$r: $CON_3D página(s) con botón 3D" || falla "$r: ninguna página con botón 3D"
  # La Simple-page queda sin 3D
  curl -s "$BASE/demos/$r/simple/" > "$TMP/simple.html"
  grep -qF 'model-viewer' "$TMP/simple.html" && falla "/demos/$r/simple/ carga model-viewer" || pasa "/demos/$r/simple/ sin model-viewer"
  grep -qF 'demo-3d-btn' "$TMP/simple.html" && falla "/demos/$r/simple/ tiene botón 3D" || pasa "/demos/$r/simple/ sin botón 3D"
done

# Fuera de la comida, el visor dice "producto" (no "plato") y la Plataforma ofrece el 3D
for r in $NO_COMIDA; do
  curl -s "$BASE/demos/$r/plataforma/" > "$TMP/tienda.html"
  grep -qF 'class="demo-3d-btn' "$TMP/tienda.html" && pasa "/demos/$r/plataforma/ tiene botón 3D" || falla "/demos/$r/plataforma/ sin botón 3D"
  grep -qF 'data-3d-objeto="producto"' "$TMP/tienda.html" && pasa "/demos/$r/plataforma/ marca data-3d-objeto=producto" || falla "/demos/$r/plataforma/ sin data-3d-objeto=producto"
done
grep -qF "closest('[data-3d-objeto]')" demos/_comun/js/menu3d.js && pasa "menu3d.js lee data-3d-objeto" || falla "menu3d.js no lee data-3d-objeto"

echo
echo "$OK ok / $FAIL fallas"
[ "$FAIL" -eq 0 ]
