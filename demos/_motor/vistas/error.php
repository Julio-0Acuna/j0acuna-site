<?php /** @var string $titulo @var string $mensaje */ ?>
<section class="t-pagina t-error-pagina">
  <h1><?= m_e($titulo) ?></h1>
  <p><?= m_e($mensaje) ?></p>
  <p class="t-acciones"><a class="t-btn t-btn--primario" href="<?= m_e(m_config()['url_base']) ?>">Volver a la tienda</a></p>
</section>
