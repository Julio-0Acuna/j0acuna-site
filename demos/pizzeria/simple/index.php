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

demo_head([
    'titulo' => $n['nombre'] . ' · Pizzería en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para una pizzería de barrio en ' . $n['comuna'] . ', Santiago: menú con QR, promociones, horario y pedidos por WhatsApp.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,800&family=Shrikhand&display=swap',
    'css' => ['/demos/pizzeria/simple/estilo.css'],
    'tema' => '#d62718',
    'icono' => ['V', '#d62718', '#fff4e0'],
    'clase_body' => 'vr',
]);
demo_aviso($rubro);
?>
<header class="vr-top">
  <div class="vr-envoltura vr-top__fila">
    <a class="vr-marca" href="#contenido">
      <span class="vr-marca__sello" aria-hidden="true">VR</span>
      <span class="vr-marca__nombre"><?= demo_e($n['nombre_corto']) ?><small>Pizzería · <?= demo_e($n['comuna']) ?></small></span>
    </a>
    <nav class="vr-nav" aria-label="Secciones">
      <a href="#pizzas">Pizzas</a>
      <a href="#promos">Promos</a>
      <a href="#menu">Menú</a>
      <a href="#horario">Horario</a>
      <a href="#google">Ubicación</a>
    </nav>
    <a class="vr-btn vr-btn--chico" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Pedir</span></a>
  </div>
</header>

