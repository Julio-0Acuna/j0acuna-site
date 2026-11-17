<?php
/** @var array $carrito @var array $direcciones @var array $errores @var array $previo @var array $horas @var array $cfg @var array $usuario */
$val = static fn (string $k, string $def = ''): string => is_string($previo[$k] ?? null) ? $previo[$k] : $def;
$entrega = $val('entrega', !empty($cfg['retiro']) ? 'retiro' : 'despacho');
$pago = $val('pago', !empty($cfg['demo']) ? 'prueba' : 'al_recibir');
$dirElegida = $val('direccion_id', $direcciones ? (string) $direcciones[0]['id'] : '0');
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="t-error" id="err-' . m_e($k) . '">' . m_e($errores[$k]) . '</p>' : '';
?>
<section class="t-pagina t-checkout">
  <h1>Finalizar compra</h1>
  <?php if ($errores): ?><p class="demo-mensaje demo-mensaje--error" role="alert">Revisa los campos marcados.</p><?php endif; ?>
  <form method="post" action="<?= m_e(m_url('checkout')) ?>" class="t-checkout__grid" data-checkout
        data-subtotal="<?= (int) $carrito['subtotal'] ?>"
        data-gratis="<?= (int) ($cfg['despacho_gratis_desde'] ?? 0) ?>"
        data-comunas="<?= m_e(json_encode($cfg['comunas'], JSON_UNESCAPED_UNICODE)) ?>">
    <?= m_csrf_campo() ?>
    <div class="t-checkout__datos">
      <fieldset class="t-caja">
        <legend>¿Cómo lo recibes?</legend>
        <div class="t-opciones">
          <?php if (!empty($cfg['retiro'])): ?>
          <label class="t-opcion"><input type="radio" name="entrega" value="retiro"<?= $entrega === 'retiro' ? ' checked' : '' ?>> <span><strong>Retiro en el local</strong><small><?= m_e(m_texto('retiro_texto', 'Sin costo. Te avisamos cuando esté listo.')) ?></small></span></label>
          <?php endif; ?>
          <?php if (!empty($cfg['despacho'])): ?>
          <label class="t-opcion"><input type="radio" name="entrega" value="despacho"<?= $entrega === 'despacho' ? ' checked' : '' ?>> <span><strong>Despacho a domicilio</strong><small>Costo según comuna.</small></span></label>
          <?php endif; ?>
        </div>
        <?= $err('entrega') ?>

        <div class="t-despacho" data-solo-despacho>
          <?php if ($direcciones): ?>
          <p class="t-subtitulo">Tus direcciones</p>
          <div class="t-opciones">
            <?php foreach ($direcciones as $d): ?>
            <label class="t-opcion"><input type="radio" name="direccion_id" value="<?= (int) $d['id'] ?>" data-comuna="<?= m_e($d['comuna']) ?>"<?= $dirElegida === (string) $d['id'] ? ' checked' : '' ?>>
              <span><strong><?= m_e($d['etiqueta']) ?></strong><small><?= m_e($d['calle']) ?>, <?= m_e($d['comuna']) ?></small></span></label>
            <?php endforeach; ?>
            <label class="t-opcion"><input type="radio" name="direccion_id" value="0"<?= $dirElegida === '0' ? ' checked' : '' ?>> <span><strong>Otra dirección</strong></span></label>
          </div>
          <?= $err('direccion_id') ?>
          <?php else: ?>
          <input type="hidden" name="direccion_id" value="0">
          <?php endif; ?>
          <div class="t-nueva-direccion" data-nueva-direccion>
            <div class="t-campo">
              <label for="calle">Calle y número</label>
              <input id="calle" name="calle" maxlength="120" value="<?= m_e($val('calle')) ?>" autocomplete="off"<?= isset($errores['calle']) ? ' aria-invalid="true" aria-describedby="err-calle"' : '' ?>>
              <?= $err('calle') ?>
            </div>
            <div class="t-campo">
              <label for="comuna">Comuna</label>
              <select id="comuna" name="comuna"<?= isset($errores['comuna']) ? ' aria-invalid="true" aria-describedby="err-comuna"' : '' ?>>
                <option value="">Elige tu comuna</option>
                <?php foreach ($cfg['comunas'] as $comuna => $costo): ?>
                <option value="<?= m_e($comuna) ?>"<?= $val('comuna') === $comuna ? ' selected' : '' ?>><?= m_e($comuna) ?> · <?= m_e(m_clp((int) $costo)) ?></option>
                <?php endforeach; ?>
              </select>
              <?= $err('comuna') ?>
            </div>
            <div class="t-campo">
              <label for="referencia">Depto., block o referencia <small>(opcional)</small></label>
              <input id="referencia" name="referencia" maxlength="120" value="<?= m_e($val('referencia')) ?>" autocomplete="off">
            </div>
            <label class="t-check"><input type="checkbox" name="guardar_direccion" value="1"> Guardar esta dirección en mi cuenta</label>
          </div>
        </div>

        <div class="t-campo">
          <label for="cuando">¿Para cuándo?</label>
          <select id="cuando" name="cuando">
            <option value="">Lo antes posible</option>
            <?php foreach ($horas as $h): ?><option value="<?= m_e($h) ?>"<?= $val('cuando') === $h ? ' selected' : '' ?>>Hoy a las <?= m_e($h) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="t-campo">
          <label for="nota">Nota <small>(opcional, máx. 300 caracteres)</small></label>
          <textarea id="nota" name="nota" maxlength="300" rows="2"><?= m_e($val('nota')) ?></textarea>
          <?= $err('nota') ?>
        </div>
      </fieldset>

      <fieldset class="t-caja">
        <legend>Pago</legend>
        <div class="t-opciones">
          <?php if (!empty($cfg['demo'])): ?>
          <label class="t-opcion"><input type="radio" name="pago" value="prueba"<?= $pago === 'prueba' ? ' checked' : '' ?>> <span><strong>Pago en línea <em class="t-etiqueta">de prueba</em></strong><small>Pantalla de pago simulada: no se cobra nada.</small></span></label>
          <?php endif; ?>
          <label class="t-opcion"><input type="radio" name="pago" value="al_recibir"<?= $pago === 'al_recibir' ? ' checked' : '' ?>> <span><strong>Pagar al recibir</strong><small>Efectivo, débito o crédito.</small></span></label>
        </div>
        <?= $err('pago') ?>
      </fieldset>
    </div>

    <aside class="t-caja t-checkout__resumen" aria-label="Resumen del pedido">
      <h2>Tu pedido</h2>
      <ul class="t-mini-lineas">
        <?php foreach ($carrito['lineas'] as $l): ?>
        <li><span><?= (int) $l['cantidad'] ?> × <?= m_e($l['producto']['nombre']) ?></span><span><?= m_e(m_clp($l['total'])) ?></span></li>
        <?php endforeach; ?>
      </ul>
      <p class="t-resumen__fila"><span>Subtotal</span><span><?= m_e(m_clp($carrito['subtotal'])) ?></span></p>
      <p class="t-resumen__fila"><span>Despacho</span><span data-costo-despacho>Según comuna</span></p>
      <p class="t-resumen__fila t-resumen__total"><span>Total</span><strong data-total><?= m_e(m_clp($carrito['subtotal'])) ?></strong></p>
      <button class="t-btn t-btn--primario t-btn--bloque" type="submit">Confirmar pedido</button>
      <p class="t-pequeno">Comprando como <?= m_e($usuario['nombre']) ?><?= m_es_mayorista($usuario) ? ' (mayorista)' : '' ?>.</p>
    </aside>
  </form>
</section>
