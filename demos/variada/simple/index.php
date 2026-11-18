<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version);

/** Enlace de WhatsApp con la nota de pedido de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);
$waGeneral = $wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.');
$estado = demo_horario_estado($n['horario']);

// Menú del día: de lunes a viernes el de hoy; el fin de semana se muestra el del lunes.
$hoy = (int) (new DateTimeImmutable('now', new DateTimeZone(DEMO_ZONA_HORARIA)))->format('N');
$diaMenu = $hoy <= 5 ? $hoy : 1;
$delDia = $n['semana'][$diaMenu];
$etiquetaDia = $hoy <= 5 ? 'Hoy ' . mb_strtolower(DEMO_DIAS[$hoy]) : 'El lunes';
$waMenu = $wa('Hola, quiero reservar el menú del día ' . ($hoy <= 5 ? 'de hoy' : 'del lunes') . ' (' . mb_strtolower(DEMO_DIAS[$diaMenu]) . '): '
    . $delDia['plato'] . ', con ' . mb_strtolower($delDia['entrada']) . ', ' . mb_strtolower($delDia['postre']) . ' y jugo natural (' . demo_clp($n['precio_menu']) . ').');

demo_head([
    'titulo' => $n['nombre'] . ' · Comida casera en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para una picada de comida casera en ' . $n['comuna'] . ', Santiago: menú del día, carta con QR, colaciones para oficinas y pedidos por WhatsApp.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Young+Serif&family=Permanent+Marker&family=Public+Sans:wght@400;600;800&display=swap',
    'css' => ['/demos/variada/simple/estilo.css'],
    'tema' => '#a8462a',
    'icono' => ['E', '#a8462a', '#fbf4e6'],
    'clase_body' => 'de',
]);
demo_aviso($rubro);
?>
<header class="de-top">
  <div class="de-envoltura de-top__fila">
    <a class="de-marca" href="#contenido">
      <svg class="de-marca__olla" viewBox="0 0 48 48" width="44" height="44" aria-hidden="true" focusable="false">
        <path d="M17 12c-2-3 2-4 0-7M24 12c-2-3 2-4 0-7M31 12c-2-3 2-4 0-7" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
        <path d="M5 19h38c0 12-8 21-19 21S5 31 5 19z" fill="currentColor"/>
        <path d="M2 19h44" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
        <path d="M11 25c2 6 6 9 13 10" fill="none" stroke="#fbf4e6" stroke-width="2" stroke-linecap="round" opacity=".55"/>
      </svg>
      <span class="de-marca__nombre"><small>Picada</small><?= demo_e($n['nombre_corto']) ?></span>
    </a>
    <nav class="de-nav" aria-label="Secciones">
      <a href="#menu-del-dia">Menú del día</a>
      <a href="#siempre">Platos</a>
      <a href="#carta">Carta</a>
      <a href="#oficinas">Oficinas</a>
      <a href="#google">Ubicación</a>
    </nav>
    <a class="de-btn de-btn--chico" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Pedir</span></a>
  </div>
</header>

<main id="contenido">
  <section class="de-hero" aria-labelledby="de-hero-titulo">
    <div class="de-envoltura de-hero__grid">
      <div class="de-hero__texto">
        <p class="de-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <h1 id="de-hero-titulo"><span class="de-hero__ante">Picada</span> <?= demo_e($n['nombre_corto']) ?></h1>
        <p class="de-hero__eslogan"><?= demo_e($n['eslogan']) ?></p>
        <p class="de-hero__bajada"><?= demo_e($n['bajada']) ?></p>
        <div class="de-acciones">
          <a class="de-btn" href="<?= demo_e($waMenu) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Reservar mi colación</span></a>
          <a class="de-btn de-btn--claro" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
      </div>
      <div class="de-hero__plato">
        <div class="de-paila">
          <span class="de-paila__foto"><?= demo_img($n['hero_foto'], 'Guiso casero servido con cucharón desde una olla de greda', ['lazy' => false, 'ancho' => 900, 'alto' => 900, 'sizes' => '(max-width: 760px) 80vw, 460px']) ?></span>
        </div>
        <a class="de-cartulina de-cartulina--amarillo de-hero__cartulina" href="#menu-del-dia">
          <span class="de-cartulina__dia"><?= demo_e($etiquetaDia) ?></span>
          <strong><?= demo_e($delDia['plato']) ?></strong>
          <span class="de-cartulina__precio"><?= demo_e(demo_clp($n['precio_menu'])) ?></span>
          <small>con entrada, postre y jugo</small>
        </a>
      </div>
    </div>
  </section>

  <section class="de-seccion de-dia" id="menu-del-dia" aria-labelledby="dia-titulo">
    <div class="de-envoltura">
      <div class="de-cabeza">
        <p class="de-ante">De lunes a viernes, desde las 12:00</p>
        <h2 id="dia-titulo">Menú del día</h2>
        <p class="de-cabeza__texto">Entrada, plato de fondo, postre, jugo natural y pan amasado por <strong><?= demo_e(demo_clp($n['precio_menu'])) ?></strong>. Solo el fondo, <?= demo_e(demo_clp($n['precio_solo_fondo'])) ?>.</p>
      </div>

      <article class="de-hoy" aria-labelledby="hoy-titulo">
        <div class="de-hoy__foto">
          <?= demo_img($delDia['foto'], $delDia['plato'] . ' servido en plato hondo', ['ancho' => 800, 'alto' => 800, 'anchos' => [480, 800, 1100], 'sizes' => '(max-width: 760px) 90vw, 420px']) ?>
        </div>
        <div class="de-hoy__texto">
          <p class="de-hoy__dia"><?= demo_e($etiquetaDia) ?></p>
          <h3 id="hoy-titulo"><?= demo_e($delDia['plato']) ?></h3>
          <p><?= demo_e($delDia['detalle']) ?></p>
          <dl class="de-hoy__tiempos">
            <div><dt>Entrada</dt><dd><?= demo_e($delDia['entrada']) ?></dd></div>
            <div><dt>Fondo</dt><dd><?= demo_e($delDia['plato']) ?></dd></div>
            <div><dt>Postre</dt><dd><?= demo_e($delDia['postre']) ?></dd></div>
            <div><dt>Bebida</dt><dd>Jugo natural y pan amasado</dd></div>
          </dl>
          <div class="de-acciones">
            <a class="de-btn" href="<?= demo_e($waMenu) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Reservar <?= $hoy <= 5 ? 'la de hoy' : 'la del lunes' ?></span></a>
            <p class="de-hoy__precio"><?= demo_e(demo_clp($n['precio_menu'])) ?> <small>+ <?= demo_e(demo_clp($n['precio_envase'])) ?> el táper para llevar</small></p>
          </div>
          <?php if ($hoy > 5): ?><p class="de-nota">Los sábados y domingos cocinamos solo a la carta.</p><?php endif; ?>
        </div>
      </article>

      <h3 class="de-semana__titulo">La semana en la olla</h3>
      <ol class="de-semana">
        <?php foreach ($n['semana'] as $d => $m): ?>
        <li class="de-cartulina de-cartulina--<?= demo_e($m['color']) ?><?= $d === $diaMenu ? ' is-hoy' : '' ?>"<?= $d === $diaMenu ? ' aria-current="date"' : '' ?>>
          <span class="de-cartulina__dia"><?= demo_e(DEMO_DIAS[$d]) ?><?= $d === $hoy ? ' · hoy' : '' ?></span>
          <strong><?= demo_e($m['plato']) ?></strong>
          <small><?= demo_e($m['entrada']) ?> · <?= demo_e($m['postre']) ?></small>
          <a href="<?= demo_e($wa('Hola, quiero reservar el menú del ' . mb_strtolower(DEMO_DIAS[$d]) . ': ' . $m['plato'] . ' (' . demo_clp($n['precio_menu']) . ').')) ?>" target="_blank" rel="noopener" aria-label="Reservar el menú del <?= demo_e(mb_strtolower(DEMO_DIAS[$d])) ?> por WhatsApp">Reservar</a>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section class="de-seccion" id="siempre" aria-labelledby="siempre-titulo">
    <div class="de-envoltura">
      <div class="de-cabeza">
        <p class="de-ante">Todos los días, a la carta</p>
        <h2 id="siempre-titulo">Los platos de siempre</h2>
      </div>
      <div class="de-platos">
        <?php foreach ($n['siempre'] as $p): ?>
        <article class="de-plato">
          <div class="de-plato__foto">
            <?= demo_img($p['foto'], $p['nombre'], ['ancho' => 500, 'alto' => 500, 'anchos' => [320, 500, 800], 'sizes' => '(max-width: 600px) 70vw, 240px']) ?>
            <?php if ($p['nota'] !== ''): ?><span class="de-plato__nota"><?= demo_e($p['nota']) ?></span><?php endif; ?>
          </div>
          <h3><?= demo_e($p['nombre']) ?></h3>
          <p><?= demo_e($p['desc']) ?></p>
          <a class="de-plato__pedir" href="<?= demo_e($wa('Hola, quiero pedir: ' . $p['nombre'] . ' (' . demo_clp($p['precio']) . ').')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($p['nombre']) ?> por WhatsApp, <?= demo_e(demo_clp($p['precio'])) ?>">
            <strong><?= demo_e(demo_clp($p['precio'])) ?></strong><span>Pedir</span>
          </a>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="de-seccion de-seccion--papel" id="carta" aria-labelledby="carta-titulo">
    <div class="de-envoltura">
      <div class="de-cabeza">
        <p class="de-ante">Todo lo que sale de la cocina</p>
        <h2 id="carta-titulo">La carta</h2>
      </div>
      <div class="de-carta">
        <div class="de-carta__hoja">
          <?php foreach ($n['carta'] as $categoria => $platos): ?>
          <div class="de-carta__grupo">
            <h3><?= demo_e($categoria) ?></h3>
            <ul>
              <?php foreach ($platos as [$plato, $detalle, $precio]): ?>
              <li>
                <span class="de-carta__linea"><strong><?= demo_e($plato) ?></strong><span class="de-carta__puntos" aria-hidden="true"></span><span class="de-carta__precio"><?= demo_e($precio) ?></span></span>
                <span class="de-carta__detalle"><?= demo_e($detalle) ?>
                  <a href="<?= demo_e($wa('Hola, quiero pedir: ' . $plato . ' (' . $precio . ').')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($plato) ?> por WhatsApp">Pedir</a></span>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endforeach; ?>
        </div>
        <aside class="de-carta__lado" aria-label="La carta en tu celular">
          <div class="de-qr-tarjeta">
            <p class="de-qr-tarjeta__titulo">La carta en tu mesa</p>
            <?php demo_qr('#carta', 'Escanea y mira esta carta en tu celular', 'de-qr'); ?>
            <p>Este código va pegado en cada mesa y en la caja: abre la carta con los precios del día.</p>
          </div>
          <div class="de-carta__fotos">
            <?= demo_img($n['fotos']['jugos'], 'Vaso de jugo natural de mango con bombilla', ['ancho' => 300, 'alto' => 300, 'anchos' => [300, 600], 'sizes' => '130px']) ?>
            <?= demo_img($n['fotos']['flan'], 'Flan casero con caramelo en plato blanco', ['ancho' => 300, 'alto' => 300, 'anchos' => [300, 600], 'sizes' => '130px']) ?>
            <?= demo_img($n['fotos']['empanadas'], 'Empanadas de queso fritas y doradas', ['ancho' => 300, 'alto' => 300, 'anchos' => [300, 600], 'sizes' => '130px']) ?>
          </div>
        </aside>
      </div>
    </div>
  </section>

  <section class="de-seccion de-oficinas" id="oficinas" aria-labelledby="oficinas-titulo">
    <div class="de-envoltura">
      <div class="de-cabeza">
        <p class="de-ante">Para llevar y para la pega</p>
        <h2 id="oficinas-titulo">Colaciones sin hacer fila</h2>
      </div>
      <ul class="de-oficinas__lista">
        <li>
          <span class="de-oficinas__icono" aria-hidden="true"><svg viewBox="0 0 32 32" width="30" height="30" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 11h18l-1.6 15H8.6z"/><path d="M12 11V8a4 4 0 0 1 8 0v3"/></svg></span>
          <h3>Retiro en el local</h3>
          <p>Reserva por WhatsApp antes de las 12:30 y pasa a buscar tu colación en táper, lista y caliente.</p>
        </li>
        <li>
          <span class="de-oficinas__icono" aria-hidden="true"><svg viewBox="0 0 32 32" width="30" height="30" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="23" r="3.5"/><circle cx="24" cy="23" r="3.5"/><path d="M12.5 23h8M5 16h11l3 7M19 12h4l4 7"/><path d="M14 10h5v6"/></svg></span>
          <h3>Despacho en San Miguel</h3>
          <p>$1.500 a cualquier dirección de la comuna. Gratis desde 4 colaciones.</p>
        </li>
        <li class="de-oficinas__empresa">
          <span class="de-oficinas__icono" aria-hidden="true"><svg viewBox="0 0 32 32" width="30" height="30" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="10" width="22" height="15" rx="2"/><path d="M12 10V7h8v3M5 16h22"/></svg></span>
          <h3>¿Almuerzan en la oficina?</h3>
          <p>Desde 6 colaciones, <strong>$5.490 cada una</strong> con despacho gratis. Te mandamos el menú de la semana el lunes temprano.</p>
          <a class="de-btn de-btn--chico de-btn--claro" href="<?= demo_e($wa('Hola, quiero cotizar colaciones para mi oficina en ' . $n['comuna'] . '. Somos __ personas.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Cotizar para mi oficina</span></a>
        </li>
      </ul>
    </div>
  </section>

  <section class="de-seccion de-historia" aria-labelledby="historia-titulo">
    <div class="de-envoltura de-historia__grid">
      <figure class="de-historia__foto de-historia__foto--grande">
        <?= demo_img($n['fotos']['greda'], 'Platos caseros servidos en ollas de greda en el mesón de un restaurante', ['ancho' => 800, 'alto' => 900, 'sizes' => '(max-width: 760px) 90vw, 45vw']) ?>
      </figure>
      <div class="de-historia__texto">
        <p class="de-ante">Desde 1998</p>
        <h2 id="historia-titulo">La olla de greda de la Ernestina</h2>
        <p>Todo partió con una olla de greda de Pomaire y una ventana a la calle: la Ernestina vendía cazuela a los trabajadores de la maestranza del barrio. Hoy somos ocho mesas, la misma olla y su hija en la caja.</p>
        <p>Cocinamos cada mañana con verdura de la feria de la esquina. Cuando se acaba el plato del día, se acaba: por eso conviene reservar.</p>
        <figure class="de-historia__foto de-historia__foto--chica">
          <?= demo_img($n['fotos']['fogon'], 'Olla de greda sobre una cocina a leña encendida', ['ancho' => 500, 'alto' => 400, 'anchos' => [320, 500, 800], 'sizes' => '(max-width: 760px) 70vw, 260px']) ?>
        </figure>
      </div>
    </div>
  </section>

  <section class="de-seccion de-seccion--papel" id="horario" aria-labelledby="horario-titulo">
    <div class="de-envoltura de-horario">
      <div>
        <p class="de-ante">Te esperamos</p>
        <h2 id="horario-titulo">Horario</h2>
        <p class="de-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario de-tabla-horario'); ?>
      </div>
      <ul class="de-pagos">
        <li><strong>Menú del día</strong><span>De lunes a viernes, hasta que se acabe la olla.</span></li>
        <li><strong>Fin de semana</strong><span>Solo a la carta. Los domingos, empanadas de pino.</span></li>
        <li><strong>Pago</strong><span>Efectivo, débito, crédito o transferencia.</span></li>
      </ul>
    </div>
  </section>

  <div class="de-seccion">
    <div class="de-envoltura">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería la ficha de tu picada en Google Maps: horario, cómo llegar y reseñas de tus clientes.']); ?>
    </div>
  </div>

  <section class="de-cierre" aria-labelledby="cierre-titulo">
    <div class="de-envoltura">
      <h2 id="cierre-titulo">¿Te guardamos un plato?</h2>
      <p>Escríbenos antes de la una y tu colación te espera caliente.</p>
      <div class="de-acciones">
        <a class="de-btn de-btn--claro" href="<?= demo_e($waMenu) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Reservar por WhatsApp</span></a>
        <a class="de-btn de-btn--borde" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
      </div>
    </div>
  </section>
</main>

<footer class="de-pie">
  <div class="de-envoltura">
    <p class="de-pie__marca"><?= demo_e($n['nombre']) ?></p>
    <p><?= demo_e($n['comuna']) ?>, Santiago · Menú del día y colaciones por WhatsApp</p>
    <?php demo_aviso_pie($rubro); ?>
  </div>
</footer>

<nav class="de-barra" aria-label="Pedir">
  <a href="<?= demo_e($waMenu) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Reservar</span></a>
  <a href="#carta"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="2" d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"/></svg><span>Carta</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
</nav>
<?php demo_scripts(['qr']); ?>
</body>
</html>
