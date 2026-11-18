<?php
declare(strict_types=1);

// Plantilla común de las páginas de Atelier Lúcuma (Multi-page).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['al_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['al'] = require __DIR__ . '/_datos.php';

const AL_BASE = '/demos/pasteleria/multi/';
const AL_MESES = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

function al(): array
{
    return $GLOBALS['al'];
}

function al_rubro(): array
{
    return $GLOBALS['al_rubro'];
}

function al_nota(): string
{
    $r = al_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan']);
}

/** WhatsApp con la nota de pedido de prueba. */
function al_wa(string $texto): string
{
    return demo_wa($texto . "\n" . al_nota());
}

/** Menú de navegación: clave => [archivo, nombre]. */
function al_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'tortas' => ['tortas.php', 'Colección'],
        'carta' => ['carta.php', 'Carta'],
        'encargo' => ['encargo.php', 'Encargos'],
        'nosotros' => ['nosotros.php', 'Atelier'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

/** Precio "desde" de una torta o plato. */
function al_desde(array $p): int
{
    return min($p['precios'] ?? $p['variantes']);
}

/** "8 a 20 porciones" */
function al_porciones(array $t): string
{
    $n = array_map('intval', array_keys($t['precios']));
    return count($n) > 1 ? min($n) . ' a ' . max($n) . ' porciones' : $n[0] . ' porciones';
}

function al_ahora(): DateTimeImmutable
{
    return new DateTimeImmutable('today', new DateTimeZone(DEMO_ZONA_HORARIA));
}

/** "sábado 22 de noviembre" */
function al_fecha_larga(DateTimeImmutable $d): string
{
    return mb_strtolower(DEMO_DIAS[(int) $d->format('N')]) . ' ' . $d->format('j') . ' de ' . AL_MESES[(int) $d->format('n')];
}

/** Próximos días abiertos (para retiro o despacho): ['2026-11-22' => 'sábado 22 de noviembre', ...]. */
function al_dias_abiertos(int $desde, int $cuantos): array
{
    $dias = [];
    $d = al_ahora()->modify('+' . $desde . ' days');
    for ($i = 0; count($dias) < $cuantos && $i < 60; $i++, $d = $d->modify('+1 day')) {
        if (!empty(al()['horario'][(int) $d->format('N')])) {
            $dias[$d->format('Y-m-d')] = al_fecha_larga($d);
        }
    }
    return $dias;
}

/** Tortas que se pueden pedir desde la carta (las de matrimonio van solo por encargo). */
function al_tortas_carta(): array
{
    return array_filter(al()['tortas'], static fn (array $t): bool => !in_array('matrimonio', $t['ocasiones'], true) || count($t['ocasiones']) > 1);
}

/** Carta completa: platos + tortas enteras (con sus tamaños como variantes). */
function al_carta(): array
{
    $carta = [];
    foreach (al()['platos'] as $id => $p) {
        $carta[$id] = $p + ['modelo' => ''];
    }
    foreach (al_tortas_carta() as $id => $t) {
        $carta[$id] = ['cat' => 'tortas', 'nombre' => 'Torta ' . $t['nombre'], 'desc' => $t['notas'], 'foto' => $t['foto'], 'variantes' => $t['precios'], 'modelo' => $t['modelo']];
    }
    return $carta;
}

/** Configuración del carrito en el navegador (js/carrito.js). */
function al_config_carrito(): array
{
    $n = al();
    $catalogo = [];
    foreach (al_carta() as $id => $p) {
        $catalogo[$id] = ['nombre' => $p['nombre'], 'variantes' => $p['variantes']];
    }
    return [
        'clave' => 'demo-pasteleria-multi-carrito',
        'negocio' => $n['nombre'],
        'nota' => al_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_despacho'],
        'catalogo' => $catalogo,
        'textos' => [
            'retiro' => 'Entrega: retiro en el atelier',
            'despacho' => 'Entrega: despacho refrigerado a {direccion}, {comuna}',
        ],
    ];
}

/** Formulario "Agregar" de un plato, con selector de tamaño si tiene variantes. */
function al_agregar(string $id, array $p): string
{
    $html = '<form class="demo-agregar al-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(AL_BASE . 'pedido.php') . '" method="get">';
    $variantes = $p['variantes'];
    $primero = array_key_first($variantes);
    if (count($variantes) > 1) {
        $html .= '<label class="demo-sr" for="var-' . demo_e($id) . '">Tamaño de ' . demo_e($p['nombre']) . '</label>';
        $html .= '<select name="variante" id="var-' . demo_e($id) . '">';
        foreach ($variantes as $nombre => $precio) {
            $html .= '<option value="' . demo_e($nombre) . '">' . demo_e($nombre) . '</option>';
        }
        $html .= '</select>';
    } else {
        $html .= '<input type="hidden" name="variante" value="' . demo_e($primero) . '">';
    }
    $html .= '<span class="al-agregar__precio" data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($p['nombre']) . ' al pedido">Agregar</button>';
    return $html . '</form>';
}

/** Logo tipográfico. */
function al_logo(): string
{
    return '<span class="al-logo__a">Atelier</span> <span class="al-logo__b">Lúcuma</span>';
}

/**
 * Abre la página: head, aviso de demo y cabecera tipo portada de revista.
 * $opc: extra_head.
 */
function al_abrir(string $actual, string $titulo, string $descripcion, array $opc = []): void
{
    $n = al();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400;0,6..96,500;1,6..96,400;1,6..96,500&family=Hanken+Grotesk:wght@400;500;600&display=swap',
        'css' => [AL_BASE . 'estilo.css'],
        'tema' => '#f6f0e8',
        'icono' => ['L', '#2b1d18', '#e1b07c'],
        'clase_body' => 'al al-pagina-' . $actual,
        'extra_head' => $opc['extra_head'] ?? '',
    ]);
    demo_aviso(al_rubro());
    ?>
