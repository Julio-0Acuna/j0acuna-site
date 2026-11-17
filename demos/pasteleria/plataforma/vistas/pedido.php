<?php
/**
 * Pedido de Merengue Club: el del motor, con el día de entrega y el mensaje en la torta
 * separados de la nota (ver _encargo.php), también en el resumen para WhatsApp.
 * @var array $pedido @var string $wa @var ?array $usuario @var array $cfg
 */
$extra = mc_separar_nota($pedido['nota']);
$wa = m_wa(m_pedido_resumen_texto($pedido)
    . ($extra['fecha'] !== '' ? "\nDía: " . $extra['fecha'] : '')
    . ($extra['mensaje'] !== '' ? "\nMensaje en la torta: «" . $extra['mensaje'] . '»' : '')
    . ($extra['resto'] !== '' ? "\nNota: " . $extra['resto'] : ''));
$para = ($extra['fecha'] !== '' ? ucfirst($extra['fecha']) : 'Hoy') . ($pedido['cuando'] !== '' ? ', a las ' . $pedido['cuando'] : ($extra['fecha'] !== '' ? ', durante el día' : ', lo antes posible'));
$pasos = ['nuevo' => 'Recibido', 'preparando' => 'En el horno', 'listo' => $pedido['entrega'] === 'retiro' ? 'Listo para retiro' : 'En camino', 'entregado' => 'Entregado'];
$indice = array_search($pedido['estado'], array_keys($pasos), true);
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
        <div><dt>Entrega</dt><dd><?= $pedido['entrega'] === 'despacho' ? 'Despacho a ' . m_e($pedido['direccion']) . ', ' . m_e($pedido['comuna']) : 'Retiro en el local' ?></dd></div>
        <div><dt>Para</dt><dd><?= m_e($para) ?></dd></div>
        <?php if ($extra['mensaje'] !== ''): ?><div><dt>Mensaje en la torta</dt><dd>«<?= m_e($extra['mensaje']) ?>»</dd></div><?php endif; ?>
        <div><dt>Pago</dt><dd><?= $pedido['pago'] === 'prueba' ? 'Pago de prueba' . ((int) $pedido['pagado'] ? ' (aprobado)' : '') : 'Al recibir' . ((int) $pedido['pagado'] ? ' (pagado)' : '') ?></dd></div>
        <?php if ($extra['resto'] !== ''): ?><div><dt>Nota</dt><dd><?= m_e($extra['resto']) ?></dd></div><?php endif; ?>
        <?php if (m_es_dueno($usuario)): ?><div><dt>Cliente</dt><dd><?= m_e($pedido['cliente']) ?><?= $pedido['cliente_empresa'] !== '' ? ' · ' . m_e($pedido['cliente_empresa']) : '' ?> (<?= m_e($pedido['tipo_cliente']) ?>)</dd></div><?php endif; ?>
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
