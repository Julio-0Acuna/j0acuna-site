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
$otras = array_values(array_filter(portfolio_sections($site), static fn (array $s): bool => $s['id'] !== $section['id']));
$migas = [['Proyectos', '/proyectos'], [$section['nombre'], $section['url']]];
$wa = site_wa($site, 'Hola Julio, vi tus proyectos de «' . $section['nombre'] . '» y quiero algo así.');

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
      'eyebrow' => count($projects) === 1 ? '1 proyecto' : count($projects) . ' proyectos',
      'titulo' => $section['nombre'],
      'bajada' => $section['resumen'],
      'migas' => $migas,
  ]); ?>

  <section class="pb-5" aria-label="Proyectos de <?= e($section['nombre']) ?>">
    <div class="container">
      <div class="tiles <?= count($projects) === 1 ? 'tiles-1' : 'tiles-3' ?>">
        <?php foreach ($projects as $project): ?>
          <?php render_partial('mosaico', [
              'url' => project_url($project),
              'imagen' => $project['image_url'],
              'titulo' => $project['title'],
              'texto' => $project['description'],
              'nivel' => 'h2',
              'clase' => count($projects) === 1 ? 'tile-wide' : '',
              'mas' => count($projects) === 1 ? 'Ver proyecto →' : '',
          ]); ?>
        <?php endforeach; ?>
      </div>

      <div class="other-sections">
        <p>Otras secciones</p>
        <div class="inline-links mt-0">
          <a class="more-link section-back" href="/proyectos"><span aria-hidden="true">←</span> Todas las secciones</a>
          <?php foreach ($otras as $o): ?>
            <a class="more-link" href="<?= e($o['url']) ?>"><?= e($o['nombre']) ?></a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <section class="contact-section section-alt">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Quieres algo así?</h2>
          <p>Cuéntame qué necesita tu negocio y te digo cómo lo haría.</p>
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
