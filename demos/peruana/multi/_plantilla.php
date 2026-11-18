<?php
declare(strict_types=1);

// Plantilla común de las páginas de Muña (Multi-page).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['mu_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['mu'] = require __DIR__ . '/_datos.php';

const MU_BASE = '/demos/peruana/multi/';

function mu(): array
{
    return $GLOBALS['mu'];
}

function mu_rubro(): array
{
    return $GLOBALS['mu_rubro'];
}

function mu_nota(): string
{
    $r = mu_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan']);
}

/** WhatsApp con la nota de pedido de prueba. */
function mu_wa(string $texto): string
{
    return demo_wa($texto . "\n" . mu_nota());
}

/** Menú de navegación: clave => [archivo, nombre]. */
function mu_paginas(): array
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
function mu_desde(array $p): int
{
    return min($p['variantes']);
}

/** Chakana (cruz andina escalonada) en SVG, para el logo y los separadores. */
function mu_chakana(int $tam = 18, string $clase = 'mu-chakana'): string
{
    return '<svg class="' . demo_e($clase) . '" viewBox="0 0 12 12" width="' . $tam . '" height="' . $tam . '" aria-hidden="true" focusable="false">'
        . '<path fill="currentColor" fill-rule="evenodd" d="M4 0h4v2h2v2h2v4h-2v2H8v2H4v-2H2V8H0V4h2V2h2zM6 4.4a1.6 1.6 0 1 0 0 3.2 1.6 1.6 0 0 0 0-3.2z"/></svg>';
}

/** Configuración del carrito en el navegador (js/carrito.js). */
function mu_config_carrito(): array
{
    $n = mu();
    $catalogo = [];
    foreach ($n['platos'] as $id => $p) {
        $catalogo[$id] = ['nombre' => $p['nombre'], 'variantes' => $p['variantes']];
    }
    return [
        'clave' => 'demo-peruana-multi-carrito',
        'negocio' => $n['nombre'],
        'nota' => mu_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_despacho'],
        'catalogo' => $catalogo,
    ];
}

/** Formulario "Agregar" de un plato, con selector de porción si tiene variantes. */
function mu_agregar(string $id, array $p): string
{
    $html = '<form class="demo-agregar mu-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(MU_BASE . 'pedido.php') . '" method="get">';
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
        $html .= '<span class="mu-agregar__porcion">' . demo_e($primero) . '</span>';
    }
    $html .= '<span class="mu-agregar__precio" data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($p['nombre']) . ' al pedido">Agregar</button>';
    return $html . '</form>';
}

/** Abre la página: head, aviso de demo y cabecera con menú. $opc: extra_head. */
function mu_abrir(string $actual, string $titulo, string $descripcion, array $opc = []): void
{
    $n = mu();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=Gloock&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap',
        'css' => [MU_BASE . 'estilo.css'],
        'tema' => '#1a1220',
        'icono' => ['M', '#1a1220', '#f2a51a'],
        'clase_body' => 'mu mu-pagina-' . $actual,
        'extra_head' => $opc['extra_head'] ?? '',
    ]);
    demo_aviso(mu_rubro());
    ?>
<header class="mu-cabecera">
  <div class="mu-ancho mu-cabecera__fila">
    <a class="mu-logo" href="<?= demo_e(MU_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio">
      <?= mu_chakana(20, 'mu-chakana mu-logo__signo') ?>
      <span class="mu-logo__nombre">Muña</span>
      <span class="mu-logo__bajada">nikkei · criollo</span>
    </a>
    <nav class="mu-nav" aria-label="Menú principal">
      <?php mu_nav_items($actual); ?>
    </nav>
    <div class="mu-cabecera__acciones">
      <a class="mu-carrito" href="<?= demo_e(MU_BASE . 'pedido.php') ?>" data-carrito-etiqueta aria-label="Ver tu pedido">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M4.5 9h15l-1.6 10.5H6.1zM8.5 9 12 4l3.5 5"/></svg>
        <span class="mu-carrito__texto">Pedido</span>
        <span class="mu-carrito__num" data-carrito-cantidad hidden>0</span>
      </a>
      <a class="mu-btn mu-btn--chico mu-cabecera__reservar" href="<?= demo_e(MU_BASE . 'reservas.php') ?>">Reservar</a>
      <details class="mu-menu-movil">
        <summary aria-label="Abrir menú"><span></span><span></span></summary>
        <nav aria-label="Menú del sitio">
          <?php mu_nav_items($actual); ?>
          <a href="<?= demo_e(MU_BASE . 'pedido.php') ?>"<?= $actual === 'pedido' ? ' aria-current="page" class="is-activo"' : '' ?>>Tu pedido</a>
        </nav>
      </details>
    </div>
  </div>
</header>
<?php
}

function mu_nav_items(string $actual): void
{
    foreach (mu_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(MU_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>', demo_e($nombre), '</a>';
    }
}

/** Separador con la chakana al centro. */
function mu_separador(): void
{
    echo '<div class="mu-separador" aria-hidden="true"><span></span>', mu_chakana(16), '<span></span></div>';
}

/** Cierra la página: pie, configuración del carrito y scripts. $scripts: ['qr', '3d', 'buscador']. */
function mu_cerrar(array $scripts = []): void
{
    $n = mu();
    $config = json_encode(mu_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="mu-pie">
  <div class="mu-ancho">
    <div class="mu-pie__grid">
      <div>
        <p class="mu-logo mu-logo--pie"><?= mu_chakana(18, 'mu-chakana mu-logo__signo') ?><span class="mu-logo__nombre">Muña</span></p>
        <p><?= demo_e($n['eslogan']) ?>.<br><?= demo_e($n['comuna']) ?>, Santiago.</p>
      </div>
      <div>
        <p class="mu-pie__titulo">Horario</p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario mu-pie__horario'); ?>
      </div>
      <div>
        <p class="mu-pie__titulo">Reservas y pedidos</p>
        <p><a href="<?= demo_e(MU_BASE . 'reservas.php') ?>">Reservar una mesa</a></p>
        <p><a href="<?= demo_e(MU_BASE . 'carta.php') ?>">Pedir para llevar</a></p>
        <p><a href="<?= demo_e(mu_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
      </div>
    </div>
    <?php demo_aviso_pie(mu_rubro()); ?>
  </div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo "</body>\n</html>\n";
}
