<?php /** @var array $site */ ?>
<footer class="site-footer">
  <div class="container footer-grid">
    <div>
      <strong><?= e($site['profile']['name']) ?></strong>
      <span>&copy; <?= date('Y') ?> · <?= e($site['profile']['location']) ?></span>
    </div>
    <nav aria-label="Enlaces del pie de página">
      <?php if ($site['links']['whatsapp'] !== ''): ?>
        <a href="<?= e(wa_link($site['links']['whatsapp'], $site['profile']['whatsapp_mensaje'])) ?>" target="_blank" rel="noopener">WhatsApp</a>
      <?php endif; ?>
      <a href="mailto:<?= e($site['links']['email']) ?>">Correo</a>
      <a href="<?= e($site['links']['linkedin']) ?>" target="_blank" rel="noopener">LinkedIn</a>
      <a href="<?= e($site['links']['github']) ?>" target="_blank" rel="noopener">GitHub</a>
    </nav>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="<?= e(asset('assets/js/main.js')) ?>"></script>
</body>
</html>
