<?php
/**
 * Layout de Nori Club: barra blanca con estado del local, carrito en píldora salmón
 * y, en el celular, un botón flotante "Ver carrito" cuando hay productos.
 * @var string $contenido @var string $titulo @var string $vista @var array $cfg @var ?array $usuario @var int $unidades_carrito
 */
$cfg ??= m_config();
$usuario ??= m_usuario();
$unidades = (int) ($unidades_carrito ?? array_sum(m_carrito_crudo()));
$vista ??= '';
$esDueno = m_es_dueno($usuario);
$estado = demo_horario_estado($cfg['negocio']['horario']);
$cuentaUrl = m_url($usuario ? ($esDueno ? 'panel' : 'cuenta') : 'ingresar');
$cuentaTexto = $usuario ? ($esDueno ? 'Panel' : 'Mi cuenta') : 'Ingresar';
$enCuenta = in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto', 'pedido'], true);
$enCarrito = in_array($vista, ['carrito', 'checkout', 'pago'], true);
$subtotal = $unidades > 0 && !$enCarrito ? m_carrito(m_db(), $usuario)['subtotal'] : 0;
$scripts = ['qr'];
if ($vista === 'inicio') {
    $scripts[] = '3d';
}

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para una cadena de sushi con delivery en ' . $cfg['negocio']['comuna'] . ', Santiago: combos, cuentas de clientes, empresas y eventos, pago de prueba y panel del dueño.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&family=Unbounded:wght@500;600;700&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#232874',
    'icono' => ['N', '#232874', '#ff8a6b'],
    'clase_body' => 't nc nc-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<header class="nc-barra">
  <div class="nc-barra__fila">
    <a class="nc-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <span class="nc-logo__icono" aria-hidden="true"><span></span><span></span><span></span></span>
      <span class="nc-logo__texto">nori<strong>club</strong></span>
    </a>
    <p class="nc-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= m_e($cfg['negocio']['comuna']) ?> · <?= m_e($estado['texto']) ?></p>
    <nav class="nc-nav" aria-label="Tienda">
      <a href="<?= m_e($cfg['url_base']) ?>#menu"<?= $vista === 'inicio' ? ' class="is-activo"' : '' ?>>Carta</a>
      <a href="<?= m_e(m_es_mayorista($usuario) ? m_url('inicio', ['cat' => 'empresas']) . '#menu' : m_url('registro')) ?>">Empresas</a>
      <a class="nc-nav__cuenta<?= $enCuenta ? ' is-activo' : '' ?>" href="<?= m_e($cuentaUrl) ?>"<?= $enCuenta ? ' aria-current="page"' : '' ?>>
        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="8" r="3.6"/><path d="M5 20c1.2-3.6 3.8-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
        <span><?= m_e($cuentaTexto) ?></span>
      </a>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="nc-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </nav>
    <a class="nc-carrito<?= $enCarrito ? ' is-activo' : '' ?>" href="<?= m_e(m_url('carrito')) ?>"<?= $enCarrito ? ' aria-current="page"' : '' ?> aria-label="Carrito, <?= $unidades ?> <?= $unidades === 1 ? 'producto' : 'productos' ?>">
      <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h2l1.6 10.2a1.5 1.5 0 0 0 1.5 1.3h8.3a1.5 1.5 0 0 0 1.5-1.2L20 8H7"/><circle cx="9.5" cy="20" r="1.2"/><circle cx="17" cy="20" r="1.2"/></svg>
      <span class="nc-carrito__texto">Carrito</span>
      <span class="nc-carrito__num"><?= $unidades ?></span>
    </a>
  </div>
</header>

<main id="contenido" class="t-principal nc-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta para empresas está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif (m_es_mayorista($usuario)): ?>
    <p class="nc-modo-empresa"><strong>Cuenta empresa</strong> · <?= m_e($usuario['empresa']) ?> · ves precios por volumen y bandejas para eventos</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="nc-pie">
  <div class="nc-pie__fila">
    <div>
      <p class="nc-logo__texto">nori<strong>club</strong></p>
      <p><?= m_e($cfg['negocio']['eslogan']) ?>. Despacho en <?= count($cfg['comunas']) ?> comunas y retiro en el local.</p>
    </div>
    <div>
      <p class="nc-pie__titulo">Horario</p>
      <?php demo_horario_tabla($cfg['negocio']['horario'], 'demo-horario nc-pie__horario'); ?>
    </div>
    <div>
      <p class="nc-pie__titulo">Contacto</p>
      <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Nori Club.')) ?>" target="_blank" rel="noopener">WhatsApp</a></p>
      <p><a href="<?= m_e(demo_tel()) ?>">Llamar</a></p>
      <p><a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    </div>
  </div>
  <div class="nc-pie__fila nc-pie__aviso"><?php demo_aviso_pie($cfg['rubro']); ?></div>
</footer>

<?php if ($unidades > 0 && !$enCarrito): ?>
<a class="nc-flotante" href="<?= m_e(m_url('carrito')) ?>">
  <span class="nc-flotante__num"><?= $unidades ?></span>
  <span>Ver carrito</span>
  <strong><?= m_e(m_clp((int) $subtotal)) ?></strong>
</a>
<?php endif; ?>
<?php demo_scripts($scripts); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
