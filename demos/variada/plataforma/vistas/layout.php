<?php
/**
 * Layout de Mediodía: cabecera de papel con cinta de reparto, pie azul petróleo.
 * @var string $contenido @var string $titulo @var string $vista @var array $cfg @var ?array $usuario @var int $unidades_carrito
 */
$cfg ??= m_config();
$usuario ??= m_usuario();
$unidades = (int) ($unidades_carrito ?? array_sum(m_carrito_crudo()));
$vista ??= '';
$esDueno = m_es_dueno($usuario);
$cuentaUrl = m_url($usuario ? ($esDueno ? 'panel' : 'cuenta') : 'ingresar');
$cuentaTexto = $usuario ? ($esDueno ? 'Panel' : 'Mi cuenta') : 'Ingresar';
$scripts = ['qr'];
if ($vista === 'inicio') {
    $scripts[] = '3d';
}
// Próximo día de reparto (para la cinta superior).
$fechas = m_fechas_entrega($cfg);
$proximo = $fechas ? (string) reset($fechas) : '';

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para una cocina de colaciones en ' . $cfg['negocio']['comuna'] . ', Santiago: pedidos con fecha para oficinas, cuentas para empresas, pago de prueba y panel del dueño.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Gloock&family=Schibsted+Grotesk:wght@400;500;600;700;800&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#10343a',
    'icono' => ['M', '#10343a', '#c6ec5f'],
    'clase_body' => 't md md-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<header class="md-cabecera">
  <div class="md-cabecera__fila">
    <a class="md-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <svg class="md-logo__sol" viewBox="0 0 40 40" width="34" height="34" aria-hidden="true" focusable="false">
        <circle cx="20" cy="20" r="18" fill="#10343a"/>
        <path d="M20 2a18 18 0 0 1 0 36z" fill="#c6ec5f"/>
        <path d="M20 9v11l7 4" fill="none" stroke="#f4f1ea" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <span class="md-logo__texto">mediodía</span>
    </a>
    <nav class="md-nav" aria-label="Tienda">
      <a href="<?= m_e($cfg['url_base']) ?>#semana" class="md-nav__solo-ancho">Menú de la semana</a>
      <a href="<?= m_e($cfg['url_base']) ?>#menu">Menú</a>
      <?php if (!$usuario): ?><a href="<?= m_e(m_url('registro')) ?>" class="md-nav__solo-ancho">Empresas</a><?php endif; ?>
      <a href="<?= m_e($cuentaUrl) ?>"<?= in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto'], true) ? ' aria-current="page" class="is-activo"' : '' ?>><?= m_e($cuentaTexto) ?></a>
      <a class="md-carrito<?= in_array($vista, ['carrito', 'checkout', 'pago'], true) ? ' is-activo' : '' ?>" href="<?= m_e(m_url('carrito')) ?>" aria-label="Carrito, <?= $unidades ?> producto<?= $unidades === 1 ? '' : 's' ?>">
        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h16l-1.5 11h-13z"/><path d="M8.5 8V6.5a3.5 3.5 0 0 1 7 0V8"/></svg>
        <span class="md-carrito__texto">Pedido</span>
        <?php if ($unidades > 0): ?><span class="md-carrito__num"><?= $unidades ?></span><?php endif; ?>
      </a>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="md-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </nav>
  </div>
  <p class="md-cinta">
    <span>Repartimos de lunes a viernes, de 12:00 a 14:30</span>
    <?php if ($proximo !== ''): ?><span class="md-cinta__proximo">Próximo reparto: <strong><?= m_e($proximo) ?></strong></span><?php endif; ?>
  </p>
</header>

<main id="contenido" class="t-principal md-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta de empresa está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif (m_es_mayorista($usuario)): ?>
    <p class="md-modo-empresa"><span>Empresa</span> <?= m_e($usuario['empresa']) ?> · ves precio empresa y los packs para reuniones</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="md-pie">
  <div class="md-pie__fila">
    <div>
      <p class="md-logo__texto md-pie__marca">mediodía</p>
      <p><?= m_e($cfg['negocio']['eslogan']) ?>.</p>
    </div>
    <div>
      <p class="md-pie__titulo">Cocina</p>
      <p><?= m_e($cfg['negocio']['comuna']) ?>, Santiago · Lunes a viernes</p>
      <p>Despacho en <?= count($cfg['comunas']) ?> comunas · Retiro en la cocina</p>
    </div>
    <div>
      <p class="md-pie__titulo">Contacto</p>
      <p><a href="<?= m_e(m_wa('Hola, quiero cotizar colaciones para mi empresa con Mediodía.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= m_e(demo_tel()) ?>">Llamar</a> · <a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    </div>
  </div>
  <div class="md-pie__fila md-pie__fila--aviso"><?php demo_aviso_pie($cfg['rubro']); ?></div>
</footer>
<?php demo_scripts($scripts); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
