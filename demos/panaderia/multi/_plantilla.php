<?php
declare(strict_types=1);

// Plantilla común de las páginas de Miga Lenta (Multi-page).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['ml_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['ml'] = require __DIR__ . '/_datos.php';

const ML_BASE = '/demos/panaderia/multi/';

function ml(): array
{
    return $GLOBALS['ml'];
}

function ml_rubro(): array
{
    return $GLOBALS['ml_rubro'];
}

function ml_nota(): string
{
    $r = ml_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan']);
}

/** WhatsApp con la nota de pedido de prueba. */
function ml_wa(string $texto): string
{
    return demo_wa($texto . "\n" . ml_nota());
}

/** Menú de navegación: clave => [archivo, nombre]. */
function ml_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'panes' => ['panes.php', 'Panes'],
        'proceso' => ['proceso.php', 'Proceso'],
        'preguntas' => ['preguntas.php', 'Preguntas'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

function ml_desde(array $p): int
{
    return min($p['variantes']);
}

/** Flecha de los botones (se desliza al pasar el mouse). */
function ml_flecha(): string
{
    return '<svg class="ml-flecha" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" d="M4 12h15M13 6l6 6-6 6"/></svg>';
}

/** Configuración del carrito en el navegador (js/carrito.js). */
function ml_config_carrito(): array
{
    $n = ml();
    $catalogo = [];
    foreach ($n['productos'] as $id => $p) {
        $catalogo[$id] = ['nombre' => $p['nombre'], 'variantes' => $p['variantes']];
    }
    return [
        'clave' => 'demo-panaderia-multi-carrito',
        'negocio' => $n['nombre'],
        'nota' => ml_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_despacho'],
        'catalogo' => $catalogo,
    ];
}

/** Formulario "Agregar" de un producto, con selector (entera / media, unidad / caja) si tiene variantes. */
function ml_agregar(string $id, array $p): string
{
    $html = '<form class="demo-agregar ml-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(ML_BASE . 'pedido.php') . '" method="get">';
    $variantes = $p['variantes'];
    $primero = array_key_first($variantes);
    if (count($variantes) > 1) {
        $html .= '<label class="demo-sr" for="var-' . demo_e($id) . '">Tamaño de ' . demo_e($p['nombre']) . '</label>';
        $html .= '<select name="variante" id="var-' . demo_e($id) . '">';
        foreach ($variantes as $nombre => $precio) {
            $html .= '<option value="' . demo_e($nombre) . '">' . demo_e($nombre) . '</option>';
        }
        $html .= '</select>';
    } else {
        $html .= '<input type="hidden" name="variante" value="' . demo_e($primero) . '">';
    }
    $html .= '<span class="ml-agregar__precio" data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($p['nombre']) . ' al pedido"><span aria-hidden="true">+</span> Agregar</button>';
    return $html . '</form>';
}

/** Abre la página: head, aviso de demo y cabecera con menú. */
function ml_abrir(string $actual, string $titulo, string $descripcion, array $opc = []): void
{
    $n = ml();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=Instrument+Sans:wght@400;500;600&family=Instrument+Serif:ital@0;1&display=swap',
        'css' => [ML_BASE . 'estilo.css'],
        'tema' => '#f5f1e8',
        'icono' => ['m', '#2a211b', '#f5f1e8'],
        'clase_body' => 'ml ml-pagina-' . $actual,
        'extra_head' => $opc['extra_head'] ?? '',
    ]);
    demo_aviso(ml_rubro());
    ?>
<header class="ml-cabecera">
  <div class="ml-ancho ml-cabecera__fila">
    <a class="ml-logo" href="<?= demo_e(ML_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio">
      <span class="ml-logo__nombre">Miga <em>Lenta</em></span>
      <span class="ml-logo__bajada">Masa madre · <?= demo_e($n['comuna']) ?></span>
    </a>
    <nav class="ml-nav" aria-label="Menú principal">
      <?php ml_nav_items($actual); ?>
    </nav>
    <a class="ml-carrito<?= $actual === 'pedido' ? ' is-activo' : '' ?>" href="<?= demo_e(ML_BASE . 'pedido.php') ?>" data-carrito-etiqueta aria-label="Ver tu pedido">
      <span class="ml-carrito__texto">Pedido</span>
      <span class="ml-carrito__num" data-carrito-cantidad hidden>0</span>
    </a>
    <details class="ml-menu-movil">
      <summary aria-label="Abrir menú"><span></span><span></span></summary>
      <nav aria-label="Menú del sitio">
        <?php ml_nav_items($actual); ?>
        <a href="<?= demo_e(ML_BASE . 'pedido.php') ?>"<?= $actual === 'pedido' ? ' aria-current="page" class="is-activo"' : '' ?>>Tu pedido</a>
      </nav>
    </details>
  </div>
</header>
<?php
}

function ml_nav_items(string $actual): void
{
    foreach (ml_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(ML_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>', demo_e($nombre), '</a>';
    }
}

/** Cierra la página: pie, configuración del carrito y scripts. $scripts: ['qr', '3d', 'buscador']. */
function ml_cerrar(array $scripts = []): void
{
    $n = ml();
    $config = json_encode(ml_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="ml-pie">
  <div class="ml-ancho">
    <p class="ml-pie__frase">Pan de masa madre, <em>fermentado sin apuro.</em></p>
    <div class="ml-pie__grid">
      <div>
        <p class="ml-etiqueta">Dónde</p>
        <p><?= demo_e($n['nombre']) ?> · <?= demo_e($n['comuna']) ?>, Santiago</p>
        <p>Despacho de martes a sábado.</p>
      </div>
      <div>
        <p class="ml-etiqueta">Horario</p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario ml-pie__horario'); ?>
      </div>
      <div>
        <p class="ml-etiqueta">Encargos</p>
        <p><a href="<?= demo_e(ML_BASE . 'panes.php') ?>">Armar un pedido</a></p>
        <p><a href="<?= demo_e(ml_wa('Hola, quiero hacer un encargo en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Escribir por WhatsApp</a></p>
        <p><a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
      </div>
    </div>
    <?php demo_aviso_pie(ml_rubro()); ?>
  </div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo "</body>\n</html>\n";
}
