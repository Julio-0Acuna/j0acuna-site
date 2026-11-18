<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/_edad.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version);

/** Enlace de WhatsApp con la nota de pedido de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);
$waGeneral = $wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.');
const LP_MAX_CANTIDAD = 12;

// Pedido rápido sin JavaScript: ?pedir=<producto>&cantidad=<1..12> → WhatsApp con el total escrito.
$pedir = is_string($_GET['pedir'] ?? null) ? $_GET['pedir'] : '';
$cantidad = filter_var($_GET['cantidad'] ?? '1', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => LP_MAX_CANTIDAD]]);
if ($pedir !== '' && isset($n['productos'][$pedir]) && $cantidad !== false) {
    $p = $n['productos'][$pedir];
    $texto = 'Hola, quiero pedir en ' . $n['nombre'] . ":\n"
        . '• ' . $cantidad . ' × ' . $p['nombre'] . ' – ' . demo_clp($p['precio'] * $cantidad) . "\n"
        . 'Despacho en ' . $n['comuna'] . ': ' . demo_clp($n['despacho']) . ' (gratis sobre ' . demo_clp($n['despacho_gratis']) . ").\n"
        . 'Mi dirección es: ';
    header('Location: ' . $wa($texto), true, 303);
    exit;
}

$estado = demo_horario_estado($n['horario']);

/** Chapa (tapa de botella) dibujada en SVG: el logo de La Pasada. */
function lp_chapa(int $tam = 44): string
{
    $puntos = [];
    $total = 42;
    for ($i = 0; $i < $total; $i++) {
        $r = $i % 2 === 0 ? 49 : 44.5;
        $a = $i / $total * 2 * M_PI;
        $puntos[] = round(50 + $r * cos($a), 2) . ',' . round(50 + $r * sin($a), 2);
    }
    return '<svg class="lp-chapa" viewBox="0 0 100 100" width="' . $tam . '" height="' . $tam . '" aria-hidden="true" focusable="false">'
        . '<polygon points="' . implode(' ', $puntos) . '" fill="currentColor"/>'
        . '<circle cx="50" cy="50" r="35" fill="none" stroke="#0b1f45" stroke-width="3" stroke-dasharray="2 4"/>'
        . '<text x="50" y="63" text-anchor="middle" font-family="Anton, Impact, sans-serif" font-size="38" fill="#0b1f45">LP</text></svg>';
}

$cantidades = range(1, 6);

demo_head([
    'titulo' => $n['nombre'] . ' · Botillería con delivery en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para una botillería de barrio con delivery en ' . $n['comuna'] . ', Santiago: packs, promociones de fin de semana, lista de precios con QR y pedidos por WhatsApp.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Anton&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,700&family=Kalam:wght@700&display=swap',
    'css' => ['/demos/botilleria/simple/estilo.css'],
    'tema' => '#0b1f45',
    'icono' => ['P', '#ffd23f', '#0b1f45'],
    'clase_body' => 'lp',
    'extra_head' => bot_edad_head('demo-botilleria-simple-edad'),
]);
demo_aviso($rubro);
bot_edad([
    'clave' => 'demo-botilleria-simple-edad',
    'negocio' => $n['nombre'],
    'clase' => 'lp-edad',
    'marca' => '<p class="edad__marca">' . lp_chapa(56) . '<span>' . demo_e($n['nombre']) . '</span></p>',
]);
?>
<p class="lp-tira-edad"><?= bot_icono_18(18) ?><span><?= demo_e(BOT_AVISO_EDAD) ?></span></p>
<header class="lp-top">
  <div class="lp-envoltura lp-top__fila">
    <a class="lp-marca" href="#contenido" aria-label="<?= demo_e($n['nombre']) ?>, inicio">
      <?= lp_chapa(46) ?>
      <span class="lp-marca__texto"><strong>La Pasada</strong><small>Botillería · <?= demo_e($n['comuna']) ?></small></span>
    </a>
    <nav class="lp-nav" aria-label="Secciones">
      <a href="#productos">Lo que más sale</a>
      <a href="#packs">Packs</a>
      <a href="#promos">Promos</a>
      <a href="#delivery">Delivery</a>
      <a href="#precios">Precios</a>
    </nav>
    <a class="lp-btn lp-btn--amarillo lp-btn--chico" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Pedir</span></a>
  </div>
</header>

