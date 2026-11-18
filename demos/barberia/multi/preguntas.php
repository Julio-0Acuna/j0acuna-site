<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = eg();

eg_abrir('preguntas', 'Preguntas frecuentes', 'Preguntas frecuentes de ' . $n['nombre'] . ': reservas, elección de barbero, atrasos, planes de socio, bebidas de cortesía y formas de pago.');
?>
<main id="contenido">
  <?php eg_titular('Preguntas', 'Antes de sentarse', 'Lo que más nos preguntan por WhatsApp. Si su duda no está aquí, escríbanos.'); ?>
  <section class="eg-seccion eg-seccion--sin-arriba">
    <div class="eg-ancho eg-preguntas">
      <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
      <details class="eg-pregunta"<?= $i === 0 ? ' open' : '' ?>>
        <summary><?= demo_e($pregunta) ?></summary>
        <p><?= demo_e($respuesta) ?></p>
      </details>
      <?php endforeach; ?>
      <p class="eg-centro"><a class="eg-btn" href="<?= demo_e(eg_wa('Hola, tengo una pregunta para ' . $n['nombre'] . ': ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> Preguntar por WhatsApp</a></p>
    </div>
  </section>
</main>
<?php eg_cerrar();
