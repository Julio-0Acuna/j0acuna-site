<?php
require_once __DIR__ . '/includes/view.php';
require_once __DIR__ . '/includes/blog.php';

$site = portfolio_load_site();
$todos = blog_posts();
$temas = blog_temas();

// Filtro por tema con enlaces normales (?tema=…): funciona sin JS y el canonical sigue siendo /blog.
$temaSlug = is_string($_GET['tema'] ?? null) && isset($temas[$_GET['tema']]) ? $_GET['tema'] : '';
$temaNombre = $temaSlug !== '' ? $temas[$temaSlug] : '';
$posts = $temaSlug !== '' ? blog_por_tema($todos, $temaSlug) : $todos;
$primero = $temaSlug !== '' ? ($posts[0] ?? null) : blog_destacado($posts);
$resto = array_values(array_filter($posts, static fn (array $p): bool => $primero === null || $p['slug'] !== $primero['slug']));

render_partial('head', [
    'title' => ($temaSlug !== '' ? 'Blog: ' . $temaNombre : 'Blog: páginas web, tiendas online y sistemas para negocios') . ' | ' . $site['profile']['name'],
    'description' => $temaSlug !== ''
        ? 'Artículos del blog sobre ' . $temaNombre . ', con guías prácticas para negocios y pymes de Chile.'
        : 'Guías para negocios y pymes de Chile: creación de páginas web, tiendas online, apps, menú QR, pedidos por WhatsApp, reservas, Google y sistemas a medida.',
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
      'bajada' => 'Guías prácticas sobre páginas web, tiendas online, apps, menú QR, pedidos por WhatsApp, reservas, Google y sistemas a medida, pensadas para negocios de Chile.',
      'migas' => [['Blog', '/blog']],
      'grad' => 3,
  ]); ?>

  <section class="sec pt-0" aria-label="Artículos">
    <div class="wrap">
      <?php if ($todos): ?>
        <nav class="blog-temas" aria-label="Temas del blog">
          <a class="blog-tema<?= $temaSlug === '' ? ' is-on' : '' ?>" href="/blog"<?= $temaSlug === '' ? ' aria-current="page"' : '' ?>>Todos <span class="blog-tema-n"><?= count($todos) ?></span></a>
          <?php foreach ($temas as $slug => $nombre): $n = count(blog_por_tema($todos, $slug)); ?>
            <a class="blog-tema<?= $slug === $temaSlug ? ' is-on' : '' ?>" href="<?= e(blog_tema_url($slug)) ?>"<?= $slug === $temaSlug ? ' aria-current="page"' : '' ?>><?= e($nombre) ?><?php if ($n > 0): ?> <span class="blog-tema-n"><?= $n ?></span><?php endif; ?></a>
          <?php endforeach; ?>
        </nav>
      <?php endif; ?>

      <?php if ($primero === null): ?>
        <?php if ($temaSlug !== ''): ?>
          <p class="blog-vacio">Pronto publicaré artículos sobre este tema. Mientras tanto, puedes ver <a href="/blog">todos los artículos</a>.</p>
        <?php else: ?>
          <p class="blog-vacio">Pronto publicaré los primeros artículos.</p>
        <?php endif; ?>
      <?php else: ?>
        <?php // Sin imagen: el destacado va solo con texto a todo el ancho y el resumen en grande. ?>
        <a class="blog-principal<?= $primero['imagen'] === '' ? ' blog-principal--texto' : '' ?> rv" href="<?= e(blog_url($primero)) ?>">
          <?php if ($primero['imagen'] !== ''): ?>
            <span class="blog-principal-media">
              <img src="<?= e(asset($primero['imagen'])) ?>" alt="<?= e($primero['imagen_alt']) ?>" width="1200" height="750">
            </span>
          <?php endif; ?>
          <span class="blog-principal-texto">
            <span class="blog-nuevo"><i aria-hidden="true"></i><?= $temaSlug !== '' ? e($temaNombre) : ($primero['destacado'] ? e($primero['tema'] !== '' ? $primero['tema'] : 'Destacado') : 'Lo más nuevo') ?></span>
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
                <?= blog_item_media($p) ?>
                <span class="blog-meta"><?php if ($p['tema'] !== '' && $temaSlug === ''): ?><span class="blog-chip"><?= e($p['tema']) ?></span> <?php endif; ?><?= e(blog_fecha($p['fecha'])) ?> · <?= (int) $p['minutos'] ?> min</span>
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
