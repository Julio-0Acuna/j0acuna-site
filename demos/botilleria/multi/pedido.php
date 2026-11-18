<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = co();
// Horas de retiro en la tienda: cada 30 minutos entre las 11:00 y las 20:00.
$horas = [];
for ($m = 11 * 60; $m <= 20 * 60; $m += 30) {
    $horas[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
}

co_abrir('pedido', 'Tu bolsa', 'Arma tu pedido de ' . $n['nombre'] . ' y envíalo por WhatsApp: retiro en la tienda o despacho en ' . $n['comuna'] . ' y comunas cercanas, con caja de regalo y reservas de catas.');
?>
<main id="contenido">
  <header class="co-titular">
    <div class="co-ancho">
      <p class="co-sobre">Tu pedido</p>
      <h1>Tu <em>bolsa</em></h1>
      <p>Revisa tus botellas y catas, elige retiro o despacho y envía el pedido por WhatsApp. Te confirmamos el stock y la hora.</p>
    </div>
  </header>

  <section class="co-seccion co-seccion--sin-arriba">
    <div class="co-ancho co-pedido" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar el pedido aquí necesitas JavaScript. También puedes <a href="<?= demo_e(co_wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.')) ?>">pedir directo por WhatsApp</a>.</p></noscript>

      <div class="co-pedido__vacio" data-carrito-vacio>
        <p>Tu bolsa está vacía.</p>
        <div class="co-acciones">
          <a class="co-btn" href="<?= demo_e(CO_BASE . 'catalogo.php') ?>">Ver vinos y destilados</a>
          <a class="co-btn co-btn--linea-oscura" href="<?= demo_e(CO_BASE . 'catas.php') ?>">Ver catas</a>
        </div>
      </div>

      <form class="co-pedido__form" data-carrito-form hidden novalidate>
        <div class="co-tarjeta">
          <h2>En tu bolsa</h2>
          <ul class="co-pedido__lista" data-carrito-lista aria-live="polite"></ul>
          <p><a class="co-enlace" href="<?= demo_e(CO_BASE . 'catalogo.php') ?>">Seguir eligiendo</a></p>
        </div>

        <div class="co-tarjeta">
          <h2>Entrega</h2>
          <fieldset class="co-opciones">
            <legend class="demo-sr">Tipo de entrega</legend>
            <label><input type="radio" name="entrega" value="retiro" checked> Retiro en la tienda</label>
            <label><input type="radio" name="entrega" value="despacho"> Despacho a domicilio</label>
          </fieldset>
          <div class="co-campo" data-solo-retiro>
            <label for="hora">Hora de retiro</label>
            <select id="hora" name="hora">
              <option value="">Cuando esté listo</option>
              <?php foreach ($horas as $h): ?><option value="<?= demo_e($h) ?>"><?= demo_e($h) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="co-campo" data-solo-despacho hidden>
            <label for="comuna">Comuna</label>
            <select id="comuna" name="comuna">
              <option value="">Elige tu comuna</option>
              <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
              <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="co-campo" data-solo-despacho hidden>
            <label for="direccion">Dirección</label>
            <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle, número y depto.">
          </div>
          <div class="co-campo">
            <label for="nombre">Tu nombre</label>
            <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
          </div>
          <label class="co-check"><input type="checkbox" name="regalo" data-carrito-extra="Para regalo" data-carrito-extra-valor="Sí, envolver con caja y tarjeta"> Es para regalo</label>
          <div class="co-campo">
            <label for="tarjeta">Mensaje para la tarjeta <small>(opcional, lo escribimos a mano)</small></label>
            <input id="tarjeta" name="tarjeta" maxlength="140" autocomplete="off" data-carrito-extra="Mensaje de la tarjeta" placeholder="Ej.: ¡Feliz cumpleaños, papá!">
          </div>
          <div class="co-campo">
            <label for="nota">Nota <small>(opcional)</small></label>
            <textarea id="nota" name="nota" rows="2" maxlength="200" placeholder="Ej.: llamar al llegar, el conserje recibe"></textarea>
          </div>
          <label class="co-check co-check--edad"><input type="checkbox" name="mayor" required data-carrito-extra="Edad" data-carrito-extra-valor="Confirmo que soy mayor de 18 años"> Confirmo que soy mayor de 18 años. Entiendo que se verifica la edad con carnet en la entrega o el retiro.</label>
          <p class="co-pequeno">Esta es una demo: no guardamos tus datos. El pedido se arma en tu navegador y se envía por WhatsApp.</p>
        </div>

        <div class="co-tarjeta co-pedido__total">
          <h2>Resumen</h2>
          <dl class="co-totales">
            <div><dt>Subtotal</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Despacho</dt><dd data-carrito-despacho>—</dd></div>
            <div class="co-totales__total"><dt>Total</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <button class="co-btn co-btn--bloque co-btn--wa" type="submit"><?= demo_icono_wa() ?> Enviar pedido por WhatsApp</button>
          <div class="co-pedido__enviado" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu pedido. ¿Quieres vaciar la bolsa?</p>
            <button class="co-btn co-btn--linea-oscura" type="button" data-carrito-vaciar>Vaciar la bolsa</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>
<?php co_cerrar();
