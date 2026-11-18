<?php
/** "Cómo trabajo" en tres pasos, con línea en degradado. @var string $titulo_id (opcional) @var string $numero (opcional, ej. "04") */
$titulo_id = $titulo_id ?? 'titulo-pasos';
?>
<section class="sec" id="como-trabajo" aria-labelledby="<?= e($titulo_id) ?>">
  <div class="wrap">
    <div class="sec__head rv">
      <p class="kicker"><?php if (!empty($numero)): ?><b><?= e($numero) ?></b> <?php endif; ?>Cómo trabajo</p>
      <h2 class="h2" id="<?= e($titulo_id) ?>">Simple, claro <span class="soft">y sin letra chica.</span></h2>
    </div>
    <ol class="steps">
      <li class="step rv"><small>PASO 01</small><h3>Conversamos</h3><p>Me cuentas cómo funciona tu negocio y qué necesitas.</p></li>
      <li class="step rv"><small>PASO 02</small><h3>Te propongo</h3><p>Una propuesta clara y por escrito: qué incluye, plazo y precio.</p></li>
      <li class="step rv"><small>PASO 03</small><h3>Lo dejo andando</h3><p>Lo publico, te enseño a usarlo y quedo de soporte.</p></li>
    </ol>
  </div>
</section>
