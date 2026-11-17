<?php
/**
 * Layout de Merengue Club: tienda pop con barra superior en píldora y pie de color.
 * @var string $contenido @var string $titulo @var string $vista @var array $cfg @var ?array $usuario @var int $unidades_carrito
 */
$cfg ??= m_config();
$usuario ??= m_usuario();
$unidades = (int) ($unidades_carrito ?? array_sum(m_carrito_crudo()));
$vista ??= '';
$esDueno = m_es_dueno($usuario);
$esMayorista = m_es_mayorista($usuario);
$cuentaUrl = m_url($usuario ? ($esDueno ? 'panel' : 'cuenta') : 'ingresar');
$cuentaTexto = $usuario ? ($esDueno ? 'Panel' : 'Mi cuenta') : 'Ingresar';
$enlaces = [
    'tienda' => [$cfg['url_base'] . '#menu', 'Tienda'],
    'mesas' => [m_url('inicio', ['cat' => 'mesas']) . '#menu', 'Mesas dulces'],
    'cafeterias' => [$esMayorista ? m_url('inicio', ['cat' => 'cafeterias']) . '#menu' : $cfg['url_base'] . '#cafeterias', 'Cafeterías'],
];
$scripts = ['qr'];
if ($vista === 'inicio') {
    $scripts[] = '3d';
}

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online de tortas con encargos por fecha, mayoristas para cafeterías y panel del dueño, en ' . $cfg['negocio']['comuna'] . ', Santiago.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Albert+Sans:wght@400;500;600;700&family=Unbounded:wght@500;700;800&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#ff4f7b',
    'icono' => ['m', '#ff4f7b', '#fff7ee'],
    'clase_body' => 't mc mc-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<header class="mc-cabecera">
  <div class="mc-cabecera__fila">
    <a class="mc-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <span class="mc-logo__icono" aria-hidden="true">
        <svg viewBox="0 0 40 40" width="34" height="34" focusable="false"><path fill="#fff7ee" d="M20 4c3 3.2 1.4 6 4.6 7.4 3.4 1.5 6.9.2 7.8 4.3.8 3.7-2.7 5-1.6 8.2 1 3.2 4.4 4.6 2.4 8.3-1.8 3.2-6 1.9-9.3 3.4-2.6 1.2-2.6 3.4-3.9 3.4s-1.3-2.2-3.9-3.4c-3.3-1.5-7.5-.2-9.3-3.4-2-3.7 1.4-5.1 2.4-8.3 1.1-3.2-2.4-4.5-1.6-8.2.9-4.1 4.4-2.8 7.8-4.3C18.6 10 17 7.2 20 4z"/><path fill="none" stroke="#ff4f7b" stroke-width="2.4" stroke-linecap="round" d="M13 20c2.5-3 5.5-3 7 0s4.5 3 7 0M14 27c2-2.2 4.2-2.2 6 0s4 2.2 6 0"/></svg>
      </span>
      <span class="mc-logo__texto">merengue<span>club</span></span>
    </a>
    <nav class="mc-nav" aria-label="Tienda">
      <?php foreach ($enlaces as [$href, $nombre]): ?><a href="<?= m_e($href) ?>"><?= m_e($nombre) ?></a><?php endforeach; ?>
    </nav>
    <div class="mc-cabecera__acciones">
      <a class="mc-accion<?= in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto', 'pedido'], true) ? ' is-activo' : '' ?>" href="<?= m_e($cuentaUrl) ?>"<?= in_array($vista, ['cuenta', 'ingresar', 'panel'], true) ? ' aria-current="page"' : '' ?>>
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="3.6"/><path d="M4.8 20c1.3-3.7 4-5.6 7.2-5.6s5.9 1.9 7.2 5.6"/></svg>
        <span class="mc-accion__texto"><?= m_e($cuentaTexto) ?></span>
      </a>
      <a class="mc-accion mc-accion--carrito<?= in_array($vista, ['carrito', 'checkout', 'pago'], true) ? ' is-activo' : '' ?>" href="<?= m_e(m_url('carrito')) ?>"<?= $vista === 'carrito' ? ' aria-current="page"' : '' ?> aria-label="Carrito<?= $unidades > 0 ? ', ' . $unidades . ($unidades === 1 ? ' producto' : ' productos') : ', vacío' ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M5 8h14l-1.3 12H6.3z"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8"/></svg>
        <span class="mc-accion__texto">Carrito</span>
        <?php if ($unidades > 0): ?><span class="mc-burbuja" aria-hidden="true"><?= $unidades ?></span><?php endif; ?>
      </a>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="mc-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </div>
  </div>
  <nav class="mc-nav-movil" aria-label="Tienda (celular)">
    <?php foreach ($enlaces as [$href, $nombre]): ?><a href="<?= m_e($href) ?>"><?= m_e($nombre) ?></a><?php endforeach; ?>
    <?php if ($usuario): ?>
    <form method="post" action="<?= m_e(m_url('salir')) ?>" class="mc-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
    <?php endif; ?>
  </nav>
</header>

<main id="contenido" class="t-principal mc-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta mayorista está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif ($esMayorista): ?>
    <p class="mc-modo-mayorista"><strong>Modo cafetería</strong> · <?= m_e($usuario['empresa']) ?> · ves precios mayoristas y productos para cafeterías</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="mc-pie">
  <div class="mc-pie__fila">
    <p class="mc-pie__marca" aria-hidden="true">merengue<span>club</span></p>
    <div class="mc-pie__columnas">
      <p><strong><?= m_e($cfg['negocio']['nombre']) ?></strong><br><?= m_e($cfg['negocio']['comuna']) ?>, Santiago · Despacho en <?= count($cfg['comunas']) ?> comunas · Retiro en el local</p>
      <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Merengue Club.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= m_e(demo_tel()) ?>">Llamar</a> · <a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    </div>
    <?php demo_aviso_pie($cfg['rubro']); ?>
  </div>
</footer>
<?php demo_scripts($scripts); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
