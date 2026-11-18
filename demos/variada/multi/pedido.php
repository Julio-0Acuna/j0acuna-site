<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = dp();
// Horas de retiro: cada 30 minutos entre las 12:30 y las 22:30.
$horas = [];
for ($m = 12 * 60 + 30; $m <= 22 * 60 + 30; $m += 30) {
    $horas[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
}

dp_abrir('pedido', 'Tu comanda', 'Arma tu pedido de ' . $n['nombre'] . ' y envíalo por WhatsApp: retiro en el local o despacho en ' . $n['comuna'] . ' y comunas cercanas.');
?>
<main id="contenido">
  <header class="dp-titular">
    <div class="dp-ancho">
      <p class="dp-mono dp-sobre">Para llevar</p>
      <h1>Tu comanda</h1>
      <p>Revisa lo que pediste, elige el pan, retiro o despacho, y envía la comanda por WhatsApp. Te confirmamos el tiempo de entrega.</p>
    </div>
  </header>

  <section class="dp-seccion dp-seccion--sin-arriba">
    <div class="dp-ancho dp-pedido" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar la comanda aquí necesitas JavaScript. También puedes <a href="<?= demo_e(dp_wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.')) ?>">pedir directo por WhatsApp</a>.</p></noscript>

      <div class="dp-pedido__vacio" data-carrito-vacio>
        <p class="dp-mono">Comanda vacía</p>
        <p>Todavía no agregas nada. Parte por un churrasco italiano, nunca falla.</p>
        <a class="dp-btn" href="<?= demo_e(DP_BASE . 'carta.php') ?>">Ir a la carta</a>
      </div>

      <form class="dp-pedido__form" data-carrito-form hidden novalidate>
        <div class="dp-comanda">
          <p class="dp-comanda__cabeza dp-mono"><span><?= demo_e(mb_strtoupper($n['nombre'])) ?></span><span>Comanda</span></p>
          <h2 class="demo-sr">Tu pedido</h2>
          <ul class="dp-comanda__lista" data-carrito-lista aria-live="polite"></ul>
          <dl class="dp-totales dp-mono">
            <div><dt>Subtotal</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Despacho</dt><dd data-carrito-despacho>—</dd></div>
            <div class="dp-totales__total"><dt>Total</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <p><a class="dp-enlace" href="<?= demo_e(DP_BASE . 'carta.php') ?>">Agregar algo más</a></p>
        </div>

        <div class="dp-tarjeta">
          <h2>Cómo lo quieres</h2>
          <div class="dp-campo">
            <label for="pan">Pan de los sánguches</label>
            <select id="pan" name="pan" data-carrito-extra="Pan">
              <option value="">Marraqueta (la de siempre)</option>
              <option value="frica">Frica</option>
              <option value="integral">Pan integral</option>
            </select>
          </div>
          <div class="dp-campo">
            <label for="aji">Ají</label>
            <select id="aji" name="aji" data-carrito-extra="Ají">
              <option value="">Sin ají</option>
              <option value="verde">Ají verde aparte</option>
              <option value="pebre">Pebre aparte</option>
            </select>
          </div>
          <fieldset class="dp-checks">
            <legend class="demo-sr">Agregados</legend>
            <label><input type="checkbox" name="mayo_aparte" data-carrito-extra="Mayo aparte"> Mayo aparte</label>
            <label><input type="checkbox" name="cubiertos" data-carrito-extra="Cubiertos y servilletas"> Cubiertos y servilletas</label>
          </fieldset>
        </div>

        <div class="dp-tarjeta">
          <h2>Entrega</h2>
          <fieldset class="dp-opciones">
            <legend class="demo-sr">Tipo de entrega</legend>
            <label><input type="radio" name="entrega" value="retiro" checked> Retiro en el local</label>
            <label><input type="radio" name="entrega" value="despacho"> Despacho a domicilio</label>
          </fieldset>
          <div class="dp-campo" data-solo-retiro>
            <label for="hora">Hora de retiro</label>
            <select id="hora" name="hora">
              <option value="">Lo antes posible</option>
              <?php foreach ($horas as $h): ?><option value="<?= demo_e($h) ?>"><?= demo_e($h) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="dp-campo" data-solo-despacho hidden>
            <label for="comuna">Comuna</label>
            <select id="comuna" name="comuna">
              <option value="">Elige tu comuna</option>
              <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
              <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="dp-campo" data-solo-despacho hidden>
            <label for="direccion">Dirección</label>
            <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle, número y depto.">
          </div>
          <div class="dp-campo">
            <label for="nombre">Tu nombre</label>
            <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
          </div>
          <div class="dp-campo">
            <label for="nota">Nota para la plancha <small>(opcional)</small></label>
            <textarea id="nota" name="nota" rows="2" maxlength="200" placeholder="Ej.: el churrasco sin tomate"></textarea>
          </div>
          <button class="dp-btn dp-btn--bloque dp-btn--wa" type="submit"><?= demo_icono_wa() ?> Enviar comanda por WhatsApp</button>
          <div class="dp-pedido__enviado" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu comanda. ¿Vaciamos el carrito?</p>
            <button class="dp-btn dp-btn--linea" type="button" data-carrito-vaciar>Vaciar la comanda</button>
          </div>
          <p class="dp-pequeno">Esta es una demo: no guardamos tus datos. La comanda se arma en tu navegador y se envía por WhatsApp.</p>
        </div>
      </form>
    </div>
  </section>
</main>
<?php dp_cerrar();