<header class="al-cabecera">
  <div class="al-ancho al-cabecera__fila">
    <p class="al-cabecera__lugar"><?= demo_e($n['categoria']) ?> · <?= demo_e($n['comuna']) ?></p>
    <a class="al-logo" href="<?= demo_e(AL_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio"><?= al_logo() ?></a>
    <div class="al-cabecera__acciones">
      <a class="al-carrito" href="<?= demo_e(AL_BASE . 'pedido.php') ?>" data-carrito-etiqueta aria-label="Ver tu pedido">
        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.4" d="M4.5 8.5h15l-1.3 11.5H5.8zM9 8.5V7a3 3 0 0 1 6 0v1.5"/></svg>
        <span class="al-carrito__texto">Pedido</span>
        <span class="al-carrito__num" data-carrito-cantidad hidden>0</span>
      </a>
      <details class="al-menu-movil">
        <summary aria-label="Abrir menú"><span></span><span></span></summary>
        <nav aria-label="Menú del sitio">
          <?php al_nav_items($actual); ?>
        </nav>
      </details>
    </div>
  </div>
  <nav class="al-nav" aria-label="Menú principal">
    <div class="al-ancho"><?php al_nav_items($actual); ?></div>
  </nav>
</header>
<?php
}

function al_nav_items(string $actual): void
{
    foreach (al_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(AL_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>', demo_e($nombre), '</a>';
    }
}

/** Encabezado de página interior. */
function al_titular(string $sobre, string $titulo, string $bajada = ''): void
{
    ?>
  <header class="al-titular">
    <div class="al-ancho">
      <p class="al-sobre"><?= demo_e($sobre) ?></p>
      <h1><?= $titulo ?></h1>
      <?php if ($bajada !== ''): ?><p class="al-titular__bajada"><?= $bajada ?></p><?php endif; ?>
    </div>
  </header>
<?php
}

/** Cierra la página: pie, configuración del carrito y scripts. $scripts: ['qr', '3d', 'buscador', 'encargo']. */
function al_cerrar(array $scripts = []): void
{
    $n = al();
    $config = json_encode(al_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="al-pie">
  <div class="al-ancho al-pie__grid">
    <div>
      <p class="al-logo al-logo--pie"><?= al_logo() ?></p>
      <p><?= demo_e($n['eslogan']) ?>.<br><?= demo_e($n['comuna']) ?>, Santiago.</p>
    </div>
    <div>
      <p class="al-pie__titulo">Horario del atelier</p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario al-pie__horario'); ?>
    </div>
    <div>
      <p class="al-pie__titulo">Encargos</p>
      <p><a href="<?= demo_e(AL_BASE . 'encargo.php') ?>">Encargar una torta</a></p>
      <p><a href="<?= demo_e(al_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Escribir por WhatsApp</a></p>
      <p><a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
    </div>
  </div>
  <div class="al-ancho"><?php demo_aviso_pie(al_rubro()); ?></div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    $propios = array_values(array_intersect($scripts, ['encargo']));
    demo_scripts(array_merge(array_diff($scripts, ['encargo']), ['carrito']));
    if ($propios) {
        echo '<script src="', demo_e(demo_asset(AL_BASE . 'encargo.js')), '" defer></script>', "\n";
    }
    echo "</body>\n</html>\n";
}
