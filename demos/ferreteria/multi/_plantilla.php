<?php
declare(strict_types=1);

// Plantilla común de las páginas de Pigmento (Multi-page).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['pg_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['pg'] = require __DIR__ . '/_datos.php';

const PG_BASE = '/demos/ferreteria/multi/';

function pg(): array
{
    return $GLOBALS['pg'];
}

function pg_rubro(): array
{
    return $GLOBALS['pg_rubro'];
}

function pg_nota(): string
{
    $r = pg_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan']);
}

/** WhatsApp con la nota de pedido de prueba. */
function pg_wa(string $texto): string
{
    return demo_wa($texto . "\n" . pg_nota());
}

/** Menú de navegación: clave => [archivo, nombre]. */
function pg_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'catalogo' => ['catalogo.php', 'Pinturas'],
        'colores' => ['colores.php', 'Colores'],
        'asesoria' => ['asesoria.php', 'Asesoría'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

/** Todos los colores de la carta: código => [nombre, hex, familia]. */
function pg_colores(): array
{
    static $todos = null;
    if ($todos === null) {
        $todos = [];
        foreach (pg()['colores'] as $familia => $lista) {
            foreach ($lista as [$codigo, $nombre, $hex]) {
                $todos[$codigo] = [$nombre, $hex, $familia];
            }
        }
    }
    return $todos;
}

/** ¿Texto claro u oscuro sobre este color? (luminancia relativa simple). */
function pg_tinta_sobre(string $hex): string
{
    $hex = ltrim($hex, '#');
    [$r, $g, $b] = array_map(static fn (string $c): float => hexdec($c) / 255, str_split($hex, 2));
    $lin = static fn (float $c): float => $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
    $l = 0.2126 * $lin($r) + 0.7152 * $lin($g) + 0.0722 * $lin($b);
    return $l > 0.32 ? '#23211e' : '#ffffff';
}

/** Carta de color (chip): bloque de color con código y nombre. */
function pg_chip(string $codigo, string $clase = '', bool $enlace = true): string
{
    $c = pg_colores()[$codigo] ?? null;
    if ($c === null) {
        return '';
    }
    [$nombre, $hex] = $c;
    $contenido = '<span class="pg-chip__color" style="background:' . demo_e($hex) . '"></span>'
        . '<span class="pg-chip__pie"><span class="pg-chip__codigo">' . demo_e($codigo) . '</span><span class="pg-chip__nombre">' . demo_e($nombre) . '</span></span>';
    if (!$enlace) {
        return '<span class="pg-chip ' . demo_e($clase) . '">' . $contenido . '</span>';
    }
    return '<a class="pg-chip ' . demo_e($clase) . '" href="' . demo_e(PG_BASE . 'cotizacion.php?color=' . rawurlencode($codigo)) . '" aria-label="Cotizar el color ' . demo_e($nombre . ' (' . $codigo . ')') . '">' . $contenido . '</a>';
}

/** Precio "desde" de un producto. */
function pg_desde(array $p): int
{
    return min($p['variantes']);
}

/** Configuración del carrito en el navegador (_comun/js/carrito.js). */
function pg_config_carrito(): array
{
    $n = pg();
    $catalogo = [];
    foreach ($n['productos'] as $id => $p) {
        $catalogo[$id] = ['nombre' => $p['nombre'], 'variantes' => $p['variantes']];
    }
    return [
        'clave' => 'demo-ferreteria-multi-cotizacion',
        'negocio' => $n['nombre'],
        'nota' => pg_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_despacho'],
        'catalogo' => $catalogo,
    ];
}

/** Formulario "Agregar a la cotización" con selector de formato (¼ galón, galón, tineta). */
function pg_agregar(string $id, array $p): string
{
    $html = '<form class="demo-agregar pg-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(PG_BASE . 'cotizacion.php') . '" method="get">';
    $variantes = $p['variantes'];
    $primero = array_key_first($variantes);
    if (count($variantes) > 1) {
        $html .= '<label class="demo-sr" for="var-' . demo_e($id) . '">Formato de ' . demo_e($p['nombre']) . '</label>';
        $html .= '<select name="variante" id="var-' . demo_e($id) . '">';
        foreach ($variantes as $nombre => $precio) {
            $html .= '<option value="' . demo_e($nombre) . '">' . demo_e($nombre) . ' · ' . demo_e(demo_clp($precio)) . '</option>';
        }
        $html .= '</select>';
    } else {
        $html .= '<input type="hidden" name="variante" value="' . demo_e($primero) . '">';
        $html .= '<span class="pg-agregar__formato">' . demo_e($primero === 'Única' ? 'Unidad' : $primero) . '</span>';
    }
    $html .= '<span class="pg-agregar__precio" data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($p['nombre']) . ' a la cotización">Agregar</button>';
    return $html . '</form>';
}

/** Marca: tres cartas de color superpuestas y el nombre en cursiva. */
function pg_logo(): string
{
    return '<span class="pg-logo">'
        . '<svg viewBox="0 0 40 40" width="34" height="34" aria-hidden="true" focusable="false">'
        . '<rect x="4" y="9" width="14" height="24" rx="1.5" fill="#b4542f" transform="rotate(-14 11 21)"/>'
        . '<rect x="13" y="7" width="14" height="24" rx="1.5" fill="#9aab8f" transform="rotate(-2 20 19)"/>'
        . '<rect x="22" y="8" width="14" height="24" rx="1.5" fill="#24495a" transform="rotate(11 29 20)"/>'
        . '<rect x="22" y="25" width="14" height="7" fill="#f6f3ee" transform="rotate(11 29 20)"/></svg>'
        . '<span class="pg-logo__texto">Pigmento<small>Pinturas · ' . demo_e(pg()['comuna']) . '</small></span></span>';
}

/** Abre la página: head, aviso de demo y cabecera con menú. */
function pg_abrir(string $actual, string $titulo, string $descripcion): void
{
    $n = pg();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=Familjen+Grotesk:wght@400;500;600;700&family=Fragment+Mono&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;1,6..72,400;1,6..72,500&display=swap',
        'css' => [PG_BASE . 'estilo.css'],
        'tema' => '#f6f3ee',
        'icono' => ['P', '#b4542f', '#f6f3ee'],
        'clase_body' => 'pg pg-pagina-' . $actual,
    ]);
    demo_aviso(pg_rubro());
    ?>
