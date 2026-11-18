<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = co();
$seleccion = array_filter($n['productos'], static fn (array $p): bool => $p['destacado']);
$catas = co_catas();
$proxima = $catas[0];
$estado = demo_horario_estado($n['horario']);

co_abrir('inicio', 'Vinos y destilados de especialidad en ' . $n['comuna'], 'Demo de sitio de varias páginas para una tienda de vinos y destilados con catas en ' . $n['comuna'] . ', Santiago: catálogo con QR, reservas de catas y pedidos por WhatsApp.');
?>
<main id="contenido">
  <section class="co-portada" aria-labelledby="portada-titulo">
    <figure class="co-portada__foto">
      <?= demo_img($n['fotos']['hero'], 'Pasillo de una cava con botellas de vino guardadas a ambos lados y luz cálida', ['lazy' => false, 'ancho' => 1600, 'alto' => 1000, 'anchos' => [800, 1400, 2000], 'sizes' => '100vw']) ?>
    </figure>
    <div class="co-ancho co-portada__texto">
      <p class="co-sobre">Vinoteca y destilados · <?= demo_e($n['comuna']) ?></p>
      <h1 id="portada-titulo">Vinos que <em>se conversan</em></h1>
      <p class="co-portada__bajada">Elegimos cada botella después de probarla: valles grandes y secanos chicos, piscos de guarda y destilados de pequeños productores. Y cada jueves, una cata.</p>
      <div class="co-acciones">
        <a class="co-btn co-btn--ocre" href="<?= demo_e(CO_BASE . 'catalogo.php') ?>">Ver vinos y destilados</a>
        <a class="co-btn co-btn--linea" href="<?= demo_e(CO_BASE . 'catas.php') ?>">Reservar una cata</a>
      </div>
      <p class="co-portada__estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><?= demo_e($estado['texto']) ?></p>
    </div>
  </section>

  <section class="co-cifras" aria-label="La cava en números">
    <div class="co-ancho co-cifras__fila">
      <p><strong>180</strong><span>etiquetas en la cava</span></p>
      <p><strong>34</strong><span>productores, casi todos pequeños</span></p>
      <p><strong>12</strong><span>valles, de Elqui a Malleco</span></p>
      <p><strong>Jue</strong><span>cata semanal, 19:30</span></p>
    </div>
  </section>

  <section class="co-seccion" aria-labelledby="seleccion-titulo">
    <div class="co-ancho">
      <header class="co-encabezado co-encabezado--fila">
        <div>
          <p class="co-sobre">Esta semana en el mesón</p>
          <h2 id="seleccion-titulo">La <em>selección</em></h2>
        </div>
        <p>Cuatro botellas que estamos abriendo en la tienda. Agrégalas a tu bolsa y te las llevamos, o pasa a probarlas.</p>
      </header>
      <ol class="co-seleccion">
        <?php foreach ($seleccion as $id => $p): ?>
        <li class="co-ficha">
          <figure class="co-ficha__foto">
            <?= demo_img($p['foto'], $p['nombre'] . ', ' . $p['origen'], ['ancho' => 600, 'alto' => 800, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 86vw, (max-width: 1100px) 45vw, 270px']) ?>
          </figure>
          <p class="co-ficha__origen"><?= demo_e($p['origen']) ?></p>
          <h3><?= demo_e($p['nombre']) ?></h3>
          <p class="co-ficha__notas"><?= demo_e($p['notas']) ?></p>
          <?= co_agregar($id, $p) ?>
        </li>
        <?php endforeach; ?>
      </ol>
      <p class="co-centro"><a class="co-enlace" href="<?= demo_e(CO_BASE . 'catalogo.php') ?>">Ver todo el catálogo</a></p>
    </div>
  </section>

  <section class="co-seccion co-tinto" aria-labelledby="cata-titulo">
    <div class="co-ancho co-proxima">
      <figure class="co-proxima__foto">
        <?= demo_img($n['fotos']['cata_servir'], 'Sommelier sirviendo vino en copas de cata sobre una mesa oscura', ['ancho' => 900, 'alto' => 1000, 'anchos' => [480, 900, 1300], 'sizes' => '(max-width: 860px) 92vw, 46vw']) ?>
      </figure>
      <div class="co-proxima__texto">
        <p class="co-sobre">Próxima cata</p>
        <p class="co-fecha" aria-hidden="true">
          <strong><?= demo_e($proxima['fecha']->format('j')) ?></strong>
          <span><?= demo_e(mb_strtolower(DEMO_DIAS[(int) $proxima['fecha']->format('N')])) ?><br><?= demo_e(DEMO_MESES[(int) $proxima['fecha']->format('n')]) ?> · <?= demo_e($proxima['hora']) ?></span>
        </p>
        <h2 id="cata-titulo"><?= demo_e($proxima['titulo']) ?></h2>
        <p class="demo-sr"><?= demo_e(ucfirst(demo_fecha_larga($proxima['fecha']))) ?>, a las <?= demo_e($proxima['hora']) ?>.</p>
        <p><?= demo_e($proxima['texto']) ?></p>
        <ul class="co-incluye">
          <?php foreach ($proxima['incluye'] as $item): ?><li><?= demo_e($item) ?></li><?php endforeach; ?>
        </ul>
        <p class="co-cupos"><strong>Quedan <?= (int) $proxima['quedan'] ?> cupos</strong> de <?= (int) $proxima['cupos'] ?> · valor por persona, con todo incluido</p>
        <div class="co-acciones">
          <?= co_agregar($proxima['id'], ['nombre' => 'Cata ' . $proxima['titulo'], 'variantes' => [$proxima['variante'] => $proxima['precio']]], 'Reservar un cupo') ?>
          <a class="co-enlace co-enlace--claro" href="<?= demo_e(CO_BASE . 'catas.php') ?>">Ver todas las catas</a>
        </div>
      </div>
    </div>
  </section>

  <section class="co-seccion" aria-labelledby="cava-titulo">
    <div class="co-ancho co-cava">
      <div class="co-cava__texto">
        <p class="co-sobre">La cava</p>
        <h2 id="cava-titulo">Una tienda chica, <em>con tiempo para conversar</em></h2>
        <p>Cava Ocre es una vinoteca de barrio en el centro de Santiago. No tenemos mil etiquetas: tenemos las que nos gustan, de productores que conocemos y que podemos explicar con calma.</p>
        <ol class="co-principios">
          <li><span>I</span><div><strong>Probamos todo antes</strong><p>Ninguna botella entra a la cava sin pasar por la mesa de cata.</p></div></li>
          <li><span>II</span><div><strong>Productores pequeños</strong><p>Viñas familiares, secanos y destilerías de pocos miles de botellas.</p></div></li>
          <li><span>III</span><div><strong>Te ayudamos a elegir</strong><p>Dinos para qué es y cuánto quieres gastar: el resto lo vemos juntos.</p></div></li>
        </ol>
      </div>
      <div class="co-cava__fotos">
        <figure class="co-cava__grande">
          <?= demo_img($n['fotos']['cava'], 'Sala de la vinoteca con estanterías de madera llenas de botellas', ['ancho' => 900, 'alto' => 1100, 'anchos' => [480, 900, 1300], 'sizes' => '(max-width: 860px) 70vw, 34vw']) ?>
        </figure>
        <figure class="co-cava__chica">
          <?= demo_img($n['fotos']['copa_corchos'], 'Copa de vino con corchos dentro, sobre fondo claro', ['ancho' => 500, 'alto' => 500, 'anchos' => [300, 500, 800], 'sizes' => '(max-width: 860px) 40vw, 200px']) ?>
        </figure>
      </div>
    </div>
  </section>

  <section class="co-banda" aria-labelledby="destilados-titulo">
    <figure class="co-banda__foto" aria-hidden="true">
      <?= demo_img($n['fotos']['destilados'], '', ['ancho' => 1400, 'alto' => 700, 'anchos' => [800, 1400, 2000], 'sizes' => '100vw']) ?>
    </figure>
    <div class="co-ancho co-banda__texto">
      <p class="co-sobre">También en la cava</p>
      <h2 id="destilados-titulo">Pisco, gin y whisky <em>de pequeños alambiques</em></h2>
      <p>Piscos de guarda del Limarí, gin con maqui y canelo, maltas escocesas y un vermut hecho con uva País.</p>
      <a class="co-btn co-btn--ocre" href="<?= demo_e(CO_BASE . 'catalogo.php#cat-destilados') ?>">Ver destilados</a>
    </div>
  </section>

  <section class="co-seccion co-hueso" aria-labelledby="regalo-titulo">
    <div class="co-ancho co-regalo">
      <figure class="co-regalo__foto">
        <?= demo_img($n['fotos']['regalo'], 'Caja de regalo envuelta en papel kraft, atada con hilo y sellada con lacre rojo', ['ancho' => 800, 'alto' => 600, 'anchos' => [480, 800, 1200], 'sizes' => '(max-width: 860px) 92vw, 45vw']) ?>
      </figure>
      <div>
        <p class="co-sobre">Para regalar</p>
        <h2 id="regalo-titulo">Papel kraft, lacre <em>y una tarjeta a mano</em></h2>
        <p>Elige una o dos botellas, agrega la caja de regalo y escribe el mensaje al hacer tu pedido. La envolvemos con viruta, hilo y sello de lacre, y la despachamos al día siguiente.</p>
        <div class="co-acciones">
          <a class="co-btn" href="<?= demo_e(CO_BASE . 'catalogo.php#cat-accesorios') ?>">Armar un regalo</a>
          <a class="co-btn co-btn--linea-oscura" href="<?= demo_e(co_wa('Hola, quiero que me ayuden a elegir un regalo en ' . $n['nombre'] . '. Mi presupuesto es: ')) ?>" target="_blank" rel="noopener">Pedir una recomendación</a>
        </div>
      </div>
    </div>
  </section>

  <div class="co-seccion co-papel">
    <div class="co-ancho"><?php co_google(); ?></div>
  </div>
</main>
<?php co_cerrar();
