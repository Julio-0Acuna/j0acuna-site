<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = nc();
$ahora = new DateTimeImmutable('now', new DateTimeZone(DEMO_ZONA_HORARIA));
$hoy = $n['horario'][(int) $ahora->format('N')] ?? null;

// Horas de retiro: cada 30 minutos desde la próxima media hora hasta el cierre de hoy (si no, desde mañana).
$horas = [];
if ($hoy !== null) {
    $min = (int) $ahora->format('G') * 60 + (int) $ahora->format('i') + 30;
    $desde = max(demo_minutos($hoy[0]) + 30, (int) (ceil($min / 30) * 30));
    for ($m = $desde; $m <= demo_minutos($hoy[1]) - 15 && count($horas) < 16; $m += 30) {
        $horas[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
    }
}

nc_abrir('pedido', 'Tu bolsa', 'Arma tu pedido en ' . $n['nombre_corto'] . ' y envíalo por WhatsApp: retiro en la farmacia de ' . $n['comuna'] . ' o despacho en el día.');
?>
<main id="contenido">
  <header class="nc-cabeza-pagina">
    <div class="nc-ancho">
      <p class="nc-sobre">Tu bolsa</p>
      <h1>Revisa y envía tu pedido</h1>
      <p>Elige retiro o despacho y envíalo por WhatsApp. Te confirmamos el stock y, si pides despacho, la hora aproximada de llegada.</p>
    </div>
  </header>

  <section class="nc-seccion nc-seccion--sin-arriba">
    <div class="nc-ancho nc-pedido" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar la bolsa aquí necesitas JavaScript. También puedes <a href="<?= demo_e(nc_wa('Hola, quiero hacer un pedido en ' . $n['nombre_corto'] . '.')) ?>">pedir directo por WhatsApp</a>.</p></noscript>

      <div class="nc-pedido__vacio" data-carrito-vacio>
        <?= nc_muestra('crema', 'nc-muestra--chica') ?>
        <p><strong>Tu bolsa está vacía.</strong> Recorre la tienda y agrega lo que necesitas.</p>
        <a class="nc-btn" href="<?= demo_e(NC_BASE . 'tienda.php') ?>">Ir a la tienda</a>
      </div>

      <form class="nc-pedido__form" data-carrito-form hidden novalidate>
        <div class="nc-caja nc-pedido__productos">
          <h2 class="nc-caja__titulo">Productos</h2>
          <ul class="nc-pedido__lista" data-carrito-lista aria-live="polite"></ul>
          <p><a class="nc-enlace" href="<?= demo_e(NC_BASE . 'tienda.php') ?>">Seguir comprando</a></p>
        </div>

        <div class="nc-caja nc-pedido__datos">
          <h2 class="nc-caja__titulo">Entrega</h2>
          <fieldset class="nc-opciones nc-opciones--tarjetas">
            <legend class="demo-sr">Tipo de entrega</legend>
            <label class="nc-opcion"><input type="radio" name="entrega" value="retiro" checked> <span><strong>Retiro en la farmacia</strong><small>Listo en 20 minutos, en <?= demo_e($n['comuna']) ?>.</small></span></label>
            <label class="nc-opcion"><input type="radio" name="entrega" value="despacho"> <span><strong>Despacho a domicilio</strong><small>Mínimo <?= demo_e(demo_clp($n['minimo_despacho'])) ?>. Gratis desde <?= demo_e(demo_clp($n['gratis_desde'])) ?>.</small></span></label>
          </fieldset>

          <div class="nc-campo" data-solo-retiro>
            <label for="hora">¿A qué hora pasas?</label>
            <select id="hora" name="hora">
              <option value=""><?= $horas ? 'Lo antes posible' : 'Mañana, apenas abramos' ?></option>
              <?php foreach ($horas as $h): ?><option value="<?= demo_e($h) ?>"><?= demo_e($h) ?></option><?php endforeach; ?>
            </select>
          </div>

          <div class="nc-campos-dos" data-solo-despacho hidden>
            <div class="nc-campo">
              <label for="comuna">Comuna</label>
              <select id="comuna" name="comuna">
                <option value="">Elige tu comuna</option>
                <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
                <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="nc-campo">
              <label for="direccion">Dirección</label>
              <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle y número">
            </div>
            <div class="nc-campo nc-campo--ancho">
              <label for="depto">Depto., oficina o referencia <small>(opcional)</small></label>
              <input id="depto" name="depto" maxlength="60" autocomplete="off" placeholder="Ej.: oficina 1204, dejar en conserjería" data-carrito-extra="Depto. o referencia">
            </div>
          </div>

          <div class="nc-campos-dos">
            <div class="nc-campo">
              <label for="piel">Tu tipo de piel <small>(para incluirte muestras)</small></label>
              <select id="piel" name="piel" data-carrito-extra="Tipo de piel (para muestras)">
                <option value="">Prefiero no decir</option>
                <option value="seca">Seca</option>
                <option value="mixta">Mixta</option>
                <option value="grasa">Grasa</option>
                <option value="sensible">Sensible</option>
              </select>
            </div>
            <div class="nc-campo">
              <label for="pago">¿Cómo pagas?</label>
              <select id="pago" name="pago" data-carrito-extra="Pago">
                <option value="debito">Débito o crédito</option>
                <option value="efectivo">Efectivo</option>
                <option value="transferencia">Transferencia</option>
              </select>
            </div>
          </div>
          <div class="nc-campo">
            <label for="receta">¿Necesitas también un medicamento con receta? <small>(opcional, se cotiza aparte)</small></label>
            <input id="receta" name="receta" maxlength="120" autocomplete="off" placeholder="Nombre y dosis como aparecen en tu receta" data-carrito-extra="Receta para cotizar">
            <small class="nc-ayuda">No se suma al total: te escribimos con el precio. La receta se presenta al retirar o al recibir y la revisa la química farmacéutica.</small>
          </div>
          <div class="nc-campos-dos">
            <div class="nc-campo">
              <label for="nombre">Tu nombre</label>
              <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
            </div>
            <div class="nc-campo">
              <label for="nota">Nota <small>(opcional)</small></label>
              <input id="nota" name="nota" maxlength="200" autocomplete="off" placeholder="Ej.: es para regalo">
            </div>
          </div>
          <p class="nc-pequeno">Esta es una demo: no guardamos tus datos. El pedido se arma en tu navegador y se envía por WhatsApp.</p>
        </div>

        <div class="nc-caja nc-pedido__total">
          <h2 class="nc-caja__titulo">Resumen</h2>
          <dl class="nc-totales">
            <div><dt>Subtotal</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Despacho</dt><dd data-carrito-despacho>—</dd></div>
            <div class="nc-totales__total"><dt>Total</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <p class="nc-meta" data-carrito-minimo aria-live="polite" hidden></p>
          <p class="nc-meta nc-meta--gratis" data-carrito-gratis aria-live="polite" hidden></p>
          <button class="nc-btn nc-btn--bloque" type="submit"><?= demo_icono_wa(18) ?><span>Enviar pedido por WhatsApp</span></button>
          <div class="nc-pedido__enviado" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu pedido. ¿Vaciamos la bolsa?</p>
            <button class="nc-btn nc-btn--linea" type="button" data-carrito-vaciar>Vaciar la bolsa</button>
          </div>
          <p class="nc-ficha__aviso">Lee las instrucciones antes de usar un medicamento.</p>
        </div>
      </form>
    </div>
  </section>
</main>
<?php nc_cerrar();
