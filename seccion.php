<?php
require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$id = is_string($_GET['id'] ?? null) ? $_GET['id'] : '';
$section = preg_match('/^[a-z0-9-]+$/', $id) ? portfolio_find_section($site, $id) : null;

if ($section === null) {
    http_response_code(404);
    render_partial('head', ['title' => 'Sección no encontrada | ' . $site['profile']['name'], 'description' => 'Esta sección no existe.', 'canonical' => '/proyectos']);
    render_partial('nav', ['site' => $site, 'actual' => 'proyectos']);
    render_partial('error-404', [
        'site' => $site,
        'titulo' => 'Sección no encontrada',
        'texto' => 'Esta sección no existe o todavía no tiene proyectos.',
        'boton' => ['Ver todas las secciones', '/proyectos'],
    ]);
    render_partial('footer', ['site' => $site]);
    return;
}

$projects = portfolio_section_projects($site, $section['id']);
$otras = array_values(array_filter(portfolio_sections($site), static fn (array $s): bool => $s['id'] !== $section['id']));
$migas = [['Proyectos', '/proyectos'], [$section['nombre'], $section['url']]];
$uno = count($projects) === 1;

render_partial('head', [
    'title' => $section['nombre'] . ': proyectos | ' . $site['profile']['name'],
    'description' => $section['resumen'] . ' Proyectos de Julio Acuña, desarrollador web en Santiago.',
    'og_image' => $section['portada'] !== '' ? $section['portada'] : 'assets/img/og/og-inicio.jpg',
    'canonical' => $section['url'],
    'jsonld' => [jsonld_breadcrumbs($migas)],
]);
render_partial('nav', ['site' => $site, 'actual' => 'proyectos']);
?>
<main id="main">
  <?php render_partial('page-header', [
      'eyebrow' => $uno ? '1 proyecto' : count($projects) . ' proyectos',
      'titulo' => $section['nombre'],
      'bajada' => $section['resumen'],
      'migas' => $migas,
      'grad' => min(2, max(1, count(preg_split('/\s+/u', $section['nombre'])) - 1)),
  ]); ?>

  <section class="sec pt-0" aria-label="Proyectos de <?= e($section['nombre']) ?>">
    <div class="wrap">
      <div class="tiles <?= $uno ? 'tiles-1' : 'tiles-3' ?>">
        <?php foreach ($projects as $project): ?>
          <div class="rv">
            <?php render_partial('mosaico', [
                'url' => project_url($project),
                'imagen' => $project['image_url'],
                'titulo' => $project['title'],
                'etiqueta' => implode(' · ', array_filter([$project['pais'], (string) $project['anio']])),
                'texto' => $project['description'],
                'nivel' => 'h2',
                'clase' => $uno ? 'tile-wide' : '',
                'mas' => 'Ver proyecto',
            ]); ?>
          </div>
        <?php endforeach; ?>
      </div>

      <nav class="other" aria-label="Otras secciones">
        <p class="kicker">Otras secciones</p>
        <ul class="chips">
          <li><a class="chip" href="/proyectos"><svg aria-hidden="true" width="16" height="16"><use href="#i-back"/></svg>&nbsp;Todas las secciones</a></li>
          <?php foreach ($otras as $o): ?>
            <li><a class="chip" href="<?= e($o['url']) ?>"><?= e($o['nombre']) ?><small><?= (int) $o['cantidad'] ?></small></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </div>
  </section>

  <?php render_partial('cta', [
      'site' => $site,
      'titulo' => '¿Quieres algo así?',
      'texto' => 'Cuéntame qué necesita tu negocio y te digo cómo lo haría.',
      'mensaje' => 'Hola Julio, vi tus proyectos de «' . $section['nombre'] . '» y quiero algo así.',
      'secundario' => ['Ver servicios', '/servicios'],
  ]); ?>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $site['profile']['whatsapp_mensaje']]);
render_partial('footer', ['site' => $site]);
