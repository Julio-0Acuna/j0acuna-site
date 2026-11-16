<?php
require __DIR__ . '/includes/data.php';

$site = portfolio_load_site();
$projects = portfolio_visible_projects($site);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($site['profile']['name']) ?> | Portafolio</title>
  <meta name="description" content="<?= htmlspecialchars($site['profile']['tagline']) ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg fixed-top portfolio-nav">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#inicio"><?= htmlspecialchars($site['profile']['name']) ?></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Abrir menu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="#proyectos">Proyectos</a></li>
          <li class="nav-item"><a class="nav-link" href="#habilidades">Habilidades</a></li>
          <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
          <li class="nav-item"><a class="nav-link" href="admin/login.php">Admin</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main>
    <section id="inicio" class="hero-section">
      <div class="container">
        <div class="row align-items-center g-5">
          <div class="col-lg-7">
            <p class="eyebrow mb-3"><?= htmlspecialchars($site['profile']['role']) ?></p>
            <h1><?= htmlspecialchars($site['profile']['headline']) ?></h1>
            <p class="lead mt-4"><?= htmlspecialchars($site['profile']['bio']) ?></p>
            <div class="hero-actions mt-4">
              <a class="minimal-btn minimal-btn-light" href="#proyectos"><span>Ver proyectos</span></a>
              <a class="minimal-btn minimal-btn-ghost" href="#contacto"><span>Contactame</span></a>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="code-window" data-tilt-card>
              <div class="window-bar">
                <span></span><span></span><span></span>
                <small>portfolio.php</small>
              </div>
              <pre><code><span class="code-muted">&lt;?php</span>
$developer = "<?= htmlspecialchars($site['profile']['name']) ?>";
$stack = ["PHP", "CSS", "JavaScript"];

if ($idea-&gt;needsWeb()) {
    return buildPortfolio(
        clean: true,
        admin: "secure",
        motion: "fluid"
    );
}</code></pre>
              <div class="terminal-line">
                <span class="status-dot"></span>
                <span>deploy-ready desde <?= htmlspecialchars($site['profile']['location']) ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="proyectos" class="section-band">
      <div class="container reveal">
        <div class="section-heading">
          <p class="eyebrow">Trabajo reciente</p>
          <h2>Proyectos desarrollados</h2>
        </div>
        <div class="project-viewer" data-project-viewer>
          <button class="viewer-btn viewer-btn-prev" type="button" data-viewer-prev aria-label="Proyecto anterior">‹</button>
          <div class="project-track" data-project-track tabindex="0" aria-label="Viewer horizontal de proyectos">
          <?php foreach ($projects as $project): ?>
              <article class="project-card reveal">
                <?php if (!empty($project['image_url'])): ?>
                  <img class="project-image" src="<?= htmlspecialchars($project['image_url']) ?>" alt="Vista conceptual de <?= htmlspecialchars($project['title']) ?>">
                <?php else: ?>
                  <div class="project-thumb">
                    <span>&lt;/&gt;</span>
                    <strong><?= htmlspecialchars(strtoupper(substr($project['title'], 0, 2))) ?></strong>
                  </div>
                <?php endif; ?>
                <div class="project-body">
                  <span class="badge text-bg-light"><?= htmlspecialchars($project['category']) ?></span>
                  <h3><?= htmlspecialchars($project['title']) ?></h3>
                  <p><?= htmlspecialchars($project['description']) ?></p>
                  <div class="tech-list">
                    <?php foreach ($project['technologies'] as $tech): ?>
                      <span><?= htmlspecialchars($tech) ?></span>
                    <?php endforeach; ?>
                  </div>
                </div>
                <div class="project-actions">
                  <?php if (!empty($project['demo_url'])): ?>
                    <a class="minimal-link" href="<?= htmlspecialchars($project['demo_url']) ?>" target="_blank" rel="noopener">Demo</a>
                  <?php endif; ?>
                  <?php if (!empty($project['repo_url'])): ?>
                    <a class="minimal-link" href="<?= htmlspecialchars($project['repo_url']) ?>" target="_blank" rel="noopener">Codigo</a>
                  <?php endif; ?>
                </div>
              </article>
          <?php endforeach; ?>
          </div>
          <button class="viewer-btn viewer-btn-next" type="button" data-viewer-next aria-label="Proyecto siguiente">›</button>
        </div>
      </div>
    </section>

    <section id="habilidades" class="skills-section">
      <div class="container reveal">
        <div class="row align-items-start g-5">
          <div class="col-lg-5">
            <p class="eyebrow">Stack</p>
            <h2>Tecnologias y enfoque</h2>
            <p><?= htmlspecialchars($site['profile']['approach']) ?></p>
          </div>
          <div class="col-lg-7">
            <div class="skills-grid">
              <?php foreach ($site['skills'] as $skill): ?>
                <span><?= htmlspecialchars($skill) ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="contacto" class="contact-section">
      <div class="container reveal">
        <div class="contact-shell">
          <div>
            <p class="eyebrow">Contacto</p>
            <h2>Conversemos sobre tu proximo proyecto</h2>
            <p><?= htmlspecialchars($site['profile']['contact_text']) ?></p>
          </div>
          <div class="contact-actions">
            <a class="minimal-btn minimal-btn-dark" href="mailto:<?= htmlspecialchars($site['links']['email']) ?>"><span>Email</span></a>
            <a class="minimal-btn minimal-btn-outline" href="<?= htmlspecialchars($site['links']['github']) ?>" target="_blank" rel="noopener"><span>GitHub</span></a>
            <a class="minimal-btn minimal-btn-outline" href="<?= htmlspecialchars($site['links']['linkedin']) ?>" target="_blank" rel="noopener"><span>LinkedIn</span></a>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="container footer-grid">
      <div>
        <strong><?= htmlspecialchars($site['profile']['name']) ?></strong>
        <span>&copy; <?= date('Y') ?> Portafolio administrable en PHP</span>
      </div>
      <nav aria-label="Enlaces del footer">
        <a href="<?= htmlspecialchars($site['links']['github']) ?>" target="_blank" rel="noopener">GitHub</a>
        <a href="<?= htmlspecialchars($site['links']['linkedin']) ?>" target="_blank" rel="noopener">LinkedIn</a>
        <a href="admin/login.php">Admin</a>
      </nav>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
