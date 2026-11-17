<?php /** @var array $carrito @var array $datos @var int $despacho @var int $total @var array $cfg */ ?>
<section class="t-pagina t-pago">
  <div class="t-pago__simulado" role="note">
    <strong>PAGO DE PRUEBA</strong>
    <span>Esta pantalla simula una pasarela de pago. No se cobra nada y no debes ingresar datos de tarjeta.</span>
  </div>
  <div class="t-caja t-pago__caja">
    <p class="t-pago__comercio"><?= m_e($cfg['negocio']['nombre']) ?></p>
    <h1>Total a pagar <span><?= m_e(m_clp($total)) ?></span></h1>
    <ul class="t-mini-lineas">
      <?php foreach ($carrito['lineas'] as $l): ?>
      <li><span><?= (int) $l['cantidad'] ?> × <?= m_e($l['producto']['nombre']) ?></span><span><?= m_e(m_clp($l['total'])) ?></span></li>
      <?php endforeach; ?>
      <?php if ($datos['entrega'] === 'despacho'): ?>
      <li><span>Despacho a <?= m_e($datos['comuna']) ?></span><span><?= m_e(m_clp($despacho)) ?></span></li>
      <?php endif; ?>
    </ul>
    <div class="t-pago__tarjeta" aria-hidden="true">
      <span>•••• •••• •••• 4242</span><span>Tarjeta ficticia de demostración</span>
    </div>
    <form method="post" action="<?= m_e(m_url('pago')) ?>" class="t-pago__botones">
      <?= m_csrf_campo() ?>
      <button class="t-btn t-btn--primario t-btn--bloque" type="submit" name="accion" value="aprobar">Aprobar pago de prueba</button>
      <button class="t-btn t-btn--bloque" type="submit" name="accion" value="rechazar">Simular pago rechazado</button>
    </form>
    <p class="t-pequeno">En un sitio real aquí se conecta Webpay, Mercado Pago u otra pasarela.</p>
  </div>
</section>
