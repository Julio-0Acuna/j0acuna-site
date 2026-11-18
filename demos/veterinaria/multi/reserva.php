<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = cp();
$dias = cp_dias();
$waDirecto = cp_wa('Hola, quiero reservar una hora en ' . $n['nombre'] . '. Mi mascota se llama: ');

cp_abrir('reserva', 'Tu reserva', 'Reserva tu hora en ' . $n['nombre'] . ': servicios elegidos, datos de la mascota (nombre, especie y edad), profesional, día y bloque, en la clínica o a domicilio. Se envía por WhatsApp.');
?>
<main id="contenido">
  <?php cp_titular('Último paso', 'Tu reserva', 'Revisa los servicios, cuéntanos de tu mascota y elige el día. Se abre WhatsApp con todo escrito y te confirmamos la hora.'); ?>

  <section class="cp-seccion cp-seccion--sin-arriba">
    <div class="cp-ancho cp-reserva" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar la reserva aquí necesitas JavaScript. También puedes <a href="<?= demo_e($waDirecto) ?>">reservar directo por WhatsApp</a>.</p></noscript>

      <div class="cp-reserva__vacia cp-tarjeta" data-carrito-vacio>
        <h2>Aún no agregas servicios</h2>
        <p>Elige una especialidad y vuelve aquí para completar los datos de tu mascota.</p>
        <a class="cp-btn" href="<?= demo_e(CP_BASE . 'especialidades.php') ?>">Elegir especialidad</a>
      </div>

      <form class="cp-reserva__form" data-carrito-form hidden novalidate>
        <div class="cp-reserva__datos">
          <fieldset class="cp-tarjeta">
            <legend><span class="cp-paso">1</span> Servicios</legend>
            <ul class="cp-reserva__lista" data-carrito-lista aria-live="polite"></ul>
            <p><a class="cp-enlace" href="<?= demo_e(CP_BASE . 'especialidades.php') ?>">Agregar otro servicio</a></p>
          </fieldset>

          <fieldset class="cp-tarjeta">
            <legend><span class="cp-paso">2</span> Tu mascota</legend>
            <div class="cp-form-grid">
              <div class="cp-campo">
                <label for="mascota">Nombre</label>
                <input id="mascota" name="mascota" maxlength="30" required autocomplete="off" placeholder="Ej.: Olivia" data-carrito-extra="Mascota">
              </div>
              <div class="cp-campo">
                <label for="especie">Especie</label>
                <select id="especie" name="especie" required data-carrito-extra="Especie">
                  <option value="">Elige la especie</option>
                  <?php foreach ($n['especies'] as $e): ?><option value="<?= demo_e($e) ?>"><?= demo_e($e) ?></option><?php endforeach; ?>
                </select>
              </div>
              <div class="cp-campo">
                <label for="edad">Edad</label>
                <input id="edad" name="edad" maxlength="20" required autocomplete="off" placeholder="Ej.: 7 años, 4 meses" data-carrito-extra="Edad">
              </div>
              <div class="cp-campo">
                <label for="peso">Peso aproximado <small>(opcional)</small></label>
                <input id="peso" name="peso" maxlength="12" inputmode="decimal" autocomplete="off" placeholder="Ej.: 12 kg" data-carrito-extra="Peso aprox.">
              </div>
            </div>
            <label class="cp-check"><input type="checkbox" name="primera" value="1" data-carrito-extra="Primera visita" data-carrito-extra-valor="Sí, abrir ficha nueva"> Es su primera visita a Ciprés</label>
          </fieldset>

          <fieldset class="cp-tarjeta">
            <legend><span class="cp-paso">3</span> Día, bloque y lugar</legend>
            <div class="cp-form-grid">
              <div class="cp-campo">
                <label for="dia">Día</label>
                <select id="dia" name="dia" required data-carrito-extra="Día">
                  <option value="">Elige un día</option>
                  <?php foreach ($dias as $d): ?><option value="<?= demo_e($d) ?>"><?= demo_e($d) ?></option><?php endforeach; ?>
                </select>
              </div>
              <div class="cp-campo">
                <label for="bloque">Bloque</label>
                <select id="bloque" name="bloque" required data-carrito-extra="Bloque">
                  <option value="">Elige un bloque</option>
                  <?php foreach ($n['bloques'] as $b): ?><option value="<?= demo_e($b) ?>"><?= demo_e($b) ?></option><?php endforeach; ?>
                </select>
              </div>
              <div class="cp-campo cp-campo--ancho">
                <label for="profesional">Profesional <small>(opcional)</small></label>
                <select id="profesional" name="profesional" data-carrito-extra="Profesional">
                  <option value="">Sin preferencia: la primera hora disponible</option>
                  <?php foreach ($n['equipo'] as $m): ?><option value="<?= demo_e($m['nombre']) ?>"><?= demo_e($m['nombre']) ?> · <?= demo_e($m['cargo']) ?></option><?php endforeach; ?>
                </select>
              </div>
            </div>
            <p class="cp-pequeno">Los sábados atendemos hasta las 18:00 y el domingo solo urgencias. Te confirmamos la hora exacta por WhatsApp.</p>
            <div class="cp-opciones" role="radiogroup" aria-label="¿Dónde atendemos a tu mascota?">
              <label class="cp-opcion"><input type="radio" name="entrega" value="retiro" checked> <span><strong>En la clínica</strong><small>Sin costo adicional. Te esperamos en el bloque elegido.</small></span></label>
              <label class="cp-opcion"><input type="radio" name="entrega" value="despacho"> <span><strong>Visita a domicilio</strong><small>Para controles, vacunas y mascotas nerviosas. Costo según comuna.</small></span></label>
            </div>
            <div class="cp-form-grid">
              <div class="cp-campo" data-solo-despacho hidden>
                <label for="comuna">Comuna</label>
                <select id="comuna" name="comuna">
                  <option value="">Elige tu comuna</option>
                  <?php foreach ($n['comunas_domicilio'] as $comuna => $costo): ?>
                  <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="cp-campo" data-solo-despacho hidden>
                <label for="direccion">Dirección</label>
                <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle, número y depto.">
              </div>
            </div>
          </fieldset>

          <fieldset class="cp-tarjeta">
            <legend><span class="cp-paso">4</span> Tus datos</legend>
            <div class="cp-campo">
              <label for="nombre">Tu nombre</label>
              <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
            </div>
            <div class="cp-campo">
              <label for="nota">Motivo de la consulta <small>(opcional)</small></label>
              <textarea id="nota" name="nota" rows="3" maxlength="200" placeholder="Ej.: se rasca las orejas hace una semana, control de su soplo…"></textarea>
            </div>
            <p class="cp-pequeno">Esta es una demo: no guardamos tus datos. La reserva se arma en tu navegador y se envía por WhatsApp.</p>
          </fieldset>
        </div>

        <aside class="cp-tarjeta cp-reserva__resumen" aria-label="Resumen de la reserva">
          <h2>Resumen</h2>
          <dl class="cp-totales">
            <div><dt>Servicios</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Domicilio</dt><dd data-carrito-despacho>—</dd></div>
            <div class="cp-totales__total"><dt>Total desde</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <p class="cp-pequeno">El valor final se confirma en la consulta; cualquier examen extra se cotiza antes.</p>
          <button class="cp-btn cp-btn--bloque cp-btn--wa" type="submit"><?= demo_icono_wa(18) ?> Enviar reserva por WhatsApp</button>
          <div class="cp-reserva__enviada" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu reserva. ¿Quieres vaciarla?</p>
            <button class="cp-btn cp-btn--linea" type="button" data-carrito-vaciar>Vaciar la reserva</button>
          </div>
        </aside>
      </form>
    </div>
  </section>
</main>
<?php cp_cerrar();
