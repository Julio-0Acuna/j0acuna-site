<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = ku();
$estado = demo_horario_estado($n['horario']);
ku_abrir('contacto', 'Reservas, horario y contacto', 'Reservas en la barra, horario, pedidos para llevar y cómo llegar a ' . $n['nombre'] . ' en ' . $n['comuna'] . ', Santiago. Demo de sitio para restaurante de sushi.');
?>
<main id="contenido">
  <?php ku_titular('Reservas', 'Te guardamos <em>un asiento</em>', '<span class="ku-estado ' . ($estado['abierto'] ? 'is-abierto' : '') . '">' . demo_e($estado['texto']) . '</span>', '約'); ?>

  <section class="ku-seccion ku-seccion--sin-arriba">
    <div class="ku-ancho ku-contacto">
      <div class="ku-tarjeta">
        <h2>Reservar en la barra</h2>
        <p>Escríbenos por WhatsApp con el día, el turno (19:30 o 21:30) y cuántas personas son. Te confirmamos y te preguntamos por alergias.</p>
        <div class="ku-acciones ku-acciones--columna">
          <a class="ku-btn" href="<?= demo_e(ku_wa_reserva()) ?>" target="_blank" rel="noopener"><span>Reservar por WhatsApp</span></a>
          <a class="ku-btn ku-btn--linea" href="<?= demo_e(demo_tel()) ?>"><span>Llamar</span></a>
        </div>
      </div>
      <div class="ku-tarjeta">
        <h2>Horario</h2>
        <?php demo_horario_tabla($n['horario'], 'demo-horario ku-horario'); ?>
        <p class="ku-pequeno">La cocina recibe el último pedido para llevar 45 minutos antes del cierre.</p>
      </div>
      <div class="ku-tarjeta">
        <h2>Para llevar</h2>
        <p>Retiro en el local o despacho en <?= demo_e(implode(', ', array_keys($n['comunas_despacho']))) ?>.</p>
        <a class="ku-btn ku-btn--linea" href="<?= demo_e(KU_BASE . 'menu.php') ?>"><span>Armar mi pedido</span></a>
      </div>
    </div>
  </section>

  <div class="ku-seccion ku-claro">
    <div class="ku-ancho">
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
<?php ku_cerrar();
