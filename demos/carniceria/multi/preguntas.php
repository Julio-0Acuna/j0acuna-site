<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = vt();
vt_abrir('preguntas', 'Preguntas frecuentes', 'Maduración en seco, despacho, grosores a pedido, conservación y medios de pago en ' . $n['nombre'] . '. Demo de carnicería en ' . $n['comuna'] . '.');
?>
<main id="contenido">
  <header class="vt-titular">
    <div class="vt-ancho">
      <p class="vt-mono">Preguntas frecuentes</p>
      <h1>Lo que más <em>nos preguntan</em></h1>
      <p>¿No encuentras tu respuesta? Escríbenos por WhatsApp y te contestamos en el día.</p>
    </div>
  </header>

  <section class="vt-seccion vt-seccion--sin-arriba">
    <div class="vt-ancho vt-preguntas">
      <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
      <details class="vt-pregunta"<?= $i === 0 ? ' open' : '' ?>>
        <summary><span class="vt-mono"><?= sprintf('%02d', $i + 1) ?></span><?= demo_e($pregunta) ?></summary>
        <p><?= demo_e($respuesta) ?></p>
      </details>
      <?php endforeach; ?>
      <p class="vt-centro">
        <a class="vt-btn" href="<?= demo_e(vt_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Preguntar por WhatsApp</a>
      </p>
    </div>
  </section>
</main>
<?php vt_cerrar();
