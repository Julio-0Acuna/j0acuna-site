<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = fo();
fo_abrir('preguntas', 'Preguntas frecuentes', 'Despacho, retiro, reservas, opciones vegetarianas y medios de pago en ' . $n['nombre'] . '. Demo de pizzería en ' . $n['comuna'] . '.');
?>
<main id="contenido">
  <header class="fo-titular">
    <div class="fo-ancho">
      <p class="fo-sobre">Preguntas frecuentes</p>
      <h1>Lo que más <em>nos preguntan</em></h1>
      <p>¿No encuentras tu respuesta? Escríbenos por WhatsApp y te contestamos al tiro.</p>
    </div>
  </header>

  <section class="fo-seccion fo-seccion--sin-arriba">
    <div class="fo-ancho fo-preguntas">
      <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
      <details class="fo-pregunta"<?= $i === 0 ? ' open' : '' ?>>
        <summary><?= demo_e($pregunta) ?></summary>
        <p><?= demo_e($respuesta) ?></p>
      </details>
      <?php endforeach; ?>
      <p class="fo-centro">
        <a class="fo-btn" href="<?= demo_e(fo_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Preguntar por WhatsApp</a>
      </p>
    </div>
  </section>
</main>
<?php fo_cerrar();
