<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = rp();
$firmas = array_filter($n['platos'], static fn (array $p): bool => $p['firma']);
$banquetes = array_filter($n['platos'], static fn (array $p): bool => $p['cat'] === 'banquetes');

rp_abrir('inicio', 'Cocina cantonesa y dim sum en ' . $n['comuna'], 'Demo de sitio de varias páginas para un restaurante cantonés con dim sum en ' . $n['comuna'] . ', Santiago: carta con QR y platos en 3D, reservas y pedidos para llevar por WhatsApp.');
?>
<main id="contenido">
  <section class="rp-portada" aria-labelledby="rp-portada-titulo">
    <div class="rp-portada__foto">
      <?= demo_img($n['fotos']['hero'], 'Canasto de bambú con dim sum recién salido del vapor, junto a un pocillo de salsa', ['lazy' => false, 'ancho' => 1400, 'alto' => 930, 'anchos' => [700, 1000, 1400, 1900], 'sizes' => '(max-width: 900px) 100vw, 62vw']) ?>
    </div>
    <div class="rp-ancho rp-portada__grid">
      <div class="rp-portada__texto">
        <?= rp_hanzi('點心', 'rp-hanzi--vertical') ?>
        <p class="rp-sobre">Casa cantonesa · <?= demo_e($n['comuna']) ?></p>
        <h1 id="rp-portada-titulo">El dim sum se hace <em>a mano</em>, cada mañana</h1>
        <p class="rp-portada__bajada">Har gow de masa translúcida, pato laqueado durante 24 horas y té servido en tetera. La cocina de Cantón, sin apuro, en <?= demo_e($n['comuna']) ?>.</p>
        <div class="rp-acciones">
          <a class="rp-btn" href="<?= demo_e(RP_BASE . 'reservas.php') ?>">Reservar mesa</a>
          <a class="rp-btn rp-btn--linea" href="<?= demo_e(RP_BASE . 'carta.php') ?>">Ver la carta</a>
        </div>
      </div>
    </div>
  </section>

  <section class="rp-franja" aria-label="Nuestra cocina">
    <div class="rp-ancho rp-franja__fila">
      <p><strong>6:30</strong><span>empezamos a estirar la masa</span></p>
      <p><strong>24 h</strong><span>de marinado para el pato</span></p>
      <p><strong>4</strong><span>tés de hoja entera</span></p>
    </div>
  </section>

  <section class="rp-seccion rp-porcelana" aria-labelledby="yumcha-titulo">
    <div class="rp-ancho rp-yumcha">
      <figure class="rp-octogono">
        <?= demo_img($n['fotos']['yumcha'], 'Bollos al vapor con sello rojo en un canasto de bambú, junto a otros platos de dim sum', ['ancho' => 800, 'alto' => 800, 'sizes' => '(max-width: 860px) 86vw, 44vw']) ?>
      </figure>
      <div>
        <?= rp_hanzi('飲茶', 'rp-hanzi--marca') ?>
        <p class="rp-sobre">Sábado y domingo · 11:30 a 16:00</p>
        <h2 id="yumcha-titulo">Yum cha: <em>té y dim sum</em> de fin de semana</h2>
        <p>En Cantón, el fin de semana se sale a «tomar té». Los canastos pasan en carro por el salón y cada uno elige lo que se le antoja: har gow, siu mai, bao de char siu y tartaletas de huevo recién horneadas.</p>
        <p>Es la mejor forma de probar mucho y compartir. Reserva, porque se llena.</p>
        <a class="rp-btn" href="<?= demo_e(RP_BASE . 'reservas.php') ?>">Reservar para el yum cha</a>
      </div>
    </div>
  </section>

  <section class="rp-seccion" aria-labelledby="firmas-titulo">
    <div class="rp-ancho">
      <header class="rp-encabezado">
        <p class="rp-sobre">De la casa</p>
        <h2 id="firmas-titulo">Los que <em>no pueden faltar</em></h2>
      </header>
      <div class="rp-firmas">
        <?php foreach ($firmas as $id => $p): ?>
        <article class="rp-firma">
          <figure class="rp-marco">
            <?= demo_img($p['foto'], $p['nombre'], ['ancho' => 600, 'alto' => 760, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 640px) 86vw, (max-width: 1000px) 44vw, 24vw']) ?>
          </figure>
          <h3><?= demo_e($p['nombre']) ?></h3>
          <p><?= demo_e($p['desc']) ?></p>
          <p class="rp-firma__precio">Desde <?= demo_e(demo_clp(rp_desde($p))) ?></p>
        </article>
        <?php endforeach; ?>
      </div>
      <p class="rp-centro"><a class="rp-btn rp-btn--linea" href="<?= demo_e(RP_BASE . 'carta.php') ?>">Ver la carta completa</a></p>
    </div>
  </section>

  <section class="rp-seccion rp-oscuro rp-celosia" aria-labelledby="banquetes-titulo">
    <div class="rp-ancho">
      <header class="rp-encabezado rp-encabezado--centro">
        <?= rp_hanzi('宴', 'rp-hanzi--marca') ?>
        <p class="rp-sobre">Mesa redonda giratoria</p>
        <h2 id="banquetes-titulo">Banquetes para <em>2, 4 y 6</em></h2>
        <p>Como se come en Cantón: todo al centro y la mesa gira para que cada uno se sirva. Menús cerrados, pensados para probar la cocina completa.</p>
      </header>
      <div class="rp-banquetes">
        <?php foreach ($banquetes as $id => $p): ?>
        <article class="rp-banquete">
          <div class="rp-banquete__foto"><?= demo_img($p['foto'], $p['nombre'] . ': varios platos cantoneses al centro de la mesa', ['ancho' => 700, 'alto' => 500, 'anchos' => [480, 700, 1000], 'sizes' => '(max-width: 860px) 86vw, 30vw']) ?></div>
          <h3><?= demo_e($p['nombre']) ?></h3>
          <p><?= demo_e($p['desc']) ?></p>
          <p class="rp-banquete__precio"><?= demo_e(demo_clp(rp_desde($p))) ?></p>
        </article>
        <?php endforeach; ?>
      </div>
      <p class="rp-centro"><a class="rp-btn rp-btn--claro" href="<?= demo_e(RP_BASE . 'reservas.php') ?>">Reservar un banquete</a></p>
    </div>
  </section>

  <section class="rp-seccion" aria-labelledby="cocina-titulo">
    <div class="rp-ancho rp-dos">
      <div class="rp-prosa">
        <p class="rp-sobre">La cocina</p>
        <h2 id="cocina-titulo">El pato se trincha <em>en la mesa</em></h2>
        <p>Lo marinamos un día entero con cinco especias, lo secamos al aire y lo asamos hasta que la piel cruje como papel. Llega entero y el cocinero lo corta frente a ti.</p>
        <p>El pato entero conviene encargarlo con un día de anticipación.</p>
        <a class="rp-enlace" href="<?= demo_e(RP_BASE . 'nosotros.php') ?>">Conoce nuestra historia</a>
      </div>
      <figure class="rp-marco rp-marco--ancho">
        <?= demo_img($n['fotos']['chef'], 'Cocinero con gorro blanco trinchando un pato laqueado sobre una bandeja', ['ancho' => 900, 'alto' => 1100, 'sizes' => '(max-width: 860px) 86vw, 44vw']) ?>
      </figure>
    </div>
  </section>

  <section class="rp-seccion rp-porcelana" aria-labelledby="llevar-titulo">
    <div class="rp-ancho rp-llevar">
      <div>
        <?= rp_hanzi('外賣', 'rp-hanzi--marca') ?>
        <p class="rp-sobre">Para llevar</p>
        <h2 id="llevar-titulo">El dim sum <em>también viaja</em></h2>
        <p>Arma tu pedido en la carta, elige retiro o despacho a Lo Barnechea, Vitacura o Las Condes, y envíalo por WhatsApp. Lo empacamos en canastos térmicos.</p>
      </div>
      <div class="rp-acciones">
        <a class="rp-btn" href="<?= demo_e(RP_BASE . 'carta.php') ?>">Armar mi pedido</a>
        <a class="rp-btn rp-btn--linea" href="<?= demo_e(demo_tel()) ?>">Llamar</a>
      </div>
    </div>
  </section>

  <div class="rp-seccion">
    <div class="rp-ancho">
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
<?php rp_cerrar();
