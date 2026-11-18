<?php
/**
 * Encabezado de página interna: migas, antetítulo, H1 (últimas palabras en degradado) y bajada.
 * @var string $eyebrow @var string $titulo @var string $bajada @var array $migas [[nombre, ruta], ...] sin el inicio
 * @var int $grad (opcional, palabras finales en degradado; 0 = ninguna; por defecto 2)
 */
$migas = $migas ?? [];
?>
<header class="page-hd page-header">
  <div class="wrap page-hd__in">
    <?php if ($migas): ?>
      <nav class="crumbs" aria-label="Estás en">
        <a href="/">Inicio</a>
        <?php foreach ($migas as $i => [$nombre, $ruta]): ?>
          <span aria-hidden="true">/</span>
          <?php if ($i === count($migas) - 1): ?>
            <span aria-current="page"><?= e($nombre) ?></span>
          <?php else: ?>
            <a href="<?= e($ruta) ?>"><?= e($nombre) ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </nav>
    <?php endif; ?>
    <?php if (!empty($eyebrow)): ?><p class="kicker eyebrow"><?= e($eyebrow) ?></p><?php endif; ?>
    <h1><?= titulo_grad($titulo, $grad ?? 2) ?></h1>
    <?php if (!empty($bajada)): ?><p class="page-lead"><?= e($bajada) ?></p><?php endif; ?>
  </div>
</header>
