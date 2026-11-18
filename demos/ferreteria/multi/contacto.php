<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = pg();
$estado = demo_horario_estado($n['horario']);

pg_abrir('contacto', 'Contacto y preguntas', 'Contacto de ' . $n['nombre'] . ', tienda de pinturas en ' . $n['comuna'] . ': horario, WhatsApp, despacho por comuna, preguntas frecuentes y ubicación en Google Maps.');
?>
<main id="contenido">
  <?php pg_titular('Contacto', 'Pasa a ver los colores <em>en persona</em>', 'La carta física, las muestras pintadas y la máquina de teñir están en la tienda. También cotizamos por WhatsApp y despachamos en siete comunas.', ['PG-301', 'PG-302', 'PG-303', 'PG-304', 'PG-101']); ?>

  <section class="pg-seccion pg-seccion--sin-arriba">
    <div class="pg-ancho pg-contacto">
      <div class="pg-tarjeta">
        <h2>Escríbenos</h2>
        <p class="pg-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario pg-horario'); ?>
        <div class="pg-acciones">
          <a class="pg-btn" href="<?= demo_e(pg_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> WhatsApp</a>
          <a class="pg-btn pg-btn--linea" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel(18) ?> Llamar</a>
        </div>
      </div>
      <div class="pg-tarjeta">
        <h2>Despacho</h2>
        <p>Mismo día si cotizas antes de las 14:00. Las tinetas te las dejamos adentro.</p>
        <dl class="pg-despacho">
          <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
          <div><dt><?= demo_e($comuna) ?></dt><dd><?= demo_e(demo_clp($costo)) ?></dd></div>
          <?php endforeach; ?>
        </dl>
      </div>
    </div>
  </section>

  <section class="pg-seccion pg-seccion--papel" aria-labelledby="faq-titulo">
    <div class="pg-ancho pg-faq">
      <div>
        <p class="pg-sobre">Preguntas frecuentes</p>
        <h2 id="faq-titulo">Lo que más <em>nos preguntan</em></h2>
      </div>
      <div class="pg-faq__lista">
        <?php foreach ($n['preguntas'] as [$pregunta, $respuesta]): ?>
        <details class="pg-pregunta">
          <summary><?= demo_e($pregunta) ?></summary>
          <p><?= demo_e($respuesta) ?></p>
        </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <div class="pg-seccion">
    <div class="pg-ancho">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Busca «' . $n['nombre'] . '» en Google Maps: horario, cómo llegar y opiniones de clientes.']); ?>
    </div>
  </div>
</main>
<?php pg_cerrar();
