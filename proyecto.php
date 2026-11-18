<?php
require_once __DIR__ . '/includes/view.php';
require_once __DIR__ . '/includes/prism.php';

$site = portfolio_load_site();
$id = is_string($_GET['id'] ?? null) ? $_GET['id'] : '';
$p = preg_match('/^[a-z0-9-]+$/', $id) ? portfolio_find_project($site, $id) : null;

if ($p === null) {
    http_response_code(404);
    render_partial('head', [
        'title' => 'Proyecto no encontrado | ' . $site['profile']['name'],
        'description' => 'Este proyecto no existe o ya no está publicado.',
        'canonical' => '/proyectos',
    ]);
    render_partial('nav', ['site' => $site, 'actual' => 'proyectos']);
    render_partial('error-404', [
        'site' => $site,
        'titulo' => 'Proyecto no encontrado',
        'texto' => 'Este proyecto no existe o ya no está publicado.',
        'boton' => ['Ver todos los proyectos', '/proyectos'],
    ]);
    render_partial('footer', ['site' => $site]);
    return;
}

$mensaje = 'Hola Julio, vi el proyecto «' . $p['title'] . '» y quiero algo así para mi negocio.';
$wa = site_wa($site, $mensaje);
$imagenes = array_values(array_filter(array_merge([$p['image_url']], $p['galeria']), static fn ($img): bool => is_string($img) && $img !== ''));
$imagenes = array_slice(array_values(array_unique($imagenes)), 0, 4);
$meta = array_filter([
    $p['mostrar_cliente'] && $p['cliente'] !== '' ? $p['cliente'] : 'Cliente confidencial',
    $p['pais'],
    (string) $p['anio'],
    category_name($p['categoria_id']),
], static fn ($v): bool => $v !== '');
$secciones = ['El problema' => $p['problema'], 'La solución' => $p['solucion'], 'El resultado' => $p['resultado']];
$seccion = portfolio_find_section($site, $p['categoria_id']);
$migas = [['Proyectos', '/proyectos']];
if ($seccion !== null) {
    $migas[] = [$seccion['nombre'], $seccion['url']];
}
$migas[] = [$p['title'], project_url($p)];
$snippet = portfolio_snippet($p);
$n = 0;

