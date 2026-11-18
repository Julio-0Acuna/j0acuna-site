<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = mz();
$firma = array_filter($n['servicios'], static fn (array $s): bool => $s['firma']);
$estado = demo_horario_estado($n['horario']);
$resena = $n['resenas'][0];

mz_abrir('inicio', 'Estudio de color y balayage en ' . $n['comuna'], 'Demo de sitio web de varias páginas para un salón de autor de color y balayage en ' . $n['comuna'] . ', Santiago: carta de tonos, servicios con duración y precio desde según el largo, y reserva con colorista, día y hora por WhatsApp.');
?>
<main id="contenido">
  <section class="mz-portada" aria-labelledby="portada-titulo">
    <div class="mz-ancho mz-portada__grid">
      <div class="mz-portada__texto">
        <p class="mz-edicion"><span>Edición 09</span><span>Primavera · <?= demo_e($n['comuna']) ?></span></p>
        <h1 id="portada-titulo">Tu color, <em>a mano alzada.</em></h1>
        <p class="mz-portada__bajada">Estudio de color y balayage en <?= demo_e($n['comuna']) ?>. Diagnóstico, prueba de mechón y un tono pensado para tu piel, tu pelo y cómo vives.</p>
        <div class="mz-acciones">
          <a class="mz-btn" href="<?= demo_e(MZ_BASE . 'servicios.php') ?>">Reservar tu hora</a>
          <a class="mz-btn mz-btn--linea" href="#tonos">Ver la carta de tonos</a>
        </div>
        <p class="mz-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
      </div>
      <div class="mz-portada__fotos">
        <figure class="mz-foto mz-foto--grande">
          <?= demo_img($n['fotos']['hero'], 'Mujer de espalda con el pelo largo rubio y ondulado frente a una pared gris', ['lazy' => false, 'ancho' => 800, 'alto' => 1100, 'sizes' => '(max-width: 860px) 70vw, 34vw', 'anchos' => [480, 800, 1200]]) ?>
          <figcaption>Rubio miel, balayage de Antonia</figcaption>
        </figure>
        <figure class="mz-foto mz-foto--chica mz-foto--a" aria-hidden="true"><?= demo_img($n['fotos']['hero_b'], '', ['lazy' => false, 'ancho' => 400, 'alto' => 520, 'anchos' => [300, 400, 600], 'sizes' => '(max-width: 860px) 30vw, 14vw']) ?></figure>
        <figure class="mz-foto mz-foto--chica mz-foto--b" aria-hidden="true"><?= demo_img($n['fotos']['hero_c'], '', ['lazy' => false, 'ancho' => 400, 'alto' => 520, 'anchos' => [300, 400, 600], 'sizes' => '(max-width: 860px) 30vw, 14vw']) ?></figure>
      </div>
    </div>
  </section>

  <section class="mz-seccion mz-tonos" id="tonos" aria-labelledby="tonos-titulo">
    <div class="mz-ancho">
      <header class="mz-encabezado mz-encabezado--fila">
        <div>
          <p class="mz-sobre">Carta de tonos</p>
          <h2 id="tonos-titulo">Elige un <em>punto de partida</em></h2>
        </div>
        <p>Así trabajamos: con mechones de muestra sobre tu pelo y luz natural. Toca un tono y te mostramos con qué servicio llegas a él.</p>
      </header>
      <ul class="mz-carta">
        <?php foreach ($n['tonos'] as $i => [$nombre, $claro, $oscuro, $servicio]): $s = $n['servicios'][$servicio]; ?>
        <li>
          <a class="mz-mechon" href="<?= demo_e(MZ_BASE . 'servicios.php#s-' . $servicio) ?>" style="--claro: <?= demo_e($claro) ?>; --oscuro: <?= demo_e($oscuro) ?>">
            <span class="mz-mechon__muestra" aria-hidden="true"></span>
            <span class="mz-mechon__num" aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></span>
            <span class="mz-mechon__nombre"><?= demo_e($nombre) ?></span>
            <span class="mz-mechon__servicio"><?= demo_e($s['nombre']) ?></span>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="mz-seccion mz-seccion--papel" aria-labelledby="firma-titulo">
    <div class="mz-ancho">
      <header class="mz-encabezado mz-encabezado--fila">
        <div>
          <p class="mz-sobre">Servicios firma</p>
          <h2 id="firma-titulo">Lo que <em>nos piden</em> por nombre</h2>
        </div>
        <a class="mz-enlace" href="<?= demo_e(MZ_BASE . 'servicios.php') ?>">Ver todos los servicios</a>
      </header>
      <div class="mz-firma">
        <?php foreach ($firma as $id => $s): ?>
        <article class="mz-tarjeta">
          <div class="mz-tarjeta__foto"><?= demo_img($s['foto'], $s['alt'], ['ancho' => 600, 'alto' => 800, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 760px) 90vw, 30vw']) ?></div>
          <p class="mz-meta"><span><?= demo_e($n['categorias'][$s['cat']][0]) ?></span><span><?= demo_e(mz_duracion($s['min'])) ?></span></p>
          <h3><?= demo_e($s['nombre']) ?></h3>
          <p><?= demo_e($s['desc']) ?></p>
          <?= mz_agregar($id, $s) ?>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="mz-seccion mz-metodo" aria-labelledby="metodo-titulo">
    <div class="mz-ancho mz-metodo__grid">
      <figure class="mz-metodo__foto">
        <?= demo_img($n['fotos']['mechas'], 'Colorista separa mechones con una paleta y papel de aluminio', ['ancho' => 700, 'alto' => 900, 'sizes' => '(max-width: 860px) 90vw, 40vw']) ?>
      </figure>
      <div>
        <p class="mz-sobre">El método Matiz</p>
        <h2 id="metodo-titulo">Antes del color, <em>una conversación</em></h2>
        <ol class="mz-pasos">
          <li><h3>Diagnóstico</h3><p>Miramos tu pelo con luz natural, tu historia de tintes y fotos de referencia. Te decimos qué es posible hoy y qué en dos sesiones.</p></li>
          <li><h3>Prueba de mechón</h3><p>Para decoloraciones y primeras veces, 48 horas antes y sin costo. Así no hay sorpresas en la piel ni en el tono.</p></li>
          <li><h3>Color y cuidado en casa</h3><p>Trabajamos con protector de enlaces y te vas con una rutina simple para que el color dure entre visitas.</p></li>
        </ol>
      </div>
    </div>
  </section>

  <section class="mz-seccion mz-seccion--tinta" aria-labelledby="trabajos-titulo">
    <div class="mz-ancho">
      <header class="mz-encabezado mz-encabezado--fila">
        <div>
          <p class="mz-sobre">Trabajos recientes</p>
          <h2 id="trabajos-titulo">De la silla <em>a la calle</em></h2>
        </div>
        <a class="mz-enlace mz-enlace--claro" href="<?= demo_e(MZ_BASE . 'estudio.php') ?>">Conoce a las coloristas</a>
      </header>
      <ul class="mz-trabajos">
        <?php foreach ($n['trabajos'] as [$foto, $alt, $pie]): ?>
        <li>
          <figure><?= demo_img($foto, $alt, ['ancho' => 500, 'alto' => 700, 'anchos' => [320, 500, 800], 'sizes' => '(max-width: 760px) 60vw, 20vw']) ?><figcaption><?= demo_e($pie) ?></figcaption></figure>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="mz-seccion mz-cita" aria-label="Reseña de ejemplo">
    <div class="mz-ancho">
      <blockquote>
        <p>«<?= demo_e($resena[2]) ?>»</p>
        <footer><?= demo_e($resena[0]) ?> · <span class="mz-cita__sello">Reseña de ejemplo</span></footer>
      </blockquote>
    </div>
  </section>

  <section class="mz-seccion mz-seccion--papel" aria-label="Encuéntranos en Google">
    <div class="mz-ancho">
      <?php demo_google([
          'nombre' => $n['nombre_largo'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => array_slice($n['resenas'], 1),
      ], ['clase' => 'mz-google', 'bajada' => 'Busca «Matiz estudio de color» en Google Maps: horario, cómo llegar y opiniones de quienes ya cambiaron de tono.']); ?>
    </div>
  </section>

  <section class="mz-seccion mz-cierre" aria-labelledby="cierre-titulo">
    <div class="mz-ancho mz-cierre__caja">
      <h2 id="cierre-titulo">¿Lista para <em>cambiar de tono?</em></h2>
      <p>Arma tu reserva con los servicios que quieras, elige colorista, día y hora, y te confirmamos por WhatsApp.</p>
      <div class="mz-acciones">
        <a class="mz-btn" href="<?= demo_e(MZ_BASE . 'servicios.php') ?>">Elegir servicios</a>
        <a class="mz-btn mz-btn--linea" href="<?= demo_e(mz_wa('Hola, quiero una evaluación de color en ' . $n['nombre_largo'] . '. Les envío una foto de mi pelo con luz natural.')) ?>" target="_blank" rel="noopener">Enviar una foto de mi pelo</a>
      </div>
    </div>
  </section>
</main>
<?php mz_cerrar();
