<?php
declare(strict_types=1);

// Plantilla común de las páginas de Bruma, spa canino (Multi-page).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['br_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['br'] = require __DIR__ . '/_datos.php';

const BR_BASE = '/demos/peluqueria-canina/multi/';

function br(): array
{
    return $GLOBALS['br'];
}

function br_rubro(): array
{
    return $GLOBALS['br_rubro'];
}

function br_nota(): string
{
    $r = br_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan'], 'Reserva');
}

/** WhatsApp con la nota de reserva de prueba. */
function br_wa(string $texto): string
{
    return demo_wa($texto . "\n" . br_nota());
}

/** Menú de navegación: clave => [archivo, nombre]. */
function br_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'rituales' => ['rituales.php', 'Rituales'],
        'casa' => ['casa.php', 'La casa'],
        'preguntas' => ['preguntas.php', 'Preguntas'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

/** Precio "desde" de un servicio. */
function br_desde(array $s): int
{
    return min($s['variantes']);
}

/**
 * Configuración del carrito en el navegador (_comun/js/carrito.js): la reserva de un ritual. "Retiro" = lo llevas tú
 * al spa; "despacho" = traslado ida y vuelta desde tu casa, con costo por comuna (gratis desde traslado_gratis).
 */
function br_config_carrito(): array
{
    $n = br();
    $catalogo = [];
    foreach ($n['servicios'] as $id => $s) {
        $catalogo[$id] = ['nombre' => $s['nombre'], 'variantes' => $s['variantes']];
    }
    return [
        'clave' => 'demo-peluqueria-canina-multi-reserva',
        'negocio' => $n['nombre'],
        'nota' => br_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['traslado'],
        'gratis_desde' => $n['traslado_gratis'],
        'catalogo' => $catalogo,
        'textos' => [
            'saludo' => 'Hola, quiero reservar en {negocio}:',
            'retiro' => 'Llegada: lo llevo yo al spa',
            'despacho' => 'Llegada: traslado ida y vuelta desde {direccion}, {comuna}',
            'costo_despacho' => 'Traslado ida y vuelta ({comuna}): {costo}',
            'gratis_retiro' => 'Sin traslado',
            'gratis' => 'Gratis',
            'elige_comuna' => 'Elige tu comuna',
            'falta_gratis' => 'Te faltan {falta} para el traslado gratis.',
            'agregado' => 'Agregado a tu reserva: {producto}',
            'ver_pedido' => 'Ver tu reserva ({n} servicios)',
            'ver_pedido_uno' => 'Ver tu reserva (1 servicio)',
        ],
    ];
}

/**
 * Días que se ofrecen para la reserva: desde mañana, 12 días con atención.
 * @return string[] ["Mañana, miércoles 25 de noviembre", "Jueves 26 de noviembre", ...]
 */
function br_dias(int $cuantos = 12): array
{
    $n = br();
    $hoy = new DateTimeImmutable('today', new DateTimeZone(DEMO_ZONA_HORARIA));
    $dias = [];
    for ($i = 1; count($dias) < $cuantos && $i <= 30; $i++) {
        $d = $hoy->modify('+' . $i . ' days');
        $dia = (int) $d->format('N');
        if (empty($n['horario'][$dia])) {
            continue;
        }
        $texto = DEMO_DIAS[$dia] . ' ' . $d->format('j') . ' de ' . DEMO_MESES[(int) $d->format('n')];
        $dias[] = $i === 1 ? 'Mañana, ' . mb_strtolower($texto) : $texto;
    }
    return $dias;
}

/** Formulario "Agregar a la reserva" de un servicio, con selector de tamaño u opción. */
function br_agregar(string $id, array $s): string
{
    $html = '<form class="demo-agregar br-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(BR_BASE . 'reserva.php') . '" method="get">';
    $variantes = $s['variantes'];
    $primero = array_key_first($variantes);
    if (count($variantes) > 1) {
        $html .= '<label class="demo-sr" for="var-' . demo_e($id) . '">Tamaño u opción de ' . demo_e($s['nombre']) . '</label>';
        $html .= '<select name="variante" id="var-' . demo_e($id) . '">';
        foreach ($variantes as $nombre => $precio) {
            $html .= '<option value="' . demo_e($nombre) . '">' . demo_e($nombre) . ' · ' . demo_e(demo_clp($precio)) . '</option>';
        }
        $html .= '</select>';
    } else {
        $html .= '<input type="hidden" name="variante" value="' . demo_e($primero) . '">';
    }
    $html .= '<span class="br-agregar__precio"><small>' . (count($variantes) > 1 ? 'desde' : ($primero === 'Única' ? 'valor' : demo_e(mb_strtolower($primero)))) . '</small> <span data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span></span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($s['nombre']) . ' a la reserva">Agregar</button>';
    return $html . '</form>';
}

/** Marca: tres líneas de bruma y el nombre. */
function br_logo(string $clase = ''): string
{
    return '<span class="br-logo ' . demo_e($clase) . '">'
        . '<svg viewBox="0 0 40 28" width="34" height="24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M3 7c4-3.4 8-3.4 12 0s8 3.4 12 0 7-3 10-1"/><path d="M3 14c4-3.4 8-3.4 12 0s8 3.4 12 0 7-3 10-1" opacity=".7"/><path d="M3 21c4-3.4 8-3.4 12 0s8 3.4 12 0 7-3 10-1" opacity=".45"/></svg>'
        . '<span class="br-logo__texto">Bruma<small>spa canino</small></span></span>';
}

/** Abre la página: head, aviso de demo y cabecera con menú. */
function br_abrir(string $actual, string $titulo, string $descripcion): void
{
    $n = br();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Tenor+Sans&display=swap',
        'css' => [BR_BASE . 'estilo.css'],
        'tema' => '#e6ebee',
        'icono' => ['B', '#22313a', '#efc4af'],
        'clase_body' => 'br br-pagina-' . $actual,
    ]);
    demo_aviso(br_rubro());
    ?>
