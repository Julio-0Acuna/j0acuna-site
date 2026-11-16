<?php

const PORTFOLIO_ADMIN_FILE = __DIR__ . '/../data/admin.json';

/** Ruta de site.json. Se evalúa en cada llamada para que las pruebas puedan cambiarla. */
function portfolio_site_file(): string
{
    $env = getenv('PORTFOLIO_SITE_FILE');
    return ($env !== false && $env !== '') ? $env : __DIR__ . '/../data/site.json';
}

function portfolio_load_site(): array
{
    $file = portfolio_site_file();
    $data = file_exists($file) ? json_decode((string) file_get_contents($file), true) : null;
    $site = is_array($data) ? array_replace_recursive(portfolio_default_site(), $data) : portfolio_default_site();

    // array_replace_recursive mezcla listas por índice: se toman tal cual vienen en el archivo.
    $site['projects'] = array_map('portfolio_normalize_project', is_array($data['projects'] ?? null) ? $data['projects'] : $site['projects']);
    $site['services'] = array_map('portfolio_normalize_service', is_array($data['services'] ?? null) ? $data['services'] : []);
    $site['skills'] = is_array($data['skills'] ?? null) ? $data['skills'] : $site['skills'];

    return $site;
}

function portfolio_save_site(array $site): void
{
    portfolio_write_json(portfolio_site_file(), $site);
}

function portfolio_categories(): array
{
    return [
        ['id' => 'restaurantes', 'nombre' => 'Restaurantes y delivery'],
        ['id' => 'ejemplos', 'nombre' => 'Ejemplos por rubro'],
        ['id' => 'gestion', 'nombre' => 'Comercio y gestión'],
        ['id' => 'web', 'nombre' => 'Páginas web'],
        ['id' => 'ia', 'nombre' => 'Inteligencia artificial'],
        ['id' => 'formacion', 'nombre' => 'Formación y estudio'],
    ];
}

function portfolio_category_from_legacy(string $category): string
{
    $map = [
        'Pagina web' => 'web',
        'Proyecto web' => 'web',
        'Python' => 'formacion',
        'Python + MySQL' => 'formacion',
        'Java + MySQL' => 'formacion',
        'Blockchain' => 'formacion',
    ];
    return $map[$category] ?? 'web';
}

function portfolio_normalize_project(array $p): array
{
    $defaults = [
        'id' => '',
        'title' => '',
        'category' => '',
        'categoria_id' => portfolio_category_from_legacy((string) ($p['category'] ?? '')),
        'description' => '',
        'technologies' => [],
        'image_url' => '',
        'demo_url' => '',
        'repo_url' => '',
        'published' => false,
        'destacado' => false,
        'en_restaurantes' => false,
        'orden' => 999,
        'cliente' => '',
        'mostrar_cliente' => false,
        'pais' => '',
        'anio' => '',
        'problema' => '',
        'solucion' => '',
        'resultado' => '',
        'funcionalidades' => [],
        'galeria' => [],
        'credito' => '',
    ];
    $p = array_replace($defaults, $p);
    $p['orden'] = (int) $p['orden'];
    foreach (['published', 'destacado', 'en_restaurantes', 'mostrar_cliente'] as $flag) {
        $p[$flag] = (bool) $p[$flag];
    }
    foreach (['technologies', 'funcionalidades', 'galeria'] as $list) {
        $p[$list] = is_array($p[$list]) ? array_values($p[$list]) : [];
    }
    return $p;
}

function portfolio_normalize_service(array $s): array
{
    $defaults = [
        'id' => '',
        'titulo' => '',
        'resumen' => '',
        'puntos' => [],
        'para' => 'ambos',
        'nivel' => '',
        'orden' => 999,
        'publicado' => false,
        'precio_desde' => '',
        'mostrar_precio' => false,
    ];
    $s = array_replace($defaults, $s);
    $s['orden'] = (int) $s['orden'];
    $s['publicado'] = (bool) $s['publicado'];
    $s['mostrar_precio'] = (bool) $s['mostrar_precio'];
    $s['puntos'] = is_array($s['puntos']) ? array_values($s['puntos']) : [];
    return $s;
}

