<?php
/**
 * Página 404 con el mismo lenguaje visual: código grande desvanecido, título, texto y botones.
 * Imprime todo el <main>; quien llama pone head, nav y footer.
 * @var array $site @var string $titulo @var string $texto @var array $boton [texto, ruta]
 */
?>
<main id="main" class="err">
  <header class="page-hd">
    <div class="wrap page-hd__in">
      <p class="err__code" aria-hidden="true">404</p>
      <p class="kicker">Error 404</p>
      <h1><?= e($titulo) ?></h1>
      <p class="page-lead"><?= e($texto) ?></p>
      <div class="actions">
        <a class="btn btn--light" href="<?= e($boton[1]) ?>"><?= e($boton[0]) ?><svg aria-hidden="true"><use href="#i-arr"/></svg></a>
        <a class="btn btn--glass" href="/">Ir al inicio</a>
      </div>
    </div>
  </header>
</main>
