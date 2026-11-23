<?php
/**
 * Layout de Farmacia Lucero: farmacia abierta las 24 horas con tienda online.
 * Franja de turno azul medianoche con la hora y el estado del despacho, cabecera blanca con buscador,
 * cuenta y carrito, y pie con el químico farmacéutico director técnico y el aviso de los medicamentos.
 * @var string $contenido @var string $titulo @var string $vista @var array $cfg @var ?array $usuario
 */
$cfg ??= m_config();
$usuario ??= m_usuario();
$vista ??= '';
$esDueno = m_es_dueno($usuario);
$esEmpresa = m_es_mayorista($usuario);
$unidades = array_sum(m_carrito_crudo());
$qActual = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
$cuentaUrl = m_url($usuario ? ($esDueno ? 'panel' : 'cuenta') : 'ingresar');
$cuentaTexto = $usuario ? ($esDueno ? 'Panel' : 'Mi cuenta') : 'Ingresar';
$ahora = new DateTimeImmutable('now', new DateTimeZone($cfg['zona_horaria']));
[$despDesde, $despHasta] = $cfg['entrega']['horas'];
$minuto = (int) $ahora->format('G') * 60 + (int) $ahora->format('i');
$hayDespacho = $minuto >= demo_minutos($despDesde) && $minuto < demo_minutos($despHasta);
$estrella = '<svg viewBox="0 0 32 32" width="22" height="22" aria-hidden="true" focusable="false"><path fill="currentColor" d="M16 2c.9 7.6 6.4 13.1 14 14-7.6.9-13.1 6.4-14 14-.9-7.6-6.4-13.1-14-14 7.6-.9 13.1-6.4 14-14z"/></svg>';

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para una farmacia 24 horas en ' . $cfg['negocio']['comuna'] . ', Santiago: productos de venta directa con buscador y códigos, retiro a cualquier hora o despacho hoy, cotización de recetas, convenios para empresas y panel del dueño.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600;700&family=Sora:wght@500;600;700;800&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#0d1838',
    'icono' => ['✦', '#0d1838', '#ffc93c'],
    'clase_body' => 't lu lu-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<div class="lu-turno">
  <p class="lu-ancho">
    <span class="lu-turno__vivo"><span class="lu-punto" aria-hidden="true"></span>Abierto ahora · son las <strong><?= m_e($ahora->format('H:i')) ?></strong></span>
    <span class="lu-turno__sep" aria-hidden="true"><?= $estrella ?></span>
    <span><?= $hayDespacho ? 'Despacho hoy hasta las ' . m_e($despHasta) : 'Despacho desde las ' . m_e($despDesde) . ' · retiro en el local a cualquier hora' ?></span>
    <span class="lu-turno__sep lu-turno__extra" aria-hidden="true"><?= $estrella ?></span>
    <span class="lu-turno__extra">Químico farmacéutico de turno las 24 horas</span>
  </p>
</div>
<header class="lu-cabecera">
  <div class="lu-ancho lu-cabecera__fila">
    <a class="lu-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <span class="lu-logo__marca"><?= $estrella ?></span>
      <span class="lu-logo__texto">Lucero<small>farmacia 24 horas</small></span>
    </a>
    <form class="lu-buscar" role="search" method="get" action="<?= m_e($cfg['url_base']) ?>#menu">
      <label class="demo-sr" for="lu-q">Buscar en la farmacia</label>
      <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      <input type="search" id="lu-q" name="q" value="<?= m_e($qActual) ?>" maxlength="40" placeholder="<?= m_e(m_texto('buscar_placeholder')) ?>">
      <button type="submit">Buscar</button>
    </form>
    <nav class="lu-acciones" aria-label="Tu cuenta y tu carrito">
      <a class="lu-accion<?= in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto'], true) ? ' is-activo' : '' ?>" href="<?= m_e($cuentaUrl) ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="3.6"/><path d="M5 20c1.2-3.6 3.8-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
        <span><?= m_e($cuentaTexto) ?></span>
      </a>
      <a class="lu-accion lu-accion--carrito<?= in_array($vista, ['carrito', 'checkout', 'pago'], true) ? ' is-activo' : '' ?>" href="<?= m_e(m_url('carrito')) ?>" aria-label="Carrito, <?= $unidades ?> <?= $unidades === 1 ? 'producto' : 'productos' ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"><path d="M5 8h14l-1.2 12H6.2z"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8"/></svg>
        <span>Carrito</span>
        <?php if ($unidades > 0): ?><span class="lu-burbuja"><?= $unidades ?></span><?php endif; ?>
      </a>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="lu-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main id="contenido" class="t-principal lu-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu convenio está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif ($esEmpresa): ?>
    <p class="lu-convenio-activo"><span class="lu-convenio-activo__sello">Convenio activo</span> <?= m_e($usuario['empresa']) ?> · ves precios convenio, botiquines completos e insumos por caja</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="lu-pie">
  <div class="lu-ancho">
    <p class="lu-pie__aviso"><?= $estrella ?><span><strong>Consulta a tu químico farmacéutico.</strong> Lee las instrucciones antes de usar un medicamento. Los medicamentos con receta no se venden por la tienda: se cotizan y se entregan con la receta a la vista.</span></p>
    <div class="lu-pie__grid">
      <div>
        <p class="lu-logo lu-logo--pie"><span class="lu-logo__marca"><?= $estrella ?></span><span class="lu-logo__texto">Lucero<small>farmacia 24 horas</small></span></p>
        <p><?= m_e($cfg['negocio']['eslogan']) ?>. <?= m_e($cfg['negocio']['comuna']) ?>, Santiago.</p>
        <p>Químico farmacéutico director técnico: Q.F. <?= m_e($cfg['negocio']['qf']) ?>.</p>
        <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Farmacia Lucero.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= m_e(demo_tel()) ?>">Llamar</a></p>
      </div>
      <div>
        <p class="lu-pie__titulo">Despacho hoy</p>
        <ul class="lu-pie__comunas">
          <?php foreach ($cfg['comunas'] as $comuna => $costo): ?><li><span><?= m_e($comuna) ?></span><span><?= m_e(m_clp((int) $costo)) ?></span></li><?php endforeach; ?>
        </ul>
        <p class="lu-pie__chico">De <?= m_e($despDesde) ?> a <?= m_e($despHasta) ?>. Gratis desde <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?>.</p>
      </div>
      <div>
        <p class="lu-pie__titulo">Horario</p>
        <?php demo_horario_tabla($cfg['negocio']['horario'], 'demo-horario lu-pie__horario'); ?>
        <p class="lu-pie__chico">Retiro en el local a cualquier hora. De noche, por la ventanilla de turno.</p>
      </div>
      <div>
        <p class="lu-pie__titulo">Empresas en convenio</p>
        <p>Colegios, jardines, oficinas y clubes: botiquines completos e insumos por caja con precio convenio.</p>
        <p><a href="<?= m_e(m_url('registro')) ?>"><?= m_e(m_texto('crear_cuenta_mayorista')) ?></a> · <a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
      </div>
    </div>
    <?php demo_aviso_pie($cfg['rubro']); ?>
  </div>
</footer>
<?php demo_scripts(); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
