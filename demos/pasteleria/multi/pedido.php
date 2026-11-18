<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = al();
// Horas de retiro: cada 30 minutos entre las 10:30 y las 19:00.
$horas = [];
for ($m = 10 * 60 + 30; $m <= 19 * 60; $m += 30) {
    $horas[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
}
$dias = al_dias_abiertos(0, 14);
$desdeTortas = array_key_first(al_dias_abiertos($n['anticipacion'][0], 1));

al_abrir('pedido', 'Tu pedido', 'Arma tu pedido de ' . $n['nombre'] . ' y envíalo por WhatsApp: retiro en el atelier o despacho en ' . $n['comuna'] . ' y el sector oriente.');
al_titular('Para llevar', 'Tu <em>pedido</em>', 'Revisa lo que elegiste, la fecha y la entrega, y envía el pedido por WhatsApp. Te confirmamos al tiro.');
?>
<main id="contenido">
  <section class="al-seccion al-seccion--sin-arriba">
    <div class="al-ancho al-pedido" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar el pedido aquí necesitas JavaScript. También puedes <a href="<?= demo_e(al_wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.')) ?>">pedir directo por WhatsApp</a>.</p></noscript>

      <div class="al-pedido__vacio" data-carrito-vacio>
        <p>Tu pedido está vacío.</p>
        <a class="al-btn" href="<?= demo_e(AL_BASE . 'carta.php') ?>">Ir a la carta</a>
      </div>

      <form class="al-pedido__form" data-carrito-form hidden novalidate>
        <div class="al-tarjeta">
          <h2>Lo que elegiste</h2>
          <ul class="al-pedido__lista" data-carrito-lista aria-live="polite"></ul>
          <p><a class="al-enlace" href="<?= demo_e(AL_BASE . 'carta.php') ?>">Agregar algo más</a></p>
        </div>

        <div class="al-tarjeta">
          <h2>Fecha y entrega</h2>
          <div class="al-campo">
            <label for="fecha">¿Para qué día?</label>
            <select id="fecha" name="fecha" data-carrito-extra="Fecha">
              <?php foreach ($dias as $valor => $texto): ?>
              <option value="<?= demo_e($valor) ?>"<?= $valor === $desdeTortas ? ' selected' : '' ?>><?= demo_e($texto) ?></option>
              <?php endforeach; ?>
            </select>
            <small class="al-ayuda">Las tortas enteras se entregan desde el <?= demo_e($dias[$desdeTortas] ?? '') ?>.</small>
          </div>
          <fieldset class="al-opciones">
            <legend class="demo-sr">Tipo de entrega</legend>
            <label><input type="radio" name="entrega" value="retiro" checked> <span><strong>Retiro en el atelier</strong></span></label>
            <label><input type="radio" name="entrega" value="despacho"> <span><strong>Despacho refrigerado</strong></span></label>
          </fieldset>
          <div class="al-campo" data-solo-retiro>
            <label for="hora">Hora de retiro</label>
            <select id="hora" name="hora">
              <option value="">Cualquier hora</option>
              <?php foreach ($horas as $h): ?><option value="<?= demo_e($h) ?>"><?= demo_e($h) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="al-campo" data-solo-despacho hidden>
            <label for="comuna">Comuna</label>
            <select id="comuna" name="comuna">
              <option value="">Elige tu comuna</option>
              <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
              <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="al-campo" data-solo-despacho hidden>
            <label for="direccion">Dirección</label>
            <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle, número y depto.">
          </div>
          <div class="al-campo">
            <label for="mensaje_torta">Mensaje en la torta <small>(opcional)</small></label>
            <input id="mensaje_torta" name="mensaje_torta" maxlength="40" autocomplete="off" placeholder="Ej.: Feliz cumpleaños, Ana" data-carrito-extra="Mensaje en la torta">
          </div>
          <div class="al-campo">
            <label for="nombre">Tu nombre</label>
            <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
          </div>
          <div class="al-campo">
            <label for="nota">Comentario <small>(opcional)</small></label>
            <textarea id="nota" name="nota" rows="2" maxlength="120" placeholder="Ej.: sin flores comestibles"></textarea>
          </div>
          <p class="al-pequeno">Esta es una demo: no guardamos tus datos. El pedido se arma en tu navegador y se envía por WhatsApp.</p>
        </div>

        <div class="al-tarjeta al-pedido__total">
          <h2>Resumen</h2>
          <dl class="al-totales">
            <div><dt>Subtotal</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Despacho</dt><dd data-carrito-despacho>—</dd></div>
            <div class="al-totales__total"><dt>Total</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <button class="al-btn al-btn--bloque al-btn--wa" type="submit"><?= demo_icono_wa() ?> Enviar pedido por WhatsApp</button>
          <div class="al-pedido__enviado" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu pedido. ¿Quieres vaciar el carrito?</p>
            <button class="al-btn al-btn--linea" type="button" data-carrito-vaciar>Vaciar el pedido</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>
<?php al_cerrar();
