<?php /** @var array $site */ ?>
<nav class="navbar navbar-expand-lg fixed-top portfolio-nav">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/"><?= e($site['profile']['name']) ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Abrir menú">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="/#servicios">Servicios</a></li>
        <li class="nav-item"><a class="nav-link" href="/#proyectos">Proyectos</a></li>
        <li class="nav-item"><a class="nav-link" href="/restaurantes">Restaurantes</a></li>
        <li class="nav-item"><a class="nav-link" href="/#contacto">Contacto</a></li>
      </ul>
    </div>
  </div>
</nav>
