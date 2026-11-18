<?php
/**
 * Tarjeta de producto de Acopio Norte (reemplaza la del motor): igual que la del motor, con el código
 * (campo codigo, texto 'producto_codigo') sobre el nombre, y "por día" y el botón "Arrendar" en el arriendo
 * de herramientas (el motor no tiene precio por día ni un texto de botón por categoría).
 * @var array $p (con precio_final) @var ?array $usuario @var array $cfg @var bool $con_id
 * @var string $prefijo para el id de la tarjeta (destacados: 'd'; el mismo producto sale también en el catálogo)
 */
$prefijo ??= '';
$agotado = (int) $p['stock'] <= 0;
$mayorista = m_es_mayorista($usuario) && $p['precio_mayorista'] !== null && (int) $p['precio_mayorista'] < (int) $p['precio'];
$codigo = trim((string) ($p['codigo'] ?? ''));
$porDia = ($p['categoria_slug'] ?? '') === 'arriendo';
?>
<article class="t-producto an-producto<?= $agotado ? ' is-agotado' : '' ?>"<?= !empty($con_id) ? ' id="' . m_e($prefijo) . 'p' . (int) $p['id'] . '"' : '' ?>>
  <div class="t-producto__foto<?= $p['foto'] === '' ? ' t-producto__foto--vacia' : '' ?>">
    <?php if ($p['foto'] !== ''): ?>
      <?= demo_img($p['foto'], $p['nombre'], ['ancho' => 480, 'alto' => 360, 'anchos' => [320, 480, 800], 'sizes' => '(max-width: 600px) 92vw, (max-width: 1000px) 45vw, 300px']) ?>
    <?php endif; ?>
    <?php if ((int) $p['solo_mayorista'] === 1): ?><span class="t-sello t-sello--mayorista"><?= m_e(m_texto('solo_mayoristas')) ?></span>
    <?php elseif ((int) $p['destacado'] === 1): ?><span class="t-sello"><?= m_e(m_texto('sello_destacado', 'Favorito')) ?></span><?php endif; ?>
  </div>
  <div class="t-producto__cuerpo">
    <?php if ($codigo !== ''): ?><p class="an-sku"><?= m_e(m_texto('producto_codigo', null, ['codigo' => $codigo])) ?></p><?php endif; ?>
    <h3><?= m_e($p['nombre']) ?></h3>
    <p class="t-producto__desc"><?= m_e($p['descripcion']) ?></p>
    <p class="t-producto__precio">
      <strong><?= m_e(m_clp((int) $p['precio_final'])) ?></strong><?php if ($porDia): ?> <span class="an-por-dia">por día</span><?php endif; ?>
      <?php if ($mayorista): ?><s aria-label="<?= m_e(m_texto('producto_precio_normal')) ?>"><?= m_e(m_clp((int) $p['precio'])) ?></s> <span class="t-etiqueta"><?= m_e(m_texto('precio_mayorista')) ?></span><?php endif; ?>
    </p>
    <div class="t-producto__acciones">
      <?php if ($agotado): ?>
        <span class="t-agotado"><?= m_e(m_texto('producto_agotado')) ?></span>
      <?php else: ?>
        <form method="post" action="<?= m_e(m_url('carrito')) ?>">
          <?= m_csrf_campo() ?>
          <input type="hidden" name="accion" value="agregar">
          <input type="hidden" name="producto_id" value="<?= (int) $p['id'] ?>">
          <input type="hidden" name="volver" value="inicio">
          <button class="t-btn t-btn--primario" type="submit" aria-label="<?= m_e(m_texto('producto_agregar_aria', null, ['producto' => $p['nombre']])) ?>"><?= $porDia ? 'Arrendar' : m_e(m_texto('producto_agregar')) ?></button>
        </form>
      <?php endif; ?>
    </div>
    <?php if (!$agotado && (int) $p['stock'] <= 5): ?><p class="t-producto__stock"><?= m_e(m_texto('producto_quedan', null, ['n' => (int) $p['stock']])) ?></p><?php endif; ?>
  </div>
</article>
