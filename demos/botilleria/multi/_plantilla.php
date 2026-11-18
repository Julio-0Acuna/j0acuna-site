<?php
declare(strict_types=1);

// Plantilla común de las páginas de Cava Ocre (Multi-page): cabecera, pie, carrito y catas.
require_once dirname(__DIR__) . '/_edad.php';

$GLOBALS['co_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['co'] = require __DIR__ . '/_datos.php';

const CO_BASE = '/demos/botilleria/multi/';
const CO_EDAD = 'demo-botilleria-multi-edad';

function co(): array
{
    return $GLOBALS['co'];
}

function co_rubro(): array
{
    return $GLOBALS['co_rubro'];
}

function co_nota(): string
{
    $r = co_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan']);
}

/** WhatsApp con la nota de pedido de prueba. */
function co_wa(string $texto): string
{
    return demo_wa($texto . "\n" . co_nota());
}

/** Menú de navegación: clave => [archivo, nombre]. */
function co_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'catalogo' => ['catalogo.php', 'Vinos y destilados'],
        'catas' => ['catas.php', 'Catas'],
        'preguntas' => ['preguntas.php', 'Preguntas'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

/** Precio "desde" de un producto. */
function co_desde(array $p): int
{
    return min($p['variantes']);
}

/**
 * Próximas catas con su fecha calculada desde hoy (día de la semana 1..7 + semanas de diferencia),
 * para que la agenda de la demo nunca quede en el pasado. Cada cata trae 'fecha' y 'variante'
 * (la etiqueta que va al carrito, ej. "Jue 20/11 · 19:30").
 */
function co_catas(?DateTimeImmutable $hoy = null): array
{
    $hoy ??= new DateTimeImmutable('today', new DateTimeZone(DEMO_ZONA_HORARIA));
    $catas = [];
    foreach (co()['catas'] as $c) {
        $dias = ((int) $c['dia'] - (int) $hoy->format('N') + 7) % 7;
        $dias = $dias === 0 ? 7 : $dias; // nunca hoy: siempre con al menos un día para reservar
        $fecha = $hoy->modify('+' . ($dias + 7 * (int) $c['semana']) . ' days');
        $corta = demo_fecha_corta($fecha);
        $c['fecha'] = $fecha;
        $c['variante'] = mb_strtoupper(mb_substr($corta, 0, 1)) . mb_substr($corta, 1) . ' · ' . $c['hora'];
        $catas[] = $c;
    }
    usort($catas, static fn (array $a, array $b): int => $a['fecha'] <=> $b['fecha']);
    return $catas;
}

/** Configuración del carrito en el navegador (_comun/js/carrito.js): productos y catas. */
function co_config_carrito(): array
{
    $n = co();
    $catalogo = [];
    foreach ($n['productos'] as $id => $p) {
        $catalogo[$id] = ['nombre' => $p['nombre'], 'variantes' => $p['variantes']];
    }
    foreach (co_catas() as $c) {
        $catalogo[$c['id']] = ['nombre' => 'Cata «' . $c['titulo'] . '», 1 persona', 'variantes' => [$c['variante'] => $c['precio']]];
    }
    return [
        'clave' => 'demo-botilleria-multi-carrito',
        'negocio' => $n['nombre'],
        'nota' => co_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_despacho'],
        'catalogo' => $catalogo,
    ];
}

/** Formulario "Agregar" de un producto, con selector de formato si tiene varios. */
function co_agregar(string $id, array $p, string $texto = 'Agregar'): string
{
    $html = '<form class="demo-agregar co-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(CO_BASE . 'pedido.php') . '" method="get">';
    $variantes = $p['variantes'];
    $primero = (string) array_key_first($variantes);
    if (count($variantes) > 1) {
        $html .= '<label class="demo-sr" for="var-' . demo_e($id) . '">Formato de ' . demo_e($p['nombre']) . '</label>';
        $html .= '<select name="variante" id="var-' . demo_e($id) . '">';
        foreach ($variantes as $nombre => $precio) {
            $html .= '<option value="' . demo_e((string) $nombre) . '">' . demo_e((string) $nombre) . ' · ' . demo_e(demo_clp($precio)) . '</option>';
        }
        $html .= '</select>';
    } else {
        $html .= '<input type="hidden" name="variante" value="' . demo_e($primero) . '">';
    }
    $html .= '<span class="co-agregar__precio" data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($p['nombre']) . ' a tu bolsa">' . demo_e($texto) . '</button>';
    return $html . '</form>';
}

/** Logo: sello de lacre con las iniciales y el nombre en cursiva. */
function co_logo(string $clase = ''): string
{
    return '<span class="co-logo ' . demo_e($clase) . '"><span class="co-logo__sello" aria-hidden="true">CO</span>'
        . '<span class="co-logo__nombre">Cava <em>Ocre</em></span></span>';
}

/**
 * Abre la página: head, aviso de demo, mayoría de edad y cabecera con menú.
 * $opc: extra_head (HTML), clase (del <body>).
 */
function co_abrir(string $actual, string $titulo, string $descripcion, array $opc = []): void
{
    $n = co();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=Libre+Franklin:wght@400;500;600&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;1,6..72,400;1,6..72,500&display=swap',
        'css' => [CO_BASE . 'estilo.css'],
        'tema' => '#3b0d18',
        'icono' => ['O', '#3b0d18', '#e8c58f'],
        'clase_body' => 'co co-pagina-' . $actual,
        'extra_head' => bot_edad_head(CO_EDAD) . ($opc['extra_head'] ?? ''),
    ]);
    demo_aviso(co_rubro());
    bot_edad(['clave' => CO_EDAD, 'negocio' => $n['nombre'], 'clase' => 'co-edad', 'marca' => '<p class="edad__marca">' . co_logo() . '</p>']);
    ?>
<p class="co-tira"><?= bot_icono_18(18) ?><span><?= demo_e(BOT_AVISO_EDAD) ?></span></p>
<header class="co-cabecera">
  <div class="co-ancho co-cabecera__fila">
    <a class="co-logo-enlace" href="<?= demo_e(CO_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio"><?= co_logo() ?></a>
    <nav class="co-nav" aria-label="Menú principal"><?php co_nav_items($actual); ?></nav>
    <a class="co-bolsa" href="<?= demo_e(CO_BASE . 'pedido.php') ?>" data-carrito-etiqueta aria-label="Ver tu bolsa"<?= $actual === 'pedido' ? ' aria-current="page"' : '' ?>>
      <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.5" d="M6 8h12l-1 12H7zM9 8V6.5a3 3 0 0 1 6 0V8"/></svg>
      <span class="co-bolsa__texto">Bolsa</span>
      <span class="co-bolsa__num" data-carrito-cantidad hidden>0</span>
    </a>
    <details class="co-menu-movil">
      <summary aria-label="Abrir menú"><span></span><span></span></summary>
      <nav aria-label="Menú del sitio"><?php co_nav_items($actual); ?></nav>
    </details>
  </div>
</header>
<?php
}

