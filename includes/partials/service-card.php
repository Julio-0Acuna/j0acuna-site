<?php /** @var array $service @var array $site */ ?>
<article class="service-card">
  <h3><?= e($service['titulo']) ?></h3>
  <p><?= e($service['resumen']) ?></p>
  <?php if ($service['puntos']): ?>
    <ul>
      <?php foreach ($service['puntos'] as $punto): ?>
        <li><?= e($punto) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
  <?php if ($service['mostrar_precio'] && $service['precio_desde'] !== ''): ?>
    <p class="service-price">Desde <?= e($service['precio_desde']) ?></p>
  <?php endif; ?>
  <?php if ($site['links']['whatsapp'] !== ''): ?>
    <a class="minimal-btn minimal-btn-outline service-cta" href="<?= e(wa_link($site['links']['whatsapp'], 'Hola Julio, quiero cotizar: ' . $service['titulo'])) ?>" target="_blank" rel="noopener"><span>Cotizar</span></a>
  <?php endif; ?>
</article>
