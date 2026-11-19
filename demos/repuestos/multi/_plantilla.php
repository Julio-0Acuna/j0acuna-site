<?php
declare(strict_types=1);

// Plantilla común de las páginas de Calibre, frenos y suspensión (Multi-page).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';
require_once dirname(__DIR__) . '/_patente.php';

$GLOBALS['cb_rubro'] = require dirname(__DIR__) . '/_rubro.php';
$GLOBALS['cb'] = require __DIR__ . '/_datos.php';

const CB_BASE = '/demos/repuestos/multi/';
/** Cilindradas del buscador por vehículo. */
const CB_CILINDRADAS = ['1.0', '1.2', '1.4', '1.6', '1.8', '2.0', '2.2', '2.4', '2.5', '2.8', '3.0'];

function cb(): array
{
    return $GLOBALS['cb'];
}

function cb_rubro(): array
{
    return $GLOBALS['cb_rubro'];
}

function cb_nota(): string
{
    $r = cb_rubro();
    return demo_nota($r['rubro_nombre'], $r['versiones']['multi']['plan'], 'Cotización');
}

/** WhatsApp con la nota de cotización de prueba. */
function cb_wa(string $texto): string
{
    return demo_wa($texto . "\n" . cb_nota());
}

/** Menú de navegación: clave => [archivo, nombre]. */
function cb_paginas(): array
{
    return [
        'inicio' => ['', 'Inicio'],
        'catalogo' => ['catalogo.php', 'Catálogo'],
        'guia' => ['guia.php', 'Guía de desgaste'],
        'nosotros' => ['nosotros.php', 'Nosotros'],
        'contacto' => ['contacto.php', 'Contacto'],
    ];
}

/** "1.6" (con punto, como se escribe la cilindrada en Chile). */
function cb_cc(float $cc): string
{
    return number_format($cc, 1, '.', '');
}

/** Texto de compatibilidad: "City car y hatchback · 1.2–1.6 · 2012–2020" o "Universal". */
function cb_compat_texto(?array $c): string
{
    if ($c === null) {
        return 'Universal';
    }
    $nombres = [];
    foreach (array_values($c['tipos']) as $i => $t) {
        $nombre = RP_TIPOS[$t] ?? $t;
        $nombres[] = ($i === 0 || $nombre === 'SUV') ? $nombre : mb_strtolower($nombre);
    }
    $ultimo = array_pop($nombres);
    $texto = $nombres ? implode(', ', $nombres) . ' y ' . $ultimo : $ultimo;
    return $texto . ' · ' . cb_cc($c['cc'][0]) . '–' . cb_cc($c['cc'][1]) . ' · ' . $c['anios'][0] . '–' . $c['anios'][1];
}

/** Filtro del buscador por vehículo y texto, leído de ?tipo=&anio=&cc=&q= (validado). */
function cb_filtro(): array
{
    $tipo = is_string($_GET['tipo'] ?? null) && isset(RP_TIPOS[$_GET['tipo']]) ? $_GET['tipo'] : '';
    $anioCrudo = is_string($_GET['anio'] ?? null) ? trim($_GET['anio']) : '';
    $anio = ctype_digit($anioCrudo) && (int) $anioCrudo >= 1970 && (int) $anioCrudo <= (int) date('Y') + 1 ? (int) $anioCrudo : 0;
    $cc = is_string($_GET['cc'] ?? null) && in_array($_GET['cc'], CB_CILINDRADAS, true) ? $_GET['cc'] : '';
    $q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
    return ['tipo' => $tipo, 'anio' => $anio, 'cc' => $cc, 'q' => $q];
}

/** ¿El producto calza con el vehículo del filtro? Los universales siempre calzan. */
function cb_calza(array $p, array $f): bool
{
    $c = $p['compat'];
    if ($c === null) {
        return true;
    }
    if ($f['tipo'] !== '' && !in_array($f['tipo'], $c['tipos'], true)) {
        return false;
    }
    if ($f['anio'] > 0 && ($f['anio'] < $c['anios'][0] || $f['anio'] > $c['anios'][1])) {
        return false;
    }
    if ($f['cc'] !== '' && ((float) $f['cc'] < $c['cc'][0] - 0.001 || (float) $f['cc'] > $c['cc'][1] + 0.001)) {
        return false;
    }
    return true;
}

