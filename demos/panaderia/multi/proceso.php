<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = ml();
ml_abrir('proceso', 'Nuestro proceso', 'Cómo hace ' . $n['nombre'] . ' su pan de masa madre en ' . $n['comuna'] . ': 36 horas de fermentación, pliegues a mano y horno de piedra. Demo de sitio de varias páginas.');
?>
<main id="contenido">
  <header class="ml-titular">
    <div class="ml-ancho">
      <p class="ml-etiqueta">Cuaderno de oficio</p>
      <h1>Treinta y seis horas <em>por hogaza</em></h1>
      <p>Somos un obrador chico en <?= demo_e($n['comuna']) ?>. Hacemos todo a mano y anotamos cada paso, porque el pan cambia con el clima y la harina de cada semana.</p>
    </div>
  </header>

  <section class="ml-seccion ml-seccion--sin-arriba" aria-label="Las etapas del pan">
    <ol class="ml-ancho ml-linea">
      <?php foreach ($n['proceso'] as $i => [$hora, $titulo, $texto, $foto]): ?>
      <li class="ml-linea__paso">
        <figure>
          <?= demo_img($n['fotos'][$foto], $titulo . ': ' . $texto, ['lazy' => $i > 0, 'ancho' => 800, 'alto' => 600, 'anchos' => [480, 800, 1200], 'sizes' => '(max-width: 900px) 92vw, 46vw']) ?>
        </figure>
        <div class="ml-linea__texto">
          <p class="ml-linea__hora"><?= demo_e($hora) ?></p>
          <h2><span><?= sprintf('%02d', $i + 1) ?></span> <?= demo_e($titulo) ?></h2>
          <p><?= demo_e($texto) ?></p>
        </div>
      </li>
      <?php endforeach; ?>
    </ol>
  </section>

  <section class="ml-seccion ml-oscuro" aria-labelledby="equipo-titulo">
    <div class="ml-ancho ml-dos">
      <div>
        <p class="ml-etiqueta">Quiénes somos</p>
        <h2 id="equipo-titulo">Tres panaderos, <em>un horno de piedra</em></h2>
        <p class="ml-lead">Empezamos horneando para los vecinos del pasaje. Hoy somos tres, abrimos de martes a domingo y seguimos con la misma masa madre del primer día.</p>
        <ul class="ml-lista">
          <li>Harinas de molinos chilenos, sin mejoradores</li>
          <li>Sal de mar y agua filtrada</li>
          <li>Ninguna levadura de sobre, ni en la bollería</li>
          <li>Lo que sobra del día se dona a un comedor del barrio</li>
        </ul>
        <a class="ml-btn ml-btn--claro" href="<?= demo_e(ML_BASE . 'panes.php') ?>"><span>Ver los panes</span><?= ml_flecha() ?></a>
      </div>
      <figure class="ml-foto-alta ml-foto-alta--oscura">
        <?= demo_img($n['fotos']['oscura'], 'Hogaza recién horneada espolvoreada con harina sobre fondo oscuro', ['ancho' => 800, 'alto' => 1000, 'sizes' => '(max-width: 900px) 92vw, 45vw']) ?>
        <figcaption><span>Fig. 06</span> La última hogaza del día, lista para el mesón</figcaption>
      </figure>
    </div>
  </section>
</main>
<?php ml_cerrar();
