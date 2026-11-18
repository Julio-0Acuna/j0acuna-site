<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = mz();
$estado = demo_horario_estado($n['horario']);

mz_abrir('contacto', 'Contacto y horario', 'Contacto de ' . $n['nombre_largo'] . ' en ' . $n['comuna'] . ', Santiago: WhatsApp, teléfono, horario de martes a sábado y ubicación en Google Maps. Demo de sitio web para peluquerías.');
?>
<main id="contenido">
  <?php mz_titular('04', 'Contacto', 'Te esperamos <em>en ' . demo_e($n['comuna']) . '</em>', 'De martes a sábado. Los lunes cerramos para capacitarnos y probar tonos nuevos.'); ?>

  <section class="mz-seccion mz-seccion--sin-arriba" aria-label="Datos de contacto">
    <div class="mz-ancho mz-contacto">
      <div class="mz-contacto__caja">
        <p class="mz-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <h2>Escríbenos o llámanos</h2>
        <p>Respondemos WhatsApp de martes a sábado durante el horario del estudio. Para evaluar un color, envía una foto con luz natural, de frente y de espalda.</p>
        <div class="mz-acciones">
          <a class="mz-btn" href="<?= demo_e(mz_wa('Hola, quiero hacer una consulta en ' . $n['nombre_largo'] . '.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> WhatsApp</a>
          <a class="mz-btn mz-btn--linea" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel(18) ?> Llamar</a>
        </div>
      </div>
      <div class="mz-contacto__caja mz-contacto__caja--horario">
        <h2>Horario</h2>
        <?php demo_horario_tabla($n['horario'], 'demo-horario mz-horario'); ?>
        <p class="mz-pequeno">Los procesos de más de cuatro horas empiezan a las 10:00 o a las 11:30.</p>
      </div>
    </div>
  </section>

  <section class="mz-seccion mz-seccion--papel" aria-label="Encuéntranos en Google">
    <div class="mz-ancho">
      <?php demo_google([
          'nombre' => $n['nombre_largo'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['clase' => 'mz-google']); ?>
    </div>
  </section>
</main>
<?php mz_cerrar();
