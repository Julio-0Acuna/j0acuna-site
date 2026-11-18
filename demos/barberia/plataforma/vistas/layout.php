<?php
/**
 * Layout de Filo Barbería: cadena de barberías con agenda por barbero, tienda y cuentas para empresas.
 * Franja del poste de barbero, cabecera hueso con buscador y barra de categorías en tinta.
 * En el checkout agrega el selector de barbero (ver _barberos.php).
 * @var string $contenido @var string $titulo @var string $vista @var array $cfg @var ?array $usuario @var array $previo
 */
require_once dirname(__DIR__) . '/_barberos.php';

$cfg ??= m_config();
$usuario ??= m_usuario();
$vista ??= '';
$esDueno = m_es_dueno($usuario);
$unidades = array_sum(m_carrito_crudo());
$categorias = m_categorias(m_db());
$catActual = is_string($_GET['cat'] ?? null) ? $_GET['cat'] : '';
$qActual = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
$cuentaUrl = m_url($usuario ? ($esDueno ? 'panel' : 'cuenta') : 'ingresar');
$cuentaTexto = $usuario ? ($esDueno ? 'Panel' : 'Mi cuenta') : 'Ingresar';
$empresaUrl = m_es_mayorista($usuario) ? m_url('inicio', ['cat' => 'empresas']) . '#menu' : m_url('registro');
$barbero = fb_recordar_barbero($cfg);
if ($vista === 'checkout') {
    $contenido = fb_checkout_con_barbero($contenido, $cfg, is_array($previo ?? null) ? $previo : [], $barbero);
}
$opcion = $barbero !== '' ? fb_opciones($cfg)[$barbero] : null;

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para una cadena de barberías en Santiago: agenda por barbero con día y hora, planes mensuales, productos con carrito, cuentas para empresas con convenio, pago de prueba y panel del dueño.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter+Tight:wght@400;500;600;700&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#121316',
    'icono' => ['F', '#2343d6', '#f2efe8'],
    'clase_body' => 't fb fb-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<div class="fb-poste" aria-hidden="true"></div>
<header class="fb-cabecera">
  <div class="fb-ancho fb-cabecera__fila">
    <a class="fb-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <span class="fb-logo__marca" aria-hidden="true"></span>
      <span class="fb-logo__texto">Filo<small>Barbería · 3 sucursales</small></span>
    </a>
    <form class="fb-buscar" role="search" method="get" action="<?= m_e($cfg['url_base']) ?>#menu">
      <label class="demo-sr" for="fb-q">Buscar servicios y productos</label>
      <input type="search" id="fb-q" name="q" value="<?= m_e($qActual) ?>" maxlength="40" placeholder="<?= m_e(m_texto('buscar_placeholder', 'Buscar…')) ?>">
      <button type="submit" aria-label="Buscar"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="square"/></svg></button>
    </form>
    <nav class="fb-acciones" aria-label="Tu cuenta y tu reserva">
      <a class="fb-accion<?= in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto'], true) ? ' is-activo' : '' ?>" href="<?= m_e($cuentaUrl) ?>">
        <svg viewBox="0 0 24 24" width="21" height="21" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.6"/><path d="M5 20c1.2-3.6 3.8-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
        <span><?= m_e($cuentaTexto) ?></span>
      </a>
      <a class="fb-accion fb-accion--reserva<?= in_array($vista, ['carrito', 'checkout', 'pago'], true) ? ' is-activo' : '' ?>" href="<?= m_e(m_url('carrito')) ?>" aria-label="Mi reserva, <?= $unidades ?> <?= $unidades === 1 ? 'ítem' : 'ítems' ?>">
        <svg viewBox="0 0 24 24" width="21" height="21" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3.5" y="5" width="17" height="15"/><path d="M3.5 10h17M8 3v4M16 3v4"/></svg>
        <span>Mi reserva</span>
        <?php if ($unidades > 0): ?><span class="fb-burbuja"><?= $unidades ?></span><?php endif; ?>
      </a>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="fb-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </nav>
  </div>
  <nav class="fb-cats" aria-label="Categorías">
    <div class="fb-ancho fb-cats__fila">
      <a href="<?= m_e($cfg['url_base']) ?>#barberos">Barberos</a>
      <?php foreach ($categorias as $c): ?>
      <?php if ($c['slug'] === 'empresas') { continue; } ?>
      <a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"<?= $catActual === $c['slug'] ? ' class="is-activo" aria-current="true"' : '' ?>><?= m_e($c['nombre']) ?></a>
      <?php endforeach; ?>
      <a class="fb-cats__empresas<?= $catActual === 'empresas' ? ' is-activo' : '' ?>" href="<?= m_e($empresaUrl) ?>">Empresas</a>
    </div>
  </nav>
</header>

<main id="contenido" class="t-principal fb-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta de empresa está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif (m_es_mayorista($usuario)): ?>
    <p class="fb-modo-empresa"><strong>Cuenta de empresa</strong> · <?= m_e($usuario['empresa']) ?> · ves precios de convenio, packs y jornadas en tu oficina</p>
  <?php endif; ?>
  <?php if ($opcion !== null && in_array($vista, ['inicio', 'carrito'], true)): ?>
    <p class="fb-elegido"><span>Reservando con <strong><?= m_e($opcion[1] === 'el primero disponible' ? 'el primer barbero disponible' : $opcion[1]) ?></strong> · Sucursal <?= m_e($cfg['sucursales'][$opcion[2]]['nombre']) ?></span> <a href="<?= m_e($cfg['url_base']) ?>#barberos">Cambiar</a></p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="fb-pie">
  <div class="fb-ancho fb-pie__grid">
    <div>
      <p class="fb-pie__logo">Filo</p>
      <p><?= m_e($cfg['negocio']['eslogan']) ?>.</p>
      <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Filo Barbería.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= m_e(demo_tel()) ?>">Llamar</a></p>
    </div>
    <div>
      <p class="fb-pie__titulo">Sucursales</p>
      <ul>
        <?php foreach ($cfg['sucursales'] as $s): ?><li><?= m_e($s['nombre']) ?> <span>· <?= m_e($s['detalle']) ?></span></li><?php endforeach; ?>
      </ul>
    </div>
    <div>
      <p class="fb-pie__titulo">Horario</p>
      <?php demo_horario_tabla($cfg['negocio']['horario'], 'demo-horario fb-pie__horario'); ?>
    </div>
    <div>
      <p class="fb-pie__titulo">Cuenta</p>
      <ul>
        <li><a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></li>
        <li><a href="<?= m_e(m_url('registro')) ?>"><?= m_e(m_texto('crear_cuenta_mayorista')) ?></a></li>
        <li><a href="<?= m_e(m_url('inicio', ['cat' => 'planes'])) ?>#menu">Planes mensuales</a></li>
      </ul>
    </div>
  </div>
  <div class="fb-ancho"><?php demo_aviso_pie($cfg['rubro']); ?></div>
</footer>
<?php demo_scripts(); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
