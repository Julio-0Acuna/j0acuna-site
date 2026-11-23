<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = br();
$estado = demo_horario_estado($n['horario']);
$destacados = array_filter($n['servicios'], static fn (array $s): bool => isset($s['destacado']));

br_abrir('inicio', 'Spa canino en ' . $n['comuna'], 'Demo de página web de varias páginas para un spa canino boutique en ' . $n['comuna'] . ', Santiago: rituales de baño, corte de raza a tijera, deslanado, traslado ida y vuelta y reserva por WhatsApp.');
?>
<main id="contenido">
  <section class="br-hero" aria-labelledby="hero-titulo">
    <div class="br-ancho br-hero__grid">
      <div class="br-hero__texto">
        <p class="br-sobre">Spa canino boutique · <?= demo_e($n['comuna']) ?></p>
        <h1 id="hero-titulo">Un baño tibio, <em>sin apuro.</em></h1>
        <p class="br-hero__bajada">En Bruma atendemos a un perro a la vez: agua a 32 °C, productos suaves, secado silencioso y corte a tijera. Si quieres, lo buscamos en tu casa y te lo devolvemos dormido.</p>
        <div class="br-acciones">
          <a class="br-btn" href="<?= demo_e(BR_BASE . 'rituales.php') ?>">Ver los rituales</a>
          <a class="br-btn br-btn--linea" href="<?= demo_e(BR_BASE . 'reserva.php') ?>">Reservar</a>
        </div>
        <p class="br-estado"><span class="br-estado__punto<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
      </div>
      <div class="br-hero__arte">
        <figure class="br-ovalo br-ovalo--grande">
          <?= demo_img($n['fotos']['hero'], 'Perrito blanco envuelto en una toalla y con gorro de baño, mirando a la cámara', ['lazy' => false, 'ancho' => 800, 'alto' => 1000, 'sizes' => '(max-width: 860px) 78vw, 440px', 'anchos' => [480, 800, 1100]]) ?>
        </figure>
        <figure class="br-ovalo br-ovalo--chico">
          <?= demo_img($n['fotos']['sillon'], 'Pomerania color crema sentado en un sillón negro de peluquería con un juguete en el hocico', ['ancho' => 400, 'alto' => 520, 'sizes' => '(max-width: 860px) 34vw, 190px', 'anchos' => [300, 400, 600]]) ?>
        </figure>
        <svg class="br-sello" viewBox="0 0 120 120" aria-hidden="true" focusable="false">
          <defs><path id="br-circulo" d="M60 60m-46 0a46 46 0 1 1 92 0a46 46 0 1 1-92 0"/></defs>
          <circle cx="60" cy="60" r="58" fill="#22313a"/>
          <text><textPath href="#br-circulo">un perro a la vez · agua tibia · sin jaulas ·</textPath></text>
          <path d="M44 56c5-4 10-4 16 0s11 4 16 0M44 66c5-4 10-4 16 0s11 4 16 0" fill="none" stroke="#efc4af" stroke-width="2.2" stroke-linecap="round"/>
        </svg>
      </div>
    </div>
  </section>

  <ul class="br-hechos br-ancho" aria-label="Así trabajamos">
    <li><strong>1</strong><span>perro por sala</span></li>
    <li><strong>32 °C</strong><span>el agua del baño</span></li>
    <li><strong>0</strong><span>jaulas de secado</span></li>
    <li><strong>Ida y vuelta</strong><span>traslado desde tu casa</span></li>
  </ul>

  <section class="br-seccion" aria-labelledby="rituales-titulo">
    <div class="br-ancho">
      <header class="br-encabezado br-encabezado--centro">
        <p class="br-sobre">La carta</p>
        <h2 id="rituales-titulo">Tres rituales <em>para empezar</em></h2>
        <p>Precios según el tamaño. Agrega el que quieras a tu reserva y, en el último paso, cuéntanos de tu perro.</p>
      </header>
      <div class="br-rituales">
        <?php foreach ($destacados as $id => $s): ?>
        <article class="br-ritual">
          <figure class="br-ovalo br-ritual__foto"><?= demo_img($s['foto'], $s['alt'], ['ancho' => 520, 'alto' => 650, 'anchos' => [320, 520, 800], 'sizes' => '(max-width: 760px) 70vw, 300px']) ?></figure>
          <p class="br-ritual__num" aria-hidden="true"><?= demo_e($s['destacado']) ?></p>
          <h3><?= demo_e($s['nombre']) ?></h3>
          <p class="br-meta"><?= demo_e($s['duracion']) ?></p>
          <p class="br-ritual__desc"><?= demo_e($s['desc']) ?></p>
          <?= br_agregar($id, $s) ?>
        </article>
        <?php endforeach; ?>
      </div>
      <p class="br-centro"><a class="br-enlace" href="<?= demo_e(BR_BASE . 'rituales.php') ?>">Ver la carta completa: baños, tijera, pelaje, detalles, gatos y botica</a></p>
    </div>
  </section>

  <section class="br-seccion br-seccion--terrazo" aria-labelledby="minutos-titulo">
    <div class="br-ancho br-minutos">
      <figure class="br-ovalo br-minutos__foto">
        <?= demo_img($n['fotos']['tina'], 'Cachorro golden con collar verde apoyado en el borde de la tina, esperando su baño', ['ancho' => 700, 'alto' => 900, 'sizes' => '(max-width: 900px) 80vw, 420px', 'anchos' => [400, 700, 1000]]) ?>
      </figure>
      <div>
        <header class="br-encabezado">
          <p class="br-sobre">El Ritual Bruma</p>
          <h2 id="minutos-titulo">Noventa minutos, <em>minuto a minuto</em></h2>
        </header>
        <ol class="br-linea">
          <?php foreach ($n['ritual'] as [$min, $titulo, $texto]): ?>
          <li><span class="br-linea__min"><?= demo_e($min) ?></span><div><strong><?= demo_e($titulo) ?></strong><span><?= demo_e($texto) ?></span></div></li>
          <?php endforeach; ?>
        </ol>
      </div>
    </div>
  </section>

  <section class="br-seccion br-seccion--pizarra" aria-labelledby="traslado-titulo">
    <div class="br-ancho br-traslado">
      <div>
        <p class="br-sobre br-sobre--claro">Traslado ida y vuelta</p>
        <h2 id="traslado-titulo">Lo buscamos en tu casa <em>y vuelve dormido</em></h2>
        <p>Pasamos a buscarlo en un auto con jaula acolchada y aire acondicionado, y te lo devolvemos al terminar. Un solo valor de ida y vuelta según tu comuna; gratis en reservas sobre <?= demo_e(demo_clp($n['traslado_gratis'])) ?>.</p>
        <dl class="br-comunas">
          <?php foreach ($n['traslado'] as $comuna => $costo): ?>
          <div><dt><?= demo_e($comuna) ?></dt><dd><?= demo_e(demo_clp($costo)) ?></dd></div>
          <?php endforeach; ?>
        </dl>
        <a class="br-btn br-btn--durazno" href="<?= demo_e(BR_BASE . 'reserva.php') ?>">Reservar con traslado</a>
      </div>
      <figure class="br-ovalo br-ovalo--horizontal">
        <?= demo_img($n['fotos']['siesta'], 'Golden retriever durmiendo sobre un sillón gris después del baño', ['ancho' => 900, 'alto' => 640, 'sizes' => '(max-width: 900px) 92vw, 50vw']) ?>
      </figure>
    </div>
  </section>

  <section class="br-seccion" aria-labelledby="casa-titulo">
    <div class="br-ancho">
      <header class="br-encabezado br-encabezado--fila">
        <div>
          <p class="br-sobre">La casa</p>
          <h2 id="casa-titulo">Dos salas, <em>una tina tibia</em> y mucha calma</h2>
        </div>
        <p>Una casona en <?= demo_e($n['comuna']) ?> con piso de terrazo, luz natural y música baja. Sin ladridos de fondo ni perros esperando en jaulas.</p>
      </header>
      <ul class="br-trio">
        <li><figure class="br-ovalo"><?= demo_img($n['fotos']['tijeras'], 'Tijera de peluquería y peineta negra sobre un mesón azul, bajo una lámpara', ['ancho' => 520, 'alto' => 650, 'anchos' => [320, 520, 800], 'sizes' => '(max-width: 700px) 44vw, 30vw']) ?></figure><p>Tijera y peineta: <em>la terminación es a mano</em></p></li>
        <li><figure class="br-ovalo"><?= demo_img($n['fotos']['caniche'], 'Caniche gris recién peinado, en blanco y negro', ['ancho' => 520, 'alto' => 650, 'anchos' => [320, 520, 800], 'sizes' => '(max-width: 700px) 44vw, 30vw']) ?></figure><p>Corte de raza <em>con el estándar o a tu gusto</em></p></li>
        <li><figure class="br-ovalo"><?= demo_img($n['fotos']['groomer'], 'Groomer terminando a tijera el corte de un caniche gris sobre la mesa de trabajo', ['ancho' => 520, 'alto' => 650, 'anchos' => [320, 520, 800], 'sizes' => '(max-width: 700px) 44vw, 30vw']) ?></figure><p>Una groomer por perro, <em>de principio a fin</em></p></li>
      </ul>
      <p class="br-centro"><a class="br-enlace" href="<?= demo_e(BR_BASE . 'casa.php') ?>">Conoce la casa y al equipo</a></p>
    </div>
  </section>

  <div class="br-seccion br-seccion--hueso">
    <div class="br-ancho">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería la ficha del spa en Google Maps: horario, cómo llegar y lo que dicen sus clientes.']); ?>
    </div>
  </div>

  <section class="br-cierre" aria-labelledby="cierre-titulo">
    <div class="br-ancho br-cierre__grid">
      <figure class="br-ovalo br-cierre__foto"><?= demo_img($n['fotos']['toalla'], '', ['ancho' => 600, 'alto' => 760, 'anchos' => [360, 600, 900], 'sizes' => '(max-width: 760px) 60vw, 320px']) ?></figure>
      <div>
        <h2 id="cierre-titulo">¿Le reservamos <em>su próximo ritual?</em></h2>
        <p>Elige el ritual y el tamaño, cuéntanos de tu perro y te confirmamos por WhatsApp.</p>
        <div class="br-acciones">
          <a class="br-btn br-btn--durazno" href="<?= demo_e(BR_BASE . 'reserva.php') ?>">Reservar</a>
          <a class="br-btn br-btn--linea-clara" href="<?= demo_e(br_wa('Hola, quiero reservar en ' . $n['nombre'] . '. Mi perro se llama: ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> WhatsApp</a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php br_cerrar();
