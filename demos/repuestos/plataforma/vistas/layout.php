<?php
/**
 * Layout de Cardán Distribuidora: banda de rodado, cabecera negro neumático con el vehículo elegido,
 * cuenta y carro, y pie con los datos de la bodega.
 * @var string $contenido @var string $titulo @var string $vista @var array $cfg @var ?array $usuario @var int $unidades_carrito
 */
require_once __DIR__ . '/_vehiculo.php';
$cfg ??= m_config();
$usuario ??= m_usuario();
$vista ??= '';
$esDueno = m_es_dueno($usuario);
$unidades = (int) ($unidades_carrito ?? array_sum(m_carrito_crudo()));
$veh = cd_vehiculo($cfg);
$cuentaUrl = m_url($usuario ? ($esDueno ? 'panel' : 'cuenta') : 'ingresar');
$cuentaTexto = $usuario ? ($esDueno ? 'Panel' : 'Mi cuenta') : 'Ingresar';
$talleresUrl = m_es_mayorista($usuario) ? m_url('inicio', ['cat' => 'talleres']) . '#menu' : m_url('registro');

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para una distribuidora de repuestos de auto en ' . $cfg['negocio']['comuna'] . ', Santiago: búsqueda por patente o por vehículo, stock a la vista, precio taller, despacho con día y hora, pago de prueba y panel del dueño.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Atkinson+Hyperlegible:wght@400;700&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#0f1513',
    'icono' => ['C', '#0f1513', '#2fe0a2'],
    'clase_body' => 't cd cd-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<div class="cd-rodado" aria-hidden="true"></div>
<header class="cd-cabecera">
  <div class="cd-ancho cd-cabecera__fila">
    <a class="cd-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <svg class="cd-logo__icono" viewBox="0 0 40 40" width="38" height="38" aria-hidden="true" focusable="false">
        <rect x="2" y="2" width="36" height="36" rx="6" fill="#2fe0a2"/>
        <path d="M11 14v12M29 14v12M11 20h18" stroke="#0f1513" stroke-width="3.2" stroke-linecap="round"/>
        <circle cx="20" cy="20" r="4.2" fill="#0f1513"/><circle cx="20" cy="20" r="1.6" fill="#2fe0a2"/>
      </svg>
      <span class="cd-logo__texto">Cardán<small>Distribuidora · <?= m_e($cfg['negocio']['comuna']) ?></small></span>
    </a>
    <nav class="cd-nav" aria-label="Tienda">
      <a href="<?= m_e($cfg['url_base']) ?>#menu"<?= $vista === 'inicio' ? ' class="is-activo"' : '' ?>>Catálogo</a>
      <a href="<?= m_e($talleresUrl) ?>"><?= m_es_mayorista($usuario) ? 'Packs para talleres' : 'Talleres' ?></a>
      <a href="<?= m_e(m_wa('Hola, quiero cotizar repuestos en Cardán. Mi patente es: ')) ?>" target="_blank" rel="noopener">Cotizar por WhatsApp</a>
    </nav>
    <div class="cd-acciones">
      <?php if ($veh['activo']): ?>
      <a class="cd-chip-auto" href="<?= m_e($cfg['url_base']) ?>#menu" title="Repuestos para tu vehículo"><span aria-hidden="true">●</span><?= m_e($veh['texto']) ?></a>
      <?php endif; ?>
      <a class="cd-accion<?= in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto'], true) ? ' is-activo' : '' ?>" href="<?= m_e($cuentaUrl) ?>">
        <svg viewBox="0 0 24 24" width="21" height="21" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="4"/><path d="M4 21c1.3-4 4.3-6 8-6s6.7 2 8 6"/></svg>
        <span><?= m_e($cuentaTexto) ?></span>
      </a>
      <a class="cd-carro<?= in_array($vista, ['carrito', 'checkout', 'pago'], true) ? ' is-activo' : '' ?>" href="<?= m_e(m_url('carrito')) ?>" aria-label="Carro, <?= $unidades ?> <?= $unidades === 1 ? 'unidad' : 'unidades' ?>">
        <svg viewBox="0 0 24 24" width="21" height="21" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M2.5 4h3l2.3 10.5h10.4L20.5 7.5H7"/><circle cx="9.5" cy="19" r="1.6"/><circle cx="17" cy="19" r="1.6"/></svg>
        <span class="cd-carro__texto">Carro</span>
        <span class="cd-carro__num"><?= $unidades ?></span>
      </a>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="cd-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </div>
  </div>
</header>

<main id="contenido" class="t-principal cd-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta de taller está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif (m_es_mayorista($usuario)): ?>
    <p class="cd-modo-taller"><strong>Precio taller activo</strong> · <?= m_e($usuario['empresa']) ?> · ves precio taller y packs por caja</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="cd-pie">
  <div class="cd-ancho cd-pie__grid">
    <div>
      <p class="cd-logo__texto cd-logo__texto--pie">Cardán<small>Distribuidora de repuestos</small></p>
      <p><?= m_e($cfg['negocio']['eslogan']) ?>. Bodega y retiro en <?= m_e($cfg['negocio']['comuna']) ?>, Santiago.</p>
    </div>
    <div>
      <p class="cd-pie__titulo">Horario de bodega</p>
      <?php demo_horario_tabla($cfg['negocio']['horario'], 'demo-horario cd-pie__horario'); ?>
    </div>
    <div>
      <p class="cd-pie__titulo">Atención</p>
      <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Cardán Distribuidora.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= m_e(demo_tel()) ?>">Llamar</a></p>
      <p>Despacho en <?= count($cfg['comunas']) ?> comunas · <a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    </div>
  </div>
  <div class="cd-ancho"><?php demo_aviso_pie($cfg['rubro']); ?></div>
</footer>
<?php demo_scripts(); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
