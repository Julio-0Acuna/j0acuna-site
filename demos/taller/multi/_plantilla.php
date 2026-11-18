<?php
declare(strict_types=1);

// Plantilla común de las páginas de Vértice Garage (Multi-page).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['vg_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['vg'] = require __DIR__ . '/_datos.php';

const VG_BASE = '/demos/taller/multi/';

function vg(): array
{
    return $GLOBALS['vg'];
}

function vg_rubro(): array
{
    return $GLOBALS['vg_rubro'];
}

function vg_nota(): string
{
    $r = vg_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan']);
}

/** WhatsApp con la nota de pedido de prueba. */
function vg_wa(string $texto): string
{
    return demo_wa($texto . "\n" . vg_nota());
}

/** Menú de navegación: clave => [archivo, nombre]. */
function vg_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'servicios' => ['servicios.php', 'Servicios'],
        'taller' => ['taller.php', 'El taller'],
        'preguntas' => ['preguntas.php', 'Preguntas'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

/** Precio "desde" de un servicio. */
function vg_desde(array $s): int
{
    return min($s['variantes']);
}

/** Configuración del carrito en el navegador (_comun/js/carrito.js). */
function vg_config_carrito(): array
{
    $n = vg();
    $catalogo = [];
    foreach ($n['servicios'] as $id => $s) {
        $catalogo[$id] = ['nombre' => $s['nombre'], 'variantes' => $s['variantes']];
    }
    return [
        'clave' => 'demo-taller-multi-reserva',
        'negocio' => $n['nombre'],
        'nota' => vg_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_despacho'],
        'catalogo' => $catalogo,
    ];
}

/**
 * Días que se ofrecen para la reserva: desde mañana, 12 días, sin los días cerrados.
 * @return string[] ["Mié 19/11", ...]
 */
function vg_dias(int $cuantos = 12): array
{
    $n = vg();
    $hoy = new DateTimeImmutable('today', new DateTimeZone(DEMO_ZONA_HORARIA));
    $dias = [];
    for ($i = 1; count($dias) < $cuantos && $i <= 30; $i++) {
        $d = $hoy->modify('+' . $i . ' days');
        if (empty($n['horario'][(int) $d->format('N')])) {
            continue;
        }
        $texto = DEMO_DIAS[(int) $d->format('N')] . ' ' . $d->format('j') . ' de ' . DEMO_MESES[(int) $d->format('n')];
        $dias[] = $i === 1 ? 'Mañana, ' . mb_strtolower($texto) : $texto;
    }
    return $dias;
}

/** Formulario "Agregar a la reserva" de un servicio, con selector de tipo de vehículo. */
function vg_agregar(string $id, array $s): string
{
    $html = '<form class="demo-agregar vg-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(VG_BASE . 'reserva.php') . '" method="get">';
    $variantes = $s['variantes'];
    $primero = array_key_first($variantes);
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
    $html .= '<span class="vg-agregar__precio"><small>desde</small> <span data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span></span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($s['nombre']) . ' a la reserva">Agregar</button>';
    return $html . '</form>';
}

/** Marca: el vértice (dos trazos que se juntan) y el nombre. */
function vg_logo(string $clase = ''): string
{
    return '<span class="vg-logo ' . demo_e($clase) . '">'
        . '<svg viewBox="0 0 40 40" width="34" height="34" aria-hidden="true" focusable="false"><path d="M5 9 20 33 35 9" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linejoin="miter"/><path d="M13 9 20 21 27 9" fill="none" stroke="#35d6f5" stroke-width="3.2"/></svg>'
        . '<span class="vg-logo__texto">Vértice<small>Garage</small></span></span>';
}

/**
 * Abre la página: head, aviso de demo y cabecera con menú.
 */
function vg_abrir(string $actual, string $titulo, string $descripcion): void
{
    $n = vg();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500&family=Geist:wght@300;400;500;600&family=Michroma&display=swap',
        'css' => [VG_BASE . 'estilo.css'],
        'tema' => '#0c0e11',
        'icono' => ['V', '#0c0e11', '#35d6f5'],
        'clase_body' => 'vg vg-pagina-' . $actual,
    ]);
    demo_aviso(vg_rubro());
    ?>
<header class="vg-cabecera">
  <div class="vg-ancho vg-cabecera__fila">
    <a class="vg-cabecera__marca" href="<?= demo_e(VG_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio"><?= vg_logo() ?></a>
    <nav class="vg-nav" aria-label="Menú principal">
      <?php vg_nav_items($actual); ?>
    </nav>
    <a class="vg-reserva-btn<?= $actual === 'reserva' ? ' is-activo' : '' ?>" href="<?= demo_e(VG_BASE . 'reserva.php') ?>" data-carrito-etiqueta aria-label="Ver tu reserva">
      <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><rect x="3.5" y="5" width="17" height="15" rx="2" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 10h17M8 3v4M16 3v4" stroke="currentColor" stroke-width="1.7" fill="none"/></svg>
      <span class="vg-reserva-btn__texto">Tu reserva</span>
      <span class="vg-reserva-btn__num" data-carrito-cantidad hidden>0</span>
    </a>
    <details class="vg-menu-movil">
      <summary aria-label="Abrir menú"><span></span><span></span></summary>
      <nav aria-label="Menú del sitio">
        <?php vg_nav_items($actual); ?>
        <a href="<?= demo_e(demo_tel()) ?>">Llamar</a>
      </nav>
    </details>
  </div>
</header>
<?php
}

function vg_nav_items(string $actual): void
{
    foreach (vg_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(VG_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>', demo_e($nombre), '</a>';
    }
}

/** Encabezado de página interior (todas menos el inicio). */
function vg_titular(string $sobre, string $titulo, string $bajada): void
{
    ?>
<header class="vg-titular">
  <div class="vg-ancho">
    <p class="vg-sobre"><?= demo_e($sobre) ?></p>
    <h1><?= demo_e($titulo) ?></h1>
    <p class="vg-titular__bajada"><?= demo_e($bajada) ?></p>
  </div>
</header>
<?php
}

/** Cierra la página: pie, configuración del carrito y scripts. */
function vg_cerrar(array $scripts = []): void
{
    $n = vg();
    $config = json_encode(vg_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="vg-pie">
  <div class="vg-ancho vg-pie__grid">
    <div>
      <p class="vg-pie__logo"><?= vg_logo() ?></p>
      <p><?= demo_e($n['eslogan']) ?>. <?= demo_e($n['comuna']) ?>, Santiago.</p>
    </div>
    <div>
      <p class="vg-pie__titulo">Horario</p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario vg-pie__horario'); ?>
    </div>
    <div>
      <p class="vg-pie__titulo">Reserva</p>
      <p><a href="<?= demo_e(VG_BASE . 'servicios.php') ?>">Elegir servicios</a></p>
      <p><a href="<?= demo_e(vg_wa('Hola, quiero reservar una hora en ' . $n['nombre'] . '. Patente y modelo: ')) ?>" target="_blank" rel="noopener">Reservar por WhatsApp</a></p>
      <p><a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
    </div>
  </div>
  <div class="vg-ancho"><?php demo_aviso_pie(vg_rubro()); ?></div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo "</body>\n</html>\n";
}
