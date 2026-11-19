<?php
// Blog: artículos en data/blog/<slug>.md con encabezado "clave: valor" entre líneas ---
// y cuerpo en Markdown simple. Todo el HTML sale escapado.
// Encabezado: titulo, descripcion, palabra_clave, palabras_clave (lista separada por comas),
// tema (uno de blog_temas()), destacado, fecha, imagen, imagen_alt, resumen, borrador.
// Las líneas que empiezan con # dentro del encabezado son comentarios (fuentes, notas).
// Preguntas frecuentes: sección "## Preguntas frecuentes" con "### ¿Pregunta?" y su respuesta.
require_once __DIR__ . '/view.php';

function blog_dir(): string
{
    $env = getenv('PORTFOLIO_BLOG_DIR');
    return ($env !== false && $env !== '') ? $env : __DIR__ . '/../data/blog';
}

/** Temas del blog: slug para ?tema=… => nombre visible. "Mi historia" queda disponible aunque no tenga artículos. */
function blog_temas(): array
{
    return [
        'paginas-web' => 'Páginas web',
        'tiendas-y-plataformas' => 'Tiendas y plataformas',
        'restaurantes-y-delivery' => 'Restaurantes y delivery',
        'reservas-y-servicios' => 'Reservas y servicios',
        'comercio' => 'Comercio',
        'google-y-seo' => 'Google y SEO',
        'sistemas-e-ia' => 'Sistemas e IA',
        'mi-historia' => 'Mi historia',
    ];
}

