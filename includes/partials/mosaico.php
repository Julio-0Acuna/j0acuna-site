<?php
/**
 * Mosaico: imagen en un marco con brillo y el título debajo. Todo el mosaico es un enlace.
 * Si existe una miniatura en assets/img/mini/ (misma ruta, sin "assets/img/"), se usa esa.
 * @var string $url @var string $imagen @var string $titulo
 * @var string $texto (opcional) @var string $etiqueta (opcional, línea chica sobre el título)
 * @var string $mas (opcional, ej. "Ver ejemplo") @var string $nivel (opcional, h2|h3, por defecto h3)
 * @var bool $externo (opcional, abre en otra pestaña) @var string $clase (opcional, ej. tile-wide)
 * @var string $alt (opcional, texto alternativo de la imagen)
 */
$texto = $texto ?? '';
$etiqueta = $etiqueta ?? '';
$mas = trim(preg_replace('/\s*→\s*$/u', '', $mas ?? ''));
$nivel = in_array($nivel ?? 'h3', ['h2', 'h3', 'h4'], true) ? ($nivel ?? 'h3') : 'h3';
$src = $imagen !== '' ? imagen_mini($imagen) : '';
?>
<a class="tile<?= !empty($clase) ? ' ' . e($clase) : '' ?>" href="<?= e($url) ?>"<?= !empty($externo) ? ' target="_blank" rel="noopener"' : '' ?>>
  <span class="tile-media">
    <?php if ($src !== ''): ?>
      <img src="<?= e(preg_match('#^https?://#i', $src) ? $src : asset($src)) ?>" alt="<?= e($alt ?? 'Pantalla de ' . $titulo) ?>" loading="lazy" width="600" height="375">
    <?php else: ?>
      <span class="tile-placeholder" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($titulo, 0, 2))) ?></span>
    <?php endif; ?>
  </span>
  <div class="tile-body">
    <?php if ($etiqueta !== ''): ?><span class="tile-tag"><?= e($etiqueta) ?></span><?php endif; ?>
    <<?= $nivel ?> class="tile-title"><?= e($titulo) ?></<?= $nivel ?>>
    <?php if ($texto !== ''): ?><span class="tile-text"><?= e($texto) ?></span><?php endif; ?>
    <?php if ($mas !== ''): ?><span class="tile-more"><?= e($mas) ?><svg aria-hidden="true"><use href="#i-arr"/></svg></span><?php endif; ?>
  </div>
</a>
