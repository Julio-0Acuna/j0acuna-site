<?php
require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$projects = portfolio_visible_projects($site);
$featured = portfolio_featured_projects($site);
$categories = portfolio_categories_in_use($site);
$services = portfolio_visible_services($site, 'todos');
$mosaic = array_slice(array_values(array_filter($featured, static fn (array $p): bool => $p['image_url'] !== '')), 0, 3);
$waGeneral = $site['links']['whatsapp'] !== '' ? wa_link($site['links']['whatsapp'], $site['profile']['whatsapp_mensaje']) : '';

render_partial('head', [
    'title' => $site['profile']['name'] . ' | Páginas web y sistemas para negocios',
    'description' => $site['profile']['tagline'],
    'og_image' => 'assets/img/og/og-inicio.jpg',
    'canonical' => '/',
]);
render_partial('nav', ['site' => $site]);
?>
<main>
  <section id="inicio" class="hero-section">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="<?= $mosaic ? 'col-lg-7' : 'col-lg-9' ?>">
          <p class="eyebrow mb-3"><?= e($site['profile']['role']) ?></p>
          <h1><?= e($site['profile']['headline']) ?></h1>
          <p class="lead mt-4"><?= e($site['profile']['bio']) ?></p>
          <div class="hero-actions mt-4">
            <?php if ($waGeneral !== ''): ?>
              <a class="minimal-btn minimal-btn-light" href="<?= e($waGeneral) ?>" target="_blank" rel="noopener"><span>Escríbeme por WhatsApp</span></a>
            <?php endif; ?>
            <a class="minimal-btn minimal-btn-ghost" href="#proyectos"><span>Ver proyectos</span></a>
          </div>
        </div>
        <?php if ($mosaic): ?>
          <div class="col-lg-5">
            <div class="hero-mosaic hero-mosaic-<?= count($mosaic) ?>">
              <?php foreach ($mosaic as $p): ?>
                <a href="<?= e(project_url($p)) ?>"><img src="<?= e(asset($p['image_url'])) ?>" alt="<?= e($p['title']) ?>" width="1200" height="750"></a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <?php if ($services): ?>
    <section id="servicios" class="section-band">
      <div class="container reveal">
        <div class="section-heading">
          <p class="eyebrow">Servicios</p>
          <h2>Qué puedo hacer por tu negocio</h2>
        </div>
        <div class="service-grid">
          <?php foreach ($services as $service): ?>
            <?php render_partial('service-card', ['service' => $service, 'site' => $site]); ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php if ($featured): ?>
    <section id="destacados" class="section-band section-alt">
      <div class="container reveal">
        <div class="section-heading">
          <p class="eyebrow">Destacados</p>
          <h2>Proyectos que hablan por mí</h2>
        </div>
        <div class="project-grid project-grid-featured">
          <?php foreach ($featured as $project): ?>
            <?php render_partial('project-card', ['project' => $project]); ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section id="proyectos" class="section-band">
    <div class="container reveal">
      <div class="section-heading">
        <p class="eyebrow">Trabajo realizado</p>
        <h2>Todos los proyectos</h2>
      </div>
      <?php if (count($categories) > 1): ?>
        <div class="filter-bar" role="group" aria-label="Filtrar proyectos por categoría">
          <button type="button" class="filter-chip is-active" data-filtro="todos" aria-pressed="true">Todos</button>
          <?php foreach ($categories as $category): ?>
            <button type="button" class="filter-chip" data-filtro="<?= e($category['id']) ?>" aria-pressed="false"><?= e($category['nombre']) ?></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <div class="project-grid" data-project-grid>
        <?php foreach ($projects as $project): ?>
          <?php render_partial('project-card', ['project' => $project]); ?>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="como-trabajo" class="section-band section-alt">
    <div class="container reveal">
      <div class="section-heading">
        <p class="eyebrow">Cómo trabajo</p>
        <h2>Simple, en tres pasos</h2>
      </div>
      <ol class="steps">
        <li><strong>Conversamos</strong><span>Me cuentas cómo funciona tu negocio y qué necesitas.</span></li>
        <li><strong>Te envío una propuesta</strong><span>Qué se hace, cuánto demora y cuánto cuesta, por escrito.</span></li>
        <li><strong>Lo publico y te enseño a usarlo</strong><span>Queda en línea y sabes manejarlo desde el primer día.</span></li>
      </ol>
    </div>
  </section>

  <section id="contacto" class="contact-section">
    <div class="container reveal">
      <div class="contact-shell">
        <div>
          <p class="eyebrow">Contacto</p>
          <h2>Conversemos sobre tu próximo proyecto</h2>
          <p><?= e($site['profile']['contact_text']) ?></p>
        </div>
        <div class="contact-actions">
          <?php if ($waGeneral !== ''): ?>
            <a class="minimal-btn minimal-btn-dark" href="<?= e($waGeneral) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="mailto:<?= e($site['links']['email']) ?>"><span>Correo</span></a>
          <a class="minimal-btn minimal-btn-outline" href="<?= e($site['links']['linkedin']) ?>" target="_blank" rel="noopener"><span>LinkedIn</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $site['profile']['whatsapp_mensaje']]);
render_partial('footer', ['site' => $site]);