render_partial('head', [
    'title' => $p['title'] . ' | ' . $site['profile']['name'],
    'description' => $p['description'],
    'og_image' => $imagenes[0] ?? 'assets/img/og/og-inicio.jpg',
    'canonical' => project_url($p),
    'jsonld' => [jsonld_project($p, $site), jsonld_breadcrumbs($migas)],
    'extra_head' => $snippet !== null ? prism_head() : '',
]);
render_partial('nav', ['site' => $site, 'actual' => 'proyectos']);
?>
<main id="main" class="detail-page">
  <header class="pd-hd">
    <div class="wrap pd-hd__in">
      <nav class="crumbs" aria-label="Estás en">
        <a href="/">Inicio</a>
        <?php foreach ($migas as $i => [$nombre, $ruta]): ?>
          <span aria-hidden="true">/</span>
          <?php if ($i === count($migas) - 1): ?>
            <span aria-current="page"><?= e($nombre) ?></span>
          <?php else: ?>
            <a href="<?= e($ruta) ?>"><?= e($nombre) ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </nav>
      <p class="kicker"><?= e(category_name($p['categoria_id'])) ?></p>
      <h1><?= e($p['title']) ?></h1>
      <p class="page-lead"><?= e($p['description']) ?></p>
      <ul class="pd-meta" aria-label="Datos del proyecto">
        <?php foreach ($meta as $item): ?>
          <li class="pill"><?= e($item) ?></li>
        <?php endforeach; ?>
      </ul>
      <?php if ($p['credito'] !== ''): ?>
        <p class="pd-credit"><?= e($p['credito']) ?></p>
      <?php endif; ?>
    </div>
  </header>

  <div class="wrap">
    <?php if ($imagenes): ?>
      <div class="gallery rv">
        <figure class="frame">
          <span class="frame__bar" aria-hidden="true"><i></i><i></i><i></i><span><?= e(proyecto_barra($p)) ?></span></span>
          <span class="frame__img"><img src="<?= e(asset($imagenes[0])) ?>" alt="Vista de <?= e($p['title']) ?>" width="1200" height="750"></span>
        </figure>
        <?php if (count($imagenes) > 1): ?>
          <div class="gallery__more gallery__more--<?= count($imagenes) - 1 ?>">
            <?php foreach (array_slice($imagenes, 1) as $k => $img): ?>
              <img src="<?= e(asset($img)) ?>" alt="Vista <?= $k + 2 ?> de <?= e($p['title']) ?>" loading="lazy" width="1200" height="750">
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <?php if ($p['imagen_ejemplo']): ?>
        <p class="image-note">Imagen con datos de ejemplo: el sistema real trabaja con información de clientes que no se muestra.</p>
      <?php endif; ?>
    <?php endif; ?>

    <div class="pd-grid">
      <div class="pd-main">
        <?php foreach ($secciones as $titulo => $texto): ?>
          <?php if ($texto !== ''): $n++; ?>
            <section class="pd-sec rv">
              <h2><small><?= sprintf('%02d', $n) ?></small><?= e($titulo) ?></h2>
              <p><?= e($texto) ?></p>
            </section>
          <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($p['funcionalidades']): $n++; ?>
          <section class="pd-sec rv">
            <h2><small><?= sprintf('%02d', $n) ?></small>Qué incluye</h2>
            <ul class="checks">
              <?php foreach ($p['funcionalidades'] as $f): ?>
                <li><?php render_partial('icono', ['icono' => 'check', 'tam' => 18]); ?><span><?= e($f) ?></span></li>
              <?php endforeach; ?>
            </ul>
          </section>
        <?php endif; ?>
        <?php if ($snippet !== null): ?>
          <?php render_partial('code-block', ['snippet' => $snippet, 'numero' => sprintf('%02d', $n + 1)]); ?>
        <?php endif; ?>
      </div>
      <aside class="pd-aside" aria-label="Tecnologías y enlaces">
        <?php if ($p['technologies']): ?>
          <h2>Tecnologías</h2>
          <div class="tech-list">
            <?php foreach ($p['technologies'] as $tech): ?>
              <span><?= e($tech) ?></span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <div class="pd-actions">
          <?php if ($wa !== ''): ?>
            <a class="btn btn--light" href="<?= e($wa) ?>" target="_blank" rel="noopener"><svg aria-hidden="true"><use href="#i-wa"/></svg>Quiero algo así</a>
          <?php endif; ?>
          <?php if ($p['demo_url'] !== ''): ?>
            <a class="btn btn--glass" href="<?= e($p['demo_url']) ?>" target="_blank" rel="noopener">Ver sitio<svg aria-hidden="true"><use href="#i-out"/></svg></a>
          <?php endif; ?>
          <?php if ($p['repo_url'] !== ''): ?>
            <a class="btn btn--glass" href="<?= e($p['repo_url']) ?>" target="_blank" rel="noopener">Ver código<svg aria-hidden="true"><use href="#i-out"/></svg></a>
          <?php endif; ?>
        </div>
        <?php if ($seccion !== null): ?>
          <a class="more" href="<?= e($seccion['url']) ?>"><svg aria-hidden="true"><use href="#i-back"/></svg><?= e($seccion['nombre']) ?></a>
        <?php endif; ?>
      </aside>
    </div>
  </div>

  <?php render_partial('cta', [
      'site' => $site,
      'titulo' => '¿Quieres algo así?',
      'texto' => 'Cuéntame qué necesita tu negocio y te digo cómo lo haría, sin compromiso.',
      'mensaje' => $mensaje,
      'secundario' => ['Ver más proyectos', '/proyectos'],
  ]); ?>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $mensaje]);
if ($snippet !== null) {
    echo prism_scripts($snippet['lenguaje']);
}
render_partial('footer', ['site' => $site]);
