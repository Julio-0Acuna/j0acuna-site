<?php
require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$sections = portfolio_sections($site);

render_partial('head', [
    'title' => 'Portafolio de proyectos web | ' . $site['profile']['name'],
    'description' => 'Proyectos de Julio Acuña, desarrollador web en Santiago: sistemas para restaurantes, tiendas online, páginas web e inteligencia artificial.',
    'canonical' => '/proyectos',
    'jsonld' => [jsonld_breadcrumbs([['Proyectos', '/proyectos']])],
]);
render_partial('nav', ['site' => $site, 'actual' => 'proyectos']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Portafolio',
      'titulo' => 'Proyectos por sección',
      'bajada' => 'Elige una sección para ver sus proyectos.',
      'migas' => [['Proyectos', '/proyectos']],
  ]); ?>

  <section class="section-band pt-0">
    <div class="container">
      <div class="path-grid">
        <?php foreach ($sections as $s): ?>
          <a class="path-card" href="<?= e($s['url']) ?>">
            <?php if ($s['portada'] !== ''): ?>
              <img src="<?= e(asset($s['portada'])) ?>" alt="" loading="lazy" width="1200" height="750">
            <?php endif; ?>
            <span class="path-body">
              <strong><?= e($s['nombre']) ?></strong>
              <span><?= e($s['resumen']) ?></span>
              <span class="path-more"><?= (int) $s['cantidad'] ?> proyecto<?= $s['cantidad'] === 1 ? '' : 's' ?> →</span>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $site['profile']['whatsapp_mensaje']]);
render_partial('footer', ['site' => $site]);
