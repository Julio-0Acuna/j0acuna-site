<?php
require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$services = portfolio_visible_services($site, 'todos');
$faqs = $site['faqs'];
$wa = site_wa($site, $site['profile']['whatsapp_mensaje']);

$jsonld = [jsonld_breadcrumbs([['Servicios', '/servicios']])];
foreach ($services as $s) {
    $jsonld[] = jsonld_service($s, $site);
}
if ($faqs) {
    $jsonld[] = jsonld_faq($faqs);
}

render_partial('head', [
    'title' => 'Páginas web y sistemas para negocios en Chile | ' . $site['profile']['name'],
    'description' => 'Página web, menú digital con QR, pedidos por WhatsApp, tienda online, apps de delivery y sistemas a medida para negocios de Santiago y todo Chile.',
    'canonical' => '/servicios',
    'jsonld' => $jsonld,
]);
render_partial('nav', ['site' => $site, 'actual' => 'servicios']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Servicios',
      'titulo' => 'Páginas web y sistemas para negocios en Chile',
      'bajada' => 'Trabajo desde ' . $site['profile']['ciudad'] . ' con negocios de ' . $site['profile']['area'] . '. Elige lo que necesitas y te envío una propuesta clara.',
      'migas' => [['Servicios', '/servicios']],
  ]); ?>

  <section class="section-band pt-0">
    <div class="container">
      <div class="service-grid">
        <?php foreach ($services as $service): ?>
          <?php render_partial('service-card', ['service' => $service, 'site' => $site]); ?>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php if ($faqs): ?>
    <section id="preguntas" class="section-band section-alt">
      <div class="container">
        <div class="section-heading">
          <p class="eyebrow">Preguntas frecuentes</p>
          <h2>Lo que más me preguntan</h2>
        </div>
        <div class="faq-list">
          <?php foreach ($faqs as $f): ?>
            <details class="faq-item">
              <summary><?= e($f['pregunta']) ?></summary>
              <p><?= e($f['respuesta']) ?></p>
            </details>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Tienes otra idea?</h2>
          <p>Cuéntame qué necesita tu negocio y lo vemos juntos.</p>
        </div>
        <div class="contact-actions">
          <?php if ($wa !== ''): ?>
            <a class="minimal-btn minimal-btn-dark" href="<?= e($wa) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/proyectos"><span>Ver proyectos</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $site['profile']['whatsapp_mensaje']]);
render_partial('footer', ['site' => $site]);
