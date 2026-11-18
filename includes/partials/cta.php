<?php
/**
 * Cierre de página: brillo, insignia, título grande en degradado, texto y botones.
 * @var array $site @var string $titulo @var string $texto @var string $mensaje (WhatsApp)
 * @var string $insignia (opcional) @var array $secundario [texto, ruta] (opcional)
 * @var bool $grande (opcional, título gigante como en la portada; por defecto si el título es corto)
 */
$wa = site_wa($site, $mensaje ?? $site['profile']['whatsapp_mensaje']);
$insignia = $insignia ?? 'Propuesta sin compromiso';
$grande = $grande ?? mb_strlen($titulo) <= 16;
$idCta = 'cta-' . substr(md5($titulo), 0, 6);
?>
<section class="cta<?= $grande ? '' : ' cta--md' ?>" aria-labelledby="<?= $idCta ?>">
  <div class="wrap cta__in">
    <?php if ($insignia !== ''): ?><p class="badge rv"><i aria-hidden="true"></i><?= e($insignia) ?></p><?php endif; ?>
    <h2 class="rv" id="<?= $idCta ?>"><span class="grad"><?= e($titulo) ?></span></h2>
    <?php if (!empty($texto)): ?><p class="rv"><?= e($texto) ?></p><?php endif; ?>
    <div class="actions rv">
      <?php if ($wa !== ''): ?>
        <a class="btn btn--light" href="<?= e($wa) ?>" target="_blank" rel="noopener"><svg aria-hidden="true"><use href="#i-wa"/></svg>Escríbeme por WhatsApp</a>
      <?php endif; ?>
      <?php if (!empty($secundario)): ?>
        <a class="btn btn--glass" href="<?= e($secundario[1]) ?>"><?= e($secundario[0]) ?></a>
      <?php endif; ?>
    </div>
    <?php if (!empty($nota)): ?><small class="rv"><?= $nota ?></small><?php endif; ?>
  </div>
</section>