<main id="contenido">
  <section class="lp-hero" aria-labelledby="lp-hero-titulo">
    <div class="lp-envoltura lp-hero__grid">
      <div class="lp-hero__texto">
        <p class="lp-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <h1 id="lp-hero-titulo"><span class="lp-hero__frio">Helado</span> y a la puerta</h1>
        <p class="lp-hero__bajada">Cervezas, vinos, piscos, hielo y bebidas con delivery en <?= demo_e($n['comuna']) ?>. Pide por WhatsApp, paga al recibir y te lo llevamos en 30 a 45 minutos.</p>
        <div class="lp-acciones">
          <a class="lp-btn lp-btn--amarillo" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir por WhatsApp</span></a>
          <a class="lp-btn lp-btn--borde" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
        <dl class="lp-datos">
          <div><dt>Entrega</dt><dd>30–45 min</dd></div>
          <div><dt>Despacho</dt><dd><?= demo_e(demo_clp($n['despacho'])) ?></dd></div>
          <div><dt>Gratis sobre</dt><dd><?= demo_e(demo_clp($n['despacho_gratis'])) ?></dd></div>
        </dl>
      </div>
      <div class="lp-hero__visual">
        <figure class="lp-refri">
          <?= demo_img($n['fotos']['hero'], 'Dos latas amarillas recién sacadas del refrigerador', ['lazy' => false, 'ancho' => 900, 'alto' => 1000, 'sizes' => '(max-width: 860px) 88vw, 460px']) ?>
          <span class="lp-refri__manilla" aria-hidden="true"></span>
        </figure>
        <p class="lp-etiqueta-precio lp-etiqueta-precio--hero" aria-hidden="true"><small>Hielo 2 kg</small><strong><?= demo_e(demo_clp(1990)) ?></strong></p>
        <p class="lp-sticker" aria-hidden="true">Siempre<br>helada</p>
      </div>
    </div>
  </section>

  <nav class="lp-pasillos" aria-label="Categorías de la lista de precios">
    <div class="lp-envoltura lp-pasillos__fila">
      <?php foreach (array_keys($n['precios']) as $i => $cat): ?>
      <a href="#cat-<?= $i ?>"><span aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></span><?= demo_e($cat) ?></a>
      <?php endforeach; ?>
    </div>
  </nav>

  <section class="lp-seccion lp-seccion--hielo" id="productos" aria-labelledby="productos-titulo">
    <div class="lp-envoltura">
      <header class="lp-cabeza">
        <p class="lp-antetitulo">El refrigerador del barrio</p>
        <h2 id="productos-titulo">Lo que más sale</h2>
        <p>Elige cuántos y toca <strong>Pedir</strong>: se abre WhatsApp con tu pedido y el total ya escrito.</p>
      </header>
      <div class="lp-estante">
        <?php foreach ($n['productos'] as $id => $p): ?>
        <article class="lp-producto" id="p-<?= demo_e($id) ?>">
          <div class="lp-producto__foto">
            <?= demo_img($p['foto'], $p['nombre'], ['ancho' => 600, 'alto' => 450, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 560px) 92vw, (max-width: 980px) 45vw, 280px']) ?>
            <?php if ($p['sello'] !== ''): ?><span class="lp-producto__sello"><?= demo_e($p['sello']) ?></span><?php endif; ?>
          </div>
          <div class="lp-producto__cuerpo">
            <h3><?= demo_e($p['nombre']) ?></h3>
            <p><?= demo_e($p['desc']) ?></p>
          </div>
          <form class="lp-riel" method="get" action="#p-<?= demo_e($id) ?>" target="_blank">
            <input type="hidden" name="pedir" value="<?= demo_e($id) ?>">
            <p class="lp-etiqueta-precio"><strong><?= demo_e(demo_clp($p['precio'])) ?></strong></p>
            <label class="demo-sr" for="cant-<?= demo_e($id) ?>">Cantidad de <?= demo_e($p['nombre']) ?></label>
            <select id="cant-<?= demo_e($id) ?>" name="cantidad">
              <?php foreach ($cantidades as $c): ?><option value="<?= $c ?>"><?= $c ?></option><?php endforeach; ?>
            </select>
            <button type="submit" aria-label="Pedir <?= demo_e($p['nombre']) ?> por WhatsApp"><?= demo_icono_wa(16) ?><span>Pedir</span></button>
          </form>
        </article>
        <?php endforeach; ?>
        <article class="lp-producto lp-producto--mas">
          <p class="lp-producto__grande" aria-hidden="true">+30</p>
          <h3>¿Buscas otra cosa?</h3>
          <p>Gin, ron, whisky, espumantes, jugos, frutos secos y más en la lista de precios.</p>
          <a class="lp-btn lp-btn--azul lp-btn--chico" href="#precios">Ver la lista completa</a>
        </article>
      </div>
      <p class="lp-aviso-edad"><?= bot_icono_18(20) ?><span><strong><?= demo_e(BOT_AVISO_EDAD) ?>.</strong> Si quien recibe no muestra su carnet, el pedido vuelve al local.</span></p>
    </div>
  </section>

  <section class="lp-seccion lp-seccion--noche" id="packs" aria-labelledby="packs-titulo">
    <div class="lp-envoltura">
      <header class="lp-cabeza lp-cabeza--claro">
        <p class="lp-antetitulo">Armados y listos</p>
        <h2 id="packs-titulo">Packs para el asado y el cumpleaños</h2>
        <p>Todo en un solo pedido, con hielo incluido y a mejor precio que por separado.</p>
      </header>
      <div class="lp-packs">
        <?php foreach ($n['packs'] as $pack): ?>
        <article class="lp-pack" id="pack-<?= demo_e($pack['id']) ?>">
          <div class="lp-pack__foto">
            <?= demo_img($pack['foto'], $pack['alt'], ['ancho' => 700, 'alto' => 480, 'anchos' => [480, 700, 1000], 'sizes' => '(max-width: 860px) 92vw, 33vw']) ?>
            <span class="lp-pack__para"><?= demo_e($pack['para']) ?></span>
          </div>
          <div class="lp-pack__cuerpo">
            <h3><?= demo_e($pack['titulo']) ?></h3>
            <ul>
              <?php foreach ($pack['trae'] as $item): ?><li><?= demo_e($item) ?></li><?php endforeach; ?>
            </ul>
            <p class="lp-pack__precio"><strong><?= demo_e(demo_clp($pack['precio'])) ?></strong> <s>Por separado <?= demo_e(demo_clp($pack['normal'])) ?></s></p>
            <a class="lp-btn lp-btn--amarillo lp-btn--bloque" href="<?= demo_e($wa('Hola, quiero el ' . $pack['titulo'] . ' (' . demo_clp($pack['precio']) . '): ' . implode(', ', $pack['trae']) . '.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Lo quiero</span></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="lp-seccion lp-promos" id="promos" aria-labelledby="promos-titulo">
    <div class="lp-promos__fondo" aria-hidden="true">
      <?= demo_img($n['fotos']['hielo'], '', ['ancho' => 1400, 'alto' => 800, 'anchos' => [900, 1400, 2000], 'sizes' => '100vw']) ?>
    </div>
    <div class="lp-envoltura">
      <header class="lp-cabeza lp-cabeza--claro">
        <p class="lp-antetitulo">Viernes, sábado y domingo</p>
        <h2 id="promos-titulo">Promos de fin de semana</h2>
      </header>
      <div class="lp-cupones">
        <?php foreach ($n['promos'] as $i => $promo): ?>
        <article class="lp-cupon<?= $i === 1 ? ' lp-cupon--coral' : '' ?>">
          <div class="lp-cupon__cabeza">
            <p class="lp-cupon__dias"><?= demo_e($promo['dias']) ?></p>
            <h3><?= demo_e($promo['titulo']) ?></h3>
            <p class="lp-cupon__precio"><?= demo_e($promo['precio']) ?></p>
          </div>
          <div class="lp-cupon__pie">
            <p><?= demo_e($promo['detalle']) ?></p>
            <a class="lp-cupon__btn" href="<?= demo_e($wa($promo['mensaje'])) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(16) ?><span>Pedir la promo</span></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <p class="lp-promos__nota">Promociones válidas hasta agotar stock. Disfruta con responsabilidad: si bebes, no manejes.</p>
    </div>
  </section>

  <section class="lp-seccion" id="delivery" aria-labelledby="delivery-titulo">
    <div class="lp-envoltura lp-delivery">
      <figure class="lp-delivery__foto">
        <?= demo_img($n['fotos']['moto'], 'Repartidor en moto con caja de delivery recorriendo la ciudad de noche', ['ancho' => 800, 'alto' => 900, 'anchos' => [480, 800, 1200], 'sizes' => '(max-width: 860px) 92vw, 42vw']) ?>
        <figcaption><span class="lp-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></span></figcaption>
      </figure>
      <div>
        <p class="lp-antetitulo">Delivery en el barrio</p>
        <h2 id="delivery-titulo">Te lo llevamos en <?= demo_e($n['comuna']) ?></h2>
        <p>Despacho <?= demo_e(demo_clp($n['despacho'])) ?> a todo <?= demo_e($n['comuna']) ?>, gratis sobre <?= demo_e(demo_clp($n['despacho_gratis'])) ?>. Recibimos pedidos hasta 30 minutos antes del cierre.</p>
        <ul class="lp-sectores" aria-label="Tiempo de entrega por sector">
          <?php foreach ($n['sectores'] as [$sector, $tiempo]): ?>
          <li><span><?= demo_e($sector) ?></span><strong><?= demo_e($tiempo) ?></strong></li>
          <?php endforeach; ?>
        </ul>
        <div class="lp-delivery__horario">
          <h3>Horario</h3>
          <?php demo_horario_tabla($n['horario'], 'demo-horario lp-horario'); ?>
        </div>
        <ul class="lp-reglas">
          <li><strong>Pago al recibir</strong> con efectivo, débito, crédito o transferencia.</li>
          <li><strong>Se verifica la edad en la entrega:</strong> pedimos el carnet a quien recibe. No dejamos pedidos a menores de 18 años.</li>
          <li><strong>Retiro en el local:</strong> pide por WhatsApp y pasa a buscarlo en 10 minutos.</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="lp-seccion lp-seccion--hielo" id="precios" aria-labelledby="precios-titulo">
    <div class="lp-envoltura">
      <header class="lp-cabeza">
        <p class="lp-antetitulo">Precios de hoy</p>
        <h2 id="precios-titulo">Lista de precios</h2>
        <p>Toca un producto para pedirlo por WhatsApp. Precios con IVA incluido.</p>
      </header>
      <div class="lp-lista">
        <div class="lp-lista__grupos">
          <?php foreach (array_values($n['precios']) as $i => $items): $cat = array_keys($n['precios'])[$i]; ?>
          <section class="lp-lista__grupo" id="cat-<?= $i ?>" aria-labelledby="cat-<?= $i ?>-titulo">
            <h3 id="cat-<?= $i ?>-titulo"><span aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></span><?= demo_e($cat) ?></h3>
            <ul>
              <?php foreach ($items as [$nombre, $detalle, $precio]): ?>
              <li>
                <a href="<?= demo_e($wa('Hola, quiero pedir: ' . $nombre . ' (' . demo_clp($precio) . ').')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($nombre) ?> por WhatsApp, <?= demo_e(demo_clp($precio)) ?>">
                  <span class="lp-lista__nombre"><?= demo_e($nombre) ?><small><?= demo_e($detalle) ?></small></span>
                  <span class="lp-lista__precio"><?= demo_e(demo_clp($precio)) ?></span>
                </a>
              </li>
              <?php endforeach; ?>
            </ul>
          </section>
          <?php endforeach; ?>
        </div>
        <aside class="lp-lista__lado" aria-label="Lista de precios en tu celular">
          <div class="lp-iman">
            <p class="lp-iman__titulo">Pégalo en el refri</p>
            <?php demo_qr('#precios', 'Escanea y mira la lista de precios en tu celular', 'lp-qr'); ?>
            <p>Este código va en un imán que regalamos con cada pedido: lleva directo a esta lista y a WhatsApp.</p>
          </div>
          <figure class="lp-lista__foto">
            <?= demo_img($n['fotos']['cubo'], 'Cubo azul lleno de cubos de hielo', ['ancho' => 600, 'alto' => 600, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 860px) 60vw, 300px']) ?>
          </figure>
        </aside>
      </div>
    </div>
  </section>

  <div class="lp-seccion lp-seccion--google">
    <div class="lp-envoltura">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería tu ficha en Google Maps: horario, cómo llegar y reseñas de tus clientes del barrio.']); ?>
    </div>
  </div>

  <section class="lp-cierre" aria-labelledby="cierre-titulo">
    <figure class="lp-cierre__foto" aria-hidden="true">
      <?= demo_img($n['fotos']['abierto'], '', ['ancho' => 900, 'alto' => 900, 'anchos' => [480, 900], 'sizes' => '(max-width: 860px) 60vw, 360px']) ?>
    </figure>
    <div class="lp-envoltura lp-cierre__texto">
      <h2 id="cierre-titulo">¿Se acabó el hielo?</h2>
      <p>Escríbenos ahora y llega helado en menos de una hora.</p>
      <div class="lp-acciones">
        <a class="lp-btn lp-btn--amarillo" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir por WhatsApp</span></a>
        <a class="lp-btn lp-btn--borde" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
      </div>
    </div>
  </section>
</main>

<footer class="lp-pie">
  <div class="lp-envoltura lp-pie__fila">
    <div class="lp-pie__marca"><?= lp_chapa(40) ?><p><strong><?= demo_e($n['nombre']) ?></strong><br><?= demo_e($n['comuna']) ?>, Santiago · Delivery en el barrio</p></div>
    <p class="lp-pie__edad"><?= bot_icono_18(20) ?><span><?= demo_e(BOT_AVISO_EDAD) ?>. Disfruta con responsabilidad: si bebes, no manejes.</span></p>
  </div>
  <div class="lp-envoltura"><?php demo_aviso_pie($rubro); ?></div>
</footer>

<nav class="lp-barra" aria-label="Pedir">
  <a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
</nav>
<?php demo_scripts(['qr']); ?>
<?= bot_edad_script() ?>

</body>
</html>
