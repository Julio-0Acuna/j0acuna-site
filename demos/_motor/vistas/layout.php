<?php
/**
 * Layout por defecto (neutro). Cada rubro debería traer el suyo en vistas/layout.php.
 * @var string $contenido @var string $titulo @var array $cfg @var ?array $usuario @var int $unidades_carrito
 */
$cfg ??= m_config();
$usuario ??= m_usuario();
?>
<!doctype html>
<html lang="es-CL">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= m_e($titulo ?? $cfg['negocio']['nombre']) ?></title>
  <?php if (!empty($cfg['demo'])): ?><meta name="robots" content="noindex, follow"><?php endif; ?>
  <link rel="stylesheet" href="<?= m_e(demo_asset(DEMO_URL_COMUN . 'demo.css')) ?>">
  <link rel="stylesheet" href="<?= m_e(demo_asset(DEMO_URL_COMUN . 'tienda.css')) ?>">
</head>
<body class="t">
<?php if (!empty($cfg['demo'])) { demo_aviso($cfg['rubro']); } ?>
<header class="t-cabecera">
  <a href="<?= m_e($cfg['url_base']) ?>"><?= m_e($cfg['negocio']['nombre']) ?></a>
  <nav>
    <a href="<?= m_e(m_url('carrito')) ?>"><?= m_e(m_texto('layout_carrito', null, ['n' => (int) ($unidades_carrito ?? 0)])) ?></a>
    <?php if ($usuario): ?><a href="<?= m_e(m_url(m_es_dueno($usuario) ? 'panel' : 'cuenta')) ?>"><?= m_e(m_texto(m_es_dueno($usuario) ? 'layout_panel' : 'layout_cuenta')) ?></a>
    <?php else: ?><a href="<?= m_e(m_url('ingresar')) ?>"><?= m_e(m_texto('layout_ingresar')) ?></a><?php endif; ?>
  </nav>
</header>
<main id="contenido" class="t-principal">
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>
<?php demo_scripts(['qr', '3d']); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
