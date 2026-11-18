<?php
/**
 * Layout de Onda Salones: salón con dos sedes, agenda online y tienda.
 * Franja lila, cabecera blanca con buscador, accesos a la cuenta y a la reserva, y categorías con íconos.
 * @var string $contenido @var string $titulo @var string $vista @var array $cfg @var ?array $usuario
 */
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
$proUrl = m_es_mayorista($usuario) ? m_url('inicio', ['cat' => 'profesional']) . '#menu' : m_url('registro');
// Íconos de las categorías (trazos simples, 24×24).
$iconos = [
    'cortes' => '<circle cx="6" cy="7" r="2.6"/><circle cx="6" cy="17" r="2.6"/><path d="M8.2 8.6 19 17M8.2 15.4 19 7"/>',
    'color' => '<path d="M12 3.5c3.2 4.2 5.3 7 5.3 9.8a5.3 5.3 0 0 1-10.6 0c0-2.8 2.1-5.6 5.3-9.8z"/><path d="M9.6 14.2a2.5 2.5 0 0 0 2.4 2.3"/>',
    'tratamientos' => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M18 6l-2.5 2.5M8.5 15.5 6 18"/>',
    'unas' => '<path d="M9 21V11a3 3 0 0 1 6 0v10z"/><path d="M10.5 8V4.5h3V8"/>',
    'cejas' => '<path d="M2.5 12s3.6-5.5 9.5-5.5S21.5 12 21.5 12s-3.6 5.5-9.5 5.5S2.5 12 2.5 12z"/><circle cx="12" cy="12" r="2.6"/><path d="M5 5.5c2-1.3 4.4-2 7-2s5 .7 7 2"/>',
    'novias' => '<circle cx="12" cy="15" r="5.5"/><path d="M9.5 4.5 12 7.5l2.5-3M9.5 4.5h5"/>',
    'tienda' => '<path d="M5 8h14l-1.2 12H6.2z"/><path d="M9 8a3 3 0 0 1 6 0"/>',
    'profesional' => '<rect x="3.5" y="7.5" width="17" height="12" rx="2"/><path d="M9 7.5V5.5h6v2M3.5 12.5h17"/>',
];
$icono = static fn (string $slug, int $tam = 20): string => isset($iconos[$slug])
    ? '<svg viewBox="0 0 24 24" width="' . $tam . '" height="' . $tam . '" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $iconos[$slug] . '</svg>'
    : '';
$ola = '<svg class="on-logo__ola" viewBox="0 0 40 24" width="40" height="24" aria-hidden="true" focusable="false"><path d="M2 16c4.5 0 4.5-8 9-8s4.5 8 9 8 4.5-8 9-8 4.5 8 9 8" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round"/></svg>';

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para un salón de belleza con dos sedes en Santiago: reserva de hora por día, hora y estilista, tienda de productos, cuentas para profesionales, pago de prueba y panel de la dueña.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Funnel+Display:wght@500;600;700;800&family=Funnel+Sans:wght@400;500;600;700&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#16131f',
    'icono' => ['o', '#c9b8ff', '#16131f'],
    'clase_body' => 't on on-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<p class="on-franja">
  <span>Agenda online las 24 horas · <strong>Las Condes</strong> y <strong>Ñuñoa</strong></span>
  <a href="<?= m_e($proUrl) ?>">¿Eres estilista? Compra a precio profesional</a>
