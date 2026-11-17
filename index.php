<?php
require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$featured = portfolio_featured_projects($site, 3);
$wa = site_wa($site, $site['profile']['whatsapp_mensaje']);

$caminos = [];
foreach ([
    ['restaurantes', 'Restaurantes y comercios', 'Página web, menú QR, pedidos por WhatsApp y apps de delivery.', '/restaurantes'],
    ['gestion', 'Sistemas para empresas', 'Tiendas online y sistemas que ordenan ventas, inventario y equipos.', '/proyectos/gestion'],
    ['ia', 'Inteligencia artificial', 'Asistentes, voz y documentos que responden por ti.', '/proyectos/ia'],
] as [$sec, $titulo, $texto, $ruta]) {
    $s = portfolio_find_section($site, $sec);
    if ($s !== null) {
        $caminos[] = ['titulo' => $titulo, 'texto' => $texto, 'ruta' => $ruta, 'imagen' => $s['portada']];
    }
}

render_partial('head', [
    'title' => $site['profile']['name'] . ' | Desarrollador web en ' . $site['profile']['ciudad'] . ', Chile',
    'description' => $site['profile']['tagline'],
    'og_image' => 'assets/img/og/og-inicio.jpg',
    'canonical' => '/',
    'jsonld' => [jsonld_business($site)],
]);
render_partial('nav', ['site' => $site, 'actual' => 'inicio']);
?>
<main>
  <section id="inicio" class="hero-section">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-10">
          <p class="eyebrow mb-3">Desarrollador web en <?= e($site['profile']['ciudad']) ?> · negocios de <?= e($site['profile']['area']) ?></p>
          <h1><?= e($site['profile']['headline']) ?></h1>
          <p class="lead mt-4"><?= e($site['profile']['bio']) ?></p>
          <div class="hero-actions mt-4">
            <?php if ($wa !== ''): ?>
              <a class="minimal-btn minimal-btn-light" href="<?= e($wa) ?>" target="_blank" rel="noopener"><span>Escríbeme por WhatsApp</span></a>
            <?php endif; ?>
            <a class="minimal-btn minimal-btn-ghost" href="/proyectos"><span>Ver proyectos</span></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php if ($caminos): ?>
    <section id="caminos" class="section-band">
      <div class="container">
        <div class="section-heading">
          <p class="eyebrow">¿Qué necesitas?</p>
          <h2>Elige tu camino</h2>
        </div>
        <div class="path-grid">
          <?php foreach ($caminos as $c): ?>
            <a class="path-card" href="<?= e($c['ruta']) ?>">
              <?php if ($c['imagen'] !== ''): ?>
                <img src="<?= e(asset($c['imagen'])) ?>" alt="" loading="lazy" width="1200" height="750">
              <?php endif; ?>
              <span class="path-body">
                <strong><?= e($c['titulo']) ?></strong>
                <span><?= e($c['texto']) ?></span>
                <span class="path-more">Ver más →</span>
              </span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php if ($featured): ?>
    <section id="destacados" class="section-band section-alt">
      <div class="container">
        <div class="section-heading">
          <p class="eyebrow">Destacados</p>
          <h2>Algunos proyectos</h2>
        </div>
        <div class="project-grid">
          <?php foreach ($featured as $project): ?>
            <?php render_partial('project-card', ['project' => $project, 'compacto' => true]); ?>
          <?php endforeach; ?>
        </div>
        <p class="mt-4"><a class="minimal-btn minimal-btn-outline" href="/proyectos"><span>Ver todos los proyectos</span></a></p>
      </div>
    </section>
  <?php endif; ?>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos?</h2>
          <p><?= e($site['profile']['contact_text']) ?></p>
        </div>
        <div class="contact-actions">
          <?php if ($wa !== ''): ?>
            <a class="minimal-btn minimal-btn-dark" href="<?= e($wa) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/servicios"><span>Ver servicios</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $site['profile']['whatsapp_mensaje']]);
render_partial('footer', ['site' => $site]);
