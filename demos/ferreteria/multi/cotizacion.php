<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = pg();
// Color elegido desde la carta (?color=PG-201): queda marcado en el selector.
$colorPedido = is_string($_GET['color'] ?? null) ? strtoupper(mb_substr(trim($_GET['color']), 0, 8)) : '';
$colorPedido = isset(pg_colores()[$colorPedido]) ? $colorPedido : '';

pg_abrir('cotizacion', 'Tu cotización', 'Arma tu cotización de pintura en ' . $n['nombre'] . ': productos y formatos, color de la carta, metros cuadrados, visita de colorista y retiro o despacho. Se envía por WhatsApp.');
?>
<main id="contenido">
  <?php pg_titular('Último paso', 'Tu <em>cotización</em>', 'Revisa los productos, elige el color y cuéntanos qué vas a pintar. Se abre WhatsApp con todo escrito y te respondemos con el total y la hora de retiro.'); ?>

  <section class="pg-seccion pg-seccion--sin-arriba">
    <div class="pg-ancho pg-cotizacion" data-carrito>
      <noscript><p class="demo-mensaje demo-mensaje--info">Para armar la cotización aquí necesitas JavaScript. También puedes <a href="<?= demo_e(pg_wa('Hola, quiero cotizar pintura en ' . $n['nombre'] . '.')) ?>">cotizar directo por WhatsApp</a>.</p></noscript>

      <?php if ($colorPedido !== ''): ?>
      <p class="pg-color-elegido"><?= pg_chip($colorPedido, 'pg-chip--mini', false) ?> <span>Elegiste <strong><?= demo_e(pg_colores()[$colorPedido][0]) ?></strong> (<?= demo_e($colorPedido) ?>). Ahora agrega la pintura en la que lo quieres.</span></p>
      <?php endif; ?>

      <div class="pg-tarjeta pg-cotizacion__vacia" data-carrito-vacio>
        <p>Tu cotización está vacía. Agrega pinturas o herramientas desde el catálogo.</p>
        <a class="pg-btn" href="<?= demo_e(PG_BASE . 'catalogo.php') ?>">Ir al catálogo</a>
      </div>

      <form class="pg-cotizacion__form" data-carrito-form hidden novalidate>
        <div class="pg-cotizacion__datos">
          <fieldset class="pg-tarjeta">
            <legend><span class="pg-mono">01</span> Productos</legend>
            <ul class="pg-cotizacion__lista" data-carrito-lista aria-live="polite"></ul>
            <p><a class="pg-enlace" href="<?= demo_e(PG_BASE . 'catalogo.php') ?>">Agregar otro producto</a></p>
          </fieldset>

          <fieldset class="pg-tarjeta">
            <legend><span class="pg-mono">02</span> Color y superficie</legend>
            <div class="pg-form-grid">
              <div class="pg-campo pg-campo--ancho">
                <label for="color">Color de la carta</label>
                <select id="color" name="color" data-carrito-extra="Color">
                  <option value="">Blanco o sin teñir</option>
                  <?php foreach ($n['colores'] as $familia => $lista): ?>
                  <optgroup label="<?= demo_e($familia) ?>">
                    <?php foreach ($lista as [$codigo, $nombre]): ?>
                    <option value="<?= demo_e($codigo) ?>"<?= $colorPedido === $codigo ? ' selected' : '' ?>><?= demo_e($codigo . ' · ' . $nombre) ?></option>
                    <?php endforeach; ?>
                  </optgroup>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="pg-campo">
                <label for="superficie">¿Qué vas a pintar?</label>
                <select id="superficie" name="superficie" data-carrito-extra="Superficie">
                  <option value="">Elige una opción</option>
                  <?php foreach ($n['superficies'] as $s): ?><option value="<?= demo_e($s) ?>"><?= demo_e($s) ?></option><?php endforeach; ?>
                </select>
              </div>
              <div class="pg-campo">
                <label for="metros">Metros cuadrados aprox.</label>
                <input id="metros" name="metros" inputmode="decimal" maxlength="6" autocomplete="off" placeholder="Ej.: 40" data-carrito-extra="Metros cuadrados">
              </div>
            </div>
            <label class="pg-check"><input type="checkbox" name="colorista" value="1" data-carrito-extra="Visita de colorista" data-carrito-extra-valor="Sí, quiero agendarla ($19.990)"> Quiero una visita de colorista a domicilio ($19.990, se descuenta si compras sobre $120.000)</label>
            <p class="pg-pequeno">¿No sabes los metros? Usa la <a href="<?= demo_e(PG_BASE . 'colores.php#calculadora') ?>">calculadora de pintura</a>.</p>
          </fieldset>

          <fieldset class="pg-tarjeta">
            <legend><span class="pg-mono">03</span> Retiro o despacho</legend>
            <div class="pg-opciones" role="radiogroup" aria-label="¿Cómo lo recibes?">
              <label class="pg-opcion"><input type="radio" name="entrega" value="retiro" checked> <span><strong>Retiro en la tienda</strong><small>Sin costo. Te avisamos cuando esté preparada.</small></span></label>
              <label class="pg-opcion"><input type="radio" name="entrega" value="despacho"> <span><strong>Despacho</strong><small>Mismo día si cotizas antes de las 14:00.</small></span></label>
            </div>
            <div class="pg-form-grid">
              <div class="pg-campo" data-solo-despacho hidden>
                <label for="comuna">Comuna</label>
                <select id="comuna" name="comuna">
                  <option value="">Elige tu comuna</option>
                  <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
                  <option value="<?= demo_e($comuna) ?>"><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="pg-campo" data-solo-despacho hidden>
                <label for="direccion">Dirección</label>
                <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle, número y depto.">
              </div>
            </div>
          </fieldset>

          <fieldset class="pg-tarjeta">
            <legend><span class="pg-mono">04</span> Tus datos</legend>
            <div class="pg-campo">
              <label for="nombre">Tu nombre</label>
              <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required>
            </div>
            <div class="pg-campo">
              <label for="nota">Algo más <small>(opcional)</small></label>
              <textarea id="nota" name="nota" rows="3" maxlength="200" placeholder="Ej.: el muro tiene manchas de humedad, ¿qué me recomiendan?"></textarea>
            </div>
            <p class="pg-pequeno">Esta es una demo: no guardamos tus datos. La cotización se arma en tu navegador y se envía por WhatsApp.</p>
          </fieldset>
        </div>

        <aside class="pg-tarjeta pg-cotizacion__resumen" aria-label="Resumen de la cotización">
          <h2>Resumen</h2>
          <dl class="pg-totales">
            <div><dt>Productos</dt><dd data-carrito-subtotal>$0</dd></div>
            <div><dt>Despacho</dt><dd data-carrito-despacho>—</dd></div>
            <div class="pg-totales__total"><dt>Total</dt><dd data-carrito-total>$0</dd></div>
          </dl>
          <p class="pg-pequeno">Precios con IVA. La preparación del color no tiene costo.</p>
          <button class="pg-btn pg-btn--bloque pg-btn--wa" type="submit"><?= demo_icono_wa() ?> Enviar cotización por WhatsApp</button>
          <div class="pg-cotizacion__enviada" data-carrito-enviado hidden>
            <p>¡Listo! Se abrió WhatsApp con tu cotización. ¿Quieres vaciarla?</p>
            <button class="pg-btn pg-btn--linea" type="button" data-carrito-vaciar>Vaciar la cotización</button>
          </div>
        </aside>
      </form>
    </div>
  </section>
</main>
<?php pg_cerrar();