</p>
<header class="on-cabecera">
  <div class="on-ancho on-cabecera__fila">
    <a class="on-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <?= $ola ?><span class="on-logo__texto">onda<small>salones</small></span>
    </a>
    <form class="on-buscar" role="search" method="get" action="<?= m_e($cfg['url_base']) ?>#menu">
      <label class="demo-sr" for="on-q">Buscar servicios y productos</label>
      <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      <input type="search" id="on-q" name="q" value="<?= m_e($qActual) ?>" maxlength="40" placeholder="<?= m_e(m_texto('buscar_placeholder', 'Buscar…')) ?>">
      <button type="submit">Buscar</button>
    </form>
    <nav class="on-acciones" aria-label="Tu cuenta y tu reserva">
      <a class="on-accion<?= in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto'], true) ? ' is-activo' : '' ?>" href="<?= m_e($cuentaUrl) ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="3.6"/><path d="M5 20c1.2-3.6 3.8-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
        <span><?= m_e($cuentaTexto) ?></span>
      </a>
      <a class="on-accion on-accion--reserva<?= in_array($vista, ['carrito', 'checkout', 'pago'], true) ? ' is-activo' : '' ?>" href="<?= m_e(m_url('carrito')) ?>" aria-label="Mi reserva, <?= $unidades ?> <?= $unidades === 1 ? 'ítem' : 'ítems' ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3.5" y="5" width="17" height="15" rx="3"/><path d="M3.5 10h17M8 3v4M16 3v4"/></svg>
        <span>Mi reserva</span>
        <?php if ($unidades > 0): ?><span class="on-burbuja"><?= $unidades ?></span><?php endif; ?>
      </a>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="on-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </nav>
  </div>
  <nav class="on-cats" aria-label="Categorías">
    <div class="on-ancho on-cats__fila">
      <a href="<?= m_e($cfg['url_base']) ?>#menu"<?= $vista === 'inicio' && $catActual === '' && $qActual === '' ? ' class="is-activo"' : '' ?>>Todo</a>
      <?php foreach ($categorias as $c): ?>
      <?php if ($c['slug'] === 'profesional') { continue; } ?>
      <a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"<?= $catActual === $c['slug'] ? ' class="is-activo" aria-current="true"' : '' ?>><?= $icono($c['slug'], 18) ?><?= m_e($c['nombre']) ?></a>
      <?php endforeach; ?>
      <a class="on-cats__pro<?= $catActual === 'profesional' ? ' is-activo' : '' ?>" href="<?= m_e($proUrl) ?>"><?= $icono('profesional', 18) ?>Profesionales</a>
    </div>
  </nav>
</header>

<main id="contenido" class="t-principal on-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta profesional está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif (m_es_mayorista($usuario)): ?>
    <p class="on-modo-pro"><strong>Cuenta profesional</strong> · <?= m_e($usuario['empresa']) ?> · ves precios de distribuidor e insumos profesionales</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="on-pie">
  <div class="on-ancho on-pie__grid">
    <div>
      <p class="on-logo on-pie__logo"><?= $ola ?><span class="on-logo__texto">onda<small>salones</small></span></p>
      <p><?= m_e($cfg['negocio']['eslogan']) ?>. Santiago de Chile.</p>
      <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Onda Salones.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= m_e(demo_tel()) ?>">Llamar</a></p>
    </div>
    <div>
      <p class="on-pie__titulo">Salones</p>
      <ul class="on-pie__sedes">
        <?php foreach ($cfg['sedes'] as $s): ?><li><strong><?= m_e($s['nombre']) ?></strong><span><?= m_e($s['detalle']) ?></span></li><?php endforeach; ?>
      </ul>
    </div>
    <div>
      <p class="on-pie__titulo">Servicios</p>
      <ul>
        <?php foreach ($categorias as $c): ?><?php if ($c['slug'] === 'profesional') { continue; } ?><li><a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"><?= m_e($c['nombre']) ?></a></li><?php endforeach; ?>
      </ul>
    </div>
    <div>
      <p class="on-pie__titulo">Horario</p>
      <?php demo_horario_tabla($cfg['negocio']['horario'], 'demo-horario on-pie__horario'); ?>
      <p class="on-pie__prueba"><a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    </div>
  </div>
  <div class="on-ancho"><?php demo_aviso_pie($cfg['rubro']); ?></div>
</footer>
<?php demo_scripts(); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
