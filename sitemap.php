<?php
// Uso (CLI): tools/php.sh sitemap.php  → escribe sitemap.xml con las páginas públicas.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$urls = ['/', '/restaurantes'];
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
echo count($urls) . " URLs escritas en sitemap.xml\n";
