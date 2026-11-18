<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = pg();
$estado = demo_horario_estado($n['horario']);
$destacados = array_filter($n['productos'], static fn (array $p): bool => $p['destacado']);

pg_abrir('inicio', 'Casa de pinturas en ' . $n['comuna'], 'Demo de sitio de varias páginas para una tienda de pinturas y terminaciones en ' . $n['comuna'] . ', Santiago: carta de colores, calculadora de pintura, asesoría de colorista y cotización por WhatsApp.');
?>
<main id="contenido">
  <section class="pg-hero" aria-labelledby="hero-titulo">
    <div class="pg-ancho pg-hero__grid">
      <div class="pg-hero__texto">
        <p class="pg-sobre"><?= demo_e($n['eslogan']) ?> · <?= demo_e($n['comuna']) ?> · desde <?= (int) $n['desde'] ?></p>
        <h1 id="hero-titulo">El color correcto, <em>a la primera.</em></h1>
        <p class="pg-hero__bajada">Pinturas preparadas a máquina en cualquier color, una colorista que va a tu casa y la cantidad justa calculada antes de comprar. Cotizas aquí y te llega todo por WhatsApp.</p>
        <div class="pg-acciones">
          <a class="pg-btn" href="<?= demo_e(PG_BASE . 'colores.php') ?>">Ver la carta de colores</a>
          <a class="pg-btn pg-btn--linea" href="<?= demo_e(PG_BASE . 'catalogo.php') ?>">Cotizar pintura</a>
        </div>
        <p class="pg-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
      </div>
      <figure class="pg-hero__foto">
        <?= demo_img($n['fotos']['hero'], 'Muro pintado color terracota con una planta en un macetero de greda', ['lazy' => false, 'ancho' => 900, 'alto' => 1150, 'anchos' => [500, 900, 1300], 'sizes' => '(max-width: 860px) 92vw, 46vw']) ?>
        <figcaption class="pg-hero__chip"><?= pg_chip('PG-201', 'pg-chip--grande', false) ?><span class="pg-hero__nota">El muro de la foto, en látex mate.</span></figcaption>
      </figure>
    </div>
  </section>

  <section class="pg-seccion pg-temporada" aria-labelledby="temporada-titulo">
    <div class="pg-ancho">
      <div class="pg-cabeza pg-cabeza--fila">
        <div>
          <p class="pg-sobre">Temporada de verano</p>
          <h2 id="temporada-titulo">Cuatro colores que <em>estamos preparando mucho</em></h2>
        </div>
        <a class="pg-enlace" href="<?= demo_e(PG_BASE . 'colores.php') ?>">Ver los 24 colores de la carta</a>
      </div>
      <div class="pg-abanico">
        <?php foreach ($n['temporada'] as $codigo): ?><?= pg_chip($codigo, 'pg-chip--abanico') ?><?php endforeach; ?>
      </div>
      <p class="pg-pequeno">Toca un color para cotizarlo. Lo preparamos en látex, esmalte al agua o pintura para fachadas.</p>
    </div>
  </section>

  <section class="pg-seccion pg-seccion--tinta" aria-labelledby="acertar-titulo">
    <div class="pg-ancho">
      <div class="pg-cabeza">
        <p class="pg-sobre">Asesoría</p>
        <h2 id="acertar-titulo">Tres maneras de <em>no equivocarse</em> de color</h2>
      </div>
      <ol class="pg-maneras">
        <?php foreach (array_slice($n['servicios'], 0, 3) as $i => [$nombre, $detalle, $precio, $foto, $alt]): ?>
        <li class="pg-manera">
          <?= demo_img($n['fotos'][$foto], $alt, ['ancho' => 700, 'alto' => 520, 'anchos' => [400, 700, 1000], 'sizes' => '(max-width: 860px) 92vw, 30vw']) ?>
          <span class="pg-manera__num" aria-hidden="true">0<?= $i + 1 ?></span>
          <h3><?= demo_e($nombre) ?></h3>
          <p><?= demo_e($detalle) ?></p>
          <p class="pg-manera__precio"><?= demo_e($precio) ?></p>
        </li>
        <?php endforeach; ?>
      </ol>
      <a class="pg-btn pg-btn--claro" href="<?= demo_e(PG_BASE . 'asesoria.php') ?>">Conocer la asesoría</a>
    </div>
  </section>

  <section class="pg-seccion" aria-labelledby="destacados-titulo">
    <div class="pg-ancho">
      <div class="pg-cabeza pg-cabeza--fila">
        <div>
          <p class="pg-sobre">Lo que más sale del mesón</p>
          <h2 id="destacados-titulo">Pinturas <em>para empezar</em></h2>
        </div>
        <a class="pg-enlace" href="<?= demo_e(PG_BASE . 'catalogo.php') ?>">Ver todo el catálogo</a>
      </div>
      <ul class="pg-productos">
        <?php foreach ($destacados as $id => $p): ?>
        <li class="pg-producto">
          <div class="pg-producto__foto"><?= demo_img($p['foto'], $p['alt'], ['ancho' => 600, 'alto' => 600, 'anchos' => [320, 600, 900], 'sizes' => '(max-width: 600px) 92vw, (max-width: 1000px) 45vw, 280px']) ?></div>
          <p class="pg-producto__cat"><?= demo_e($n['categorias'][$p['cat']][0]) ?></p>
          <h3><?= demo_e($p['nombre']) ?></h3>
          <p class="pg-producto__desc"><?= demo_e($p['desc']) ?></p>
          <?= pg_agregar($id, $p) ?>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="pg-seccion pg-calcula" aria-labelledby="calcula-titulo">
    <div class="pg-ancho pg-calcula__grid">
      <figure class="pg-calcula__foto"><?= demo_img($n['fotos']['salvia'], 'Muro verde salvia con maceteros de greda y plantas colgando', ['ancho' => 900, 'alto' => 700, 'anchos' => [500, 900, 1300], 'sizes' => '(max-width: 860px) 92vw, 50vw']) ?></figure>
      <div>
        <p class="pg-sobre">Antes de comprar</p>
        <h2 id="calcula-titulo">¿Cuántos galones <em>necesitas?</em></h2>
        <p>Un galón rinde unos 35 m² por mano en muro liso. Anota el largo de tus muros, la altura y cuántas puertas y ventanas hay, y la calculadora te dice si te conviene galón o tineta.</p>
        <a class="pg-btn" href="<?= demo_e(PG_BASE . 'colores.php#calculadora') ?>">Usar la calculadora</a>
      </div>
    </div>
  </section>

  <div class="pg-seccion pg-seccion--papel">
    <div class="pg-ancho">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería la ficha de tu tienda en Google Maps: horario, cómo llegar y opiniones de clientes.']); ?>
    </div>
  </div>
</main>
<?php pg_cerrar();
