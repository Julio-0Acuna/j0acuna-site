<?php
declare(strict_types=1);

// Plantilla común de las páginas de Clínica Veterinaria Ciprés (Multi-page).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['cp_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['cp'] = require __DIR__ . '/_datos.php';

const CP_BASE = '/demos/veterinaria/multi/';

function cp(): array
{
    return $GLOBALS['cp'];
}

function cp_rubro(): array
{
    return $GLOBALS['cp_rubro'];
}

function cp_nota(): string
{
    $r = cp_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan']);
}

/** WhatsApp con la nota de pedido de prueba. */
function cp_wa(string $texto): string
{
    return demo_wa($texto . "\n" . cp_nota());
}

/** Menú de navegación: clave => [archivo, nombre]. */
function cp_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'especialidades' => ['especialidades.php', 'Especialidades'],
        'equipo' => ['equipo.php', 'Equipo'],
        'preguntas' => ['preguntas.php', 'Preguntas'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

/** Precio "desde" de un servicio. */
function cp_desde(array $s): int
{
    return min($s['variantes']);
}

/** Configuración del carrito en el navegador (_comun/js/carrito.js). */
function cp_config_carrito(): array
{
    $n = cp();
    $catalogo = [];
    foreach ($n['servicios'] as $id => $s) {
        $catalogo[$id] = ['nombre' => $s['nombre'], 'variantes' => $s['variantes']];
    }
    return [
        'clave' => 'demo-veterinaria-multi-reserva',
        'negocio' => $n['nombre'],
        'nota' => cp_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_domicilio'],
        'catalogo' => $catalogo,
    ];
}

/**
 * Días que se ofrecen para la reserva: desde mañana, 12 días con atención (sin domingos: solo urgencias).
 * @return string[] ["Mañana, miércoles 19 de noviembre", "Jueves 20 de noviembre", ...]
 */
function cp_dias(int $cuantos = 12): array
{
    $n = cp();
    $hoy = new DateTimeImmutable('today', new DateTimeZone(DEMO_ZONA_HORARIA));
    $dias = [];
    for ($i = 1; count($dias) < $cuantos && $i <= 30; $i++) {
        $d = $hoy->modify('+' . $i . ' days');
        $dia = (int) $d->format('N');
        if (empty($n['horario'][$dia]) || in_array($dia, $n['sin_reservas'], true)) {
            continue;
        }
        $texto = DEMO_DIAS[$dia] . ' ' . $d->format('j') . ' de ' . DEMO_MESES[(int) $d->format('n')];
        $dias[] = $i === 1 ? 'Mañana, ' . mb_strtolower($texto) : $texto;
    }
    return $dias;
}

/** Formulario "Agregar a la reserva" de un servicio, con selector de especie o tamaño. */
function cp_agregar(string $id, array $s): string
{
    $html = '<form class="demo-agregar cp-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(CP_BASE . 'reserva.php') . '" method="get">';
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
    $html .= '<span class="cp-agregar__precio"><small>desde</small> <span data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span></span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($s['nombre']) . ' a la reserva">Agregar</button>';
    return $html . '</form>';
}

/** Marca: un ciprés (llama alargada) y el nombre. */
function cp_logo(string $clase = ''): string
{
    return '<span class="cp-logo ' . demo_e($clase) . '">'
        . '<svg viewBox="0 0 32 44" width="26" height="36" aria-hidden="true" focusable="false"><path d="M16 2c5.5 7 8.5 14.5 8.5 23 0 8.4-3.8 13.6-8.5 13.6S7.5 33.4 7.5 25C7.5 16.5 10.5 9 16 2z" fill="currentColor"/><path d="M16 12v30" stroke="#c9c4f6" stroke-width="2.2" stroke-linecap="round"/><path d="M16 22l4.5-4M16 29l-4.5-4" stroke="#c9c4f6" stroke-width="2" stroke-linecap="round"/></svg>'
        . '<span class="cp-logo__texto">Ciprés<small>clínica veterinaria</small></span></span>';
}

/** Abre la página: head, aviso de demo y cabecera con menú. */
function cp_abrir(string $actual, string $titulo, string $descripcion): void
{
    $n = cp();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,500;0,8..60,600;1,8..60,400;1,8..60,500&display=swap',
        'css' => [CP_BASE . 'estilo.css'],
        'tema' => '#16382f',
        'icono' => ['C', '#16382f', '#c9c4f6'],
        'clase_body' => 'cp cp-pagina-' . $actual,
    ]);
    demo_aviso(cp_rubro());
    ?>
