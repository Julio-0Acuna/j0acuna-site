<?php
/** @var array $pedido @var string $wa @var ?array $usuario @var array $cfg */
$pasos = ['nuevo' => 'Recibido', 'preparando' => 'Preparando', 'listo' => $pedido['entrega'] === 'retiro' ? 'Listo para retiro' : 'En camino', 'entregado' => 'Entregado'];
$indice = array_search($pedido['estado'], array_keys($pasos), true);
$fecha = m_fecha_texto((string) ($pedido['fecha_entrega'] ?? ''));
if ($fecha !== '') {
    $fecha = mb_strtoupper(mb_substr($fecha, 0, 1)) . mb_substr($fecha, 1);
    $para = $pedido['cuando'] !== '' ? m_texto('fecha_hora', null, ['fecha' => $fecha, 'hora' => $pedido['cuando']]) : m_texto('fecha_sin_hora', null, ['fecha' => $fecha]);
} else {
    $para = $pedido['cuando'] !== '' ? m_texto('hora_entrega', null, ['hora' => $pedido['cuando']]) : m_texto('cuando_pronto');
}
?>
<section class="t-pagina t-pedido">
  <p class="t-sobre">Pedido <?= m_e($pedido['codigo']) ?></p>
  <h1><?= $pedido['estado'] === 'cancelado' ? 'Pedido cancelado' : m_e(m_texto('pedido_titulo', '¡Gracias por tu pedido!')) ?></h1>

  <?php if ($pedido['estado'] !== 'cancelado'): ?>
  <ol class="t-pasos" aria-label="Estado del pedido">
    <?php $i = 0; foreach ($pasos as $clave => $nombre): ?>
    <li class="<?= $indice !== false && $i <= $indice ? 'is-hecho' : '' ?><?= $clave === $pedido['estado'] ? ' is-actual' : '' ?>"<?= $clave === $pedido['estado'] ? ' aria-current="step"' : '' ?>><?= m_e($nombre) ?></li>
    <?php $i++; endforeach; ?>
  </ol>
  <?php endif; ?>

  <div class="t-pedido__grid">
    <div class="t-caja">
      <h2>Detalle</h2>
      <ul class="t-mini-lineas">
        <?php foreach ($pedido['items'] as $it): ?>
        <li><span><?= (int) $it['cantidad'] ?> × <?= m_e($it['nombre']) ?></span><span><?= m_e(m_clp((int) $it['precio_unitario'] * (int) $it['cantidad'])) ?></span></li>
        <?php endforeach; ?>
      </ul>
      <p class="t-resumen__fila"><span>Subtotal</span><span><?= m_e(m_clp((int) $pedido['subtotal'])) ?></span></p>
      <?php if ($pedido['entrega'] === 'despacho'): ?>
      <p class="t-resumen__fila"><span>Despacho</span><span><?= (int) $pedido['costo_despacho'] === 0 ? 'Gratis' : m_e(m_clp((int) $pedido['costo_despacho'])) ?></span></p>
      <?php endif; ?>
      <p class="t-resumen__fila t-resumen__total"><span>Total</span><strong><?= m_e(m_clp((int) $pedido['total'])) ?></strong></p>
    </div>
    <div class="t-caja">
      <h2>Entrega y pago</h2>
      <dl class="t-datos">
        <div><dt>Entrega</dt><dd><?= $pedido['entrega'] === 'despacho' ? 'Despacho a ' . m_e($pedido['direccion']) . ', ' . m_e($pedido['comuna']) : m_e(m_texto('retiro_titulo')) ?></dd></div>
        <div><dt>Para</dt><dd><?= m_e($para) ?></dd></div>
        <div><dt>Pago</dt><dd><?= $pedido['pago'] === 'prueba' ? 'Pago de prueba' . ((int) $pedido['pagado'] ? ' (aprobado)' : '') : 'Al recibir' . ((int) $pedido['pagado'] ? ' (pagado)' : '') ?></dd></div>
        <?php if ($pedido['nota'] !== ''): ?><div><dt>Nota</dt><dd><?= m_e($pedido['nota']) ?></dd></div><?php endif; ?>
        <?php if (m_es_dueno($usuario)): ?><div><dt>Cliente</dt><dd><?= m_e($pedido['cliente']) ?><?= $pedido['cliente_empresa'] !== '' ? ' · ' . m_e($pedido['cliente_empresa']) : '' ?> (<?= m_e($pedido['tipo_cliente'] === 'mayorista' ? m_minuscula(m_texto('mayorista')) : $pedido['tipo_cliente']) ?>)</dd></div><?php endif; ?>
      </dl>
      <a class="t-btn t-btn--wa t-btn--bloque" href="<?= m_e($wa) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> Enviar resumen por WhatsApp</a>
      <p class="t-pequeno">Opcional: te llega la confirmación por WhatsApp.</p>
    </div>
  </div>
  <p class="t-acciones">
    <?php if (m_es_dueno($usuario)): ?>
      <a class="t-btn" href="<?= m_e(m_url('panel', ['ver' => 'pedidos'])) ?>">Volver al panel</a>
    <?php else: ?>
      <a class="t-btn" href="<?= m_e(m_url('cuenta')) ?>">Ver mis pedidos</a>
      <a class="t-btn t-btn--primario" href="<?= m_e($cfg['url_base']) ?>">Seguir comprando</a>
    <?php endif; ?>
  </p>
</section>