<header class="br-cabecera">
  <div class="br-ancho br-cabecera__fila">
    <nav class="br-nav" aria-label="Menú principal">
      <?php br_nav_items($actual); ?>
    </nav>
    <a class="br-cabecera__marca" href="<?= demo_e(BR_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio"><?= br_logo() ?></a>
    <div class="br-cabecera__acciones">
      <a class="br-reserva-btn<?= $actual === 'reserva' ? ' is-activo' : '' ?>" href="<?= demo_e(BR_BASE . 'reserva.php') ?>" data-carrito-etiqueta aria-label="Ver tu reserva">
        <span class="br-reserva-btn__texto">Tu reserva</span>
        <span class="br-reserva-btn__num" data-carrito-cantidad hidden>0</span>
      </a>
      <details class="br-menu-movil">
        <summary aria-label="Abrir menú"><span></span><span></span></summary>
        <nav aria-label="Menú del sitio">
          <?php br_nav_items($actual); ?>
          <a href="<?= demo_e(demo_tel()) ?>">Llamar</a>
        </nav>
      </details>
    </div>
  </div>
</header>
<?php
}

function br_nav_items(string $actual): void
{
    foreach (br_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(BR_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>', demo_e($nombre), '</a>';
    }
}

/** Encabezado de página interior (todas menos el inicio). $titulo admite <em> (se escapa todo lo demás). */
function br_titular(string $numero, string $sobre, string $titulo, string $bajada): void
{
    $tituloHtml = str_replace(['&lt;em&gt;', '&lt;/em&gt;'], ['<em>', '</em>'], demo_e($titulo));
    ?>
<header class="br-titular">
  <div class="br-ancho">
    <p class="br-sobre"><span class="br-sobre__num"><?= demo_e($numero) ?></span> <?= demo_e($sobre) ?></p>
    <h1><?= $tituloHtml ?></h1>
    <p class="br-titular__bajada"><?= demo_e($bajada) ?></p>
  </div>
</header>
<?php
}

/** Cierra la página: pie, configuración del carrito y scripts. */
function br_cerrar(array $scripts = []): void
{
    $n = br();
    $config = json_encode(br_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="br-pie">
  <div class="br-ancho br-pie__grid">
    <div>
      <p class="br-pie__logo"><?= br_logo() ?></p>
      <p><?= demo_e($n['eslogan']) ?>. Rituales de baño, corte a tijera, deslanado y traslado ida y vuelta desde tu casa.</p>
    </div>
    <div>
      <p class="br-pie__titulo">Horario</p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario br-pie__horario'); ?>
      <p class="br-pie__nota">Jueves en la mañana: solo gatos.</p>
    </div>
    <div>
      <p class="br-pie__titulo">Reservas</p>
      <p><a href="<?= demo_e(BR_BASE . 'rituales.php') ?>">Elegir un ritual</a></p>
      <p><a href="<?= demo_e(br_wa('Hola, quiero reservar en ' . $n['nombre'] . '. Mi perro se llama: ')) ?>" target="_blank" rel="noopener">Reservar por WhatsApp</a></p>
      <p><a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
    </div>
  </div>
  <div class="br-ancho"><?php demo_aviso_pie(br_rubro()); ?></div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo "</body>\n</html>\n";
}
