<?php

const PORTFOLIO_SITE_FILE = __DIR__ . '/../data/site.json';
const PORTFOLIO_ADMIN_FILE = __DIR__ . '/../data/admin.json';

function portfolio_load_site(): array
{
    if (!file_exists(PORTFOLIO_SITE_FILE)) {
        return portfolio_default_site();
    }

    $data = json_decode((string) file_get_contents(PORTFOLIO_SITE_FILE), true);
    return is_array($data) ? array_replace_recursive(portfolio_default_site(), $data) : portfolio_default_site();
}

function portfolio_save_site(array $site): void
{
    portfolio_write_json(PORTFOLIO_SITE_FILE, $site);
}

function portfolio_visible_projects(array $site): array
{
    return array_values(array_filter($site['projects'], static fn ($project) => !empty($project['published'])));
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

    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
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
        ],
        'links' => [
            'email' => 'tu-email@example.com',
            'github' => 'https://github.com/',
            'linkedin' => 'https://www.linkedin.com/',
        ],
        'skills' => ['PHP', 'JavaScript', 'CSS', 'Bootstrap', 'MySQL', 'Git', 'Responsive Design'],
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
