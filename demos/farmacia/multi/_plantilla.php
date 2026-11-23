<?php
declare(strict_types=1);

// Plantilla común de las páginas de Nácar (Multi-page): farmacia y dermocosmética en Providencia.
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$GLOBALS['nc_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['nc'] = require __DIR__ . '/_datos.php';

const NC_BASE = '/demos/farmacia/multi/';

function nc(): array
{
    return $GLOBALS['nc'];
}

function nc_rubro(): array
{
    return $GLOBALS['nc_rubro'];
}

function nc_plan(): string
{
    return nc_rubro()['versiones']['multi']['plan'];
}

function nc_nota(string $tipo = 'Pedido'): string
{
    return demo_nota(nc_rubro()['rubro_nombre'], nc_plan(), $tipo);
}

/** WhatsApp con la nota de pedido de prueba. */
function nc_wa(string $texto, string $tipo = 'Pedido'): string
{
    return demo_wa($texto . "\n" . nc_nota($tipo));
}

/** Menú: clave => [archivo, nombre]. */
function nc_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'tienda' => ['tienda.php', 'Tienda'],
        'receta' => ['receta.php', 'Recetas'],
        'farmacia' => ['farmacia.php', 'La farmacia'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

/** Configuración del carrito en el navegador (_comun/js/carrito.js). */
function nc_config_carrito(): array
{
    $n = nc();
    $catalogo = [];
    foreach ($n['productos'] as $id => $p) {
        $catalogo[$id] = ['nombre' => $p['nombre'], 'variantes' => $p['variantes']];
    }
    return [
        'clave' => 'demo-farmacia-multi-carrito',
        'negocio' => $n['nombre_corto'],
        'nota' => nc_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_despacho'],
        'minimo' => $n['minimo_despacho'],
        'minimo_solo_despacho' => true,
        'gratis_desde' => $n['gratis_desde'],
        'textos' => [
            'saludo' => 'Hola, quiero hacer un pedido en {negocio} (farmacia y dermocosmética):',
            'retiro' => 'Entrega: retiro en la farmacia, Providencia',
            'falta_minimo' => 'Te faltan {falta} para el despacho (mínimo {minimo}). Agrega algo más o retira en la farmacia.',
            'falta_gratis' => 'Te faltan {falta} para el despacho gratis.',
        ],
        'catalogo' => $catalogo,
    ];
}

/** Muestra de la textura del producto (dibujada con CSS, sin fotos de envases ni marcas). */
function nc_muestra(string $textura, string $clase = ''): string
{
    return '<span class="nc-muestra nc-muestra--' . demo_e($textura) . ' ' . demo_e($clase) . '" aria-hidden="true"><span class="nc-muestra__mancha"></span></span>';
}

/** Texto del buscador de un producto (mismo criterio en PHP, demo_coincide, y en js/buscador.js). */
function nc_texto_busqueda(array $p): string
{
    $n = nc();
    return $p['codigo'] . ' ' . $p['nombre'] . ' ' . $p['desc'] . ' ' . $n['categorias'][$p['cat']][1] . ' ' . implode(' ', $p['datos']) . ' ' . implode(' ', array_keys($p['variantes']));
}

/** Formulario "Agregar" (carrito.js) con el precio de la primera variante. */
function nc_agregar(string $id, array $p): string
{
    $variantes = $p['variantes'];
    $primera = array_key_first($variantes);
    $html = '<form class="demo-agregar nc-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(NC_BASE . 'pedido.php') . '" method="get">';
    $html .= '<p class="nc-precio"><span data-precio>' . demo_e(demo_clp($variantes[$primera])) . '</span>';
    if (count($variantes) === 1 && $primera !== 'Única') {
        $html .= ' <small>' . demo_e($primera) . '</small>';
    }
    $html .= '</p>';
    if (count($variantes) > 1) {
        $html .= '<label class="demo-sr" for="var-' . demo_e($id) . '">Tamaño de ' . demo_e($p['nombre']) . '</label>';
        $html .= '<select name="variante" id="var-' . demo_e($id) . '">';
        foreach ($variantes as $nombre => $precio) {
            $html .= '<option value="' . demo_e($nombre) . '">' . demo_e($nombre) . ' · ' . demo_e(demo_clp($precio)) . '</option>';
        }
        $html .= '</select>';
    } else {
        $html .= '<input type="hidden" name="variante" value="' . demo_e($primera) . '">';
    }
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($p['nombre']) . ' a la bolsa"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg><span>Agregar</span></button>';
    return $html . '</form>';
}

/**
 * Ficha de producto. $opc: buscable (bool), oculto (bool), titulo ('h3' por defecto).
 */
function nc_ficha(string $id, array $p, array $opc = []): string
{
    $n = nc();
    $atributos = ' id="p-' . demo_e($id) . '"';
    if (!empty($opc['buscable'])) {
        $atributos .= ' data-buscable data-texto="' . demo_e(nc_texto_busqueda($p)) . '"';
    }
    if (!empty($opc['oculto'])) {
        $atributos .= ' hidden';
    }
    $h = $opc['titulo'] ?? 'h3';
    $html = '<article class="nc-ficha nc-ficha--' . demo_e($p['cat']) . '"' . $atributos . '>';
    $html .= '<div class="nc-ficha__muestra">' . nc_muestra($p['textura']);
    $html .= '<span class="nc-ficha__codigo">' . demo_e($p['codigo']) . '</span>';
    if (!empty($p['medicamento'])) {
        $html .= '<span class="nc-ficha__sello">Venta directa</span>';
    } elseif (!empty($p['destacado'])) {
        $html .= '<span class="nc-ficha__sello nc-ficha__sello--claro">Favorito</span>';
    }
    $html .= '</div><div class="nc-ficha__cuerpo">';
    $html .= '<p class="nc-ficha__cat">' . demo_e($n['categorias'][$p['cat']][1]) . '</p>';
    $html .= '<' . $h . ' class="nc-ficha__nombre">' . demo_e($p['nombre']) . '</' . $h . '>';
    $html .= '<p class="nc-ficha__desc">' . demo_e($p['desc']) . '</p>';
    $html .= '<dl class="nc-ficha__datos">';
    foreach ($p['datos'] as $etiqueta => $valor) {
        $html .= '<div><dt>' . demo_e($etiqueta) . '</dt><dd>' . demo_e($valor) . '</dd></div>';
    }
    $html .= '</dl>';
    if (!empty($p['medicamento'])) {
        $html .= '<p class="nc-ficha__aviso">Lee las instrucciones antes de usar un medicamento.</p>';
    }
    $html .= nc_agregar($id, $p);
    return $html . "</div></article>
";
}

/** Perla del logo (degradado de nácar). */
function nc_perla(string $clase = ''): string
{
    return '<span class="nc-perla ' . demo_e($clase) . '" aria-hidden="true"></span>';
}

/** Abre la página: head, aviso de demo, franja de estado y cabecera. */
function nc_abrir(string $actual, string $titulo, string $descripcion): void
{
    $n = nc();
    $estado = demo_horario_estado($n['horario']);
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre_corto'] . ', farmacia en ' . $n['comuna'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600&family=Spline+Sans+Mono:wght@400;500&display=swap',
        'css' => [NC_BASE . 'estilo.css'],
        'tema' => '#f7f3ef',
        'icono' => ['n', '#2c1a2b', '#f3dfe4'],
        'clase_body' => 'nc nc-pagina-' . $actual,
    ]);
    demo_aviso(nc_rubro());
    ?>
<div class="nc-franja">
  <p class="nc-ancho">
    <span class="nc-franja__estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></span>
    <span class="nc-franja__extra">Despacho en el día en <?= demo_e($n['comuna']) ?> · gratis desde <?= demo_e(demo_clp($n['gratis_desde'])) ?></span>
    <span class="nc-franja__extra">Q.F. en sala todo el horario</span>
  </p>
</div>
<header class="nc-cabecera">
  <div class="nc-ancho nc-cabecera__fila">
    <a class="nc-logo" href="<?= demo_e(NC_BASE) ?>" aria-label="<?= demo_e($n['nombre']) ?>, inicio">
      <?= nc_perla() ?>
      <span class="nc-logo__texto">nácar<small>farmacia &amp; dermocosmética</small></span>
    </a>
    <nav class="nc-nav" aria-label="Menú principal">
      <?php foreach (nc_paginas() as $clave => [$archivo, $nombre]): ?>
      <a href="<?= demo_e(NC_BASE . $archivo) ?>"<?= $clave === $actual ? ' aria-current="page" class="is-activo"' : '' ?>><?= demo_e($nombre) ?></a>
      <?php endforeach; ?>
    </nav>
    <a class="nc-bolsa" href="<?= demo_e(NC_BASE . 'pedido.php') ?>" data-carrito-etiqueta aria-label="Ver tu pedido"<?= $actual === 'pedido' ? ' aria-current="page"' : '' ?>>
      <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M5 8h14l-1 13H6z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>
      <span class="nc-bolsa__texto">Bolsa</span>
      <span class="nc-bolsa__num" data-carrito-cantidad hidden>0</span>
    </a>
  </div>
</header>
<?php
}

