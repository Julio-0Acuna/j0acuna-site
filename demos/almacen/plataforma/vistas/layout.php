<?php
/**
 * Layout de Bodega Surtido: distribuidora y almacén online.
 * Cinta de embalaje con las condiciones, cabecera azul marino con buscador, pasillos en cartón kraft
 * y aviso del pedido mínimo en el carrito.
 * @var string $contenido @var string $titulo @var string $vista @var array $cfg @var ?array $usuario
 */
$cfg ??= m_config();
$usuario ??= m_usuario();
$vista ??= '';
$esDueno = m_es_dueno($usuario);
$esNegocio = m_es_mayorista($usuario);
$unidades = array_sum(m_carrito_crudo());
$categorias = m_categorias(m_db());
$catActual = is_string($_GET['cat'] ?? null) ? $_GET['cat'] : '';
$qActual = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
$cuentaUrl = m_url($usuario ? ($esDueno ? 'panel' : 'cuenta') : 'ingresar');
$cuentaTexto = $usuario ? ($esDueno ? 'Panel' : 'Mi cuenta') : 'Ingresar';
$cajasUrl = $esNegocio ? m_url('inicio', ['cat' => 'cajas']) . '#menu' : m_url('registro');
$minimo = (int) $cfg['pedido_minimo'];
$gratis = (int) $cfg['despacho_gratis_desde'];

// Aviso del pedido mínimo y del reparto gratis en el carrito y al pagar.
$avisoMinimo = null;
if (in_array($vista, ['carrito', 'checkout'], true) && $unidades > 0) {
    $subtotal = (int) m_carrito(m_db(), $usuario)['subtotal'];
    if ($subtotal < $minimo) {
        $avisoMinimo = ['falta', 'Te faltan ' . m_clp($minimo - $subtotal) . ' para el pedido mínimo de ' . m_clp($minimo) . '.', $subtotal * 100 / $minimo];
    } elseif ($subtotal < $gratis) {
        $avisoMinimo = ['gratis', 'Pedido mínimo cumplido. Con ' . m_clp($gratis - $subtotal) . ' más, el reparto es gratis.', $subtotal * 100 / $gratis];
    } else {
        $avisoMinimo = ['ok', 'Tu pedido tiene reparto gratis.', 100];
    }
}
$caja = '<svg viewBox="0 0 32 32" width="34" height="34" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M4 9.5 16 4l12 5.5v13L16 28 4 22.5z"/><path d="M4 9.5 16 15l12-5.5M16 15v13"/><path d="m10 6.8 12 5.6v4.2" stroke-width="1.6"/></svg>';

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para una distribuidora y almacén en ' . $cfg['negocio']['comuna'] . ', Santiago: catálogo con buscador, precios por mayor para almacenes, colegios y oficinas, reparto con día, pago de prueba y panel del dueño.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Familjen+Grotesk:wght@500;600;700&family=Libre+Franklin:wght@400;500;600;700&family=Stardos+Stencil:wght@700&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#16233f',
    'icono' => ['S', '#16233f', '#e8c99a'],
    'clase_body' => 't bs bs-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<div class="bs-cinta">
  <p class="bs-ancho">
    <span>Pedido mínimo <strong><?= m_e(m_clp($minimo)) ?></strong></span>
    <span class="bs-cinta__sep" aria-hidden="true"></span>
    <span>Reparto gratis desde <strong><?= m_e(m_clp($gratis)) ?></strong></span>
    <span class="bs-cinta__sep bs-cinta__extra" aria-hidden="true"></span>
    <span class="bs-cinta__extra">Lunes a sábado, desde el día siguiente</span>
    <a class="bs-cinta__enlace" href="<?= m_e($cajasUrl) ?>"><?= $esNegocio ? 'Ver productos por caja' : 'Precios por mayor para negocios' ?></a>
  </p>
