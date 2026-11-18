<?php /** @var array $carrito @var array $datos @var int $despacho @var int $total @var array $cfg */ ?>
<section class="t-pagina t-pago">
  <div class="t-pago__simulado" role="note">
    <strong><?= m_e(m_texto('pago_aviso_titulo')) ?></strong>
    <span><?= m_e(m_texto('pago_aviso_texto')) ?></span>
  </div>
  <div class="t-caja t-pago__caja">
    <p class="t-pago__comercio"><?= m_e($cfg['negocio']['nombre']) ?></p>
    <h1><?= m_e(m_texto('pago_total')) ?> <span><?= m_e(m_clp($total)) ?></span></h1>
    <ul class="t-mini-lineas">
      <?php foreach ($carrito['lineas'] as $l): ?>
      <li><span><?= (int) $l['cantidad'] ?> × <?= m_e($l['producto']['nombre']) ?></span><span><?= m_e(m_clp($l['total'])) ?></span></li>
      <?php endforeach; ?>
      <?php if ($datos['entrega'] === 'despacho'): ?>
      <li><span><?= m_e(m_texto('pago_despacho_a', null, ['comuna' => $datos['comuna']])) ?></span><span><?= m_e(m_clp($despacho)) ?></span></li>
      <?php endif; ?>
    </ul>
    <div class="t-pago__tarjeta" aria-hidden="true">
      <span>•••• •••• •••• 4242</span><span><?= m_e(m_texto('pago_tarjeta')) ?></span>
    </div>
    <form method="post" action="<?= m_e(m_url('pago')) ?>" class="t-pago__botones">
      <?= m_csrf_campo() ?>
      <button class="t-btn t-btn--primario t-btn--bloque" type="submit" name="accion" value="aprobar"><?= m_e(m_texto('pago_aprobar')) ?></button>
      <button class="t-btn t-btn--bloque" type="submit" name="accion" value="rechazar"><?= m_e(m_texto('pago_rechazar')) ?></button>
    </form>
    <p class="t-pequeno"><?= m_e(m_texto('pago_pie')) ?></p>
  </div>
</section>
