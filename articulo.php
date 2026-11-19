<?php
require_once __DIR__ . '/includes/view.php';
require_once __DIR__ . '/includes/blog.php';

$site = portfolio_load_site();
$slug = is_string($_GET['slug'] ?? null) ? $_GET['slug'] : '';
$post = blog_find($slug);
$css = '<link rel="stylesheet" href="' . e(asset('assets/css/blog.css')) . '">';

if ($post === null) {
    http_response_code(404);
    render_partial('head', ['title' => 'Artículo no encontrado | ' . $site['profile']['name'], 'description' => 'Este artículo no existe.', 'canonical' => '/blog', 'extra_head' => $css]);
    render_partial('nav', ['site' => $site, 'actual' => 'blog']);
    render_partial('error-404', [
        'site' => $site,
        'titulo' => 'Artículo no encontrado',
        'texto' => 'Este artículo no existe o ya no está publicado.',
        'boton' => ['Ver el blog', '/blog'],
    ]);
    render_partial('footer', ['site' => $site]);
    return;
}

$migas = [['Blog', '/blog'], [$post['titulo'], blog_url($post)]];
$relacionados = blog_related($post, blog_posts());
$jsonld = [jsonld_blogposting($post, $site), jsonld_breadcrumbs($migas)];
$faqs = blog_faqs($post['cuerpo']);
if ($faqs) {
    $jsonld[] = jsonld_faq($faqs);
}
$mensaje = 'Hola Julio, leí tu artículo «' . $post['titulo'] . '» y quiero conversar sobre mi negocio.';
$wa = site_wa($site, $mensaje);

render_partial('head', [
    'title' => $post['titulo'] . ' | ' . $site['profile']['name'],
    'description' => $post['descripcion'] !== '' ? $post['descripcion'] : $post['resumen'],
    'og_image' => $post['imagen'] !== '' ? $post['imagen'] : 'assets/img/og/og-inicio.jpg',
    'canonical' => blog_url($post),
    'jsonld' => $jsonld,
    'extra_head' => $css,
]);
render_partial('nav', ['site' => $site, 'actual' => 'blog']);
?>
<main id="main" class="articulo">
  <header class="page-hd articulo-header">
    <div class="narrow page-hd__in">
      <nav class="crumbs" aria-label="Estás en">
        <a href="/">Inicio</a><span aria-hidden="true">/</span><a href="/blog">Blog</a>
      </nav>
      <h1><?= e($post['titulo']) ?></h1>
      <p class="blog-meta"><?= e(blog_fecha($post['fecha'])) ?> · <?= (int) $post['minutos'] ?> min de lectura · <?= e($site['profile']['name']) ?><?php if ($post['tema'] !== ''): ?> · <a class="blog-chip" href="<?= e(blog_tema_url($post['tema_slug'])) ?>"><?= e($post['tema']) ?></a><?php endif; ?></p>
    </div>
  </header>

  <div class="wrap articulo-ancho">
    <?php if ($post['imagen'] !== ''): ?>
      <figure class="frame articulo-imagen">
        <span class="frame__bar" aria-hidden="true"><i></i><i></i><i></i><span>j0acuna.site/blog</span></span>
        <span class="frame__img"><img src="<?= e(asset($post['imagen'])) ?>" alt="<?= e($post['imagen_alt']) ?>" width="1200" height="750"></span>
      </figure>
    <?php endif; ?>
    <div class="articulo-cuerpo">
      <?= blog_markdown($post['cuerpo']) ?>
    </div>

    <aside class="articulo-cierre" aria-label="Conversemos">
      <p class="badge"><i aria-hidden="true"></i>Propuesta sin compromiso</p>
      <strong><span class="grad">¿Quieres algo así para tu negocio?</span></strong>
      <span>Te cuento qué conviene en tu caso, sin compromiso.</span>
      <span class="articulo-botones">
        <?php if ($wa !== ''): ?>
          <a class="btn btn--light" href="<?= e($wa) ?>" target="_blank" rel="noopener"><svg aria-hidden="true"><use href="#i-wa"/></svg>Escríbeme por WhatsApp</a>
        <?php endif; ?>
        <a class="btn btn--glass" href="/servicios">Ver servicios</a>
      </span>
    </aside>
  </div>

  <?php if ($relacionados): ?>
    <section class="sec" aria-labelledby="titulo-relacionados">
      <div class="wrap">
        <div class="sec__head sec__head--row">
          <p class="kicker">Blog</p>
          <h2 class="h2 relacionados-titulo" id="titulo-relacionados">Sigue leyendo</h2>
          <a class="more" href="/blog">Todos los artículos<svg aria-hidden="true"><use href="#i-arr"/></svg></a>
        </div>
        <div class="blog-lista">
          <?php foreach ($relacionados as $p): ?>
            <a class="blog-item rv" href="<?= e(blog_url($p)) ?>">
              <?= blog_item_media($p) ?>
              <strong><?= e($p['titulo']) ?></strong>
              <span class="blog-resumen"><?= e($p['resumen']) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $mensaje]);
render_partial('footer', ['site' => $site]);
