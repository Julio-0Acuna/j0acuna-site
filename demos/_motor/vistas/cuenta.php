<?php
/** @var array $usuario @var array $direcciones @var array $pedidos @var array $errores @var array $previo @var array $cfg */
$val = static fn (string $k): string => is_string($previo[$k] ?? null) ? $previo[$k] : '';
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="t-error">' . m_e($errores[$k]) . '</p>' : '';
?>
<section class="t-pagina t-cuenta">
  <h1><?= m_e(m_texto('cuenta_hola', null, ['nombre' => $usuario['nombre']])) ?></h1>
  <div class="t-caja t-cuenta__perfil">
    <dl class="t-datos">
      <div><dt><?= m_e(m_texto('correo')) ?></dt><dd><?= m_e($usuario['email']) ?></dd></div>
      <div><dt><?= m_e(m_texto('cuenta_tipo')) ?></dt><dd>
        <?php if ($usuario['rol'] === 'dueno'): ?><?= m_e(m_texto('cuenta_dueno')) ?> · <a href="<?= m_e(m_url('panel')) ?>"><?= m_e(m_texto('cuenta_ir_panel')) ?></a>
        <?php elseif ($usuario['tipo'] === 'mayorista'): ?><?= m_e(m_texto('mayorista')) ?><?= $usuario['empresa'] !== '' ? ' · ' . m_e($usuario['empresa']) : '' ?>
          <?php if ($usuario['estado'] === 'pendiente'): ?><span class="t-etiqueta t-etiqueta--alerta"><?= m_e(m_texto('cuenta_pendiente')) ?></span>
          <?php elseif ($usuario['estado'] === 'rechazado'): ?><span class="t-etiqueta t-etiqueta--alerta"><?= m_e(m_texto('cuenta_rechazada')) ?></span>
          <?php else: ?><span class="t-etiqueta"><?= m_e(m_texto('cuenta_aprobada')) ?></span><?php endif; ?>
        <?php else: ?><?= m_e(m_texto('particular')) ?><?php endif; ?>
      </dd></div>
    </dl>
    <form method="post" action="<?= m_e(m_url('salir')) ?>">
      <?= m_csrf_campo() ?>
      <button class="t-enlace" type="submit"><?= m_e(m_texto('cuenta_salir')) ?></button>
    </form>
  </div>

  <section class="t-caja" aria-labelledby="historial">
    <h2 id="historial"><?= m_e(m_texto('cuenta_pedidos')) ?></h2>
    <?php if (!$pedidos): ?>
      <p><?= m_e(m_texto('cuenta_sin_pedidos')) ?> <a href="<?= m_e($cfg['url_base']) ?>#menu"><?= m_e(m_texto('ver_catalogo')) ?></a></p>
    <?php else: ?>
    <ul class="t-historial">
      <?php foreach ($pedidos as $p): ?>
      <li>
        <div>
          <a href="<?= m_e(m_url('pedido', ['id' => $p['id']])) ?>"><strong><?= m_e($p['codigo']) ?></strong></a>
          <span><?= m_e(date('d/m H:i', (int) $p['creado_en'])) ?> · <?= m_e(m_clp((int) $p['total'])) ?><?= ($p['fecha_entrega'] ?? '') !== '' ? ' · ' . m_e(m_texto('cuenta_para_fecha', null, ['fecha' => m_fecha_texto($p['fecha_entrega'], false)])) : '' ?></span>
          <?php if ($extras = m_pedido_extras($p)): ?><span class="t-historial__extras"><?= m_e(implode(' · ', array_map(static fn (array $x): string => $x[0] . ': ' . $x[1], $extras))) ?></span><?php endif; ?>
        </div>
        <span class="t-estado t-estado--<?= m_e($p['estado']) ?>"><?= m_e(m_estado_texto($p['estado'])) ?></span>
        <form method="post" action="<?= m_e(m_url('cuenta')) ?>">
          <?= m_csrf_campo() ?>
          <input type="hidden" name="accion" value="repetir">
          <input type="hidden" name="pedido_id" value="<?= (int) $p['id'] ?>">
          <button class="t-btn t-btn--chico" type="submit"><?= m_e(m_texto('cuenta_repetir')) ?></button>
        </form>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </section>

  <section class="t-caja" id="direcciones" aria-labelledby="dir-titulo">
    <h2 id="dir-titulo"><?= m_e(m_texto('cuenta_direcciones')) ?></h2>
    <?php if ($direcciones): ?>
    <ul class="t-direcciones">
      <?php foreach ($direcciones as $d): ?>
      <li>
        <div><strong><?= m_e($d['etiqueta']) ?></strong><span><?= m_e($d['calle']) ?>, <?= m_e($d['comuna']) ?><?= $d['referencia'] !== '' ? ' · ' . m_e($d['referencia']) : '' ?></span></div>
        <form method="post" action="<?= m_e(m_url('cuenta')) ?>">
          <?= m_csrf_campo() ?>
          <input type="hidden" name="accion" value="direccion_borrar">
          <input type="hidden" name="direccion_id" value="<?= (int) $d['id'] ?>">
          <button class="t-enlace" type="submit" aria-label="<?= m_e(m_texto('cuenta_eliminar_aria', null, ['etiqueta' => $d['etiqueta']])) ?>"><?= m_e(m_texto('cuenta_eliminar')) ?></button>
        </form>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <?php if (count($direcciones) < M_MAX_DIRECCIONES): ?>
    <details class="t-agregar-dir"<?= $errores ? ' open' : '' ?>>
      <summary><?= m_e(m_texto('cuenta_agregar_direccion')) ?></summary>
      <form method="post" action="<?= m_e(m_url('cuenta')) ?>" class="t-form-grid">
        <?= m_csrf_campo() ?>
        <input type="hidden" name="accion" value="direccion_agregar">
        <div class="t-campo"><label for="etiqueta"><?= m_e(m_texto('cuenta_dir_nombre')) ?></label><input id="etiqueta" name="etiqueta" maxlength="30" required value="<?= m_e($val('etiqueta')) ?>"><?= $err('etiqueta') ?></div>
        <div class="t-campo"><label for="calle"><?= m_e(m_texto('direccion_calle')) ?></label><input id="calle" name="calle" maxlength="120" required value="<?= m_e($val('calle')) ?>"><?= $err('calle') ?></div>
        <div class="t-campo"><label for="comuna"><?= m_e(m_texto('direccion_comuna')) ?></label>
          <select id="comuna" name="comuna" required><option value=""><?= m_e(m_texto('cuenta_elige')) ?></option>
            <?php foreach ($cfg['comunas'] as $comuna => $costo): ?><option value="<?= m_e($comuna) ?>"<?= $val('comuna') === $comuna ? ' selected' : '' ?>><?= m_e($comuna) ?></option><?php endforeach; ?>
          </select><?= $err('comuna') ?></div>
        <div class="t-campo"><label for="referencia"><?= m_e(m_texto('cuenta_referencia')) ?> <small><?= m_e(m_texto('opcional')) ?></small></label><input id="referencia" name="referencia" maxlength="120" value="<?= m_e($val('referencia')) ?>"></div>
        <button class="t-btn t-btn--primario" type="submit"><?= m_e(m_texto('cuenta_guardar_direccion')) ?></button>
      </form>
    </details>
    <?php endif; ?>
  </section>
</section>
