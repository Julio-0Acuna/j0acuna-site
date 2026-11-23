<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = br();

br_abrir('preguntas', 'Preguntas frecuentes', 'Preguntas frecuentes de ' . $n['nombre'] . ': secado sin jaulas, traslado, perros nerviosos, deslanado sin rapar, gatos y formas de pago. Demo de spa canino en ' . $n['comuna'] . '.');
?>
<main id="contenido">
  <?php br_titular('04', 'Preguntas', 'Lo que <em>siempre nos preguntan</em>', 'Si te queda alguna duda, escríbenos por WhatsApp: respondemos de martes a sábado en el horario del spa.'); ?>

  <section class="br-seccion br-seccion--sin-arriba" aria-label="Preguntas frecuentes">
    <div class="br-ancho br-faq">
      <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
      <details class="br-faq__item"<?= $i === 0 ? ' open' : '' ?>>
        <summary><span class="br-faq__num" aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></span><span><?= demo_e($pregunta) ?></span></summary>
        <p><?= demo_e($respuesta) ?></p>
      </details>
      <?php endforeach; ?>
      <aside class="br-faq__mas">
        <p>¿No está tu pregunta?</p>
        <a class="br-btn br-btn--linea" href="<?= demo_e(br_wa('Hola, tengo una pregunta sobre ' . $n['nombre'] . ': ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> Pregúntanos por WhatsApp</a>
      </aside>
    </div>
  </section>
</main>
<?php br_cerrar();
