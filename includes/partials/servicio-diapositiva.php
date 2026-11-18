<?php
/**
 * Una diapositiva del carrusel de /servicios: captura de referencia en marco de navegador,
 * para quién, qué incluye, tecnologías, enlace al ejemplo y botón "Cotizar" por WhatsApp.
 * @var array $service @var array $site @var int $i (desde 0) @var int $total
 */
$numero = $i + 1;
$wa = site_wa($site, 'Hola Julio, quiero cotizar: ' . $service['titulo']);
$ejemploExterno = (bool) preg_match('#^https?://#i', $service['ejemplo_url']);
$barra = 'j0acuna.site';
if ($service['ejemplo_url'] !== '') {
    $barra = $ejemploExterno
        ? (string) parse_url($service['ejemplo_url'], PHP_URL_HOST)
        : 'j0acuna.site' . rtrim((string) parse_url($service['ejemplo_url'], PHP_URL_PATH), '/');
}
?>
<article class="srv__slide<?= $i === 0 ? ' is-on' : '' ?>" id="<?= e($service['id']) ?>" data-slide<?= $total > 1 ? ' role="tabpanel" aria-labelledby="tab-' . e($service['id']) . '"' : '' ?>>
  <figure class="srv__media frame">
    <span class="frame__bar" aria-hidden="true"><i></i><i></i><i></i><span><?= e($barra) ?></span></span>
    <span class="frame__img">
      <?php if ($service['imagen'] !== ''): ?>
        <img src="<?= e(asset($service['imagen'])) ?>" alt="Ejemplo de referencia: <?= e($service['titulo']) ?>" width="1200" height="750"<?= $i === 0 ? '' : ' loading="lazy"' ?>>
      <?php else: ?>
        <span class="frame__ph" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($service['titulo'], 0, 2))) ?></span>
      <?php endif; ?>
    </span>
  </figure>
  <div class="srv__text">
    <p class="srv__num"><?= sprintf('%02d', $numero) ?> <span>/ <?= sprintf('%02d', (int) $total) ?></span></p>
    <h3><?= e($service['titulo']) ?></h3>
    <p class="srv__sum"><?= e($service['resumen']) ?></p>
    <?php if ($service['para_quien'] !== ''): ?>
      <p class="srv__for"><strong>Para quién:</strong> <?= e($service['para_quien']) ?></p>
    <?php endif; ?>
    <?php if ($service['puntos']): ?>
      <p class="label">Qué incluye</p>
      <ul class="checks">
        <?php foreach ($service['puntos'] as $punto): ?>
          <li><?php render_partial('icono', ['icono' => 'check', 'tam' => 18]); ?><span><?= e($punto) ?></span></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <?php if ($service['tecnologias']): ?>
      <p class="label">Tecnologías y herramientas</p>
      <ul class="tech-chips">
        <?php foreach ($service['tecnologias'] as $tech): ?>
          <li><?= e($tech) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <?php if ($service['mostrar_precio'] && $service['precio_desde'] !== ''): ?>
      <p class="service-price">Desde <?= e($service['precio_desde']) ?></p>
    <?php endif; ?>
    <div class="srv__actions">
      <?php if ($wa !== ''): ?>
        <a class="btn btn--light" href="<?= e($wa) ?>" target="_blank" rel="noopener"><svg aria-hidden="true"><use href="#i-wa"/></svg>Cotizar</a>
      <?php endif; ?>
      <?php if ($service['ejemplo_url'] !== ''): ?>
        <a class="more" href="<?= e($service['ejemplo_url']) ?>"<?= $ejemploExterno ? ' target="_blank" rel="noopener"' : '' ?>><?= e($service['ejemplo_texto'] !== '' ? $service['ejemplo_texto'] : 'Ver un ejemplo') ?><svg aria-hidden="true"><use href="#i-arr"/></svg></a>
      <?php endif; ?>
    </div>
  </div>
</article>