/** Texto en el que se busca (nombre, código con y sin guion, medida, categoría y compatibilidad). */
function cb_texto_busqueda(array $p): string
{
    $n = cb();
    return $p['codigo'] . ' ' . str_replace('-', '', $p['codigo']) . ' ' . $p['nombre'] . ' ' . $p['medida'] . ' '
        . $n['categorias'][$p['cat']][0] . ' ' . cb_compat_texto($p['compat']);
}

/** ¿Coincide con la búsqueda? Cada palabra debe aparecer (sin tildes ni mayúsculas). */
function cb_coincide_texto(array $p, string $q): bool
{
    $texto = demo_normalizar(cb_texto_busqueda($p));
    foreach (preg_split('/\s+/u', demo_normalizar($q)) ?: [] as $palabra) {
        if ($palabra !== '' && !str_contains($texto, $palabra) && !str_contains($texto, str_replace('-', '', $palabra))) {
            return false;
        }
    }
    return true;
}

/** Configuración del carrito en el navegador (_comun/js/carrito.js), con textos de cotización. El código va en el nombre. */
function cb_config_carrito(): array
{
    $n = cb();
    $catalogo = [];
    foreach ($n['productos'] as $id => $p) {
        $catalogo[$id] = ['nombre' => $p['codigo'] . ' ' . $p['nombre'], 'variantes' => $p['variantes']];
    }
    return [
        'clave' => 'demo-repuestos-multi-carrito',
        'negocio' => $n['nombre_largo'],
        'nota' => cb_nota(),
        'numero' => DEMO_WHATSAPP,
        'comunas' => $n['comunas_despacho'],
        'catalogo' => $catalogo,
        'textos' => [
            'saludo' => 'Hola, quiero cotizar en {negocio}:',
            'agregado' => 'Agregado a la cotización: {producto}',
            'ver_pedido' => 'Ver tu cotización ({n} repuestos)',
            'ver_pedido_uno' => 'Ver tu cotización (1 repuesto)',
        ],
    ];
}

/** Formulario "Agregar a la cotización", con selector de unidad o par si hay variantes. */
function cb_agregar(string $id, array $p): string
{
    $html = '<form class="demo-agregar cb-agregar" data-id="' . demo_e($id) . '" action="' . demo_e(CB_BASE . 'cotizar.php') . '" method="get">';
    $variantes = $p['variantes'];
    $primero = array_key_first($variantes);
    if (count($variantes) > 1) {
        $html .= '<label class="demo-sr" for="var-' . demo_e($id) . '">Formato de ' . demo_e($p['nombre']) . '</label>';
        $html .= '<select name="variante" id="var-' . demo_e($id) . '">';
        foreach ($variantes as $nombre => $precio) {
            $html .= '<option value="' . demo_e($nombre) . '">' . demo_e($nombre) . ' · ' . demo_e(demo_clp($precio)) . '</option>';
        }
        $html .= '</select>';
    } else {
        $html .= '<input type="hidden" name="variante" value="' . demo_e($primero) . '">';
        $html .= '<span class="cb-agregar__formato">' . demo_e($primero) . '</span>';
    }
    $html .= '<span class="cb-agregar__precio" data-precio>' . demo_e(demo_clp($variantes[$primero])) . '</span>';
    $html .= '<button type="submit" aria-label="Agregar ' . demo_e($p['nombre']) . ' a la cotización">Agregar</button>';
    return $html . '</form>';
}

