<?php
declare(strict_types=1);

// Plantilla común de las páginas de Río Perla (Multi-page).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['rp_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['rp'] = require __DIR__ . '/_datos.php';

const RP_BASE = '/demos/china/multi/';
/** Caracteres chinos decorativos que usa el sitio (se pide solo ese subconjunto de la fuente). */
const RP_HANZI = '珠江點心飲茶蒸炸湯粵宴甜菜單訂座外賣故事聯絡';

function rp(): array
{
    return $GLOBALS['rp'];
}

function rp_rubro(): array
{
    return $GLOBALS['rp_rubro'];
}

function rp_nota(): string
{
    $r = rp_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan']);
}

/** WhatsApp con la nota de pedido de prueba. */
function rp_wa(string $texto): string
{
    return demo_wa($texto . "\n" . rp_nota());
}

/** Menú de navegación: clave => [archivo, nombre]. */
function rp_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'carta' => ['carta.php', 'Carta'],
        'reservas' => ['reservas.php', 'Reservas'],
        'nosotros' => ['nosotros.php', 'Nosotros'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

/** Precio "desde" de un plato. */
function rp_desde(array $p): int
{
    return min($p['variantes']);
}

/** Caracteres chinos decorativos (los lectores de pantalla los omiten). */
function rp_hanzi(string $texto, string $clase = ''): string
{
    return '<span class="rp-hanzi ' . demo_e($clase) . '" lang="zh-Hant" aria-hidden="true">' . demo_e($texto) . '</span>';
}

/** Configuración del carrito en el navegador (_comun/js/carrito.js). */
function rp_config_carrito(): array
{
    $n = rp();
    $catalogo = [];
    foreach ($n['platos'] as $id => $p) {
        $catalogo[$id] = ['nombre' => $p['nombre'], 'variantes' => $p['variantes']];
    }
    return [
        'clave' => 'demo-china-multi-carrito',
        'negocio' => $n['nombre'],
        'nota' => rp_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_despacho'],
        'catalogo' => $catalogo,
    ];
}

/** Formulario "Agregar" de un plato, con selector de porción si tiene variantes. */
function rp_agregar(string $id, array $p): string
{
    $html = '<form class="demo-agregar rp-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(RP_BASE . 'pedido.php') . '" method="get">';
    $variantes = $p['variantes'];
    $primero = array_key_first($variantes);
    if (count($variantes) > 1) {
        $html .= '<label class="demo-sr" for="var-' . demo_e($id) . '">Porción de ' . demo_e($p['nombre']) . '</label>';
        $html .= '<select name="variante" id="var-' . demo_e($id) . '">';
        foreach ($variantes as $nombre => $precio) {
            $html .= '<option value="' . demo_e($nombre) . '">' . demo_e($nombre) . ' · ' . demo_e(demo_clp($precio)) . '</option>';
        }
        $html .= '</select>';
    } else {
        $html .= '<input type="hidden" name="variante" value="' . demo_e($primero) . '">';
        if ($primero !== 'Única') {
            $html .= '<span class="rp-agregar__porcion">' . demo_e($primero) . '</span>';
        }
    }
    $html .= '<span class="rp-agregar__precio" data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($p['nombre']) . ' al pedido">Agregar</button>';
    return $html . '</form>';
}

/**
 * Abre la página: head, aviso de demo y cabecera con menú.
 * $actual: clave de la página; $titulo y $descripcion para el <head>.
 */
function rp_abrir(string $actual, string $titulo, string $descripcion): void
{
    $n = rp();
    $hanzi = 'https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@600&display=swap&text=' . rawurlencode(RP_HANZI);
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=Marcellus&family=Red+Hat+Text:wght@400;500;600&display=swap',
        'css' => [RP_BASE . 'estilo.css'],
        'tema' => '#0e2622',
        'icono' => ['R', '#a8322a', '#f3ede2'],
        'clase_body' => 'rp rp-pagina-' . $actual,
        'extra_head' => '<link rel="stylesheet" href="' . demo_e($hanzi) . '">',
    ]);
    demo_aviso(rp_rubro());
    ?>
