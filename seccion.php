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

$migas = [['Proyectos', '/proyectos'], [$section['nombre'], $section['url']]];
$actual = $section;
$head = [
    'title' => $section['nombre'] . ': proyectos | ' . $site['profile']['name'],
    'description' => $section['resumen'] . ' Proyectos de Julio Acuña, desarrollador web en Santiago.',
    'og_image' => $section['portada'] !== '' ? $section['portada'] : 'assets/img/og/og-inicio.jpg',
    'canonical' => $section['url'],
    'jsonld' => [jsonld_breadcrumbs($migas)],
];
require __DIR__ . '/includes/proyectos-pagina.php';
