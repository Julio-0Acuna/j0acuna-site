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

// --- 18/11: palabras clave, temas, preguntas frecuentes y enlaces internos

t('blog lee palabras_clave como lista, sin vacias ni repetidas (tampoco la principal)', function () {
    eq(blog_find('segundo-articulo', BLOG_FIXTURES)['palabras_clave'], ['menú digital', 'carta QR']);
    eq(blog_find('primer-articulo', BLOG_FIXTURES)['palabras_clave'], []);
});

t('blog ignora lineas de comentario en el encabezado', function () {
    $p = blog_parse("---\n# Fuente: nic.cl/dominios/tarifas.html\ntitulo: X\nfecha: 2026-11-18\n---\nCuerpo.", 'x');
    eq($p['titulo'], 'X');
    eq(blog_find('segundo-articulo', BLOG_FIXTURES)['titulo'], 'Menú QR para tu local');
});

t('jsonld keywords junta la principal y sus variantes; articleSection es el tema', function () {
    $site = ['profile' => ['name' => 'Julio Acuña']];
    $d = jsonld_blogposting(blog_find('segundo-articulo', BLOG_FIXTURES), $site);
    eq($d['keywords'], 'menú QR, menú digital, carta QR');
    eq($d['articleSection'], 'Restaurantes y delivery');
    $d1 = jsonld_blogposting(blog_find('primer-articulo', BLOG_FIXTURES), $site);
    eq($d1['keywords'], 'página web para restaurantes');
    $sin = jsonld_blogposting(blog_parse("---\ntitulo: X\nfecha: 2026-11-18\n---\nCuerpo.", 'x'), $site);
    ok(!isset($sin['keywords']) && !isset($sin['articleSection']), 'sin palabra clave ni tema no hay keywords ni articleSection');
});

t('blog temas: lista fija con slug, incluido Mi historia', function () {
    eq(array_values(blog_temas()), ['Páginas web', 'Tiendas y plataformas', 'Restaurantes y delivery', 'Reservas y servicios', 'Comercio', 'Google y SEO', 'Sistemas e IA', 'Mi historia']);
    eq(array_keys(blog_temas()), ['paginas-web', 'tiendas-y-plataformas', 'restaurantes-y-delivery', 'reservas-y-servicios', 'comercio', 'google-y-seo', 'sistemas-e-ia', 'mi-historia']);
});

t('blog lee el tema, acepta mayusculas distintas y descarta temas que no existen', function () {
    $p = blog_find('primer-articulo', BLOG_FIXTURES);
    eq([$p['tema'], $p['tema_slug']], ['Páginas web', 'paginas-web']);
    $q = blog_parse("---\ntitulo: X\nfecha: 2026-11-18\ntema: google y seo\n---\nCuerpo.", 'x');
    eq([$q['tema'], $q['tema_slug']], ['Google y SEO', 'google-y-seo']);
    $r = blog_parse("---\ntitulo: X\nfecha: 2026-11-18\ntema: Cualquier cosa\n---\nCuerpo.", 'x');
    eq([$r['tema'], $r['tema_slug']], ['', '']);
});

t('blog filtra por tema', function () {
    $posts = blog_posts(BLOG_FIXTURES);
    eq(array_column(blog_por_tema($posts, 'paginas-web'), 'slug'), ['primer-articulo']);
    eq(array_column(blog_por_tema($posts, 'restaurantes-y-delivery'), 'slug'), ['segundo-articulo']);
    eq(blog_por_tema($posts, 'mi-historia'), []);
});

t('blog destacado: el marcado con destacado, si no el mas nuevo', function () {
    $posts = blog_posts(BLOG_FIXTURES);
    eq(blog_destacado($posts)['slug'], 'primer-articulo');
    eq(blog_destacado(blog_por_tema($posts, 'restaurantes-y-delivery'))['slug'], 'segundo-articulo');
    eq(blog_destacado([]), null);
    eq(blog_find('segundo-articulo', BLOG_FIXTURES)['destacado'], false);
});

t('blog extrae las preguntas frecuentes en texto plano', function () {
    eq(blog_faqs(blog_find('segundo-articulo', BLOG_FIXTURES)['cuerpo']), [
        ['pregunta' => '¿Funciona sin internet?', 'respuesta' => 'No. El cliente necesita datos o wifi para abrir la carta.'],
        ['pregunta' => '¿Puedo cambiar precios?', 'respuesta' => 'Sí, desde el panel. Lee la guía de pedidos.'],
    ]);
    eq(blog_faqs(blog_find('primer-articulo', BLOG_FIXTURES)['cuerpo']), []);
    eq(blog_faqs("### ¿Sin seccion?\n\nRespuesta suelta."), [], 'solo cuenta lo que va bajo ## Preguntas frecuentes');
});

t('markdown: el titulo de preguntas frecuentes lleva ancla', function () {
    $html = blog_markdown(blog_find('segundo-articulo', BLOG_FIXTURES)['cuerpo']);
    contiene($html, '<h2 id="preguntas-frecuentes">Preguntas frecuentes</h2>');
    contiene($html, '<h3>¿Funciona sin internet?</h3>');
    contiene($html, '<h2>Otra sección</h2>');
});

