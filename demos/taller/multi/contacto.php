<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = vg();
$estado = demo_horario_estado($n['horario']);

vg_abrir('contacto', 'Contacto y horario', 'Contacto de ' . $n['nombre'] . ', taller mecánico en ' . $n['comuna'] . ': horario, WhatsApp, teléfono y cómo llegar.');
?>
<main id="contenido">
  <?php vg_titular('Contacto', 'Hablemos de tu auto', 'Reserva en línea, escríbenos por WhatsApp o llámanos. Estamos en ' . $n['comuna'] . ', a pasos de la avenida principal.'); ?>

  <section class="vg-seccion vg-seccion--sin-arriba">
    <div class="vg-ancho vg-contacto">
      <div class="vg-caja">
        <p class="vg-sobre"><span class="vg-led<?= $estado['abierto'] ? ' is-on' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <h2>Horario</h2>
        <?php demo_horario_tabla($n['horario'], 'demo-horario vg-horario'); ?>
        <p class="vg-pequeno">Recepción de autos hasta una hora antes del cierre. Los sábados, solo bloques de la mañana.</p>
      </div>
      <div class="vg-caja">
        <h2>Escríbenos</h2>
        <p>Respondemos en menos de una hora hábil. Si nos mandas la patente y el kilometraje, mejor.</p>
        <div class="vg-acciones vg-acciones--columna">
          <a class="vg-btn" href="<?= demo_e(vg_wa('Hola, quiero consultar en ' . $n['nombre'] . '. Patente y modelo: ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> WhatsApp</a>
          <a class="vg-btn vg-btn--linea" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel(18) ?> Llamar</a>
          <a class="vg-enlace" href="<?= demo_e(VG_BASE . 'servicios.php') ?>">O reserva tu hora en línea</a>
        </div>
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
      ], ['bajada' => 'Busca «' . $n['nombre'] . '» en Google Maps para ver cómo llegar y las opiniones de otros clientes.']); ?>
    </div>
  </div>
</main>
<?php vg_cerrar();