/** Cierra la página: pie, configuración del carrito y scripts. $scripts: además de 'carrito' (ej. 'buscador'). */
function nc_cerrar(array $scripts = []): void
{
    $n = nc();
    $config = json_encode(nc_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="nc-pie">
  <div class="nc-ancho">
    <p class="nc-pie__aviso"><?= nc_perla('nc-perla--chica') ?><span><strong>Consulta a tu químico farmacéutico.</strong> Lee las instrucciones antes de usar un medicamento. Los suplementos alimentarios no reemplazan una alimentación variada.</span></p>
    <div class="nc-pie__grid">
      <div>
        <p class="nc-logo nc-logo--pie"><?= nc_perla() ?><span class="nc-logo__texto">nácar<small>farmacia &amp; dermocosmética</small></span></p>
        <p><?= demo_e($n['comuna']) ?>, Santiago.</p>
        <p>Directora técnica: Q.F. <?= demo_e($n['qf']) ?>.</p>
      </div>
      <div>
        <p class="nc-pie__titulo">Horario</p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario nc-pie__horario'); ?>
      </div>
      <div>
        <p class="nc-pie__titulo">Tienda</p>
        <ul class="nc-pie__lista">
          <?php foreach ($n['categorias'] as $slug => [$num, $nombre]): ?>
          <li><a href="<?= demo_e(NC_BASE . 'tienda.php?cat=' . $slug) ?>"><?= demo_e($nombre) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <p class="nc-pie__titulo">Contacto</p>
        <p><a href="<?= demo_e(nc_wa('Hola, tengo una consulta para ' . $n['nombre_corto'] . '.')) ?>" target="_blank" rel="noopener">WhatsApp</a></p>
        <p><a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
        <p><a href="<?= demo_e(NC_BASE . 'receta.php') ?>">Cotiza tu receta</a></p>
      </div>
    </div>
    <?php demo_aviso_pie(nc_rubro()); ?>
  </div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo "</body>\n</html>\n";
}
