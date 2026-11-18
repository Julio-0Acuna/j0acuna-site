<?php
require_once __DIR__ . '/includes/view.php';

// Proyectos: "Ejemplos por rubro" arriba y debajo el portafolio con la vitrina por secciones
// (plantilla compartida con seccion.php en includes/proyectos-pagina.php).
$site = portfolio_load_site();
$actual = null;
$head = [
    'title' => 'Portafolio de proyectos web | ' . $site['profile']['name'],
    'description' => 'Proyectos de Julio Acuña, desarrollador web en Santiago: sistemas para restaurantes, tiendas online, páginas web e inteligencia artificial.',
    'canonical' => '/proyectos',
    'jsonld' => [jsonld_breadcrumbs([['Proyectos', '/proyectos']])],
];
require __DIR__ . '/includes/proyectos-pagina.php';
