<?php
/**
 * Tarjeta de producto de Palé: etiqueta de bodega con código (SKU), stock a la vista y selector
 * de cantidad de cajas (los negocios compran varias a la vez). Misma acción que la del motor.
 * @var array $p (con precio_final) @var ?array $usuario @var array $cfg @var bool $con_id (ancla #p<id>)
 */
$agotado = (int) $p['stock'] <= 0;
$mayorista = m_es_mayorista($usuario) && $p['precio_mayorista'] !== null && (int) $p['precio_mayorista'] < (int) $p['precio'];
$precio = (int) $p['precio_final'];
$idCampo = 'cant-' . (!empty($con_id) ? 'c' : 'd') . (int) $p['id'];
$maximo = max(1, min(10, (int) $p['stock']));
$sku = 'PD-' . str_pad((string) (int) $p['id'], 4, '0', STR_PAD_LEFT);
?>
<article class="t-producto pd-producto<?= $agotado ? ' is-agotado' : '' ?>"<?= !empty($con_id) ? ' id="p' . (int) $p['id'] . '"' : '' ?>>
  <div class="t-producto__foto">
    <?php if ($p['foto'] !== ''): ?>
      <?= demo_img($p['foto'], $p['nombre'], ['ancho' => 480, 'alto' => 360, 'anchos' => [320, 480, 800], 'sizes' => '(max-width: 600px) 92vw, (max-width: 1000px) 45vw, 280px']) ?>
    <?php endif; ?>
    <?php if ((int) $p['solo_mayorista'] === 1): ?><span class="t-sello t-sello--mayorista"><?= m_e(m_texto('solo_mayoristas')) ?></span>
    <?php elseif ((int) $p['destacado'] === 1): ?><span class="t-sello"><?= m_e(m_texto('sello_destacado')) ?></span><?php endif; ?>
  </div>
  <div class="t-producto__cuerpo">
    <p class="pd-sku"><span><?= m_e($sku) ?></span><span class="pd-stock<?= !$agotado && (int) $p['stock'] <= 5 ? ' is-bajo' : '' ?>"><?= $agotado ? 'Sin stock' : 'Stock ' . (int) $p['stock'] ?></span></p>
    <h3><?= m_e($p['nombre']) ?></h3>
    <p class="t-producto__desc"><?= m_e($p['descripcion']) ?></p>
    <p class="t-producto__precio">
      <strong><?= m_e(m_clp($precio)) ?></strong>
      <?php if ($mayorista): ?><s aria-label="Precio normal"><?= m_e(m_clp((int) $p['precio'])) ?></s> <span class="t-etiqueta"><?= m_e(m_texto('precio_mayorista')) ?></span><?php endif; ?>
    </p>
    <div class="t-producto__acciones">
      <?php if ($agotado): ?>
        <span class="t-agotado">Agotado</span>
      <?php else: ?>
        <form class="pd-agregar" method="post" action="<?= m_e(m_url('carrito')) ?>">
          <?= m_csrf_campo() ?>
          <input type="hidden" name="accion" value="agregar">
          <input type="hidden" name="producto_id" value="<?= (int) $p['id'] ?>">
          <input type="hidden" name="volver" value="inicio">
          <label class="demo-sr" for="<?= m_e($idCampo) ?>">Cantidad de <?= m_e($p['nombre']) ?></label>
          <select id="<?= m_e($idCampo) ?>" name="cantidad">
            <?php for ($c = 1; $c <= $maximo; $c++): ?><option value="<?= $c ?>"><?= $c ?></option><?php endfor; ?>
          </select>
          <button class="t-btn t-btn--primario" type="submit" aria-label="Agregar <?= m_e($p['nombre']) ?> al carrito">Agregar</button>
        </form>
      <?php endif; ?>
    </div>
    <?php if (!$agotado && (int) $p['stock'] <= 5): ?><p class="t-producto__stock">Quedan <?= (int) $p['stock'] ?></p><?php endif; ?>
  </div>
</article>
