<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = eg();
$estado = demo_horario_estado($n['horario']);

eg_abrir('contacto', 'Contacto y horario', 'Horario, ubicación y contacto de ' . $n['nombre'] . ', barbería de caballeros en el ' . $n['barrio'] . ', ' . $n['comuna'] . ', Santiago.');
?>
<main id="contenido">
  <?php eg_titular('Contacto', 'Dónde y cuándo', 'En el ' . $n['barrio'] . ', a pasos del metro. Suba la escalera de mármol y toque el timbre de bronce.'); ?>
  <section class="eg-seccion eg-seccion--sin-arriba">
    <div class="eg-ancho eg-contacto">
      <div class="eg-placa">
        <p class="eg-sobre">Horario</p>
        <p class="eg-estado"><span class="eg-estado__punto<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario eg-tabla-horario'); ?>
        <p class="eg-pequeno">Los sábados, el último bloque es a las 17:00.</p>
      </div>
      <div class="eg-contacto__datos">
        <h2>Escríbanos o llámenos</h2>
        <p>Para reservar, mover una hora o pedir una tarjeta de regalo. Respondemos en horario de atención.</p>
        <div class="eg-acciones">
          <a class="eg-btn" href="<?= demo_e(eg_wa('Hola, quiero reservar una hora en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> WhatsApp</a>
          <a class="eg-btn eg-btn--linea" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel(18) ?> Llamar</a>
        </div>
        <ul class="eg-datos">
          <li><strong>Ubicación</strong><span><?= demo_e(ucfirst($n['barrio'])) ?>, <?= demo_e($n['comuna']) ?>, Santiago. Segundo piso.</span></li>
          <li><strong>Pago</strong><span>Efectivo, débito, crédito o transferencia.</span></li>
          <li><strong>Estacionamiento</strong><span>Estacionamientos públicos en la misma cuadra.</span></li>
        </ul>
      </div>
    </div>
  </section>
  <div class="eg-seccion eg-seccion--oscura">
    <div class="eg-ancho">
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
<?php eg_cerrar();
