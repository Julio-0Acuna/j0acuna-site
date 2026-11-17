<?php /** Portada por defecto del catálogo. Cada rubro puede reemplazarla con su propio vistas/portada.php. @var array $cfg */ ?>
<section class="t-portada">
  <h1><?= m_e($cfg['negocio']['nombre']) ?></h1>
  <p><?= m_e($cfg['negocio']['eslogan'] ?? '') ?></p>
  <a class="t-btn t-btn--primario" href="#menu">Ver catálogo</a>
</section>
