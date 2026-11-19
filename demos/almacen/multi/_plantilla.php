<?php
declare(strict_types=1);

// Plantilla común de las páginas de Turno 24 (Multi-page): minimarket abierto las 24 horas.
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['tn_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['tn'] = require __DIR__ . '/_datos.php';

const TN_BASE = '/demos/almacen/multi/';

function tn(): array
{
    return $GLOBALS['tn'];
}

function tn_rubro(): array
{
    return $GLOBALS['tn_rubro'];
}

function tn_nota(): string
{
    $r = tn_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan']);
}

/** WhatsApp con la nota de pedido de prueba. */
function tn_wa(string $texto): string
{
    return demo_wa($texto . "\n" . tn_nota());
}

function tn_ahora(): DateTimeImmutable
{
    return new DateTimeImmutable('now', new DateTimeZone(DEMO_ZONA_HORARIA));
}

/** ¿Hay despacho ahora? (de 08:00 a 02:00, cruza la medianoche). */
function tn_hay_despacho(?DateTimeImmutable $ahora = null): bool
{
    $ahora ??= tn_ahora();
    $min = (int) $ahora->format('G') * 60 + (int) $ahora->format('i');
    [$desde, $hasta] = tn()['despacho_horas'];
    return $min >= demo_minutos($desde) || $min < demo_minutos($hasta);
}

/** Menú: clave => [archivo, nombre]. */
function tn_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'catalogo' => ['catalogo.php', 'Pasillos'],
        'nosotros' => ['nosotros.php', 'Nosotros'],
        'preguntas' => ['preguntas.php', 'Preguntas'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

/** Ícono SVG de un pasillo. */
function tn_icono(string $pasillo, int $tam = 28): string
{
    $trazo = tn()['pasillos'][$pasillo][3] ?? '';
    return '<svg viewBox="0 0 24 24" width="' . $tam . '" height="' . $tam . '" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' . $trazo . '</svg>';
}

/** Configuración del carrito en el navegador (_comun/js/carrito.js). */
function tn_config_carrito(): array
{
    $n = tn();
    $catalogo = [];
    foreach ($n['productos'] as $id => $p) {
        $catalogo[$id] = ['nombre' => $p['nombre'], 'variantes' => $p['variantes']];
    }
    return [
        'clave' => 'demo-almacen-multi-carrito',
        'negocio' => $n['nombre'],
        'nota' => tn_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_despacho'],
        // Pedido mínimo solo para despacho: carrito.js muestra el aviso en [data-carrito-minimo] y bloquea el envío.
        'minimo' => $n['minimo_despacho'],
        'minimo_solo_despacho' => true,
        'textos' => [
            'falta_minimo' => 'Te faltan {falta} para el despacho (mínimo {minimo}). Agrega algo más o elige retiro en el local.',
        ],
        'catalogo' => $catalogo,
    ];
}

/** Formulario "Agregar" con la etiqueta de góndola (precio, precio anterior si está en oferta y variante). */
function tn_agregar(string $id, array $p): string
{
    $variantes = $p['variantes'];
    $primera = array_key_first($variantes);
    $html = '<form class="demo-agregar tn-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(TN_BASE . 'pedido.php') . '" method="get">';
    $html .= '<p class="tn-etiqueta"><span class="tn-etiqueta__precio" data-precio>' . demo_e(demo_clp($variantes[$primera])) . '</span>';
    if (!empty($p['antes'])) {
        $html .= ' <s class="tn-etiqueta__antes" data-antes="' . demo_e($primera) . '" aria-label="Precio normal">' . demo_e(demo_clp((int) $p['antes'])) . '</s>';
    }
    $html .= '<span class="tn-etiqueta__unidad">' . (count($variantes) > 1 ? 'desde · ' : '') . demo_e($primera) . '</span></p>';
    if (count($variantes) > 1) {
        $html .= '<label class="demo-sr" for="var-' . demo_e($id) . '">Formato de ' . demo_e($p['nombre']) . '</label>';
        $html .= '<select name="variante" id="var-' . demo_e($id) . '">';
        foreach ($variantes as $nombre => $precio) {
            $html .= '<option value="' . demo_e($nombre) . '">' . demo_e($nombre) . ' · ' . demo_e(demo_clp($precio)) . '</option>';
        }
        $html .= '</select>';
    } else {
        $html .= '<input type="hidden" name="variante" value="' . demo_e($primera) . '">';
    }
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($p['nombre']) . ' al pedido"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg><span>Agregar</span></button>';
    return $html . '</form>';
}

/**
 * Tarjeta de producto. $opc: buscable (bool, para el buscador del catálogo), oculto (bool), lazy (bool).
 */
function tn_producto(string $id, array $p, array $opc = []): string
{
    $n = tn();
    [$num, $pasillo] = $n['pasillos'][$p['cat']];
    $atributos = ' id="p-' . demo_e($id) . '"';
    if (!empty($opc['buscable'])) {
        $atributos .= ' data-buscable data-texto="' . demo_e($p['nombre'] . ' ' . $p['desc'] . ' ' . $pasillo . ' ' . implode(' ', array_keys($p['variantes']))) . '"';
    }
    if (!empty($opc['oculto'])) {
        $atributos .= ' hidden';
    }
    $html = '<article class="tn-prod' . (!empty($p['antes']) ? ' is-oferta' : '') . '"' . $atributos . '>';
    $html .= '<div class="tn-prod__foto">' . demo_img($p['foto'], $p['nombre'], ['ancho' => 480, 'alto' => 360, 'anchos' => [320, 480, 800], 'sizes' => '(max-width: 600px) 46vw, (max-width: 1100px) 30vw, 260px', 'lazy' => $opc['lazy'] ?? true]);
    if (!empty($p['antes'])) {
        $html .= '<span class="tn-prod__oferta">Oferta</span>';
    }
    $html .= '</div><div class="tn-prod__cuerpo">';
    $html .= '<p class="tn-prod__pasillo">Pasillo ' . demo_e($num) . '</p>';
    $html .= '<h3>' . demo_e($p['nombre']) . '</h3>';
    $html .= '<p class="tn-prod__desc">' . demo_e($p['desc']) . '</p>';
    $html .= tn_agregar($id, $p);
    if (!empty($opc['con_3d']) && ($p['modelo'] ?? '') !== '') {
        $html .= demo_boton_3d(['id' => $id, 'nombre' => $p['nombre'], 'modelo' => $p['modelo'], 'foto' => $p['foto'], 'objeto' => 'producto'], 'tn-btn-3d');
    }
    return $html . '</div></article>';
}

/** Abre la página: head, aviso de demo, franja de estado y cabecera con buscador. */
function tn_abrir(string $actual, string $titulo, string $descripcion): void
{
    $n = tn();
    $ahora = tn_ahora();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&family=Space+Grotesk:wght@500;700&display=swap',
        'css' => [TN_BASE . 'estilo.css'],
        'tema' => '#2b2ee6',
        'icono' => ['24', '#2b2ee6', '#c6f135'],
        'clase_body' => 'tn tn-pagina-' . $actual,
    ]);
    demo_aviso(tn_rubro());
    $despacho = tn_hay_despacho($ahora);
    ?>
<div class="tn-franja">
  <p class="tn-ancho">
    <span class="tn-franja__vivo"><span class="tn-punto" aria-hidden="true"></span>Abierto ahora · son las <?= demo_e($ahora->format('H:i')) ?></span>
    <span class="tn-franja__sep" aria-hidden="true">/</span>
    <span><?= $despacho ? 'Despacho en unos ' . (int) $n['despacho_minutos'] . ' minutos' : 'De 2:00 a 8:00, solo retiro en el local' ?></span>
    <span class="tn-franja__sep tn-franja__extra" aria-hidden="true">/</span>
    <span class="tn-franja__extra">Mínimo para despacho <?= demo_e(demo_clp($n['minimo_despacho'])) ?></span>
  </p>
</div>
<header class="tn-cabecera">
  <div class="tn-ancho tn-cabecera__fila">
    <a class="tn-logo" href="<?= demo_e(TN_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio">
      <span class="tn-logo__num" aria-hidden="true">24</span>
      <span class="tn-logo__texto">Turno<small>minimarket</small></span>
    </a>
    <form class="tn-buscar" role="search" method="get" action="<?= demo_e(TN_BASE . 'catalogo.php') ?>">
      <label class="demo-sr" for="tn-buscar-cabecera">Buscar en los pasillos</label>
      <input type="search" id="tn-buscar-cabecera" name="q" maxlength="40" placeholder="¿Qué te falta? Hielo, pan, huevos…" autocomplete="off">
      <button type="submit" aria-label="Buscar"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2.2"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg></button>
    </form>
    <a class="tn-carrito" href="<?= demo_e(TN_BASE . 'pedido.php') ?>" data-carrito-etiqueta aria-label="Ver tu pedido"<?= $actual === 'pedido' ? ' aria-current="page"' : '' ?>>
      <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"><path d="M3 4h2.5l2.2 11h10.6L20.5 7H6.4"/><circle cx="9.5" cy="19.5" r="1.4"/><circle cx="17" cy="19.5" r="1.4"/></svg>
      <span class="tn-carrito__texto">Pedido</span>
      <span class="tn-carrito__num" data-carrito-cantidad hidden>0</span>
    </a>
  </div>
  <nav class="tn-nav" aria-label="Menú principal">
    <div class="tn-ancho">
      <?php foreach (tn_paginas() as $clave => [$archivo, $nombre]): ?>
      <a href="<?= demo_e(TN_BASE . $archivo) ?>"<?= $clave === $actual ? ' aria-current="page" class="is-activo"' : '' ?>><?= demo_e($nombre) ?></a>
      <?php endforeach; ?>
      <a class="tn-nav__wa" href="<?= demo_e(tn_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(16) ?><span>WhatsApp</span></a>
    </div>
  </nav>
</header>
<?php
}