t('miniatura: imagen del articulo o logo JA, nunca iniciales', function () {
    $con = blog_item_media(blog_find('primer-articulo', BLOG_FIXTURES));
    contiene($con, '<img src="/demos/pizzeria/img/multi.webp');
    contiene($con, 'alt="Sitio de una pizzería"');
    $sin = blog_item_media(blog_find('segundo-articulo', BLOG_FIXTURES));
    contiene($sin, 'blog-item-media--logo');
    contiene($sin, '<use href="#i-logo"/>');
    no_contiene($sin, '<img');
    no_contiene($sin, 'tile-placeholder');
});

t('relacionados priorizan el mismo tema y despues los mas nuevos', function () {
    $mk = static fn (string $slug, string $tema): array => ['slug' => $slug, 'tema' => $tema, 'titulo' => $slug];
    $posts = [$mk('a', 'Comercio'), $mk('b', 'Google y SEO'), $mk('c', 'Comercio'), $mk('d', 'Google y SEO'), $mk('e', 'Comercio')];
    eq(array_column(blog_related($posts[3], $posts, 3), 'slug'), ['b', 'a', 'c']);
    eq(array_column(blog_related($posts[0], $posts, 3), 'slug'), ['c', 'e', 'b']);
    eq(array_column(blog_related($mk('z', ''), $posts, 2), 'slug'), ['a', 'b'], 'sin tema: los mas nuevos');
});

// --- Artículos reales (data/blog): encabezado completo y enlaces internos que existen

t('articulos reales: encabezado completo, tema valido y 5 a 10 palabras clave', function () {
    $temas = blog_temas();
    foreach (blog_posts() as $p) {
        $s = $p['slug'];
        ok($p['descripcion'] !== '' && mb_strlen($p['descripcion']) <= 160, "$s: descripcion vacia o de mas de 160 caracteres");
        ok(mb_strlen($p['titulo']) <= 65, "$s: titulo de mas de 65 caracteres");
        ok($p['palabra_clave'] !== '' && $p['resumen'] !== '', "$s: falta palabra_clave o resumen");
        ok($p['imagen'] === '' || is_file(__DIR__ . '/../' . $p['imagen']), "$s: no existe la imagen {$p['imagen']}");
        ok(in_array($p['tema'], $temas, true), "$s: tema invalido");
        $n = count($p['palabras_clave']);
        ok($n >= 5 && $n <= 10, "$s: tiene $n palabras_clave (5 a 10)");
        // "Mi historia" es un relato personal: no lleva preguntas frecuentes.
        ok($p['tema'] === 'Mi historia' || count(blog_faqs($p['cuerpo'])) >= 3, "$s: menos de 3 preguntas frecuentes");
    }
});

t('articulos reales: Mi historia tiene su articulo y es el destacado del indice', function () {
    $posts = blog_posts();
    eq(array_column(blog_por_tema($posts, 'mi-historia'), 'slug'), ['mi-historia']);
    eq(blog_destacado($posts)['slug'], 'mi-historia');
    eq(count(array_filter($posts, static fn (array $p): bool => $p['destacado'])), 1, 'un solo destacado');
});

t('articulos reales: los enlaces internos llevan a paginas que existen', function () {
    $site = portfolio_load_site();
    $servicios = array_column($site['services'], 'id');
    $secciones = array_map(static fn (array $s): string => $s['url'], portfolio_sections($site));
    $proyectos = array_map('project_url', portfolio_visible_projects($site));
    $slugs = array_column(blog_posts(), 'slug');
    $fijas = ['/', '/servicios', '/proyectos', '/restaurantes', '/contacto', '/blog', '/demos/'];
    $raiz = realpath(__DIR__ . '/..');
    foreach (blog_posts() as $p) {
        preg_match_all('/\]\((\/[^)\s]*)\)/u', $p['cuerpo'], $m);
        foreach ($m[1] as $url) {
            [$ruta, $ancla] = array_pad(explode('#', $url, 2), 2, '');
            [$ruta, $query] = array_pad(explode('?', $ruta, 2), 2, '');
            $donde = "{$p['slug']} enlaza $url";
            if ($ruta === '/servicios' && $ancla !== '') {
                ok(in_array($ancla, $servicios, true), "$donde: no existe el servicio #$ancla");
            } elseif ($ruta === '/blog' && $query !== '') {
                ok(isset(blog_temas()[preg_replace('/^tema=/', '', $query)]), "$donde: tema inexistente");
            } elseif (str_starts_with($ruta, '/blog/')) {
                ok(in_array(substr($ruta, 6), $slugs, true), "$donde: no existe el articulo");
            } elseif (str_starts_with($ruta, '/demos/')) {
                $f = $raiz . $ruta;
                ok(is_file($f) || is_file(rtrim($f, '/') . '/index.php'), "$donde: no existe en demos/");
            } elseif (str_starts_with($ruta, '/proyectos/')) {
                ok(in_array($ruta, $secciones, true), "$donde: no existe la seccion");
            } elseif (str_starts_with($ruta, '/proyecto/')) {
                ok(in_array($ruta, $proyectos, true), "$donde: proyecto oculto o inexistente");
            } else {
                ok(in_array($ruta, $fijas, true), "$donde: ruta desconocida");
            }
        }
        ok(str_contains($p['cuerpo'], '](/contacto)'), "{$p['slug']}: no enlaza a /contacto");
    }
});
