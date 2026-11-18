<?php /** @var array $site */ ?>
<footer class="foot">
  <div class="wrap">
    <hr class="hr">
    <div class="foot__row">
      <a class="brand" href="/"><span class="brand__dot" aria-hidden="true"></span><?= e($site['profile']['name']) ?></a>
      <nav aria-label="Pie de página">
        <ul>
          <li><a href="/servicios">Servicios</a></li>
          <li><a href="/proyectos">Proyectos</a></li>
          <li><a href="/restaurantes">Restaurantes</a></li>
          <li><a href="/blog">Blog</a></li>
          <li><a href="/contacto">Contacto</a></li>
        </ul>
      </nav>
      <ul class="foot__soc" aria-label="Redes y contacto">
        <?php if ($site['links']['whatsapp'] !== ''): ?>
          <li><a href="<?= e(wa_link($site['links']['whatsapp'], $site['profile']['whatsapp_mensaje'])) ?>" target="_blank" rel="noopener">WhatsApp</a></li>
        <?php endif; ?>
        <li><a href="mailto:<?= e($site['links']['email']) ?>">Correo</a></li>
        <li><a href="<?= e($site['links']['linkedin']) ?>" target="_blank" rel="noopener">LinkedIn</a></li>
        <li><a href="<?= e($site['links']['github']) ?>" target="_blank" rel="noopener">GitHub</a></li>
      </ul>
    </div>
    <p class="foot__big" aria-hidden="true"><?= e($site['profile']['name']) ?></p>
    <div class="foot__bot">
      <span>&copy; <?= date('Y') ?> · <?= e($site['profile']['location']) ?></span>
      <span>Desarrollo web para negocios de <?= e($site['profile']['area']) ?></span>
    </div>
  </div>
</footer>
<script src="<?= e(asset('assets/js/main.js')) ?>" defer></script>
</body>
</html>
