<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = rp();
// Horas de retiro: cada 30 minutos entre las 12:30 y las 22:30.
$horas = [];
for ($m = 12 * 60 + 30; $m <= 22 * 60 + 30; $m += 30) {
    $horas[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
}

rp_abrir('pedido', 'Tu pedido para llevar', 'Arma tu pedido de ' . $n['nombre'] . ' y envíalo por WhatsApp: retiro en el local o despacho en ' . implode(', ', array_keys($n['comunas_despacho'])) . '.');
?>
<main id="contenido">
  <?php rp_titular('Para llevar', 'Tu <em>pedido</em>', 'Revisa tus platos, elige retiro o despacho y envía el pedido por WhatsApp. Te confirmamos la hora de entrega.', '外賣'); ?>

  <section class="rp-seccion rp-seccion--sin-arriba">
    <div class="rp-ancho rp-pedido" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar el pedido aquí necesitas JavaScript. También puedes <a href="<?= demo_e(rp_wa('Hola, quiero hacer un pedido para llevar en ' . $n['nombre'] . '.')) ?>">pedir directo por WhatsApp</a>.</p></noscript>

      <div class="rp-pedido__vacio" data-carrito-vacio>
        <p>Tu pedido está vacío.</p>
        <a class="rp-btn" href="<?= demo_e(RP_BASE . 'carta.php') ?>">Ir a la carta</a>
      </div>

      <form class="rp-pedido__form" data-carrito-form hidden novalidate>
        <div class="rp-tarjeta">
          <h2>Platos</h2>
          <ul class="rp-pedido__lista" data-carrito-lista aria-live="polite"></ul>
          <p><a class="rp-enlace" href="<?= demo_e(RP_BASE . 'carta.php') ?>">Agregar más platos</a></p>
        </div>

        <div class="rp-tarjeta">
          <h2>Entrega</h2>
          <fieldset class="rp-opciones rp-opciones--fila">
            <legend class="demo-sr">Tipo de entrega</legend>
            <label class="rp-opcion"><input type="radio" name="entrega" value="retiro" checked> <span>Retiro en el local</span></label>
            <label class="rp-opcion"><input type="radio" name="entrega" value="despacho"> <span>Despacho a domicilio</span></label>
          </fieldset>
          <div class="rp-campo" data-solo-retiro>
            <label for="hora">Hora de retiro</label>
            <select id="hora" name="hora">
              <option value="">Lo antes posible</option>
              <?php foreach ($horas as $h): ?><option value="<?= demo_e($h) ?>"><?= demo_e($h) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="rp-campo" data-solo-despacho hidden>
            <label for="comuna">Comuna</label>
            <select id="comuna" name="comuna">
              <option value="">Elige tu comuna</option>
              <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
              <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="rp-campo" data-solo-despacho hidden>
            <label for="direccion">Dirección</label>
            <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle, número y depto.">
          </div>
          <div class="rp-campo">
            <label for="nombre">Tu nombre</label>
            <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
          </div>
          <div class="rp-campo">
            <label for="salsas">Salsas para acompañar</label>
            <select id="salsas" name="salsas" data-carrito-extra="Salsas">
              <option value="">Las de siempre (soya y ciruela)</option>
              <option value="chile">Agregar aceite de chile de la casa</option>
              <option value="jengibre">Agregar vinagre negro con jengibre</option>
            </select>
          </div>
          <label class="rp-check"><input type="checkbox" name="palillos" data-carrito-extra="Palillos" data-carrito-extra-valor="Sí, incluir palillos"> Incluir palillos</label>
          <div class="rp-campo">
            <label for="nota">Nota para la cocina <small>(opcional)</small></label>
            <textarea id="nota" name="nota" rows="2" maxlength="200" placeholder="Ej.: sin cebollín en la sopa"></textarea>
          </div>
          <p class="rp-pequeno">Esta es una demo: no guardamos tus datos. El pedido se arma en tu navegador y se envía por WhatsApp.</p>
        </div>

        <div class="rp-tarjeta rp-pedido__total">
          <h2>Resumen</h2>
          <dl class="rp-totales">
            <div><dt>Subtotal</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Despacho</dt><dd data-carrito-despacho>—</dd></div>
            <div class="rp-totales__total"><dt>Total</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <button class="rp-btn rp-btn--bloque rp-btn--wa" type="submit"><?= demo_icono_wa() ?> Enviar pedido por WhatsApp</button>
          <div class="rp-pedido__enviado" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu pedido. ¿Quieres vaciar el carrito?</p>
            <button class="rp-btn rp-btn--linea" type="button" data-carrito-vaciar>Vaciar el pedido</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>
<?php rp_cerrar();
