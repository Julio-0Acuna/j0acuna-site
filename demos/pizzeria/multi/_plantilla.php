<?php
declare(strict_types=1);

// Plantilla común de las páginas de Forno Ottavia (Multi-page).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['fo_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['fo'] = require __DIR__ . '/_datos.php';

const FO_BASE = '/demos/pizzeria/multi/';

function fo(): array
{
    return $GLOBALS['fo'];
}

function fo_rubro(): array
{
    return $GLOBALS['fo_rubro'];
}

function fo_nota(): string
{
    $r = fo_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan']);
}

/** WhatsApp con la nota de pedido de prueba. */
function fo_wa(string $texto): string
{
    return demo_wa($texto . "\n" . fo_nota());
}

/** Menú de navegación: clave => [archivo, nombre]. */
function fo_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'menu' => ['menu.php', 'Menú'],
        'nosotros' => ['nosotros.php', 'Nosotros'],
        'preguntas' => ['preguntas.php', 'Preguntas'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

/** Precio "desde" de un plato. */
function fo_desde(array $p): int
{
    return min($p['variantes']);
}

/** Configuración del carrito en el navegador (js/carrito.js). */
function fo_config_carrito(): array
{
    $n = fo();
    $catalogo = [];
    foreach ($n['platos'] as $id => $p) {
        $catalogo[$id] = ['nombre' => $p['nombre'], 'variantes' => $p['variantes']];
    }
    return [
        'clave' => 'demo-pizzeria-multi-carrito',
        'negocio' => $n['nombre'],
        'nota' => fo_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_despacho'],
        'catalogo' => $catalogo,
    ];
}

/** Formulario "Agregar" de un plato, con selector de tamaño si tiene variantes. */
function fo_agregar(string $id, array $p): string
{
    $html = '<form class="demo-agregar fo-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(FO_BASE . 'pedido.php') . '" method="get">';
    $variantes = $p['variantes'];
    $primero = array_key_first($variantes);
    if (count($variantes) > 1) {
        $html .= '<label class="demo-sr" for="var-' . demo_e($id) . '">Tamaño de ' . demo_e($p['nombre']) . '</label>';
        $html .= '<select name="variante" id="var-' . demo_e($id) . '">';
        foreach ($variantes as $nombre => $precio) {
            $html .= '<option value="' . demo_e($nombre) . '">' . demo_e($nombre) . ' · ' . demo_e(demo_clp($precio)) . '</option>';
        }
        $html .= '</select>';
    } else {
        $html .= '<input type="hidden" name="variante" value="' . demo_e($primero) . '">';
    }
    $html .= '<span class="fo-agregar__precio" data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($p['nombre']) . ' al pedido">Agregar</button>';
    return $html . '</form>';
}

/**
 * Abre la página: head, aviso de demo y cabecera con menú.
 * $opc: scripts adicionales en el head (extra_head).
 */
function fo_abrir(string $actual, string $titulo, string $descripcion, array $opc = []): void
{
    $n = fo();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=Jost:wght@400;500;600&display=swap',
        'css' => [FO_BASE . 'estilo.css'],
        'tema' => '#1e1c1a',
        'icono' => ['O', '#1e1c1a', '#d8b98a'],
        'clase_body' => 'fo fo-pagina-' . $actual,
        'extra_head' => $opc['extra_head'] ?? '',
    ]);
    demo_aviso(fo_rubro());
    ?>
<header class="fo-cabecera">
  <div class="fo-ancho fo-cabecera__fila">
    <a class="fo-logo" href="<?= demo_e(FO_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio">
      <span class="fo-logo__forno">Forno</span>
      <span class="fo-logo__nombre">Ottavia</span>
    </a>
    <a class="fo-carrito" href="<?= demo_e(FO_BASE . 'pedido.php') ?>" data-carrito-etiqueta aria-label="Ver tu pedido">
      <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M5 8h14l-1.2 12H6.2zM9 8V6a3 3 0 0 1 6 0v2"/></svg>
      <span class="fo-carrito__texto">Pedido</span>
      <span class="fo-carrito__num" data-carrito-cantidad hidden>0</span>
    </a>
    <details class="fo-menu-movil">
      <summary aria-label="Abrir menú"><span></span><span></span><span></span></summary>
      <nav aria-label="Menú del sitio">
        <?php fo_nav_items($actual); ?>
      </nav>
    </details>
  </div>
  <nav class="fo-nav" aria-label="Menú principal">
    <div class="fo-ancho"><?php fo_nav_items($actual); ?></div>
  </nav>
</header>
<?php
}

function fo_nav_items(string $actual): void
{
    foreach (fo_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(FO_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>', demo_e($nombre), '</a>';
    }
}

/** Cierra la página: pie, configuración del carrito y scripts. $scripts: ['qr', '3d']. */
function fo_cerrar(array $scripts = []): void
{
    $n = fo();
    $config = json_encode(fo_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="fo-pie">
  <div class="fo-ancho fo-pie__grid">
    <div>
      <p class="fo-logo fo-logo--pie"><span class="fo-logo__forno">Forno</span> <span class="fo-logo__nombre">Ottavia</span></p>
      <p><?= demo_e($n['eslogan']) ?>.</p>
      <p><?= demo_e($n['comuna']) ?>, Santiago</p>
    </div>
    <div>
      <p class="fo-pie__titulo">Horario</p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario fo-pie__horario'); ?>
    </div>
    <div>
      <p class="fo-pie__titulo">Pide o reserva</p>
      <p><a href="<?= demo_e(fo_wa('Hola, quiero reservar una mesa en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Reservar por WhatsApp</a></p>
      <p><a href="<?= demo_e(FO_BASE . 'menu.php') ?>">Ver el menú</a></p>
      <p><a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
    </div>
  </div>
  <div class="fo-ancho"><?php demo_aviso_pie(fo_rubro()); ?></div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo "</body>\n</html>\n";
}
