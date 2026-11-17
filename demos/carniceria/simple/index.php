<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version);
const AR_BASE = '/demos/carniceria/simple/';

/** Enlace de WhatsApp con la nota de pedido de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);

/** 250 → "250 g"; 1500 → "1,5 kg". */
function ar_peso(int $gramos): string
{
    if ($gramos < 1000) {
        return $gramos . ' g';
    }
    return str_replace('.', ',', rtrim(rtrim(number_format($gramos / 1000, 2, '.', ''), '0'), '.')) . ' kg';
}

/** Total aproximado de un corte según su precio por kilo. */
function ar_total(int $kilo, int $gramos): int
{
    return (int) round($kilo * $gramos / 1000);
}

function ar_mensaje_corte(array $c, int $gramos): string
{
    return 'Hola, quiero pedir ' . ar_peso($gramos) . ' de ' . $c['nombre'] . ' (' . demo_clp(ar_total($c['kilo'], $gramos)) . ' aprox., a ' . demo_clp($c['kilo']) . ' el kg).';
}

// Pedido de un corte con su peso, sin JavaScript: ?pedir=lomo-vetado&gramos=1500 → WhatsApp con el pedido escrito.
// Solo cortes y pesos de la lista; cualquier otro valor muestra la página normal.
$pedir = $_GET['pedir'] ?? null;
if (is_string($pedir) && isset($n['cortes'][$pedir])) {
    $gramos = filter_var($_GET['gramos'] ?? '', FILTER_VALIDATE_INT);
    if (is_int($gramos) && in_array($gramos, $n['gramos'], true)) {
        header('Location: ' . $wa(ar_mensaje_corte($n['cortes'][$pedir], $gramos)), true, 303);
        exit;
    }
}

$waGeneral = $wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.');
$estado = demo_horario_estado($n['horario']);

/** Íconos de uso (SVG en línea, decorativos: el texto va al lado). */
$iconos = [
    'parrilla' => '<path d="M4 10h16a8 8 0 0 1-16 0zM8 4.5v2.5M12 3.5v3.5M16 4.5v2.5M8 17.5 6.5 21M16 17.5l1.5 3.5"/>',
    'olla' => '<path d="M5 9h14v7a4 4 0 0 1-4 4H9a4 4 0 0 1-4-4zM2.5 11H5M19 11h2.5M9 5.5h6M12 5.5V9"/>',
    'horno' => '<rect x="3.5" y="3.5" width="17" height="17" rx="2"/><path d="M3.5 8.5h17M7.5 6h.01M10.5 6h.01M7.5 12h9v5h-9z"/>',
    'sarten' => '<path d="M2.5 11h14a6 6 0 0 1-6 6h-2a6 6 0 0 1-6-6zM16.5 11.5l5-2.5"/>',
];
$icono = static fn (string $uso): string => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' . $iconos[$uso] . '</svg>';

demo_head([
    'titulo' => $n['nombre'] . ' · Carnicería en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para una carnicería de barrio en ' . $n['comuna'] . ', Santiago: cortes con su uso, venta por kilo con selector de gramos, packs para asado, lista de precios con QR y pedidos por WhatsApp.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@600;800;900&family=Work+Sans:wght@400;500;600;700&display=swap',
    'css' => [AR_BASE . 'estilo.css'],
    'tema' => '#1c3d9b',
    'icono' => ['A', '#1c3d9b', '#ffffff'],
    'clase_body' => 'ar',
]);
demo_aviso($rubro);
?>
<header class="ar-top">
  <div class="ar-envoltura ar-top__fila">
    <a class="ar-placa" href="#contenido" aria-label="<?= demo_e($n['nombre']) ?>, inicio">
      <small>Carnicería</small>
      <span>El Aromo</span>
    </a>
    <nav class="ar-nav" aria-label="Secciones">
      <a href="#ofertas">Ofertas</a>
      <a href="#cortes">Cortes</a>
      <a href="#packs">Packs para asado</a>
      <a href="#precios">Precios</a>
      <a href="#horario">Horario</a>
    </nav>
    <a class="ar-btn ar-btn--chico" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Pedir</span></a>
  </div>
</header>

