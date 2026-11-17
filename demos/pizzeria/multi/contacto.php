<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = fo();
$estado = demo_horario_estado($n['horario']);
fo_abrir('contacto', 'Contacto y horario', 'Horario, reservas, despacho y cómo llegar a ' . $n['nombre'] . ' en ' . $n['comuna'] . ', Santiago. Demo de sitio para pizzería.');
?>
<main id="contenido">
  <header class="fo-titular">
    <div class="fo-ancho">
      <p class="fo-sobre">Contacto</p>
      <h1>Ven a vernos <em>a <?= demo_e($n['comuna']) ?></em></h1>
      <p class="fo-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><?= demo_e($estado['texto']) ?></p>
    </div>
  </header>

  <section class="fo-seccion fo-seccion--sin-arriba">
    <div class="fo-ancho fo-contacto">
      <div class="fo-tarjeta">
        <h2>Horario</h2>
        <?php demo_horario_tabla($n['horario']); ?>
        <p class="fo-pequeno">Cocina abierta hasta 30 minutos antes del cierre.</p>
      </div>
      <div class="fo-tarjeta">
        <h2>Reservas y pedidos</h2>
        <p>Reservas, pedidos para llevar y consultas por WhatsApp. Despacho en <?= demo_e(implode(', ', array_keys($n['comunas_despacho']))) ?>.</p>
        <div class="fo-acciones fo-acciones--columna">
          <a class="fo-btn" href="<?= demo_e(fo_wa('Hola, quiero reservar una mesa en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Reservar por WhatsApp</a>
          <a class="fo-btn fo-btn--linea" href="<?= demo_e(demo_tel()) ?>">Llamar</a>
          <a class="fo-btn fo-btn--linea" href="<?= demo_e(FO_BASE . 'menu.php') ?>">Pedir para llevar</a>
        </div>
      </div>
    </div>
  </section>

  <div class="fo-seccion fo-marfil">
    <div class="fo-ancho">
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
<?php fo_cerrar();
