<?php
/**
 * Tarjeta de producto. @var array $p (con precio_final) @var ?array $usuario @var array $cfg @var bool $con_id (ancla #p<id>)
 * @var string $prefijo para los id de la tarjeta (ej. 'd' en destacados: el mismo producto sale dos veces en la portada)
 */
$prefijo ??= '';
$agotado = (int) $p['stock'] <= 0;
$codigo = trim((string) ($p['codigo'] ?? ''));
$mayorista = m_es_mayorista($usuario) && $p['precio_mayorista'] !== null && (int) $p['precio_mayorista'] < (int) $p['precio'];
?>
<article class="t-producto<?= $agotado ? ' is-agotado' : '' ?>"<?= !empty($con_id) ? ' id="' . m_e($prefijo) . 'p' . (int) $p['id'] . '"' : '' ?>>
  <div class="t-producto__foto<?= $p['foto'] === '' ? ' t-producto__foto--vacia' : '' ?>">
    <?php if ($p['foto'] !== ''): ?>
      <?= demo_img($p['foto'], $p['nombre'], ['ancho' => 480, 'alto' => 360, 'anchos' => [320, 480, 800], 'sizes' => '(max-width: 600px) 92vw, (max-width: 1000px) 45vw, 300px']) ?>
    <?php endif; ?>
    <?php if ((int) $p['solo_mayorista'] === 1): ?><span class="t-sello t-sello--mayorista"><?= m_e(m_texto('solo_mayoristas')) ?></span>
    <?php elseif ((int) $p['destacado'] === 1): ?><span class="t-sello"><?= m_e(m_texto('sello_destacado', 'Favorito')) ?></span><?php endif; ?>
  </div>
  <div class="t-producto__cuerpo">
    <h3><?= m_e($p['nombre']) ?></h3>
    <?php if ($codigo !== ''): ?><p class="t-producto__codigo"><?= m_e(m_texto('producto_codigo', null, ['codigo' => $codigo])) ?></p><?php endif; ?>
    <p class="t-producto__desc"><?= m_e($p['descripcion']) ?></p>
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
      <?php if ($p['modelo3d'] !== '' && $p['foto'] !== ''): ?>
        <?= demo_boton_3d(['id' => $p['id'], 'nombre' => $p['nombre'], 'modelo' => $p['modelo3d'], 'foto' => $p['foto']], 't-btn-3d', m_texto('producto_ver_3d')) ?>
      <?php endif; ?>
    </div>
    <?php if (!$agotado && (int) $p['stock'] <= 5): ?><p class="t-producto__stock"><?= m_e(m_texto('producto_quedan', null, ['n' => (int) $p['stock']])) ?></p><?php endif; ?>
  </div>
</article>
