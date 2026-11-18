<?php
declare(strict_types=1);

// Plantilla común de las páginas de El Gabinete (Multi-page).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['eg_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['eg'] = require __DIR__ . '/_datos.php';

const EG_BASE = '/demos/barberia/multi/';

function eg(): array
{
    return $GLOBALS['eg'];
}

function eg_rubro(): array
{
    return $GLOBALS['eg_rubro'];
}

function eg_nota(): string
{
    $r = eg_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan']);
}

/** WhatsApp con la nota de pedido de prueba. */
function eg_wa(string $texto): string
{
    return demo_wa($texto . "\n" . eg_nota());
}

/** Menú de navegación: clave => [archivo, nombre]. */
function eg_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'servicios' => ['servicios.php', 'Servicios'],
        'casa' => ['la-casa.php', 'La casa'],
        'preguntas' => ['preguntas.php', 'Preguntas'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

/** Configuración del carrito en el navegador (_comun/js/carrito.js). */
function eg_config_carrito(): array
{
    $n = eg();
    $catalogo = [];
    foreach ($n['servicios'] as $id => $s) {
        $catalogo[$id] = ['nombre' => $s['nombre'], 'variantes' => $s['variantes']];
    }
    return [
        'clave' => 'demo-barberia-multi-reserva',
        'negocio' => $n['nombre'],
        'nota' => eg_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => [],
        'catalogo' => $catalogo,
    ];
}

/**
 * Días que se ofrecen para la reserva: desde mañana, 14 días abiertos.
 * @return string[] ["Mañana, miércoles 19 de noviembre", "Jueves 20 de noviembre", ...]
 */
function eg_dias(int $cuantos = 14): array
{
    $n = eg();
    $hoy = new DateTimeImmutable('today', new DateTimeZone(DEMO_ZONA_HORARIA));
    $dias = [];
    for ($i = 1; count($dias) < $cuantos && $i <= 30; $i++) {
        $d = $hoy->modify('+' . $i . ' days');
        if (empty($n['horario'][(int) $d->format('N')])) {
            continue;
        }
        $texto = demo_fecha_larga($d);
        $dias[] = $i === 1 ? 'Mañana, ' . $texto : mb_strtoupper(mb_substr($texto, 0, 1)) . mb_substr($texto, 1);
    }
    return $dias;
}

/** Precio más bajo de un servicio. */
function eg_desde(array $s): int
{
    return min($s['variantes']);
}

/** Formulario "Agregar a la reserva" de un servicio o producto, con selector de opción si tiene más de una. */
function eg_agregar(string $id, array $s): string
{
    $variantes = $s['variantes'];
    $primero = array_key_first($variantes);
    $html = '<form class="demo-agregar eg-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(EG_BASE . 'reserva.php') . '" method="get">';
    if (count($variantes) > 1) {
        $html .= '<label class="demo-sr" for="var-' . demo_e($id) . '">Opción de ' . demo_e($s['nombre']) . '</label>';
        $html .= '<select name="variante" id="var-' . demo_e($id) . '">';
        foreach ($variantes as $nombre => $precio) {
            $html .= '<option value="' . demo_e($nombre) . '">' . demo_e($nombre) . ' · ' . demo_e(demo_clp($precio)) . '</option>';
        }
        $html .= '</select>';
    } else {
        $html .= '<input type="hidden" name="variante" value="' . demo_e($primero) . '">';
    }
    $html .= '<span class="eg-agregar__precio" data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($s['nombre']) . ' a la reserva">Agregar</button>';
    return $html . '</form>';
}

/** Emblema: rombo doble con la G, como placa de bronce. */
function eg_emblema(int $tam = 44): string
{
    return '<svg class="eg-emblema" viewBox="0 0 48 48" width="' . $tam . '" height="' . $tam . '" aria-hidden="true" focusable="false">'
        . '<path d="M24 2 46 24 24 46 2 24z" fill="none" stroke="currentColor" stroke-width="1.6"/>'
        . '<path d="M24 8 40 24 24 40 8 24z" fill="none" stroke="currentColor" stroke-width=".8"/>'
        . '<path d="M29.5 19.2a7 7 0 1 0 .9 6.3h-6" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="square"/></svg>';
}

function eg_logo(): string
{
    return '<span class="eg-logo">' . eg_emblema() . '<span class="eg-logo__texto">El Gabinete<small>Barbería de caballeros</small></span></span>';
}

/** Filete art déco que separa secciones. */
function eg_filete(): string
{
    return '<div class="eg-filete" aria-hidden="true"><span></span><svg viewBox="0 0 40 16" width="40" height="16"><path d="M20 1 28 8 20 15 12 8z M2 8h8 M30 8h8" fill="none" stroke="currentColor" stroke-width="1.2"/></svg><span></span></div>';
}

/** Abre la página: head, aviso de demo y cabecera con menú. */
function eg_abrir(string $actual, string $titulo, string $descripcion): void
{
    $n = eg();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Libre+Caslon+Text:ital,wght@0,400;0,700;1,400&display=swap',
        'css' => [EG_BASE . 'estilo.css'],
        'tema' => '#0f2620',
        'icono' => ['G', '#0f2620', '#c9a45c'],
        'clase_body' => 'eg eg-pagina-' . $actual,
    ]);
    demo_aviso(eg_rubro());
    ?>
