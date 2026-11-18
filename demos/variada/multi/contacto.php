<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = dp();
$estado = demo_horario_estado($n['horario']);
dp_abrir('contacto', 'Contacto y horario', 'Horario, despacho y cómo llegar a ' . $n['nombre'] . ' en ' . $n['comuna'] . ', Santiago. Demo de sitio para sanguchería.');
?>
<main id="contenido">
  <header class="dp-titular">
    <div class="dp-ancho">
      <p class="dp-mono dp-sobre">Contacto</p>
      <h1>Pasa a la barra</h1>
      <p class="dp-estado"><span class="dp-punto <?= $estado['abierto'] ? 'is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
    </div>
  </header>

  <section class="dp-seccion dp-seccion--sin-arriba">
    <div class="dp-ancho dp-contacto">
      <div class="dp-tarjeta">
        <h2>Horario de plancha</h2>
        <?php demo_horario_tabla($n['horario']); ?>
        <p class="dp-pequeno">La plancha se apaga 20 minutos antes del cierre.</p>
      </div>
      <div class="dp-tarjeta">
        <h2>Pedidos y despacho</h2>
        <p>Pedidos para llevar y consultas por WhatsApp. Despacho en <?= demo_e(implode(', ', array_keys($n['comunas_despacho']))) ?>.</p>
        <div class="dp-acciones dp-acciones--columna">
          <a class="dp-btn" href="<?= demo_e(DP_BASE . 'carta.php') ?>">Armar una comanda</a>
          <a class="dp-btn dp-btn--linea" href="<?= demo_e(dp_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> Escribir por WhatsApp</a>
          <a class="dp-btn dp-btn--linea" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel(18) ?> Llamar</a>
        </div>
      </div>
    </div>
  </section>

  <div class="dp-seccion dp-seccion--mayo">
    <div class="dp-ancho">
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
<?php dp_cerrar();
