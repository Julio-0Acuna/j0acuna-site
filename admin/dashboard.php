<?php
require __DIR__ . '/../includes/auth.php';
admin_require_login();

$site = portfolio_load_site();
$message = $_GET['saved'] ?? '' ? 'Cambios guardados.' : '';
$publishedCount = count(portfolio_visible_projects($site));
$draftCount = max(count($site['projects']) - $publishedCount, 0);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-body">
  <aside class="admin-sidebar">
    <a class="brand" href="dashboard.php"><span>J</span> Portafolio Admin</a>
    <nav>
      <a href="#perfil">Perfil</a>
      <a href="#links">Links</a>
      <a href="#skills">Skills</a>
      <a href="#proyectos">Proyectos</a>
    </nav>
    <a class="btn btn-outline-light btn-sm" href="../index.php" target="_blank" rel="noopener">Ver sitio</a>
    <a class="btn btn-light btn-sm" href="logout.php">Salir</a>
  </aside>

  <main class="admin-main">
    <header class="admin-header">
      <div>
        <p class="admin-kicker">Contenido editable</p>
        <h1>Centro de control</h1>
      </div>
      <span class="admin-user"><?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?></span>
    </header>

    <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>

    <section class="admin-stats" aria-label="Resumen del portafolio">
      <article>
        <span>Proyectos</span>
        <strong><?= count($site['projects']) ?></strong>
      </article>
      <article>
        <span>Publicados</span>
        <strong><?= $publishedCount ?></strong>
      </article>
      <article>
        <span>Ocultos</span>
        <strong><?= $draftCount ?></strong>
      </article>
      <article>
        <span>Skills</span>
        <strong><?= count($site['skills']) ?></strong>
      </article>
    </section>

    <form method="post" action="save.php" class="admin-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(admin_csrf_token()) ?>">

      <section id="perfil" class="admin-panel">
        <div class="panel-copy">
          <p class="admin-kicker">Identidad</p>
          <h2>Perfil publico</h2>
        </div>
        <div class="row g-3">
          <?php foreach ($site['profile'] as $key => $value): ?>
            <div class="col-md-6">
              <label><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $key))) ?>
                <textarea class="form-control" name="profile[<?= htmlspecialchars($key) ?>]" rows="<?= strlen($value) > 80 ? 4 : 2 ?>"><?= htmlspecialchars($value) ?></textarea>
              </label>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <section id="links" class="admin-panel">
        <div class="panel-copy">
          <p class="admin-kicker">Redes</p>
          <h2>GitHub, LinkedIn y contacto</h2>
        </div>
        <div class="row g-3">
          <?php foreach ($site['links'] as $key => $value): ?>
            <div class="col-md-4">
              <label><?= htmlspecialchars(ucfirst($key)) ?>
                <input class="form-control" name="links[<?= htmlspecialchars($key) ?>]" value="<?= htmlspecialchars($value) ?>">
              </label>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <section id="skills" class="admin-panel">
        <div class="panel-copy">
          <p class="admin-kicker">Stack</p>
          <h2>Habilidades</h2>
        </div>
        <label>Separadas por coma
          <textarea class="form-control" name="skills" rows="3"><?= htmlspecialchars(implode(', ', $site['skills'])) ?></textarea>
        </label>
      </section>

      <section id="proyectos" class="admin-panel">
        <div class="panel-title-row">
          <div class="panel-copy">
            <p class="admin-kicker">Viewer horizontal</p>
            <h2>Proyectos</h2>
          </div>
          <button class="admin-action-btn" type="button" data-add-project>+ Agregar proyecto</button>
        </div>
        <div data-projects>
          <?php foreach ($site['projects'] as $index => $project): ?>
            <article class="project-editor" data-project>
              <div class="project-editor-header">
                <div>
                  <strong><?= htmlspecialchars($project['title'] ?: 'Proyecto') ?></strong>
                  <span><?= !empty($project['published']) ? 'Publicado' : 'Oculto' ?></span>
                </div>
                <button class="danger-action" type="button" data-remove-project>Eliminar</button>
              </div>
              <?php if (!empty($project['image_url'])): ?>
                <img class="project-preview" src="../<?= htmlspecialchars($project['image_url']) ?>" alt="Preview de <?= htmlspecialchars($project['title']) ?>">
              <?php endif; ?>
              <input type="hidden" name="projects[<?= $index ?>][id]" value="<?= htmlspecialchars($project['id']) ?>">
              <div class="row g-3">
                <div class="col-md-6"><label>Titulo <input class="form-control" name="projects[<?= $index ?>][title]" value="<?= htmlspecialchars($project['title']) ?>"></label></div>
                <div class="col-md-6"><label>Categoria <input class="form-control" name="projects[<?= $index ?>][category]" value="<?= htmlspecialchars($project['category']) ?>"></label></div>
                <div class="col-12"><label>Descripcion <textarea class="form-control" name="projects[<?= $index ?>][description]" rows="3"><?= htmlspecialchars($project['description']) ?></textarea></label></div>
                <div class="col-md-6"><label>Tecnologias <input class="form-control" name="projects[<?= $index ?>][technologies]" value="<?= htmlspecialchars(implode(', ', $project['technologies'])) ?>"></label></div>
                <div class="col-md-6"><label>Imagen del viewer <input class="form-control" name="projects[<?= $index ?>][image_url]" value="<?= htmlspecialchars($project['image_url'] ?? '') ?>" placeholder="assets/img/projects/mi-proyecto.png"></label></div>
                <div class="col-md-6"><label>Demo <input class="form-control" name="projects[<?= $index ?>][demo_url]" value="<?= htmlspecialchars($project['demo_url']) ?>"></label></div>
                <div class="col-md-6"><label>Repo <input class="form-control" name="projects[<?= $index ?>][repo_url]" value="<?= htmlspecialchars($project['repo_url']) ?>"></label></div>
                <div class="col-12">
                  <label class="form-check">
                    <input class="form-check-input" type="checkbox" name="projects[<?= $index ?>][published]" value="1" <?= !empty($project['published']) ? 'checked' : '' ?>>
                    <span class="form-check-label">Publicado</span>
                  </label>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <div class="save-bar">
        <button class="btn btn-primary btn-lg" type="submit">Guardar cambios</button>
      </div>
    </form>
  </main>

  <template id="project-template">
    <article class="project-editor" data-project>
      <div class="project-editor-header">
        <strong>Proyecto nuevo</strong>
        <button class="btn btn-outline-danger btn-sm" type="button" data-remove-project>Eliminar</button>
      </div>
      <input type="hidden" data-name="id" value="">
      <div class="row g-3">
        <div class="col-md-6"><label>Titulo <input class="form-control" data-name="title"></label></div>
        <div class="col-md-6"><label>Categoria <input class="form-control" data-name="category"></label></div>
        <div class="col-12"><label>Descripcion <textarea class="form-control" data-name="description" rows="3"></textarea></label></div>
        <div class="col-md-6"><label>Tecnologias <input class="form-control" data-name="technologies" placeholder="PHP, CSS, JavaScript"></label></div>
        <div class="col-md-6"><label>Imagen del viewer <input class="form-control" data-name="image_url" placeholder="assets/img/projects/mi-proyecto.png"></label></div>
        <div class="col-md-6"><label>Demo <input class="form-control" data-name="demo_url"></label></div>
        <div class="col-md-6"><label>Repo <input class="form-control" data-name="repo_url"></label></div>
        <div class="col-12">
          <label class="form-check">
            <input class="form-check-input" type="checkbox" data-name="published" value="1" checked>
            <span class="form-check-label">Publicado</span>
          </label>
        </div>
      </div>
    </article>
  </template>

  <script src="assets/admin.js"></script>
</body>
</html>
