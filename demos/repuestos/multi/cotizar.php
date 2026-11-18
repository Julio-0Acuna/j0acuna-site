<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = cb();
// Horas de retiro: cada 30 minutos entre las 09:00 y las 18:00.
$horas = [];
for ($m = 9 * 60; $m <= 18 * 60; $m += 30) {
    $horas[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
}

cb_abrir('cotizar', 'Tu cotización', 'Arma tu cotización de repuestos de frenos y suspensión en ' . $n['nombre_largo'] . ' y envíala por WhatsApp con la patente, el vehículo, el año y el motor.');
?>
<main id="contenido">
  <?php cb_titular('Cotización', 'Tu cotización', 'Revisa los repuestos, agrega los datos del vehículo y envíala por WhatsApp. Con la patente confirmamos que todo calce antes de despachar.'); ?>

  <section class="cb-seccion cb-seccion--sin-arriba">
    <div class="cb-ancho cb-cotizar" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar la cotización aquí necesitas JavaScript. También puedes <a href="<?= demo_e(cb_wa('Hola, quiero cotizar repuestos. Mi patente es: ')) ?>">cotizar directo por WhatsApp</a>.</p></noscript>

      <div class="cb-cotizar__vacio" data-carrito-vacio>
        <p>Tu cotización está vacía.</p>
        <a class="cb-btn" href="<?= demo_e(CB_BASE . 'catalogo.php') ?>">Buscar repuestos</a>
      </div>

      <form class="cb-cotizar__form" data-carrito-form hidden novalidate>
        <div class="cb-tarjeta">
          <h2>Repuestos</h2>
          <ul class="cb-cotizar__lista" data-carrito-lista aria-live="polite"></ul>
          <p><a class="cb-enlace" href="<?= demo_e(CB_BASE . 'catalogo.php') ?>">Agregar más repuestos</a></p>
        </div>

        <div class="cb-tarjeta cb-tarjeta--vehiculo">
          <h2>Tu vehículo</h2>
          <p class="cb-pequeno">Con la patente basta. Si no la tienes, escribe marca, modelo, año y motor como aparecen en el padrón.</p>
          <div class="cb-campo cb-campo--patente">
            <label for="patente">Patente</label>
            <input id="patente" name="patente" maxlength="9" autocomplete="off" autocapitalize="characters" spellcheck="false" placeholder="BB CD 12" pattern="[A-Za-z]{2}[ ·.\-]?(?:[A-Za-z]{2}[ ·.\-]?[0-9]{2}|[0-9]{2}[ ·.\-]?[0-9]{2})" title="Patente chilena: BB·CD·12 o AB·12·34" aria-describedby="patente-ayuda" data-carrito-extra="Patente" data-patente>
            <p class="cb-pequeno" id="patente-ayuda">Formato chileno: 4 letras y 2 números (BB·CD·12) o 2 letras y 4 números (AB·12·34).</p>
          </div>
          <div class="cb-campo">
            <label for="vehiculo">Vehículo <small>(marca y modelo)</small></label>
            <input id="vehiculo" name="vehiculo" maxlength="60" autocomplete="off" placeholder="Como aparece en el padrón" data-carrito-extra="Vehículo">
          </div>
          <div class="cb-fila">
            <div class="cb-campo">
              <label for="anio">Año</label>
              <input id="anio" name="anio" inputmode="numeric" maxlength="4" autocomplete="off" placeholder="2016" data-carrito-extra="Año" data-guardado="anio">
            </div>
            <div class="cb-campo">
              <label for="motor">Motor</label>
              <input id="motor" name="motor" maxlength="20" autocomplete="off" placeholder="1.6 bencina" data-carrito-extra="Motor" data-guardado="cc">
            </div>
          </div>
          <div class="cb-campo">
            <label for="vin">VIN <small>(opcional, 17 caracteres)</small></label>
            <input id="vin" name="vin" maxlength="17" autocomplete="off" autocapitalize="characters" spellcheck="false" placeholder="Está en el padrón y en el parabrisas" data-carrito-extra="VIN">
          </div>
        </div>

        <div class="cb-tarjeta">
          <h2>Entrega</h2>
          <fieldset class="cb-opciones">
            <legend class="demo-sr">Tipo de entrega</legend>
            <label><input type="radio" name="entrega" value="retiro" checked> Retiro en el local</label>
            <label><input type="radio" name="entrega" value="despacho"> Despacho</label>
          </fieldset>
          <div class="cb-campo" data-solo-retiro>
            <label for="hora">Hora de retiro</label>
            <select id="hora" name="hora">
              <option value="">Cuando esté listo</option>
              <?php foreach ($horas as $h): ?><option value="<?= demo_e($h) ?>"><?= demo_e($h) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="cb-campo" data-solo-despacho hidden>
            <label for="comuna">Comuna</label>
            <select id="comuna" name="comuna">
              <option value="">Elige la comuna</option>
              <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
              <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="cb-campo" data-solo-despacho hidden>
            <label for="direccion">Dirección</label>
            <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle y número, o el taller que lo instala">
          </div>
          <div class="cb-campo">
            <label for="nombre">Tu nombre</label>
            <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
          </div>
          <div class="cb-campo">
            <label for="nota">Nota <small>(opcional)</small></label>
            <textarea id="nota" name="nota" rows="2" maxlength="200" placeholder="Ej.: el disco viejo mide 23 mm; el auto chilla al frenar"></textarea>
          </div>
          <p class="cb-pequeno">Es una demo: no guardamos tus datos. La cotización se arma en tu navegador y se envía por WhatsApp.</p>
        </div>

        <div class="cb-tarjeta cb-cotizar__total">
          <h2>Resumen</h2>
          <dl class="cb-totales">
            <div><dt>Subtotal</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Despacho</dt><dd data-carrito-despacho>—</dd></div>
            <div class="cb-totales__total"><dt>Total</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <button class="cb-btn cb-btn--bloque cb-btn--wa" type="submit"><?= demo_icono_wa() ?> Enviar cotización por WhatsApp</button>
          <div class="cb-cotizar__enviado" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu cotización. ¿Quieres vaciarla?</p>
            <button class="cb-btn cb-btn--linea" type="button" data-carrito-vaciar>Vaciar la cotización</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>
<?php cb_cerrar();