/** Ficha de un producto en una fila (catálogo e inicio). $visible: false = oculto por el filtro sin JS. */
function cb_ficha(string $id, array $p, bool $visible = true): void
{
    $c = $p['compat'];
    ?>
<li class="cb-ficha" id="p-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e(cb_texto_busqueda($p)) ?>"
    data-tipos="<?= demo_e($c ? implode(' ', $c['tipos']) : '*') ?>"
    data-cc="<?= demo_e($c ? cb_cc($c['cc'][0]) . ' ' . cb_cc($c['cc'][1]) : '') ?>"
    data-anios="<?= demo_e($c ? $c['anios'][0] . ' ' . $c['anios'][1] : '') ?>"<?= $visible ? '' : ' hidden' ?>>
  <p class="cb-ficha__codigo"><?= demo_e($p['codigo']) ?></p>
  <div class="cb-ficha__datos">
    <h3><?= demo_e($p['nombre']) ?></h3>
    <p class="cb-ficha__medida"><?= demo_e($p['medida']) ?></p>
    <p class="cb-ficha__calza"><span class="cb-ficha__etiqueta">Calza</span> <?= demo_e(cb_compat_texto($c)) ?></p>
    <?php if (($p['modelo'] ?? '') !== ''): ?><?= demo_boton_3d(['id' => $id, 'nombre' => $p['nombre'], 'modelo' => $p['modelo'], 'foto' => cb()['categorias'][$p['cat']][2], 'objeto' => 'producto'], 'cb-btn-3d') ?><?php endif; ?>
  </div>
  <?= cb_agregar($id, $p) ?>
</li>
<?php
}

/** Buscador por vehículo: tipo, año y motor (GET, funciona sin JavaScript). */
function cb_buscador_vehiculo(array $f, string $clase = '', bool $conTexto = false): void
{
    ?>
<form class="cb-vehiculo <?= demo_e($clase) ?>" action="<?= demo_e(CB_BASE . 'catalogo.php') ?>" method="get" role="search" data-vehiculo>
  <p class="cb-vehiculo__titulo">Tu vehículo</p>
  <div class="cb-vehiculo__campos">
    <div class="cb-campo">
      <label for="v-tipo<?= demo_e($clase) ?>">Tipo</label>
      <select id="v-tipo<?= demo_e($clase) ?>" name="tipo" data-filtro="tipo">
        <option value="">Todos</option>
        <?php foreach (RP_TIPOS as $clave => $nombre): ?><option value="<?= demo_e($clave) ?>"<?= $f['tipo'] === $clave ? ' selected' : '' ?>><?= demo_e($nombre) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="cb-campo">
      <label for="v-anio<?= demo_e($clase) ?>">Año</label>
      <input id="v-anio<?= demo_e($clase) ?>" name="anio" inputmode="numeric" maxlength="4" placeholder="2016" autocomplete="off" value="<?= $f['anio'] > 0 ? (int) $f['anio'] : '' ?>" data-filtro="anio">
    </div>
    <div class="cb-campo">
      <label for="v-cc<?= demo_e($clase) ?>">Motor</label>
      <select id="v-cc<?= demo_e($clase) ?>" name="cc" data-filtro="cc">
        <option value="">Todos</option>
        <?php foreach (CB_CILINDRADAS as $cc): ?><option value="<?= demo_e($cc) ?>"<?= $f['cc'] === $cc ? ' selected' : '' ?>><?= demo_e($cc) ?></option><?php endforeach; ?>
      </select>
    </div>
    <?php if ($conTexto): ?>
    <div class="cb-campo cb-campo--texto">
      <label for="v-q">Nombre o código</label>
      <input type="search" id="v-q" name="q" maxlength="40" placeholder="Ej.: pastillas, CB-2101" autocomplete="off" value="<?= demo_e($f['q']) ?>" data-filtro="q">
    </div>
    <?php endif; ?>
    <button class="cb-btn" type="submit">Ver repuestos</button>
  </div>
</form>
<?php
}

