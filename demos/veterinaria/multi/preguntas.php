<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = cp();

cp_abrir('preguntas', 'Preguntas frecuentes', 'Preguntas frecuentes de ' . $n['nombre'] . ': cómo reservar, horario de urgencias (no 24 horas), elegir al médico, visitas a domicilio, qué llevar y formas de pago.');
?>
<main id="contenido">
  <?php cp_titular('Preguntas frecuentes', 'Antes de venir', 'Lo que más nos preguntan por teléfono, respondido de una vez. Si tu duda no está, escríbenos.'); ?>

  <section class="cp-seccion cp-seccion--sin-arriba">
    <div class="cp-ancho cp-preguntas">
      <div class="cp-preguntas__lista">
        <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
        <details class="cp-pregunta"<?= $i === 0 ? ' open' : '' ?>>
          <summary><span class="cp-pregunta__num" aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></span><?= demo_e($pregunta) ?></summary>
          <p><?= demo_e($respuesta) ?></p>
        </details>
        <?php endforeach; ?>
      </div>
      <aside class="cp-preguntas__lado">
        <div class="cp-tarjeta">
          <h2>¿No encontraste tu respuesta?</h2>
          <p>Escríbenos por WhatsApp y te responde alguien del equipo en horario de atención.</p>
          <a class="cp-btn" href="<?= demo_e(cp_wa('Hola, tengo una pregunta para ' . $n['nombre'] . ': ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> Preguntar por WhatsApp</a>
        </div>
        <div class="cp-tarjeta cp-tarjeta--lavanda">
          <h2>Primera visita</h2>
          <ul class="cp-checks">
            <li>Carnet de vacunas y exámenes anteriores.</li>
            <li>Gatos y exóticos, en transportadora cerrada.</li>
            <li>Llega diez minutos antes para abrir su ficha.</li>
          </ul>
        </div>
      </aside>
    </div>
  </section>
</main>
<?php cp_cerrar();
