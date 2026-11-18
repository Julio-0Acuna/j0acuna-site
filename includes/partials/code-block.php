<?php /** @var array $snippet ['lenguaje','titulo','explicacion','codigo'] @var string $numero (opcional) */ ?>
<?php $nombres = ['php' => 'PHP', 'python' => 'Python', 'javascript' => 'JavaScript', 'java' => 'Java', 'sql' => 'SQL']; ?>
<section class="pd-sec code code-section rv">
  <h2><?php if (!empty($numero)): ?><small><?= e($numero) ?></small><?php endif; ?>Así está hecho</h2>
  <?php if ($snippet['titulo'] !== ''): ?><p class="code-title"><?= e($snippet['titulo']) ?></p><?php endif; ?>
  <?php if ($snippet['explicacion'] !== ''): ?><p><?= e($snippet['explicacion']) ?></p><?php endif; ?>
  <div class="frame frame--quiet">
    <span class="frame__bar" aria-hidden="true"><i></i><i></i><i></i><span><?= e(($nombres[$snippet['lenguaje']] ?? 'Código') . ' · fragmento real') ?></span></span>
    <pre class="code-window-real"><code class="language-<?= e($snippet['lenguaje']) ?>"><?= e($snippet['codigo']) ?></code></pre>
  </div>
</section>
