<?php
/** @var array $pedido @var string $wa @var ?array $usuario @var array $cfg */
$pasos = ['nuevo' => m_texto('pedido_paso_nuevo')];
foreach (['preparando', 'listo', 'entregado'] as $paso) {
    $pasos[$paso] = m_estado_texto($paso, $pedido['entrega']);
}
$indice = array_search($pedido['estado'], array_keys($pasos), true);
$fecha = m_fecha_texto((string) ($pedido['fecha_entrega'] ?? ''));
if ($fecha !== '') {
    $fecha = mb_strtoupper(mb_substr($fecha, 0, 1)) . mb_substr($fecha, 1);
    $para = $pedido['cuando'] !== '' ? m_texto('fecha_hora', null, ['fecha' => $fecha, 'hora' => $pedido['cuando']]) : m_texto('fecha_sin_hora', null, ['fecha' => $fecha]);
} else {
    $para = $pedido['cuando'] !== '' ? m_texto('hora_entrega', null, ['hora' => $pedido['cuando']]) : m_texto('cuando_pronto');
}
$pago = $pedido['pago'] === 'prueba'
    ? m_texto((int) $pedido['pagado'] ? 'pedido_pago_prueba_aprobado' : 'pedido_pago_prueba')
    : m_texto((int) $pedido['pagado'] ? 'pedido_pago_al_recibir_pagado' : 'pedido_pago_al_recibir');
?>
<section class="t-pagina t-pedido">
  <p class="t-sobre"><?= m_e(m_texto('pedido_codigo', null, ['codigo' => $pedido['codigo']])) ?></p>
  <h1><?= $pedido['estado'] === 'cancelado' ? m_e(m_texto('pedido_cancelado')) : m_e(m_texto('pedido_titulo', '¡Gracias por tu pedido!')) ?></h1>

  <?php if ($pedido['estado'] !== 'cancelado'): ?>
  <ol class="t-pasos" aria-label="<?= m_e(m_texto('pedido_estado')) ?>">
    <?php $i = 0; foreach ($pasos as $clave => $nombre): ?>
    <li class="<?= $indice !== false && $i <= $indice ? 'is-hecho' : '' ?><?= $clave === $pedido['estado'] ? ' is-actual' : '' ?>"<?= $clave === $pedido['estado'] ? ' aria-current="step"' : '' ?>><?= m_e($nombre) ?></li>
    <?php $i++; endforeach; ?>
  </ol>
  <?php endif; ?>

  <div class="t-pedido__grid">
    <div class="t-caja">
      <h2><?= m_e(m_texto('pedido_detalle')) ?></h2>
      <ul class="t-mini-lineas">
        <?php foreach ($pedido['items'] as $it): ?>
        <li><span><?= (int) $it['cantidad'] ?> × <?= m_e($it['nombre']) ?></span><span><?= m_e(m_clp((int) $it['precio_unitario'] * (int) $it['cantidad'])) ?></span></li>
        <?php endforeach; ?>
      </ul>
      <p class="t-resumen__fila"><span><?= m_e(m_texto('pedido_subtotal')) ?></span><span><?= m_e(m_clp((int) $pedido['subtotal'])) ?></span></p>
      <?php if ($pedido['entrega'] === 'despacho'): ?>
      <p class="t-resumen__fila"><span><?= m_e(m_texto('pedido_despacho')) ?></span><span><?= (int) $pedido['costo_despacho'] === 0 ? m_e(m_texto('pedido_gratis')) : m_e(m_clp((int) $pedido['costo_despacho'])) ?></span></p>
      <?php endif; ?>
      <p class="t-resumen__fila t-resumen__total"><span><?= m_e(m_texto('pedido_total')) ?></span><strong><?= m_e(m_clp((int) $pedido['total'])) ?></strong></p>
    </div>
    <div class="t-caja">
      <h2><?= m_e(m_texto('pedido_entrega_pago')) ?></h2>
      <dl class="t-datos">
        <div><dt><?= m_e(m_texto('pedido_entrega')) ?></dt><dd><?= $pedido['entrega'] === 'despacho' ? m_e(m_texto('pedido_despacho_a', null, ['direccion' => $pedido['direccion'], 'comuna' => $pedido['comuna']])) : m_e(m_texto('retiro_titulo')) ?></dd></div>
        <div><dt><?= m_e(m_texto('pedido_para')) ?></dt><dd><?= m_e($para) ?></dd></div>
        <div><dt><?= m_e(m_texto('pedido_pago')) ?></dt><dd><?= m_e($pago) ?></dd></div>
        <?php foreach (m_pedido_extras($pedido) as [$etiqueta, $valor]): ?><div><dt><?= m_e($etiqueta) ?></dt><dd><?= m_e($valor) ?></dd></div><?php endforeach; ?>
        <?php if ($pedido['nota'] !== ''): ?><div><dt><?= m_e(m_texto('pedido_nota')) ?></dt><dd><?= m_e($pedido['nota']) ?></dd></div><?php endif; ?>
        <?php if (m_es_dueno($usuario)): ?><div><dt><?= m_e(m_texto('pedido_cliente')) ?></dt><dd><?= m_e($pedido['cliente']) ?><?= $pedido['cliente_empresa'] !== '' ? ' · ' . m_e($pedido['cliente_empresa']) : '' ?> (<?= m_e(m_minuscula(m_texto($pedido['tipo_cliente'] === 'mayorista' ? 'mayorista' : 'particular'))) ?>)</dd></div><?php endif; ?>
      </dl>
      <a class="t-btn t-btn--wa t-btn--bloque" href="<?= m_e($wa) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> <?= m_e(m_texto('pedido_wa')) ?></a>
      <p class="t-pequeno"><?= m_e(m_texto('pedido_wa_ayuda')) ?></p>
    </div>
  </div>
  <p class="t-acciones">
    <?php if (m_es_dueno($usuario)): ?>
      <a class="t-btn" href="<?= m_e(m_url('panel', ['ver' => 'pedidos'])) ?>"><?= m_e(m_texto('pedido_volver_panel')) ?></a>
    <?php else: ?>
      <a class="t-btn" href="<?= m_e(m_url('cuenta')) ?>"><?= m_e(m_texto('pedido_mis_pedidos')) ?></a>
      <a class="t-btn t-btn--primario" href="<?= m_e($cfg['url_base']) ?>"><?= m_e(m_texto('pedido_seguir')) ?></a>
    <?php endif; ?>
  </p>
</section>
