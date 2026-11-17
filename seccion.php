<?php
require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$id = is_string($_GET['id'] ?? null) ? $_GET['id'] : '';
$section = preg_match('/^[a-z0-9-]+$/', $id) ? portfolio_find_section($site, $id) : null;

if ($section === null) {
    http_response_code(404);
    render_partial('head', ['title' => 'Sección no encontrada | ' . $site['profile']['name'], 'description' => 'Esta sección no existe.', 'canonical' => '/proyectos']);
    render_partial('nav', ['site' => $site, 'actual' => 'proyectos']);
    render_partial('page-header', ['eyebrow' => 'Error 404', 'titulo' => 'Sección no encontrada', 'bajada' => 'Esta sección no existe o todavía no tiene proyectos.']);
    echo '<main class="section-band pt-0"><div class="container"><a class="minimal-btn minimal-btn-dark" href="/proyectos"><span>Ver todas las secciones</span></a></div></main>';
    render_partial('footer', ['site' => $site]);
    return;
}

$projects = portfolio_section_projects($site, $section['id']);
$migas = [['Proyectos', '/proyectos'], [$section['nombre'], $section['url']]];

render_partial('head', [
    'title' => $section['nombre'] . ': proyectos | ' . $site['profile']['name'],
    'description' => $section['resumen'] . ' Proyectos de Julio Acuña, desarrollador web en Santiago.',
    'og_image' => $section['portada'] !== '' ? $section['portada'] : 'assets/img/og/og-inicio.jpg',
    'canonical' => $section['url'],
    'jsonld' => [jsonld_breadcrumbs($migas)],
]);
render_partial('nav', ['site' => $site, 'actual' => 'proyectos']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Proyectos',
      'titulo' => $section['nombre'],
      'bajada' => $section['resumen'],
      'migas' => $migas,
  ]); ?>

  <section class="section-band pt-0">
    <div class="container">
      <div class="project-grid">
        <?php foreach ($projects as $project): ?>
          <?php render_partial('project-card', ['project' => $project, 'compacto' => true]); ?>
        <?php endforeach; ?>
      </div>
      <p class="mt-4"><a class="minimal-link section-back" href="/proyectos">← Todas las secciones</a></p>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $site['profile']['whatsapp_mensaje']]);
render_partial('footer', ['site' => $site]);
