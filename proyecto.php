<?php
require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$id = is_string($_GET['id'] ?? null) ? $_GET['id'] : '';
$p = preg_match('/^[a-z0-9-]+$/', $id) ? portfolio_find_project($site, $id) : null;

if ($p === null) {
    http_response_code(404);
    render_partial('head', [
        'title' => 'Proyecto no encontrado | ' . $site['profile']['name'],
        'description' => 'Este proyecto no existe o ya no está publicado.',
        'canonical' => '/',
    ]);
    render_partial('nav', ['site' => $site]);
    ?>
    <main class="detail-page">
      <div class="container">
        <p class="eyebrow">Error 404</p>
        <h1>Proyecto no encontrado</h1>
        <p class="detail-lead">Este proyecto no existe o ya no está publicado.</p>
        <a class="minimal-btn minimal-btn-dark" href="/"><span>Ver todos los proyectos</span></a>
      </div>
    </main>
    <?php
    render_partial('footer', ['site' => $site]);
    return;
}

$wa = $site['links']['whatsapp'] !== ''
    ? wa_link($site['links']['whatsapp'], 'Hola Julio, vi el proyecto «' . $p['title'] . '» y quiero algo así para mi negocio.')
    : '';
$imagenes = array_values(array_filter(array_merge([$p['image_url']], $p['galeria']), static fn ($img): bool => is_string($img) && $img !== ''));
$imagenes = array_slice(array_values(array_unique($imagenes)), 0, 4);
$meta = array_filter([
    $p['mostrar_cliente'] && $p['cliente'] !== '' ? $p['cliente'] : 'Cliente confidencial',
    $p['pais'],
    (string) $p['anio'],
    category_name($p['categoria_id']),
], static fn ($v): bool => $v !== '');
$secciones = ['El problema' => $p['problema'], 'La solución' => $p['solucion'], 'El resultado' => $p['resultado']];

render_partial('head', [
    'title' => $p['title'] . ' | ' . $site['profile']['name'],
    'description' => $p['description'],
    'og_image' => $imagenes[0] ?? 'assets/img/og/og-inicio.jpg',
    'canonical' => project_url($p),
]);
render_partial('nav', ['site' => $site]);
?>
<main class="detail-page">
  <div class="container">
    <a class="detail-back" href="/#proyectos">&larr; Todos los proyectos</a>
    <header class="detail-header">
      <p class="eyebrow"><?= e(category_name($p['categoria_id'])) ?></p>
      <h1><?= e($p['title']) ?></h1>
      <p class="detail-lead"><?= e($p['description']) ?></p>
      <ul class="detail-meta">
        <?php foreach ($meta as $item): ?>
          <li><?= e($item) ?></li>
        <?php endforeach; ?>
      </ul>
      <?php if ($p['credito'] !== ''): ?>
        <p class="detail-credit"><?= e($p['credito']) ?></p>
      <?php endif; ?>
    </header>

    <?php if ($imagenes): ?>
      <div class="detail-gallery detail-gallery-<?= count($imagenes) ?>">
        <?php foreach ($imagenes as $img): ?>
          <img src="<?= e(asset($img)) ?>" alt="Vista de <?= e($p['title']) ?>" loading="lazy" width="1200" height="750">
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="detail-grid">
      <div>
        <?php foreach ($secciones as $titulo => $texto): ?>
          <?php if ($texto !== ''): ?>
            <section class="detail-section">
              <h2><?= e($titulo) ?></h2>
              <p><?= e($texto) ?></p>
            </section>
          <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($p['funcionalidades']): ?>
          <section class="detail-section">
            <h2>Qué incluye</h2>
            <ul class="detail-list">
              <?php foreach ($p['funcionalidades'] as $f): ?>
                <li><?= e($f) ?></li>
              <?php endforeach; ?>
            </ul>
          </section>
        <?php endif; ?>
      </div>
      <aside class="detail-aside">
        <?php if ($p['technologies']): ?>
          <h2>Tecnologías</h2>
          <div class="tech-list">
            <?php foreach ($p['technologies'] as $tech): ?>
              <span><?= e($tech) ?></span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <div class="detail-actions">
          <?php if ($wa !== ''): ?>
            <a class="minimal-btn minimal-btn-dark" href="<?= e($wa) ?>" target="_blank" rel="noopener"><span>Quiero algo así</span></a>
          <?php endif; ?>
          <?php if ($p['demo_url'] !== ''): ?>
            <a class="minimal-btn minimal-btn-outline" href="<?= e($p['demo_url']) ?>" target="_blank" rel="noopener"><span>Ver sitio</span></a>
          <?php endif; ?>
          <?php if ($p['repo_url'] !== ''): ?>
            <a class="minimal-btn minimal-btn-outline" href="<?= e($p['repo_url']) ?>" target="_blank" rel="noopener"><span>Ver código</span></a>
          <?php endif; ?>
        </div>
      </aside>
    </div>
  </div>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, vi el proyecto «' . $p['title'] . '» y quiero algo así para mi negocio.']);
render_partial('footer', ['site' => $site]);
