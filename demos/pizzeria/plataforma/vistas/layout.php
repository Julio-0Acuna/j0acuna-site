<?php
/**
 * Layout de Cohete Pizza: estilo app, barra superior y barra de pestañas abajo en el celular.
 * @var string $contenido @var string $titulo @var string $vista @var array $cfg @var ?array $usuario @var int $unidades_carrito
 */
$cfg ??= m_config();
$usuario ??= m_usuario();
$unidades = (int) ($unidades_carrito ?? array_sum(m_carrito_crudo()));
$vista ??= '';
$esDueno = m_es_dueno($usuario);
$tabs = [
    'inicio' => [$cfg['url_base'] . '#menu', 'Menú', '<path d="M4 6h16M4 12h16M4 18h10"/>'],
    'carrito' => [m_url('carrito'), 'Carrito', '<path d="M5 7h14l-1.4 11H6.4z"/><path d="M9 7V5.5a3 3 0 0 1 6 0V7"/>'],
    'cuenta' => [m_url($usuario ? ($esDueno ? 'panel' : 'cuenta') : 'ingresar'), $usuario ? ($esDueno ? 'Panel' : 'Mi cuenta') : 'Ingresar', '<circle cx="12" cy="8" r="3.5"/><path d="M5 20c1.2-3.6 3.8-5.4 7-5.4s5.8 1.8 7 5.4"/>'],
];
$tabActual = match (true) {
    in_array($vista, ['carrito', 'checkout', 'pago'], true) => 'carrito',
    in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto', 'pedido'], true) => 'cuenta',
    default => 'inicio',
};
$scripts = ['qr'];
if ($vista === 'inicio') {
    $scripts[] = '3d';
}

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para una cadena de pizzerías con delivery en ' . $cfg['negocio']['comuna'] . ', Santiago: cuentas de clientes, mayoristas, pago de prueba y panel del dueño.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#ff5a1f',
    'icono' => ['C', '#ff5a1f', '#1a1423'],
    'clase_body' => 't cp cp-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<header class="cp-barra">
  <div class="cp-barra__fila">
    <a class="cp-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <span class="cp-logo__icono" aria-hidden="true">
        <svg viewBox="0 0 32 32" width="26" height="26" focusable="false"><path fill="currentColor" d="M20.6 3.4c3.4-.9 6.7-.6 7.6.4 1 .9 1.3 4.2.4 7.6-.8 3-2.7 6.2-5.8 9.1l.5 4.4-4.6 4.2-1.2-4.9-4.9-4.9-4.9-1.2 4.2-4.6 4.4.5c2.9-3.1 6.1-5 9.1-5.8zM21 8.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM7.3 21.7c1.4-1.4 3.6-1.4 5 0s1.4 3.6 0 5c-1.6 1.6-6.3 2.2-6.3 2.2s.6-4.7 1.3-7.2z"/></svg>
      </span>
      <span class="cp-logo__texto">Cohete<strong>Pizza</strong></span>
    </a>
    <p class="cp-ubicacion"><span class="cp-punto" aria-hidden="true"></span><?= m_e($cfg['negocio']['comuna']) ?> · delivery 30 min</p>
    <nav class="cp-nav" aria-label="Tienda">
      <?php foreach ($tabs as $clave => [$href, $nombre]): ?>
      <a href="<?= m_e($href) ?>"<?= $clave === $tabActual ? ' class="is-activo" aria-current="page"' : '' ?>><?= m_e($nombre) ?><?php if ($clave === 'carrito' && $unidades > 0): ?> <span class="cp-burbuja"><?= $unidades ?></span><?php endif; ?></a>
      <?php endforeach; ?>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="cp-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main id="contenido" class="t-principal cp-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta mayorista está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif (m_es_mayorista($usuario)): ?>
    <p class="cp-modo-mayorista">Modo mayorista · <?= m_e($usuario['empresa']) ?> · ves precios para negocios</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="cp-pie">
  <div class="cp-pie__fila">
    <p class="cp-logo__texto">Cohete<strong>Pizza</strong></p>
    <p><?= m_e($cfg['negocio']['comuna']) ?>, Santiago · Despacho en <?= count($cfg['comunas']) ?> comunas · Retiro en el local</p>
    <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Cohete Pizza.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= m_e(demo_tel()) ?>">Llamar</a> · <a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    <?php demo_aviso_pie($cfg['rubro']); ?>
  </div>
</footer>

<nav class="cp-tabs" aria-label="Navegación principal">
  <?php foreach ($tabs as $clave => [$href, $nombre, $icono]): ?>
  <a href="<?= m_e($href) ?>"<?= $clave === $tabActual ? ' class="is-activo" aria-current="page"' : '' ?>>
    <svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?= $icono ?></svg>
    <span><?= m_e($nombre) ?></span>
    <?php if ($clave === 'carrito' && $unidades > 0): ?><span class="cp-burbuja" aria-label="<?= $unidades ?> productos"><?= $unidades ?></span><?php endif; ?>
  </a>
  <?php endforeach; ?>
</nav>
<?php demo_scripts($scripts); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
