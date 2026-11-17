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
    render_partial('page-header', ['eyebrow' => 'Error 404', 'titulo' => 'Artículo no encontrado', 'bajada' => 'Este artículo no existe o ya no está publicado.']);
    echo '<main class="section-band pt-0"><div class="container"><a class="minimal-btn minimal-btn-dark" href="/blog"><span>Ver el blog</span></a></div></main>';
    render_partial('footer', ['site' => $site]);
    return;
}

$migas = [['Blog', '/blog'], [$post['titulo'], blog_url($post)]];
$relacionados = blog_related($post, blog_posts());
$wa = site_wa($site, 'Hola Julio, leí tu artículo «' . $post['titulo'] . '» y quiero conversar sobre mi negocio.');

render_partial('head', [
    'title' => $post['titulo'] . ' | ' . $site['profile']['name'],
    'description' => $post['descripcion'] !== '' ? $post['descripcion'] : $post['resumen'],
    'og_image' => $post['imagen'] !== '' ? $post['imagen'] : 'assets/img/og/og-inicio.jpg',
    'canonical' => blog_url($post),
    'jsonld' => [jsonld_blogposting($post, $site), jsonld_breadcrumbs($migas)],
    'extra_head' => $css,
]);
render_partial('nav', ['site' => $site, 'actual' => 'blog']);
?>
<main class="articulo">
  <header class="page-header articulo-header">
    <div class="container articulo-ancho">
      <nav class="breadcrumbs" aria-label="Estás en">
        <a href="/">Inicio</a><span aria-hidden="true">›</span><a href="/blog">Blog</a>
      </nav>
      <h1><?= e($post['titulo']) ?></h1>
      <p class="blog-meta"><?= e(blog_fecha($post['fecha'])) ?> · <?= (int) $post['minutos'] ?> min de lectura · <?= e($site['profile']['name']) ?></p>
    </div>
  </header>

  <div class="container articulo-ancho">
    <?php if ($post['imagen'] !== ''): ?>
      <img class="articulo-imagen" src="<?= e(asset($post['imagen'])) ?>" alt="<?= e($post['imagen_alt']) ?>" width="1200" height="750">
    <?php endif; ?>
    <div class="articulo-cuerpo">
      <?= blog_markdown($post['cuerpo']) ?>
    </div>

    <aside class="articulo-cierre">
      <strong>¿Quieres algo así para tu negocio?</strong>
      <span>Te cuento qué conviene en tu caso, sin compromiso.</span>
      <span class="articulo-botones">
        <?php if ($wa !== ''): ?>
          <a class="minimal-btn minimal-btn-light" href="<?= e($wa) ?>" target="_blank" rel="noopener"><span>Escríbeme por WhatsApp</span></a>
        <?php endif; ?>
        <a class="minimal-btn minimal-btn-ghost" href="/servicios"><span>Ver servicios</span></a>
      </span>
    </aside>
  </div>

  <?php if ($relacionados): ?>
    <section class="section-band">
      <div class="container">
        <h2 class="relacionados-titulo">Sigue leyendo</h2>
        <div class="blog-lista">
          <?php foreach ($relacionados as $p): ?>
            <a class="blog-item" href="<?= e(blog_url($p)) ?>">
              <?php if ($p['imagen'] !== ''): ?>
                <img src="<?= e(asset($p['imagen'])) ?>" alt="<?= e($p['imagen_alt']) ?>" loading="lazy" width="1200" height="750">
              <?php endif; ?>
              <strong><?= e($p['titulo']) ?></strong>
              <span><?= e($p['resumen']) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, leí tu artículo «' . $post['titulo'] . '» y quiero conversar sobre mi negocio.']);
render_partial('footer', ['site' => $site]);
