<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = tn();
$ahora = tn_ahora();
$ofertas = array_slice(array_filter($n['productos'], static fn (array $p): bool => !empty($p['antes'])), 0, 4, true);
$atajos = ['hielo', 'marraqueta', 'huevos', 'cola', 'papel', 'paltas'];

tn_abrir('inicio', 'Minimarket 24 horas en ' . $n['barrio'], 'Demo de página web de varias páginas para un minimarket abierto las 24 horas en ' . $n['barrio'] . ': pasillos con buscador, ofertas, carrito y pedido por WhatsApp con despacho por comuna.');
?>
<main id="contenido">
  <section class="tn-hero" aria-labelledby="tn-hero-titulo">
    <div class="tn-ancho tn-hero__grid">
      <div class="tn-hero__texto">
        <p class="tn-chip"><span class="tn-punto" aria-hidden="true"></span>Abierto ahora · 24 horas, 7 días</p>
        <h1 id="tn-hero-titulo">Lo que te falta, <span>a cualquier hora.</span></h1>
        <p class="tn-hero__bajada">Minimarket en <?= demo_e($n['barrio']) ?> que nunca cierra. Busca, arma tu pedido y te lo llevamos en unos <?= (int) $n['despacho_minutos'] ?> minutos, o pásalo a buscar camino a la casa.</p>
        <form class="tn-buscar tn-buscar--grande" role="search" method="get" action="<?= demo_e(TN_BASE . 'catalogo.php') ?>">
          <label class="demo-sr" for="tn-buscar-portada">Buscar en los pasillos</label>
          <input type="search" id="tn-buscar-portada" name="q" maxlength="40" placeholder="Arroz, pan, hielo…" autocomplete="off">
          <button type="submit">Buscar</button>
        </form>
        <p class="tn-atajos">
          <span>Lo más buscado:</span>
          <?php foreach ($atajos as $id): ?>
          <a href="<?= demo_e(TN_BASE . 'catalogo.php?q=' . rawurlencode(mb_strtolower(explode(' ', $n['productos'][$id]['nombre'])[0]))) ?>"><?= demo_e(mb_strtolower($n['productos'][$id]['nombre'])) ?></a>
          <?php endforeach; ?>
        </p>
      </div>
      <figure class="tn-hero__foto">
        <?= demo_img($n['fotos']['hero'], 'Fachada de un minimarket iluminado de noche, con cajones de fruta y verdura en la vereda', ['lazy' => false, 'ancho' => 1000, 'alto' => 760, 'sizes' => '(max-width: 900px) 100vw, 50vw', 'anchos' => [600, 1000, 1400]]) ?>
        <figcaption class="tn-reloj">
          <span class="tn-reloj__hora"><?= demo_e($ahora->format('H:i')) ?></span>
          <span class="tn-reloj__texto">y estamos abiertos.<br><?= tn_hay_despacho($ahora) ? 'Despacho en ~' . (int) $n['despacho_minutos'] . ' min.' : 'Retiro en el local.' ?></span>
        </figcaption>
      </figure>
    </div>
  </section>

  <section class="tn-seccion" aria-labelledby="pasillos-titulo">
    <div class="tn-ancho">
      <div class="tn-titular">
        <p class="tn-sobre">Ocho pasillos, todo a la mano</p>
        <h2 id="pasillos-titulo">Recorre los pasillos</h2>
      </div>
      <ul class="tn-pasillos">
        <?php foreach ($n['pasillos'] as $slug => [$num, $nombre, $desc]): ?>
        <li>
          <a class="tn-pasillo" href="<?= demo_e(TN_BASE . 'catalogo.php?cat=' . $slug) ?>">
            <span class="tn-pasillo__letrero"><span>Pasillo</span> <strong><?= demo_e($num) ?></strong></span>
            <span class="tn-pasillo__icono"><?= tn_icono($slug, 30) ?></span>
            <span class="tn-pasillo__nombre"><?= demo_e($nombre) ?></span>
            <span class="tn-pasillo__desc"><?= demo_e($desc) ?></span>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="tn-seccion tn-seccion--gris" aria-labelledby="ofertas-titulo">
    <div class="tn-ancho">
      <div class="tn-titular tn-titular--fila">
        <div>
          <p class="tn-sobre">Hasta el domingo</p>
          <h2 id="ofertas-titulo">Ofertas de la semana</h2>
        </div>
        <a class="tn-enlace" href="<?= demo_e(TN_BASE . 'catalogo.php') ?>">Ver todos los pasillos</a>
      </div>
      <div class="tn-grilla">
        <?php foreach ($ofertas as $id => $p): ?>
        <?= tn_producto($id, $p) ?>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="tn-despacho" aria-labelledby="despacho-titulo">
    <div class="tn-ancho tn-despacho__grid">
      <div>
        <p class="tn-sobre tn-sobre--lima">Despacho de <?= demo_e($n['despacho_horas'][0]) ?> a <?= demo_e($n['despacho_horas'][1]) ?></p>
        <h2 id="despacho-titulo"><span class="tn-despacho__num"><?= (int) $n['despacho_minutos'] ?></span> minutos y está en tu puerta</h2>
        <p>Arma el pedido en la web y llega escrito a nuestro WhatsApp. Te confirmamos, lo preparamos y sale con un repartidor del local. Pedido mínimo para despacho: <?= demo_e(demo_clp($n['minimo_despacho'])) ?>.</p>
        <a class="tn-btn tn-btn--lima" href="<?= demo_e(TN_BASE . 'catalogo.php') ?>">Empezar mi pedido</a>
      </div>
      <dl class="tn-comunas">
        <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
        <div><dt><?= demo_e($comuna) ?></dt><dd><?= demo_e(demo_clp($costo)) ?></dd></div>
        <?php endforeach; ?>
      </dl>
    </div>
  </section>

  <section class="tn-seccion" aria-labelledby="noche-titulo">
    <div class="tn-ancho">
      <div class="tn-titular">
        <p class="tn-sobre">Después de las 22:00</p>
        <h2 id="noche-titulo">Lo que más se lleva de noche</h2>
      </div>
      <div class="tn-grilla">
        <?php foreach ($n['de_noche'] as $id): ?>
        <?= tn_producto($id, $n['productos'][$id]) ?>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <div class="tn-seccion tn-google">
    <div class="tn-ancho">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ]); ?>
    </div>
  </div>
</main>
<?php tn_cerrar();
