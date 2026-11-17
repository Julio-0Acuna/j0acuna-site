<?php
/** @var array $site @var string $actual (inicio|servicios|proyectos|restaurantes|contacto) */
$actual = $actual ?? '';
$items = [
    'inicio' => ['/', 'Inicio'],
    'servicios' => ['/servicios', 'Servicios'],
    'proyectos' => ['/proyectos', 'Proyectos'],
    'restaurantes' => ['/restaurantes', 'Restaurantes'],
    'contacto' => ['/contacto', 'Contacto'],
];
?>
<nav class="navbar navbar-expand-lg fixed-top portfolio-nav">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/"><?= e($site['profile']['name']) ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Abrir menú">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto">
        <?php foreach ($items as $clave => [$ruta, $nombre]): ?>
          <li class="nav-item"><a class="nav-link<?= $clave === $actual ? ' active' : '' ?>" href="<?= e($ruta) ?>"<?= $clave === $actual ? ' aria-current="page"' : '' ?>><?= e($nombre) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</nav>
