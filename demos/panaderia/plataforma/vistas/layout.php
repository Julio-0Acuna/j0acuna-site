<?php
/**
 * Layout de Panificadora Alba: barra cobalto con el horario de reparto, letras condensadas de caja de reparto.
 * @var string $contenido @var string $titulo @var string $vista @var array $cfg @var ?array $usuario @var int $unidades_carrito
 */
$cfg ??= m_config();
$usuario ??= m_usuario();
$unidades = (int) ($unidades_carrito ?? array_sum(m_carrito_crudo()));
$vista ??= '';
$esDueno = m_es_dueno($usuario);
$enlaces = [
    'inicio' => [$cfg['url_base'] . '#menu', 'Catálogo', '<path d="M4 5h16v4H4zM4 11h7v8H4zM13 11h7v8h-7z"/>'],
    'carrito' => [m_url('carrito'), 'Carrito', '<path d="M3 4h3l2.2 11h10.3L21 8H7"/><circle cx="10" cy="19.5" r="1.4"/><circle cx="17" cy="19.5" r="1.4"/>'],
    'cuenta' => [m_url($usuario ? ($esDueno ? 'panel' : 'cuenta') : 'ingresar'), $usuario ? ($esDueno ? 'Panel' : 'Mi cuenta') : 'Ingresar', '<circle cx="12" cy="8" r="3.6"/><path d="M4.8 20c1.3-3.7 4-5.6 7.2-5.6s5.9 1.9 7.2 5.6"/>'],
];
$actual = match (true) {
    in_array($vista, ['carrito', 'checkout', 'pago'], true) => 'carrito',
    in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto', 'pedido'], true) => 'cuenta',
    default => 'inicio',
};
$scripts = ['qr'];
if ($vista === 'inicio') {
    $scripts[] = '3d';
}
$ruta = $cfg['ruta'] ?? [];

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para una panificadora con reparto al amanecer en ' . $cfg['negocio']['comuna'] . ', Santiago: cuentas de clientes, precios mayoristas para almacenes y restaurantes, pago de prueba y panel del dueño.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Barlow:wght@400;500;600;700&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#1f3bb3',
    'icono' => ['A', '#1f3bb3', '#ffd23f'],
    'clase_body' => 't al al-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<header class="al-cabecera">
  <div class="al-ancho al-cabecera__fila">
    <a class="al-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <svg class="al-logo__sol" viewBox="0 0 48 48" width="44" height="44" aria-hidden="true" focusable="false">
        <path d="M8 34a16 16 0 0 1 32 0z" fill="#ffd23f"/>
        <g stroke="#ffd23f" stroke-width="3" stroke-linecap="round"><path d="M24 6v6M9.5 12.5l4 4M38.5 12.5l-4 4M3 25h6M39 25h6"/></g>
        <path d="M4 38h40" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
      </svg>
      <span class="al-logo__texto"><small>Panificadora</small>Alba</span>
    </a>
    <?php if ($ruta): ?>
    <p class="al-reparto-chip"><span aria-hidden="true">●</span> Pide hasta las <?= m_e($ruta[0][0]) ?> · llega mañana <?= m_e($ruta[2][0]) ?>–<?= m_e($ruta[3][0]) ?></p>
    <?php endif; ?>
    <nav class="al-nav" aria-label="Tienda">
      <?php foreach ($enlaces as $clave => [$href, $nombre, $icono]): ?>
      <a href="<?= m_e($href) ?>"<?= $clave === $actual ? ' class="is-activo" aria-current="page"' : '' ?>>
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?= $icono ?></svg>
        <span class="al-nav__texto"><?= m_e($nombre) ?></span>
        <?php if ($clave === 'carrito' && $unidades > 0): ?><span class="al-burbuja" aria-label="<?= $unidades ?> productos"><?= $unidades ?></span><?php endif; ?>
      </a>
      <?php endforeach; ?>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="al-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main id="contenido" class="t-principal al-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta mayorista está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif (m_es_mayorista($usuario)): ?>
    <p class="al-modo-mayorista"><strong>Modo mayorista</strong> · <?= m_e($usuario['empresa']) ?> · ves precios por mayor y la sección «Para negocios»</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="al-pie">
  <div class="al-ancho al-pie__grid">
    <div>
      <p class="al-logo__texto al-logo__texto--pie"><small>Panificadora</small>Alba</p>
      <p><?= m_e($cfg['negocio']['eslogan']) ?>.</p>
      <p><?= m_e($cfg['negocio']['comuna']) ?>, Santiago · Reparto en <?= count($cfg['comunas']) ?> comunas · Retiro en la planta</p>
    </div>
    <?php if ($ruta): ?>
    <ol class="al-pie__ruta" aria-label="Horario del reparto">
      <?php foreach ($ruta as [$hora, $texto]): ?><li><strong><?= m_e($hora) ?></strong> <?= m_e($texto) ?></li><?php endforeach; ?>
    </ol>
    <?php endif; ?>
    <div>
      <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Panificadora Alba.')) ?>" target="_blank" rel="noopener">WhatsApp</a></p>
      <p><a href="<?= m_e(demo_tel()) ?>">Llamar</a></p>
      <p><a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    </div>
  </div>
  <div class="al-ancho"><?php demo_aviso_pie($cfg['rubro']); ?></div>
</footer>
<?php demo_scripts($scripts); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
