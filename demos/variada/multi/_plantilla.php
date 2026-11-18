<?php
declare(strict_types=1);

// Plantilla común de las páginas de Doble Palta (Multi-page).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['dp_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['dp'] = require __DIR__ . '/_datos.php';

const DP_BASE = '/demos/variada/multi/';

function dp(): array
{
    return $GLOBALS['dp'];
}

function dp_rubro(): array
{
    return $GLOBALS['dp_rubro'];
}

function dp_nota(): string
{
    $r = dp_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan']);
}

/** WhatsApp con la nota de pedido de prueba. */
function dp_wa(string $texto): string
{
    return demo_wa($texto . "\n" . dp_nota());
}

/** Menú de navegación: clave => [archivo, nombre]. */
function dp_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'carta' => ['carta.php', 'Carta'],
        'plancha' => ['plancha.php', 'La plancha'],
        'preguntas' => ['preguntas.php', 'Preguntas'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

/** Precio "desde" de un plato. */
function dp_desde(array $p): int
{
    return min($p['variantes']);
}

/** Configuración del carrito en el navegador (js/carrito.js). */
function dp_config_carrito(): array
{
    $n = dp();
    $catalogo = [];
    foreach ($n['platos'] as $id => $p) {
        $catalogo[$id] = ['nombre' => $p['nombre'], 'variantes' => $p['variantes']];
    }
    return [
        'clave' => 'demo-variada-multi-carrito',
        'negocio' => $n['nombre'],
        'nota' => dp_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_despacho'],
        'catalogo' => $catalogo,
    ];
}

/** Formulario "Agregar" de un plato, con selector de estilo o tamaño si tiene variantes. */
function dp_agregar(string $id, array $p): string
{
    $html = '<form class="demo-agregar dp-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(DP_BASE . 'pedido.php') . '" method="get">';
    $variantes = $p['variantes'];
    $primero = array_key_first($variantes);
    if (count($variantes) > 1) {
        $html .= '<label class="demo-sr" for="var-' . demo_e($id) . '">Estilo o tamaño de ' . demo_e($p['nombre']) . '</label>';
        $html .= '<select name="variante" id="var-' . demo_e($id) . '">';
        foreach ($variantes as $nombre => $precio) {
            $html .= '<option value="' . demo_e($nombre) . '">' . demo_e($nombre) . ' · ' . demo_e(demo_clp($precio)) . '</option>';
        }
        $html .= '</select>';
    } else {
        $html .= '<input type="hidden" name="variante" value="' . demo_e($primero) . '">';
    }
    $html .= '<span class="dp-agregar__precio" data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($p['nombre']) . ' a la comanda">Agregar</button>';
    return $html . '</form>';
}

/** Logo: palabra "DOBLE PALTA" con media palta como O. */
function dp_logo(string $clase = ''): string
{
    return '<span class="dp-logo ' . demo_e($clase) . '"><span class="dp-logo__doble">D'
        . '<svg class="dp-logo__palta" viewBox="0 0 40 52" aria-hidden="true" focusable="false"><path d="M20 2C10 2 6 14 4 26 1.5 40 9 50 20 50s18.5-10 16-24C34 14 30 2 20 2z" fill="#3d6a2c"/><path d="M20 8c-7 0-10 10-11.5 19C6.6 37 12 44 20 44s13.4-7 11.5-17C30 18 27 8 20 8z" fill="#cfe38f"/><circle cx="20" cy="30" r="8" fill="#7a4a24"/><circle cx="17.5" cy="27.5" r="2.4" fill="#a8703f"/></svg>'
        . 'BLE</span><span class="dp-logo__palta-texto">PALTA</span></span>';
}

/**
 * Abre la página: head, aviso de demo y cabecera.
 * $opc: extra_head.
 */
function dp_abrir(string $actual, string $titulo, string $descripcion, array $opc = []): void
{
    $n = dp();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,400..900&family=DM+Mono:wght@400;500&display=swap',
        'css' => [DP_BASE . 'estilo.css'],
        'tema' => '#151311',
        'icono' => ['P', '#3d6a2c', '#fff6e3'],
        'clase_body' => 'dp dp-pagina-' . $actual,
        'extra_head' => $opc['extra_head'] ?? '',
    ]);
    demo_aviso(dp_rubro());
    ?>
<header class="dp-cabecera">
  <div class="dp-ancho dp-cabecera__fila">
    <a class="dp-cabecera__marca" href="<?= demo_e(DP_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio"><?= dp_logo() ?></a>
    <nav class="dp-nav" aria-label="Menú principal"><?php dp_nav_items($actual); ?></nav>
    <a class="dp-comanda-btn" href="<?= demo_e(DP_BASE . 'pedido.php') ?>" data-carrito-etiqueta aria-label="Ver tu pedido">
      <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" d="M6 3h12v18l-2-1.5-2 1.5-2-1.5-2 1.5-2-1.5L6 21zM9 8h6M9 12h6M9 16h3"/></svg>
      <span class="dp-comanda-btn__texto">Comanda</span>
      <span class="dp-comanda-btn__num" data-carrito-cantidad hidden>0</span>
    </a>
    <details class="dp-menu-movil">
      <summary aria-label="Abrir menú"><span></span><span></span><span></span></summary>
      <nav aria-label="Menú del sitio"><?php dp_nav_items($actual); ?></nav>
    </details>
  </div>
</header>
<?php
}

function dp_nav_items(string $actual): void
{
    foreach (dp_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(DP_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>', demo_e($nombre), '</a>';
    }
}

/** Cierra la página: pie, configuración del carrito y scripts. $scripts: ['qr', '3d', 'buscador']. */
function dp_cerrar(array $scripts = []): void
{
    $n = dp();
    $config = json_encode(dp_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="dp-pie">
  <div class="dp-ancho dp-pie__grid">
    <div>
      <?= dp_logo('dp-logo--pie') ?>
      <p><?= demo_e($n['eslogan']) ?>.</p>
      <p><?= demo_e($n['comuna']) ?>, Santiago</p>
    </div>
    <div>
      <p class="dp-pie__titulo">Horario de plancha</p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario dp-pie__horario'); ?>
    </div>
    <div>
      <p class="dp-pie__titulo">Pide</p>
      <p><a href="<?= demo_e(DP_BASE . 'carta.php') ?>">Armar una comanda</a></p>
      <p><a href="<?= demo_e(dp_wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Pedir por WhatsApp</a></p>
      <p><a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
    </div>
  </div>
  <div class="dp-ancho"><?php demo_aviso_pie(dp_rubro()); ?></div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo "</body>\n</html>\n";
}
