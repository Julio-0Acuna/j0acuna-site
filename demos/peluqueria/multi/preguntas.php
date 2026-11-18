<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = mz();

mz_abrir('preguntas', 'Preguntas frecuentes', 'Preguntas frecuentes de ' . $n['nombre_largo'] . ': cuánto demora un balayage, por qué el precio es desde, prueba de mechón, cancelaciones y productos. Demo de peluquería en ' . $n['comuna'] . '.');
?>
<main id="contenido">
  <?php mz_titular('03', 'Preguntas frecuentes', 'Antes de <em>sentarte</em>', 'Lo que más nos preguntan por WhatsApp, respondido con calma. Si te queda una duda, escríbenos.'); ?>

  <section class="mz-seccion mz-seccion--sin-arriba" aria-label="Preguntas">
    <div class="mz-ancho mz-faq">
      <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
      <details class="mz-faq__item"<?= $i === 0 ? ' open' : '' ?>>
        <summary><span class="mz-faq__num" aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></span><?= demo_e($pregunta) ?></summary>
        <p><?= demo_e($respuesta) ?></p>
      </details>
      <?php endforeach; ?>
      <aside class="mz-faq__aparte">
        <p class="mz-sobre">¿Otra duda?</p>
        <p>Mándanos una foto de tu pelo con luz natural y te respondemos qué se puede hacer y cuánto cuesta.</p>
        <a class="mz-btn" href="<?= demo_e(mz_wa('Hola, tengo una duda antes de reservar en ' . $n['nombre_largo'] . ': ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> Preguntar por WhatsApp</a>
      </aside>
    </div>
  </section>
</main>
<?php mz_cerrar();
