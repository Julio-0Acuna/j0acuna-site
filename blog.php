<?php
require_once __DIR__ . '/includes/view.php';
require_once __DIR__ . '/includes/blog.php';

$site = portfolio_load_site();
$posts = blog_posts();
$primero = $posts[0] ?? null;
$resto = array_slice($posts, 1);

render_partial('head', [
    'title' => 'Blog: páginas web y ventas para negocios en Chile | ' . $site['profile']['name'],
    'description' => 'Guías prácticas para restaurantes, comercios y pymes de Chile: páginas web, menú QR, pedidos por WhatsApp, apps de delivery y Google.',
    'canonical' => '/blog',
    'og_image' => $primero !== null && $primero['imagen'] !== '' ? $primero['imagen'] : 'assets/img/og/og-inicio.jpg',
    'jsonld' => [jsonld_breadcrumbs([['Blog', '/blog']])],
    'extra_head' => '<link rel="stylesheet" href="' . e(asset('assets/css/blog.css')) . '">',
]);
render_partial('nav', ['site' => $site, 'actual' => 'blog']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Blog',
      'titulo' => 'Ideas para que tu negocio venda más en internet',
      'bajada' => 'Guías prácticas sobre páginas web, menú QR, pedidos por WhatsApp, apps de delivery y Google, pensadas para negocios de Chile.',
      'migas' => [['Blog', '/blog']],
  ]); ?>

  <section class="section-band pt-0">
    <div class="container">
      <?php if ($primero === null): ?>
        <p class="blog-vacio">Pronto publicaré los primeros artículos.</p>
      <?php else: ?>
        <a class="blog-principal" href="<?= e(blog_url($primero)) ?>">
          <?php if ($primero['imagen'] !== ''): ?>
            <img src="<?= e(asset($primero['imagen'])) ?>" alt="<?= e($primero['imagen_alt']) ?>" width="1200" height="750">
          <?php endif; ?>
          <span class="blog-principal-texto">
            <span class="blog-meta"><?= e(blog_fecha($primero['fecha'])) ?> · <?= (int) $primero['minutos'] ?> min de lectura</span>
            <strong><?= e($primero['titulo']) ?></strong>
            <span><?= e($primero['resumen']) ?></span>
            <span class="blog-leer">Leer artículo →</span>
          </span>
        </a>
        <?php if ($resto): ?>
          <div class="blog-lista">
            <?php foreach ($resto as $p): ?>
              <a class="blog-item" href="<?= e(blog_url($p)) ?>">
                <?php if ($p['imagen'] !== ''): ?>
                  <img src="<?= e(asset($p['imagen'])) ?>" alt="<?= e($p['imagen_alt']) ?>" loading="lazy" width="1200" height="750">
                <?php endif; ?>
                <span class="blog-meta"><?= e(blog_fecha($p['fecha'])) ?> · <?= (int) $p['minutos'] ?> min</span>
                <strong><?= e($p['titulo']) ?></strong>
                <span><?= e($p['resumen']) ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $site['profile']['whatsapp_mensaje']]);
render_partial('footer', ['site' => $site]);