<header class="rp-cabecera">
  <div class="rp-ancho rp-cabecera__fila">
    <a class="rp-logo" href="<?= demo_e(RP_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio">
      <span class="rp-sello" lang="zh-Hant" aria-hidden="true"><span>珠</span><span>江</span></span>
      <span class="rp-logo__texto"><span class="rp-logo__nombre">Río Perla</span><span class="rp-logo__sub">Casa cantonesa · dim sum</span></span>
    </a>
    <nav class="rp-nav" aria-label="Menú principal">
      <?php rp_nav_items($actual); ?>
    </nav>
    <a class="rp-carrito" href="<?= demo_e(RP_BASE . 'pedido.php') ?>" data-carrito-etiqueta aria-label="Ver tu pedido"<?= $actual === 'pedido' ? ' aria-current="page"' : '' ?>>
      <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.5" d="M4.5 9h15l-1.6 10.5H6.1zM8.5 9 12 4l3.5 5M9 13.5h6"/></svg>
      <span class="rp-carrito__texto">Para llevar</span>
      <span class="rp-carrito__num" data-carrito-cantidad hidden>0</span>
    </a>
    <details class="rp-menu-movil">
      <summary aria-label="Abrir menú"><span></span><span></span></summary>
      <nav aria-label="Menú del sitio">
        <?php rp_nav_items($actual); ?>
        <a class="rp-menu-movil__reservar" href="<?= demo_e(RP_BASE . 'reservas.php') ?>">Reservar mesa</a>
      </nav>
    </details>
  </div>
</header>
<?php
}

function rp_nav_items(string $actual): void
{
    foreach (rp_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(RP_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>', demo_e($nombre), '</a>';
    }
}

/** Encabezado de las páginas interiores. */
function rp_titular(string $sobre, string $titulo, string $bajada, string $hanzi): void
{
    ?>
<header class="rp-titular">
  <div class="rp-ancho rp-titular__fila">
    <?= rp_hanzi($hanzi, 'rp-hanzi--titular') ?>
    <div>
      <p class="rp-sobre"><?= demo_e($sobre) ?></p>
      <h1><?= $titulo ?></h1>
      <?php if ($bajada !== ''): ?><p class="rp-titular__bajada"><?= demo_e($bajada) ?></p><?php endif; ?>
    </div>
  </div>
</header>
<?php
}

/** Cierra la página: pie, configuración del carrito y scripts. $scripts: ['qr', '3d', 'buscador']. */
function rp_cerrar(array $scripts = []): void
{
    $n = rp();
    $config = json_encode(rp_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="rp-pie">
  <div class="rp-ancho rp-pie__grid">
    <div>
      <p class="rp-logo rp-logo--pie"><span class="rp-sello" lang="zh-Hant" aria-hidden="true"><span>珠</span><span>江</span></span><span class="rp-logo__nombre">Río Perla</span></p>
      <p><?= demo_e($n['eslogan']) ?>.</p>
      <p><?= demo_e($n['comuna']) ?>, Santiago</p>
    </div>
    <div>
      <p class="rp-pie__titulo">Horario</p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario rp-pie__horario'); ?>
    </div>
    <div>
      <p class="rp-pie__titulo">Reservas y pedidos</p>
      <p><a href="<?= demo_e(RP_BASE . 'reservas.php') ?>">Reservar mesa</a></p>
      <p><a href="<?= demo_e(RP_BASE . 'carta.php') ?>">Pedir para llevar</a></p>
      <p><a href="<?= demo_e(rp_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
    </div>
  </div>
  <div class="rp-ancho"><?php demo_aviso_pie(rp_rubro()); ?></div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo "</body>\n</html>\n";
}
