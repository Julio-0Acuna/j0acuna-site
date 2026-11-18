<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = mz();
$dias = mz_dias();
// Duración de cada servicio (minutos) para el tiempo estimado de la cita (reserva.js).
$duraciones = array_map(static fn (array $s): int => (int) $s['min'], $n['servicios']);

mz_abrir('reserva', 'Tu reserva', 'Reserva tu hora en ' . $n['nombre_largo'] . ': servicios elegidos, colorista, día, hora de inicio, historia de tu pelo y tiempo estimado. Se envía por WhatsApp.');
?>
<main id="contenido">
  <?php mz_titular('05', 'Último paso', 'Tu <em>reserva</em>', 'Revisa los servicios, elige colorista, día y hora. Se abre WhatsApp con todo escrito y te confirmamos en el horario del estudio.'); ?>

  <section class="mz-seccion mz-seccion--sin-arriba">
    <div class="mz-ancho mz-reserva" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar la reserva aquí necesitas JavaScript. También puedes <a href="<?= demo_e(mz_wa('Hola, quiero reservar una hora en ' . $n['nombre_largo'] . '. Servicio: ')) ?>">reservar directo por WhatsApp</a>.</p></noscript>

      <div class="mz-reserva__vacia" data-carrito-vacio>
        <p>Tu reserva está vacía. Empieza por la carta de servicios o por un tono que te guste.</p>
        <a class="mz-btn" href="<?= demo_e(MZ_BASE . 'servicios.php') ?>">Ver servicios</a>
      </div>

      <form class="mz-reserva__form" data-carrito-form hidden novalidate>
        <div class="mz-reserva__datos">
          <fieldset class="mz-caja">
            <legend><span class="mz-paso">i.</span> Servicios</legend>
            <ul class="mz-reserva__lista" data-carrito-lista aria-live="polite"></ul>
            <p class="mz-tiempo" data-mz-tiempo hidden>Tiempo estimado en el sillón: <strong data-mz-tiempo-valor></strong></p>
            <input type="hidden" name="duracion" value="" data-carrito-extra="Duración estimada" data-mz-duracion>
            <p><a class="mz-enlace" href="<?= demo_e(MZ_BASE . 'servicios.php') ?>">Agregar otro servicio</a></p>
          </fieldset>

          <fieldset class="mz-caja">
            <legend><span class="mz-paso">ii.</span> Colorista, día y hora</legend>
            <div class="mz-campo">
              <span class="mz-campo__etiqueta" id="colorista-etiqueta">¿Con quién?</span>
              <div class="mz-opciones-chip" role="radiogroup" aria-labelledby="colorista-etiqueta">
                <label class="mz-chip"><input type="radio" name="colorista" value="La primera disponible" checked data-carrito-extra="Colorista"> <span>La primera disponible</span></label>
                <?php foreach ($n['coloristas'] as $c): ?>
                <label class="mz-chip"><input type="radio" name="colorista" value="<?= demo_e($c['nombre']) ?>" data-carrito-extra="Colorista"> <span><?= demo_e($c['corto']) ?><small><?= demo_e($c['rol']) ?></small></span></label>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="mz-form-grid">
              <div class="mz-campo">
                <label for="dia">Día</label>
                <select id="dia" name="dia" required data-carrito-extra="Día">
                  <option value="">Elige un día</option>
                  <?php foreach ($dias as $f => $d): ?><option value="<?= demo_e($f) ?>"><?= demo_e($d) ?></option><?php endforeach; ?>
                </select>
              </div>
              <div class="mz-campo">
                <label for="hora-inicio">Hora de inicio</label>
                <select id="hora-inicio" name="hora_inicio" required data-carrito-extra="Hora de inicio">
                  <option value="">Elige una hora</option>
                  <?php foreach ($n['horas'] as $h): ?><option value="<?= demo_e($h) ?>"><?= demo_e($h) ?></option><?php endforeach; ?>
                </select>
              </div>
            </div>
            <p class="mz-pequeno">De martes a sábado. Para procesos de más de cuatro horas te recomendamos empezar a las 10:00 o a las 11:30.</p>
          </fieldset>

          <fieldset class="mz-caja">
            <legend><span class="mz-paso">iii.</span> Tu pelo</legend>
            <div class="mz-campo">
              <label for="historia">¿Qué tiene tu pelo hoy?</label>
              <select id="historia" name="historia" required data-carrito-extra="Historia del pelo">
                <option value="">Elige una opción</option>
                <option value="natural">Natural, sin químicos</option>
                <option value="tinte">Tinte en los últimos 6 meses</option>
                <option value="aclarado">Decoloración, mechas o balayage</option>
                <option value="alisado">Alisado o keratina</option>
                <option value="casa">Tinte de caja aplicado en casa</option>
              </select>
            </div>
            <label class="mz-check"><input type="checkbox" name="prueba" value="1" data-carrito-extra="Prueba de mechón" data-carrito-extra-valor="Sí, 48 h antes"> Quiero prueba de mechón y de sensibilidad 48 horas antes (sin costo)</label>
          </fieldset>

          <fieldset class="mz-caja" data-mz-productos>
            <legend><span class="mz-paso">iv.</span> ¿Y los productos?</legend>
            <div class="mz-opciones" role="radiogroup" aria-label="Cómo recibes los productos">
              <label class="mz-opcion"><input type="radio" name="entrega" value="retiro" checked> <span><strong>Los retiro en el estudio</strong><small>Te los dejamos listos para el día de tu hora.</small></span></label>
              <label class="mz-opcion"><input type="radio" name="entrega" value="despacho"> <span><strong>Envíenlos a mi casa</strong><small>Despacho en 24 a 48 horas hábiles, costo según comuna.</small></span></label>
            </div>
            <div class="mz-form-grid">
              <div class="mz-campo" data-solo-despacho hidden>
                <label for="comuna">Comuna</label>
                <select id="comuna" name="comuna">
                  <option value="">Elige tu comuna</option>
                  <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
                  <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mz-campo" data-solo-despacho hidden>
                <label for="direccion">Dirección</label>
                <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle, número y depto.">
              </div>
            </div>
          </fieldset>

          <fieldset class="mz-caja">
            <legend><span class="mz-paso">v.</span> Tus datos</legend>
            <div class="mz-campo">
              <label for="nombre">Tu nombre</label>
              <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
            </div>
            <div class="mz-campo">
              <label for="nota">¿Qué te gustaría lograr? <small>(opcional)</small></label>
              <textarea id="nota" name="nota" rows="3" maxlength="200" placeholder="Ej.: un rubio más frío, que no se note la raíz, tengo una foto de referencia…"></textarea>
            </div>
            <p class="mz-pequeno">Esta es una demo: no guardamos tus datos. La reserva se arma en tu navegador y se envía por WhatsApp.</p>
          </fieldset>
        </div>

        <aside class="mz-caja mz-reserva__resumen" aria-label="Resumen de la reserva">
          <h2>Resumen</h2>
          <dl class="mz-totales">
            <div><dt>Servicios y productos</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Despacho</dt><dd data-carrito-despacho>—</dd></div>
            <div class="mz-totales__total"><dt>Total desde</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <p class="mz-pequeno">El valor final se confirma en el diagnóstico, antes de empezar.</p>
          <button class="mz-btn mz-btn--bloque mz-btn--wa" type="submit"><?= demo_icono_wa() ?> Enviar reserva por WhatsApp</button>
          <div class="mz-reserva__enviada" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu reserva. ¿Quieres vaciarla?</p>
            <button class="mz-btn mz-btn--linea" type="button" data-carrito-vaciar>Vaciar la reserva</button>
          </div>
        </aside>
      </form>
    </div>
  </section>
</main>
<script type="application/json" id="mz-duraciones"><?= json_encode($duraciones, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<script src="<?= demo_e(demo_asset(MZ_BASE . 'reserva.js')) ?>" defer></script>
<?php mz_cerrar();
