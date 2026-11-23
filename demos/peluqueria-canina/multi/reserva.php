<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = br();
$dias = br_dias();
$waDirecto = br_wa('Hola, quiero reservar en ' . $n['nombre'] . '. Mi perro se llama: ');

br_abrir('reserva', 'Tu reserva', 'Reserva en ' . $n['nombre'] . ': rituales elegidos, datos de tu perro (nombre, raza, tamaño, pelo y carácter), groomer, día, hora y traslado ida y vuelta. Se envía por WhatsApp.');
?>
<main id="contenido">
  <?php br_titular('06', 'Último paso', 'Tu <em>reserva</em>', 'Revisa los rituales, cuéntanos de tu perro y elige el día. Se abre WhatsApp con todo escrito y te confirmamos la hora.'); ?>

  <section class="br-seccion br-seccion--sin-arriba">
    <div class="br-ancho br-reserva" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar la reserva aquí necesitas JavaScript. También puedes <a href="<?= demo_e($waDirecto) ?>">reservar directo por WhatsApp</a>.</p></noscript>

      <div class="br-reserva__vacia br-tarjeta" data-carrito-vacio>
        <h2>Tu reserva está <em>vacía</em></h2>
        <p>Elige un ritual en la carta y vuelve aquí para contarnos de tu perro.</p>
        <a class="br-btn" href="<?= demo_e(BR_BASE . 'rituales.php') ?>">Ver la carta</a>
      </div>

      <form class="br-reserva__form" data-carrito-form hidden novalidate>
        <div class="br-reserva__datos">
          <fieldset class="br-tarjeta">
            <legend><span class="br-paso">I</span> Rituales</legend>
            <ul class="br-reserva__lista" data-carrito-lista aria-live="polite"></ul>
            <p><a class="br-enlace" href="<?= demo_e(BR_BASE . 'rituales.php') ?>">Agregar otro ritual o un producto de la botica</a></p>
          </fieldset>

          <fieldset class="br-tarjeta">
            <legend><span class="br-paso">II</span> Tu perro</legend>
            <div class="br-form-grid">
              <div class="br-campo">
                <label for="perro">Nombre</label>
                <input id="perro" name="perro" maxlength="30" required autocomplete="off" placeholder="Ej.: Olivia" data-carrito-extra="Perro">
              </div>
              <div class="br-campo">
                <label for="raza">Raza <small>(opcional)</small></label>
                <input id="raza" name="raza" maxlength="40" autocomplete="off" placeholder="Ej.: caniche, golden, quiltro" data-carrito-extra="Raza">
              </div>
              <div class="br-campo">
                <label for="tamano">Tamaño</label>
                <select id="tamano" name="tamano" required data-carrito-extra="Tamaño">
                  <option value="">Elige el tamaño</option>
                  <?php foreach ($n['tamanos'] as $t): ?><option value="<?= demo_e($t) ?>"><?= demo_e($t) ?></option><?php endforeach; ?>
                </select>
              </div>
              <div class="br-campo">
                <label for="pelo">Pelo</label>
                <select id="pelo" name="pelo" data-carrito-extra="Pelo">
                  <option value="">Elige el tipo de pelo</option>
                  <?php foreach ($n['pelos'] as $p): ?><option value="<?= demo_e($p) ?>"><?= demo_e($p) ?></option><?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="br-campo">
              <span class="br-campo__etiqueta" id="caracter-etiqueta">¿Cómo es en el baño?</span>
              <div class="br-chips" role="radiogroup" aria-labelledby="caracter-etiqueta">
                <?php foreach (['Tranquilo', 'Nervioso con el secador', 'Primera vez en un spa'] as $i => $c): ?>
                <label class="br-chip"><input type="radio" name="caracter" value="<?= demo_e($c) ?>"<?= $i === 0 ? ' checked' : '' ?> data-carrito-extra="Carácter"> <span><?= demo_e($c) ?></span></label>
                <?php endforeach; ?>
              </div>
            </div>
            <label class="br-check"><input type="checkbox" name="piel" value="1" data-carrito-extra="Piel sensible" data-carrito-extra-valor="Sí, usar productos para piel sensible"> Tiene la piel sensible o alergias</label>
          </fieldset>

          <fieldset class="br-tarjeta">
            <legend><span class="br-paso">III</span> Día, hora y llegada</legend>
            <div class="br-form-grid">
              <div class="br-campo">
                <label for="dia">Día</label>
                <select id="dia" name="dia" required data-carrito-extra="Día">
                  <option value="">Elige un día</option>
                  <?php foreach ($dias as $d): ?><option value="<?= demo_e($d) ?>"><?= demo_e($d) ?></option><?php endforeach; ?>
                </select>
              </div>
              <div class="br-campo">
                <label for="hora-inicio">Hora de inicio</label>
                <select id="hora-inicio" name="hora_inicio" required data-carrito-extra="Hora">
                  <option value="">Elige una hora</option>
                  <?php foreach ($n['horas'] as $h): ?><option value="<?= demo_e($h) ?>"><?= demo_e($h) ?></option><?php endforeach; ?>
                </select>
              </div>
              <div class="br-campo br-campo--ancho">
                <label for="groomer">Groomer <small>(opcional)</small></label>
                <select id="groomer" name="groomer" data-carrito-extra="Groomer">
                  <option value="">Sin preferencia: según el ritual y la raza</option>
                  <?php foreach ($n['equipo'] as $p): ?><option value="<?= demo_e($p['nombre']) ?>"><?= demo_e($p['nombre']) ?> · <?= demo_e(mb_strtolower($p['rol'])) ?></option><?php endforeach; ?>
                </select>
              </div>
            </div>
            <p class="br-pequeno">Atendemos de martes a sábado. Los gatos, los jueves en la mañana. Te confirmamos la hora por WhatsApp.</p>
            <div class="br-opciones" role="radiogroup" aria-label="¿Cómo llega al spa?">
              <label class="br-opcion"><input type="radio" name="entrega" value="retiro" checked> <span><strong>Lo llevo yo</strong><small>Sin costo. Llega cinco minutos antes para conversar.</small></span></label>
              <label class="br-opcion"><input type="radio" name="entrega" value="despacho"> <span><strong>Traslado ida y vuelta</strong><small>Lo buscamos y te lo devolvemos. Valor según comuna.</small></span></label>
            </div>
            <div class="br-form-grid">
              <div class="br-campo" data-solo-despacho hidden>
                <label for="comuna">Comuna</label>
                <select id="comuna" name="comuna">
                  <option value="">Elige tu comuna</option>
                  <?php foreach ($n['traslado'] as $comuna => $costo): ?>
                  <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="br-campo" data-solo-despacho hidden>
                <label for="direccion">Dirección</label>
                <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle, número y depto.">
              </div>
            </div>
          </fieldset>

          <fieldset class="br-tarjeta">
            <legend><span class="br-paso">IV</span> Tus datos</legend>
            <div class="br-campo">
              <label for="nombre">Tu nombre</label>
              <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
            </div>
            <div class="br-campo">
              <label for="nota">¿Algo más que debamos saber? <small>(opcional)</small></label>
              <textarea id="nota" name="nota" rows="3" maxlength="200" placeholder="Ej.: el corte más corto en las patas, que no le toquen la cola…"></textarea>
            </div>
            <p class="br-pequeno">Esta es una demo: no guardamos tus datos. La reserva se arma en tu navegador y se envía por WhatsApp.</p>
          </fieldset>
        </div>

        <aside class="br-tarjeta br-reserva__resumen" aria-label="Resumen de la reserva">
          <h2>Resumen</h2>
          <dl class="br-totales">
            <div><dt>Rituales</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Traslado</dt><dd data-carrito-despacho>—</dd></div>
            <div class="br-totales__total"><dt>Total</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <p class="br-gratis" data-carrito-gratis hidden></p>
          <p class="br-pequeno">Pagas al terminar, con débito, crédito o transferencia.</p>
          <button class="br-btn br-btn--bloque" type="submit"><?= demo_icono_wa(18) ?> Enviar reserva por WhatsApp</button>
          <div class="br-reserva__enviada" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu reserva. ¿Quieres vaciarla?</p>
            <button class="br-btn br-btn--linea" type="button" data-carrito-vaciar>Vaciar la reserva</button>
          </div>
        </aside>
      </form>
    </div>
  </section>
</main>
<?php br_cerrar();
