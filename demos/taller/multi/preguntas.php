<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = vg();

vg_abrir('preguntas', 'Preguntas frecuentes', 'Preguntas frecuentes de ' . $n['nombre'] . ': cómo reservar hora, precios, marcas que atendemos, repuestos, retiro a domicilio y garantía.');
?>
<main id="contenido">
  <?php vg_titular('Preguntas frecuentes', 'Antes de reservar', 'Lo que más nos preguntan por WhatsApp. Si tu duda no está aquí, escríbenos.'); ?>

  <section class="vg-seccion vg-seccion--sin-arriba">
    <div class="vg-ancho vg-preguntas">
      <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
      <details class="vg-pregunta"<?= $i === 0 ? ' open' : '' ?>>
        <summary><span class="vg-mono"><?= sprintf('%02d', $i + 1) ?></span><?= demo_e($pregunta) ?></summary>
        <p><?= demo_e($respuesta) ?></p>
      </details>
      <?php endforeach; ?>
      <div class="vg-preguntas__cierre">
        <p>¿Otra pregunta?</p>
        <a class="vg-btn vg-btn--linea" href="<?= demo_e(vg_wa('Hola, tengo una pregunta para ' . $n['nombre'] . ': ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> Escríbenos</a>
      </div>
    </div>
  </section>
</main>
<?php vg_cerrar();