</div>
<header class="bs-cabecera">
  <div class="bs-ancho bs-cabecera__fila">
    <a class="bs-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <span class="bs-logo__caja"><?= $caja ?></span>
      <span class="bs-logo__texto"><span class="bs-logo__bodega">Bodega</span><span class="bs-logo__nombre">Surtido</span></span>
    </a>
    <form class="bs-buscar" role="search" method="get" action="<?= m_e($cfg['url_base']) ?>#menu">
      <label class="demo-sr" for="bs-q">Buscar en el catálogo</label>
      <input type="search" id="bs-q" name="q" value="<?= m_e($qActual) ?>" maxlength="40" placeholder="<?= m_e(m_texto('buscar_placeholder')) ?>">
      <button type="submit" aria-label="Buscar"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2.2"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg></button>
    </form>
    <nav class="bs-acciones" aria-label="Tu cuenta y tu pedido">
      <a class="bs-accion<?= in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto'], true) ? ' is-activo' : '' ?>" href="<?= m_e($cuentaUrl) ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="3.6"/><path d="M5 20c1.2-3.6 3.8-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
        <span><?= m_e($cuentaTexto) ?></span>
      </a>
      <a class="bs-accion bs-accion--pedido<?= in_array($vista, ['carrito', 'checkout', 'pago'], true) ? ' is-activo' : '' ?>" href="<?= m_e(m_url('carrito')) ?>" aria-label="Mi pedido, <?= $unidades ?> <?= $unidades === 1 ? 'producto' : 'productos' ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5z"/><path d="M3 7.5 12 12l9-4.5M12 12v9"/></svg>
        <span>Mi pedido</span>
        <?php if ($unidades > 0): ?><span class="bs-burbuja"><?= $unidades ?></span><?php endif; ?>
      </a>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="bs-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </nav>
  </div>
  <nav class="bs-pasillos" aria-label="Categorías">
    <div class="bs-ancho bs-pasillos__fila">
      <a href="<?= m_e($cfg['url_base']) ?>#menu"<?= $vista === 'inicio' && $catActual === '' && $qActual === '' ? ' class="is-activo"' : '' ?>>Todo</a>
      <?php foreach ($categorias as $c): ?>
      <?php if ($c['slug'] === 'cajas') { continue; } ?>
      <a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"<?= $catActual === $c['slug'] ? ' class="is-activo" aria-current="true"' : '' ?>><?= m_e($c['nombre']) ?></a>
      <?php endforeach; ?>
      <a class="bs-pasillos__cajas<?= $catActual === 'cajas' ? ' is-activo' : '' ?>" href="<?= m_e($cajasUrl) ?>"><?php if (!$esNegocio): ?><svg viewBox="0 0 24 24" width="15" height="15" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg><?php endif; ?>Por caja y saco</a>
    </div>
  </nav>
</header>

<main id="contenido" class="t-principal bs-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta de negocio está pendiente de aprobación. Mientras tanto ves los precios de tienda.</p>
  <?php elseif ($esNegocio): ?>
    <p class="bs-modo-negocio"><span class="bs-modo-negocio__sello">Cuenta de negocio</span> <?= m_e($usuario['empresa']) ?> · ves precios por mayor y productos por caja, saco y fardo</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?php if ($avisoMinimo !== null): ?>
  <div class="bs-minimo bs-minimo--<?= m_e($avisoMinimo[0]) ?>" role="status">
    <p><?= m_e($avisoMinimo[1]) ?></p>
    <div class="bs-minimo__barra" aria-hidden="true"><span style="width: <?= (int) min(100, round($avisoMinimo[2])) ?>%"></span></div>
  </div>
  <?php endif; ?>
  <?= $contenido ?>
</main>

<footer class="bs-pie">
  <div class="bs-ancho bs-pie__grid">
    <div>
      <p class="bs-logo bs-logo--pie"><span class="bs-logo__caja"><?= $caja ?></span><span class="bs-logo__texto"><span class="bs-logo__bodega">Bodega</span><span class="bs-logo__nombre">Surtido</span></span></p>
      <p><?= m_e($cfg['negocio']['eslogan']) ?>. <?= m_e($cfg['negocio']['comuna']) ?>, Santiago.</p>
      <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Bodega Surtido.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= m_e(demo_tel()) ?>">Llamar</a></p>
    </div>
    <div>
      <p class="bs-pie__titulo">Reparto en camión</p>
      <ul class="bs-pie__comunas">
        <?php foreach ($cfg['comunas'] as $comuna => $costo): ?><li><span><?= m_e($comuna) ?></span><span><?= m_e(m_clp((int) $costo)) ?></span></li><?php endforeach; ?>
      </ul>
    </div>
    <div>
      <p class="bs-pie__titulo">Horario de bodega</p>
      <?php demo_horario_tabla($cfg['negocio']['horario'], 'demo-horario bs-pie__horario'); ?>
    </div>
    <div>
      <p class="bs-pie__titulo">Para negocios</p>
      <p>Almacenes, colegios, jardines y oficinas: precios por mayor con una cuenta aprobada.</p>
      <p><a href="<?= m_e(m_url('registro')) ?>"><?= m_e(m_texto('crear_cuenta_mayorista')) ?></a> · <a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    </div>
  </div>
  <div class="bs-ancho"><?php demo_aviso_pie($cfg['rubro']); ?></div>
</footer>
<?php demo_scripts(); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
