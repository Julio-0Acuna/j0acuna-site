<?php
/**
 * Panel del dueño. @var string $ver @var string $estado @var array $ventas @var array $conteo
 * @var array $pedidos @var array $productos @var array $pendientes @var array $aprobados @var array $cfg
 */
$pestanas = ['resumen' => m_texto('panel_resumen'), 'pedidos' => m_texto('panel_pedidos'), 'productos' => m_texto('panel_productos')];
if (!empty($cfg['mayoristas'])) {
    $pestanas['mayoristas'] = m_texto('mayoristas') . ($pendientes ? ' (' . count($pendientes) . ')' : '');
}
/** "Retiro · para el sáb 22/11 a las 17:00" (con fecha) o "Retiro · para las 17:00" (modo hoy). */
$paraCuando = static function (array $p): string {
    $fecha = m_fecha_texto((string) ($p['fecha_entrega'] ?? ''), false);
    if ($fecha !== '') {
        return ' · ' . ($p['cuando'] !== '' ? m_texto('panel_para_fecha_hora', null, ['fecha' => $fecha, 'hora' => $p['cuando']]) : m_texto('panel_para_fecha', null, ['fecha' => $fecha]));
    }
    return $p['cuando'] !== '' ? ' · ' . m_texto('panel_para_hora', null, ['hora' => $p['cuando']]) : '';
};
$siguienteTexto = ['nuevo' => m_texto('panel_avanzar_nuevo'), 'preparando' => m_texto('panel_avanzar_preparando'), 'listo' => m_texto('panel_avanzar_listo')];
$bajoStock = array_filter($productos, static fn (array $p): bool => (int) $p['visible'] === 1 && (int) $p['stock'] <= 5);
?>
<section class="t-pagina t-panel">
  <div class="t-panel__cabeza">
    <h1><?= m_e(m_texto('panel_titulo')) ?></h1>
    <a class="t-btn" href="<?= m_e(m_url('panel_producto')) ?>"><?= m_e(m_texto('panel_nuevo_producto')) ?></a>
  </div>
  <nav class="t-pestanas" aria-label="<?= m_e(m_texto('panel_secciones')) ?>">
    <?php foreach ($pestanas as $clave => $nombre): ?>
    <a href="<?= m_e(m_url('panel', ['ver' => $clave])) ?>"<?= $ver === $clave ? ' aria-current="page" class="is-activo"' : '' ?>><?= m_e($nombre) ?></a>
    <?php endforeach; ?>
  </nav>

  <?php if ($ver === 'resumen'): ?>
  <div class="t-kpis" aria-label="<?= m_e(m_texto('panel_ventas_dia')) ?>">
    <div class="t-kpi"><span><?= m_e(m_texto('panel_ventas_hoy')) ?></span><strong><?= m_e(m_clp($ventas['total'])) ?></strong></div>
    <div class="t-kpi"><span><?= m_e(m_texto('panel_pedidos_hoy')) ?></span><strong><?= (int) $ventas['pedidos'] ?></strong></div>
    <div class="t-kpi"><span><?= m_e(m_texto('panel_ticket')) ?></span><strong><?= m_e(m_clp($ventas['promedio'])) ?></strong></div>
    <div class="t-kpi"><span><?= m_e(m_texto('panel_por_atender')) ?></span><strong><?= (int) $conteo['nuevo'] + (int) $conteo['preparando'] ?></strong></div>
  </div>
  <div class="t-panel__dos">
    <div class="t-caja">
      <h2><?= m_e(m_texto('panel_mas_vendidos')) ?></h2>
      <?php if (!$ventas['top']): ?><p><?= m_e(m_texto('panel_sin_ventas')) ?></p><?php else: ?>
      <ol class="t-top">
        <?php foreach ($ventas['top'] as $t): ?><li><span><?= m_e($t['nombre']) ?></span><span><?= m_e(m_texto('panel_unidades', null, ['n' => (int) $t['unidades']])) ?> · <?= m_e(m_clp((int) $t['monto'])) ?></span></li><?php endforeach; ?>
      </ol>
      <?php endif; ?>
    </div>
    <div class="t-caja">
      <h2><?= m_e(m_texto('panel_avisos')) ?></h2>
      <ul class="t-avisos">
        <?php if ($pendientes): ?><li><a href="<?= m_e(m_url('panel', ['ver' => 'mayoristas'])) ?>"><?= m_e(m_texto('mayoristas_esperan', null, ['n' => count($pendientes)])) ?></a></li><?php endif; ?>
        <?php foreach ($bajoStock as $p): ?><li><?= m_e(m_texto('panel_stock_bajo', null, ['producto' => $p['nombre'], 'n' => (int) $p['stock']])) ?></li><?php endforeach; ?>
        <?php if (!$pendientes && !$bajoStock): ?><li><?= m_e(m_texto('panel_todo_en_orden')) ?></li><?php endif; ?>
      </ul>
    </div>
  </div>
  <h2 class="t-panel__sub"><?= m_e(m_texto('panel_pedidos_nuevos')) ?></h2>
  <?php endif; ?>

  <?php if ($ver === 'resumen' || $ver === 'pedidos'): ?>
    <?php if ($ver === 'pedidos'): ?>
    <nav class="t-chips" aria-label="<?= m_e(m_texto('panel_filtrar')) ?>">
      <a href="<?= m_e(m_url('panel', ['ver' => 'pedidos'])) ?>"<?= $estado === '' ? ' class="is-activo" aria-current="true"' : '' ?>><?= m_e(m_texto('panel_todos')) ?></a>
      <?php foreach (array_keys(M_ESTADOS) as $clave): ?>
      <a href="<?= m_e(m_url('panel', ['ver' => 'pedidos', 'estado' => $clave])) ?>"<?= $estado === $clave ? ' class="is-activo" aria-current="true"' : '' ?>><?= m_e(m_estado_texto($clave)) ?> (<?= (int) $conteo[$clave] ?>)</a>
      <?php endforeach; ?>
    </nav>
    <?php endif; ?>
    <?php if (!$pedidos): ?><p class="t-vacio"><?= m_e(m_texto('panel_sin_pedidos')) ?></p><?php endif; ?>
    <ul class="t-pedidos-panel">
      <?php foreach ($pedidos as $p): ?>
      <li class="t-caja t-pedido-panel">
        <div class="t-pedido-panel__cabeza">
          <a href="<?= m_e(m_url('pedido', ['id' => $p['id']])) ?>"><strong><?= m_e($p['codigo']) ?></strong></a>
          <span class="t-estado t-estado--<?= m_e($p['estado']) ?>"><?= m_e(m_estado_texto($p['estado'])) ?></span>
        </div>
        <p><?= m_e($p['cliente']) ?><?= $p['tipo_cliente'] === 'mayorista' ? ' · <span class="t-etiqueta">' . m_e(m_texto('mayorista')) . '</span>' . ($p['cliente_empresa'] !== '' ? ' ' . m_e($p['cliente_empresa']) : '') : '' ?></p>
        <p class="t-pequeno"><?= m_e(date('d/m H:i', (int) $p['creado_en'])) ?> · <?= $p['entrega'] === 'despacho' ? m_e(m_texto('panel_despacho_a', null, ['comuna' => $p['comuna']])) : m_e(m_texto('panel_retiro')) ?><?= m_e($paraCuando($p)) ?> · <?= m_e(m_texto($p['pago'] === 'prueba' ? 'panel_pago_prueba' : 'panel_pago_al_recibir')) ?></p>
        <?php if ($extras = m_pedido_extras($p)): ?><p class="t-pequeno t-pedido-panel__extras"><?= m_e(implode(' · ', array_map(static fn (array $x): string => $x[0] . ': ' . $x[1], $extras))) ?></p><?php endif; ?>
        <?php if (trim((string) ($p['nota'] ?? '')) !== ''): ?><p class="t-pequeno t-pedido-panel__nota"><?= m_e(m_texto('panel_nota', null, ['nota' => m_recortar((string) $p['nota'], 80)])) ?></p><?php endif; ?>
        <p class="t-pedido-panel__total"><?= m_e(m_clp((int) $p['total'])) ?></p>
        <div class="t-pedido-panel__acciones">
          <?php if (isset($siguienteTexto[$p['estado']])): ?>
          <form method="post" action="<?= m_e(m_url('panel')) ?>">
            <?= m_csrf_campo() ?><input type="hidden" name="ver" value="<?= m_e($ver) ?>"><input type="hidden" name="estado_filtro" value="<?= m_e($estado) ?>">
            <input type="hidden" name="accion" value="avanzar"><input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
            <button class="t-btn t-btn--primario t-btn--chico" type="submit"><?= m_e($siguienteTexto[$p['estado']]) ?></button>
          </form>
          <?php endif; ?>
          <?php if (!in_array($p['estado'], ['entregado', 'cancelado'], true)): ?>
          <form method="post" action="<?= m_e(m_url('panel')) ?>">
            <?= m_csrf_campo() ?><input type="hidden" name="ver" value="<?= m_e($ver) ?>"><input type="hidden" name="estado_filtro" value="<?= m_e($estado) ?>">
            <input type="hidden" name="accion" value="cancelar"><input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
            <button class="t-enlace" type="submit"><?= m_e(m_texto('panel_cancelar')) ?></button>
          </form>
          <?php endif; ?>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php if ($ver === 'productos'): ?>
  <div class="t-tabla-envoltura">
    <table class="t-tabla">
      <caption class="demo-sr"><?= m_e(m_texto('panel_tabla')) ?></caption>
      <thead><tr><th scope="col"><?= m_e(m_texto('panel_producto')) ?></th><th scope="col"><?= m_e(m_texto('panel_precio')) ?></th><?php if (!empty($cfg['mayoristas'])): ?><th scope="col"><?= m_e(m_texto('mayorista')) ?></th><?php endif; ?><th scope="col"><?= m_e(m_texto('panel_stock')) ?></th><th scope="col"><?= m_e(m_texto('panel_visible')) ?></th><th scope="col"><span class="demo-sr"><?= m_e(m_texto('panel_acciones')) ?></span></th></tr></thead>
      <tbody>
        <?php foreach ($productos as $p): ?>
        <tr class="<?= (int) $p['visible'] ? '' : 'is-oculto' ?>">
          <th scope="row"><?= m_e($p['nombre']) ?><small><?= m_e($p['categoria_nombre']) ?><?= (int) $p['solo_mayorista'] ? ' · ' . m_e(m_minuscula(m_texto('solo_mayoristas'))) : '' ?></small></th>
          <td><?= m_e(m_clp((int) $p['precio'])) ?></td>
          <?php if (!empty($cfg['mayoristas'])): ?><td><?= $p['precio_mayorista'] !== null ? m_e(m_clp((int) $p['precio_mayorista'])) : '—' ?></td><?php endif; ?>
          <td class="<?= (int) $p['stock'] <= 5 ? 't-alerta' : '' ?>"><?= (int) $p['stock'] ?></td>
          <td>
            <form method="post" action="<?= m_e(m_url('panel')) ?>">
              <?= m_csrf_campo() ?><input type="hidden" name="ver" value="productos">
              <input type="hidden" name="accion" value="visible"><input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
              <button class="t-interruptor<?= (int) $p['visible'] ? ' is-on' : '' ?>" type="submit" aria-label="<?= m_e(m_texto((int) $p['visible'] ? 'panel_ocultar' : 'panel_mostrar', null, ['producto' => $p['nombre']])) ?>"><?= m_e(m_texto((int) $p['visible'] ? 'panel_si' : 'panel_no')) ?></button>
            </form>
          </td>
          <td><a href="<?= m_e(m_url('panel_producto', ['id' => $p['id']])) ?>"><?= m_e(m_texto('panel_editar')) ?><span class="demo-sr"> <?= m_e($p['nombre']) ?></span></a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <?php if ($ver === 'mayoristas'): ?>
  <div class="t-caja">
    <h2><?= m_e(m_texto('panel_solicitudes')) ?></h2>
    <?php if (!$pendientes): ?><p><?= m_e(m_texto('panel_sin_solicitudes')) ?></p><?php endif; ?>
    <ul class="t-mayoristas">
      <?php foreach ($pendientes as $m): ?>
      <li>
        <div><strong><?= m_e($m['empresa'] !== '' ? $m['empresa'] : $m['nombre']) ?></strong><span><?= m_e($m['nombre']) ?> · <?= m_e($m['email']) ?><?= $m['telefono'] !== '' ? ' · ' . m_e($m['telefono']) : '' ?></span></div>
        <div class="t-mayoristas__acciones">
          <form method="post" action="<?= m_e(m_url('panel')) ?>"><?= m_csrf_campo() ?><input type="hidden" name="ver" value="mayoristas"><input type="hidden" name="accion" value="aprobar"><input type="hidden" name="id" value="<?= (int) $m['id'] ?>"><button class="t-btn t-btn--primario t-btn--chico" type="submit"><?= m_e(m_texto('panel_aprobar')) ?></button></form>
          <form method="post" action="<?= m_e(m_url('panel')) ?>"><?= m_csrf_campo() ?><input type="hidden" name="ver" value="mayoristas"><input type="hidden" name="accion" value="rechazar"><input type="hidden" name="id" value="<?= (int) $m['id'] ?>"><button class="t-enlace" type="submit"><?= m_e(m_texto('panel_rechazar')) ?></button></form>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <div class="t-caja">
    <h2><?= m_e(m_texto('mayoristas_aprobados')) ?></h2>
    <ul class="t-mayoristas">
      <?php foreach ($aprobados as $m): ?>
      <li><div><strong><?= m_e($m['empresa'] !== '' ? $m['empresa'] : $m['nombre']) ?></strong><span><?= m_e($m['nombre']) ?> · <?= m_e($m['email']) ?></span></div></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>
</section>