/** Lista "a, b, c" sin vacías ni repetidas (sin distinguir mayúsculas), dejando fuera las de $excluir. */
function blog_lista(string $texto, array $excluir = []): array
{
    $vistas = array_map(static fn (string $x): string => mb_strtolower($x), $excluir);
    $out = [];
    foreach (explode(',', $texto) as $item) {
        $item = trim($item);
        $clave = mb_strtolower($item);
        if ($item === '' || in_array($clave, $vistas, true)) {
            continue;
        }
        $vistas[] = $clave;
        $out[] = $item;
    }
    return $out;
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
    $principal = $meta['palabra_clave'] ?? '';
    $tema = '';
    $temaSlug = '';
    foreach (blog_temas() as $s => $nombre) {
        if (mb_strtolower($nombre) === mb_strtolower($meta['tema'] ?? '')) {
            [$tema, $temaSlug] = [$nombre, $s];
        }
    }
    $si = static fn (string $v): bool => in_array(strtolower($v), ['true', 'si', 'sí', '1'], true);
    return [
        'slug' => $slug,
        'titulo' => $titulo,
        'descripcion' => $meta['descripcion'] ?? '',
        'palabra_clave' => $principal,
        'palabras_clave' => blog_lista($meta['palabras_clave'] ?? '', $principal !== '' ? [$principal] : []),
        'tema' => $tema,
        'tema_slug' => $temaSlug,
        'destacado' => $si($meta['destacado'] ?? 'false'),
        'fecha' => $fecha,
        'imagen' => $meta['imagen'] ?? '',
        'imagen_alt' => ($meta['imagen_alt'] ?? '') !== '' ? $meta['imagen_alt'] : $titulo,
        'resumen' => $meta['resumen'] ?? '',
        'borrador' => $si($meta['borrador'] ?? 'false'),
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

/** "Sigue leyendo": primero los del mismo tema y después el resto, cada grupo en el orden de $posts. */
function blog_related(array $post, array $posts, int $n = 3): array
{
    $otros = array_values(array_filter($posts, static fn (array $p): bool => $p['slug'] !== $post['slug']));
    $tema = $post['tema'] ?? '';
    if ($tema !== '') {
        $mismo = array_filter($otros, static fn (array $p): bool => ($p['tema'] ?? '') === $tema);
        $resto = array_filter($otros, static fn (array $p): bool => ($p['tema'] ?? '') !== $tema);
        $otros = array_merge(array_values($mismo), array_values($resto));
    }
    return array_slice($otros, 0, $n);
}

function blog_por_tema(array $posts, string $temaSlug): array
{
    return array_values(array_filter($posts, static fn (array $p): bool => $p['tema_slug'] === $temaSlug));
}

/** Artículo grande de /blog: el primero marcado como destacado o, si no hay, el más nuevo. */
function blog_destacado(array $posts): ?array
{
    foreach ($posts as $p) {
        if (!empty($p['destacado'])) {
            return $p;
        }
    }
    return $posts[0] ?? null;
}

function blog_es_titulo_faq(string $titulo): bool
{
    return mb_strtolower(trim($titulo)) === 'preguntas frecuentes';
}

/** Markdown en línea a texto plano: enlaces quedan con su texto, sin asteriscos. */
function blog_texto_plano(string $md): string
{
    $t = preg_replace('/\[([^\]]+)\]\([^)\s]+\)/u', '$1', $md);
    $t = preg_replace('/\*\*(.+?)\*\*/u', '$1', $t);
    $t = preg_replace('/(?<![*\w])\*(?!\s)(.+?)(?<!\s)\*(?![*\w])/u', '$1', $t);
    return trim(preg_replace('/\s+/u', ' ', $t));
}

/**
 * Preguntas frecuentes del cuerpo: cada "### Pregunta" bajo "## Preguntas frecuentes" con el texto que la sigue,
 * hasta el siguiente "## ". Devuelve [['pregunta' => …, 'respuesta' => …], …] en texto plano, sin las vacías.
 */
function blog_faqs(string $md): array
{
    $md = str_replace(["\r\n", "\r"], "\n", trim($md));
    $dentro = false;
    $faqs = [];
    $actual = null;
    foreach (preg_split('/\n\s*\n/', $md) as $bloque) {
        $bloque = trim($bloque);
        if (preg_match('/^##\s+(.+)$/u', $bloque, $m) && !str_contains($bloque, "\n")) {
            $dentro = blog_es_titulo_faq($m[1]);
            $actual = null;
            continue;
        }
        if (!$dentro || $bloque === '') {
            continue;
        }
        if (preg_match('/^###\s+(.+)$/u', $bloque, $m) && !str_contains($bloque, "\n")) {
            $faqs[] = ['pregunta' => blog_texto_plano($m[1]), 'respuesta' => ''];
            $actual = count($faqs) - 1;
            continue;
        }
        if ($actual !== null) {
            $lineas = array_map(static fn (string $l): string => preg_replace('/^\s*(?:[-*]\s+|\d+\.\s+|>\s?)/', '', $l), explode("\n", $bloque));
            $texto = blog_texto_plano(implode(' ', $lineas));
            $faqs[$actual]['respuesta'] = trim($faqs[$actual]['respuesta'] . ' ' . $texto);
        }
    }
    return array_values(array_filter($faqs, static fn (array $f): bool => $f['pregunta'] !== '' && $f['respuesta'] !== ''));
}

/** Miniatura de un artículo en las listas (/blog y "Sigue leyendo"): su imagen o, si no tiene, el logo JA en degradado. */
function blog_item_media(array $p): string
{
    if ($p['imagen'] !== '') {
        return '<span class="blog-item-media"><img src="' . e(asset($p['imagen'])) . '" alt="' . e($p['imagen_alt'])
            . '" loading="lazy" width="1200" height="750"></span>';
    }
    return '<span class="blog-item-media blog-item-media--logo"><svg aria-hidden="true" focusable="false"><use href="#i-logo"/></svg></span>';
}

function blog_tema_url(string $temaSlug): string
{
    return '/blog?tema=' . $temaSlug;
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
            $id = $n === 2 && blog_es_titulo_faq($m[2]) ? ' id="preguntas-frecuentes"' : '';
            $html .= "<h$n$id>" . blog_inline(e($m[2])) . "</h$n>\n";
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
    $claves = array_merge($post['palabra_clave'] !== '' ? [$post['palabra_clave']] : [], $post['palabras_clave'] ?? []);
    if ($claves) {
        $data['keywords'] = implode(', ', $claves);
    }
    if (($post['tema'] ?? '') !== '') {
        $data['articleSection'] = $post['tema'];
    }
    if ($post['imagen'] !== '') {
        $data['image'] = absolute_url($post['imagen']);
    }
    return $data;
}
