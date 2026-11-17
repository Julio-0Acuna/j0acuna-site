<?php
declare(strict_types=1);

// Plantilla común de las páginas de Veta Carnes Maduradas (Multi-page).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['vt_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['vt'] = require __DIR__ . '/_datos.php';

const VT_BASE = '/demos/carniceria/multi/';

function vt(): array
{
    return $GLOBALS['vt'];
}

function vt_rubro(): array
{
    return $GLOBALS['vt_rubro'];
}

function vt_nota(): string
{
    $r = vt_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan']);
}

/** WhatsApp con la nota de pedido de prueba. */
function vt_wa(string $texto): string
{
    return demo_wa($texto . "\n" . vt_nota());
}

/** Menú de navegación: clave => [archivo, nombre]. */
function vt_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'cortes' => ['cortes.php', 'Cortes'],
        'camara' => ['camara.php', 'La cámara'],
        'preguntas' => ['preguntas.php', 'Preguntas'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

/** 400 → "400 g"; 1200 → "1,2 kg". */
function vt_peso(int $gramos): string
{
    if ($gramos < 1000) {
        return $gramos . ' g';
    }
    return str_replace('.', ',', rtrim(rtrim(number_format($gramos / 1000, 2, '.', ''), '0'), '.')) . ' kg';
}

/**
 * Variantes de un corte [nombre => precio]. Los cortes por peso se calculan desde su precio por kilo
 * (redondeado a la decena) para que el carrito y la página muestren lo mismo.
 */
function vt_variantes(array $c): array
{
    if (isset($c['variantes'])) {
        return $c['variantes'];
    }
    $out = [];
    foreach ($c['pesos'] as $g) {
        $out[vt_peso($g)] = (int) (round($c['kilo'] * $g / 1000 / 10) * 10);
    }
    return $out;
}

/** Precio "desde" de un corte. */
function vt_desde(array $c): int
{
    return min(vt_variantes($c));
}

/** Configuración del carrito en el navegador (js/carrito.js de la base común). */
function vt_config_carrito(): array
{
    $n = vt();
    $catalogo = [];
    foreach ($n['cortes'] as $id => $c) {
        $catalogo[$id] = ['nombre' => $c['nombre'], 'variantes' => vt_variantes($c)];
    }
    return [
        'clave' => 'demo-carniceria-multi-carrito',
        'negocio' => $n['nombre'],
        'nota' => vt_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_despacho'],
        'catalogo' => $catalogo,
    ];
}

/** Íconos de uso (SVG decorativo; el nombre va al lado). */
function vt_icono(string $uso): string
{
    $trazos = [
        'parrilla' => '<path d="M4 10h16a8 8 0 0 1-16 0zM8 4.5v2.5M12 3.5v3.5M16 4.5v2.5M8 17.5 6.5 21M16 17.5l1.5 3.5"/>',
        'olla' => '<path d="M5 9h14v7a4 4 0 0 1-4 4H9a4 4 0 0 1-4-4zM2.5 11H5M19 11h2.5M9 5.5h6M12 5.5V9"/>',
        'horno' => '<rect x="3.5" y="3.5" width="17" height="17" rx="2"/><path d="M3.5 8.5h17M7.5 6h.01M10.5 6h.01M7.5 12h9v5h-9z"/>',
        'sarten' => '<path d="M2.5 11h14a6 6 0 0 1-6 6h-2a6 6 0 0 1-6-6zM16.5 11.5l5-2.5"/>',
    ];
    return '<svg viewBox="0 0 24 24" width="15" height="15" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' . ($trazos[$uso] ?? '') . '</svg>';
}

/** Formulario "Agregar" de un corte, con selector de peso (o de presentación) si tiene más de una. */
function vt_agregar(string $id, array $c): string
{
    $variantes = vt_variantes($c);
    $primero = array_key_first($variantes);
    $html = '<form class="demo-agregar vt-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(VT_BASE . 'pedido.php') . '" method="get">';
    if (count($variantes) > 1) {
        $html .= '<label class="vt-agregar__etiqueta" for="var-' . demo_e($id) . '">' . (isset($c['kilo']) ? 'Peso' : 'Presentación') . '</label>';
        $html .= '<select name="variante" id="var-' . demo_e($id) . '">';
        foreach ($variantes as $nombre => $precio) {
            $html .= '<option value="' . demo_e($nombre) . '">' . demo_e($nombre) . ' · ' . demo_e(demo_clp($precio)) . '</option>';
        }
        $html .= '</select>';
    } else {
        $html .= '<input type="hidden" name="variante" value="' . demo_e($primero) . '">';
    }
    $html .= '<span class="vt-agregar__precio" data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($c['nombre']) . ' al pedido">Agregar</button>';
    return $html . '</form>';
}

/** Etiqueta de cámara: "28 días · 2 °C" o "Fresco". */
function vt_etiqueta(array $c): string
{
    return (int) $c['dias'] > 0 ? (int) $c['dias'] . ' días de maduración' : 'Fresco';
}

/** Abre la página: head, aviso de demo y cabecera con menú. */
function vt_abrir(string $actual, string $titulo, string $descripcion, array $opc = []): void
{
    $n = vt();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Manrope:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap',
        'css' => [VT_BASE . 'estilo.css'],
        'tema' => '#0e0d0c',
        'icono' => ['V', '#8f1d1d', '#efe8dc'],
        'clase_body' => 'vt vt-pagina-' . $actual,
        'extra_head' => $opc['extra_head'] ?? '',
    ]);
    demo_aviso(vt_rubro());
    ?>
