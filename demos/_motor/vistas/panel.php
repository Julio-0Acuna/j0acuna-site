<?php
/**
 * Panel del dueño. @var string $ver @var string $estado @var array $ventas @var array $conteo
 * @var array $pedidos @var array $productos @var array $pendientes @var array $aprobados @var array $cfg
 */
$pestanas = ['resumen' => 'Resumen', 'pedidos' => 'Pedidos', 'productos' => 'Productos'];
if (!empty($cfg['mayoristas'])) {
    $pestanas['mayoristas'] = 'Mayoristas' . ($pendientes ? ' (' . count($pendientes) . ')' : '');
}
$siguienteTexto = ['nuevo' => 'Pasar a preparando', 'preparando' => 'Marcar listo', 'listo' => 'Marcar entregado'];
$bajoStock = array_filter($productos, static fn (array $p): bool => (int) $p['visible'] === 1 && (int) $p['stock'] <= 5);
?>
<section class="t-pagina t-panel">
  <div class="t-panel__cabeza">
    <h1>Panel del dueño</h1>
    <a class="t-btn" href="<?= m_e(m_url('panel_producto')) ?>">+ Nuevo producto</a>
  </div>
  <nav class="t-pestanas" aria-label="Secciones del panel">
    <?php foreach ($pestanas as $clave => $nombre): ?>
    <a href="<?= m_e(m_url('panel', ['ver' => $clave])) ?>"<?= $ver === $clave ? ' aria-current="page" class="is-activo"' : '' ?>><?= m_e($nombre) ?></a>
    <?php endforeach; ?>
  </nav>

  <?php if ($ver === 'resumen'): ?>
  <div class="t-kpis" aria-label="Ventas del día">
    <div class="t-kpi"><span>Ventas de hoy</span><strong><?= m_e(m_clp($ventas['total'])) ?></strong></div>
    <div class="t-kpi"><span>Pedidos de hoy</span><strong><?= (int) $ventas['pedidos'] ?></strong></div>
    <div class="t-kpi"><span>Ticket promedio</span><strong><?= m_e(m_clp($ventas['promedio'])) ?></strong></div>
    <div class="t-kpi"><span>Por atender</span><strong><?= (int) $conteo['nuevo'] + (int) $conteo['preparando'] ?></strong></div>
  </div>
  <div class="t-panel__dos">
    <div class="t-caja">
      <h2>Más vendidos hoy</h2>
      <?php if (!$ventas['top']): ?><p>Aún no hay ventas hoy.</p><?php else: ?>
      <ol class="t-top">
        <?php foreach ($ventas['top'] as $t): ?><li><span><?= m_e($t['nombre']) ?></span><span><?= (int) $t['unidades'] ?> u. · <?= m_e(m_clp((int) $t['monto'])) ?></span></li><?php endforeach; ?>
      </ol>
      <?php endif; ?>
    </div>
    <div class="t-caja">
      <h2>Avisos</h2>
      <ul class="t-avisos">
        <?php if ($pendientes): ?><li><a href="<?= m_e(m_url('panel', ['ver' => 'mayoristas'])) ?>"><?= count($pendientes) ?> mayorista(s) esperan aprobación</a></li><?php endif; ?>
        <?php foreach ($bajoStock as $p): ?><li>Stock bajo: <?= m_e($p['nombre']) ?> (<?= (int) $p['stock'] ?>)</li><?php endforeach; ?>
        <?php if (!$pendientes && !$bajoStock): ?><li>Todo en orden.</li><?php endif; ?>
      </ul>
    </div>
  </div>
  <h2 class="t-panel__sub">Pedidos nuevos</h2>
  <?php endif; ?>

  <?php if ($ver === 'resumen' || $ver === 'pedidos'): ?>
    <?php if ($ver === 'pedidos'): ?>
    <nav class="t-chips" aria-label="Filtrar por estado">
      <a href="<?= m_e(m_url('panel', ['ver' => 'pedidos'])) ?>"<?= $estado === '' ? ' class="is-activo" aria-current="true"' : '' ?>>Todos</a>
      <?php foreach (M_ESTADOS as $clave => $nombre): ?>
      <a href="<?= m_e(m_url('panel', ['ver' => 'pedidos', 'estado' => $clave])) ?>"<?= $estado === $clave ? ' class="is-activo" aria-current="true"' : '' ?>><?= m_e($nombre) ?> (<?= (int) $conteo[$clave] ?>)</a>
      <?php endforeach; ?>
    </nav>
    <?php endif; ?>
    <?php if (!$pedidos): ?><p class="t-vacio">No hay pedidos aquí.</p><?php endif; ?>
    <ul class="t-pedidos-panel">
      <?php foreach ($pedidos as $p): ?>
      <li class="t-caja t-pedido-panel">
        <div class="t-pedido-panel__cabeza">
          <a href="<?= m_e(m_url('pedido', ['id' => $p['id']])) ?>"><strong><?= m_e($p['codigo']) ?></strong></a>
          <span class="t-estado t-estado--<?= m_e($p['estado']) ?>"><?= m_e(M_ESTADOS[$p['estado']]) ?></span>
        </div>
        <p><?= m_e($p['cliente']) ?><?= $p['tipo_cliente'] === 'mayorista' ? ' · <span class="t-etiqueta">Mayorista</span>' . ($p['cliente_empresa'] !== '' ? ' ' . m_e($p['cliente_empresa']) : '') : '' ?></p>
        <p class="t-pequeno"><?= m_e(date('d/m H:i', (int) $p['creado_en'])) ?> · <?= $p['entrega'] === 'despacho' ? 'Despacho a ' . m_e($p['comuna']) : 'Retiro' ?><?= $p['cuando'] !== '' ? ' · para las ' . m_e($p['cuando']) : '' ?> · <?= $p['pago'] === 'prueba' ? 'Pago de prueba' : 'Paga al recibir' ?></p>
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
            <button class="t-enlace" type="submit">Cancelar</button>
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
      <caption class="demo-sr">Productos de la tienda</caption>
      <thead><tr><th scope="col">Producto</th><th scope="col">Precio</th><?php if (!empty($cfg['mayoristas'])): ?><th scope="col">Mayorista</th><?php endif; ?><th scope="col">Stock</th><th scope="col">Visible</th><th scope="col"><span class="demo-sr">Acciones</span></th></tr></thead>
      <tbody>
        <?php foreach ($productos as $p): ?>
        <tr class="<?= (int) $p['visible'] ? '' : 'is-oculto' ?>">
          <th scope="row"><?= m_e($p['nombre']) ?><small><?= m_e($p['categoria_nombre']) ?><?= (int) $p['solo_mayorista'] ? ' · solo mayoristas' : '' ?></small></th>
          <td><?= m_e(m_clp((int) $p['precio'])) ?></td>
          <?php if (!empty($cfg['mayoristas'])): ?><td><?= $p['precio_mayorista'] !== null ? m_e(m_clp((int) $p['precio_mayorista'])) : '—' ?></td><?php endif; ?>
          <td class="<?= (int) $p['stock'] <= 5 ? 't-alerta' : '' ?>"><?= (int) $p['stock'] ?></td>
          <td>
            <form method="post" action="<?= m_e(m_url('panel')) ?>">
              <?= m_csrf_campo() ?><input type="hidden" name="ver" value="productos">
              <input type="hidden" name="accion" value="visible"><input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
              <button class="t-interruptor<?= (int) $p['visible'] ? ' is-on' : '' ?>" type="submit" aria-label="<?= (int) $p['visible'] ? 'Ocultar' : 'Mostrar' ?> <?= m_e($p['nombre']) ?>"><?= (int) $p['visible'] ? 'Sí' : 'No' ?></button>
            </form>
          </td>
          <td><a href="<?= m_e(m_url('panel_producto', ['id' => $p['id']])) ?>">Editar<span class="demo-sr"> <?= m_e($p['nombre']) ?></span></a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <?php if ($ver === 'mayoristas'): ?>
  <div class="t-caja">
    <h2>Solicitudes pendientes</h2>
    <?php if (!$pendientes): ?><p>No hay solicitudes pendientes.</p><?php endif; ?>
    <ul class="t-mayoristas">
      <?php foreach ($pendientes as $m): ?>
      <li>
        <div><strong><?= m_e($m['empresa'] !== '' ? $m['empresa'] : $m['nombre']) ?></strong><span><?= m_e($m['nombre']) ?> · <?= m_e($m['email']) ?><?= $m['telefono'] !== '' ? ' · ' . m_e($m['telefono']) : '' ?></span></div>
        <div class="t-mayoristas__acciones">
          <form method="post" action="<?= m_e(m_url('panel')) ?>"><?= m_csrf_campo() ?><input type="hidden" name="ver" value="mayoristas"><input type="hidden" name="accion" value="aprobar"><input type="hidden" name="id" value="<?= (int) $m['id'] ?>"><button class="t-btn t-btn--primario t-btn--chico" type="submit">Aprobar</button></form>
          <form method="post" action="<?= m_e(m_url('panel')) ?>"><?= m_csrf_campo() ?><input type="hidden" name="ver" value="mayoristas"><input type="hidden" name="accion" value="rechazar"><input type="hidden" name="id" value="<?= (int) $m['id'] ?>"><button class="t-enlace" type="submit">Rechazar</button></form>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <div class="t-caja">
    <h2>Mayoristas aprobados</h2>
    <ul class="t-mayoristas">
      <?php foreach ($aprobados as $m): ?>
      <li><div><strong><?= m_e($m['empresa'] !== '' ? $m['empresa'] : $m['nombre']) ?></strong><span><?= m_e($m['nombre']) ?> · <?= m_e($m['email']) ?></span></div></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>
</section>
