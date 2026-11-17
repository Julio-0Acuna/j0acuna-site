<?php
/** @var string $eyebrow @var string $titulo @var string $bajada @var array $migas [[nombre, ruta], ...] sin el inicio */
$migas = $migas ?? [];
?>
<header class="page-header">
  <div class="container">
    <?php if ($migas): ?>
      <nav class="breadcrumbs" aria-label="Estás en">
        <a href="/">Inicio</a>
        <?php foreach ($migas as $i => [$nombre, $ruta]): ?>
          <span aria-hidden="true">›</span>
          <?php if ($i === count($migas) - 1): ?>
            <span aria-current="page"><?= e($nombre) ?></span>
          <?php else: ?>
            <a href="<?= e($ruta) ?>"><?= e($nombre) ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </nav>
    <?php endif; ?>
    <?php if (!empty($eyebrow)): ?><p class="eyebrow"><?= e($eyebrow) ?></p><?php endif; ?>
    <h1><?= e($titulo) ?></h1>
    <?php if (!empty($bajada)): ?><p class="page-lead"><?= e($bajada) ?></p><?php endif; ?>
  </div>
</header>
