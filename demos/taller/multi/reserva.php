<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = vg();
$dias = vg_dias();

vg_abrir('reserva', 'Tu reserva', 'Reserva tu hora en ' . $n['nombre'] . ': servicios elegidos, datos del vehículo (patente, modelo, año y kilometraje), día y bloque, y retiro a domicilio. Se envía por WhatsApp.');
?>
<main id="contenido">
  <?php vg_titular('Último paso', 'Tu reserva', 'Revisa los servicios, completa la ficha del vehículo y elige el día. Se abre WhatsApp con todo escrito y te confirmamos la hora.'); ?>

  <section class="vg-seccion vg-seccion--sin-arriba">
    <div class="vg-ancho vg-reserva" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar la reserva aquí necesitas JavaScript. También puedes <a href="<?= demo_e(vg_wa('Hola, quiero reservar una hora en ' . $n['nombre'] . '. Patente y modelo: ')) ?>">reservar directo por WhatsApp</a>.</p></noscript>

      <div class="vg-reserva__vacia vg-caja" data-carrito-vacio>
        <p>Aún no agregas servicios a tu reserva.</p>
        <a class="vg-btn" href="<?= demo_e(VG_BASE . 'servicios.php') ?>">Elegir servicios</a>
      </div>

      <form class="vg-reserva__form" data-carrito-form hidden novalidate>
        <div class="vg-reserva__datos">
          <fieldset class="vg-caja">
            <legend><span class="vg-mono">01</span> Servicios</legend>
            <ul class="vg-reserva__lista" data-carrito-lista aria-live="polite"></ul>
            <p><a class="vg-enlace" href="<?= demo_e(VG_BASE . 'servicios.php') ?>">Agregar otro servicio</a></p>
          </fieldset>

          <fieldset class="vg-caja">
            <legend><span class="vg-mono">02</span> Ficha del vehículo</legend>
            <div class="vg-form-grid">
              <div class="vg-campo">
                <label for="patente">Patente</label>
                <input id="patente" name="patente" maxlength="8" required autocomplete="off" autocapitalize="characters" placeholder="ABCD12" pattern="[A-Za-z]{2}[ -]?[A-Za-z0-9]{2}[ -]?[0-9]{2}" title="Seis caracteres, por ejemplo ABCD12 o AB1234" data-carrito-extra="Patente">
              </div>
              <div class="vg-campo">
                <label for="modelo">Marca y modelo</label>
                <input id="modelo" name="modelo" maxlength="40" required autocomplete="off" placeholder="Ej.: SUV mediano" data-carrito-extra="Vehículo">
              </div>
              <div class="vg-campo">
                <label for="anio">Año</label>
                <input id="anio" name="anio" inputmode="numeric" maxlength="4" pattern="(19[7-9][0-9]|20[0-9]{2})" title="Año de cuatro dígitos" autocomplete="off" placeholder="2019" data-carrito-extra="Año">
              </div>
              <div class="vg-campo">
                <label for="km">Kilometraje</label>
                <input id="km" name="km" inputmode="numeric" maxlength="9" autocomplete="off" placeholder="45.000" data-carrito-extra="Kilometraje">
              </div>
            </div>
          </fieldset>

          <fieldset class="vg-caja">
            <legend><span class="vg-mono">03</span> Día y entrega del auto</legend>
            <div class="vg-form-grid">
              <div class="vg-campo">
                <label for="dia">Día</label>
                <select id="dia" name="dia" required data-carrito-extra="Día">
                  <option value="">Elige un día</option>
                  <?php foreach ($dias as $d): ?><option value="<?= demo_e($d) ?>"><?= demo_e($d) ?></option><?php endforeach; ?>
                </select>
              </div>
              <div class="vg-campo">
                <label for="bloque">Bloque</label>
                <select id="bloque" name="bloque" required data-carrito-extra="Bloque">
                  <option value="">Elige un bloque</option>
                  <?php foreach ($n['bloques'] as $b): ?><option value="<?= demo_e($b) ?>"><?= demo_e($b) ?></option><?php endforeach; ?>
                </select>
              </div>
            </div>
            <p class="vg-pequeno">Los sábados atendemos solo los dos primeros bloques. Te confirmamos la hora exacta por WhatsApp.</p>
            <div class="vg-opciones" role="radiogroup" aria-label="¿Cómo llega el auto al taller?">
              <label class="vg-opcion"><input type="radio" name="entrega" value="retiro" checked> <span><strong>Llevo el auto al taller</strong><small>Sin costo. Te esperamos en el bloque elegido.</small></span></label>
              <label class="vg-opcion"><input type="radio" name="entrega" value="despacho"> <span><strong>Retírenlo en mi casa u oficina</strong><small>Chofer con seguro, ida y vuelta. Costo según comuna.</small></span></label>
            </div>
            <div class="vg-campo" data-solo-retiro>
              <label class="vg-check"><input type="checkbox" name="espera" value="1" data-carrito-extra="Espera en la sala"> Espero en la sala mientras trabajan (servicios de hasta 2 horas)</label>
            </div>
            <div class="vg-form-grid">
              <div class="vg-campo" data-solo-despacho hidden>
                <label for="comuna">Comuna</label>
                <select id="comuna" name="comuna">
                  <option value="">Elige tu comuna</option>
                  <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
                  <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="vg-campo" data-solo-despacho hidden>
                <label for="direccion">Dirección de retiro</label>
                <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle, número y depto. u oficina">
              </div>
            </div>
          </fieldset>

          <fieldset class="vg-caja">
            <legend><span class="vg-mono">04</span> Tus datos</legend>
            <div class="vg-campo">
              <label for="nombre">Tu nombre</label>
              <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
            </div>
            <div class="vg-campo">
              <label for="nota">¿Qué le notas al auto? <small>(opcional)</small></label>
              <textarea id="nota" name="nota" rows="3" maxlength="200" placeholder="Ej.: vibra el volante sobre 100 km/h, luz de motor encendida…"></textarea>
            </div>
            <p class="vg-pequeno">Esta es una demo: no guardamos tus datos. La reserva se arma en tu navegador y se envía por WhatsApp.</p>
          </fieldset>
        </div>

        <aside class="vg-caja vg-reserva__resumen" aria-label="Resumen de la reserva">
          <h2>Resumen</h2>
          <dl class="vg-totales">
            <div><dt>Servicios</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Traslado</dt><dd data-carrito-despacho>—</dd></div>
            <div class="vg-totales__total"><dt>Total desde</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <p class="vg-pequeno">El valor final se confirma con el diagnóstico; nada se cambia sin tu aprobación.</p>
          <button class="vg-btn vg-btn--bloque vg-btn--wa" type="submit"><?= demo_icono_wa() ?> Enviar reserva por WhatsApp</button>
          <div class="vg-reserva__enviada" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu reserva. ¿Quieres vaciarla?</p>
            <button class="vg-btn vg-btn--linea" type="button" data-carrito-vaciar>Vaciar la reserva</button>
          </div>
        </aside>
      </form>
    </div>
  </section>
</main>
<?php vg_cerrar();
