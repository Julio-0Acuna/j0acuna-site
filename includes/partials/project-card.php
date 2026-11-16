<?php /** @var array $project */ ?>
<article class="project-card" data-categoria="<?= e($project['categoria_id']) ?>">
  <a class="project-card-link" href="<?= e(project_url($project)) ?>" aria-label="Ver <?= e($project['title']) ?>">
    <?php if ($project['image_url'] !== ''): ?>
      <img class="project-image" src="<?= e(asset($project['image_url'])) ?>" alt="Vista de <?= e($project['title']) ?>" loading="lazy" width="1200" height="750">
    <?php else: ?>
      <div class="project-thumb" aria-hidden="true">
        <span>&lt;/&gt;</span>
        <strong><?= e(mb_strtoupper(mb_substr($project['title'], 0, 2))) ?></strong>
      </div>
    <?php endif; ?>
  </a>
  <div class="project-body">
    <span class="badge text-bg-light"><?= e(category_name($project['categoria_id'])) ?></span>
    <h3><a href="<?= e(project_url($project)) ?>"><?= e($project['title']) ?></a></h3>
    <p><?= e($project['description']) ?></p>
    <?php if ($project['technologies']): ?>
      <div class="tech-list">
        <?php foreach ($project['technologies'] as $tech): ?>
          <span><?= e($tech) ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
  <div class="project-actions">
    <a class="minimal-link" href="<?= e(project_url($project)) ?>">Ver proyecto</a>
  </div>
</article>
