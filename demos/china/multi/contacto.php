<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = rp();
$estado = demo_horario_estado($n['horario']);
rp_abrir('contacto', 'Contacto, horario y preguntas', 'Horario, reservas, despacho, preguntas frecuentes y cómo llegar a ' . $n['nombre'] . ' en ' . $n['comuna'] . ', Santiago. Demo de sitio para restaurante cantonés.');
?>
<main id="contenido">
  <?php rp_titular('Contacto', 'Te esperamos <em>en ' . demo_e($n['comuna']) . '</em>', '', '聯絡'); ?>

  <section class="rp-seccion rp-seccion--sin-arriba">
    <div class="rp-ancho rp-contacto">
      <div class="rp-tarjeta">
        <h2>Horario</h2>
        <p class="rp-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario rp-horario'); ?>
        <p class="rp-pequeno">Yum cha sábado y domingo de 11:30 a 16:00. La cocina cierra 45 minutos antes.</p>
      </div>
      <div class="rp-tarjeta">
        <h2>Reservas y pedidos</h2>
        <p>Reserva en la web o escríbenos. Despacho propio en <?= demo_e(implode(', ', array_keys($n['comunas_despacho']))) ?>.</p>
        <div class="rp-acciones rp-acciones--columna">
          <a class="rp-btn" href="<?= demo_e(RP_BASE . 'reservas.php') ?>">Reservar mesa</a>
          <a class="rp-btn rp-btn--linea" href="<?= demo_e(rp_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Escribir por WhatsApp</a>
          <a class="rp-btn rp-btn--linea" href="<?= demo_e(demo_tel()) ?>">Llamar</a>
        </div>
      </div>
    </div>
  </section>

  <section class="rp-seccion rp-porcelana" id="preguntas" aria-labelledby="preguntas-titulo">
    <div class="rp-ancho rp-preguntas">
      <header class="rp-encabezado">
        <p class="rp-sobre">Preguntas frecuentes</p>
        <h2 id="preguntas-titulo">Lo que más <em>nos preguntan</em></h2>
      </header>
      <div>
        <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
        <details class="rp-pregunta"<?= $i === 0 ? ' open' : '' ?>>
          <summary><?= demo_e($pregunta) ?></summary>
          <p><?= demo_e($respuesta) ?></p>
        </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <div class="rp-seccion">
    <div class="rp-ancho">
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
<?php rp_cerrar();
