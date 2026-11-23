<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = nc();
$destacados = array_filter($n['productos'], static fn (array $p): bool => !empty($p['destacado']));
$estrella = $n['productos']['serum-niacinamida'];

nc_abrir('inicio', 'Farmacia y dermocosmética', 'Demo de página web de varias páginas para una farmacia de dermocosmética en ' . $n['comuna'] . ', Santiago: tienda con buscador y códigos, asesoría de piel, cotización de recetas y pedidos por WhatsApp.');
?>
<main id="contenido">
  <section class="nc-hero" aria-labelledby="nc-hero-titulo">
    <div class="nc-ancho nc-hero__grid">
      <div class="nc-hero__texto">
        <p class="nc-sobre">Farmacia · Dermocosmética · Bienestar</p>
        <h1 id="nc-hero-titulo">Farmacia y dermocosmética, <em>con calma.</em></h1>
        <p class="nc-hero__bajada">Protectores, limpiadores, sérums y lo de siempre de la farmacia, con una química farmacéutica que te ayuda a elegir sin apuro. Arma tu bolsa y te llega por WhatsApp: retiras en <?= demo_e($n['comuna']) ?> o te lo llevamos en el día.</p>
        <div class="nc-acciones">
          <a class="nc-btn" href="<?= demo_e(NC_BASE . 'tienda.php') ?>">Ver la tienda</a>
          <a class="nc-btn nc-btn--linea" href="<?= demo_e(NC_BASE . 'farmacia.php#asesoria') ?>">Asesoría de piel sin costo</a>
        </div>
      </div>
      <div class="nc-hero__visual">
        <figure class="nc-hero__foto">
          <?= demo_img($n['fotos']['hero'], 'Retrato de una mujer con los ojos cerrados y la piel al natural, con luz suave', ['lazy' => false, 'ancho' => 900, 'alto' => 1100, 'anchos' => [480, 900, 1300], 'sizes' => '(max-width: 860px) 88vw, 480px']) ?>
        </figure>
        <div class="nc-hero__ficha" aria-label="Producto destacado">
          <?= nc_muestra($estrella['textura'], 'nc-muestra--chica') ?>
          <div>
            <p class="nc-mono"><?= demo_e($estrella['codigo']) ?> · <?= demo_e($estrella['datos']['Textura']) ?></p>
            <p class="nc-hero__ficha-nombre"><?= demo_e($estrella['nombre']) ?></p>
            <p class="nc-hero__ficha-precio"><?= demo_e(demo_clp($estrella['variantes']['Única'])) ?> <small>30 ml</small></p>
          </div>
        </div>
        <span class="nc-burbuja nc-burbuja--1" aria-hidden="true"></span>
        <span class="nc-burbuja nc-burbuja--2" aria-hidden="true"></span>
      </div>
    </div>
  </section>

  <ul class="nc-ancho nc-promesas" aria-label="Lo que ofrece la farmacia">
    <li><span class="nc-mono">01</span><strong>Asesoría de piel</strong><small>20 minutos, sin costo y sin presión.</small></li>
    <li><span class="nc-mono">02</span><strong>Despacho en el día</strong><small>En <?= demo_e($n['comuna']) ?>, en <?= demo_e($n['despacho_minutos']) ?>.</small></li>
    <li><span class="nc-mono">03</span><strong>Recetas por WhatsApp</strong><small>Cotiza y la apartamos.</small></li>
    <li><span class="nc-mono">04</span><strong>Probadores en sala</strong><small>Siente la textura antes.</small></li>
  </ul>

  <section class="nc-seccion" aria-labelledby="necesidad-titulo">
    <div class="nc-ancho">
      <div class="nc-cabeza">
        <p class="nc-sobre">Elige por necesidad</p>
        <h2 id="necesidad-titulo">Seis estantes, una sola farmacia</h2>
      </div>
      <div class="nc-estantes">
        <?php foreach ($n['categorias'] as $slug => [$num, $nombre, $desc, $foto, $alt]): ?>
        <a class="nc-estante" href="<?= demo_e(NC_BASE . 'tienda.php?cat=' . $slug) ?>">
          <span class="nc-estante__foto"><?= demo_img($foto, $alt, ['ancho' => 520, 'alto' => 520, 'anchos' => [300, 520, 800], 'sizes' => '(max-width: 700px) 42vw, 180px']) ?></span>
          <span class="nc-mono"><?= demo_e($num) ?></span>
          <strong><?= demo_e($nombre) ?></strong>
          <small><?= demo_e($desc) ?></small>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="nc-seccion nc-seccion--nacar" aria-labelledby="favoritos-titulo">
    <div class="nc-ancho">
      <div class="nc-cabeza nc-cabeza--fila">
        <div>
          <p class="nc-sobre">Lo que más se lleva</p>
          <h2 id="favoritos-titulo">Favoritos de la semana</h2>
        </div>
        <a class="nc-enlace" href="<?= demo_e(NC_BASE . 'tienda.php') ?>">Ver toda la tienda →</a>
      </div>
      <div class="nc-fichas">
        <?php foreach ($destacados as $id => $p): ?><?= nc_ficha($id, $p) ?><?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="nc-seccion nc-texturas" aria-labelledby="texturas-titulo">
    <div class="nc-ancho nc-texturas__grid">
      <figure class="nc-texturas__foto">
        <?= demo_img($n['fotos']['texturas'], 'Brazo con tres pequeñas muestras de crema blanca sobre una tela clara', ['ancho' => 900, 'alto' => 1000, 'anchos' => [480, 900, 1300], 'sizes' => '(max-width: 860px) 92vw, 50vw']) ?>
      </figure>
      <div>
        <p class="nc-sobre">Asesoría de piel</p>
        <h2 id="texturas-titulo">Primero la textura, después la compra</h2>
        <p>En la farmacia hay probadores de casi todo. Te mostramos dos o tres opciones en el dorso de la mano, te contamos cómo se usan y te llevas solo lo que te acomoda.</p>
        <ul class="nc-lista-mono">
          <li><span>Fluido</span> Se esparce fácil y no se siente.</li>
          <li><span>Gel</span> Fresco, para pieles que brillan.</li>
          <li><span>Crema rica</span> Envolvente, para la noche.</li>
          <li><span>Aceite seco</span> Se absorbe sin dejar brillo.</li>
        </ul>
        <p class="nc-pequeno">La asesoría es de cosmética: no reemplaza la consulta médica ni dermatológica.</p>
        <a class="nc-btn" href="<?= demo_e(nc_wa('Hola, quiero agendar una asesoría de piel en ' . $n['nombre_corto'] . '. ¿Qué horarios tienen?', 'Reserva')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Agendar por WhatsApp</span></a>
      </div>
    </div>
  </section>

  <section class="nc-receta-banda" aria-labelledby="receta-banda-titulo">
    <div class="nc-ancho nc-receta-banda__grid">
      <div>
        <p class="nc-sobre nc-sobre--claro">Medicamentos con receta</p>
        <h2 id="receta-banda-titulo">¿Tienes una receta? La cotizamos por WhatsApp.</h2>
        <p>Los medicamentos con receta no van en la bolsa: nos dices cuál es, te confirmamos precio y stock y lo apartamos. La receta se presenta al retirar o al recibir y la revisa nuestra química farmacéutica.</p>
      </div>
      <a class="nc-btn nc-btn--claro" href="<?= demo_e(NC_BASE . 'receta.php') ?>">Cotizar una receta</a>
    </div>
  </section>

  <div class="nc-seccion">
    <div class="nc-ancho">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería la ficha de la farmacia en Google Maps: horario, cómo llegar y reseñas de clientes.']); ?>
    </div>
  </div>
</main>
<?php nc_cerrar();