<main id="contenido">
  <section class="vr-hero">
    <div class="vr-envoltura vr-hero__grid">
      <div class="vr-hero__texto">
        <p class="vr-etiqueta"><span class="vr-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"></span><?= demo_e($estado['texto']) ?></p>
        <h1><?= demo_e($n['nombre']) ?></h1>
        <p class="vr-hero__eslogan"><?= demo_e($n['eslogan']) ?></p>
        <p class="vr-hero__bajada"><?= demo_e($n['bajada']) ?></p>
        <div class="vr-acciones">
          <a class="vr-btn" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir por WhatsApp</span></a>
          <a class="vr-btn vr-btn--claro" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
      </div>
      <div class="vr-hero__foto">
        <?= demo_img($n['hero_foto'], 'Pizza recién horneada cortada en triángulos sobre una tabla de madera', ['lazy' => false, 'ancho' => 900, 'alto' => 900, 'sizes' => '(max-width: 760px) 90vw, 480px']) ?>
        <p class="vr-sello-giro" aria-hidden="true"><span>Hecha a mano en Ñuñoa</span></p>
      </div>
    </div>
  </section>

  <div class="vr-mantel" aria-hidden="true"></div>

  <section class="vr-seccion" id="pizzas" aria-labelledby="pizzas-titulo">
    <div class="vr-envoltura">
      <p class="vr-antetitulo">Las favoritas del barrio</p>
      <h2 id="pizzas-titulo">Pizzas destacadas</h2>
      <div class="vr-pizzas">
        <?php foreach ($n['destacadas'] as $p): ?>
        <article class="vr-pizza">
          <div class="vr-pizza__foto">
            <?= demo_img($p['foto'], 'Pizza ' . $p['nombre'], ['ancho' => 600, 'alto' => 600, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 600px) 90vw, 33vw']) ?>
            <?php if ($p['sello'] !== ''): ?><span class="vr-pizza__sello"><?= demo_e($p['sello']) ?></span><?php endif; ?>
          </div>
          <h3><?= demo_e($p['nombre']) ?></h3>
          <p><?= demo_e($p['desc']) ?></p>
          <div class="vr-pizza__pedir">
            <a class="vr-precio" href="<?= demo_e($wa('Hola, quiero pedir 1 pizza ' . $p['nombre'] . ' mediana (' . demo_clp($p['mediana']) . ').')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($p['nombre']) ?> mediana por WhatsApp, <?= demo_e(demo_clp($p['mediana'])) ?>">
              <small>Mediana</small><strong><?= demo_e(demo_clp($p['mediana'])) ?></strong>
            </a>
            <a class="vr-precio vr-precio--fuerte" href="<?= demo_e($wa('Hola, quiero pedir 1 pizza ' . $p['nombre'] . ' familiar (' . demo_clp($p['familiar']) . ').')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($p['nombre']) ?> familiar por WhatsApp, <?= demo_e(demo_clp($p['familiar'])) ?>">
              <small>Familiar</small><strong><?= demo_e(demo_clp($p['familiar'])) ?></strong>
            </a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <p class="vr-ayuda">Toca un precio y se abre WhatsApp con tu pedido escrito.</p>
    </div>
  </section>

  <section class="vr-seccion vr-seccion--rosa" id="promos" aria-labelledby="promos-titulo">
    <div class="vr-envoltura">
      <p class="vr-antetitulo">Para compartir</p>
      <h2 id="promos-titulo">Promociones</h2>
      <div class="vr-cupones">
        <?php foreach ($n['promos'] as $promo): ?>
        <article class="vr-cupon">
          <?= demo_img($promo['foto'], $promo['titulo'] . ': pizza recién servida', ['ancho' => 600, 'alto' => 450, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 760px) 90vw, 40vw', 'clase' => 'vr-cupon__foto']) ?>
          <div class="vr-cupon__texto">
            <h3><?= demo_e($promo['titulo']) ?></h3>
            <p><?= demo_e($promo['texto']) ?></p>
            <p class="vr-cupon__precio"><?= demo_e($promo['precio']) ?></p>
            <a class="vr-btn vr-btn--chico" href="<?= demo_e($wa($promo['mensaje'])) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>La quiero</span></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="vr-seccion vr-pizarra" id="menu" aria-labelledby="menu-titulo">
    <div class="vr-envoltura">
      <p class="vr-antetitulo">Todo lo que hacemos</p>
      <h2 id="menu-titulo">Menú</h2>
      <div class="vr-menu">
        <div class="vr-menu__columnas">
          <?php foreach ($n['menu'] as $categoria => $platos): ?>
          <div class="vr-menu__grupo">
            <h3><?= demo_e($categoria) ?></h3>
            <ul>
              <?php foreach ($platos as [$plato, $detalle, $precio]): ?>
              <li>
                <span class="vr-menu__linea"><strong><?= demo_e($plato) ?></strong><span class="vr-menu__puntos" aria-hidden="true"></span><span class="vr-menu__precio"><?= demo_e($precio) ?></span></span>
                <span class="vr-menu__detalle"><?= demo_e($detalle) ?>
                  <a href="<?= demo_e($wa('Hola, quiero pedir: ' . $plato . ' (' . $precio . ').')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($plato) ?> por WhatsApp">Pedir</a></span>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endforeach; ?>
        </div>
        <aside class="vr-menu__lado" aria-label="Menú en tu celular">
          <?php demo_qr('#menu', 'Escanea y mira este menú en tu celular', 'vr-qr'); ?>
          <p>Imprime este código y ponlo en tus mesas o en la caja: lleva directo al menú.</p>
          <div class="vr-menu__fotos">
            <?= demo_img($n['picar_foto'], 'Pan de ajo recién horneado', ['ancho' => 300, 'alto' => 300, 'anchos' => [300, 600], 'sizes' => '120px']) ?>
            <?= demo_img($n['bebida_foto'], 'Limonada natural con limón y menta', ['ancho' => 300, 'alto' => 300, 'anchos' => [300, 600], 'sizes' => '120px']) ?>
            <?= demo_img($n['postre_foto'], 'Brownie de chocolate con salsa', ['ancho' => 300, 'alto' => 300, 'anchos' => [300, 600], 'sizes' => '120px']) ?>
          </div>
        </aside>
      </div>
    </div>
  </section>

  <section class="vr-seccion" id="horario" aria-labelledby="horario-titulo">
    <div class="vr-envoltura vr-horario">
      <div>
        <p class="vr-antetitulo">Te esperamos</p>
        <h2 id="horario-titulo">Horario y entregas</h2>
        <p class="vr-estado-linea"><span class="vr-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario vr-tabla-horario'); ?>
      </div>
      <ul class="vr-entregas">
        <li><strong>Retiro en el local</strong><span>Pide por WhatsApp y pasa a buscarla en 20 minutos.</span></li>
        <li><strong>Despacho en <?= demo_e($n['comuna']) ?></strong><span>$1.990 dentro de la comuna. Gratis sobre $25.000.</span></li>
        <li><strong>Pago</strong><span>Efectivo, débito, crédito o transferencia.</span></li>
      </ul>
      <figure class="vr-horario__foto">
        <?= demo_img($n['barrio_foto'], 'Amigos compartiendo pizzas y bebidas en una mesa', ['ancho' => 700, 'alto' => 500, 'anchos' => [480, 700, 1000], 'sizes' => '(max-width: 760px) 90vw, 40vw']) ?>
      </figure>
    </div>
  </section>

  <div class="vr-seccion vr-seccion--crema">
    <div class="vr-envoltura">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería tu ficha en Google Maps: horario, cómo llegar y reseñas de tus clientes.']); ?>
    </div>
  </div>

  <section class="vr-cierre" aria-labelledby="cierre-titulo">
    <div class="vr-envoltura">
      <h2 id="cierre-titulo">¿Se te antojó?</h2>
      <p>Escríbenos y tu pizza sale del horno en 20 minutos.</p>
      <div class="vr-acciones">
        <a class="vr-btn vr-btn--claro" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir por WhatsApp</span></a>
        <a class="vr-btn vr-btn--borde" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
      </div>
    </div>
  </section>
</main>

<footer class="vr-pie">
  <div class="vr-envoltura">
    <p class="vr-pie__marca"><?= demo_e($n['nombre']) ?></p>
    <p><?= demo_e($n['comuna']) ?>, Santiago · Pedidos por WhatsApp</p>
    <?php demo_aviso_pie($rubro); ?>
  </div>
</footer>

<nav class="vr-barra" aria-label="Pedir">
  <a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
</nav>
<?php demo_scripts(['qr']); ?>
</body>
</html>
