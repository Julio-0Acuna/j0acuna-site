<?php
/**
 * Layout de Causa Común: franja de aviso amarilla, barra con el logo en capas (como una causa)
 * y, en el celular, las secciones como pestañas que se deslizan bajo la barra.
 * @var string $contenido @var string $titulo @var string $vista @var array $cfg @var ?array $usuario @var int $unidades_carrito
 */
$cfg ??= m_config();
$usuario ??= m_usuario();
$unidades = (int) ($unidades_carrito ?? array_sum(m_carrito_crudo()));
$vista ??= '';
$esDueno = m_es_dueno($usuario);
$estado = demo_horario_estado($cfg['negocio']['horario']);
$cuentaUrl = m_url($usuario ? ($esDueno ? 'panel' : 'cuenta') : 'ingresar');
$cuentaTexto = $usuario ? ($esDueno ? 'Panel' : 'Mi cuenta') : 'Ingresar';
$enCuenta = in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto', 'pedido'], true);
$enCarrito = in_array($vista, ['carrito', 'checkout', 'pago'], true);
$catActual = is_string($_GET['cat'] ?? null) ? $_GET['cat'] : '';
$secciones = [
    'carta' => [$cfg['url_base'] . '#menu', 'Carta', $vista === 'inicio' && $catActual === ''],
    'colaciones' => [m_url('inicio', ['cat' => 'colaciones']) . '#menu', 'Colaciones', $vista === 'inicio' && $catActual === 'colaciones'],
    'empresas' => [m_es_mayorista($usuario) ? m_url('inicio', ['cat' => 'empresas']) . '#menu' : $cfg['url_base'] . '#empresas', 'Empresas', $vista === 'inicio' && $catActual === 'empresas'],
];
$scripts = ['qr'];
if ($vista === 'inicio') {
    $scripts[] = '3d';
}

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para una cocina peruana con delivery y colaciones para empresas en ' . $cfg['negocio']['comuna'] . ', Santiago: pedidos programados por día, cuentas de clientes y empresas, pago de prueba y panel del dueño.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Gabarito:wght@500;600;700;800;900&family=Onest:wght@400;500;600;700&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#ffc83d',
    'icono' => ['C', '#4b1e5a', '#ffc83d'],
    'clase_body' => 't cc cc-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<div class="cc-franja">
  <p><span class="cc-punto <?= $estado['abierto'] ? 'is-abierto' : '' ?>" aria-hidden="true"></span><?= m_e($estado['texto']) ?> · <?= m_e($cfg['negocio']['comuna']) ?> · <strong>Pide para hoy o programa hasta 6 días</strong></p>
</div>
<header class="cc-barra">
  <div class="cc-barra__fila">
    <a class="cc-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <span class="cc-logo__capas" aria-hidden="true"><span></span><span></span><span></span></span>
      <span class="cc-logo__texto">Causa<br>Común</span>
    </a>
    <nav class="cc-nav" aria-label="Secciones de la tienda">
      <?php foreach ($secciones as [$href, $nombre, $activo]): ?>
      <a href="<?= m_e($href) ?>"<?= $activo ? ' class="is-activo" aria-current="page"' : '' ?>><?= m_e($nombre) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="cc-barra__acciones">
      <a class="cc-cuenta<?= $enCuenta ? ' is-activo' : '' ?>" href="<?= m_e($cuentaUrl) ?>"<?= $enCuenta ? ' aria-current="page"' : '' ?>>
        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><circle cx="12" cy="8" r="3.6"/><path d="M5 20c1.2-3.6 3.8-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
        <span><?= m_e($cuentaTexto) ?></span>
      </a>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="cc-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
      <a class="cc-carrito<?= $enCarrito ? ' is-activo' : '' ?>" href="<?= m_e(m_url('carrito')) ?>"<?= $enCarrito ? ' aria-current="page"' : '' ?> aria-label="Carrito, <?= $unidades ?> <?= $unidades === 1 ? 'producto' : 'productos' ?>">
        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 9h14l-1.3 10.2a1.6 1.6 0 0 1-1.6 1.3H7.9a1.6 1.6 0 0 1-1.6-1.3z"/><path d="M9 9V7a3 3 0 0 1 6 0v2"/></svg>
        <span class="cc-carrito__texto">Carrito</span>
        <span class="cc-carrito__num"><?= $unidades ?></span>
      </a>
    </div>
  </div>
  <nav class="cc-pestanas" aria-label="Secciones">
    <?php foreach ($secciones as [$href, $nombre, $activo]): ?>
    <a href="<?= m_e($href) ?>"<?= $activo ? ' class="is-activo" aria-current="page"' : '' ?>><?= m_e($nombre) ?></a>
    <?php endforeach; ?>
    <a href="<?= m_e($cuentaUrl) ?>"<?= $enCuenta ? ' class="is-activo"' : '' ?>><?= m_e($cuentaTexto) ?></a>
    <?php if ($usuario): ?>
    <form method="post" action="<?= m_e(m_url('salir')) ?>" class="cc-pestanas__salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
    <?php endif; ?>
  </nav>
</header>

<main id="contenido" class="t-principal cc-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta para empresas está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif (m_es_mayorista($usuario)): ?>
    <p class="cc-modo-empresa"><span class="cc-modo-empresa__sello">Cuenta empresa</span> <?= m_e($usuario['empresa']) ?> · ves precios de colación y productos para oficinas</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="cc-pie">
  <div class="cc-pie__capas" aria-hidden="true"><span></span><span></span><span></span></div>
  <div class="cc-pie__fila">
    <div>
      <p class="cc-logo__texto cc-logo__texto--pie">Causa Común</p>
      <p><?= m_e($cfg['negocio']['eslogan']) ?>. Cocina en <?= m_e($cfg['negocio']['comuna']) ?>, despacho en <?= count($cfg['comunas']) ?> comunas.</p>
    </div>
    <div>
      <p class="cc-pie__titulo">Horario de cocina</p>
      <?php demo_horario_tabla($cfg['negocio']['horario'], 'demo-horario cc-pie__horario'); ?>
    </div>
    <div>
      <p class="cc-pie__titulo">Contacto</p>
      <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Causa Común.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= m_e(demo_tel()) ?>">Llamar</a></p>
      <p><a href="<?= m_e(m_url('registro')) ?>">Cuenta para empresas</a></p>
      <p><a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    </div>
  </div>
  <div class="cc-pie__fila cc-pie__aviso"><?php demo_aviso_pie($cfg['rubro']); ?></div>
</footer>
<?php demo_scripts($scripts); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
