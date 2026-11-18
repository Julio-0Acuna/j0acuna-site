<?php
/**
 * @var array $carrito @var array $direcciones @var array $errores @var array $previo @var array $horas @var array $cfg @var array $usuario
 * @var bool $con_fecha (modo entrega 'fecha') @var array $fechas ['AAAA-MM-DD' => 'Mañana, mié 18/11']
 * @var array $campos_extra @var array $valores_extra campos extra del checkout ('checkout_campos')
 * @var array $horas_por_fecha ['AAAA-MM-DD' => ['19:00', ...]] días con menos horas que $horas (hoy): van en data-horas
 */
$con_fecha ??= false;
$fechas ??= [];
$horas_por_fecha ??= [];
$campos_extra ??= [];
$valores_extra ??= [];
$val = static fn (string $k, string $def = ''): string => is_string($previo[$k] ?? null) ? $previo[$k] : $def;
$fechaElegida = $val('fecha_entrega', (string) array_key_first($fechas));
$entrega = $val('entrega', !empty($cfg['retiro']) ? 'retiro' : 'despacho');
$pago = $val('pago', !empty($cfg['demo']) ? 'prueba' : 'al_recibir');
$dirElegida = $val('direccion_id', $direcciones ? (string) $direcciones[0]['id'] : '0');
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="t-error" id="err-' . m_e($k) . '">' . m_e($errores[$k]) . '</p>' : '';
?>
<section class="t-pagina t-checkout">
  <h1><?= m_e(m_texto('checkout_titulo')) ?></h1>
  <?php if ($errores): ?><p class="demo-mensaje demo-mensaje--error" role="alert"><?= m_e(m_texto('revisa_campos')) ?></p><?php endif; ?>
  <form method="post" action="<?= m_e(m_url('checkout')) ?>" class="t-checkout__grid" data-checkout
        data-subtotal="<?= (int) $carrito['subtotal'] ?>"
        data-gratis="<?= (int) ($cfg['despacho_gratis_desde'] ?? 0) ?>"
        data-comunas="<?= m_e(json_encode($cfg['comunas'], JSON_UNESCAPED_UNICODE)) ?>"
        data-texto-gratis-retiro="<?= m_e(m_texto('carrito_gratis_retiro')) ?>"
        data-texto-gratis="<?= m_e(m_texto('checkout_gratis')) ?>"
        data-texto-elige-comuna="<?= m_e(m_texto('checkout_elige_comuna')) ?>">
    <?= m_csrf_campo() ?>
    <div class="t-checkout__datos">
      <fieldset class="t-caja">
        <legend><?= m_e(m_texto('checkout_como_recibes')) ?></legend>
        <div class="t-opciones">
          <?php if (!empty($cfg['retiro'])): ?>
          <label class="t-opcion"><input type="radio" name="entrega" value="retiro"<?= $entrega === 'retiro' ? ' checked' : '' ?>> <span><strong><?= m_e(m_texto('retiro_titulo')) ?></strong><small><?= m_e(m_texto('retiro_texto', 'Sin costo. Te avisamos cuando esté listo.')) ?></small></span></label>
          <?php endif; ?>
          <?php if (!empty($cfg['despacho'])): ?>
          <label class="t-opcion"><input type="radio" name="entrega" value="despacho"<?= $entrega === 'despacho' ? ' checked' : '' ?>> <span><strong><?= m_e(m_texto('despacho_titulo')) ?></strong><small><?= m_e(m_texto('checkout_costo_comuna')) ?></small></span></label>
          <?php endif; ?>
        </div>
        <?= $err('entrega') ?>

        <div class="t-despacho" data-solo-despacho>
          <?php if ($direcciones): ?>
          <p class="t-subtitulo"><?= m_e(m_texto('checkout_tus_direcciones')) ?></p>
          <div class="t-opciones">
            <?php foreach ($direcciones as $d): ?>
            <label class="t-opcion"><input type="radio" name="direccion_id" value="<?= (int) $d['id'] ?>" data-comuna="<?= m_e($d['comuna']) ?>"<?= $dirElegida === (string) $d['id'] ? ' checked' : '' ?>>
              <span><strong><?= m_e($d['etiqueta']) ?></strong><small><?= m_e($d['calle']) ?>, <?= m_e($d['comuna']) ?></small></span></label>
            <?php endforeach; ?>
            <label class="t-opcion"><input type="radio" name="direccion_id" value="0"<?= $dirElegida === '0' ? ' checked' : '' ?>> <span><strong><?= m_e(m_texto('checkout_otra_direccion')) ?></strong></span></label>
          </div>
          <?= $err('direccion_id') ?>
          <?php else: ?>
          <input type="hidden" name="direccion_id" value="0">
          <?php endif; ?>
          <div class="t-nueva-direccion" data-nueva-direccion>
            <div class="t-campo">
              <label for="calle"><?= m_e(m_texto('direccion_calle')) ?></label>
              <input id="calle" name="calle" maxlength="120" value="<?= m_e($val('calle')) ?>" autocomplete="off"<?= isset($errores['calle']) ? ' aria-invalid="true" aria-describedby="err-calle"' : '' ?>>
              <?= $err('calle') ?>
            </div>
            <div class="t-campo">
              <label for="comuna"><?= m_e(m_texto('direccion_comuna')) ?></label>
              <select id="comuna" name="comuna"<?= isset($errores['comuna']) ? ' aria-invalid="true" aria-describedby="err-comuna"' : '' ?>>
                <option value=""><?= m_e(m_texto('checkout_elige_tu_comuna')) ?></option>
                <?php foreach ($cfg['comunas'] as $comuna => $costo): ?>
                <option value="<?= m_e($comuna) ?>"<?= $val('comuna') === $comuna ? ' selected' : '' ?>><?= m_e($comuna) ?> · <?= m_e(m_clp((int) $costo)) ?></option>
                <?php endforeach; ?>
              </select>
              <?= $err('comuna') ?>
            </div>
            <div class="t-campo">
              <label for="referencia"><?= m_e(m_texto('checkout_referencia')) ?> <small><?= m_e(m_texto('opcional')) ?></small></label>
              <input id="referencia" name="referencia" maxlength="120" value="<?= m_e($val('referencia')) ?>" autocomplete="off">
            </div>
            <label class="t-check"><input type="checkbox" name="guardar_direccion" value="1"> <?= m_e(m_texto('checkout_guardar_direccion')) ?></label>
          </div>
        </div>

        <?php if ($con_fecha): ?>
        <div class="t-form-grid">
          <div class="t-campo">
            <label for="fecha_entrega"><?= m_e(m_texto('fecha_titulo')) ?></label>
            <select id="fecha_entrega" name="fecha_entrega" required<?= isset($errores['fecha_entrega']) ? ' aria-invalid="true" aria-describedby="err-fecha_entrega"' : '' ?>>
              <?php foreach ($fechas as $f => $etiqueta): ?><option value="<?= m_e($f) ?>"<?= isset($horas_por_fecha[$f]) ? ' data-horas="' . m_e(implode(' ', $horas_por_fecha[$f])) . '"' : '' ?><?= $fechaElegida === $f ? ' selected' : '' ?>><?= m_e($etiqueta) ?></option><?php endforeach; ?>
            </select>
            <?= $err('fecha_entrega') ?>
          </div>
          <div class="t-campo">
            <label for="cuando"><?= m_e(m_texto('hora_titulo')) ?></label>
            <select id="cuando" name="cuando">
              <option value=""><?= m_e(m_texto('hora_cualquiera')) ?></option>
              <?php foreach ($horas as $h): ?><option value="<?= m_e($h) ?>"<?= $val('cuando') === $h ? ' selected' : '' ?>><?= m_e(m_texto('hora_entrega_fecha', null, ['hora' => $h])) ?></option><?php endforeach; ?>
            </select>
          </div>
        </div>
        <?php if (m_texto('fecha_ayuda') !== ''): ?><p class="t-pequeno"><?= m_e(m_texto('fecha_ayuda')) ?></p><?php endif; ?>
        <?php else: ?>
        <div class="t-campo">
          <label for="cuando"><?= m_e(m_texto('cuando_titulo')) ?></label>
          <select id="cuando" name="cuando">
            <option value=""><?= m_e(m_texto('cuando_pronto')) ?></option>
            <?php foreach ($horas as $h): ?><option value="<?= m_e($h) ?>"<?= $val('cuando') === $h ? ' selected' : '' ?>><?= m_e(m_texto('hora_entrega', null, ['hora' => $h])) ?></option><?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>
        <?php m_parcial('_campos_extra', ['campos' => $campos_extra, 'valores' => $valores_extra, 'errores' => $errores]); ?>
        <div class="t-campo">
          <label for="nota"><?= m_e(m_texto('checkout_nota')) ?> <small><?= m_e(m_texto('checkout_nota_ayuda')) ?></small></label>
          <textarea id="nota" name="nota" maxlength="300" rows="2"><?= m_e($val('nota')) ?></textarea>
          <?= $err('nota') ?>
        </div>
      </fieldset>

      <fieldset class="t-caja">
        <legend><?= m_e(m_texto('checkout_pago')) ?></legend>
        <div class="t-opciones">
          <?php if (!empty($cfg['demo'])): ?>
          <label class="t-opcion"><input type="radio" name="pago" value="prueba"<?= $pago === 'prueba' ? ' checked' : '' ?>> <span><strong><?= m_e(m_texto('pago_en_linea')) ?> <em class="t-etiqueta"><?= m_e(m_texto('pago_en_linea_etiqueta')) ?></em></strong><small><?= m_e(m_texto('pago_en_linea_texto')) ?></small></span></label>
          <?php endif; ?>
          <label class="t-opcion"><input type="radio" name="pago" value="al_recibir"<?= $pago === 'al_recibir' ? ' checked' : '' ?>> <span><strong><?= m_e(m_texto('pago_al_recibir')) ?></strong><small><?= m_e(m_texto('pago_al_recibir_texto')) ?></small></span></label>
        </div>
        <?= $err('pago') ?>
      </fieldset>
    </div>

    <aside class="t-caja t-checkout__resumen" aria-label="<?= m_e(m_texto('checkout_resumen')) ?>">
      <h2><?= m_e(m_texto('checkout_tu_pedido')) ?></h2>
      <ul class="t-mini-lineas">
        <?php foreach ($carrito['lineas'] as $l): ?>
        <li><span><?= (int) $l['cantidad'] ?> × <?= m_e($l['producto']['nombre']) ?></span><span><?= m_e(m_clp($l['total'])) ?></span></li>
        <?php endforeach; ?>
      </ul>
      <p class="t-resumen__fila"><span><?= m_e(m_texto('checkout_subtotal')) ?></span><span><?= m_e(m_clp($carrito['subtotal'])) ?></span></p>
      <p class="t-resumen__fila"><span><?= m_e(m_texto('checkout_despacho')) ?></span><span data-costo-despacho><?= m_e(m_texto('checkout_segun_comuna')) ?></span></p>
      <p class="t-resumen__fila t-resumen__total"><span><?= m_e(m_texto('checkout_total')) ?></span><strong data-total><?= m_e(m_clp($carrito['subtotal'])) ?></strong></p>
      <button class="t-btn t-btn--primario t-btn--bloque" type="submit"><?= m_e(m_texto('checkout_confirmar')) ?></button>
      <p class="t-pequeno"><?= m_e(m_texto('checkout_comprando_como', null, ['nombre' => $usuario['nombre']])) ?><?= m_es_mayorista($usuario) ? ' (' . m_e(m_minuscula(m_texto('mayorista'))) . ')' : '' ?>.</p>
    </aside>
  </form>
</section>
