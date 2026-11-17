<?php
/**
 * Una diapositiva del carrusel de /servicios: imagen de referencia, para quién, qué incluye,
 * tecnologías, enlace al ejemplo y botón "Cotizar" por WhatsApp.
 * @var array $service @var array $site @var int $i (desde 0) @var int $total
 */
$numero = $i + 1;
$wa = site_wa($site, 'Hola Julio, quiero cotizar: ' . $service['titulo']);
$ejemploExterno = (bool) preg_match('#^https?://#i', $service['ejemplo_url']);
?>
<div class="carousel-item<?= $i === 0 ? ' active' : '' ?>" id="<?= e($service['id']) ?>" role="group" aria-roledescription="diapositiva" aria-label="<?= $numero ?> de <?= (int) $total ?>: <?= e($service['titulo']) ?>">
  <article class="srv-slide">
    <figure class="srv-slide-media">
      <?php if ($service['imagen'] !== ''): ?>
        <img src="<?= e(asset($service['imagen'])) ?>" alt="Ejemplo de referencia: <?= e($service['titulo']) ?>" width="1200" height="750"<?= $i === 0 ? '' : ' loading="lazy"' ?>>
      <?php else: ?>
        <span class="tile-placeholder" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($service['titulo'], 0, 2))) ?></span>
      <?php endif; ?>
    </figure>
    <div class="srv-slide-text">
      <p class="srv-slide-num"><?= sprintf('%02d', $numero) ?> <span>/ <?= sprintf('%02d', (int) $total) ?></span></p>
      <h3><?= e($service['titulo']) ?></h3>
      <p class="srv-slide-summary"><?= e($service['resumen']) ?></p>
      <?php if ($service['para_quien'] !== ''): ?>
        <p class="srv-slide-for"><strong>Para quién:</strong> <?= e($service['para_quien']) ?></p>
      <?php endif; ?>
      <?php if ($service['puntos']): ?>
        <p class="srv-label">Qué incluye</p>
        <ul class="srv-checks">
          <?php foreach ($service['puntos'] as $punto): ?>
            <li><?php render_partial('icono', ['icono' => 'check', 'tam' => 18]); ?><span><?= e($punto) ?></span></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <?php if ($service['tecnologias']): ?>
        <p class="srv-label">Tecnologías y herramientas</p>
        <ul class="tech-chips">
          <?php foreach ($service['tecnologias'] as $tech): ?>
            <li><?= e($tech) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <?php if ($service['mostrar_precio'] && $service['precio_desde'] !== ''): ?>
        <p class="service-price">Desde <?= e($service['precio_desde']) ?></p>
      <?php endif; ?>
      <div class="srv-slide-actions">
        <?php if ($wa !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e($wa) ?>" target="_blank" rel="noopener"><span>Cotizar</span></a>
        <?php endif; ?>
        <?php if ($service['ejemplo_url'] !== ''): ?>
          <a class="srv-example" href="<?= e($service['ejemplo_url']) ?>"<?= $ejemploExterno ? ' target="_blank" rel="noopener"' : '' ?>><?= e($service['ejemplo_texto'] !== '' ? $service['ejemplo_texto'] : 'Ver un ejemplo') ?> <span aria-hidden="true">→</span></a>
        <?php endif; ?>
      </div>
    </div>
  </article>
</div>
