<?php
/**
 * Tarjeta de producto de Cardán (reemplaza la del motor): código de bodega (campo codigo, texto 'producto_codigo'),
 * compatibilidad leída de la descripción ("City car, hatchback · 1.2–1.6 · 2012–2020 · …", ver rp_compat_leer()),
 * stock a la vista, "Calza con tu vehículo" y precio taller.
 * @var array $p (con precio_final) @var ?array $usuario @var array $cfg @var bool $con_id @var ?string $calza_con
 * @var string $prefijo para el id de la tarjeta (destacados: 'd'; el mismo producto sale también en el catálogo)
 */
$prefijo ??= '';
$agotado = (int) $p['stock'] <= 0;
$stock = (int) $p['stock'];
$codigo = trim((string) ($p['codigo'] ?? ''));
$mayorista = m_es_mayorista($usuario) && $p['precio_mayorista'] !== null && (int) $p['precio_mayorista'] < (int) $p['precio'];
$info = rp_compat_leer((string) $p['descripcion']);
$calza_con ??= null;
[$stockClase, $stockTexto] = match (true) {
    $agotado => ['is-sin', 'Sin stock'],
    $stock <= 5 => ['is-pocos', m_texto('producto_quedan', null, ['n' => $stock])],
    default => ['is-hay', 'En bodega: ' . $stock],
};
?>
<article class="t-producto cd-producto<?= $agotado ? ' is-agotado' : '' ?>"<?= !empty($con_id) ? ' id="' . m_e($prefijo) . 'p' . (int) $p['id'] . '"' : '' ?>>
  <div class="t-producto__foto cd-producto__foto<?= $p['foto'] === '' ? ' t-producto__foto--vacia' : '' ?>">
    <?php if ($p['foto'] !== ''): ?>
      <?= demo_img($p['foto'], $p['nombre'], ['ancho' => 480, 'alto' => 360, 'anchos' => [320, 480, 800], 'sizes' => '(max-width: 600px) 92vw, (max-width: 1000px) 45vw, 280px']) ?>
    <?php else: ?>
      <span class="cd-producto__sin-foto" aria-hidden="true"><span><?= m_e($codigo !== '' ? $codigo : 'CD') ?></span></span>
    <?php endif; ?>
    <?php if ((int) $p['solo_mayorista'] === 1): ?><span class="t-sello t-sello--mayorista"><?= m_e(m_texto('solo_mayoristas')) ?></span>
    <?php elseif ((int) $p['destacado'] === 1): ?><span class="t-sello"><?= m_e(m_texto('sello_destacado', 'Favorito')) ?></span><?php endif; ?>
  </div>
  <div class="t-producto__cuerpo">
    <p class="cd-producto__fila">
      <?php if ($codigo !== ''): ?><span class="cd-sku"><?= m_e(m_texto('producto_codigo', null, ['codigo' => $codigo])) ?></span><?php endif; ?>
      <span class="cd-stock <?= m_e($stockClase) ?>"><?= m_e($stockTexto) ?></span>
    </p>
    <h3><?= m_e($p['nombre']) ?></h3>
    <p class="cd-calza"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" d="M3.5 15.5v-3l2-4.5h13l2 4.5v3zM3.5 15.5V18h3v-2.5M17.5 15.5V18h3v-2.5"/><circle cx="7.5" cy="13" r="1" fill="currentColor"/><circle cx="16.5" cy="13" r="1" fill="currentColor"/></svg>
      <span><?= m_e(rp_compat_texto($info['compat'])) ?></span></p>
    <?php if ($calza_con !== null): ?><p class="cd-calza-ok" title="<?= m_e($calza_con) ?>">Calza con tu vehículo</p><?php endif; ?>
    <p class="t-producto__desc"><?= m_e($info['texto']) ?></p>
    <p class="t-producto__precio">
      <strong><?= m_e(m_clp((int) $p['precio_final'])) ?></strong>
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
          <button class="t-btn t-btn--primario" type="submit" aria-label="<?= m_e(m_texto('producto_agregar_aria', null, ['producto' => $p['nombre']])) ?>"><?= m_e(m_texto('producto_agregar')) ?></button>
        </form>
      <?php endif; ?>
      <?php if (($p['modelo3d'] ?? '') !== '' && $p['foto'] !== ''): ?>
        <?= demo_boton_3d(['id' => $p['id'], 'nombre' => $p['nombre'], 'modelo' => $p['modelo3d'], 'foto' => $p['foto'], 'objeto' => 'producto'], 't-btn-3d cd-btn-3d', m_texto('producto_ver_3d')) ?>
      <?php endif; ?>
    </div>
  </div>
</article>
