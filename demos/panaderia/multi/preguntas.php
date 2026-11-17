<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = ml();
ml_abrir('preguntas', 'Preguntas frecuentes', 'Encargos, despacho, masa madre, duración del pan y medios de pago en ' . $n['nombre'] . '. Demo de panadería en ' . $n['comuna'] . '.');
?>
<main id="contenido">
  <header class="ml-titular">
    <div class="ml-ancho">
      <p class="ml-etiqueta">Preguntas frecuentes</p>
      <h1>Lo que más <em>nos preguntan</em></h1>
      <p>¿No está tu duda? Escríbenos por WhatsApp y te respondemos entre hornadas.</p>
    </div>
  </header>

  <section class="ml-seccion ml-seccion--sin-arriba">
    <div class="ml-ancho ml-preguntas">
      <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
      <details class="ml-pregunta"<?= $i === 0 ? ' open' : '' ?>>
        <summary><span class="ml-pregunta__num"><?= sprintf('%02d', $i + 1) ?></span><?= demo_e($pregunta) ?></summary>
        <p><?= demo_e($respuesta) ?></p>
      </details>
      <?php endforeach; ?>
      <p class="ml-centro">
        <a class="ml-btn" href="<?= demo_e(ml_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener"><span>Preguntar por WhatsApp</span><?= ml_flecha() ?></a>
      </p>
    </div>
  </section>
</main>
<?php ml_cerrar();
