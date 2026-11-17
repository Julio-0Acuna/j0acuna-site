<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = vt();
// Horas de retiro: cada 30 minutos entre las 10:30 y las 20:00.
$horas = [];
for ($m = 10 * 60 + 30; $m <= 20 * 60; $m += 30) {
    $horas[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
}

vt_abrir('pedido', 'Tu pedido', 'Arma tu pedido de ' . $n['nombre'] . ' y envíalo por WhatsApp: retiro en la tienda o despacho en ' . $n['comuna'] . ' y comunas cercanas.');
?>
<main id="contenido">
  <header class="vt-titular">
    <div class="vt-ancho">
      <p class="vt-mono">Retiro o despacho</p>
      <h1>Tu <em>pedido</em></h1>
      <p>Revisa tus cortes, elige retiro o despacho y envía el pedido por WhatsApp. Te confirmamos el peso final y la hora de entrega.</p>
    </div>
  </header>

  <section class="vt-seccion vt-seccion--sin-arriba">
    <div class="vt-ancho vt-pedido" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar el pedido aquí necesitas JavaScript. También puedes <a href="<?= demo_e(vt_wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.')) ?>">pedir directo por WhatsApp</a>.</p></noscript>

      <div class="vt-pedido__vacio" data-carrito-vacio>
        <p>Tu pedido está vacío.</p>
        <a class="vt-btn" href="<?= demo_e(VT_BASE . 'cortes.php') ?>">Ir a la carta</a>
      </div>

      <form class="vt-pedido__form" data-carrito-form hidden novalidate>
        <div class="vt-tarjeta">
          <h2>Cortes</h2>
          <ul class="vt-pedido__lista" data-carrito-lista aria-live="polite"></ul>
          <p><a class="vt-enlace" href="<?= demo_e(VT_BASE . 'cortes.php') ?>">Agregar más cortes</a></p>
        </div>

        <div class="vt-tarjeta">
          <h2>Entrega</h2>
          <fieldset class="vt-opciones">
            <legend class="demo-sr">Tipo de entrega</legend>
            <label><input type="radio" name="entrega" value="retiro" checked> Retiro en la tienda</label>
            <label><input type="radio" name="entrega" value="despacho"> Despacho a domicilio</label>
          </fieldset>
          <div class="vt-campo" data-solo-retiro>
            <label for="hora">Hora de retiro</label>
            <select id="hora" name="hora">
              <option value="">Lo antes posible</option>
              <?php foreach ($horas as $h): ?><option value="<?= demo_e($h) ?>"><?= demo_e($h) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="vt-campo" data-solo-despacho hidden>
            <label for="comuna">Comuna</label>
            <select id="comuna" name="comuna">
              <option value="">Elige tu comuna</option>
              <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
              <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="vt-campo" data-solo-despacho hidden>
            <label for="direccion">Dirección</label>
            <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle, número y depto.">
          </div>
          <div class="vt-campo">
            <label for="nombre">Tu nombre</label>
            <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
          </div>
          <div class="vt-campo">
            <label for="nota">Nota para el carnicero <small>(opcional)</small></label>
            <textarea id="nota" name="nota" rows="2" maxlength="200" placeholder="Ej.: bifes de 4 cm de grosor"></textarea>
          </div>
          <p class="vt-pequeno">Esta es una demo: no guardamos tus datos. El pedido se arma en tu navegador y se envía por WhatsApp.</p>
        </div>

        <div class="vt-tarjeta vt-pedido__total">
          <h2>Resumen</h2>
          <dl class="vt-totales">
            <div><dt>Subtotal</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Despacho</dt><dd data-carrito-despacho>—</dd></div>
            <div class="vt-totales__total"><dt>Total</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <p class="vt-pequeno">Los cortes por peso se ajustan al peso real al cortar (±5 %).</p>
          <button class="vt-btn vt-btn--bloque vt-btn--wa" type="submit"><?= demo_icono_wa() ?> Enviar pedido por WhatsApp</button>
          <div class="vt-pedido__enviado" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu pedido. ¿Quieres vaciar el carrito?</p>
            <button class="vt-btn vt-btn--linea" type="button" data-carrito-vaciar>Vaciar el pedido</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>
<?php vt_cerrar();
