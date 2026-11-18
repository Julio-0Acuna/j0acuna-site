<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = vg();

vg_abrir('taller', 'El taller', 'Conoce ' . $n['nombre'] . ': taller mecánico especializado en ' . $n['comuna'] . ' con escáner multimarca, alineadora 3D, informe con fotos y garantía de seis meses.');
?>
<main id="contenido">
  <?php vg_titular('El taller', 'Mecánica con método', 'Ningún auto sale de Vértice sin su informe: qué revisamos, qué cambiamos, qué viene en la próxima visita y fotos de todo.'); ?>

  <section class="vg-seccion vg-seccion--sin-arriba" aria-labelledby="historia-titulo">
    <div class="vg-ancho vg-dos">
      <figure class="vg-marco">
        <?= demo_img($n['fotos']['taller'], 'Interior del taller con mesones, herramientas y luz cenital', ['ancho' => 900, 'alto' => 700, 'sizes' => '(max-width: 860px) 90vw, 45vw']) ?>
      </figure>
      <div>
        <p class="vg-sobre">Desde 2014</p>
        <h2 id="historia-titulo">Nacimos de una pregunta simple</h2>
        <p>¿Por qué llevar el auto al taller tiene que ser una caja negra? Vértice partió con dos técnicos que venían de concesionarios y querían trabajar distinto: con diagnóstico antes que intuición, con precios a la vista y explicando cada cambio.</p>
        <p>Hoy somos siete personas, cuatro elevadores y una regla que no cambia: si no te podemos mostrar por qué hay que cambiar una pieza, no la cambiamos.</p>
      </div>
    </div>
  </section>

  <section class="vg-seccion vg-seccion--panel" aria-labelledby="equipo-titulo">
    <div class="vg-ancho">
      <header class="vg-encabezado">
        <p class="vg-sobre">Equipamiento</p>
        <h2 id="equipo-titulo">Herramientas de concesionario, trato de taller de confianza</h2>
      </header>
      <ul class="vg-equipos">
        <?php foreach ($n['equipamiento'] as $i => [$titulo, $texto]): ?>
        <li><span class="vg-mono"><?= sprintf('%02d', $i + 1) ?></span><h3><?= demo_e($titulo) ?></h3><p><?= demo_e($texto) ?></p></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="vg-seccion" aria-labelledby="informe-titulo">
    <div class="vg-ancho vg-dos vg-dos--invertido">
      <figure class="vg-marco">
        <?= demo_img($n['fotos']['mecanico'], 'Mecánico concentrado trabajando en el motor de un auto', ['ancho' => 900, 'alto' => 700, 'sizes' => '(max-width: 860px) 90vw, 45vw']) ?>
      </figure>
      <div>
        <p class="vg-sobre">El informe</p>
        <h2 id="informe-titulo">Lo que recibes con cada servicio</h2>
        <ul class="vg-checks">
          <li>Revisión de 40 puntos: frenos, suspensión, luces, fluidos, neumáticos y batería.</li>
          <li>Fotos de las piezas cambiadas y de lo que conviene vigilar.</li>
          <li>Lectura del escáner con las fallas guardadas, aunque no se vean en el tablero.</li>
          <li>Próxima mantención recomendada, en kilómetros y en fecha.</li>
          <li>Garantía de seis meses o 10.000 km en mano de obra y repuestos.</li>
        </ul>
        <a class="vg-btn" href="<?= demo_e(VG_BASE . 'servicios.php') ?>">Reservar una hora</a>
      </div>
    </div>
  </section>
</main>
<?php vg_cerrar();
