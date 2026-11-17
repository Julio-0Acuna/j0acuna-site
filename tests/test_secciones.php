<?php
// Secciones de proyectos, datos estructurados (schema.org) y SEO por página.
require_once __DIR__ . '/../includes/view.php';
require_once __DIR__ . '/../includes/seo.php';

function con_fixture(callable $fn): void
{
    $antes = getenv('PORTFOLIO_SITE_FILE');
    putenv('PORTFOLIO_SITE_FILE=' . __DIR__ . '/fixtures/site.json');
    try {
        $fn(portfolio_load_site());
    } finally {
        putenv('PORTFOLIO_SITE_FILE' . ($antes === false ? '' : '=' . $antes));
    }
}

t('secciones solo con proyectos visibles, con conteo y portada', function () {
    con_fixture(function (array $s) {
        $secciones = portfolio_sections($s);
        $ids = array_column($secciones, 'id');
        ok(!in_array('ejemplos', $ids), 'ejemplos solo tiene un oculto');
        $rest = $secciones[array_search('restaurantes', $ids, true)];
        eq($rest['cantidad'], 1);
        ok($rest['resumen'] !== '', 'falta resumen de seccion');
        eq($rest['url'], '/proyectos/restaurantes');
    });
});

t('proyectos de una seccion y seccion inexistente', function () {
    con_fixture(function (array $s) {
        eq(array_column(portfolio_section_projects($s, 'gestion'), 'id'), ['gestion-b']);
        eq(portfolio_find_section($s, 'ejemplos'), null);
        eq(portfolio_find_section($s, 'nada'), null);
        eq(portfolio_find_section($s, 'web')['nombre'], 'Páginas web');
    });
});

t('jsonld negocio sin direccion y con area Santiago y Chile', function () {
    con_fixture(function (array $s) {
        $b = jsonld_business($s);
        eq($b['@type'], 'ProfessionalService');
        eq($b['telephone'], '+56973537642');
        ok(!isset($b['address']['streetAddress']), 'no debe publicar calle');
        eq(array_column($b['areaServed'], 'name'), ['Santiago', 'Chile']);
        eq($b['founder']['@type'], 'Person');
        ok(in_array('https://github.com/Julio-0Acuna/', $b['founder']['sameAs'], true));
    });
});

t('jsonld faq y breadcrumbs', function () {
    $faq = jsonld_faq([['pregunta' => '¿Trabajas en regiones?', 'respuesta' => 'Sí, en todo Chile.']]);
    eq($faq['@type'], 'FAQPage');
    eq($faq['mainEntity'][0]['acceptedAnswer']['text'], 'Sí, en todo Chile.');
    $bc = jsonld_breadcrumbs([['Proyectos', '/proyectos'], ['Web', '/proyectos/web']]);
    eq($bc['itemListElement'][0]['name'], 'Inicio');
    eq($bc['itemListElement'][2]['position'], 3);
    eq($bc['itemListElement'][2]['item'], SITE_URL . '/proyectos/web');
});

t('render jsonld escapa cierre de script', function () {
    $html = render_jsonld([['@context' => 'https://schema.org', 'name' => '</script><b>x']]);
    contiene($html, '<script type="application/ld+json">');
    no_contiene($html, '</script><b>');
});

t('robots nombra robots de IA y bloquea admin', function () {
    $r = (string) file_get_contents(__DIR__ . '/../robots.txt');
    foreach (['GPTBot', 'OAI-SearchBot', 'ClaudeBot', 'PerplexityBot', 'Google-Extended', 'Applebot-Extended', 'Bingbot'] as $bot) {
        contiene($r, "User-agent: $bot");
    }
    contiene($r, 'Disallow: /admin/');
    contiene($r, 'Sitemap: https://j0acuna.site/sitemap.xml');
});

t('llms.txt con datos clave', function () {
    $l = (string) @file_get_contents(__DIR__ . '/../llms.txt');
    contiene($l, '# Julio Acuña');
    contiene($l, 'Santiago');
    contiene($l, 'todo Chile');
    contiene($l, '+56 9 7353 7642');
    contiene($l, 'https://j0acuna.site/restaurantes');
    no_contiene($l, 'AlertaOficial');
});

t('fragmento de codigo solo si mostrar_codigo y archivo existe', function () {
    $dir = __DIR__ . '/fixtures/snippets';
    eq(portfolio_snippet(['id' => 'restaurante-a', 'mostrar_codigo' => false], $dir), null);
    $s = portfolio_snippet(['id' => 'restaurante-a', 'mostrar_codigo' => true], $dir);
    eq($s['lenguaje'], 'php');
    contiene($s['codigo'], '$total = $neto * 1.19;');
    eq(portfolio_snippet(['id' => 'gestion-b', 'mostrar_codigo' => true], $dir), null);
    eq(portfolio_snippet(['id' => 'web-c', 'mostrar_codigo' => true], $dir)['lenguaje'], 'none');
    eq(portfolio_snippet(['id' => 'sin-indice', 'mostrar_codigo' => true], $dir), null);
});
