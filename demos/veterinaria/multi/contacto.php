<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = cp();
$estado = demo_horario_estado($n['horario']);

cp_abrir('contacto', 'Contacto y horario', 'Contacto de ' . $n['nombre'] . ' en ' . $n['comuna'] . ': horario, urgencias sin hora (no 24 horas), visitas a domicilio por comuna, WhatsApp y mapa.');
?>
<main id="contenido">
  <?php cp_titular('Contacto', 'Estamos en ' . $n['comuna'], 'Atendemos con hora reservada y también urgencias sin hora dentro de nuestro horario. Escríbenos o llámanos.'); ?>

  <section class="cp-seccion cp-seccion--sin-arriba">
    <div class="cp-ancho cp-contacto">
      <div class="cp-tarjeta">
        <p class="cp-chip<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <h2>Horario</h2>
        <?php demo_horario_tabla($n['horario'], 'demo-horario cp-horario'); ?>
        <p class="cp-pequeno">El domingo atendemos solo urgencias. Las horas reservadas van de lunes a sábado.</p>
      </div>
      <div class="cp-tarjeta">
        <h2>Escríbenos</h2>
        <p>Para reservar, cambiar una hora o preguntar por un resultado.</p>
        <div class="cp-acciones cp-acciones--columna">
          <a class="cp-btn" href="<?= demo_e(cp_wa('Hola, quiero hacer una consulta a ' . $n['nombre'] . ': ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> WhatsApp</a>
          <a class="cp-btn cp-btn--linea" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel(18) ?> Llamar</a>
        </div>
        <p class="cp-pequeno">Dirección exacta al confirmar tu hora · <?= demo_e($n['comuna']) ?>, Santiago.</p>
      </div>
      <div class="cp-tarjeta cp-tarjeta--lavanda">
        <h2>Visitas a domicilio</h2>
        <p>Para controles, vacunas y mascotas mayores o muy nerviosas. El costo se suma al servicio:</p>
        <dl class="cp-comunas">
          <?php foreach ($n['comunas_domicilio'] as $comuna => $costo): ?>
          <div><dt><?= demo_e($comuna) ?></dt><dd><?= demo_e(demo_clp($costo)) ?></dd></div>
          <?php endforeach; ?>
        </dl>
      </div>
    </div>
  </section>

  <?php cp_urgencias(); ?>

  <div class="cp-seccion">
    <div class="cp-ancho">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Busca «' . $n['nombre'] . '» en Google Maps para ver cómo llegar, el horario y las opiniones de otros pacientes.']); ?>
    </div>
  </div>
</main>
<?php cp_cerrar();
