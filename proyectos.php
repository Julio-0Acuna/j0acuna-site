<?php
require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$sections = portfolio_sections($site);
$totalProyectos = array_sum(array_column($sections, 'cantidad'));
$porFila = 4;

render_partial('head', [
    'title' => 'Portafolio de proyectos web | ' . $site['profile']['name'],
    'description' => 'Proyectos de Julio Acuña, desarrollador web en Santiago: sistemas para restaurantes, tiendas online, páginas web e inteligencia artificial.',
    'canonical' => '/proyectos',
    'jsonld' => [jsonld_breadcrumbs([['Proyectos', '/proyectos']])],
]);
render_partial('nav', ['site' => $site, 'actual' => 'proyectos']);
?>
<main id="main">
  <?php render_partial('page-header', [
      'eyebrow' => 'Portafolio',
      'titulo' => 'Portafolio de proyectos web',
      'bajada' => $totalProyectos . ' proyectos de un desarrollador web en ' . $site['profile']['ciudad'] . ', Chile: sistemas para restaurantes, tiendas online, páginas web e inteligencia artificial. Toca uno para ver cómo está hecho.',
      'migas' => [['Proyectos', '/proyectos']],
  ]); ?>

  <?php if (count($sections) > 1): ?>
    <nav class="wrap jump" aria-label="Secciones del portafolio">
      <ul class="chips">
        <?php foreach ($sections as $s): ?>
          <li><a class="chip" href="#seccion-<?= e($s['id']) ?>"><?= e($s['nombre']) ?><small><?= (int) $s['cantidad'] ?></small></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>
  <?php endif; ?>

  <div class="wrap">
    <?php foreach ($sections as $n => $s): ?>
      <?php
      $muestra = array_slice(portfolio_section_projects($site, $s['id']), 0, $porFila);
      $uno = count($muestra) === 1;
      ?>
      <section class="proj-section" id="seccion-<?= e($s['id']) ?>" aria-labelledby="titulo-<?= e($s['id']) ?>">
        <div class="proj-head rv">
          <div>
            <p class="kicker"><b><?= sprintf('%02d', $n + 1) ?></b> <?= $s['cantidad'] === 1 ? '1 proyecto' : (int) $s['cantidad'] . ' proyectos' ?></p>
            <h2 id="titulo-<?= e($s['id']) ?>"><a href="<?= e($s['url']) ?>"><?= e($s['nombre']) ?></a></h2>
            <p><?= e($s['resumen']) ?></p>
          </div>
          <a class="more" href="<?= e($s['url']) ?>">Ver <?= $s['cantidad'] === 1 ? 'el proyecto' : 'los ' . (int) $s['cantidad'] . ' proyectos' ?><svg aria-hidden="true"><use href="#i-arr"/></svg></a>
        </div>
        <div class="tiles <?= $uno ? 'tiles-1' : 'tiles-4' ?> rv">
          <?php foreach ($muestra as $p): ?>
            <?php render_partial('mosaico', [
                'url' => project_url($p),
                'imagen' => $p['image_url'],
                'titulo' => $p['title'],
                'texto' => $uno ? $p['description'] : '',
                'mas' => $uno ? 'Ver proyecto' : '',
                'clase' => $uno ? 'tile-wide' : '',
            ]); ?>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  </div>

  <?php render_partial('cta', [
      'site' => $site,
      'titulo' => '¿Quieres algo así para tu negocio?',
      'texto' => 'Cuéntame qué necesitas y te digo cómo lo haría.',
      'secundario' => ['Ver servicios', '/servicios'],
  ]); ?>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $site['profile']['whatsapp_mensaje']]);
render_partial('footer', ['site' => $site]);
