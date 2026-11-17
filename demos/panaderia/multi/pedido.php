<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = ml();
// Horas de retiro: cada 30 minutos entre las 8:00 y las 19:00.
$horas = [];
for ($m = 8 * 60; $m <= 19 * 60; $m += 30) {
    $horas[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
}

ml_abrir('pedido', 'Tu pedido', 'Arma tu encargo de ' . $n['nombre'] . ' y envíalo por WhatsApp: retiro en el local o despacho en ' . $n['comuna'] . ' y comunas cercanas.');
?>
<main id="contenido">
  <header class="ml-titular">
    <div class="ml-ancho">
      <p class="ml-etiqueta">Encargo</p>
      <h1>Tu <em>pedido</em></h1>
      <p>Revisa tus panes, elige retiro o despacho y envía el encargo por WhatsApp. Te confirmamos la hora.</p>
    </div>
  </header>

  <section class="ml-seccion ml-seccion--sin-arriba">
    <div class="ml-ancho ml-pedido" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar el pedido aquí necesitas JavaScript. También puedes <a href="<?= demo_e(ml_wa('Hola, quiero hacer un encargo en ' . $n['nombre'] . '.')) ?>">encargar directo por WhatsApp</a>.</p></noscript>

      <div class="ml-pedido__vacio" data-carrito-vacio>
        <p>Tu pedido está vacío. La hogaza de campo es un buen comienzo.</p>
        <a class="ml-btn" href="<?= demo_e(ML_BASE . 'panes.php') ?>"><span>Ir a los panes</span><?= ml_flecha() ?></a>
      </div>

      <form class="ml-pedido__form" data-carrito-form hidden novalidate>
        <div class="ml-tarjeta">
          <h2>Panes</h2>
          <ul class="ml-pedido__lista" data-carrito-lista aria-live="polite"></ul>
          <p><a class="ml-enlace" href="<?= demo_e(ML_BASE . 'panes.php') ?>">Agregar más panes <?= ml_flecha() ?></a></p>
        </div>

        <div class="ml-tarjeta">
          <h2>Entrega</h2>
          <fieldset class="ml-opciones">
            <legend class="demo-sr">Tipo de entrega</legend>
            <label><input type="radio" name="entrega" value="retiro" checked> <span><strong>Retiro en el local</strong><small>Sin costo, en <?= demo_e($n['comuna']) ?>.</small></span></label>
            <label><input type="radio" name="entrega" value="despacho"> <span><strong>Despacho a domicilio</strong><small>De martes a sábado.</small></span></label>
          </fieldset>
          <div class="ml-campo" data-solo-retiro>
            <label for="hora">Hora de retiro</label>
            <select id="hora" name="hora">
              <option value="">Lo antes posible</option>
              <?php foreach ($horas as $h): ?><option value="<?= demo_e($h) ?>"><?= demo_e($h) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="ml-campo" data-solo-despacho hidden>
            <label for="comuna">Comuna</label>
            <select id="comuna" name="comuna">
              <option value="">Elige tu comuna</option>
              <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
              <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="ml-campo" data-solo-despacho hidden>
            <label for="direccion">Dirección</label>
            <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle, número y depto.">
          </div>
          <div class="ml-campo">
            <label for="nombre">Tu nombre</label>
            <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
          </div>
          <div class="ml-campo">
            <label for="nota">Nota <small>(opcional)</small></label>
            <textarea id="nota" name="nota" rows="2" maxlength="200" placeholder="Ej.: para mañana sábado; la hogaza rebanada"></textarea>
          </div>
          <p class="ml-pequeno">Esta es una demo: no guardamos tus datos. El pedido se arma en tu navegador y se envía por WhatsApp.</p>
        </div>

        <div class="ml-tarjeta ml-pedido__total">
          <h2>Resumen</h2>
          <dl class="ml-totales">
            <div><dt>Subtotal</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Despacho</dt><dd data-carrito-despacho>—</dd></div>
            <div class="ml-totales__total"><dt>Total</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <button class="ml-btn ml-btn--bloque ml-btn--wa" type="submit"><?= demo_icono_wa() ?> <span>Enviar encargo por WhatsApp</span></button>
          <div class="ml-pedido__enviado" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu encargo. ¿Quieres vaciar el pedido?</p>
            <button class="ml-btn ml-btn--linea" type="button" data-carrito-vaciar><span>Vaciar el pedido</span></button>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>
<?php ml_cerrar();
