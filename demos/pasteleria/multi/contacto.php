<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = al();
$estado = demo_horario_estado($n['horario']);
al_abrir('contacto', 'Contacto, horario y preguntas', 'Horario, despacho, encargos, opciones sin gluten y cómo llegar a ' . $n['nombre'] . ' en ' . $n['comuna'] . ', Santiago. Demo de sitio para pastelería.');
al_titular('Contacto', 'Ven al atelier <em>en ' . demo_e($n['comuna']) . '</em>', '<span class="al-estado' . ($estado['abierto'] ? ' is-abierto' : '') . '">' . demo_e($estado['texto']) . '</span>');
?>
<main id="contenido">
  <section class="al-seccion al-seccion--sin-arriba">
    <div class="al-ancho al-contacto">
      <div class="al-tarjeta">
        <h2>Horario</h2>
        <?php demo_horario_tabla($n['horario'], 'demo-horario al-horario'); ?>
        <p class="al-pequeno">Los encargos se retiran dentro del horario del atelier.</p>
      </div>
      <div class="al-tarjeta">
        <h2>Encargos y consultas</h2>
        <p>Encargos, degustaciones y consultas por WhatsApp. Despacho refrigerado en <?= demo_e(implode(', ', array_keys($n['comunas_despacho']))) ?>.</p>
        <div class="al-acciones al-acciones--columna">
          <a class="al-btn" href="<?= demo_e(AL_BASE . 'encargo.php') ?>">Encargar una torta</a>
          <a class="al-btn al-btn--linea" href="<?= demo_e(al_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Escribir por WhatsApp</a>
          <a class="al-btn al-btn--linea" href="<?= demo_e(demo_tel()) ?>">Llamar</a>
        </div>
      </div>
    </div>
  </section>

  <section class="al-seccion al-seccion--rosa" aria-labelledby="preguntas-titulo">
    <div class="al-ancho al-preguntas">
      <header class="al-encabezado">
        <p class="al-sobre">Preguntas frecuentes</p>
        <h2 id="preguntas-titulo">Antes de <em>encargar</em></h2>
      </header>
      <div>
        <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
        <details class="al-pregunta"<?= $i === 0 ? ' open' : '' ?>>
          <summary><?= demo_e($pregunta) ?></summary>
          <p><?= demo_e($respuesta) ?></p>
        </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <div class="al-seccion al-seccion--google">
    <div class="al-ancho">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ]); ?>
    </div>
  </div>
</main>
<?php al_cerrar();
