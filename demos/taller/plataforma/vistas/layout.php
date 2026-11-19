<?php
/**
 * Layout de Vía Rápida: centro de servicio automotriz con reserva de hora y tienda.
 * Barra amarilla de señalética, cabecera negra con buscador y accesos a la cuenta y la reserva.
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
$flotaUrl = m_es_mayorista($usuario) ? m_url('inicio', ['cat' => 'flotas']) . '#menu' : m_url('registro');

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para un centro de servicio automotriz en ' . $cfg['negocio']['comuna'] . ', Santiago: reserva de hora por día y hora, servicios y accesorios con carrito, cuentas para flotas de empresas, pago de prueba y panel del dueño.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Saira+Condensed:ital,wght@0,600;0,700;0,800;1,800&family=Saira:wght@400;500;600;700&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#111111',
    'icono' => ['V', '#ffd400', '#111111'],
    'clase_body' => 't vr vr-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<p class="vr-senal">
  <span class="vr-senal__flecha" aria-hidden="true"></span>
  <span>Reserva tu hora en línea · <strong>sin filas</strong></span>
  <span class="vr-senal__sep" aria-hidden="true">/</span>
  <a href="<?= m_e($flotaUrl) ?>">Precios para flotas de empresas</a>
</p>
<header class="vr-cabecera">
  <div class="vr-ancho vr-cabecera__fila">
    <a class="vr-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <span class="vr-logo__rayas" aria-hidden="true"><i></i><i></i><i></i></span>
      <span class="vr-logo__texto">Vía Rápida<small>Servicio automotriz</small></span>
    </a>
    <form class="vr-buscar" role="search" method="get" action="<?= m_e($cfg['url_base']) ?>#menu">
      <label class="demo-sr" for="vr-q">Buscar servicios y accesorios</label>
      <input type="search" id="vr-q" name="q" value="<?= m_e($qActual) ?>" maxlength="40" placeholder="<?= m_e(m_texto('buscar_placeholder', 'Buscar…')) ?>">
      <button type="submit" aria-label="Buscar"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2.2"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg></button>
    </form>
    <nav class="vr-acciones" aria-label="Tu cuenta y tu reserva">
      <a class="vr-accion<?= in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto'], true) ? ' is-activo' : '' ?>" href="<?= m_e($cuentaUrl) ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="3.6"/><path d="M5 20c1.2-3.6 3.8-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
        <span><?= m_e($cuentaTexto) ?></span>
      </a>
      <a class="vr-accion vr-accion--reserva<?= in_array($vista, ['carrito', 'checkout', 'pago'], true) ? ' is-activo' : '' ?>" href="<?= m_e(m_url('carrito')) ?>" aria-label="Mi reserva, <?= $unidades ?> <?= $unidades === 1 ? 'ítem' : 'ítems' ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2"><rect x="3.5" y="5" width="17" height="15" rx="1.5"/><path d="M3.5 10h17M8 3v4M16 3v4"/></svg>
        <span>Mi reserva</span>
        <?php if ($unidades > 0): ?><span class="vr-burbuja"><?= $unidades ?></span><?php endif; ?>
      </a>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="vr-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </nav>
  </div>
  <nav class="vr-cats" aria-label="Categorías">
    <div class="vr-ancho vr-cats__fila">
      <a href="<?= m_e($cfg['url_base']) ?>#menu"<?= $vista === 'inicio' && $catActual === '' && $qActual === '' ? ' class="is-activo"' : '' ?>>Todo</a>
      <?php foreach ($categorias as $c): ?>
      <?php if ($c['slug'] === 'flotas') { continue; } ?>
      <a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"<?= $catActual === $c['slug'] ? ' class="is-activo" aria-current="true"' : '' ?>><?= m_e($c['nombre']) ?></a>
      <?php endforeach; ?>
      <a class="vr-cats__flota<?= $catActual === 'flotas' ? ' is-activo' : '' ?>" href="<?= m_e($flotaUrl) ?>">Flotas</a>
    </div>
  </nav>
</header>

<main id="contenido" class="t-principal vr-principal" data-3d-objeto="producto">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta de flota está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif (m_es_mayorista($usuario)): ?>
    <p class="vr-modo-flota"><strong>Cuenta de flota</strong> · <?= m_e($usuario['empresa']) ?> · ves precios de flota y los planes para furgones</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="vr-pie">
  <div class="vr-ancho vr-pie__grid">
    <div>
      <p class="vr-logo__texto vr-pie__logo">Vía Rápida<small>Servicio automotriz</small></p>
      <p><?= m_e($cfg['negocio']['eslogan']) ?>. <?= m_e($cfg['negocio']['comuna']) ?>, Santiago.</p>
      <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Vía Rápida.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= m_e(demo_tel()) ?>">Llamar</a></p>
    </div>
    <div>
      <p class="vr-pie__titulo">Servicios</p>
      <ul>
        <?php foreach ($categorias as $c): ?><?php if ($c['slug'] === 'flotas') { continue; } ?><li><a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"><?= m_e($c['nombre']) ?></a></li><?php endforeach; ?>
      </ul>
    </div>
    <div>
      <p class="vr-pie__titulo">Retiro a domicilio</p>
      <p>Retiramos y devolvemos tu auto en <?= count($cfg['comunas']) ?> comunas desde <?= m_e(m_clp((int) min($cfg['comunas']))) ?>, gratis en reservas sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?>.</p>
      <p><a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    </div>
    <div>
      <p class="vr-pie__titulo">Horario</p>
      <?php demo_horario_tabla($cfg['negocio']['horario'], 'demo-horario vr-pie__horario'); ?>
    </div>
  </div>
  <div class="vr-ancho"><?php demo_aviso_pie($cfg['rubro']); ?></div>
</footer>
<?php demo_scripts($vista === 'inicio' ? ['3d'] : []); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
