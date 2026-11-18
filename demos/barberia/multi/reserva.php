<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = eg();
$dias = eg_dias();
$bebidas = array_merge(array_column($n['bebidas'], 0), $n['bebidas_mas']);

eg_abrir('reserva', 'Tu reserva', 'Reserva tu hora en ' . $n['nombre'] . ': servicios elegidos, barbero, día y bloque, y bebida de cortesía. Se envía por WhatsApp.');
?>
<main id="contenido">
  <?php eg_titular('Último paso', 'Tu reserva', 'Revise sus servicios, elija barbero, día y bloque. Se abre WhatsApp con todo escrito y le confirmamos la hora.'); ?>

  <section class="eg-seccion eg-seccion--sin-arriba">
    <div class="eg-ancho eg-reserva" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar la reserva aquí necesita JavaScript. También puede <a href="<?= demo_e(eg_wa('Hola, quiero reservar una hora en ' . $n['nombre'] . '.')) ?>">reservar directo por WhatsApp</a>.</p></noscript>

      <div class="eg-reserva__vacia eg-caja" data-carrito-vacio>
        <p>Aún no agrega servicios a su reserva.</p>
        <a class="eg-btn" href="<?= demo_e(EG_BASE . 'servicios.php') ?>">Ver la carta</a>
      </div>

      <form class="eg-reserva__form" data-carrito-form hidden novalidate>
        <input type="hidden" name="entrega" value="retiro">
        <div hidden data-solo-despacho>
          <select name="comuna" aria-label="Comuna"><option value=""></option></select>
          <input name="direccion" type="hidden" value="">
        </div>
        <div class="eg-reserva__datos">
          <fieldset class="eg-caja">
            <legend><span class="eg-num">I</span> Servicios y productos</legend>
            <ul class="eg-reserva__lista" data-carrito-lista aria-live="polite"></ul>
            <p><a class="eg-enlace" href="<?= demo_e(EG_BASE . 'servicios.php') ?>">Agregar otro servicio</a></p>
            <p class="eg-pequeno">Los productos para llevar se los entregamos en el salón, el día de su hora.</p>
          </fieldset>

          <fieldset class="eg-caja">
            <legend><span class="eg-num">II</span> Barbero, día y bloque</legend>
            <div class="eg-form-grid">
              <div class="eg-campo eg-campo--ancho">
                <label for="barbero">Barbero</label>
                <select id="barbero" name="barbero" required data-carrito-extra="Barbero">
                  <option value="">Elija con quién</option>
                  <?php foreach ($n['barberos'] as $id => $b): ?>
                  <option value="<?= demo_e($id) ?>"><?= demo_e($b['nombre']) ?> · <?= demo_e($b['oficio']) ?></option>
                  <?php endforeach; ?>
                  <option value="cualquiera">El primero disponible</option>
                </select>
              </div>
              <div class="eg-campo">
                <label for="dia">Día</label>
                <select id="dia" name="dia" required data-carrito-extra="Día">
                  <option value="">Elija un día</option>
                  <?php foreach ($dias as $d): ?><option value="<?= demo_e($d) ?>"><?= demo_e($d) ?></option><?php endforeach; ?>
                </select>
              </div>
              <div class="eg-campo">
                <label for="bloque">Bloque</label>
                <select id="bloque" name="bloque" required data-carrito-extra="Bloque">
                  <option value="">Elija un bloque</option>
                  <?php foreach ($n['bloques'] as $b): ?><option value="<?= demo_e($b) ?>"><?= demo_e($b) ?></option><?php endforeach; ?>
                </select>
              </div>
            </div>
            <p class="eg-pequeno">Domingo cerrado. Los sábados, el último bloque es a las 17:00. Si su barbero no atiende ese día, le ofrecemos otro por WhatsApp.</p>
          </fieldset>

          <fieldset class="eg-caja">
            <legend><span class="eg-num">III</span> Sala de espera y sus datos</legend>
            <div class="eg-campo">
              <label for="bebida">Bebida de cortesía <small>(sin alcohol)</small></label>
              <select id="bebida" name="bebida" data-carrito-extra="Bebida de cortesía">
                <option value="">Decido al llegar</option>
                <?php foreach ($bebidas as $b): ?><option value="<?= demo_e($b) ?>"><?= demo_e($b) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="eg-campo">
              <label class="eg-check"><input type="checkbox" name="primera" value="1" data-carrito-extra="Primera visita"> Es mi primera vez en El Gabinete</label>
            </div>
            <div class="eg-campo">
              <label for="nombre">Su nombre</label>
              <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
            </div>
            <div class="eg-campo">
              <label for="nota">¿Algo que debamos saber? <small>(opcional)</small></label>
              <textarea id="nota" name="nota" rows="3" maxlength="200" placeholder="Ej.: degradado bajo, piel sensible a la navaja…"></textarea>
            </div>
            <p class="eg-pequeno">Esta es una demo: no guardamos sus datos. La reserva se arma en su navegador y se envía por WhatsApp.</p>
          </fieldset>
        </div>

        <aside class="eg-caja eg-reserva__resumen" aria-label="Resumen de la reserva">
          <h2>Resumen</h2>
          <dl class="eg-totales">
            <div><dt>Servicios y productos</dt><dd data-carrito-subtotal>$0</dd></div>
            <div class="eg-totales__total"><dt>Total a pagar en el salón</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <span data-carrito-despacho hidden></span>
          <p class="eg-pequeno">Se paga en el salón, después del servicio. Efectivo, débito, crédito o transferencia.</p>
          <button class="eg-btn eg-btn--bloque" type="submit"><?= demo_icono_wa() ?> Enviar reserva por WhatsApp</button>
          <div class="eg-reserva__enviada" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con su reserva. ¿Quiere vaciarla?</p>
            <button class="eg-btn eg-btn--linea" type="button" data-carrito-vaciar>Vaciar la reserva</button>
          </div>
        </aside>
      </form>
    </div>
  </section>
</main>
<?php eg_cerrar();
