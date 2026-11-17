<?php
require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$wa = site_wa($site, $site['profile']['whatsapp_mensaje']);

render_partial('head', [
    'title' => 'Contacto | ' . $site['profile']['name'] . ', desarrollador web en ' . $site['profile']['ciudad'],
    'description' => 'Escríbele a Julio Acuña por WhatsApp o correo. Desarrollador web en Santiago que trabaja con negocios de todo Chile.',
    'canonical' => '/contacto',
    'jsonld' => [jsonld_business($site), jsonld_breadcrumbs([['Contacto', '/contacto']])],
]);
render_partial('nav', ['site' => $site, 'actual' => 'contacto']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Contacto',
      'titulo' => 'Conversemos sobre tu negocio',
      'bajada' => $site['profile']['contact_text'],
      'migas' => [['Contacto', '/contacto']],
  ]); ?>

  <section class="section-band pt-0">
    <div class="container">
      <div class="contact-cards">
        <?php if ($wa !== ''): ?>
          <a class="contact-card" href="<?= e($wa) ?>" target="_blank" rel="noopener">
            <strong>WhatsApp</strong><span><?= e($site['links']['whatsapp']) ?></span>
          </a>
        <?php endif; ?>
        <a class="contact-card" href="mailto:<?= e($site['links']['email']) ?>">
          <strong>Correo</strong><span><?= e($site['links']['email']) ?></span>
        </a>
        <a class="contact-card" href="<?= e($site['links']['linkedin']) ?>" target="_blank" rel="noopener">
          <strong>LinkedIn</strong><span>Julio Acuña</span>
        </a>
        <a class="contact-card" href="<?= e($site['links']['github']) ?>" target="_blank" rel="noopener">
          <strong>GitHub</strong><span>Julio-0Acuna</span>
        </a>
      </div>
      <p class="contact-place"><?= e($site['profile']['ciudad']) ?>, Chile · atiendo negocios de <?= e($site['profile']['area']) ?>, también a distancia.</p>
    </div>
  </section>

  <section id="como-trabajo" class="section-band section-alt">
    <div class="container">
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
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $site['profile']['whatsapp_mensaje']]);
render_partial('footer', ['site' => $site]);
