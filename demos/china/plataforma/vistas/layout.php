<?php
/**
 * Layout de Wokazo: delivery de wok hasta tarde. Barra amarilla arriba, tarjetas con borde grueso
 * y, en el celular, una "comanda" fija abajo con el carrito.
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
$scripts = ['qr'];
if ($vista === 'inicio') {
    $scripts[] = '3d';
}

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para un delivery de comida china en ' . $cfg['negocio']['comuna'] . ', Santiago: menús para 2, 4 y 6, cuentas de clientes, empresas y eventos, pago de prueba y panel del dueño.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,400..900&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#ffc83d',
    'icono' => ['W', '#161412', '#ffc83d'],
    'clase_body' => 't wk wk-vista-' . $vista . ($unidades > 0 ? ' wk-con-carrito' : ''),
]);
demo_aviso($cfg['rubro']);
?>
<header class="wk-barra">
  <div class="wk-barra__fila">
    <a class="wk-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <span class="wk-logo__icono" aria-hidden="true">
        <svg viewBox="0 0 40 40" width="40" height="40" focusable="false"><circle cx="20" cy="20" r="20" fill="#161412"/><path d="M8 19h24c0 6.6-5.4 11-12 11S8 25.6 8 19z" fill="#ffc83d"/><path d="M31.5 19 37 14" stroke="#ffc83d" stroke-width="2.6" stroke-linecap="round"/><path d="M15 15c0-2 2-2 2-4M20 15c0-2 2-2 2-4M25 15c0-2 2-2 2-4" stroke="#e8432e" stroke-width="2" stroke-linecap="round" fill="none"/></svg>
      </span>
      <span class="wk-logo__texto">Wokazo</span>
    </a>
    <p class="wk-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span><?= m_e($estado['texto']) ?></p>
    <nav class="wk-nav" aria-label="Tienda">
      <a class="wk-nav__carta<?= !$enCuenta && !$enCarrito ? ' is-activo' : '' ?>" href="<?= m_e($cfg['url_base']) ?>#menu"<?= !$enCuenta && !$enCarrito ? ' aria-current="page"' : '' ?>>Carta</a>
      <a href="<?= m_e($cuentaUrl) ?>"<?= $enCuenta ? ' class="is-activo" aria-current="page"' : '' ?>><?= m_e($cuentaTexto) ?></a>
      <?php if ($usuario): ?>
      <?php // En el celular el cliente sale desde "Mi cuenta"; el dueño no tiene ese botón en su panel. ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="wk-salir<?= $esDueno ? '' : ' wk-salir--escritorio' ?>"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
      <a class="wk-nav__carrito<?= $enCarrito ? ' is-activo' : '' ?>" href="<?= m_e(m_url('carrito')) ?>"<?= $enCarrito ? ' aria-current="page"' : '' ?> aria-label="Carrito<?= $unidades > 0 ? ', ' . $unidades . ' productos' : '' ?>">
        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M5 9h14l-1.5 11h-11z"/><path d="M8 9l2-5M16 9l-2-5"/></svg>
        <span class="wk-nav__carrito-texto">Carrito</span>
        <?php if ($unidades > 0): ?><span class="wk-burbuja" aria-hidden="true"><?= $unidades ?></span><?php endif; ?>
      </a>
    </nav>
  </div>
</header>

<main id="contenido" class="t-principal wk-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta de empresa está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif (m_es_mayorista($usuario)): ?>
    <p class="wk-modo-empresa"><strong>Cuenta empresa</strong> · <?= m_e($usuario['empresa']) ?> · ves bandejas para eventos y precio por volumen</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="wk-pie">
  <div class="wk-pie__fila">
    <p class="wk-pie__marca">Wokazo</p>
    <p><?= m_e($cfg['negocio']['eslogan']) ?>.</p>
    <p><?= m_e($cfg['negocio']['comuna']) ?>, Santiago · Delivery en <?= count($cfg['comunas']) ?> comunas · Retiro en la cocina</p>
    <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Wokazo.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= m_e(demo_tel()) ?>">Llamar</a> · <a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    <?php demo_aviso_pie($cfg['rubro']); ?>
  </div>
</footer>

<?php if ($unidades > 0 && !$enCarrito): ?>
<a class="wk-comanda" href="<?= m_e(m_url('carrito')) ?>">
  <span class="wk-comanda__texto"><strong>Tu comanda</strong> <?= $unidades ?> <?= $unidades === 1 ? 'producto' : 'productos' ?></span>
  <span class="wk-comanda__ir">Ver carrito</span>
</a>
<?php endif; ?>
<?php demo_scripts($scripts); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
