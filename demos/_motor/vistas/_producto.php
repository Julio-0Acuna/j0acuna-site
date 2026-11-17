<?php
/** Tarjeta de producto. @var array $p (con precio_final) @var ?array $usuario @var array $cfg @var bool $con_id (ancla #p<id>) */
$agotado = (int) $p['stock'] <= 0;
$mayorista = m_es_mayorista($usuario) && $p['precio_mayorista'] !== null && (int) $p['precio_mayorista'] < (int) $p['precio'];
?>
<article class="t-producto<?= $agotado ? ' is-agotado' : '' ?>"<?= !empty($con_id) ? ' id="p' . (int) $p['id'] . '"' : '' ?>>
  <div class="t-producto__foto">
    <?php if ($p['foto'] !== ''): ?>
      <?= demo_img($p['foto'], $p['nombre'], ['ancho' => 480, 'alto' => 360, 'anchos' => [320, 480, 800], 'sizes' => '(max-width: 600px) 92vw, (max-width: 1000px) 45vw, 300px']) ?>
    <?php endif; ?>
    <?php if ((int) $p['solo_mayorista'] === 1): ?><span class="t-sello t-sello--mayorista">Solo mayoristas</span>
    <?php elseif ((int) $p['destacado'] === 1): ?><span class="t-sello"><?= m_e(m_texto('sello_destacado', 'Favorito')) ?></span><?php endif; ?>
  </div>
  <div class="t-producto__cuerpo">
    <h3><?= m_e($p['nombre']) ?></h3>
    <p class="t-producto__desc"><?= m_e($p['descripcion']) ?></p>
    <p class="t-producto__precio">
      <strong><?= m_e(m_clp((int) $p['precio_final'])) ?></strong>
      <?php if ($mayorista): ?><s aria-label="Precio normal"><?= m_e(m_clp((int) $p['precio'])) ?></s> <span class="t-etiqueta">Precio mayorista</span><?php endif; ?>
    </p>
    <div class="t-producto__acciones">
      <?php if ($agotado): ?>
        <span class="t-agotado">Agotado</span>
      <?php else: ?>
        <form method="post" action="<?= m_e(m_url('carrito')) ?>">
          <?= m_csrf_campo() ?>
          <input type="hidden" name="accion" value="agregar">
          <input type="hidden" name="producto_id" value="<?= (int) $p['id'] ?>">
          <input type="hidden" name="volver" value="inicio">
          <button class="t-btn t-btn--primario" type="submit" aria-label="Agregar <?= m_e($p['nombre']) ?> al carrito">Agregar</button>
        </form>
      <?php endif; ?>
      <?php if ($p['modelo3d'] !== '' && $p['foto'] !== ''): ?>
        <?= demo_boton_3d(['id' => $p['id'], 'nombre' => $p['nombre'], 'modelo' => $p['modelo3d'], 'foto' => $p['foto']], 't-btn-3d') ?>
      <?php endif; ?>
    </div>
    <?php if (!$agotado && (int) $p['stock'] <= 5): ?><p class="t-producto__stock">Quedan <?= (int) $p['stock'] ?></p><?php endif; ?>
  </div>
</article>
