<?php /** @var array $snippet ['lenguaje','titulo','explicacion','codigo'] */ ?>
<section class="detail-section code-section">
  <h2>Así está hecho</h2>
  <?php if ($snippet['titulo'] !== ''): ?><p class="code-title"><?= e($snippet['titulo']) ?></p><?php endif; ?>
  <?php if ($snippet['explicacion'] !== ''): ?><p><?= e($snippet['explicacion']) ?></p><?php endif; ?>
  <pre class="code-window-real"><code class="language-<?= e($snippet['lenguaje']) ?>"><?= e($snippet['codigo']) ?></code></pre>
</section>
