<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = mu();
// Horas de retiro: cada 30 minutos entre las 13:00 y las 22:30.
$horas = [];
for ($m = 13 * 60; $m <= 22 * 60 + 30; $m += 30) {
    $horas[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
}

mu_abrir('pedido', 'Tu pedido', 'Arma tu pedido de ' . $n['nombre'] . ' y envíalo por WhatsApp: retiro en el local o despacho en ' . $n['comuna'] . ' y comunas cercanas, con nivel de picante y cubiertos.');
?>
<main id="contenido">
  <header class="mu-titular">
    <div class="mu-ancho">
      <p class="mu-sobre"><?= mu_chakana(12) ?> Para llevar</p>
      <h1>Tu <em>pedido</em></h1>
      <p>Revisa tus platos, elige retiro o despacho y envía el pedido por WhatsApp. Te confirmamos el tiempo de entrega.</p>
    </div>
  </header>

  <section class="mu-seccion mu-seccion--sin-arriba">
    <div class="mu-ancho mu-pedido" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar el pedido aquí necesitas JavaScript. También puedes <a href="<?= demo_e(mu_wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.')) ?>">pedir directo por WhatsApp</a>.</p></noscript>

      <div class="mu-pedido__vacio" data-carrito-vacio>
        <?= mu_chakana(28) ?>
        <p>Tu pedido está vacío.</p>
        <a class="mu-btn" href="<?= demo_e(MU_BASE . 'carta.php') ?>">Ir a la carta</a>
      </div>

      <form class="mu-pedido__form" data-carrito-form hidden novalidate>
        <div class="mu-tarjeta">
          <h2>Platos</h2>
          <ul class="mu-pedido__lista" data-carrito-lista aria-live="polite"></ul>
          <p><a class="mu-enlace" href="<?= demo_e(MU_BASE . 'carta.php') ?>">Agregar más platos</a></p>
        </div>

        <div class="mu-tarjeta">
          <h2>Entrega</h2>
          <fieldset class="mu-opciones">
            <legend class="demo-sr">Tipo de entrega</legend>
            <label><input type="radio" name="entrega" value="retiro" checked> Retiro en el local</label>
            <label><input type="radio" name="entrega" value="despacho"> Despacho a domicilio</label>
          </fieldset>
          <div class="mu-campo" data-solo-retiro>
            <label for="hora">Hora de retiro</label>
            <select id="hora" name="hora">
              <option value="">Lo antes posible</option>
              <?php foreach ($horas as $h): ?><option value="<?= demo_e($h) ?>"><?= demo_e($h) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="mu-campo" data-solo-despacho hidden>
            <label for="comuna">Comuna</label>
            <select id="comuna" name="comuna">
              <option value="">Elige tu comuna</option>
              <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
              <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mu-campo" data-solo-despacho hidden>
            <label for="direccion">Dirección</label>
            <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle, número y depto.">
          </div>
          <div class="mu-form-grid">
            <div class="mu-campo">
              <label for="picante">¿Qué tan picante?</label>
              <select id="picante" name="picante" data-carrito-extra="Picante">
                <option value="como-viene">Como viene</option>
                <option value="suave">Suave, sin ají limo</option>
                <option value="bravo">Bravo, con rocoto extra</option>
              </select>
            </div>
            <div class="mu-campo">
              <label for="cubiertos">Cubiertos y palitos</label>
              <select id="cubiertos" name="cubiertos" data-carrito-extra="Cubiertos">
                <option value="">No, gracias</option>
                <option value="1">Para 1 persona</option>
                <option value="2">Para 2 personas</option>
                <option value="4">Para 4 personas</option>
              </select>
            </div>
          </div>
          <div class="mu-campo">
            <label for="nombre">Tu nombre</label>
            <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
          </div>
          <div class="mu-campo">
            <label for="nota">Nota para la cocina <small>(opcional)</small></label>
            <textarea id="nota" name="nota" rows="2" maxlength="200" placeholder="Ej.: el ceviche sin cebolla"></textarea>
          </div>
          <p class="mu-pequeno">Esta es una demo: no guardamos tus datos. El pedido se arma en tu navegador y se envía por WhatsApp.</p>
        </div>

        <div class="mu-tarjeta mu-pedido__total">
          <h2>Resumen</h2>
          <dl class="mu-totales">
            <div><dt>Subtotal</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Despacho</dt><dd data-carrito-despacho>—</dd></div>
            <div class="mu-totales__total"><dt>Total</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <button class="mu-btn mu-btn--bloque mu-btn--wa" type="submit"><?= demo_icono_wa() ?> Enviar pedido por WhatsApp</button>
          <div class="mu-pedido__enviado" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu pedido. ¿Quieres vaciar el carrito?</p>
            <button class="mu-btn mu-btn--linea" type="button" data-carrito-vaciar>Vaciar el pedido</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>
<?php mu_cerrar();
