<?php /** @var array $site @var string $mensaje */ ?>
<?php if ($site['links']['whatsapp'] !== ''): ?>
<a class="fab" href="<?= e(wa_link($site['links']['whatsapp'], $mensaje)) ?>" target="_blank" rel="noopener" aria-label="Escribir por WhatsApp">
  <svg aria-hidden="true"><use href="#i-wa"/></svg><span>WhatsApp</span>
</a>
<?php endif; ?>
