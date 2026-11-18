<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = cb();
$fotos = [$n['fotos']['pastillas'], $n['fotos']['disco_gastado'], $n['fotos']['espirales'], $n['categorias']['direccion'][2], $n['categorias']['liquidos'][2]];
$alts = ['Disco y caliper con pastillas gastadas', 'Disco de freno perforado con grietas', 'Espirales rojos de suspensión', 'Suspensión y dirección de una rueda delantera', 'Gotas de líquido dorado en primer plano'];

cb_abrir('guia', 'Guía de desgaste de frenos y suspensión', 'Cuándo cambiar pastillas, discos, amortiguadores, rótulas y líquido de frenos: medidas mínimas y señales de desgaste explicadas por ' . $n['nombre_largo'] . '.');
?>
<main id="contenido">
  <?php cb_titular('Guía de desgaste', '¿Cuándo se cambia cada pieza?', 'Medidas y señales que revisamos en el mesón. Úsala para saber si te toca cambiar, no para reemplazar la revisión de un mecánico.'); ?>

  <section class="cb-seccion cb-seccion--sin-arriba">
    <div class="cb-ancho">
      <ol class="cb-guia">
        <?php foreach ($n['guia'] as $i => [$titulo, $medida, $texto]): ?>
        <li class="cb-guia__item">
          <div class="cb-guia__foto"><?= demo_img($fotos[$i], $alts[$i], ['ancho' => 600, 'alto' => 450, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 760px) 92vw, 320px']) ?></div>
          <div class="cb-guia__texto">
            <p class="cb-guia__num"><?= sprintf('%02d', $i + 1) ?></p>
            <h2><?= demo_e($titulo) ?></h2>
            <p class="cb-guia__medida"><span>Límite</span> <?= demo_e($medida) ?></p>
            <p><?= demo_e($texto) ?></p>
          </div>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section class="cb-seccion cb-seccion--blanca" id="preguntas" aria-labelledby="preguntas-titulo">
    <div class="cb-ancho cb-preguntas">
      <div>
        <p class="cb-sobre">Preguntas frecuentes</p>
        <h2 id="preguntas-titulo">Lo que más nos preguntan en el mesón</h2>
        <p>¿Te queda una duda? <a href="<?= demo_e(cb_wa('Hola, tengo una pregunta sobre frenos o suspensión. Mi patente es: ')) ?>" target="_blank" rel="noopener">Escríbenos con tu patente</a>.</p>
      </div>
      <div class="cb-acordeon">
        <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
        <details<?= $i === 0 ? ' open' : '' ?>>
          <summary><?= demo_e($pregunta) ?></summary>
          <p><?= demo_e($respuesta) ?></p>
        </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>
<?php cb_cerrar();
