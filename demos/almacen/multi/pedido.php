<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = tn();
$ahora = tn_ahora();
$hayDespacho = tn_hay_despacho($ahora);

// Horas ofrecidas: cada 30 minutos desde la próxima media hora (retiro: 6 horas; despacho: dentro de su horario).
$base = $ahora->setTime((int) $ahora->format('G'), (int) $ahora->format('i') < 30 ? 30 : 0);
if ((int) $ahora->format('i') >= 30) {
    $base = $base->modify('+1 hour');
}
$horasRetiro = [];
$horasDespacho = [];
for ($i = 1; $i <= 24 && count($horasDespacho) < 10; $i++) {
    $h = $base->modify('+' . (30 * $i) . ' minutes');
    if ($i <= 12) {
        $horasRetiro[] = $h->format('H:i');
    }
    if (tn_hay_despacho($h)) {
        $horasDespacho[] = $h->format('H:i');
    }
}

tn_abrir('pedido', 'Tu pedido', 'Arma tu pedido de ' . $n['nombre'] . ' y envíalo por WhatsApp: retiro en el local a cualquier hora o despacho en ' . $n['barrio'] . ' y comunas cercanas.');
?>
<main id="contenido">
  <header class="tn-cabeza-pagina">
    <div class="tn-ancho">
      <p class="tn-sobre">Carrito</p>
      <h1>Tu pedido</h1>
      <p>Revisa los productos, elige retiro o despacho y envía el pedido por WhatsApp. Te confirmamos al tiro y te avisamos si falta algo.</p>
    </div>
  </header>

  <section class="tn-seccion tn-seccion--sin-arriba">
    <div class="tn-ancho tn-pedido" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar el pedido aquí necesitas JavaScript. También puedes <a href="<?= demo_e(tn_wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.')) ?>">pedir directo por WhatsApp</a>.</p></noscript>

      <div class="tn-pedido__vacio" data-carrito-vacio>
        <p><strong>Tu carrito está vacío.</strong> Recorre los pasillos y agrega lo que te falta.</p>
        <a class="tn-btn" href="<?= demo_e(TN_BASE . 'catalogo.php') ?>">Ir a los pasillos</a>
      </div>

      <form class="tn-pedido__form" data-carrito-form hidden novalidate>
        <div class="tn-caja tn-pedido__productos">
          <h2>Productos</h2>
          <ul class="tn-pedido__lista" data-carrito-lista aria-live="polite"></ul>
          <p><a class="tn-enlace" href="<?= demo_e(TN_BASE . 'catalogo.php') ?>">Seguir comprando</a></p>
        </div>

        <div class="tn-caja tn-pedido__datos">
          <h2>Entrega</h2>
          <fieldset class="tn-opciones">
            <legend class="demo-sr">Tipo de entrega</legend>
            <label class="tn-opcion"><input type="radio" name="entrega" value="retiro" checked> <span><strong>Retiro en el local</strong><small>A cualquier hora, listo en 15 minutos.</small></span></label>
            <label class="tn-opcion"><input type="radio" name="entrega" value="despacho"> <span><strong>Despacho</strong><small>De <?= demo_e($n['despacho_horas'][0]) ?> a <?= demo_e($n['despacho_horas'][1]) ?>. Mínimo <?= demo_e(demo_clp($n['minimo_despacho'])) ?>.</small></span></label>
          </fieldset>

          <div class="tn-campo" data-solo-retiro>
            <label for="hora">¿A qué hora pasas?</label>
            <select id="hora" name="hora">
              <option value="">Lo antes posible</option>
              <?php foreach ($horasRetiro as $h): ?><option value="<?= demo_e($h) ?>"><?= demo_e($h) ?></option><?php endforeach; ?>
            </select>
          </div>

          <div class="tn-campos-dos" data-solo-despacho hidden>
            <div class="tn-campo">
              <label for="comuna">Comuna</label>
              <select id="comuna" name="comuna">
                <option value="">Elige tu comuna</option>
                <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
                <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="tn-campo">
              <label for="cuando">¿Cuándo?</label>
              <select id="cuando" name="cuando" data-carrito-extra="Cuándo">
                <option value="<?= $hayDespacho ? 'pronto' : 'manana' ?>"><?= $hayDespacho ? 'Lo antes posible (unos ' . (int) $n['despacho_minutos'] . ' min)' : 'Desde las ' . demo_e($n['despacho_horas'][0]) ?></option>
                <?php foreach ($horasDespacho as $h): ?><option value="<?= demo_e($h) ?>">A las <?= demo_e($h) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="tn-campo">
              <label for="direccion">Dirección</label>
              <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle y número">
            </div>
            <div class="tn-campo">
              <label for="depto">Depto. o casa <small>(opcional)</small></label>
              <input id="depto" name="depto" maxlength="40" autocomplete="off" placeholder="Ej.: torre B, depto. 1204" data-carrito-extra="Depto. o casa">
            </div>
          </div>

          <div class="tn-campos-dos">
            <div class="tn-campo">
              <label for="reemplazo">Si falta un producto</label>
              <select id="reemplazo" name="reemplazo" data-carrito-extra="Si falta algo">
                <option value="parecido">Reemplázalo por uno parecido</option>
                <option value="llamar">Llámenme antes</option>
                <option value="no">No lo reemplacen</option>
              </select>
            </div>
            <div class="tn-campo">
              <label for="pago">¿Cómo pagas?</label>
              <select id="pago" name="pago" data-carrito-extra="Pago">
                <option value="efectivo">Efectivo</option>
                <option value="tarjeta">Débito o crédito</option>
                <option value="transferencia">Transferencia</option>
              </select>
            </div>
          </div>
          <div class="tn-campo">
            <label for="nombre">Tu nombre</label>
            <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
          </div>
          <div class="tn-campo">
            <label for="nota">Nota <small>(opcional)</small></label>
            <textarea id="nota" name="nota" rows="2" maxlength="200" placeholder="Ej.: paltas para mañana, marraqueta bien tostada"></textarea>
          </div>
          <p class="tn-pequeno">Esta es una demo: no guardamos tus datos. El pedido se arma en tu navegador y se envía por WhatsApp.</p>
        </div>

        <div class="tn-caja tn-pedido__total">
          <h2>Resumen</h2>
          <dl class="tn-totales">
            <div><dt>Subtotal</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Despacho</dt><dd data-carrito-despacho>—</dd></div>
            <div class="tn-totales__total"><dt>Total</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <p class="tn-minimo" data-carrito-minimo aria-live="polite" hidden></p>
          <button class="tn-btn tn-btn--bloque tn-btn--lima" type="submit"><?= demo_icono_wa() ?> Enviar pedido por WhatsApp</button>
          <div class="tn-pedido__enviado" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu pedido. ¿Vaciamos el carrito?</p>
            <button class="tn-btn tn-btn--linea" type="button" data-carrito-vaciar>Vaciar el carrito</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>
<?php tn_cerrar();
