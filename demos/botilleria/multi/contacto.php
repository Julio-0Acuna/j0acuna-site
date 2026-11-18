<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = co();
$estado = demo_horario_estado($n['horario']);
co_abrir('contacto', 'Contacto y horario', 'Horario, despacho, catas y cómo llegar a ' . $n['nombre'] . ' en ' . $n['comuna'] . ', Santiago. Demo de sitio para botillería y vinoteca.');
?>
<main id="contenido">
  <header class="co-titular">
    <div class="co-ancho">
      <p class="co-sobre">Contacto</p>
      <h1>Pasa <em>a probar</em></h1>
      <p class="co-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><?= demo_e($estado['texto']) ?></p>
    </div>
  </header>

  <section class="co-seccion co-seccion--sin-arriba">
    <div class="co-ancho co-contacto">
      <div class="co-tarjeta">
        <h2>Horario de la tienda</h2>
        <?php demo_horario_tabla($n['horario']); ?>
        <p class="co-pequeno">Los jueves cerramos más tarde por la cata semanal. Domingos cerrado.</p>
      </div>
      <div class="co-tarjeta">
        <h2>Despacho</h2>
        <ul class="co-despacho">
          <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
          <li><span><?= demo_e($comuna) ?></span><strong><?= demo_e(demo_clp($costo)) ?></strong></li>
          <?php endforeach; ?>
        </ul>
        <p class="co-pequeno">Al día siguiente hábil. Se verifica la edad de quien recibe.</p>
      </div>
      <div class="co-tarjeta co-tarjeta--tinto">
        <h2>Escríbenos</h2>
        <p>Pedidos, regalos, catas privadas y recomendaciones por WhatsApp.</p>
        <div class="co-acciones co-acciones--columna">
          <a class="co-btn co-btn--ocre" href="<?= demo_e(co_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">WhatsApp</a>
          <a class="co-btn co-btn--linea" href="<?= demo_e(demo_tel()) ?>">Llamar</a>
          <a class="co-btn co-btn--linea" href="<?= demo_e(CO_BASE . 'catas.php') ?>">Reservar una cata</a>
        </div>
      </div>
    </div>
  </section>

  <div class="co-seccion co-papel">
    <div class="co-ancho"><?php co_google(); ?></div>
  </div>
</main>
<?php co_cerrar();
