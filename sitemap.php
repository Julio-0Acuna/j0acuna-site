<?php
// Uso (CLI): tools/php.sh sitemap.php  → escribe sitemap.xml y llms.txt desde data/site.json.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/includes/view.php';
require_once __DIR__ . '/includes/blog.php';

$site = portfolio_load_site();
$posts = blog_posts();

// --- sitemap.xml
$urls = ['/', '/servicios', '/proyectos', '/restaurantes', '/contacto', '/blog'];
foreach ($posts as $p) {
    $urls[] = blog_url($p);
}

// Ejemplos por rubro: /demos/ y la página de cada rubro (las versiones son noindex).
$rubros = [];
foreach (glob(__DIR__ . '/demos/*/_rubro.php') ?: [] as $archivo) {
    $rubros[] = basename(dirname($archivo));
}
sort($rubros);
if ($rubros) {
    $urls[] = '/demos/';
    foreach ($rubros as $r) {
        $urls[] = '/demos/' . $r . '/';
    }
}
foreach (portfolio_sections($site) as $s) {
    $urls[] = $s['url'];
}
foreach (portfolio_visible_projects($site) as $p) {
    $urls[] = project_url($p);
}

$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    $xml .= '  <url><loc>' . htmlspecialchars(absolute_url($u), ENT_XML1) . '</loc></url>' . "\n";
}
$xml .= '</urlset>' . "\n";
file_put_contents(__DIR__ . '/sitemap.xml', $xml);

// --- llms.txt (solo datos publicados en el sitio)
$pr = $site['profile'];
$l = "# {$pr['name']}\n\n";
$l .= "> Desarrollador web en {$pr['ciudad']}, Chile. Hace páginas web, menú digital con QR, pedidos por WhatsApp,\n";
$l .= "> tiendas online y sistemas a medida para restaurantes, comercios y empresas de {$pr['area']}.\n\n";
$l .= "## Qué ofrece\n";
foreach (portfolio_visible_services($site, 'todos') as $srv) {
    $l .= "- {$srv['titulo']}: {$srv['resumen']}\n";
}
$l .= "\n## Dónde trabaja\n";
$l .= "Está en {$pr['ciudad']} y atiende negocios de {$pr['area']}, también a distancia por videollamada y WhatsApp.\n\n";
$l .= "## Idiomas\nEspañol y portugués.\n\n";
$l .= "## Contacto\n";
$l .= "- WhatsApp: {$site['links']['whatsapp']}\n";
$l .= "- Correo: {$site['links']['email']}\n";
$l .= "- LinkedIn: {$site['links']['linkedin']}\n";
$l .= "- GitHub: {$site['links']['github']}\n\n";
$l .= "## Páginas clave\n";
$l .= '- Inicio: ' . absolute_url('/') . "\n";
$l .= '- Servicios y preguntas frecuentes: ' . absolute_url('/servicios') . "\n";
$l .= '- Restaurantes y comercios: ' . absolute_url('/restaurantes') . "\n";
$l .= '- Proyectos: ' . absolute_url('/proyectos') . "\n";
foreach (portfolio_sections($site) as $s) {
    $l .= "  - {$s['nombre']}: " . absolute_url($s['url']) . "\n";
}
if ($rubros) {
    $l .= '- Ejemplos por rubro (sitios de demostración de negocios ficticios): ' . absolute_url('/demos/') . "\n";
    foreach ($rubros as $r) {
        $l .= '  - ' . ucfirst($r) . ': ' . absolute_url('/demos/' . $r . '/') . "\n";
    }
}
$l .= '- Blog con guías para negocios: ' . absolute_url('/blog') . "\n";
foreach ($posts as $p) {
    $l .= "  - {$p['titulo']}: " . absolute_url(blog_url($p)) . "\n";
}
$l .= '- Contacto: ' . absolute_url('/contacto') . "\n";
file_put_contents(__DIR__ . '/llms.txt', $l);

echo count($urls) . " URLs en sitemap.xml; llms.txt escrito\n";
