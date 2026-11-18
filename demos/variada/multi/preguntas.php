<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = dp();
dp_abrir('preguntas', 'Preguntas frecuentes', 'Despacho, retiro, pan, palta, opciones vegetarianas y medios de pago en ' . $n['nombre'] . '. Demo de sanguchería en ' . $n['comuna'] . '.');
?>
<main id="contenido">
  <header class="dp-titular">
    <div class="dp-ancho">
      <p class="dp-mono dp-sobre">Preguntas frecuentes</p>
      <h1>Lo que más nos preguntan</h1>
      <p>¿No está tu respuesta? Escríbenos por WhatsApp y te contestamos al tiro.</p>
    </div>
  </header>

  <section class="dp-seccion dp-seccion--sin-arriba">
    <div class="dp-ancho dp-preguntas">
      <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
      <details class="dp-pregunta"<?= $i === 0 ? ' open' : '' ?>>
        <summary><span class="dp-mono"><?= sprintf('%02d', $i + 1) ?></span><?= demo_e($pregunta) ?></summary>
        <p><?= demo_e($respuesta) ?></p>
      </details>
      <?php endforeach; ?>
      <p class="dp-centro">
        <a class="dp-btn" href="<?= demo_e(dp_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> Preguntar por WhatsApp</a>
      </p>
    </div>
  </section>
</main>
<?php dp_cerrar();