<header class="cp-cabecera">
  <div class="cp-ancho cp-cabecera__fila">
    <a class="cp-cabecera__marca" href="<?= demo_e(CP_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio"><?= cp_logo() ?></a>
    <nav class="cp-nav" aria-label="Menú principal">
      <?php cp_nav_items($actual); ?>
    </nav>
    <a class="cp-urgencia-link" href="<?= demo_e(demo_tel()) ?>"><span aria-hidden="true"></span>Urgencias</a>
    <a class="cp-reserva-btn<?= $actual === 'reserva' ? ' is-activo' : '' ?>" href="<?= demo_e(CP_BASE . 'reserva.php') ?>" data-carrito-etiqueta aria-label="Ver tu reserva">
      <svg viewBox="0 0 24 24" width="19" height="19" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3.5" y="5" width="17" height="15" rx="3"/><path d="M3.5 10h17M8 3v4M16 3v4"/></svg>
      <span class="cp-reserva-btn__texto">Tu reserva</span>
      <span class="cp-reserva-btn__num" data-carrito-cantidad hidden>0</span>
    </a>
    <details class="cp-menu-movil">
      <summary aria-label="Abrir menú"><span></span><span></span></summary>
      <nav aria-label="Menú del sitio">
        <?php cp_nav_items($actual); ?>
        <a href="<?= demo_e(demo_tel()) ?>">Llamar por una urgencia</a>
      </nav>
    </details>
  </div>
</header>
<?php
}

function cp_nav_items(string $actual): void
{
    foreach (cp_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(CP_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>', demo_e($nombre), '</a>';
    }
}

/** Encabezado de página interior (todas menos el inicio). */
function cp_titular(string $sobre, string $titulo, string $bajada): void
{
    ?>
<header class="cp-titular">
  <div class="cp-ancho">
    <p class="cp-sobre"><?= demo_e($sobre) ?></p>
    <h1><?= demo_e($titulo) ?></h1>
    <p class="cp-titular__bajada"><?= demo_e($bajada) ?></p>
  </div>
</header>
<?php
}

/** Banda de urgencias (horario claro, sin prometer 24 horas). */
function cp_urgencias(): void
{
    ?>
<section class="cp-urgencias" aria-labelledby="urgencias-titulo">
  <div class="cp-ancho cp-urgencias__grid">
    <div>
      <p class="cp-sobre">Urgencias</p>
      <h2 id="urgencias-titulo">Sin hora, en todo nuestro horario. <em>También los domingos en la mañana.</em></h2>
    </div>
    <div class="cp-urgencias__texto">
      <dl class="cp-urgencias__horas">
        <div><dt>Lunes a viernes</dt><dd>09:00 – 21:00</dd></div>
        <div><dt>Sábado</dt><dd>09:00 – 18:00</dd></div>
        <div><dt>Domingo</dt><dd>10:00 – 14:00</dd></div>
      </dl>
      <p><strong>No somos una clínica 24 horas.</strong> De noche, llámanos: la grabación te indica el hospital veterinario de turno más cercano, y al día siguiente seguimos el caso con su ficha.</p>
      <a class="cp-btn cp-btn--lavanda" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel(18) ?> Llamar a urgencias</a>
    </div>
  </div>
</section>
<?php
}

/** Cierra la página: pie, configuración del carrito y scripts. */
function cp_cerrar(array $scripts = []): void
{
    $n = cp();
    $config = json_encode(cp_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="cp-pie">
  <div class="cp-ancho cp-pie__grid">
    <div>
      <p class="cp-pie__logo"><?= cp_logo() ?></p>
      <p><?= demo_e($n['eslogan']) ?>. Medicina general, felinos, dermatología, cardiología, imagenología, odontología, cirugía y exóticos.</p>
    </div>
    <div>
      <p class="cp-pie__titulo">Horario</p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario cp-pie__horario'); ?>
      <p class="cp-pie__nota">Domingo: solo urgencias.</p>
    </div>
    <div>
      <p class="cp-pie__titulo">Reserva</p>
      <p><a href="<?= demo_e(CP_BASE . 'especialidades.php') ?>">Elegir especialidad</a></p>
      <p><a href="<?= demo_e(cp_wa('Hola, quiero reservar una hora en ' . $n['nombre'] . '. Mi mascota se llama: ')) ?>" target="_blank" rel="noopener">Reservar por WhatsApp</a></p>
      <p><a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
    </div>
  </div>
  <div class="cp-ancho"><?php demo_aviso_pie(cp_rubro()); ?></div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo "</body>\n</html>\n";
}