/** Cierra la página: pie, configuración del carrito y scripts. $scripts: además de 'carrito' (ej. 'buscador'). */
function tn_cerrar(array $scripts = []): void
{
    $n = tn();
    $config = json_encode(tn_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="tn-pie">
  <div class="tn-ancho tn-pie__grid">
    <div>
      <p class="tn-logo tn-logo--pie"><span class="tn-logo__num" aria-hidden="true">24</span><span class="tn-logo__texto">Turno<small>minimarket</small></span></p>
      <p><?= demo_e($n['eslogan']) ?>.</p>
      <p><?= demo_e($n['barrio']) ?>, Santiago.</p>
    </div>
    <div>
      <p class="tn-pie__titulo">Horario</p>
      <p><strong>Local:</strong> abierto las 24 horas, los 365 días.</p>
      <p><strong>Despacho:</strong> de <?= demo_e($n['despacho_horas'][0]) ?> a <?= demo_e($n['despacho_horas'][1]) ?>, en <?= count($n['comunas_despacho']) ?> comunas.</p>
    </div>
    <div>
      <p class="tn-pie__titulo">Pasillos</p>
      <ul class="tn-pie__lista">
        <?php foreach ($n['pasillos'] as $slug => [$num, $nombre]): ?>
        <li><a href="<?= demo_e(TN_BASE . 'catalogo.php?cat=' . $slug) ?>"><span><?= demo_e($num) ?></span> <?= demo_e($nombre) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div>
      <p class="tn-pie__titulo">Contacto</p>
      <p><a href="<?= demo_e(tn_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">WhatsApp</a></p>
      <p><a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
      <p><a href="<?= demo_e(TN_BASE . 'preguntas.php') ?>">Preguntas frecuentes</a></p>
    </div>
  </div>
  <div class="tn-ancho"><?php demo_aviso_pie(tn_rubro()); ?></div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo '<script src="', demo_e(demo_asset(TN_BASE . 'turno.js')), '" defer></script>', "\n";
    echo "</body>\n</html>\n";
}