function co_nav_items(string $actual): void
{
    foreach (co_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(CO_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>', demo_e($nombre), '</a>';
    }
}

/** Sección "Encuéntranos en Google" con los datos de la cava. */
function co_google(): void
{
    $n = co();
    demo_google([
        'nombre' => $n['nombre'],
        'categoria' => $n['categoria'],
        'comuna' => $n['comuna'],
        'horario' => $n['horario'],
        'nota' => $n['nota'],
        'resenas' => $n['resenas'],
    ], ['bajada' => 'Busca «Cava Ocre» en Google Maps: horario, cómo llegar y lo que opinan quienes vinieron a una cata.']);
}

/** Cierra la página: pie, configuración del carrito y scripts. $scripts: ['qr', 'buscador']. */
function co_cerrar(array $scripts = []): void
{
    $n = co();
    $config = json_encode(co_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="co-pie">
  <div class="co-ancho co-pie__grid">
    <div>
      <?= co_logo('co-logo--pie') ?>
      <p class="co-pie__eslogan"><?= demo_e($n['eslogan']) ?>.</p>
      <p><?= demo_e($n['comuna']) ?>, Santiago</p>
    </div>
    <div>
      <p class="co-pie__titulo">Horario de la tienda</p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario co-pie__horario'); ?>
    </div>
    <div>
      <p class="co-pie__titulo">Escríbenos</p>
      <p><a href="<?= demo_e(co_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">WhatsApp</a></p>
      <p><a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
      <p><a href="<?= demo_e(CO_BASE . 'catas.php') ?>">Reservar una cata</a></p>
    </div>
  </div>
  <div class="co-ancho co-pie__legal">
    <p class="co-pie__edad"><?= bot_icono_18(20) ?><span><?= demo_e(BOT_AVISO_EDAD) ?>. Disfruta con moderación: si bebes, no manejes.</span></p>
    <?php demo_aviso_pie(co_rubro()); ?>
  </div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo bot_edad_script(), "\n</body>\n</html>\n";
}
