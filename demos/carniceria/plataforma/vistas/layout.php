<?php
/**
 * Layout de Kilo Justo: tienda online de carnicería con barra de búsqueda, categorías
 * y botón flotante del carrito en el celular.
 * @var string $contenido @var string $titulo @var string $vista @var array $cfg @var ?array $usuario
 */
require_once __DIR__ . '/_kilo.php';

$cfg ??= m_config();
$usuario ??= m_usuario();
$vista ??= '';
$esDueno = m_es_dueno($usuario);
$productosCarrito = count(m_carrito_crudo());
$categorias = m_categorias(m_db());
$catActual = is_string($_GET['cat'] ?? null) ? $_GET['cat'] : '';
$qActual = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
$scripts = ['qr'];
if ($vista === 'inicio') {
    $scripts[] = '3d';
}
$cuentaUrl = m_url($usuario ? ($esDueno ? 'panel' : 'cuenta') : 'ingresar');
$cuentaTexto = $usuario ? ($esDueno ? 'Panel' : 'Mi cuenta') : 'Ingresar';

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para una carnicería en ' . $cfg['negocio']['comuna'] . ', Santiago: venta por kilo con selector de gramos, cuentas de clientes, precios mayoristas para restaurantes, pago de prueba y panel del dueño.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&family=Barlow+Condensed:wght@600;700;800&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#c8102e',
    'icono' => ['K', '#c8102e', '#ffffff'],
    'clase_body' => 't kj kj-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<p class="kj-util">
  <span class="kj-util__punto" aria-hidden="true"></span>
  <span>Despacho <strong>hoy mismo</strong> si pides antes de las 14:00</span>
  <span class="kj-util__sep" aria-hidden="true">·</span>
  <a href="<?= m_e(m_url('registro')) ?>">Precios mayoristas para restaurantes</a>
</p>
<header class="kj-cabecera">
  <div class="kj-ancho kj-cabecera__fila">
    <a class="kj-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <svg class="kj-logo__icono" viewBox="0 0 40 40" width="38" height="38" aria-hidden="true" focusable="false">
        <circle cx="20" cy="22" r="15" fill="#c8102e"/>
        <circle cx="20" cy="22" r="10.5" fill="#fff"/>
        <path d="M20 22 26 15" stroke="#14181d" stroke-width="2.4" stroke-linecap="round"/>
        <circle cx="20" cy="22" r="2.2" fill="#14181d"/>
        <path d="M13 6h14" stroke="#14181d" stroke-width="3" stroke-linecap="round"/>
        <path d="M14 15.5h.01M20 13h.01M26 15.5h.01" stroke="#c8102e" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <span class="kj-logo__texto">Kilo<strong>Justo</strong></span>
    </a>
    <form class="kj-buscar-cab" role="search" method="get" action="<?= m_e($cfg['url_base']) ?>#menu">
      <label class="demo-sr" for="kj-q-cab">Buscar en la tienda</label>
      <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      <input type="search" id="kj-q-cab" name="q" value="<?= m_e($qActual) ?>" maxlength="40" placeholder="<?= m_e(m_texto('buscar_placeholder', 'Buscar…')) ?>">
      <button type="submit">Buscar</button>
    </form>
    <nav class="kj-acciones" aria-label="Tu cuenta y carrito">
      <a class="kj-accion<?= in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto'], true) ? ' is-activo' : '' ?>" href="<?= m_e($cuentaUrl) ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="3.6"/><path d="M5 20c1.2-3.6 3.8-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
        <span><?= m_e($cuentaTexto) ?></span>
      </a>
      <a class="kj-accion kj-accion--carrito<?= in_array($vista, ['carrito', 'checkout', 'pago'], true) ? ' is-activo' : '' ?>" href="<?= m_e(m_url('carrito')) ?>" aria-label="Carrito, <?= $productosCarrito ?> <?= $productosCarrito === 1 ? 'producto' : 'productos' ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"><path d="M3 4h2.5l2.2 11h10.6L20.5 7H7"/><circle cx="9.5" cy="19" r="1.4"/><circle cx="17" cy="19" r="1.4"/></svg>
        <span>Carrito</span>
        <?php if ($productosCarrito > 0): ?><span class="kj-burbuja"><?= $productosCarrito ?></span><?php endif; ?>
      </a>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="kj-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </nav>
  </div>
  <nav class="kj-cats" aria-label="Categorías">
    <div class="kj-ancho kj-cats__fila">
      <a href="<?= m_e($cfg['url_base']) ?>#menu"<?= $vista === 'inicio' && $catActual === '' && $qActual === '' ? ' class="is-activo"' : '' ?>>Todo</a>
      <?php foreach ($categorias as $c): ?>
      <?php if ($c['slug'] === 'restaurantes' && !m_es_mayorista($usuario)) { continue; } ?>
      <a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"<?= $catActual === $c['slug'] ? ' class="is-activo" aria-current="true"' : '' ?>><?= m_e($c['nombre']) ?></a>
      <?php endforeach; ?>
      <a class="kj-cats__mayorista" href="<?= m_e(m_es_mayorista($usuario) ? m_url('inicio', ['cat' => 'restaurantes']) . '#menu' : m_url('registro')) ?>">Para restaurantes</a>
    </div>
  </nav>
