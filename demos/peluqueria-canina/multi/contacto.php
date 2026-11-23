<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = br();
$estado = demo_horario_estado($n['horario']);

br_abrir('contacto', 'Contacto y horario', 'Horario, traslado por comuna y contacto de ' . $n['nombre'] . ' en ' . $n['comuna'] . ', Santiago. Reserva por WhatsApp o por teléfono. Demo de spa canino.');
?>
<main id="contenido">
  <?php br_titular('05', 'Contacto', 'Te esperamos <em>en ' . $n['comuna'] . '</em>', 'Reserva en la web o escríbenos. Si prefieres que lo busquemos en tu casa, el traslado ida y vuelta tiene un valor fijo por comuna.'); ?>

  <section class="br-seccion br-seccion--sin-arriba" aria-label="Horario y contacto">
    <div class="br-ancho br-contacto">
      <div class="br-tarjeta">
        <p class="br-sobre">Horario</p>
        <p class="br-estado"><span class="br-estado__punto<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario br-horario'); ?>
        <p class="br-pequeno">Jueves en la mañana: solo gatos. Lunes y domingo cerramos para descansar.</p>
      </div>
      <div class="br-tarjeta">
        <p class="br-sobre">Traslado ida y vuelta</p>
        <dl class="br-comunas br-comunas--oscuras">
          <?php foreach ($n['traslado'] as $comuna => $costo): ?>
          <div><dt><?= demo_e($comuna) ?></dt><dd><?= demo_e(demo_clp($costo)) ?></dd></div>
          <?php endforeach; ?>
        </dl>
        <p class="br-pequeno">Gratis en reservas sobre <?= demo_e(demo_clp($n['traslado_gratis'])) ?>.</p>
      </div>
      <div class="br-tarjeta br-tarjeta--pizarra">
        <p class="br-sobre br-sobre--claro">Escríbenos</p>
        <p>Respondemos por WhatsApp en el horario del spa. Para reservar, lo más rápido es armar tu reserva en la web.</p>
        <div class="br-acciones br-acciones--columna">
          <a class="br-btn br-btn--durazno" href="<?= demo_e(br_wa('Hola, quiero reservar en ' . $n['nombre'] . '. Mi perro se llama: ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> WhatsApp</a>
          <a class="br-btn br-btn--linea-clara" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel(18) ?> Llamar</a>
        </div>
      </div>
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
      ], ['id' => 'google', 'bajada' => 'Busca «' . $n['nombre'] . '» en Google Maps para ver cómo llegar y dejarnos tu reseña.']); ?>
    </div>
  </div>
</main>
<?php br_cerrar();