<header class="vt-cabecera">
  <div class="vt-ancho vt-cabecera__fila">
    <a class="vt-logo" href="<?= demo_e(VT_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio">
      <span class="vt-logo__marca">Veta</span>
      <span class="vt-logo__sub">Carnes maduradas<br><?= demo_e($n['comuna']) ?></span>
    </a>
    <nav class="vt-nav" aria-label="Menú principal">
      <?php vt_nav_items($actual); ?>
    </nav>
    <a class="vt-carrito" href="<?= demo_e(VT_BASE . 'pedido.php') ?>" data-carrito-etiqueta aria-label="Ver tu pedido"<?= $actual === 'pedido' ? ' aria-current="page"' : '' ?>>
      <span class="vt-carrito__texto">Pedido</span>
      <span class="vt-carrito__num" data-carrito-cantidad hidden>0</span>
      <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M4 7h16l-1.5 13h-13zM8.5 7V5.5a3.5 3.5 0 0 1 7 0V7"/></svg>
    </a>
    <details class="vt-menu-movil">
      <summary aria-label="Abrir menú"><span></span><span></span></summary>
      <nav aria-label="Menú del sitio">
        <?php vt_nav_items($actual); ?>
        <a href="<?= demo_e(VT_BASE . 'pedido.php') ?>">Tu pedido</a>
      </nav>
    </details>
  </div>
</header>
<?php
}

function vt_nav_items(string $actual): void
{
    foreach (vt_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(VT_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>', demo_e($nombre), '</a>';
    }
}

/** Cierra la página: pie, configuración del carrito y scripts. $scripts: ['qr', '3d', 'buscador']. */
function vt_cerrar(array $scripts = []): void
{
    $n = vt();
    $config = json_encode(vt_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="vt-pie">
  <div class="vt-ancho">
    <p class="vt-pie__marca" aria-hidden="true">Veta</p>
    <div class="vt-pie__grid">
      <div>
        <p class="vt-pie__titulo">La cámara</p>
        <p><?= demo_e($n['eslogan']) ?>.</p>
        <p><?= demo_e($n['comuna']) ?>, Santiago</p>
      </div>
      <div>
        <p class="vt-pie__titulo">Horario</p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario vt-pie__horario'); ?>
      </div>
      <div>
        <p class="vt-pie__titulo">Pedidos</p>
        <p><a href="<?= demo_e(VT_BASE . 'cortes.php') ?>">Carta de cortes</a></p>
        <p><a href="<?= demo_e(vt_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Escribir por WhatsApp</a></p>
        <p><a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
      </div>
    </div>
    <?php demo_aviso_pie(vt_rubro()); ?>
  </div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo "</body>\n</html>\n";
}
