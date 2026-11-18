<?php
/**
 * Layout de Acopio Norte: distribuidora de materiales de construcción con tienda online.
 * Barra de servicio oscura, cabecera hormigón con buscador por código, categorías con subrayado naranja.
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
$maestrosUrl = m_es_mayorista($usuario) ? m_url('inicio', ['cat' => 'maestros']) . '#menu' : m_url('registro');

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para una distribuidora de materiales de construcción en ' . $cfg['negocio']['comuna'] . ', Santiago: buscador por código, precios para maestros y constructoras, arriendo de herramientas, despacho a obra con día y hora, pago de prueba y panel del dueño.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Sofia+Sans+Extra+Condensed:ital,wght@0,700;0,800;0,900;1,800&family=Sofia+Sans:wght@400;500;600;700&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#1b1d1f',
    'icono' => ['A', '#ff5a1f', '#1b1d1f'],
    'clase_body' => 't an an-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<div class="an-servicio">
  <div class="an-ancho an-servicio__fila">
    <p><span class="an-servicio__punto" aria-hidden="true"></span>Despacho a obra en <?= count($cfg['comunas']) ?> comunas, de lunes a sábado</p>
    <p class="an-servicio__der"><a href="<?= m_e($maestrosUrl) ?>"><?= m_es_mayorista($usuario) ? 'Ver packs para maestros' : 'Precios para maestros y constructoras' ?></a><span aria-hidden="true">·</span><a href="<?= m_e(demo_tel()) ?>">Llamar</a></p>
  </div>
</div>
<header class="an-cabecera">
  <div class="an-ancho an-cabecera__fila">
    <a class="an-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <svg class="an-logo__icono" viewBox="0 0 44 44" width="42" height="42" aria-hidden="true" focusable="false">
        <rect x="3" y="27" width="38" height="5" fill="#1b1d1f"/><rect x="5" y="32" width="5" height="6" fill="#1b1d1f"/><rect x="19.5" y="32" width="5" height="6" fill="#1b1d1f"/><rect x="34" y="32" width="5" height="6" fill="#1b1d1f"/>
        <rect x="5" y="17" width="16" height="9" fill="#ff5a1f"/><rect x="23" y="17" width="16" height="9" fill="#8a8c86"/><rect x="13" y="7" width="18" height="9" fill="#1b1d1f"/>
      </svg>
      <span class="an-logo__texto">Acopio Norte<small>Materiales · <?= m_e($cfg['negocio']['comuna']) ?></small></span>
    </a>
    <form class="an-buscar" role="search" method="get" action="<?= m_e($cfg['url_base']) ?>#menu">
      <label class="demo-sr" for="an-q">Buscar materiales por nombre o código</label>
      <span class="an-buscar__cod" aria-hidden="true">COD/</span>
      <input type="search" id="an-q" name="q" value="<?= m_e($qActual) ?>" maxlength="40" placeholder="<?= m_e(m_texto('buscar_placeholder', 'Buscar…')) ?>">
      <button type="submit" aria-label="Buscar"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2.4"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2.4" stroke-linecap="square"/></svg></button>
    </form>
    <nav class="an-acciones" aria-label="Tu cuenta y tu pedido">
      <a class="an-accion<?= in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto'], true) ? ' is-activo' : '' ?>" href="<?= m_e($cuentaUrl) ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21v-1.5A5.5 5.5 0 0 1 9.5 14h5a5.5 5.5 0 0 1 5.5 5.5V21"/><path d="M7 7.5a5 5 0 0 1 10 0V9H7z"/><path d="M5.5 9h13"/></svg>
        <span><?= m_e($cuentaTexto) ?></span>
      </a>
      <a class="an-accion an-accion--pedido<?= in_array($vista, ['carrito', 'checkout', 'pago'], true) ? ' is-activo' : '' ?>" href="<?= m_e(m_url('carrito')) ?>" aria-label="Mi pedido, <?= $unidades ?> <?= $unidades === 1 ? 'unidad' : 'unidades' ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 6h3l2.2 9.5h11L20.5 9H7"/><circle cx="9" cy="19.5" r="1.5"/><circle cx="17" cy="19.5" r="1.5"/></svg>
        <span>Pedido</span>
        <?php if ($unidades > 0): ?><span class="an-burbuja"><?= $unidades ?></span><?php endif; ?>
      </a>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="an-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </nav>
  </div>
  <nav class="an-cats" aria-label="Categorías">
    <div class="an-ancho an-cats__fila">
      <a href="<?= m_e($cfg['url_base']) ?>#menu"<?= $vista === 'inicio' && $catActual === '' && $qActual === '' ? ' class="is-activo"' : '' ?>>Todo</a>
      <?php foreach ($categorias as $c): ?>
      <?php if ($c['slug'] === 'maestros') { continue; } ?>
      <a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"<?= $catActual === $c['slug'] ? ' class="is-activo" aria-current="true"' : '' ?>><?= m_e($c['nombre']) ?></a>
      <?php endforeach; ?>
      <a class="an-cats__maestros<?= $catActual === 'maestros' ? ' is-activo' : '' ?>" href="<?= m_e($maestrosUrl) ?>">Maestros y empresas</a>
    </div>
  </nav>
</header>

<main id="contenido" class="t-principal an-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta de maestro o empresa está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif (m_es_mayorista($usuario)): ?>
    <p class="an-modo-maestro"><strong>Precio maestro activo</strong> · <?= m_e($usuario['empresa']) ?> · ves precios por volumen y los packs por pallet</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="an-pie">
  <div class="an-ancho an-pie__grid">
    <div>
      <p class="an-logo__texto an-pie__logo">Acopio Norte<small>Materiales · <?= m_e($cfg['negocio']['comuna']) ?></small></p>
      <p><?= m_e($cfg['negocio']['eslogan']) ?>. <?= m_e($cfg['negocio']['comuna']) ?>, Santiago.</p>
      <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Acopio Norte.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= m_e(demo_tel()) ?>">Llamar</a></p>
    </div>
    <div>
      <p class="an-pie__titulo">Catálogo</p>
      <ul>
        <?php foreach ($categorias as $c): ?><?php if ($c['slug'] === 'maestros') { continue; } ?><li><a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"><?= m_e($c['nombre']) ?></a></li><?php endforeach; ?>
      </ul>
    </div>
    <div>
      <p class="an-pie__titulo">Despacho a obra</p>
      <p>Camión con pluma a <?= count($cfg['comunas']) ?> comunas desde <?= m_e(m_clp((int) min($cfg['comunas']))) ?>, gratis en pedidos sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?>.</p>
      <p><a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    </div>
    <div>
      <p class="an-pie__titulo">Horario del patio</p>
      <?php demo_horario_tabla($cfg['negocio']['horario'], 'demo-horario an-pie__horario'); ?>
    </div>
  </div>
  <div class="an-ancho"><?php demo_aviso_pie($cfg['rubro']); ?></div>
</footer>
<?php demo_scripts(); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
