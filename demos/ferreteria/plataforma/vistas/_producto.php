<?php
/**
 * Tarjeta de producto de Acopio Norte (reemplaza la del motor): igual que la del motor, más el código
 * de producto ("Cód. 10101 · ..." al inicio de la descripción) y "por día" en el arriendo.
 * @var array $p (con precio_final) @var ?array $usuario @var array $cfg @var bool $con_id
 */
$agotado = (int) $p['stock'] <= 0;
$mayorista = m_es_mayorista($usuario) && $p['precio_mayorista'] !== null && (int) $p['precio_mayorista'] < (int) $p['precio'];
$descripcion = (string) $p['descripcion'];
$codigo = '';
if (preg_match('/^Cód\.\s*([A-Za-z0-9-]{3,12})\s*·\s*(.*)$/us', $descripcion, $m)) {
    $codigo = $m[1];
    $descripcion = $m[2];
}
$porDia = ($p['categoria_slug'] ?? '') === 'arriendo';
?>
<article class="t-producto an-producto<?= $agotado ? ' is-agotado' : '' ?>"<?= !empty($con_id) ? ' id="p' . (int) $p['id'] . '"' : '' ?>>
  <div class="t-producto__foto">
    <?php if ($p['foto'] !== ''): ?>
      <?= demo_img($p['foto'], $p['nombre'], ['ancho' => 480, 'alto' => 360, 'anchos' => [320, 480, 800], 'sizes' => '(max-width: 600px) 92vw, (max-width: 1000px) 45vw, 300px']) ?>
    <?php endif; ?>
    <?php if ((int) $p['solo_mayorista'] === 1): ?><span class="t-sello t-sello--mayorista"><?= m_e(m_texto('solo_mayoristas')) ?></span>
    <?php elseif ((int) $p['destacado'] === 1): ?><span class="t-sello"><?= m_e(m_texto('sello_destacado', 'Favorito')) ?></span><?php endif; ?>
  </div>
  <div class="t-producto__cuerpo">
    <?php if ($codigo !== ''): ?><p class="an-sku">Cód. <?= m_e($codigo) ?></p><?php endif; ?>
    <h3><?= m_e($p['nombre']) ?></h3>
    <p class="t-producto__desc"><?= m_e($descripcion) ?></p>
    <p class="t-producto__precio">
      <strong><?= m_e(m_clp((int) $p['precio_final'])) ?></strong><?php if ($porDia): ?> <span class="an-por-dia">por día</span><?php endif; ?>
      <?php if ($mayorista): ?><s aria-label="Precio normal"><?= m_e(m_clp((int) $p['precio'])) ?></s> <span class="t-etiqueta"><?= m_e(m_texto('precio_mayorista')) ?></span><?php endif; ?>
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
          <button class="t-btn t-btn--primario" type="submit" aria-label="Agregar <?= m_e($p['nombre']) ?> al pedido"><?= $porDia ? 'Arrendar' : 'Agregar' ?></button>
        </form>
      <?php endif; ?>
    </div>
    <?php if (!$agotado && (int) $p['stock'] <= 5): ?><p class="t-producto__stock">Quedan <?= (int) $p['stock'] ?></p><?php endif; ?>
  </div>
</article>