<header class="pg-cabecera">
  <div class="pg-ancho pg-cabecera__fila">
    <a class="pg-cabecera__marca" href="<?= demo_e(PG_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio"><?= pg_logo() ?></a>
    <nav class="pg-nav" aria-label="Menú principal">
      <?php pg_nav_items($actual); ?>
    </nav>
    <a class="pg-cotiza-btn<?= $actual === 'cotizacion' ? ' is-activo' : '' ?>" href="<?= demo_e(PG_BASE . 'cotizacion.php') ?>" data-carrito-etiqueta aria-label="Ver tu cotización">
      <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M5 8h14l-1.4 12H6.4z M9 8V6a3 3 0 0 1 6 0v2" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
      <span class="pg-cotiza-btn__texto">Cotización</span>
      <span class="pg-cotiza-btn__num" data-carrito-cantidad hidden>0</span>
    </a>
    <details class="pg-menu-movil">
      <summary aria-label="Abrir menú"><span></span><span></span></summary>
      <nav aria-label="Menú del sitio">
        <?php pg_nav_items($actual); ?>
        <a href="<?= demo_e(demo_tel()) ?>">Llamar</a>
      </nav>
    </details>
  </div>
</header>
<?php
}

function pg_nav_items(string $actual): void
{
    foreach (pg_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(PG_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>', demo_e($nombre), '</a>';
    }
}

/** Encabezado de página interior: título en serifa y una tira de colores. */
function pg_titular(string $sobre, string $titulo, string $bajada, array $tira = ['PG-201', 'PG-602', 'PG-301', 'PG-401', 'PG-501']): void
{
    ?>
<header class="pg-titular">
  <div class="pg-ancho pg-titular__grid">
    <div>
      <p class="pg-sobre"><?= demo_e($sobre) ?></p>
      <h1><?= $titulo ?></h1>
      <p class="pg-titular__bajada"><?= demo_e($bajada) ?></p>
    </div>
    <div class="pg-tira" aria-hidden="true">
      <?php foreach ($tira as $codigo): $c = pg_colores()[$codigo] ?? null; if (!$c) { continue; } ?>
      <span style="background:<?= demo_e($c[1]) ?>"></span>
      <?php endforeach; ?>
    </div>
  </div>
</header>
<?php
}

/** Cierra la página: pie, configuración del carrito y scripts. */
function pg_cerrar(array $scripts = []): void
{
    $n = pg();
    $config = json_encode(pg_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="pg-pie">
  <div class="pg-ancho pg-pie__grid">
    <div>
      <p class="pg-pie__logo"><?= pg_logo() ?></p>
      <p><?= demo_e($n['eslogan']) ?> desde <?= (int) $n['desde'] ?>. <?= demo_e($n['comuna']) ?>, Santiago.</p>
    </div>
    <div>
      <p class="pg-pie__titulo">Horario</p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario pg-pie__horario'); ?>
    </div>
    <div>
      <p class="pg-pie__titulo">Cotiza</p>
      <p><a href="<?= demo_e(PG_BASE . 'catalogo.php') ?>">Ver pinturas</a></p>
      <p><a href="<?= demo_e(PG_BASE . 'colores.php') ?>">Carta de colores y calculadora</a></p>
      <p><a href="<?= demo_e(pg_wa('Hola, quiero cotizar pintura en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
    </div>
  </div>
  <div class="pg-ancho"><?php demo_aviso_pie(pg_rubro()); ?></div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo "</body>\n</html>\n";
}
