<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = ku();
ku_abrir('preguntas', 'Preguntas frecuentes', 'Reservas, omakase, alergias, opciones sin gluten, pedidos para llevar y medios de pago en ' . $n['nombre'] . '. Demo de restaurante de sushi en ' . $n['comuna'] . '.');
?>
<main id="contenido">
  <?php ku_titular('Preguntas frecuentes', 'Antes de <em>sentarte</em>', '¿No encuentras tu respuesta? Escríbenos por WhatsApp y te respondemos el mismo día.', '問'); ?>

  <section class="ku-seccion ku-seccion--sin-arriba">
    <div class="ku-ancho ku-preguntas">
      <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
      <details class="ku-pregunta"<?= $i === 0 ? ' open' : '' ?>>
        <summary><span class="ku-pregunta__num" aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></span><?= demo_e($pregunta) ?></summary>
        <p><?= demo_e($respuesta) ?></p>
      </details>
      <?php endforeach; ?>
      <p class="ku-centro">
        <a class="ku-btn" href="<?= demo_e(ku_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener"><span>Preguntar por WhatsApp</span></a>
      </p>
    </div>
  </section>
</main>
<?php ku_cerrar();
