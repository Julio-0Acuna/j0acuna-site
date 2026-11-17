<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = ml();
$firmas = array_filter($n['productos'], static fn (array $p): bool => $p['firma']);
$estado = demo_horario_estado($n['horario']);

ml_abrir('inicio', 'Panadería de masa madre en ' . $n['comuna'], 'Demo de sitio de varias páginas para una panadería artesanal de masa madre en ' . $n['comuna'] . ', Santiago: panes en 3D, lista con QR y encargos por WhatsApp.');
?>
<main id="contenido">
  <section class="ml-portada" aria-labelledby="ml-portada-titulo">
    <div class="ml-portada__texto">
      <p class="ml-etiqueta"><span class="ml-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
      <h1 id="ml-portada-titulo">Pan que tarda <em>dos días</em> en estar listo</h1>
      <p class="ml-portada__bajada">Harina, agua, sal y masa madre. Nada más. Fermentamos cada hogaza 36 horas y la horneamos en piedra al amanecer, en <?= demo_e($n['comuna']) ?>.</p>
      <div class="ml-acciones">
        <a class="ml-btn" href="<?= demo_e(ML_BASE . 'panes.php') ?>"><span>Ver los panes</span><?= ml_flecha() ?></a>
        <a class="ml-btn ml-btn--linea" href="<?= demo_e(ML_BASE . 'proceso.php') ?>"><span>Cómo lo hacemos</span></a>
      </div>
    </div>
    <figure class="ml-portada__foto">
      <?= demo_img($n['fotos']['hero'], 'Manos abriendo una hogaza de masa madre recién horneada, con la miga alveolada a la vista', ['lazy' => false, 'ancho' => 1000, 'alto' => 1200, 'anchos' => [600, 1000, 1400], 'sizes' => '(max-width: 900px) 100vw, 50vw']) ?>
      <figcaption><span>Fig. 01</span> Hogaza de campo, 36 horas de fermentación</figcaption>
    </figure>
  </section>

  <section class="ml-ficha" aria-label="La hogaza en cifras">
    <dl class="ml-ancho ml-ficha__fila">
      <div><dt>Fermentación</dt><dd>36 h</dd></div>
      <div><dt>Hidratación</dt><dd>78 %</dd></div>
      <div><dt>Ingredientes</dt><dd>4</dd></div>
      <div><dt>Horno de piedra</dt><dd>250 °C</dd></div>
    </dl>
  </section>

  <section class="ml-seccion" aria-labelledby="hoy-titulo">
    <div class="ml-ancho">
      <header class="ml-encabezado">
        <p class="ml-etiqueta">N.º 01 — Del horno de hoy</p>
        <h2 id="hoy-titulo">Cuatro hogazas, <em>todos los días</em></h2>
        <a class="ml-enlace" href="<?= demo_e(ML_BASE . 'panes.php') ?>">Todos los panes <?= ml_flecha() ?></a>
      </header>
      <ol class="ml-hogazas">
        <?php $i = 0; foreach ($firmas as $id => $p): $i++; ?>
        <li class="ml-hogaza">
          <a href="<?= demo_e(ML_BASE . 'panes.php#producto-' . $id) ?>">
            <figure>
              <?= demo_img($p['foto'], $p['nombre'] . ' de masa madre', ['ancho' => 600, 'alto' => 750, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 80vw, (max-width: 1100px) 45vw, 25vw']) ?>
            </figure>
            <span class="ml-hogaza__num"><?= sprintf('%02d', $i) ?></span>
            <h3><?= demo_e($p['nombre']) ?></h3>
            <p><?= demo_e($p['desc']) ?></p>
            <p class="ml-hogaza__pie"><span><?= demo_e($p['ficha']) ?></span><strong>Desde <?= demo_e(demo_clp(ml_desde($p))) ?></strong></p>
          </a>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section class="ml-seccion ml-seccion--avena" aria-labelledby="proceso-titulo">
    <div class="ml-ancho ml-dos">
      <figure class="ml-foto-alta">
        <?= demo_img($n['fotos']['pliegue'], 'Manos amasando una masa de pan sobre un mesón enharinado', ['ancho' => 800, 'alto' => 1000, 'sizes' => '(max-width: 900px) 92vw, 45vw']) ?>
        <figcaption><span>Fig. 02</span> Pliegues a mano, cada 45 minutos</figcaption>
      </figure>
      <div>
        <p class="ml-etiqueta">N.º 02 — El proceso</p>
        <h2 id="proceso-titulo">Lento a propósito</h2>
        <p class="ml-lead">La levadura de sobre hace pan en tres horas. La masa madre necesita dos días, y esa espera es la que se nota en el sabor, la corteza y lo que dura.</p>
        <ol class="ml-mini-linea">
          <?php foreach ([$n['proceso'][0], $n['proceso'][3], $n['proceso'][4]] as [$hora, $titulo, $texto]): ?>
          <li><span class="ml-mini-linea__hora"><?= demo_e($hora) ?></span><strong><?= demo_e($titulo) ?></strong></li>
          <?php endforeach; ?>
        </ol>
        <a class="ml-btn ml-btn--linea" href="<?= demo_e(ML_BASE . 'proceso.php') ?>"><span>Las 36 horas, paso a paso</span><?= ml_flecha() ?></a>
      </div>
    </div>
  </section>

  <section class="ml-seccion" aria-labelledby="encargo-titulo">
    <div class="ml-ancho ml-encargo">
      <div class="ml-encargo__texto">
        <p class="ml-etiqueta">N.º 03 — Encargos</p>
        <h2 id="encargo-titulo">Encarga hoy, <em>retira mañana</em></h2>
        <p>Las hogazas se agotan antes del mediodía. Si encargas antes de las 18:00, la tuya sale del horno a tu nombre al día siguiente.</p>
      </div>
      <ol class="ml-pasos">
        <li><span>1</span><strong>Arma tu pedido</strong><small>Elige panes enteros o medios en la página de panes.</small></li>
        <li><span>2</span><strong>Elige retiro o despacho</strong><small>Retiro desde las 8:00 o despacho en <?= count($n['comunas_despacho']) ?> comunas.</small></li>
        <li><span>3</span><strong>Envíalo por WhatsApp</strong><small>Te confirmamos al tiro y te avisamos cuando esté listo.</small></li>
      </ol>
      <div class="ml-acciones">
        <a class="ml-btn" href="<?= demo_e(ML_BASE . 'panes.php') ?>"><span>Armar mi encargo</span><?= ml_flecha() ?></a>
        <a class="ml-btn ml-btn--linea" href="<?= demo_e(demo_tel()) ?>"><span>Llamar</span></a>
      </div>
    </div>
  </section>

  <section class="ml-banda" aria-label="Foto del obrador">
    <figure>
      <?= demo_img($n['fotos']['estante'], 'Hogazas de masa madre enfriándose en una rejilla de madera', ['ancho' => 1600, 'alto' => 700, 'anchos' => [800, 1200, 1600], 'sizes' => '100vw']) ?>
      <figcaption class="ml-ancho"><span>Fig. 03</span> 7:45 de la mañana: las hogazas se enfrían antes de abrir</figcaption>
    </figure>
  </section>

  <div class="ml-seccion ml-seccion--google">
    <div class="ml-ancho">
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
<?php ml_cerrar();
