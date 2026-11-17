<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = vt();
$estado = demo_horario_estado($n['horario']);
vt_abrir('contacto', 'Contacto y horario', 'Horario, despacho, catas y cómo llegar a ' . $n['nombre'] . ' en ' . $n['comuna'] . ', Santiago. Demo de sitio para carnicería.');
?>
<main id="contenido">
  <header class="vt-titular">
    <div class="vt-ancho">
      <p class="vt-mono">Contacto</p>
      <h1>Ven a ver <em>la cámara</em></h1>
      <p class="vt-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><?= demo_e($estado['texto']) ?></p>
    </div>
  </header>

  <section class="vt-seccion vt-seccion--sin-arriba">
    <div class="vt-ancho vt-contacto">
      <div class="vt-tarjeta">
        <h2>Horario</h2>
        <?php demo_horario_tabla($n['horario']); ?>
        <p class="vt-pequeno">Pedidos en línea las 24 horas. Despacho de martes a domingo.</p>
      </div>
      <div class="vt-tarjeta">
        <h2>Despacho</h2>
        <dl class="vt-comunas">
          <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
          <div><dt><?= demo_e($comuna) ?></dt><dd><?= demo_e(demo_clp($costo)) ?></dd></div>
          <?php endforeach; ?>
        </dl>
        <p class="vt-pequeno">Mismo día si pides antes de las 13:00. Envasado al vacío y refrigerado.</p>
      </div>
      <div class="vt-tarjeta">
        <h2>Pedidos y catas</h2>
        <p>Pedidos, grosores especiales, cajas para eventos y reservas de cata por WhatsApp.</p>
        <div class="vt-acciones vt-acciones--columna">
          <a class="vt-btn" href="<?= demo_e(vt_wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Escribir por WhatsApp</a>
          <a class="vt-btn vt-btn--linea" href="<?= demo_e(demo_tel()) ?>">Llamar</a>
          <a class="vt-btn vt-btn--linea" href="<?= demo_e(VT_BASE . 'cortes.php') ?>">Ver la carta</a>
        </div>
      </div>
    </div>
  </section>

  <div class="vt-seccion vt-hueso">
    <div class="vt-ancho">
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
<?php vt_cerrar();
