<?php
/** @var array $site @var string $actual (inicio|servicios|proyectos|restaurantes|blog|contacto) */
$actual = $actual ?? '';
$items = [
    'inicio' => ['/', 'Inicio'],
    'servicios' => ['/servicios', 'Servicios'],
    'proyectos' => ['/proyectos', 'Proyectos'],
    'restaurantes' => ['/restaurantes', 'Restaurantes'],
    'blog' => ['/blog', 'Blog'],
    'contacto' => ['/contacto', 'Contacto'],
];
$navWa = site_wa($site, $site['profile']['whatsapp_mensaje']);
?>
<div class="sky" aria-hidden="true"><div class="sky__grid"></div><div class="sky__a"></div><div class="sky__b"></div></div>
<header class="top" id="top">
  <div class="wrap">
    <div class="top__bar">
      <a class="brand" href="/"><span class="brand__dot" aria-hidden="true"></span><?= e($site['profile']['name']) ?></a>
      <nav class="nav" id="menu" aria-label="Principal">
        <ul>
          <?php foreach ($items as $clave => [$ruta, $nombre]): ?>
            <li><a href="<?= e($ruta) ?>"<?= $clave === $actual ? ' aria-current="page"' : '' ?>><?= e($nombre) ?></a></li>
          <?php endforeach; ?>
        </ul>
        <div class="nav__foot">
          <?php if ($navWa !== ''): ?>
            <a class="btn btn--light" href="<?= e($navWa) ?>" target="_blank" rel="noopener"><svg aria-hidden="true"><use href="#i-wa"/></svg>Escríbeme por WhatsApp</a>
          <?php endif; ?>
          <span><?= e($site['profile']['ciudad']) ?> · negocios de <?= e($site['profile']['area']) ?></span>
        </div>
      </nav>
      <?php if ($navWa !== ''): ?>
        <a class="btn btn--light btn--sm top__wa" href="<?= e($navWa) ?>" target="_blank" rel="noopener"><svg aria-hidden="true"><use href="#i-wa"/></svg>WhatsApp</a>
      <?php endif; ?>
      <button class="burger" type="button" aria-expanded="false" aria-controls="menu"><span class="sr">Abrir menú</span><i aria-hidden="true"></i></button>
    </div>
  </div>
</header>
<div id="contenido" tabindex="-1"></div>
