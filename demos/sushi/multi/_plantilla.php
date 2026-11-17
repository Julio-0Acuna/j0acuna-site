<?php
declare(strict_types=1);

// Plantilla común de las páginas de Kumo (Multi-page).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['ku_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['ku'] = require __DIR__ . '/_datos.php';

const KU_BASE = '/demos/sushi/multi/';

function ku(): array
{
    return $GLOBALS['ku'];
}

function ku_rubro(): array
{
    return $GLOBALS['ku_rubro'];
}

function ku_nota(): string
{
    $r = ku_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan']);
}

/** WhatsApp con la nota de pedido de prueba. */
function ku_wa(string $texto): string
{
    return demo_wa($texto . "\n" . ku_nota());
}

/** Enlace de reserva por WhatsApp. */
function ku_wa_reserva(string $detalle = ''): string
{
    return ku_wa('Hola, quiero reservar en la barra de ' . ku()['nombre'] . ($detalle !== '' ? ' (' . $detalle . ')' : '') . '. ¿Qué días tienen disponibles?');
}

/** Menú de navegación: clave => [archivo, nombre]. */
function ku_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'menu' => ['menu.php', 'Carta'],
        'nosotros' => ['nosotros.php', 'La barra'],
        'preguntas' => ['preguntas.php', 'Preguntas'],
        'contacto' => ['contacto.php', 'Reservas'],
    ];
}

function ku_desde(array $p): int
{
    return min($p['variantes']);
}

/** Configuración del carrito en el navegador (js/carrito.js). */
function ku_config_carrito(): array
{
    $n = ku();
    $catalogo = [];
    foreach ($n['platos'] as $id => $p) {
        $catalogo[$id] = ['nombre' => $p['nombre'], 'variantes' => $p['variantes']];
    }
    return [
        'clave' => 'demo-sushi-multi-carrito',
        'negocio' => $n['nombre'],
        'nota' => ku_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_despacho'],
        'catalogo' => $catalogo,
    ];
}

/** Formulario "Agregar" de un plato, con selector de porción si tiene variantes. */
function ku_agregar(string $id, array $p): string
{
    $html = '<form class="demo-agregar ku-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(KU_BASE . 'pedido.php') . '" method="get">';
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
            $html .= '<span class="ku-agregar__porcion">' . demo_e($primero) . '</span>';
        }
    }
    $html .= '<span class="ku-agregar__precio" data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($p['nombre']) . ' al pedido">Agregar</button>';
    return $html . '</form>';
}

/** Sello bermellón con el kanji de la nube (logo). */
function ku_sello(string $clase = ''): string
{
    return '<span class="ku-sello ' . demo_e($clase) . '" aria-hidden="true">雲</span>';
}

/** Abre la página: head, aviso de demo y cabecera. */
function ku_abrir(string $actual, string $titulo, string $descripcion): void
{
    $n = ku();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=Shippori+Mincho:wght@500;600;700&family=Zen+Kaku+Gothic+New:wght@400;500;700&display=swap',
        'css' => [KU_BASE . 'estilo.css'],
        'tema' => '#0e0e0d',
        'icono' => ['雲', '#c8402a', '#f4efe6'],
        'clase_body' => 'ku ku-pagina-' . $actual,
    ]);
    demo_aviso(ku_rubro());
    ?>
<header class="ku-cabecera">
  <div class="ku-ancho ku-cabecera__fila">
    <a class="ku-logo" href="<?= demo_e(KU_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio">
      <?= ku_sello() ?>
      <span class="ku-logo__texto"><span class="ku-logo__nombre">Kumo</span><span class="ku-logo__bajada">Omakase · <?= demo_e($n['comuna']) ?></span></span>
    </a>
    <nav class="ku-nav" aria-label="Menú principal">
      <?php ku_nav_items($actual); ?>
    </nav>
    <a class="ku-carrito" href="<?= demo_e(KU_BASE . 'pedido.php') ?>" data-carrito-etiqueta aria-label="Ver tu pedido"<?= $actual === 'pedido' ? ' aria-current="page"' : '' ?>>
      <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.5" d="M4 9h16l-1.6 11H5.6zM8.5 9 12 4l3.5 5"/></svg>
      <span class="ku-carrito__texto">Para llevar</span>
      <span class="ku-carrito__num" data-carrito-cantidad hidden>0</span>
    </a>
    <details class="ku-menu-movil">
      <summary aria-label="Abrir menú"><span></span><span></span></summary>
      <nav aria-label="Menú del sitio">
        <?php ku_nav_items($actual, true); ?>
        <a class="ku-menu-movil__reserva" href="<?= demo_e(ku_wa_reserva()) ?>" target="_blank" rel="noopener">Reservar por WhatsApp</a>
      </nav>
    </details>
  </div>
</header>
<?php
}

function ku_nav_items(string $actual, bool $conKanji = false): void
{
    $kanji = ['inicio' => '入', 'menu' => '品', 'nosotros' => '席', 'preguntas' => '問', 'contacto' => '約'];
    foreach (ku_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(KU_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>';
        if ($conKanji) {
            echo '<span class="ku-menu-movil__kanji" aria-hidden="true">', $kanji[$clave], '</span>';
        }
        echo demo_e($nombre), '</a>';
    }
}

/** Encabezado de las páginas internas. */
function ku_titular(string $sobre, string $titulo, string $bajada = '', string $vertical = ''): void
{
    ?>
<header class="ku-titular">
  <div class="ku-ancho ku-titular__fila">
    <div>
      <p class="ku-sobre"><?= demo_e($sobre) ?></p>
      <h1><?= $titulo ?></h1>
      <?php if ($bajada !== ''): ?><p class="ku-titular__bajada"><?= $bajada ?></p><?php endif; ?>
    </div>
    <?php if ($vertical !== ''): ?><p class="ku-vertical" aria-hidden="true"><?= demo_e($vertical) ?></p><?php endif; ?>
  </div>
</header>
<?php
}

/** Cierra la página: pie, configuración del carrito y scripts. $scripts: ['qr', '3d', 'buscador']. */
function ku_cerrar(array $scripts = []): void
{
    $n = ku();
    $config = json_encode(ku_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="ku-pie">
  <div class="ku-ancho ku-pie__grid">
    <div class="ku-pie__marca">
      <?= ku_sello('ku-sello--grande') ?>
      <div>
        <p class="ku-pie__nombre">Kumo</p>
        <p><?= demo_e($n['eslogan']) ?>.</p>
        <p><?= demo_e($n['comuna']) ?>, Santiago</p>
      </div>
    </div>
    <div>
      <p class="ku-pie__titulo">Horario</p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario ku-pie__horario'); ?>
    </div>
    <div>
      <p class="ku-pie__titulo">Reservas y pedidos</p>
      <p><a href="<?= demo_e(ku_wa_reserva()) ?>" target="_blank" rel="noopener">Reservar por WhatsApp</a></p>
      <p><a href="<?= demo_e(KU_BASE . 'menu.php') ?>">Pedir para llevar</a></p>
      <p><a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
    </div>
  </div>
  <div class="ku-ancho"><?php demo_aviso_pie(ku_rubro()); ?></div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo "</body>\n</html>\n";
}
