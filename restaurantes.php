<?php
require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$mensaje = 'Hola Julio, tengo un local y quiero vender más.';
$wa = $site['links']['whatsapp'] !== '' ? wa_link($site['links']['whatsapp'], $mensaje) : '';
$servicios = portfolio_visible_services($site, 'restaurantes');
$pruebas = portfolio_restaurant_projects($site);
$hayEjemplos = in_array('ejemplos', array_column(portfolio_categories_in_use($site), 'id'), true);

$niveles = ['presencia' => 'Presencia', 'directo' => 'Vende directo', 'apps' => 'Apps de delivery', 'premium' => 'Premium'];
$porNivel = [];
foreach ($servicios as $s) {
    if (isset($niveles[$s['nivel']])) {
        $porNivel[$s['nivel']][] = $s;
    }
}

render_partial('head', [
    'title' => 'Más ventas para tu local | ' . $site['profile']['name'],
    'description' => 'Página web, menú digital con QR, pedidos por WhatsApp y apps de delivery para restaurantes y comercios en Chile.',
    'og_image' => 'assets/img/og/og-restaurantes.jpg',
    'canonical' => '/restaurantes',
]);
render_partial('nav', ['site' => $site]);
?>
<main>
  <section class="local-hero">
    <div class="container">
      <p class="eyebrow mb-3">Para restaurantes y comercios</p>
      <h1>Más ventas para tu local, sin complicarte</h1>
      <p class="lead mt-3">Página web, menú digital con QR, pedidos por WhatsApp y apoyo para vender en Uber Eats y PedidosYa. Tú atiendes tu negocio; yo me encargo de la parte digital.</p>
      <?php if ($wa !== ''): ?>
        <a class="minimal-btn minimal-btn-light mt-3" href="<?= e($wa) ?>" target="_blank" rel="noopener"><span>Conversemos por WhatsApp</span></a>
      <?php endif; ?>
    </div>
  </section>

  <?php if ($porNivel): ?>
    <section class="section-band">
      <div class="container">
        <div class="section-heading">
          <p class="eyebrow">Lo que te ofrezco</p>
          <h2>Elige por dónde empezar</h2>
        </div>
        <?php foreach ($niveles as $clave => $nombre): ?>
          <?php if (!empty($porNivel[$clave])): ?>
            <div class="level-block level-<?= e($clave) ?>">
              <h3 class="level-title"><?= e($nombre) ?></h3>
              <div class="service-grid">
                <?php foreach ($porNivel[$clave] as $service): ?>
                  <?php render_partial('service-card', ['service' => $service, 'site' => $site]); ?>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <?php if ($pruebas): ?>
    <section id="pruebas" class="section-band section-alt">
      <div class="container">
        <div class="section-heading">
          <p class="eyebrow">Ya lo hice antes</p>
          <h2>Proyectos para restaurantes y comercios</h2>
        </div>
        <div class="project-grid">
          <?php foreach ($pruebas as $project): ?>
            <?php render_partial('project-card', ['project' => $project]); ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php if ($hayEjemplos): ?>
    <section class="section-band">
      <div class="container">
        <div class="section-heading">
          <p class="eyebrow">Ejemplos por rubro</p>
          <h2>Mira cómo se vería tu negocio</h2>
        </div>
        <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Ver ejemplos</span></a>
      </div>
    </section>
  <?php endif; ?>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos?</h2>
          <p>Te cuento qué conviene para tu local, sin compromiso.</p>
        </div>
        <div class="contact-actions">
          <?php if ($wa !== ''): ?>
            <a class="minimal-btn minimal-btn-dark" href="<?= e($wa) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/"><span>Ver todos mis proyectos</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $mensaje]);
render_partial('footer', ['site' => $site]);
