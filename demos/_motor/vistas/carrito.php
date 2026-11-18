<?php
/** @var array $carrito @var ?array $usuario @var array $cfg */
// Pedido mínimo: con 'pedido_minimo_solo_despacho' solo se avisa (el retiro no tiene mínimo); si no, no se puede continuar.
$avisoMinimo = m_aviso_minimo($cfg, (int) $carrito['subtotal'], 'despacho');
$bloqueado = $avisoMinimo !== '' && empty($cfg['pedido_minimo_solo_despacho']);
$faltaGratis = !empty($cfg['despacho']) ? m_falta_gratis($cfg, (int) $carrito['subtotal']) : 0;
?>
<section class="t-pagina t-carrito">
  <h1><?= m_e(m_texto('carrito_titulo')) ?></h1>
  <?php if (!$carrito['lineas']): ?>
    <div class="t-vacio">
      <p><?= m_e(m_texto('carrito_vacio')) ?></p>
      <a class="t-btn t-btn--primario" href="<?= m_e($cfg['url_base']) ?>#menu"><?= m_e(m_texto('ver_catalogo')) ?></a>
    </div>
  <?php else: ?>
    <ul class="t-lineas">
      <?php foreach ($carrito['lineas'] as $l): $p = $l['producto']; ?>
      <li class="t-linea">
        <?php if ($p['foto'] !== ''): ?>
          <?= demo_img($p['foto'], '', ['ancho' => 160, 'alto' => 160, 'anchos' => [160, 320], 'sizes' => '72px', 'clase' => 't-linea__foto']) ?>
        <?php endif; ?>
        <div class="t-linea__info">
          <strong><?= m_e($p['nombre']) ?></strong>
          <span><?= m_e(m_texto('carrito_precio_unidad', null, ['precio' => m_clp($l['precio'])])) ?><?= m_es_mayorista($usuario) && $p['precio_mayorista'] !== null ? ' · ' . m_e(m_minuscula(m_texto('precio_mayorista'))) : '' ?></span>
        </div>
        <form class="t-linea__cantidad" method="post" action="<?= m_e(m_url('carrito')) ?>">
          <?= m_csrf_campo() ?>
          <input type="hidden" name="accion" value="cambiar">
          <input type="hidden" name="producto_id" value="<?= (int) $p['id'] ?>">
          <button class="t-btn-redondo" type="submit" name="cantidad" value="<?= $l['cantidad'] - 1 ?>" aria-label="<?= m_e(m_texto('carrito_quitar_uno', null, ['producto' => $p['nombre']])) ?>">−</button>
          <span aria-label="<?= m_e(m_texto('carrito_unidades', null, ['n' => (int) $l['cantidad']])) ?>"><?= (int) $l['cantidad'] ?></span>
          <button class="t-btn-redondo" type="submit" name="cantidad" value="<?= $l['cantidad'] + 1 ?>" aria-label="<?= m_e(m_texto('carrito_agregar_uno', null, ['producto' => $p['nombre']])) ?>"<?= $l['cantidad'] >= min(M_MAX_CANTIDAD, (int) $p['stock']) ? ' disabled' : '' ?>>+</button>
        </form>
        <strong class="t-linea__total"><?= m_e(m_clp($l['total'])) ?></strong>
      </li>
      <?php endforeach; ?>
    </ul>
    <div class="t-resumen">
      <p class="t-resumen__fila"><span><?= m_e(m_texto('carrito_subtotal')) ?></span><strong><?= m_e(m_clp($carrito['subtotal'])) ?></strong></p>
      <p class="t-pequeno"><?= (int) ($cfg['despacho_gratis_desde'] ?? 0) > 0 ? m_e(m_texto('carrito_despacho_nota_gratis', null, ['gratis' => m_clp((int) $cfg['despacho_gratis_desde'])])) : m_e(m_texto('carrito_despacho_nota')) ?></p>
      <?php if ($faltaGratis > 0): ?><p class="t-pequeno t-falta-gratis" data-falta-gratis><?= m_e(m_texto('carrito_falta_gratis', null, ['falta' => m_clp($faltaGratis)])) ?></p><?php endif; ?>
      <?php if ($avisoMinimo !== ''): ?><p class="demo-mensaje demo-mensaje--info t-falta-minimo" id="falta-minimo" data-falta-minimo><?= m_e($avisoMinimo) ?></p><?php endif; ?>
      <?php if ($bloqueado): ?>
      <a class="t-btn t-btn--primario t-btn--bloque" role="link" aria-disabled="true" aria-describedby="falta-minimo"><?= m_e(m_texto('carrito_continuar')) ?></a>
      <?php else: ?>
      <a class="t-btn t-btn--primario t-btn--bloque" href="<?= m_e(m_url('checkout')) ?>"><?= m_e(m_texto('carrito_continuar')) ?></a>
      <?php endif; ?>
      <form method="post" action="<?= m_e(m_url('carrito')) ?>">
        <?= m_csrf_campo() ?>
        <input type="hidden" name="accion" value="vaciar">
        <button class="t-enlace" type="submit"><?= m_e(m_texto('carrito_vaciar')) ?></button>
      </form>
    </div>
  <?php endif; ?>
</section>