/** Abre la página: head, aviso de demo y cabecera con menú. */
function cb_abrir(string $actual, string $titulo, string $descripcion): void
{
    $n = cb();
    demo_head([
        'titulo' => $titulo . ' · ' . $n['nombre_largo'] . ' (demo)',
        'descripcion' => $descripcion,
        'fuentes' => 'https://fonts.googleapis.com/css2?family=Anybody:wdth,wght@50..150,500..900&family=Inter+Tight:wght@400;500;600;700&family=Azeret+Mono:wght@400;600&display=swap',
        'css' => [CB_BASE . 'estilo.css'],
        'tema' => '#eceef0',
        'icono' => ['C', '#d4231b', '#fff'],
        'clase_body' => 'cb cb-pagina-' . $actual,
    ]);
    demo_aviso(cb_rubro());
    ?>
<header class="cb-cabecera">
  <div class="cb-ancho cb-cabecera__fila">
    <a class="cb-logo" href="<?= demo_e(CB_BASE) ?>" aria-label="<?= demo_e($n['nombre_largo']) ?>, inicio">
      <svg class="cb-logo__icono" viewBox="0 0 40 40" width="36" height="36" aria-hidden="true" focusable="false"><circle cx="17" cy="20" r="15" fill="none" stroke="currentColor" stroke-width="3"/><circle cx="17" cy="20" r="5" fill="none" stroke="currentColor" stroke-width="2.5"/><path d="M27 7.5h6a3 3 0 0 1 3 3v19a3 3 0 0 1-3 3h-6z" fill="#d4231b"/></svg>
      <span class="cb-logo__texto">Calibre<small>Frenos · Suspensión</small></span>
    </a>
    <nav class="cb-nav" aria-label="Menú principal"><?php cb_nav_items($actual); ?></nav>
    <a class="cb-carrito" href="<?= demo_e(CB_BASE . 'cotizar.php') ?>" data-carrito-etiqueta aria-label="Ver tu cotización"<?= $actual === 'cotizar' ? ' aria-current="page"' : '' ?>>
      <span>Cotización</span>
      <span class="cb-carrito__num" data-carrito-cantidad hidden>0</span>
    </a>
    <details class="cb-menu-movil">
      <summary aria-label="Abrir menú"><span></span><span></span></summary>
      <nav aria-label="Menú del sitio"><?php cb_nav_items($actual); ?></nav>
    </details>
  </div>
</header>
<?php
}

function cb_nav_items(string $actual): void
{
    foreach (cb_paginas() as $clave => [$archivo, $nombre]) {
        $activo = $clave === $actual;
        echo '<a href="', demo_e(CB_BASE . $archivo), '"', $activo ? ' aria-current="page" class="is-activo"' : '', '>', demo_e($nombre), '</a>';
    }
}

/** Titular de una página interior, con cota decorativa. */
function cb_titular(string $sobre, string $titulo, string $bajada): void
{
    ?>
<header class="cb-titular">
  <div class="cb-ancho">
    <p class="cb-sobre"><?= demo_e($sobre) ?></p>
    <h1><?= demo_e($titulo) ?></h1>
    <span class="cb-cota" aria-hidden="true"><span>±0,1 mm</span></span>
    <p class="cb-titular__bajada"><?= demo_e($bajada) ?></p>
  </div>
</header>
<?php
}

/** Cierra la página: pie, configuración del carrito y scripts. */
function cb_cerrar(array $scripts = []): void
{
    $n = cb();
    $config = json_encode(cb_config_carrito(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<footer class="cb-pie">
  <div class="cb-ancho cb-pie__grid">
    <div>
      <p class="cb-logo__texto cb-logo__texto--pie">Calibre<small>Frenos · Suspensión</small></p>
      <p><?= demo_e($n['eslogan']) ?>.</p>
      <p><?= demo_e($n['zona']) ?></p>
    </div>
    <div>
      <p class="cb-pie__titulo">Horario</p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario cb-pie__horario'); ?>
    </div>
    <div>
      <p class="cb-pie__titulo">Cotiza</p>
      <p><a href="<?= demo_e(cb_wa('Hola, quiero cotizar repuestos de frenos o suspensión en ' . $n['nombre_largo'] . '. Mi patente es: ')) ?>" target="_blank" rel="noopener">WhatsApp con tu patente</a></p>
      <p><a href="<?= demo_e(CB_BASE . 'catalogo.php') ?>">Buscar por vehículo</a></p>
      <p><a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
    </div>
  </div>
  <div class="cb-ancho"><?php demo_aviso_pie(cb_rubro()); ?></div>
</footer>
<script type="application/json" id="demo-carrito-config"><?= $config ?></script>
<?php
    demo_scripts(array_merge($scripts, ['carrito']));
    echo '<script src="', demo_e(demo_asset(CB_BASE . 'calibre.js')), '" defer></script>', "\n";
    echo "</body>\n</html>\n";
}
