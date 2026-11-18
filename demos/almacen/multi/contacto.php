<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = tn();
tn_abrir('contacto', 'Contacto y despacho', 'Contacto de ' . $n['nombre'] . ', minimarket 24 horas en ' . $n['barrio'] . ': WhatsApp, teléfono, comunas y costos de despacho, y cómo llegar.');
?>
<main id="contenido">
  <header class="tn-cabeza-pagina">
    <div class="tn-ancho">
      <p class="tn-sobre">Contacto</p>
      <h1>Siempre hay alguien en la caja</h1>
      <p>Escríbenos, llámanos o pasa a vernos: estamos abiertos las 24 horas en <?= demo_e($n['barrio']) ?>.</p>
    </div>
  </header>

  <section class="tn-seccion tn-seccion--sin-arriba">
    <div class="tn-ancho tn-contacto">
      <a class="tn-contacto__tarjeta tn-contacto__tarjeta--wa" href="<?= demo_e(tn_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">
        <?= demo_icono_wa(30) ?>
        <strong>WhatsApp</strong>
        <span>Pedidos, reemplazos y consultas. Respondemos en minutos.</span>
      </a>
      <a class="tn-contacto__tarjeta" href="<?= demo_e(demo_tel()) ?>">
        <?= demo_icono_tel(30) ?>
        <strong>Llamar</strong>
        <span>Para lo urgente, a cualquier hora del día o de la noche.</span>
      </a>
      <div class="tn-contacto__tarjeta tn-contacto__tarjeta--info">
        <strong>Horario</strong>
        <span>Local: 24 horas, todos los días.<br>Despacho: de <?= demo_e($n['despacho_horas'][0]) ?> a <?= demo_e($n['despacho_horas'][1]) ?>.</span>
      </div>
    </div>
  </section>

  <section class="tn-seccion tn-seccion--gris" aria-labelledby="comunas-titulo">
    <div class="tn-ancho tn-tabla-despacho">
      <div>
        <p class="tn-sobre">Despacho</p>
        <h2 id="comunas-titulo">Comunas y costos</h2>
        <p>El costo se suma solo en el carrito al elegir tu comuna. Pedido mínimo para despacho: <?= demo_e(demo_clp($n['minimo_despacho'])) ?>. Para retiro en el local no hay mínimo.</p>
      </div>
      <table class="tn-tabla">
        <caption class="demo-sr">Costo de despacho por comuna</caption>
        <thead><tr><th scope="col">Comuna</th><th scope="col">Despacho</th><th scope="col">Tiempo aprox.</th></tr></thead>
        <tbody>
          <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
          <tr><th scope="row"><?= demo_e($comuna) ?></th><td><?= demo_e(demo_clp($costo)) ?></td><td><?= $costo <= 1990 ? '30 – 40 min' : '40 – 55 min' ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <div class="tn-seccion tn-google">
    <div class="tn-ancho">
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
<?php tn_cerrar();
