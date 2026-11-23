<?php
/**
 * Layout de Tina Rodante: peluquería canina en furgón con agenda y tienda online.
 * Cinta verde con la ruta de hoy, cabecera amarillo girasol con el furgón, buscador, cuenta y reserva,
 * y una barra de categorías. En Mi cuenta suma "Tus perros" (vistas/_mis_perros.php) antes del historial.
 * @var string $contenido @var string $titulo @var string $vista @var array $cfg @var ?array $usuario @var array $pedidos
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
$convenioUrl = m_es_mayorista($usuario) ? m_url('inicio', ['cat' => 'convenios']) . '#menu' : m_url('registro');
$hoy = (int) (new DateTimeImmutable('now', new DateTimeZone($cfg['zona_horaria'])))->format('N');
$rutaHoy = $cfg['ruta'][$hoy] ?? [];

// Mi cuenta de un cliente: la ficha de sus perros va antes del historial de reservas.
if ($vista === 'cuenta' && $usuario && !$esDueno && !empty($pedidos)) {
    ob_start();
    m_parcial('_mis_perros', ['pedidos' => $pedidos, 'cfg' => $cfg]);
    $fichas = (string) ob_get_clean();
    $ancla = '<section class="t-caja" aria-labelledby="historial">';
    $contenido = str_contains($contenido, $ancla) ? preg_replace('/' . preg_quote($ancla, '/') . '/', $fichas . $ancla, $contenido, 1) : $contenido . $fichas;
}

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para una peluquería canina en furgón que va a la casa, en ' . $cfg['negocio']['comuna'] . ', Santiago: agenda con día y hora, ficha de cada perro, tienda de shampoo y accesorios, convenios para condominios, pago de prueba y panel de la dueña.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600;700&family=Kanit:ital,wght@0,500;0,600;0,700;0,800;1,700;1,800&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#ffc83a',
    'icono' => ['T', '#10392c', '#ffc83a'],
    'clase_body' => 't tr tr-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<p class="tr-cinta">
  <span><?php if ($rutaHoy): ?>Hoy el furgón anda por <strong><?= m_e(implode(' y ', $rutaHoy)) ?></strong><?php else: ?>Hoy el furgón descansa: <strong>agenda para la semana</strong><?php endif; ?></span>
  <span class="tr-cinta__sep" aria-hidden="true">•</span>
  <span>Traslado gratis sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?></span>
  <span class="tr-cinta__sep" aria-hidden="true">•</span>
  <a href="<?= m_e($convenioUrl) ?>">Convenios para condominios</a>
</p>
<header class="tr-cabecera">
  <div class="tr-ancho tr-cabecera__fila">
    <a class="tr-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <?php m_parcial('_furgon', ['clase' => 'tr-furgon--logo']); ?>
      <span class="tr-logo__texto">Tina Rodante<small>peluquería canina a domicilio</small></span>
    </a>
    <form class="tr-buscar" role="search" method="get" action="<?= m_e($cfg['url_base']) ?>#menu">
      <label class="demo-sr" for="tr-q">Buscar servicios y productos</label>
      <input type="search" id="tr-q" name="q" value="<?= m_e($qActual) ?>" maxlength="40" placeholder="<?= m_e(m_texto('buscar_placeholder', 'Buscar…')) ?>">
      <button type="submit" aria-label="Buscar"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2.4"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg></button>
    </form>
    <nav class="tr-acciones" aria-label="Tu cuenta y tu reserva">
      <a class="tr-accion<?= in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto'], true) ? ' is-activo' : '' ?>" href="<?= m_e($cuentaUrl) ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="8" r="3.6"/><path d="M5 20c1.2-3.6 3.8-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
        <span><?= m_e($cuentaTexto) ?></span>
      </a>
      <a class="tr-accion tr-accion--reserva<?= in_array($vista, ['carrito', 'checkout', 'pago'], true) ? ' is-activo' : '' ?>" href="<?= m_e(m_url('carrito')) ?>" aria-label="Tu reserva, <?= $unidades ?> <?= $unidades === 1 ? 'servicio o producto' : 'servicios o productos' ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="5" width="17" height="15" rx="3"/><path d="M3.5 10h17M8 3v4M16 3v4"/></svg>
        <span>Tu reserva</span>
        <?php if ($unidades > 0): ?><span class="tr-burbuja"><?= $unidades ?></span><?php endif; ?>
      </a>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="tr-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </nav>
  </div>
  <nav class="tr-cats" aria-label="Categorías">
    <div class="tr-ancho tr-cats__fila">
      <a href="<?= m_e($cfg['url_base']) ?>#menu"<?= $vista === 'inicio' && $catActual === '' && $qActual === '' ? ' class="is-activo"' : '' ?>>Todo</a>
      <?php foreach ($categorias as $c): ?>
      <?php if ($c['slug'] === 'convenios') { continue; } ?>
      <a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"<?= $catActual === $c['slug'] ? ' class="is-activo" aria-current="true"' : '' ?>><?= m_e($c['nombre']) ?></a>
      <?php endforeach; ?>
      <a class="tr-cats__convenio<?= $catActual === 'convenios' ? ' is-activo' : '' ?>" href="<?= m_e($convenioUrl) ?>">Convenios</a>
    </div>
  </nav>
</header>

<main id="contenido" class="t-principal tr-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta de convenio está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif (m_es_mayorista($usuario)): ?>
    <p class="tr-modo-convenio"><strong>Cuenta de convenio</strong> · <?= m_e($usuario['empresa']) ?> · ves precios de convenio y las jornadas del furgón</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="tr-pie">
  <div class="tr-ancho tr-pie__grid">
    <div>
      <p class="tr-logo__texto tr-pie__logo">Tina Rodante<small>peluquería canina a domicilio</small></p>
      <p><?= m_e($cfg['negocio']['eslogan']) ?>, con base en <?= m_e($cfg['negocio']['comuna']) ?>, Santiago. Baño, corte, deslanado, uñas y oídos en un furgón con tina y agua tibia, y la tienda de lo que usamos.</p>
      <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Tina Rodante.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= m_e(demo_tel()) ?>">Llamar</a> · <a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    </div>
    <div>
      <p class="tr-pie__titulo">La ruta del furgón</p>
      <dl class="tr-pie__ruta">
        <?php foreach ($cfg['ruta'] as $dia => $comunas): ?><div><dt><?= m_e(DEMO_DIAS_CORTOS[$dia]) ?></dt><dd><?= m_e(implode(', ', $comunas)) ?></dd></div><?php endforeach; ?>
      </dl>
    </div>
    <div>
      <p class="tr-pie__titulo">Traslado</p>
      <p>A <?= count($cfg['comunas']) ?> comunas desde <?= m_e(m_clp((int) min($cfg['comunas']))) ?>, gratis sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?>. Si traes a tu perro a la base, no pagas traslado.</p>
      <p class="tr-pie__titulo">Horario</p>
      <?php demo_horario_tabla($cfg['negocio']['horario'], 'demo-horario tr-pie__horario'); ?>
    </div>
  </div>
  <div class="tr-ancho"><?php demo_aviso_pie($cfg['rubro']); ?></div>
</footer>
<?php demo_scripts(); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