</header>

<main id="contenido" class="t-principal kj-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta mayorista está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif (m_es_mayorista($usuario)): ?>
    <p class="kj-modo-mayorista"><strong>Cuenta mayorista</strong> · <?= m_e($usuario['empresa']) ?> · ves precios para negocios y las cajas para restaurantes</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="kj-pie">
  <div class="kj-ancho kj-pie__grid">
    <div>
      <p class="kj-logo__texto kj-pie__logo">Kilo<strong>Justo</strong></p>
      <p><?= m_e($cfg['negocio']['eslogan']) ?>. Bodega en <?= m_e($cfg['negocio']['comuna']) ?>, Santiago.</p>
    </div>
    <div>
      <p class="kj-pie__titulo">Comprar</p>
      <ul>
        <?php foreach ($categorias as $c): ?><?php if ($c['slug'] === 'restaurantes') { continue; } ?><li><a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"><?= m_e($c['nombre']) ?></a></li><?php endforeach; ?>
      </ul>
    </div>
    <div>
      <p class="kj-pie__titulo">Despacho</p>
      <p>En <?= count($cfg['comunas']) ?> comunas desde <?= m_e(m_clp((int) min($cfg['comunas']))) ?>. Gratis sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?>. Pedido mínimo <?= m_e(m_clp((int) $cfg['pedido_minimo'])) ?>.</p>
      <p><a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    </div>
    <div>
      <p class="kj-pie__titulo">Contacto</p>
      <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Kilo Justo.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= m_e(demo_tel()) ?>">Llamar</a></p>
      <?php demo_horario_tabla($cfg['negocio']['horario'], 'demo-horario kj-pie__horario'); ?>
    </div>
  </div>
  <div class="kj-ancho"><?php demo_aviso_pie($cfg['rubro']); ?></div>
</footer>

<?php if (!in_array($vista, ['carrito', 'checkout', 'pago'], true)): ?>
<a class="kj-flotante" href="<?= m_e(m_url('carrito')) ?>" aria-label="Ver carrito, <?= $productosCarrito ?> <?= $productosCarrito === 1 ? 'producto' : 'productos' ?>">
  <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 4h2.5l2.2 11h10.6L20.5 7H7"/><circle cx="9.5" cy="19" r="1.4"/><circle cx="17" cy="19" r="1.4"/></svg>
  <span>Carrito</span>
  <span class="kj-flotante__num"><?= $productosCarrito ?></span>
</a>
<?php endif; ?>
<?php demo_scripts($scripts); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
<script src="<?= m_e(demo_asset($cfg['url_base'] . 'kilo.js')) ?>" defer></script>
</body>
</html>
