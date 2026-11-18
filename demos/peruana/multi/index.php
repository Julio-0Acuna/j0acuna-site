<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = mu();
$firmas = array_filter($n['platos'], static fn (array $p): bool => $p['firma']);
$estado = demo_horario_estado($n['horario']);
$deg = $n['degustacion'];

mu_abrir('inicio', 'Cocina nikkei y criolla en ' . $n['comuna'], 'Demo de sitio de varias páginas para un restaurante peruano nikkei y criollo en ' . $n['comuna'] . ', Santiago: carta con QR y ceviches en 3D, reservas y pedidos por WhatsApp.');
?>
<main id="contenido">
  <section class="mu-portada" aria-labelledby="mu-titulo">
    <div class="mu-ancho mu-portada__grid">
      <div class="mu-portada__texto">
        <p class="mu-sobre"><?= mu_chakana(12) ?> Cocina nikkei y criolla · <?= demo_e($n['comuna']) ?></p>
        <h1 id="mu-titulo">Lima y Japón, <em>en un mismo plato</em></h1>
        <p class="mu-portada__bajada">Pescado de caleta cortado en la barra, wok a fuego vivo y los ajíes de siempre. Una cocina que nació en el puerto del Callao y que hoy servimos en Providencia.</p>
        <div class="mu-acciones">
          <a class="mu-btn" href="<?= demo_e(MU_BASE . 'reservas.php') ?>">Reservar mesa</a>
          <a class="mu-btn mu-btn--linea" href="<?= demo_e(MU_BASE . 'carta.php') ?>">Ver la carta</a>
        </div>
        <p class="mu-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
      </div>
      <div class="mu-portada__visual">
        <figure class="mu-portada__foto">
          <?= demo_img($n['fotos']['hero'], 'Causa nikkei de palta en un plato negro, con palitos y gotas de mayonesa de rocoto', ['lazy' => false, 'ancho' => 900, 'alto' => 1100, 'sizes' => '(max-width: 900px) 92vw, 480px']) ?>
        </figure>
        <a class="mu-ficha" href="<?= demo_e(MU_BASE . 'reservas.php') ?>">
          <span class="mu-ficha__sobre">Con reserva</span>
          <span class="mu-ficha__titulo">Menú degustación</span>
          <span class="mu-ficha__dato"><?= count($deg['tiempos']) ?> tiempos · <?= demo_e(demo_clp($deg['precio'])) ?> por persona</span>
        </a>
      </div>
    </div>
  </section>

  <section class="mu-capitulos" aria-labelledby="capitulos-titulo">
    <div class="mu-ancho">
      <header class="mu-encabezado">
        <p class="mu-sobre"><?= mu_chakana(12) ?> La carta en tres capítulos</p>
        <h2 id="capitulos-titulo">Del mar, del fuego <em>y de la chacra</em></h2>
      </header>
      <ol class="mu-capitulos__lista">
        <li class="mu-capitulo">
          <figure><?= demo_img($n['fotos']['mar'], 'Leche de tigre servida en un cuenco de cerámica oscura', ['ancho' => 600, 'alto' => 760, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 760px) 92vw, 30vw']) ?></figure>
          <p class="mu-capitulo__num">I</p>
          <h3>Del mar</h3>
          <p>Corvina, atún y ostiones que llegan cada mañana. Los cortamos frente a ti en la barra de ceviches.</p>
          <a class="mu-enlace" href="<?= demo_e(MU_BASE . 'carta.php#cat-mar') ?>">Ceviches y tiraditos</a>
        </li>
        <li class="mu-capitulo">
          <figure><?= demo_img($n['fotos']['fuego'], 'Llamas saliendo de un wok en la cocina', ['ancho' => 600, 'alto' => 760, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 760px) 92vw, 30vw']) ?></figure>
          <p class="mu-capitulo__num">II</p>
          <h3>Del fuego</h3>
          <p>El saltado se hace en segundos, con la llama metida en el wok. Los anticuchos, sobre carbón de espino.</p>
          <a class="mu-enlace" href="<?= demo_e(MU_BASE . 'carta.php#cat-fuego') ?>">Wok y parrilla</a>
        </li>
        <li class="mu-capitulo">
          <figure><?= demo_img($n['fotos']['chacra'], 'Canastos con ajíes rojos y amarillos en un mercado', ['ancho' => 600, 'alto' => 760, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 760px) 92vw, 30vw']) ?></figure>
          <p class="mu-capitulo__num">III</p>
          <h3>De la chacra</h3>
          <p>Ají amarillo, rocoto, maíz morado y papa amarilla: las bases de todas nuestras salsas y postres.</p>
          <a class="mu-enlace" href="<?= demo_e(MU_BASE . 'nosotros.php') ?>">Nuestros productores</a>
        </li>
      </ol>
    </div>
  </section>

  <section class="mu-firmas" aria-labelledby="firmas-titulo">
    <div class="mu-ancho">
      <header class="mu-encabezado mu-encabezado--fila">
        <div>
          <p class="mu-sobre"><?= mu_chakana(12) ?> De la casa</p>
          <h2 id="firmas-titulo">Platos <em>de firma</em></h2>
        </div>
        <a class="mu-btn mu-btn--linea" href="<?= demo_e(MU_BASE . 'carta.php') ?>">Toda la carta</a>
      </header>
      <div class="mu-firmas__grid">
        <?php $i = 0; foreach ($firmas as $id => $p): $i++; ?>
        <article class="mu-firma">
          <figure class="mu-firma__foto">
            <?= demo_img($p['foto'], $p['nombre'], ['ancho' => 600, 'alto' => 600, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 640px) 92vw, (max-width: 1100px) 45vw, 270px']) ?>
            <span class="mu-firma__n" aria-hidden="true"><?= sprintf('%02d', $i) ?></span>
          </figure>
          <h3><?= demo_e($p['nombre']) ?></h3>
          <p><?= demo_e($p['desc']) ?></p>
          <p class="mu-firma__precio"><?= count($p['variantes']) > 1 ? 'Desde ' : '' ?><?= demo_e(demo_clp(mu_desde($p))) ?></p>
          <?php if ($p['modelo'] !== ''): ?>
          <?= demo_boton_3d(['id' => $id, 'nombre' => $p['nombre'], 'modelo' => $p['modelo'], 'foto' => $p['foto']], 'mu-btn-3d') ?>
          <?php endif; ?>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="mu-degustacion" aria-labelledby="degustacion-titulo">
    <div class="mu-ancho mu-degustacion__grid">
      <div>
        <p class="mu-sobre"><?= mu_chakana(12) ?> Solo con reserva</p>
        <h2 id="degustacion-titulo">Menú degustación <em>de <?= count($deg['tiempos']) ?> tiempos</em></h2>
        <p>Un recorrido por la carta, pensado para toda la mesa. Dura cerca de dos horas y cambia con la pesca de la semana.</p>
        <p class="mu-degustacion__precio"><strong><?= demo_e(demo_clp($deg['precio'])) ?></strong> por persona · maridaje sin alcohol + <?= demo_e(demo_clp($deg['maridaje'])) ?></p>
        <a class="mu-btn" href="<?= demo_e(MU_BASE . 'reservas.php?ocasion=degustacion') ?>">Reservar el degustación</a>
      </div>
      <ol class="mu-tiempos">
        <?php foreach ($deg['tiempos'] as $k => [$tiempo, $detalle]): ?>
        <li><span class="mu-tiempos__n"><?= $k + 1 ?></span><span><strong><?= demo_e($tiempo) ?></strong><small><?= demo_e($detalle) ?></small></span></li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section class="mu-seccion" aria-labelledby="llevar-titulo">
    <div class="mu-ancho mu-llevar">
      <figure class="mu-llevar__foto"><?= demo_img($n['fotos']['cremas'], 'Cuencos de madera con salsas y cremas de colores sobre la mesa', ['ancho' => 800, 'alto' => 600, 'sizes' => '(max-width: 860px) 92vw, 45vw']) ?></figure>
      <div>
        <p class="mu-sobre"><?= mu_chakana(12) ?> Para llevar</p>
        <h2 id="llevar-titulo">La barra, <em>en tu casa</em></h2>
        <p>Arma tu pedido en la carta, elige retiro o despacho, qué tan picante lo quieres y si necesitas cubiertos. Llega a nuestro WhatsApp listo para preparar, sin comisiones de aplicaciones.</p>
        <div class="mu-acciones">
          <a class="mu-btn" href="<?= demo_e(MU_BASE . 'carta.php') ?>">Armar mi pedido</a>
          <a class="mu-btn mu-btn--linea" href="<?= demo_e(demo_tel()) ?>">Llamar</a>
        </div>
      </div>
    </div>
  </section>

  <div class="mu-seccion mu-claro">
    <div class="mu-ancho">
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
<?php mu_cerrar(['3d']);
