<?php
/** Carrito de Kilo Justo: igual al del motor, pero los cortes muestran el peso (1,5 kg) en vez de unidades. @var array $carrito @var ?array $usuario @var array $cfg */
require_once __DIR__ . '/_kilo.php';
$minimo = (int) ($cfg['pedido_minimo'] ?? 0);
?>
<section class="t-pagina t-carrito">
  <h1>Tu carrito</h1>
  <?php if (!$carrito['lineas']): ?>
    <div class="t-vacio">
      <p>Tu carrito está vacío.</p>
      <a class="t-btn t-btn--primario" href="<?= m_e($cfg['url_base']) ?>#menu">Ver el catálogo</a>
    </div>
  <?php else: ?>
    <ul class="t-lineas">
      <?php foreach ($carrito['lineas'] as $l): $p = $l['producto']; $g = kj_gramos($p['nombre']); $nombre = kj_nombre($p['nombre']); ?>
      <li class="t-linea">
        <?php if ($p['foto'] !== ''): ?>
          <?= demo_img($p['foto'], '', ['ancho' => 160, 'alto' => 160, 'anchos' => [160, 320], 'sizes' => '72px', 'clase' => 't-linea__foto']) ?>
        <?php endif; ?>
        <div class="t-linea__info">
          <strong><?= m_e($nombre) ?></strong>
          <span><?php if ($g !== null): ?><?= m_e(m_clp(kj_por_kilo($l['precio'], $g))) ?> el kg<?php else: ?><?= m_e(m_clp($l['precio'])) ?> c/u<?php endif; ?><?= m_es_mayorista($usuario) && $p['precio_mayorista'] !== null ? ' · precio mayorista' : '' ?></span>
        </div>
        <form class="t-linea__cantidad" method="post" action="<?= m_e(m_url('carrito')) ?>">
          <?= m_csrf_campo() ?>
          <input type="hidden" name="accion" value="cambiar">
          <input type="hidden" name="producto_id" value="<?= (int) $p['id'] ?>">
          <button class="t-btn-redondo" type="submit" name="cantidad" value="<?= $l['cantidad'] - 1 ?>" aria-label="Quitar <?= $g !== null ? m_e(kj_peso($g)) . ' de' : 'uno de' ?> <?= m_e($nombre) ?>">−</button>
          <span class="kj-cantidad" aria-label="<?= m_e(kj_cantidad($p['nombre'], (int) $l['cantidad'])) ?>"><?= m_e(kj_cantidad($p['nombre'], (int) $l['cantidad'])) ?></span>
          <button class="t-btn-redondo" type="submit" name="cantidad" value="<?= $l['cantidad'] + 1 ?>" aria-label="Agregar <?= $g !== null ? m_e(kj_peso($g)) . ' de' : 'uno de' ?> <?= m_e($nombre) ?>"<?= $l['cantidad'] >= min(M_MAX_CANTIDAD, (int) $p['stock']) ? ' disabled' : '' ?>>+</button>
        </form>
        <strong class="t-linea__total"><?= m_e(m_clp($l['total'])) ?></strong>
      </li>
      <?php endforeach; ?>
    </ul>
    <div class="t-resumen">
      <p class="t-resumen__fila"><span>Subtotal</span><strong><?= m_e(m_clp($carrito['subtotal'])) ?></strong></p>
      <?php if ($minimo > 0 && $carrito['subtotal'] < $minimo): ?>
      <p class="demo-mensaje demo-mensaje--info">El pedido mínimo es <?= m_e(m_clp($minimo)) ?>. Te faltan <?= m_e(m_clp($minimo - $carrito['subtotal'])) ?>.</p>
      <?php endif; ?>
      <p class="t-pequeno">Los cortes se cobran según el peso real al cortar. El despacho se calcula según tu comuna en el siguiente paso<?= (int) ($cfg['despacho_gratis_desde'] ?? 0) > 0 ? ' (gratis desde ' . m_e(m_clp((int) $cfg['despacho_gratis_desde'])) . ')' : '' ?>.</p>
      <a class="t-btn t-btn--primario t-btn--bloque" href="<?= m_e(m_url('checkout')) ?>">Continuar</a>
      <form method="post" action="<?= m_e(m_url('carrito')) ?>">
        <?= m_csrf_campo() ?>
        <input type="hidden" name="accion" value="vaciar">
        <button class="t-enlace" type="submit">Vaciar carrito</button>
      </form>
    </div>
  <?php endif; ?>
</section>
