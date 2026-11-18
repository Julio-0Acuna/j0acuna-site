<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = vg();
$firmas = array_filter($n['servicios'], static fn (array $s): bool => $s['firma']);
$estado = demo_horario_estado($n['horario']);

vg_abrir('inicio', 'Taller mecánico en ' . $n['comuna'], 'Demo de sitio de varias páginas para un taller mecánico especializado en ' . $n['comuna'] . ', Santiago: servicios con precio desde, reserva de hora con patente y modelo, y retiro del auto a domicilio.');
?>
<main id="contenido">
  <section class="vg-hero" aria-labelledby="hero-titulo">
    <div class="vg-hero__foto">
      <?= demo_img($n['fotos']['hero'], 'Disco de freno y caliper de un auto iluminados en un taller oscuro', ['lazy' => false, 'ancho' => 1400, 'alto' => 900, 'sizes' => '100vw', 'anchos' => [800, 1400, 2000]]) ?>
    </div>
    <div class="vg-ancho vg-hero__contenido">
      <p class="vg-sobre"><span class="vg-led<?= $estado['abierto'] ? ' is-on' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
      <h1 id="hero-titulo">Precisión que se nota al manejar.</h1>
      <p class="vg-hero__bajada">Taller especializado en <?= demo_e($n['comuna']) ?>: diagnóstico computarizado, frenos, suspensión y mantención según pauta, con informe y fotos de cada trabajo. Reserva tu hora en línea o deja que retiremos el auto en tu casa.</p>
      <div class="vg-acciones">
        <a class="vg-btn" href="<?= demo_e(VG_BASE . 'servicios.php') ?>">Reservar hora</a>
        <a class="vg-btn vg-btn--linea" href="<?= demo_e(VG_BASE . 'taller.php') ?>">Conocer el taller</a>
      </div>
      <dl class="vg-lecturas" aria-label="En cifras">
        <div><dt>Garantía</dt><dd>6 meses · 10.000 km</dd></div>
        <div><dt>Diagnóstico</dt><dd>Escáner multimarca</dd></div>
        <div><dt>Retiro</dt><dd>En tu casa u oficina</dd></div>
      </dl>
    </div>
  </section>

  <section class="vg-seccion" aria-labelledby="servicios-titulo">
    <div class="vg-ancho">
      <header class="vg-encabezado vg-encabezado--fila">
        <div>
          <p class="vg-sobre">Servicios</p>
          <h2 id="servicios-titulo">Lo que más reservan</h2>
        </div>
        <a class="vg-enlace" href="<?= demo_e(VG_BASE . 'servicios.php') ?>">Ver los <?= count($n['servicios']) ?> servicios</a>
      </header>
      <div class="vg-tarjetas">
        <?php foreach ($firmas as $id => $s): ?>
        <article class="vg-tarjeta">
          <a class="vg-tarjeta__foto" href="<?= demo_e(VG_BASE . 'servicios.php#s-' . $id) ?>" tabindex="-1" aria-hidden="true">
            <?= demo_img($s['foto'], '', ['ancho' => 600, 'alto' => 450, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 90vw, (max-width: 1100px) 45vw, 25vw']) ?>
          </a>
          <div class="vg-tarjeta__cuerpo">
            <p class="vg-meta"><span><?= demo_e($n['categorias'][$s['cat']][0]) ?></span><span class="vg-dur"><?= demo_e($s['duracion']) ?></span></p>
            <h3><a href="<?= demo_e(VG_BASE . 'servicios.php#s-' . $id) ?>"><?= demo_e($s['nombre']) ?></a></h3>
            <p><?= demo_e($s['desc']) ?></p>
            <p class="vg-precio"><small>desde</small> <?= demo_e(demo_clp(vg_desde($s))) ?></p>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="vg-seccion vg-seccion--panel" aria-labelledby="pasos-titulo">
    <div class="vg-ancho">
      <header class="vg-encabezado">
        <p class="vg-sobre">Reserva en línea</p>
        <h2 id="pasos-titulo">Tres pasos y listo</h2>
      </header>
      <ol class="vg-pasos">
        <li><span class="vg-pasos__num">01</span><h3>Elige los servicios</h3><p>Agrega lo que necesitas según el tipo de vehículo. Ves el precio desde el primer momento.</p></li>
        <li><span class="vg-pasos__num">02</span><h3>Datos del auto y hora</h3><p>Patente, modelo, año y kilometraje, más el día y el bloque que te acomodan.</p></li>
        <li><span class="vg-pasos__num">03</span><h3>Confirmamos por WhatsApp</h3><p>Te escribimos con la hora confirmada y, si quieres, coordinamos el retiro en tu casa.</p></li>
      </ol>
      <p class="vg-centro"><a class="vg-btn" href="<?= demo_e(VG_BASE . 'servicios.php') ?>">Empezar mi reserva</a></p>
    </div>
  </section>

  <section class="vg-seccion" aria-labelledby="retiro-titulo">
    <div class="vg-ancho vg-dos">
      <figure class="vg-marco">
        <?= demo_img($n['fotos']['llaves'], 'Llave puesta en el contacto de un auto, en penumbra', ['ancho' => 800, 'alto' => 640, 'sizes' => '(max-width: 860px) 90vw, 45vw']) ?>
      </figure>
      <div>
        <p class="vg-sobre">Retiro y entrega</p>
        <h2 id="retiro-titulo">No pierdas la mañana en el taller</h2>
        <p>Un chofer con seguro retira tu auto en tu casa u oficina y te lo devuelve el mismo día, lavado por fuera. Mientras tanto, te mandamos por WhatsApp el informe con fotos y el presupuesto de cualquier extra.</p>
        <ul class="vg-lista">
          <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
          <li><span><?= demo_e($comuna) ?></span><span class="vg-mono"><?= demo_e(demo_clp($costo)) ?></span></li>
          <?php endforeach; ?>
        </ul>
        <p class="vg-pequeno">Valor ida y vuelta. Lo eliges al completar la reserva.</p>
      </div>
    </div>
  </section>

  <div class="vg-seccion vg-seccion--panel">
    <div class="vg-ancho">
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

  <section class="vg-banda" aria-labelledby="banda-titulo">
    <?= demo_img($n['fotos']['faros'], '', ['ancho' => 1400, 'alto' => 700, 'sizes' => '100vw', 'anchos' => [800, 1400], 'clase' => 'vg-banda__foto']) ?>
    <div class="vg-ancho vg-banda__texto">
      <h2 id="banda-titulo">¿Una luz en el tablero?</h2>
      <p>Lee la falla con nosotros antes de que se vuelva cara. El diagnóstico se descuenta si reparas aquí.</p>
      <div class="vg-acciones">
        <a class="vg-btn" href="<?= demo_e(VG_BASE . 'servicios.php#s-scanner') ?>">Reservar diagnóstico</a>
        <a class="vg-btn vg-btn--linea" href="<?= demo_e(vg_wa('Hola, se me prendió una luz en el tablero. Patente y modelo: ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> Consultar</a>
      </div>
    </div>
  </section>
</main>
<?php vg_cerrar();
