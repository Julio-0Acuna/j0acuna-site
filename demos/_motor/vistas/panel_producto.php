<?php
/** @var ?array $producto @var ?int $id @var array $categorias @var array $fotos @var array $errores @var array $cfg */
$p = $producto ?? [];
$val = static fn (string $k, string $def = ''): string => isset($p[$k]) && !is_array($p[$k]) ? (string) $p[$k] : $def;
$marcado = static fn (string $k, bool $def): bool => array_key_exists($k, $p) ? ((string) $p[$k] !== '' && (string) $p[$k] !== '0') : $def;
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="t-error" id="err-' . m_e($k) . '">' . m_e($errores[$k]) . '</p>' : '';
$enviado = isset($p['csrf']);
?>
<section class="t-pagina t-panel">
  <p><a href="<?= m_e(m_url('panel', ['ver' => 'productos'])) ?>"><?= m_e(m_texto('panel_producto_volver')) ?></a></p>
  <h1><?= $id === null ? m_e(m_texto('panel_producto_nuevo')) : m_e(m_texto('panel_producto_editar', null, ['producto' => $val('nombre')])) ?></h1>
  <?php if ($errores): ?><p class="demo-mensaje demo-mensaje--error" role="alert"><?= m_e(m_texto('revisa_campos')) ?></p><?php endif; ?>
  <form method="post" action="<?= m_e(m_url('panel_producto', $id !== null ? ['id' => $id] : [])) ?>" class="t-caja t-form-grid" novalidate>
    <?= m_csrf_campo() ?>
    <div class="t-campo t-campo--ancho">
      <label for="nombre"><?= m_e(m_texto('panel_producto_nombre')) ?></label>
      <input id="nombre" name="nombre" maxlength="80" required value="<?= m_e($val('nombre')) ?>">
      <?= $err('nombre') ?>
    </div>
    <div class="t-campo t-campo--ancho">
      <label for="descripcion"><?= m_e(m_texto('panel_producto_descripcion')) ?> <small><?= m_e(m_texto('panel_producto_descripcion_ayuda')) ?></small></label>
      <textarea id="descripcion" name="descripcion" maxlength="300" rows="3"><?= m_e($val('descripcion')) ?></textarea>
      <?= $err('descripcion') ?>
    </div>
    <div class="t-campo">
      <label for="categoria_id"><?= m_e(m_texto('panel_producto_categoria')) ?></label>
      <select id="categoria_id" name="categoria_id">
        <?php foreach ($categorias as $c): ?><option value="<?= (int) $c['id'] ?>"<?= $val('categoria_id') === (string) $c['id'] ? ' selected' : '' ?>><?= m_e($c['nombre']) ?></option><?php endforeach; ?>
      </select>
      <?= $err('categoria_id') ?>
    </div>
    <div class="t-campo">
      <label for="precio"><?= m_e(m_texto('panel_producto_precio')) ?></label>
      <input id="precio" name="precio" inputmode="numeric" maxlength="9" required value="<?= m_e($val('precio')) ?>">
      <?= $err('precio') ?>
    </div>
    <?php if (!empty($cfg['mayoristas'])): ?>
    <div class="t-campo">
      <label for="precio_mayorista"><?= m_e(m_texto('panel_producto_precio_mayorista', null, ['precio_mayorista' => m_texto('precio_mayorista')])) ?> <small><?= m_e(m_texto('panel_producto_precio_mayorista_ayuda')) ?></small></label>
      <input id="precio_mayorista" name="precio_mayorista" inputmode="numeric" maxlength="9" value="<?= m_e($val('precio_mayorista')) ?>">
      <?= $err('precio_mayorista') ?>
    </div>
    <?php endif; ?>
    <div class="t-campo">
      <label for="codigo"><?= m_e(m_texto('panel_producto_codigo')) ?> <small><?= m_e(m_texto('panel_producto_codigo_ayuda')) ?></small></label>
      <input id="codigo" name="codigo" maxlength="30" value="<?= m_e($val('codigo')) ?>" autocomplete="off">
      <?= $err('codigo') ?>
    </div>
    <div class="t-campo">
      <label for="stock"><?= m_e(m_texto('panel_producto_stock')) ?></label>
      <input id="stock" name="stock" inputmode="numeric" maxlength="5" required value="<?= m_e($val('stock', '0')) ?>">
      <?= $err('stock') ?>
    </div>
    <div class="t-campo">
      <label for="foto"><?= m_e(m_texto('panel_producto_foto')) ?></label>
      <select id="foto" name="foto">
        <option value=""><?= m_e(m_texto('panel_producto_sin_foto')) ?></option>
        <?php foreach ($fotos as $fid => $nombre): ?><option value="<?= m_e((string) $fid) ?>"<?= $val('foto') === (string) $fid ? ' selected' : '' ?>><?= m_e($nombre) ?></option><?php endforeach; ?>
      </select>
      <small class="t-pequeno"><?= m_e(m_texto('panel_producto_foto_ayuda')) ?></small>
      <?= $err('foto') ?>
    </div>
    <?php if (!empty($cfg['modelos3d'])): ?>
    <div class="t-campo">
      <label for="modelo3d"><?= m_e(m_texto('panel_producto_modelo')) ?></label>
      <select id="modelo3d" name="modelo3d">
        <option value=""><?= m_e(m_texto('panel_producto_sin_modelo')) ?></option>
        <?php foreach ($cfg['modelos3d'] as $m): ?><option value="<?= m_e($m) ?>"<?= $val('modelo3d') === $m ? ' selected' : '' ?>><?= m_e($m) ?>.glb</option><?php endforeach; ?>
      </select>
    </div>
    <?php endif; ?>
    <fieldset class="t-campo t-campo--ancho t-checks">
      <legend><?= m_e(m_texto('panel_producto_opciones')) ?></legend>
      <label class="t-check"><input type="checkbox" name="visible" value="1"<?= ($enviado ? $marcado('visible', false) : $marcado('visible', true)) ? ' checked' : '' ?>> <?= m_e(m_texto('panel_producto_visible')) ?></label>
      <label class="t-check"><input type="checkbox" name="destacado" value="1"<?= $marcado('destacado', false) ? ' checked' : '' ?>> <?= m_e(m_texto('panel_producto_destacado')) ?></label>
      <?php if (!empty($cfg['mayoristas'])): ?>
      <label class="t-check"><input type="checkbox" name="solo_mayorista" value="1"<?= $marcado('solo_mayorista', false) ? ' checked' : '' ?>> <?= m_e(m_texto('solo_para_mayoristas')) ?></label>
      <?php endif; ?>
    </fieldset>
    <div class="t-campo--ancho t-acciones">
      <button class="t-btn t-btn--primario" type="submit"><?= m_e(m_texto($id === null ? 'panel_producto_crear' : 'panel_producto_guardar')) ?></button>
      <a class="t-btn" href="<?= m_e(m_url('panel', ['ver' => 'productos'])) ?>"><?= m_e(m_texto('panel_producto_cancelar')) ?></a>
    </div>
  </form>
</section>