<main id="contenido">
  <section class="ar-hero" aria-labelledby="ar-titulo">
    <div class="ar-envoltura ar-hero__grid">
      <div class="ar-hero__texto">
        <p class="ar-estado"><span class="ar-estado__punto<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <h1 id="ar-titulo"><span class="ar-hero__pre">Carnicería</span> El Aromo</h1>
        <p class="ar-hero__eslogan"><?= demo_e($n['eslogan']) ?>.</p>
        <p class="ar-hero__bajada"><?= demo_e($n['bajada']) ?></p>
        <div class="ar-acciones">
          <a class="ar-btn" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir por WhatsApp</span></a>
          <a class="ar-btn ar-btn--azul" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
        <ul class="ar-hero__datos" aria-label="Lo que nos distingue">
          <li><strong>Desde <?= (int) $n['desde'] ?></strong><span>en la misma esquina</span></li>
          <li><strong>Al momento</strong><span>molemos y cortamos a la vista</span></li>
          <li><strong>Mismo día</strong><span>despacho en el barrio</span></li>
        </ul>
      </div>
      <figure class="ar-hero__foto">
        <?= demo_img($n['fotos']['hero'], 'Vitrina refrigerada llena de cortes de vacuno frescos', ['lazy' => false, 'ancho' => 900, 'alto' => 1000, 'sizes' => '(max-width: 860px) 92vw, 500px']) ?>
        <p class="ar-etiqueta" aria-hidden="true"><small>Oferta</small>Asado de tira<strong>$10.990</strong><small>el kilo</small></p>
      </figure>
    </div>
  </section>

  <div class="ar-cenefa" aria-hidden="true"></div>
  <ul class="ar-cinta" aria-label="Servicios">
    <li>Molida al momento</li>
    <li>Cortes a pedido</li>
    <li>Pollo y cerdo frescos</li>
    <li>Embutidos de la casa</li>
    <li>Despacho el mismo día</li>
  </ul>

  <section class="ar-seccion ar-ofertas" id="ofertas" aria-labelledby="ofertas-titulo">
    <div class="ar-envoltura ar-ofertas__grid">
      <div class="ar-pizarra">
        <p class="ar-pizarra__ante">Esta semana en el mostrador</p>
        <h2 id="ofertas-titulo">Ofertas de la semana</h2>
        <ul class="ar-pizarra__lista">
          <?php foreach ($n['ofertas'] as [$producto, $detalle, $precio, $slug]): ?>
          <li>
            <span class="ar-pizarra__linea"><strong><?= demo_e($producto) ?></strong><span class="ar-puntos" aria-hidden="true"></span><span class="ar-pizarra__precio"><?= demo_e($precio) ?></span></span>
            <span class="ar-pizarra__detalle"><?= demo_e($detalle) ?>
              <a href="<?= demo_e($wa('Hola, quiero la oferta de la semana: ' . $producto . ', ' . $detalle . ' (' . $precio . ').')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($producto) ?> por WhatsApp">Pedir</a></span>
          </li>
          <?php endforeach; ?>
        </ul>
        <p class="ar-pizarra__pie">Válidas hasta el sábado o hasta agotar stock.</p>
      </div>
      <figure class="ar-ofertas__foto">
        <?= demo_img($n['fotos']['pizarra'], 'Carnicero trabajando una pieza entera en el mesón de madera', ['ancho' => 700, 'alto' => 860, 'anchos' => [480, 700, 1000], 'sizes' => '(max-width: 860px) 92vw, 40vw']) ?>
        <figcaption>Tomás, tercera generación de la familia, desposta en el mesón cada mañana.</figcaption>
      </figure>
    </div>
  </section>

  <section class="ar-seccion ar-cortes" id="cortes" aria-labelledby="cortes-titulo">
    <div class="ar-envoltura">
      <p class="ar-ante">Vacuno de primera</p>
      <h2 id="cortes-titulo">Cortes y para qué sirven</h2>
      <p class="ar-intro">Elige cómo lo vas a cocinar, el corte y los gramos. Se abre WhatsApp con tu pedido escrito y te lo dejamos pesado y envasado.</p>

      <fieldset class="ar-filtro">
        <legend>Ver cortes para</legend>
        <input class="demo-sr" type="radio" name="uso" id="uso-todos" value="todos" checked>
        <label for="uso-todos">Todos</label>
        <?php foreach ($n['usos'] as $clave => $nombre): ?>
        <input class="demo-sr" type="radio" name="uso" id="uso-<?= demo_e($clave) ?>" value="<?= demo_e($clave) ?>">
        <label for="uso-<?= demo_e($clave) ?>"><?= $icono($clave) ?><?= demo_e($nombre) ?></label>
        <?php endforeach; ?>
      </fieldset>

      <div class="ar-cortes__grilla">
        <?php foreach ($n['cortes'] as $slug => $c): ?>
        <article class="ar-corte" data-uso="<?= demo_e(implode(' ', $c['usos'])) ?>" id="corte-<?= demo_e($slug) ?>">
          <div class="ar-corte__foto">
            <?= demo_img($c['foto'], $c['nombre'] . ' crudo', ['ancho' => 600, 'alto' => 450, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 640px) 92vw, (max-width: 1000px) 45vw, 340px']) ?>
            <?php if ($c['sello'] !== ''): ?><span class="ar-corte__sello"><?= demo_e($c['sello']) ?></span><?php endif; ?>
          </div>
          <div class="ar-corte__cuerpo">
            <h3><?= demo_e($c['nombre']) ?></h3>
            <p class="ar-corte__kilo"><strong><?= demo_e(demo_clp($c['kilo'])) ?></strong> el kilo</p>
            <p class="ar-corte__desc"><?= demo_e($c['desc']) ?></p>
            <ul class="ar-usos" aria-label="Sirve para">
              <?php foreach ($c['usos'] as $uso): ?><li><?= $icono($uso) ?><?= demo_e($n['usos'][$uso]) ?></li><?php endforeach; ?>
            </ul>
            <form class="ar-pesar" action="<?= demo_e(AR_BASE) ?>" method="get" target="_blank" rel="noopener" data-kilo="<?= (int) $c['kilo'] ?>">
              <input type="hidden" name="pedir" value="<?= demo_e($slug) ?>">
              <div class="ar-pesar__fila">
                <label for="g-<?= demo_e($slug) ?>">Cantidad</label>
                <select id="g-<?= demo_e($slug) ?>" name="gramos">
                  <?php foreach ($n['gramos'] as $g): ?>
                  <option value="<?= $g ?>"<?= $g === $n['gramos_defecto'] ? ' selected' : '' ?>><?= demo_e(ar_peso($g)) ?> · <?= demo_e(demo_clp(ar_total($c['kilo'], $g))) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <p class="ar-pesar__total">Total aprox. <output for="g-<?= demo_e($slug) ?>" data-total><?= demo_e(demo_clp(ar_total($c['kilo'], $n['gramos_defecto']))) ?></output></p>
              <button class="ar-btn ar-btn--bloque" type="submit" aria-label="Pedir <?= demo_e($c['nombre']) ?> por WhatsApp"><?= demo_icono_wa(18) ?><span>Pedir por WhatsApp</span></button>
            </form>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <p class="ar-ayuda">El peso final puede variar unos gramos: cobramos lo que marca la balanza y te avisamos antes.</p>
    </div>
  </section>

  <section class="ar-seccion ar-packs" id="packs" aria-labelledby="packs-titulo">
    <div class="ar-envoltura">
      <p class="ar-ante">Para el fin de semana</p>
      <h2 id="packs-titulo">Packs para asado</h2>
      <p class="ar-intro">Calculamos unos 400 gramos de carne por persona, con el carbón incluido. Lo dejamos listo para retirar o te lo llevamos.</p>
      <div class="ar-packs__grilla">
        <?php foreach ($n['packs'] as $pack): ?>
        <article class="ar-ticket<?= !empty($pack['destacado']) ? ' ar-ticket--destacado' : '' ?>">
          <?= demo_img($pack['foto'], 'Carnes para asado del pack para ' . $pack['personas'] . ' personas', ['ancho' => 600, 'alto' => 380, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 860px) 92vw, 33vw', 'clase' => 'ar-ticket__foto']) ?>
          <div class="ar-ticket__cuerpo">
            <?php if (!empty($pack['destacado'])): ?><p class="ar-ticket__sello">El favorito</p><?php endif; ?>
            <h3><small>Pack para</small> <?= (int) $pack['personas'] ?> <small>personas</small></h3>
            <p class="ar-ticket__kilos"><?= demo_e($pack['kilos']) ?></p>
            <ul>
              <?php foreach ($pack['trae'] as $item): ?><li><?= demo_e($item) ?></li><?php endforeach; ?>
            </ul>
            <p class="ar-ticket__precio"><?= demo_e(demo_clp($pack['precio'])) ?></p>
            <a class="ar-btn ar-btn--bloque" href="<?= demo_e($wa('Hola, quiero el pack para asado de ' . $pack['personas'] . ' personas (' . demo_clp($pack['precio']) . '): ' . implode(', ', $pack['trae']) . '.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Lo quiero</span></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="ar-seccion ar-tambien" aria-labelledby="tambien-titulo">
    <div class="ar-envoltura">
      <p class="ar-ante">Y para completar</p>
      <h2 id="tambien-titulo">Pollo, cerdo, embutidos y carbón</h2>
      <div class="ar-tambien__grilla">
        <?php foreach ($n['tambien'] as $grupo => $items): ?>
        <div class="ar-grupo">
          <h3><?= demo_e($grupo) ?></h3>
          <ul>
            <?php foreach ($items as [$nombre, $detalle, $precio, $foto]): ?>
            <li>
              <?= demo_img($foto, $nombre, ['ancho' => 160, 'alto' => 160, 'anchos' => [160, 320], 'sizes' => '72px']) ?>
              <span class="ar-grupo__texto"><strong><?= demo_e($nombre) ?></strong><span><?= demo_e($detalle) ?></span></span>
              <span class="ar-grupo__precio"><?= demo_e($precio) ?>
                <a href="<?= demo_e($wa('Hola, quiero pedir: ' . $nombre . ' (' . $precio . ').')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($nombre) ?> por WhatsApp">Pedir</a></span>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="ar-seccion ar-lista" id="precios" aria-labelledby="precios-titulo">
    <div class="ar-envoltura ar-lista__grid">
      <div class="ar-hoja">
        <p class="ar-ante">Actualizada cada lunes</p>
        <h2 id="precios-titulo">Lista de precios</h2>
        <div class="ar-hoja__columnas">
          <?php foreach ($n['lista'] as $categoria => $filas): ?>
          <div class="ar-hoja__grupo">
            <h3><?= demo_e($categoria) ?></h3>
            <dl>
              <?php foreach ($filas as [$producto, $precio]): ?>
              <div><dt><?= demo_e($producto) ?></dt><dd><?= demo_e($precio) ?></dd></div>
              <?php endforeach; ?>
            </dl>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <aside class="ar-lista__qr" aria-label="Lista de precios en tu celular">
        <?php demo_qr('#precios', 'Escanea y mira la lista de precios en tu celular', 'ar-qr'); ?>
        <p>Imprime este código y pégalo en el mostrador o en la vitrina: tus clientes ven los precios del día y piden por WhatsApp desde la fila.</p>
      </aside>
    </div>
  </section>

  <section class="ar-seccion ar-horario" id="horario" aria-labelledby="horario-titulo">
    <div class="ar-envoltura ar-horario__grid">
      <div>
        <p class="ar-ante">Te esperamos</p>
        <h2 id="horario-titulo">Horario y despacho</h2>
        <p class="ar-estado ar-estado--linea"><span class="ar-estado__punto<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario ar-tabla-horario'); ?>
      </div>
      <ul class="ar-entregas">
        <li><strong>Retiro en el local</strong><span>Pide por WhatsApp y pasa sin hacer fila: te lo dejamos pesado, envasado y con tu nombre.</span></li>
        <li><strong>Despacho el mismo día</strong><span>En <?= demo_e(implode(', ', $n['comunas_despacho'])) ?> si pides antes de las 15:00. $1.990; gratis sobre $30.000.</span></li>
        <li><strong>Pago</strong><span>Efectivo, débito, crédito o transferencia.</span></li>
      </ul>
      <figure class="ar-horario__foto">
        <?= demo_img($n['fotos']['mostrador'], 'Carnicero cortando carne fresca detrás del mostrador', ['ancho' => 700, 'alto' => 525, 'anchos' => [480, 700, 1000], 'sizes' => '(max-width: 900px) 92vw, 33vw']) ?>
      </figure>
    </div>
  </section>

  <div class="ar-seccion ar-google-fondo">
    <div class="ar-envoltura">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería tu ficha en Google Maps: horario, cómo llegar y lo que dicen tus vecinos.']); ?>
    </div>
  </div>

  <section class="ar-cierre" aria-labelledby="cierre-titulo">
    <div class="ar-envoltura">
      <h2 id="cierre-titulo">¿Prendemos la parrilla?</h2>
      <p>Escríbenos con el corte y los gramos. Te lo dejamos listo en 20 minutos.</p>
      <div class="ar-acciones">
        <a class="ar-btn ar-btn--blanco" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir por WhatsApp</span></a>
        <a class="ar-btn ar-btn--borde" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
      </div>
    </div>
  </section>
</main>

<footer class="ar-pie">
  <div class="ar-envoltura">
    <p class="ar-placa ar-placa--pie"><small>Carnicería</small><span>El Aromo</span></p>
    <p><?= demo_e($n['comuna']) ?>, Santiago · Desde <?= (int) $n['desde'] ?> · Pedidos por WhatsApp</p>
    <?php demo_aviso_pie($rubro); ?>
  </div>
</footer>

<nav class="ar-barra" aria-label="Pedir">
  <a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
</nav>
<?php demo_scripts(['qr']); ?>
<script src="<?= demo_e(demo_asset(AR_BASE . 'gramos.js')) ?>" defer></script>
</body>
</html>
