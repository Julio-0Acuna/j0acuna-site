<?php
declare(strict_types=1);

// Plantilla común de las páginas de Matiz, estudio de color (Multi-page).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['mz_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['mz'] = require __DIR__ . '/_datos.php';

const MZ_BASE = '/demos/peluqueria/multi/';

function mz(): array
{
    return $GLOBALS['mz'];
}

function mz_rubro(): array
{
    return $GLOBALS['mz_rubro'];
}

function mz_nota(): string
{
    $r = mz_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan'], 'Reserva');
}

/** WhatsApp con la nota de reserva de prueba. */
function mz_wa(string $texto): string
{
    return demo_wa($texto . "\n" . mz_nota());
}

/** Menú de navegación: clave => [archivo, nombre]. */
function mz_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'servicios' => ['servicios.php', 'Servicios'],
        'estudio' => ['estudio.php', 'El estudio'],
        'preguntas' => ['preguntas.php', 'Preguntas'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

/** Precio "desde" de un servicio. */
function mz_desde(array $s): int
{
    return min($s['variantes']);
}

/** Duración legible: 210 → "3 h 30 min". */
function mz_duracion(int $min): string
{
    $h = intdiv($min, 60);
    $m = $min % 60;
    return trim(($h > 0 ? $h . ' h' : '') . ($m > 0 ? ' ' . $m . ' min' : ''));
}

/**
 * Configuración del carrito en el navegador (_comun/js/carrito.js): una reserva de hora. La entrega solo
 * aplica a los productos (retiro en el estudio el día de la hora o despacho), así que sus líneas lo dicen.
 */
function mz_config_carrito(): array
{
    $n = mz();
    $catalogo = [];
    foreach ($n['servicios'] as $id => $s) {
        $catalogo[$id] = ['nombre' => $s['nombre'], 'variantes' => $s['variantes']];
    }
    return [
        'clave' => 'demo-peluqueria-multi-reserva',
        'negocio' => $n['nombre_largo'],
        'nota' => mz_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_despacho'],
        'catalogo' => $catalogo,
        'textos' => [
            'saludo' => 'Hola, quiero reservar hora en {negocio}:',
            'retiro' => 'Lugar: en el estudio',
            'despacho' => 'Productos: despacho a {direccion}, {comuna}',
            'costo_despacho' => 'Despacho de productos a {comuna}: {costo}',
            'gratis_retiro' => 'Sin costo',
        ],
    ];
}

/**
 * Días que se ofrecen para la reserva: desde mañana, 14 días abiertos.
 * @return array<string, string> ['2026-11-20' => 'Mañana, jueves 20 de noviembre', ...]
 */
function mz_dias(int $cuantos = 14): array
{
    $n = mz();
    $hoy = new DateTimeImmutable('today', new DateTimeZone(DEMO_ZONA_HORARIA));
    $dias = [];
    for ($i = 1; count($dias) < $cuantos && $i <= 40; $i++) {
        $d = $hoy->modify('+' . $i . ' days');
        if (empty($n['horario'][(int) $d->format('N')])) {
            continue;
        }
        $texto = demo_fecha_larga($d);
        $dias[$d->format('Y-m-d')] = $i === 1 ? 'Mañana, ' . $texto : mb_strtoupper(mb_substr($texto, 0, 1)) . mb_substr($texto, 1);
    }
    return $dias;
}

/** Formulario "Agregar a la reserva" de un servicio o producto, con selector de largo de pelo. */
function mz_agregar(string $id, array $s): string
{
    $html = '<form class="demo-agregar mz-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(MZ_BASE . 'reserva.php') . '" method="get">';
    $variantes = $s['variantes'];
    $primero = array_key_first($variantes);
    if (count($variantes) > 1) {
        $html .= '<label class="demo-sr" for="var-' . demo_e($id) . '">Largo de pelo para ' . demo_e($s['nombre']) . '</label>';
        $html .= '<select name="variante" id="var-' . demo_e($id) . '">';
        foreach ($variantes as $nombre => $precio) {
            $html .= '<option value="' . demo_e($nombre) . '">' . demo_e($nombre) . ' · ' . demo_e(demo_clp($precio)) . '</option>';
        }
        $html .= '</select>';
    } else {
        $html .= '<input type="hidden" name="variante" value="' . demo_e($primero) . '">';
    }
    $html .= '<span class="mz-agregar__precio">' . (count($variantes) > 1 ? '<small>desde</small> ' : '') . '<span data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span></span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($s['nombre']) . ' a tu reserva">Agregar</button>';
    return $html . '</form>';
}

/** Marca: tres mechones de color y el nombre en itálica. */
function mz_logo(string $clase = ''): string
{
    return '<span class="mz-logo ' . demo_e($clase) . '">'
        . '<span class="mz-logo__tonos" aria-hidden="true"><i></i><i></i><i></i></span>'
        . '<span class="mz-logo__texto">Matiz<small>estudio de color</small></span></span>';
}

/** Abre la página: head, aviso de demo y cabecera con menú. */
function mz_abrir(string $actual, string $titulo, string $descripcion): void
{
    $n = mz();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre_largo'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,300;0,6..72,400;0,6..72,500;1,6..72,300;1,6..72,400&family=Urbanist:wght@400;500;600;700&display=swap',
        'css' => [MZ_BASE . 'estilo.css'],
        'tema' => '#f4efe7',
        'icono' => ['M', '#1d1916', '#e2b26f'],
        'clase_body' => 'mz mz-pagina-' . $actual,
    ]);
    demo_aviso(mz_rubro());
    ?>
