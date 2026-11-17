<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = fo();
// Horas de retiro: cada 30 minutos entre las 13:00 y las 23:00.
$horas = [];
for ($m = 13 * 60; $m <= 23 * 60; $m += 30) {
    $horas[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
}

fo_abrir('pedido', 'Tu pedido', 'Arma tu pedido de ' . $n['nombre'] . ' y envíalo por WhatsApp: retiro en el local o despacho en ' . $n['comuna'] . ' y comunas cercanas.');
?>
<main id="contenido">
  <header class="fo-titular">
    <div class="fo-ancho">
      <p class="fo-sobre">Para llevar</p>
      <h1>Tu <em>pedido</em></h1>
      <p>Revisa tus platos, elige retiro o despacho y envía el pedido por WhatsApp. Te confirmamos el tiempo de entrega.</p>
    </div>
  </header>

  <section class="fo-seccion fo-seccion--sin-arriba">
    <div class="fo-ancho fo-pedido" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar el pedido aquí necesitas JavaScript. También puedes <a href="<?= demo_e(fo_wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.')) ?>">pedir directo por WhatsApp</a>.</p></noscript>

      <div class="fo-pedido__vacio" data-carrito-vacio>
        <p>Tu pedido está vacío.</p>
        <a class="fo-btn" href="<?= demo_e(FO_BASE . 'menu.php') ?>">Ir al menú</a>
      </div>

      <form class="fo-pedido__form" data-carrito-form hidden novalidate>
        <div class="fo-tarjeta">
          <h2>Platos</h2>
          <ul class="fo-pedido__lista" data-carrito-lista aria-live="polite"></ul>
          <p><a class="fo-enlace" href="<?= demo_e(FO_BASE . 'menu.php') ?>">Agregar más platos</a></p>
        </div>

        <div class="fo-tarjeta">
          <h2>Entrega</h2>
          <fieldset class="fo-opciones">
            <legend class="demo-sr">Tipo de entrega</legend>
            <label><input type="radio" name="entrega" value="retiro" checked> Retiro en el local</label>
            <label><input type="radio" name="entrega" value="despacho"> Despacho a domicilio</label>
          </fieldset>
          <div class="fo-campo" data-solo-retiro>
            <label for="hora">Hora de retiro</label>
            <select id="hora" name="hora">
              <option value="">Lo antes posible</option>
              <?php foreach ($horas as $h): ?><option value="<?= demo_e($h) ?>"><?= demo_e($h) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="fo-campo" data-solo-despacho hidden>
            <label for="comuna">Comuna</label>
            <select id="comuna" name="comuna">
              <option value="">Elige tu comuna</option>
              <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
              <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="fo-campo" data-solo-despacho hidden>
            <label for="direccion">Dirección</label>
            <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle, número y depto.">
          </div>
          <div class="fo-campo">
            <label for="nombre">Tu nombre</label>
            <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
          </div>
          <div class="fo-campo">
            <label for="nota">Nota para la cocina <small>(opcional)</small></label>
            <textarea id="nota" name="nota" rows="2" maxlength="200" placeholder="Ej.: sin aceitunas en la Bismarck"></textarea>
          </div>
          <p class="fo-pequeno">Esta es una demo: no guardamos tus datos. El pedido se arma en tu navegador y se envía por WhatsApp.</p>
        </div>

        <div class="fo-tarjeta fo-pedido__total">
          <h2>Resumen</h2>
          <dl class="fo-totales">
            <div><dt>Subtotal</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Despacho</dt><dd data-carrito-despacho>—</dd></div>
            <div class="fo-totales__total"><dt>Total</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <button class="fo-btn fo-btn--bloque fo-btn--wa" type="submit"><?= demo_icono_wa() ?> Enviar pedido por WhatsApp</button>
          <div class="fo-pedido__enviado" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu pedido. ¿Quieres vaciar el carrito?</p>
            <button class="fo-btn fo-btn--linea" type="button" data-carrito-vaciar>Vaciar el pedido</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>
<?php fo_cerrar();
