<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = tn();
tn_abrir('preguntas', 'Preguntas frecuentes', 'Preguntas frecuentes de ' . $n['nombre'] . ', minimarket 24 horas en ' . $n['barrio'] . ': horario, despacho, pedido mínimo, reemplazos y medios de pago.');
?>
<main id="contenido">
  <header class="tn-cabeza-pagina">
    <div class="tn-ancho">
      <p class="tn-sobre">Ayuda</p>
      <h1>Preguntas frecuentes</h1>
      <p>Lo que más nos preguntan por WhatsApp. Si no está aquí, escríbenos: contestamos a cualquier hora.</p>
    </div>
  </header>
  <section class="tn-seccion tn-seccion--sin-arriba">
    <div class="tn-ancho tn-faq">
      <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
      <details class="tn-faq__item"<?= $i === 0 ? ' open' : '' ?>>
        <summary><span class="tn-faq__num" aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></span><?= demo_e($pregunta) ?></summary>
        <p><?= demo_e($respuesta) ?></p>
      </details>
      <?php endforeach; ?>
      <div class="tn-faq__cierre">
        <p>¿Otra duda?</p>
        <a class="tn-btn" href="<?= demo_e(tn_wa('Hola, tengo una pregunta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Preguntar por WhatsApp</span></a>
      </div>
    </div>
  </section>
</main>
<?php tn_cerrar();
