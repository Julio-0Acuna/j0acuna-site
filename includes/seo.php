<?php
// Datos estructurados schema.org (JSON-LD). Solo describen lo que se ve en la página.
require_once __DIR__ . '/view.php';

function jsonld_person(array $site): array
{
    return [
        '@type' => 'Person',
        'name' => $site['profile']['name'],
        'jobTitle' => 'Desarrollador web',
        'address' => ['@type' => 'PostalAddress', 'addressLocality' => $site['profile']['ciudad'], 'addressCountry' => 'CL'],
        'sameAs' => array_values(array_filter([$site['links']['linkedin'], $site['links']['github']])),
    ];
}

function jsonld_business(array $site): array
{
    return [
        '@context' => 'https://schema.org',
        '@type' => 'ProfessionalService',
        'name' => $site['profile']['name'] . ' · Desarrollo web',
        'description' => $site['profile']['tagline'],
        'url' => SITE_URL . '/',
        'image' => absolute_url('assets/img/og/og-inicio.jpg'),
        'telephone' => '+' . preg_replace('/\D+/', '', $site['links']['whatsapp']),
        'email' => $site['links']['email'],
        'address' => ['@type' => 'PostalAddress', 'addressLocality' => $site['profile']['ciudad'], 'addressCountry' => 'CL'],
        'areaServed' => [
            ['@type' => 'City', 'name' => $site['profile']['ciudad']],
            ['@type' => 'Country', 'name' => 'Chile'],
        ],
        'founder' => jsonld_person($site),
    ];
}

function jsonld_service(array $service, array $site): array
{
    return [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $service['titulo'],
        'description' => $service['resumen'],
        'provider' => ['@type' => 'ProfessionalService', 'name' => $site['profile']['name'] . ' · Desarrollo web', 'url' => SITE_URL . '/'],
        'areaServed' => ['@type' => 'Country', 'name' => 'Chile'],
    ];
}

function jsonld_faq(array $faqs): array
{
    return [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(static fn (array $f): array => [
            '@type' => 'Question',
            'name' => $f['pregunta'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['respuesta']],
        ], $faqs),
    ];
}

/** @param array<int, array{0:string,1:string}> $items [nombre, ruta]; el inicio se agrega primero. */
function jsonld_breadcrumbs(array $items): array
{
    $out = [];
    foreach (array_merge([['Inicio', '/']], $items) as $i => [$nombre, $ruta]) {
        $out[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $nombre, 'item' => absolute_url($ruta)];
    }
    return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $out];
}

function jsonld_project(array $p, array $site): array
{
    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'CreativeWork',
        'name' => $p['title'],
        'description' => $p['description'],
        'url' => absolute_url(project_url($p)),
        'creator' => ['@type' => 'Person', 'name' => $site['profile']['name']],
        'keywords' => implode(', ', $p['technologies']),
    ];
    if ($p['image_url'] !== '') {
        $data['image'] = absolute_url($p['image_url']);
    }
    if ($p['anio'] !== '') {
        $data['dateCreated'] = (string) $p['anio'];
    }
    return $data;
}

function render_jsonld(array $blocks): string
{
    $html = '';
    foreach ($blocks as $b) {
        $html .= '<script type="application/ld+json">'
            . json_encode($b, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP)
            . "</script>\n";
    }
    return $html;
}
