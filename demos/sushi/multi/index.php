<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = ku();
$firmas = array_filter($n['platos'], static fn (array $p): bool => $p['firma']);
$estado = demo_horario_estado($n['horario']);

ku_abrir('inicio', 'Omakase en ' . $n['comuna'], 'Demo de sitio de varias páginas para una barra de sushi omakase en ' . $n['comuna'] . ', Santiago: carta con QR y nigiris en 3D, reservas y pedidos para llevar por WhatsApp.');
?>
<main id="contenido">
  <section class="ku-portada" aria-labelledby="ku-portada-titulo">
    <div class="ku-portada__foto">
      <?= demo_img($n['fotos']['hero'], 'Itamae sirviendo pequeños platos con palillos sobre una barra de madera', ['lazy' => false, 'ancho' => 1200, 'alto' => 1400, 'anchos' => [600, 900, 1400], 'sizes' => '(max-width: 900px) 100vw, 55vw']) ?>
    </div>
    <div class="ku-ancho ku-portada__fila">
      <div class="ku-portada__texto">
        <p class="ku-sobre">おまかせ · Barra de omakase en <?= demo_e($n['comuna']) ?></p>
        <h1 id="ku-portada-titulo">Ocho asientos.<br>Un itamae.<br><em>Lo que trajo el mar hoy.</em></h1>
        <p class="ku-portada__bajada">En Kumo no hay carta en la barra: te sientas frente al itamae y él decide, pieza a pieza, con el pescado del día y arroz tibio, como en las barras pequeñas de Tokio.</p>
        <div class="ku-acciones">
          <a class="ku-btn" href="<?= demo_e(ku_wa_reserva()) ?>" target="_blank" rel="noopener"><span>Reservar en la barra</span></a>
          <a class="ku-enlace" href="<?= demo_e(KU_BASE . 'menu.php') ?>">Pedir para llevar</a>
        </div>
        <p class="ku-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><?= demo_e($estado['texto']) ?></p>
      </div>
      <p class="ku-vertical ku-portada__vertical" aria-hidden="true">雲 · 鮨 · おまかせ</p>
    </div>
  </section>

  <section class="ku-seccion ku-ritual" aria-labelledby="ritual-titulo">
    <div class="ku-ancho">
      <header class="ku-encabezado">
        <p class="ku-sobre">El recorrido</p>
        <h2 id="ritual-titulo">Cuatro momentos, <em>catorce tiempos</em></h2>
      </header>
      <ol class="ku-ritual__lista">
        <?php foreach ($n['ritual'] as [$num, $nombre, $texto]): ?>
        <li>
          <span class="ku-ritual__num" aria-hidden="true"><?= demo_e($num) ?></span>
          <h3><?= demo_e($nombre) ?></h3>
          <p><?= demo_e($texto) ?></p>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section class="ku-seccion" aria-labelledby="omakase-titulo">
    <div class="ku-ancho">
      <header class="ku-encabezado ku-encabezado--fila">
        <div>
          <p class="ku-sobre">En la barra · solo con reserva</p>
          <h2 id="omakase-titulo">Dos formas de <em>sentarse</em></h2>
        </div>
        <p>Martes a domingo. Reservas por WhatsApp; te confirmamos el turno y te preguntamos por alergias.</p>
      </header>
      <div class="ku-experiencias">
        <?php foreach ($n['omakase'] as $i => $o): ?>
        <article class="ku-experiencia">
          <figure class="ku-foto">
            <?= demo_img($o['foto'], $o['alt'], ['ancho' => 800, 'alto' => 560, 'anchos' => [480, 800, 1200], 'sizes' => '(max-width: 900px) 92vw, 45vw']) ?>
            <figcaption><?= demo_e($o['tiempos']) ?> · <?= demo_e($o['duracion']) ?></figcaption>
          </figure>
          <div class="ku-experiencia__texto">
            <h3><?= demo_e($o['nombre']) ?></h3>
            <p><?= demo_e($o['detalle']) ?></p>
            <p class="ku-experiencia__precio"><?= demo_e(demo_clp($o['precio'])) ?> <small>por persona</small></p>
            <a class="ku-btn ku-btn--linea" href="<?= demo_e(ku_wa_reserva($o['nombre'])) ?>" target="_blank" rel="noopener"><span>Reservar <?= demo_e($o['nombre']) ?></span></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="ku-seccion ku-claro" aria-labelledby="llevar-titulo">
    <div class="ku-ancho">
      <header class="ku-encabezado ku-encabezado--fila">
        <div>
          <p class="ku-sobre">Para llevar</p>
          <h2 id="llevar-titulo">La barra, <em>en tu mesa</em></h2>
        </div>
        <p>Armamos cada caja al momento. Elige retiro en Vitacura o despacho en comunas cercanas y envía el pedido por WhatsApp.</p>
      </header>
      <div class="ku-firmas">
        <?php foreach ($firmas as $id => $p): ?>
        <article class="ku-firma">
          <a class="ku-firma__foto" href="<?= demo_e(KU_BASE . 'menu.php#plato-' . $id) ?>" tabindex="-1" aria-hidden="true">
            <?= demo_img($p['foto'], '', ['ancho' => 600, 'alto' => 760, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 640px) 80vw, (max-width: 1000px) 45vw, 25vw']) ?>
          </a>
          <p class="ku-firma__jp"><?= demo_e($p['jp']) ?></p>
          <h3><a href="<?= demo_e(KU_BASE . 'menu.php#plato-' . $id) ?>"><?= demo_e($p['nombre']) ?></a></h3>
          <p class="ku-firma__precio">Desde <?= demo_e(demo_clp(ku_desde($p))) ?></p>
        </article>
        <?php endforeach; ?>
      </div>
      <p class="ku-centro"><a class="ku-btn ku-btn--tinta" href="<?= demo_e(KU_BASE . 'menu.php') ?>"><span>Ver la carta completa</span></a></p>
    </div>
  </section>

  <section class="ku-seccion" aria-labelledby="itamae-titulo">
    <div class="ku-ancho ku-dos">
      <figure class="ku-foto ku-foto--alta">
        <?= demo_img($n['fotos']['pinzas'], 'Itamae pintando con un pincel de nikiri un nigiri de pescado blanco', ['ancho' => 800, 'alto' => 1000, 'sizes' => '(max-width: 900px) 92vw, 45vw']) ?>
        <?= ku_sello('ku-sello--foto') ?>
      </figure>
      <div class="ku-prosa">
        <p class="ku-sobre">El itamae</p>
        <h2 id="itamae-titulo">Veinte años <em>detrás de una barra</em></h2>
        <p>Rodrigo aprendió a cortar pescado en una barra de ocho asientos, como esta. Cada mañana elige lo que llega a la caleta y decide el recorrido de la noche: si el lenguado está bueno, hay lenguado; si no, no.</p>
        <p>El arroz se cuece tres veces al día y se sirve a temperatura de cuerpo, para que cada nigiri se deshaga en la boca.</p>
        <a class="ku-enlace" href="<?= demo_e(KU_BASE . 'nosotros.php') ?>">Conoce la barra</a>
      </div>
    </div>
  </section>

  <div class="ku-seccion ku-claro">
    <div class="ku-ancho">
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
<?php ku_cerrar();
