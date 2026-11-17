<?php
/** @var array $usuario @var array $direcciones @var array $pedidos @var array $errores @var array $previo @var array $cfg */
$val = static fn (string $k): string => is_string($previo[$k] ?? null) ? $previo[$k] : '';
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="t-error">' . m_e($errores[$k]) . '</p>' : '';
?>
<section class="t-pagina t-cuenta">
  <h1>Hola, <?= m_e($usuario['nombre']) ?></h1>
  <div class="t-caja t-cuenta__perfil">
    <dl class="t-datos">
      <div><dt>Correo</dt><dd><?= m_e($usuario['email']) ?></dd></div>
      <div><dt>Tipo de cuenta</dt><dd>
        <?php if ($usuario['rol'] === 'dueno'): ?>Dueño · <a href="<?= m_e(m_url('panel')) ?>">ir al panel</a>
        <?php elseif ($usuario['tipo'] === 'mayorista'): ?>Mayorista<?= $usuario['empresa'] !== '' ? ' · ' . m_e($usuario['empresa']) : '' ?>
          <?php if ($usuario['estado'] === 'pendiente'): ?><span class="t-etiqueta t-etiqueta--alerta">Pendiente de aprobación</span>
          <?php elseif ($usuario['estado'] === 'rechazado'): ?><span class="t-etiqueta t-etiqueta--alerta">Solicitud rechazada: compras como particular</span>
          <?php else: ?><span class="t-etiqueta">Aprobada: ves precios mayoristas</span><?php endif; ?>
        <?php else: ?>Particular<?php endif; ?>
      </dd></div>
    </dl>
    <form method="post" action="<?= m_e(m_url('salir')) ?>">
      <?= m_csrf_campo() ?>
      <button class="t-enlace" type="submit">Cerrar sesión</button>
    </form>
  </div>

  <section class="t-caja" aria-labelledby="historial">
    <h2 id="historial">Mis pedidos</h2>
    <?php if (!$pedidos): ?>
      <p>Aún no tienes pedidos. <a href="<?= m_e($cfg['url_base']) ?>#menu">Ver el menú</a></p>
    <?php else: ?>
    <ul class="t-historial">
      <?php foreach ($pedidos as $p): ?>
      <li>
        <div>
          <a href="<?= m_e(m_url('pedido', ['id' => $p['id']])) ?>"><strong><?= m_e($p['codigo']) ?></strong></a>
          <span><?= m_e(date('d/m H:i', (int) $p['creado_en'])) ?> · <?= m_e(m_clp((int) $p['total'])) ?></span>
        </div>
        <span class="t-estado t-estado--<?= m_e($p['estado']) ?>"><?= m_e(M_ESTADOS[$p['estado']]) ?></span>
        <form method="post" action="<?= m_e(m_url('cuenta')) ?>">
          <?= m_csrf_campo() ?>
          <input type="hidden" name="accion" value="repetir">
          <input type="hidden" name="pedido_id" value="<?= (int) $p['id'] ?>">
          <button class="t-btn t-btn--chico" type="submit">Repetir pedido</button>
        </form>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </section>

  <section class="t-caja" id="direcciones" aria-labelledby="dir-titulo">
    <h2 id="dir-titulo">Mis direcciones</h2>
    <?php if ($direcciones): ?>
    <ul class="t-direcciones">
      <?php foreach ($direcciones as $d): ?>
      <li>
        <div><strong><?= m_e($d['etiqueta']) ?></strong><span><?= m_e($d['calle']) ?>, <?= m_e($d['comuna']) ?><?= $d['referencia'] !== '' ? ' · ' . m_e($d['referencia']) : '' ?></span></div>
        <form method="post" action="<?= m_e(m_url('cuenta')) ?>">
          <?= m_csrf_campo() ?>
          <input type="hidden" name="accion" value="direccion_borrar">
          <input type="hidden" name="direccion_id" value="<?= (int) $d['id'] ?>">
          <button class="t-enlace" type="submit" aria-label="Eliminar dirección <?= m_e($d['etiqueta']) ?>">Eliminar</button>
        </form>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <?php if (count($direcciones) < M_MAX_DIRECCIONES): ?>
    <details class="t-agregar-dir"<?= $errores ? ' open' : '' ?>>
      <summary>Agregar dirección</summary>
      <form method="post" action="<?= m_e(m_url('cuenta')) ?>" class="t-form-grid">
        <?= m_csrf_campo() ?>
        <input type="hidden" name="accion" value="direccion_agregar">
        <div class="t-campo"><label for="etiqueta">Nombre (ej. Casa, Trabajo)</label><input id="etiqueta" name="etiqueta" maxlength="30" required value="<?= m_e($val('etiqueta')) ?>"><?= $err('etiqueta') ?></div>
        <div class="t-campo"><label for="calle">Calle y número</label><input id="calle" name="calle" maxlength="120" required value="<?= m_e($val('calle')) ?>"><?= $err('calle') ?></div>
        <div class="t-campo"><label for="comuna">Comuna</label>
          <select id="comuna" name="comuna" required><option value="">Elige</option>
            <?php foreach ($cfg['comunas'] as $comuna => $costo): ?><option value="<?= m_e($comuna) ?>"<?= $val('comuna') === $comuna ? ' selected' : '' ?>><?= m_e($comuna) ?></option><?php endforeach; ?>
          </select><?= $err('comuna') ?></div>
        <div class="t-campo"><label for="referencia">Referencia <small>(opcional)</small></label><input id="referencia" name="referencia" maxlength="120" value="<?= m_e($val('referencia')) ?>"></div>
        <button class="t-btn t-btn--primario" type="submit">Guardar dirección</button>
      </form>
    </details>
    <?php endif; ?>
  </section>
</section>
