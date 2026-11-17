<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = vt();
vt_abrir('camara', 'La cámara de maduración', 'Cómo madura la carne ' . $n['nombre'] . ' en ' . $n['comuna'] . ': de 21 a 60 días en seco, a 2 °C, cortada a pedido. Demo de sitio de varias páginas para carnicería.');
?>
<main id="contenido">
  <header class="vt-titular">
    <div class="vt-ancho">
      <p class="vt-mono">Nosotros · La cámara</p>
      <h1>El oficio de <em>esperar</em></h1>
      <p>Veta nació en 2019 con una idea simple: traer a <?= demo_e($n['comuna']) ?> la carne madurada que antes solo se encontraba en los asadores.</p>
    </div>
  </header>

  <section class="vt-seccion vt-seccion--sin-arriba">
    <div class="vt-ancho vt-dos">
      <figure class="vt-marco">
        <?= demo_img($n['fotos']['maestro'], 'Carnicero con delantal cortando un tomahawk sobre un tronco de madera', ['lazy' => false, 'ancho' => 800, 'alto' => 1000, 'sizes' => '(max-width: 900px) 92vw, 45vw']) ?>
      </figure>
      <div class="vt-prosa">
        <p class="vt-mono">01 · Las personas</p>
        <h2>Dos carniceros <em>y una cámara</em></h2>
        <p>Joaquín aprendió el oficio en la carnicería de su padre, en el sur. Valentina venía de la cocina de un restaurante y quería que la carne madurada dejara de ser un lujo de carta.</p>
        <p>Juntos armaron una cámara de vidrio a la vista de todos, con piezas enteras colgadas y una etiqueta que dice cuándo entró cada una. Ahí la carne espera su punto, sin apuro.</p>
        <ul class="vt-lista">
          <li>Vacuno de pradera con buena infiltración de grasa</li>
          <li>Cámara a 2 °C, 80 a 85 % de humedad y aire en movimiento</li>
          <li>Cortes a pedido, envasados al vacío el mismo día</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="vt-seccion vt-claro" aria-labelledby="linea-titulo">
    <div class="vt-ancho">
      <header class="vt-encabezado">
        <p class="vt-mono">02 · Qué cambia con los días</p>
        <h2 id="linea-titulo">La línea de maduración</h2>
        <p class="vt-encabezado__bajada">Mientras más días, menos agua y más sabor. Elige según tu gusto: no hay un punto mejor que otro.</p>
      </header>
      <ol class="vt-linea">
        <?php foreach ($n['maduracion'] as $dias => [$titulo, $texto]): ?>
        <li style="--dias: <?= (int) $dias ?>">
          <p class="vt-linea__dias"><strong><?= (int) $dias ?></strong> días</p>
          <h3><?= demo_e($titulo) ?></h3>
          <p><?= demo_e($texto) ?></p>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section class="vt-seccion">
    <div class="vt-ancho vt-dos vt-dos--invertido">
      <figure class="vt-marco">
        <?= demo_img($n['fotos']['camara'], 'Lomo vetado con hueso recién salido de la cámara de maduración', ['ancho' => 800, 'alto' => 1000, 'sizes' => '(max-width: 900px) 92vw, 45vw']) ?>
      </figure>
      <div class="vt-prosa">
        <p class="vt-mono">03 · Visítanos</p>
        <h2>Ven a elegir <em>tu pieza</em></h2>
        <p>En la tienda puedes ver la cámara, elegir la pieza y pedir el grosor exacto. Si es tu primera vez, te recomendamos empezar por un lomo vetado de 28 días.</p>
        <p>Los viernes a las 19:00 hacemos catas de maduración para grupos de hasta 10 personas, con reserva previa.</p>
        <div class="vt-acciones">
          <a class="vt-btn" href="<?= demo_e(vt_wa('Hola, quiero reservar un cupo para la cata de maduración de ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Reservar una cata</a>
          <a class="vt-btn vt-btn--linea" href="<?= demo_e(VT_BASE . 'cortes.php') ?>">Ver la carta</a>
        </div>
      </div>
    </div>
  </section>

  <div class="vt-franja" aria-hidden="true">
    <?= demo_img($n['fotos']['fila'], '', ['ancho' => 1400, 'alto' => 500, 'anchos' => [700, 1000, 1400, 2000], 'sizes' => '100vw']) ?>
  </div>
</main>
<?php vt_cerrar();