function portfolio_sort_by_order(array $items, string $titleKey): array
{
    usort($items, static function (array $a, array $b) use ($titleKey): int {
        return [$a['orden'], $a[$titleKey]] <=> [$b['orden'], $b[$titleKey]];
    });
    return $items;
}

function portfolio_visible_projects(array $site): array
{
    $visible = array_values(array_filter($site['projects'], static fn (array $p): bool => !empty($p['published'])));
    return portfolio_sort_by_order($visible, 'title');
}

function portfolio_featured_projects(array $site, int $limit = 6): array
{
    $featured = array_values(array_filter(portfolio_visible_projects($site), static fn (array $p): bool => $p['destacado']));
    return array_slice($featured, 0, $limit);
}

function portfolio_restaurant_projects(array $site, int $limit = 3): array
{
    $list = array_values(array_filter(portfolio_visible_projects($site), static fn (array $p): bool => $p['en_restaurantes']));
    return array_slice($list, 0, $limit);
}

function portfolio_find_project(array $site, string $id): ?array
{
    foreach (portfolio_visible_projects($site) as $p) {
        if ($p['id'] === $id) {
            return $p;
        }
    }
    return null;
}

function portfolio_categories_in_use(array $site): array
{
    $used = array_column(portfolio_visible_projects($site), 'categoria_id');
    return array_values(array_filter(portfolio_categories(), static fn (array $c): bool => in_array($c['id'], $used, true)));
}

function portfolio_visible_services(array $site, string $para): array
{
    $allowed = [
        'restaurantes' => ['restaurantes', 'ambos'],
        'empresas' => ['empresas', 'ambos'],
    ][$para] ?? null;

    $list = array_values(array_filter($site['services'], static function (array $s) use ($allowed): bool {
        return $s['publicado'] && ($allowed === null || in_array($s['para'], $allowed, true));
    }));
    return portfolio_sort_by_order($list, 'titulo');
}

function portfolio_load_admin(): array
{
    if (!file_exists(PORTFOLIO_ADMIN_FILE)) {
        return [];
    }

    $data = json_decode((string) file_get_contents(PORTFOLIO_ADMIN_FILE), true);
    return is_array($data) ? $data : [];
}

function portfolio_save_admin(array $admin): void
{
    portfolio_write_json(PORTFOLIO_ADMIN_FILE, $admin);
}

function portfolio_write_json(string $path, array $data): void
{
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function portfolio_slug(string $text): string
{
    $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $text), '-'));
    return $slug !== '' ? $slug : 'proyecto-' . time();
}

function portfolio_default_site(): array
{
    return [
        'profile' => [
            'name' => 'Julio Acuna',
            'role' => 'Desarrollador web',
            'headline' => 'Creo experiencias web claras, modernas y administrables.',
            'tagline' => 'Portafolio personal de desarrollo web.',
            'bio' => 'Soy Julio, desarrollo soluciones web con foco en sitios rapidos, funcionales y faciles de mantener.',
            'approach' => 'Trabajo con interfaces limpias, codigo ordenado y herramientas practicas para que cada proyecto pueda crecer sin perder claridad.',
            'location' => 'Chile',
            'contact_text' => 'Puedes revisar mis redes o escribirme directamente para colaborar.',
            'whatsapp_mensaje' => 'Hola Julio, vi tu página y quiero cotizar.',
        ],
        'links' => [
            'email' => 'tu-email@example.com',
            'github' => 'https://github.com/',
            'linkedin' => 'https://www.linkedin.com/',
            'whatsapp' => '',
        ],
        'skills' => ['PHP', 'JavaScript', 'CSS', 'Bootstrap', 'MySQL', 'Git', 'Responsive Design'],
        'services' => [],
        'projects' => [
            [
                'id' => 'portafolio-admin',
                'title' => 'Portafolio administrable',
                'category' => 'PHP',
                'description' => 'Sitio personal con panel privado para editar perfil, enlaces, habilidades y proyectos.',
                'technologies' => ['PHP', 'CSS', 'JavaScript'],
                'demo_url' => '',
                'repo_url' => '',
                'published' => true,
            ],
        ],
    ];
}
