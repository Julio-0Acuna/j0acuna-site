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
<main id="main">
  <?php render_partial('page-header', [
      'eyebrow' => 'Blog',
      'titulo' => 'Ideas para que tu negocio venda más en internet',
      'bajada' => 'Guías prácticas sobre páginas web, menú QR, pedidos por WhatsApp, apps de delivery y Google, pensadas para negocios de Chile.',
      'migas' => [['Blog', '/blog']],
      'grad' => 3,
  ]); ?>

  <section class="sec pt-0" aria-label="Artículos">
    <div class="wrap">
      <?php if ($primero === null): ?>
        <p class="blog-vacio">Pronto publicaré los primeros artículos.</p>
      <?php else: ?>
        <a class="blog-principal rv" href="<?= e(blog_url($primero)) ?>">
          <span class="blog-principal-media">
            <?php if ($primero['imagen'] !== ''): ?>
              <img src="<?= e(asset($primero['imagen'])) ?>" alt="<?= e($primero['imagen_alt']) ?>" width="1200" height="750">
            <?php else: ?>
              <span class="tile-placeholder" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($primero['titulo'], 0, 2))) ?></span>
            <?php endif; ?>
          </span>
          <span class="blog-principal-texto">
            <span class="blog-nuevo"><i aria-hidden="true"></i>Lo más nuevo</span>
            <span class="blog-meta"><?= e(blog_fecha($primero['fecha'])) ?> · <?= (int) $primero['minutos'] ?> min de lectura</span>
            <strong><?= e($primero['titulo']) ?></strong>
            <span class="blog-resumen"><?= e($primero['resumen']) ?></span>
            <span class="blog-leer">Leer artículo<svg aria-hidden="true"><use href="#i-arr"/></svg></span>
          </span>
        </a>
        <?php if ($resto): ?>
          <div class="blog-lista">
            <?php foreach ($resto as $p): ?>
              <a class="blog-item rv" href="<?= e(blog_url($p)) ?>">
                <span class="blog-item-media">
                  <?php if ($p['imagen'] !== ''): ?>
                    <img src="<?= e(asset($p['imagen'])) ?>" alt="<?= e($p['imagen_alt']) ?>" loading="lazy" width="1200" height="750">
                  <?php else: ?>
                    <span class="tile-placeholder" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($p['titulo'], 0, 2))) ?></span>
                  <?php endif; ?>
                </span>
                <span class="blog-meta"><?= e(blog_fecha($p['fecha'])) ?> · <?= (int) $p['minutos'] ?> min</span>
                <strong><?= e($p['titulo']) ?></strong>
                <span class="blog-resumen"><?= e($p['resumen']) ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </section>

  <?php render_partial('cta', [
      'site' => $site,
      'titulo' => '¿Lo vemos para tu negocio?',
      'texto' => 'Si un artículo te hizo sentido, te cuento cómo aplicarlo en tu caso, sin compromiso.',
      'secundario' => ['Ver servicios', '/servicios'],
  ]); ?>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $site['profile']['whatsapp_mensaje']]);
render_partial('footer', ['site' => $site]);
