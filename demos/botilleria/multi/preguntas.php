<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = co();
co_abrir('preguntas', 'Preguntas frecuentes', 'Venta solo a mayores de 18 años, catas, despacho, regalos y medios de pago en ' . $n['nombre'] . '. Demo de botillería en ' . $n['comuna'] . '.');
?>
<main id="contenido">
  <header class="co-titular">
    <div class="co-ancho">
      <p class="co-sobre">Preguntas frecuentes</p>
      <h1>Lo que <em>nos preguntan</em></h1>
      <p>¿No encuentras tu respuesta? Escríbenos por WhatsApp: contestamos de martes a sábado.</p>
    </div>
  </header>

  <section class="co-seccion co-seccion--sin-arriba">
    <div class="co-ancho co-preguntas">
      <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
      <details class="co-pregunta"<?= $i === 0 ? ' open' : '' ?>>
        <summary><span class="co-pregunta__num" aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></span><?= demo_e($pregunta) ?></summary>
        <p><?= demo_e($respuesta) ?></p>
      </details>
      <?php endforeach; ?>
      <p class="co-centro">
        <a class="co-btn" href="<?= demo_e(co_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Preguntar por WhatsApp</a>
      </p>
    </div>
  </section>
</main>
<?php co_cerrar();
