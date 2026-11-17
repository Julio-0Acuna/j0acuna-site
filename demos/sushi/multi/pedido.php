<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = ku();
// Horas de retiro: cada 30 minutos entre las 13:00 y las 23:00.
$horas = [];
for ($m = 13 * 60; $m <= 23 * 60; $m += 30) {
    $horas[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
}

ku_abrir('pedido', 'Tu pedido para llevar', 'Arma tu pedido de ' . $n['nombre'] . ' y envíalo por WhatsApp: retiro en el local o despacho en ' . $n['comuna'] . ' y comunas cercanas.');
?>
<main id="contenido">
  <?php ku_titular('Para llevar', 'Tu <em>pedido</em>', 'Revisa tus platos, elige retiro o despacho y envía el pedido por WhatsApp. Te confirmamos la hora de entrega.', '注文'); ?>

  <section class="ku-seccion ku-seccion--sin-arriba">
    <div class="ku-ancho ku-pedido" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar el pedido aquí necesitas JavaScript. También puedes <a href="<?= demo_e(ku_wa('Hola, quiero hacer un pedido para llevar en ' . $n['nombre'] . '.')) ?>">pedir directo por WhatsApp</a>.</p></noscript>

      <div class="ku-pedido__vacio" data-carrito-vacio>
        <?= ku_sello('ku-sello--grande') ?>
        <p>Tu pedido está vacío.</p>
        <a class="ku-btn" href="<?= demo_e(KU_BASE . 'menu.php') ?>"><span>Ir a la carta</span></a>
      </div>

      <form class="ku-pedido__form" data-carrito-form hidden novalidate>
        <div class="ku-tarjeta">
          <h2>Platos</h2>
          <ul class="ku-pedido__lista" data-carrito-lista aria-live="polite"></ul>
          <p><a class="ku-enlace" href="<?= demo_e(KU_BASE . 'menu.php') ?>">Agregar más platos</a></p>
        </div>

        <div class="ku-tarjeta">
          <h2>Entrega</h2>
          <fieldset class="ku-opciones">
            <legend class="demo-sr">Tipo de entrega</legend>
            <label><input type="radio" name="entrega" value="retiro" checked> <span>Retiro en <?= demo_e($n['comuna']) ?></span></label>
            <label><input type="radio" name="entrega" value="despacho"> <span>Despacho a domicilio</span></label>
          </fieldset>
          <div class="ku-campo" data-solo-retiro>
            <label for="hora">Hora de retiro</label>
            <select id="hora" name="hora">
              <option value="">Lo antes posible</option>
              <?php foreach ($horas as $h): ?><option value="<?= demo_e($h) ?>"><?= demo_e($h) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="ku-campo" data-solo-despacho hidden>
            <label for="comuna">Comuna</label>
            <select id="comuna" name="comuna">
              <option value="">Elige tu comuna</option>
              <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
              <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="ku-campo" data-solo-despacho hidden>
            <label for="direccion">Dirección</label>
            <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle, número y depto.">
          </div>
          <div class="ku-campo">
            <label for="nombre">Tu nombre</label>
            <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
          </div>
          <div class="ku-campo">
            <label for="nota">Nota para la cocina <small>(opcional)</small></label>
            <textarea id="nota" name="nota" rows="2" maxlength="200" placeholder="Ej.: sin sésamo, alergia a mariscos"></textarea>
          </div>
          <p class="ku-pequeno">Esta es una demo: no guardamos tus datos. El pedido se arma en tu navegador y se envía por WhatsApp.</p>
        </div>

        <div class="ku-tarjeta ku-pedido__total">
          <h2>Resumen</h2>
          <dl class="ku-totales">
            <div><dt>Subtotal</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Despacho</dt><dd data-carrito-despacho>—</dd></div>
            <div class="ku-totales__total"><dt>Total</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <button class="ku-btn ku-btn--bloque ku-btn--wa" type="submit"><?= demo_icono_wa() ?> <span>Enviar pedido por WhatsApp</span></button>
          <div class="ku-pedido__enviado" data-carrito-enviado hidden>
            <p>Listo: se abrió WhatsApp con tu pedido. ¿Quieres vaciar el carrito?</p>
            <button class="ku-btn ku-btn--linea" type="button" data-carrito-vaciar><span>Vaciar el pedido</span></button>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>
<?php ku_cerrar();
