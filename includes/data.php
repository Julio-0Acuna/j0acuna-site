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
    $site['faqs'] = array_values(array_filter(
        is_array($data['faqs'] ?? null) ? $data['faqs'] : [],
        static fn ($f): bool => is_array($f) && trim((string) ($f['pregunta'] ?? '')) !== '' && trim((string) ($f['respuesta'] ?? '')) !== ''
    ));

    return $site;
}

function portfolio_save_site(array $site): void
{
    portfolio_write_json(portfolio_site_file(), $site);
}

function portfolio_categories(): array
{
    return [
        ['id' => 'restaurantes', 'nombre' => 'Restaurantes y delivery', 'resumen' => 'Pedidos, paneles y herramientas para restaurantes y apps de delivery.'],
        ['id' => 'ejemplos', 'nombre' => 'Ejemplos por rubro', 'resumen' => 'Sitios de ejemplo para distintos tipos de negocio.'],
        ['id' => 'gestion', 'nombre' => 'Comercio y gestión', 'resumen' => 'Tiendas online y sistemas que ordenan ventas, inventario y equipos.'],
        ['id' => 'web', 'nombre' => 'Páginas web', 'resumen' => 'Sitios rápidos, claros y pensados para el celular.'],
        ['id' => 'ia', 'nombre' => 'Inteligencia artificial', 'resumen' => 'Asistentes, voz y documentos con inteligencia artificial.'],
        ['id' => 'formacion', 'nombre' => 'Formación y estudio', 'resumen' => 'Cursos, juegos educativos y proyectos de estudio.'],
    ];
}

/** Secciones con al menos un proyecto visible: nombre, resumen, url, cantidad y portada. */
function portfolio_sections(array $site): array
{
    $sections = [];
    foreach (portfolio_categories() as $c) {
        $projects = portfolio_section_projects($site, $c['id']);
        if (!$projects) {
            continue;
        }
        $cover = '';
        foreach ($projects as $p) {
            if ($p['image_url'] !== '') {
                $cover = $p['image_url'];
                break;
            }
        }
        $sections[] = $c + ['url' => '/proyectos/' . $c['id'], 'cantidad' => count($projects), 'portada' => $cover];
    }
    return $sections;
}

function portfolio_section_projects(array $site, string $id): array
{
    return array_values(array_filter(portfolio_visible_projects($site), static fn (array $p): bool => $p['categoria_id'] === $id));
}

function portfolio_find_section(array $site, string $id): ?array
{
    foreach (portfolio_sections($site) as $s) {
        if ($s['id'] === $id) {
            return $s;
        }
    }
    return null;
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
        'imagen_ejemplo' => false,
        'mostrar_codigo' => false,
    ];
    $p = array_replace($defaults, $p);
    $p['imagen_ejemplo'] = (bool) $p['imagen_ejemplo'];
    $p['mostrar_codigo'] = (bool) $p['mostrar_codigo'];
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

/**
 * Aplica lo enviado por el panel sin borrar lo que el formulario no conoce:
 * perfil y enlaces se mezclan; cada proyecto se mezcla sobre el existente con su id;
 * servicios y categorías no se tocan. `published` es casilla: si no viene, queda en false.
 */
function portfolio_apply_admin_post(array $site, array $post): array
{
    $trim = static fn ($v): string => trim((string) $v);

    if (isset($post['profile']) && is_array($post['profile'])) {
        $site['profile'] = array_replace($site['profile'], array_map($trim, $post['profile']));
    }
    if (isset($post['links']) && is_array($post['links'])) {
        $site['links'] = array_replace($site['links'], array_map($trim, $post['links']));
    }
    if (array_key_exists('skills', $post)) {
        $site['skills'] = array_values(array_filter(array_map('trim', explode(',', (string) $post['skills']))));
    }

    $existing = [];
    foreach ($site['projects'] as $project) {
        $existing[$project['id']] = $project;
    }

    $projects = [];
    foreach (($post['projects'] ?? []) as $sent) {
        $title = $trim($sent['title'] ?? '');
        if ($title === '') {
            continue;
        }
        $id = $trim($sent['id'] ?? '') ?: portfolio_slug($title);
        $merged = $existing[$id] ?? [];
        foreach (['category', 'description', 'image_url', 'demo_url', 'repo_url'] as $field) {
            if (array_key_exists($field, $sent)) {
                $merged[$field] = $trim($sent[$field]);
            }
        }
        if (array_key_exists('technologies', $sent)) {
            $merged['technologies'] = array_values(array_filter(array_map('trim', explode(',', (string) $sent['technologies']))));
        }
        $merged['id'] = $id;
        $merged['title'] = $title;
        $merged['published'] = !empty($sent['published']);
        $projects[] = portfolio_normalize_project($merged);
    }
    $site['projects'] = $projects;

    return $site;
}

/**
 * Fragmento de código del proyecto (data/snippets/indice.json + archivo), solo si el proyecto
 * tiene mostrar_codigo. Devuelve null si falta algo.
 */
function portfolio_snippet(array $project, ?string $dir = null): ?array
{
    if (empty($project['mostrar_codigo'])) {
        return null;
    }
    $dir = $dir ?? __DIR__ . '/../data/snippets';
    $indice = is_file($dir . '/indice.json') ? json_decode((string) file_get_contents($dir . '/indice.json'), true) : null;
    foreach (is_array($indice) ? $indice : [] as $s) {
        if (($s['id'] ?? '') !== $project['id']) {
            continue;
        }
        $archivo = $dir . '/' . basename((string) ($s['archivo'] ?? ''));
        if (!is_file($archivo)) {
            return null;
        }
        $lenguaje = in_array($s['lenguaje'] ?? '', ['php', 'python', 'javascript', 'java', 'sql'], true) ? $s['lenguaje'] : 'none';
        return [
            'lenguaje' => $lenguaje,
            'titulo' => (string) ($s['titulo'] ?? ''),
            'explicacion' => (string) ($s['explicacion'] ?? ''),
            'codigo' => (string) file_get_contents($archivo),
        ];
    }
    return null;
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
            'ciudad' => 'Santiago',
            'area' => 'todo Chile',
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
        'faqs' => [],
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
