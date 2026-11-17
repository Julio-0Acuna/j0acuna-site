<?php
// Blog: artículos en data/blog/<slug>.md con encabezado "clave: valor" entre líneas ---
// y cuerpo en Markdown simple. Todo el HTML sale escapado.
require_once __DIR__ . '/view.php';

function blog_dir(): string
{
    $env = getenv('PORTFOLIO_BLOG_DIR');
    return ($env !== false && $env !== '') ? $env : __DIR__ . '/../data/blog';
}

function blog_parse(string $texto, string $slug): ?array
{
    $texto = str_replace(["\r\n", "\r"], "\n", $texto);
    $texto = preg_replace('/^\xEF\xBB\xBF/', '', $texto);
    if (!preg_match('/^---\n(.*?)\n---\n(.*)$/s', $texto, $m)) {
        return null;
    }
    $meta = [];
    foreach (explode("\n", $m[1]) as $linea) {
        if (preg_match('/^([a-z_]+):\s*(.*)$/', $linea, $kv)) {
            $meta[$kv[1]] = trim($kv[2]);
        }
    }
    $titulo = $meta['titulo'] ?? '';
    $fecha = $meta['fecha'] ?? '';
    if ($titulo === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        return null;
    }
    $cuerpo = trim($m[2]);
    $palabras = count(preg_split('/\s+/u', strip_tags($cuerpo), -1, PREG_SPLIT_NO_EMPTY));
    return [
        'slug' => $slug,
        'titulo' => $titulo,
        'descripcion' => $meta['descripcion'] ?? '',
        'palabra_clave' => $meta['palabra_clave'] ?? '',
        'fecha' => $fecha,
        'imagen' => $meta['imagen'] ?? '',
        'imagen_alt' => ($meta['imagen_alt'] ?? '') !== '' ? $meta['imagen_alt'] : $titulo,
        'resumen' => $meta['resumen'] ?? '',
        'borrador' => in_array(strtolower($meta['borrador'] ?? 'false'), ['true', 'si', 'sí', '1'], true),
        'cuerpo' => $cuerpo,
        'palabras' => $palabras,
        'minutos' => max(1, (int) round($palabras / 200)),
    ];
}

/** Artículos publicados, del más nuevo al más antiguo. */
function blog_posts(?string $dir = null): array
{
    $dir = $dir ?? blog_dir();
    $posts = [];
    foreach (glob($dir . '/*.md') ?: [] as $archivo) {
        $slug = basename($archivo, '.md');
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            continue;
        }
        $p = blog_parse((string) file_get_contents($archivo), $slug);
        if ($p !== null && !$p['borrador']) {
            $posts[] = $p;
        }
    }
    usort($posts, static fn (array $a, array $b): int => [$b['fecha'], $a['titulo']] <=> [$a['fecha'], $b['titulo']]);
    return $posts;
}

function blog_find(string $slug, ?string $dir = null): ?array
{
    if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
        return null;
    }
    foreach (blog_posts($dir) as $p) {
        if ($p['slug'] === $slug) {
            return $p;
        }
    }
    return null;
}

function blog_related(array $post, array $posts, int $n = 3): array
{
    return array_slice(array_values(array_filter($posts, static fn (array $p): bool => $p['slug'] !== $post['slug'])), 0, $n);
}

function blog_url(array $post): string
{
    return '/blog/' . $post['slug'];
}

function blog_fecha(string $fecha): string
{
    $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    [$a, $m, $d] = array_map('intval', explode('-', $fecha));
    return $d . ' de ' . $meses[$m - 1] . ' de ' . $a;
}

/** Formato en línea sobre texto YA escapado: enlaces seguros, negrita y cursiva. */
function blog_inline(string $escapado): string
{
    $html = preg_replace_callback('/\[([^\]]+)\]\(([^)\s]+)\)/u', static function (array $m): string {
        $url = html_entity_decode($m[2], ENT_QUOTES, 'UTF-8');
        if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
            return '<a href="' . e($url) . '">' . $m[1] . '</a>';
        }
        if (preg_match('#^https?://#i', $url)) {
            return '<a href="' . e($url) . '" target="_blank" rel="noopener">' . $m[1] . '</a>';
        }
        return $m[1];
    }, $escapado);
    $html = preg_replace('/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $html);
    return preg_replace('/(?<![*\w])\*(?!\s)(.+?)(?<!\s)\*(?![*\w])/u', '<em>$1</em>', $html);
}

function blog_markdown(string $md): string
{
    $md = str_replace(["\r\n", "\r"], "\n", trim($md));
    $html = '';
    foreach (preg_split('/\n\s*\n/', $md) as $bloque) {
        $lineas = array_values(array_filter(array_map('rtrim', explode("\n", trim($bloque))), static fn ($l) => $l !== ''));
        if (!$lineas) {
            continue;
        }
        $primera = $lineas[0];
        if (count($lineas) === 1 && preg_match('/^(#{2,3})\s+(.+)$/u', $primera, $m)) {
            $n = strlen($m[1]);
            $html .= "<h$n>" . blog_inline(e($m[2])) . "</h$n>\n";
            continue;
        }
        $todas = static fn (string $re): bool => count(array_filter($lineas, static fn ($l) => preg_match($re, $l))) === count($lineas);
        if ($todas('/^[-*]\s+/')) {
            $items = array_map(static fn ($l) => '<li>' . blog_inline(e(preg_replace('/^[-*]\s+/', '', $l))) . '</li>', $lineas);
            $html .= '<ul>' . implode('', $items) . "</ul>\n";
            continue;
        }
        if ($todas('/^\d+\.\s+/')) {
            $items = array_map(static fn ($l) => '<li>' . blog_inline(e(preg_replace('/^\d+\.\s+/', '', $l))) . '</li>', $lineas);
            $html .= '<ol>' . implode('', $items) . "</ol>\n";
            continue;
        }
        if ($todas('/^>\s?/')) {
            $texto = implode(' ', array_map(static fn ($l) => preg_replace('/^>\s?/', '', $l), $lineas));
            $html .= '<blockquote><p>' . blog_inline(e($texto)) . "</p></blockquote>\n";
            continue;
        }
        $html .= '<p>' . blog_inline(e(implode(' ', $lineas))) . "</p>\n";
    }
    return $html;
}

function jsonld_blogposting(array $post, array $site): array
{
    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $post['titulo'],
        'description' => $post['descripcion'],
        'datePublished' => $post['fecha'],
        'dateModified' => $post['fecha'],
        'inLanguage' => 'es-CL',
        'author' => ['@type' => 'Person', 'name' => $site['profile']['name'], 'url' => SITE_URL . '/'],
        'publisher' => ['@type' => 'Person', 'name' => $site['profile']['name']],
        'mainEntityOfPage' => absolute_url(blog_url($post)),
        'wordCount' => $post['palabras'],
    ];
    if ($post['palabra_clave'] !== '') {
        $data['keywords'] = $post['palabra_clave'];
    }
    if ($post['imagen'] !== '') {
        $data['image'] = absolute_url($post['imagen']);
    }
    return $data;
}