<header class="eg-cabecera">
  <div class="eg-ancho eg-cabecera__fila">
    <a class="eg-cabecera__marca" href="<?= demo_e(EG_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio"><?= eg_logo() ?></a>
    <nav class="eg-nav" aria-label="Menú principal">
      <?php eg_nav_items($actual); ?>
    </nav>
    <a class="eg-reserva-btn<?= $actual === 'reserva' ? ' is-activo' : '' ?>" href="<?= demo_e(EG_BASE . 'reserva.php') ?>" data-carrito-etiqueta aria-label="Ver tu reserva">
      <svg viewBox="0 0 24 24" width="19" height="19" aria-hidden="true" focusable="false"><rect x="3.5" y="5" width="17" height="15" rx="1" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 10h17M8 3v4M16 3v4" stroke="currentColor" stroke-width="1.6" fill="none"/></svg>
      <span class="eg-reserva-btn__texto">Tu reserva</span>
      <span class="eg-reserva-btn__num" data-carrito-cantidad hidden>0</span>
    </a>
    <details class="eg-menu-movil">
      <summary aria-label="Abrir menú"><span></span><span></span><span></span></summary>
      <nav aria-label="Menú del sitio">
        <?php eg_nav_items($actual); ?>
        <a href="<?= demo_e(demo_tel()) ?>">Llamar</a>
      </nav>
    </details>
  </div>
</header>
<?php
}

function eg_nav_items(string $actual): void
{
    foreach (eg_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(EG_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>', demo_e($nombre), '</a>';
    }
}

/** Encabezado de página interior (todas menos el inicio). */
function eg_titular(string $sobre, string $titulo, string $bajada, string $foto = '', string $alt = ''): void
{
    ?>
<header class="eg-titular<?= $foto !== '' ? ' eg-titular--foto' : '' ?>">
  <?php if ($foto !== ''): ?><?= demo_img($foto, $alt, ['lazy' => false, 'ancho' => 1400, 'alto' => 600, 'anchos' => [700, 1100, 1600], 'sizes' => '100vw', 'clase' => 'eg-titular__fondo']) ?><?php endif; ?>
  <div class="eg-ancho eg-titular__texto">
    <p class="eg-sobre"><?= demo_e($sobre) ?></p>
    <h1><?= demo_e($titulo) ?></h1>
    <p class="eg-titular__bajada"><?= demo_e($bajada) ?></p>
  </div>
</header>
<?php
}

/** Cierra la página: pie, configuración del carrito y scripts. */
function eg_cerrar(array $scripts = []): void
{
    $n = eg();
    $config = json_encode(eg_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="eg-pie">
  <div class="eg-ancho">
    <?= eg_filete() ?>
    <div class="eg-pie__grid">
      <div>
        <p class="eg-pie__logo"><?= eg_logo() ?></p>
        <p><?= demo_e($n['eslogan']) ?>. <?= demo_e(ucfirst($n['barrio'])) ?>, <?= demo_e($n['comuna']) ?>, Santiago.</p>
      </div>
      <div>
        <p class="eg-pie__titulo">Horario</p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario eg-pie__horario'); ?>
      </div>
      <div>
        <p class="eg-pie__titulo">Reservas</p>
        <p><a href="<?= demo_e(EG_BASE . 'servicios.php') ?>">Elegir servicios</a></p>
        <p><a href="<?= demo_e(eg_wa('Hola, quiero reservar una hora en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Reservar por WhatsApp</a></p>
        <p><a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
      </div>
    </div>
    <?php demo_aviso_pie(eg_rubro()); ?>
  </div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo "</body>\n</html>\n";
}