<header class="mz-cabecera">
  <div class="mz-ancho mz-cabecera__fila">
    <a class="mz-cabecera__marca" href="<?= demo_e(MZ_BASE) ?>" aria-label="<?= demo_e($n['nombre_largo']) ?>, inicio"><?= mz_logo() ?></a>
    <nav class="mz-nav" aria-label="Menú principal">
      <?php mz_nav_items($actual); ?>
    </nav>
    <a class="mz-reserva-btn<?= $actual === 'reserva' ? ' is-activo' : '' ?>" href="<?= demo_e(MZ_BASE . 'reserva.php') ?>" data-carrito-etiqueta aria-label="Ver tu reserva">
      <span class="mz-reserva-btn__texto">Tu reserva</span>
      <span class="mz-reserva-btn__num" data-carrito-cantidad hidden>0</span>
    </a>
    <details class="mz-menu-movil">
      <summary aria-label="Abrir menú"><span></span><span></span></summary>
      <nav aria-label="Menú del sitio">
        <?php mz_nav_items($actual); ?>
        <a href="<?= demo_e(demo_tel()) ?>">Llamar</a>
      </nav>
    </details>
  </div>
</header>
<?php
}

function mz_nav_items(string $actual): void
{
    foreach (mz_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(MZ_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>', demo_e($nombre), '</a>';
    }
}

/** Encabezado de página interior (todas menos el inicio), con número de "edición". */
function mz_titular(string $numero, string $sobre, string $titulo, string $bajada): void
{
    ?>
<header class="mz-titular">
  <div class="mz-ancho mz-titular__grid">
    <p class="mz-titular__num" aria-hidden="true"><?= demo_e($numero) ?></p>
    <div>
      <p class="mz-sobre"><?= demo_e($sobre) ?></p>
      <h1><?= $titulo ?></h1>
      <p class="mz-titular__bajada"><?= demo_e($bajada) ?></p>
    </div>
  </div>
</header>
<?php
}

/** Cierra la página: pie, configuración del carrito y scripts. */
function mz_cerrar(array $scripts = []): void
{
    $n = mz();
    $config = json_encode(mz_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="mz-pie">
  <div class="mz-ancho">
    <p class="mz-pie__marca" aria-hidden="true">Matiz</p>
    <div class="mz-pie__grid">
      <div>
        <p class="mz-pie__titulo">El estudio</p>
        <p><?= demo_e($n['eslogan']) ?> desde <?= (int) $n['desde'] ?>. <?= demo_e($n['comuna']) ?>, Santiago.</p>
        <p><a href="<?= demo_e(mz_wa('Hola, tengo una consulta para ' . $n['nombre_largo'] . '.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
      </div>
      <div>
        <p class="mz-pie__titulo">Horario</p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario mz-pie__horario'); ?>
      </div>
      <div>
        <p class="mz-pie__titulo">Páginas</p>
        <ul class="mz-pie__lista">
          <?php foreach (mz_paginas() as [$archivo, $nombre]): ?><li><a href="<?= demo_e(MZ_BASE . $archivo) ?>"><?= demo_e($nombre) ?></a></li><?php endforeach; ?>
          <li><a href="<?= demo_e(MZ_BASE . 'reserva.php') ?>">Tu reserva</a></li>
        </ul>
      </div>
    </div>
    <?php demo_aviso_pie(mz_rubro()); ?>
  </div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo "</body>\n</html>\n";
}
