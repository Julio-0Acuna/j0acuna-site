<?php
// Blog: lectura de artículos Markdown con encabezado, orden, borradores y HTML seguro.
require_once __DIR__ . '/../includes/blog.php';

const BLOG_FIXTURES = __DIR__ . '/fixtures/blog';

t('blog lista publicados, del mas nuevo al mas antiguo', function () {
    eq(array_column(blog_posts(BLOG_FIXTURES), 'slug'), ['segundo-articulo', 'primer-articulo']);
});

t('blog ignora borradores, archivos rotos y nombres invalidos', function () {
    $slugs = array_column(blog_posts(BLOG_FIXTURES), 'slug');
    ok(!in_array('borrador-oculto', $slugs, true));
    ok(!in_array('roto', $slugs, true));
    ok(!in_array('Mal_Nombre', $slugs, true));
    eq(blog_find('borrador-oculto', BLOG_FIXTURES), null);
    eq(blog_find('../data/site', BLOG_FIXTURES), null);
});

t('blog lee el encabezado y calcula lectura', function () {
    $p = blog_find('primer-articulo', BLOG_FIXTURES);
    eq($p['titulo'], 'Página web para restaurantes en Chile');
    eq($p['palabra_clave'], 'página web para restaurantes');
    eq($p['fecha'], '2026-11-10');
    eq($p['imagen'], 'demos/pizzeria/img/multi.webp');
    eq($p['minutos'], 1);
    eq(blog_find('segundo-articulo', BLOG_FIXTURES)['imagen_alt'], 'Menú QR para tu local');
});

t('markdown a html seguro', function () {
    $html = blog_markdown(blog_find('primer-articulo', BLOG_FIXTURES)['cuerpo']);
    contiene($html, '<strong>página web para restaurantes</strong>');
    contiene($html, '<a href="/servicios">los servicios</a>');
    contiene($html, '<a href="https://www.google.com" target="_blank" rel="noopener">Google</a>');
    contiene($html, '<h2>Qué debe tener</h2>');
    contiene($html, '<ul><li>Menú con precios</li><li>Botón de <em>WhatsApp</em></li></ul>');
    contiene($html, '<ol><li>Primero el menú</li><li>Después las fotos</li></ol>');
    contiene($html, '<blockquote><p>Una cita de ejemplo.</p></blockquote>');
    no_contiene($html, '<script>');
    contiene($html, '&lt;script&gt;');
    no_contiene($html, 'href="javascript:');
});

t('relacionados excluyen el actual', function () {
    $posts = blog_posts(BLOG_FIXTURES);
    eq(array_column(blog_related($posts[0], $posts, 3), 'slug'), ['primer-articulo']);
});

t('fecha legible en espanol', function () {
    eq(blog_fecha('2026-11-17'), '17 de noviembre de 2026');
});

t('sitemap y llms incluyen el blog', function () {
    contiene((string) file_get_contents(__DIR__ . '/../sitemap.xml'), '/blog</loc>');
    contiene((string) file_get_contents(__DIR__ . '/../llms.txt'), '/blog');
});
