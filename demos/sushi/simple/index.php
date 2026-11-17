<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version);

/** Enlace de WhatsApp con la nota de pedido de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);
$waGeneral = $wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.');
$estado = demo_horario_estado($n['horario']);
$cierreTarde = max(array_map(static fn (array $h): string => $h[1], array_filter($n['horario'])));
$despachoDesde = min($n['comunas']);

demo_head([
    'titulo' => $n['nombre'] . ' · Sushi con delivery nocturno en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para un local de sushi de barrio con delivery nocturno en ' . $n['comuna'] . ', Santiago: rolls, combos, menú con QR y pedidos por WhatsApp.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Dela+Gothic+One&family=Zen+Maru+Gothic:wght@400;500;700;900&display=swap',
    'css' => ['/demos/sushi/simple/estilo.css'],
    'tema' => '#0a0b1a',
    'icono' => ['M', '#ff3d8b', '#0a0b1a'],
    'clase_body' => 'mn',
]);
demo_aviso($rubro);
?>
<header class="mn-top">
  <div class="mn-envoltura mn-top__fila">
    <a class="mn-marca" href="#contenido" aria-label="<?= demo_e($n['nombre']) ?>, inicio">
      <span class="mn-marca__nombre">Maki<span>Neón</span></span>
      <span class="mn-marca__lugar">Sushi · <?= demo_e($n['comuna']) ?></span>
    </a>
    <nav class="mn-nav" aria-label="Secciones">
      <a href="#rolls">Rolls</a>
      <a href="#combos">Combos</a>
      <a href="#menu">Menú</a>
      <a href="#delivery">Delivery</a>
      <a href="#google">Ubicación</a>
    </nav>
    <a class="mn-btn mn-btn--chico" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Pedir</span></a>
  </div>
</header>

<main id="contenido">
  <section class="mn-hero" aria-labelledby="mn-titulo">
    <div class="mn-envoltura mn-hero__grid">
      <div class="mn-hero__texto">
        <p class="mn-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <h1 id="mn-titulo" class="mn-hero__titulo"><span class="mn-neon">Maki</span> <span class="mn-neon mn-neon--cian">Neón</span></h1>
        <p class="mn-hero__eslogan"><?= demo_e($n['eslogan']) ?></p>
        <p class="mn-hero__bajada"><?= demo_e($n['bajada']) ?></p>
        <div class="mn-acciones">
          <a class="mn-btn" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir por WhatsApp</span></a>
          <a class="mn-btn mn-btn--linea" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
        <ul class="mn-datos" aria-label="Datos del delivery">
          <li><strong>~40 min</strong><span>a tu puerta</span></li>
          <li><strong><?= demo_e(demo_clp($despachoDesde)) ?></strong><span>despacho desde</span></li>
          <li><strong><?= demo_e($cierreTarde) ?></strong><span>cierre vie y sáb</span></li>
        </ul>
      </div>
      <div class="mn-hero__foto">
        <span class="mn-kana" aria-hidden="true">寿司</span>
        <figure class="mn-marco">
          <?= demo_img($n['hero_foto'], 'Nigiri de salmón con huevas sostenido con palillos sobre rolls de salmón', ['lazy' => false, 'ancho' => 900, 'alto' => 1100, 'sizes' => '(max-width: 860px) 88vw, 460px']) ?>
        </figure>
        <p class="mn-sticker" aria-hidden="true"><span>Abierto<br>hasta las<br><strong>3 AM</strong></span></p>
      </div>
    </div>
  </section>

  <div class="mn-cinta" aria-hidden="true">
    <div class="mn-cinta__pista">
      <?php for ($i = 0; $i < 2; $i++): ?>
      <span>Rolls</span><span>Nigiris</span><span>Gyozas</span><span>Ramen</span><span>Delivery nocturno</span><span>Combos para compartir</span>
      <?php endfor; ?>
    </div>
  </div>

  <section class="mn-seccion" id="rolls" aria-labelledby="rolls-titulo">
    <div class="mn-envoltura">
      <header class="mn-cabeza">
        <p class="mn-antetitulo">Los que más salen de noche</p>
        <h2 id="rolls-titulo">Rolls de la casa</h2>
        <p>Diez piezas por roll, enrolladas cuando llega tu pedido. Toca el precio y se abre WhatsApp con el pedido escrito.</p>
      </header>
      <div class="mn-rolls">
        <?php foreach ($n['destacados'] as $p): ?>
        <article class="mn-roll">
          <div class="mn-roll__foto">
            <?= demo_img($p['foto'], $p['alt'], ['ancho' => 600, 'alto' => 450, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 640px) 92vw, (max-width: 1000px) 45vw, 340px']) ?>
            <?php if ($p['sello'] !== ''): ?><span class="mn-roll__sello"><?= demo_e($p['sello']) ?></span><?php endif; ?>
          </div>
          <div class="mn-roll__texto">
            <h3><?= demo_e($p['nombre']) ?></h3>
            <p><?= demo_e($p['desc']) ?></p>
          </div>
          <a class="mn-precio" href="<?= demo_e($wa('Hola, quiero pedir 1 ' . $p['nombre'] . ' (' . demo_clp($p['precio']) . ').')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($p['nombre']) ?> por WhatsApp, <?= demo_e(demo_clp($p['precio'])) ?>">
            <span>Pedir</span><strong><?= demo_e(demo_clp($p['precio'])) ?></strong>
          </a>
        </article>
        <?php endforeach; ?>
      </div>
      <p class="mn-desliza" aria-hidden="true">Desliza para ver más rolls →</p>
    </div>
  </section>

  <section class="mn-seccion mn-seccion--combos" id="combos" aria-labelledby="combos-titulo">
    <div class="mn-envoltura">
      <header class="mn-cabeza">
        <p class="mn-antetitulo">Para la junta</p>
        <h2 id="combos-titulo">Combos y tablas</h2>
      </header>
      <div class="mn-letreros">
        <?php foreach ($n['promos'] as $promo): ?>
        <article class="mn-letrero mn-letrero--<?= demo_e($promo['tono']) ?>">
          <?= demo_img($promo['foto'], $promo['alt'], ['ancho' => 600, 'alto' => 420, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 900px) 92vw, 360px', 'clase' => 'mn-letrero__foto']) ?>
          <div class="mn-letrero__texto">
            <p class="mn-letrero__piezas"><?= demo_e($promo['piezas']) ?></p>
            <h3><?= demo_e($promo['titulo']) ?></h3>
            <p><?= demo_e($promo['texto']) ?></p>
            <div class="mn-letrero__pie">
              <p class="mn-letrero__precio"><?= demo_e(demo_clp($promo['precio'])) ?></p>
              <a class="mn-btn mn-btn--chico" href="<?= demo_e($wa($promo['mensaje'])) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($promo['titulo']) ?> por WhatsApp"><?= demo_icono_wa(18) ?><span>Lo quiero</span></a>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="mn-seccion mn-seccion--menu" id="menu" aria-labelledby="menu-titulo">
    <div class="mn-envoltura">
      <header class="mn-cabeza">
        <p class="mn-antetitulo">Toda la carta</p>
        <h2 id="menu-titulo">Menú</h2>
      </header>
      <div class="mn-menu">
        <div class="mn-menu__grupos">
          <?php foreach ($n['menu'] as $categoria => $platos): ?>
          <div class="mn-menu__grupo">
            <h3><?= demo_e($categoria) ?></h3>
            <ul>
              <?php foreach ($platos as [$plato, $detalle, $precio]): ?>
              <li>
                <span class="mn-menu__plato"><strong><?= demo_e($plato) ?></strong><small><?= demo_e($detalle) ?></small></span>
                <span class="mn-menu__precio"><?= demo_e(demo_clp($precio)) ?></span>
                <a class="mn-menu__pedir" href="<?= demo_e($wa('Hola, quiero pedir: ' . $plato . ' (' . demo_clp($precio) . ').')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($plato) ?> por WhatsApp">+</a>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endforeach; ?>
        </div>
        <aside class="mn-menu__lado" aria-label="Menú en tu celular">
          <div class="mn-qr-caja">
            <?php demo_qr('#menu', 'Escanea y abre este menú en tu celular', 'mn-qr'); ?>
            <p>Este código va en el mesón, en las bolsas de delivery y en los volantes: lleva directo a la carta.</p>
          </div>
          <div class="mn-menu__fotos">
            <?php foreach ($n['menu_fotos'] as [$foto, $alt]): ?>
            <?= demo_img($foto, $alt, ['ancho' => 300, 'alto' => 300, 'anchos' => [300, 600], 'sizes' => '(max-width: 980px) 30vw, 100px']) ?>
            <?php endforeach; ?>
          </div>
        </aside>
      </div>
    </div>
  </section>

  <section class="mn-delivery" id="delivery" aria-labelledby="delivery-titulo">
    <?= demo_img($n['letrero_foto'], '', ['ancho' => 1400, 'alto' => 900, 'anchos' => [800, 1400, 2000], 'sizes' => '100vw', 'clase' => 'mn-delivery__fondo']) ?>
    <div class="mn-envoltura mn-delivery__grid">
      <div>
        <p class="mn-antetitulo">De 18:30 a madrugada</p>
        <h2 id="delivery-titulo">Delivery <span class="mn-neon">nocturno</span></h2>
        <ol class="mn-pasos">
          <li><strong>Escribes</strong><span>por WhatsApp o llamas</span></li>
          <li><strong>Enrollamos</strong><span>tu pedido al momento</span></li>
          <li><strong>Sale la moto</strong><span>con bolsa térmica</span></li>
          <li><strong>~40 minutos</strong><span>y está en tu puerta</span></li>
        </ol>
      </div>
      <div class="mn-vidrio" id="horario">
        <h3>Horario</h3>
        <p class="mn-estado mn-estado--linea <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario mn-horario'); ?>
      </div>
      <div class="mn-vidrio">
        <h3>Zonas y costo</h3>
        <dl class="mn-zonas">
          <?php foreach ($n['comunas'] as $comuna => $costo): ?>
          <div><dt><?= demo_e($comuna) ?></dt><dd><?= demo_e(demo_clp($costo)) ?></dd></div>
          <?php endforeach; ?>
        </dl>
        <p class="mn-pequeno">Gratis en <?= demo_e($n['comuna']) ?> sobre $20.000. Pagas al recibir con efectivo, débito o transferencia.</p>
      </div>
    </div>
  </section>

  <div class="mn-seccion mn-seccion--google">
    <div class="mn-envoltura">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería tu ficha en Google Maps: horario de delivery, cómo llegar y reseñas de tus clientes.']); ?>
    </div>
  </div>

  <section class="mn-cierre" aria-labelledby="cierre-titulo">
    <div class="mn-envoltura mn-cierre__grid">
      <div>
        <h2 id="cierre-titulo">¿Antojo a medianoche?</h2>
        <p>Escríbenos y tu sushi sale enrollado al momento. Seguimos abiertos cuando todo lo demás ya cerró.</p>
        <div class="mn-acciones">
          <a class="mn-btn mn-btn--noche" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir por WhatsApp</span></a>
          <a class="mn-btn mn-btn--linea-oscura" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
      </div>
      <?= demo_img($n['cierre_foto'], 'Torre de rolls de salmón y palta que parece flotar sobre fondo oscuro', ['ancho' => 600, 'alto' => 600, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 860px) 70vw, 360px', 'clase' => 'mn-cierre__foto']) ?>
    </div>
  </section>
</main>

<footer class="mn-pie">
  <div class="mn-envoltura">
    <p class="mn-marca__nombre">Maki<span>Neón</span></p>
    <p><?= demo_e($n['comuna']) ?>, Santiago · Delivery nocturno por WhatsApp</p>
    <?php demo_aviso_pie($rubro); ?>
  </div>
</footer>

<nav class="mn-barra" aria-label="Pedir">
  <a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
</nav>
<?php demo_scripts(['qr']); ?>
</body>
</html>
