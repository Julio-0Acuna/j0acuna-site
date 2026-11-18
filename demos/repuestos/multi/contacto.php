<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = cb();
$estado = demo_horario_estado($n['horario']);

cb_abrir('contacto', 'Contacto', 'Horario, WhatsApp, despacho y ubicación de ' . $n['nombre_largo'] . ', tienda de repuestos de frenos y suspensión en ' . $n['comuna'] . '.');
?>
<main id="contenido">
  <?php cb_titular('Contacto', 'Escríbenos con la patente', 'Respondemos por WhatsApp en horario de atención. Si nos mandas la patente y una foto del repuesto viejo, te cotizamos en minutos.'); ?>

  <section class="cb-seccion cb-seccion--sin-arriba">
    <div class="cb-ancho cb-contacto">
      <article class="cb-tarjeta">
        <h2>WhatsApp y teléfono</h2>
        <p class="cb-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <div class="cb-acciones">
          <a class="cb-btn cb-btn--wa" href="<?= demo_e(cb_wa('Hola, quiero cotizar en ' . $n['nombre_largo'] . '. Mi patente es: ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> WhatsApp</a>
          <a class="cb-btn cb-btn--linea" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel(18) ?> Llamar</a>
        </div>
      </article>
      <article class="cb-tarjeta">
        <h2>Horario</h2>
        <?php demo_horario_tabla($n['horario'], 'demo-horario cb-horario'); ?>
      </article>
      <article class="cb-tarjeta">
        <h2>Despacho</h2>
        <p class="cb-pequeno">De lunes a sábado. Pedidos antes de las 13:00 salen en la ruta de la tarde.</p>
        <ul class="cb-comunas">
          <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
          <li><span><?= demo_e($comuna) ?></span><strong><?= demo_e(demo_clp($costo)) ?></strong></li>
          <?php endforeach; ?>
        </ul>
      </article>
    </div>
  </section>

  <div class="cb-seccion cb-seccion--blanca">
    <div class="cb-ancho">
      <?php demo_google([
          'nombre' => $n['nombre_largo'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Busca «' . $n['nombre_largo'] . '» en Google Maps: horario, cómo llegar y reseñas de clientes y talleres.']); ?>
    </div>
  </div>
</main>
<?php cb_cerrar();
