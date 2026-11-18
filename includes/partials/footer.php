<?php
/** @var array $site */
$redes = [];
if ($site['links']['whatsapp'] !== '') {
    $redes[] = ['WhatsApp', wa_link($site['links']['whatsapp'], $site['profile']['whatsapp_mensaje']), 'i-wa', true];
}
if ($site['links']['email'] !== '') {
    $redes[] = ['Correo', 'mailto:' . $site['links']['email'], 'i-mail', false];
}
if ($site['links']['linkedin'] !== '') {
    $redes[] = ['LinkedIn', $site['links']['linkedin'], 'i-li', true];
}
if ($site['links']['github'] !== '') {
    $redes[] = ['GitHub', $site['links']['github'], 'i-gh', true];
}
?>
<footer class="foot">
  <div class="wrap">
    <hr class="hr">
    <div class="foot__row">
      <?php render_partial('logo', ['site' => $site]); ?>
      <nav aria-label="Pie de página">
        <ul>
          <li><a href="/servicios">Servicios</a></li>
          <li><a href="/proyectos">Proyectos</a></li>
          <li><a href="/restaurantes">Restaurantes</a></li>
          <li><a href="/blog">Blog</a></li>
          <li><a href="/contacto">Contacto</a></li>
        </ul>
      </nav>
      <ul class="foot__soc" aria-label="Redes y contacto">
        <?php foreach ($redes as [$nombre, $url, $icono, $externo]): ?>
          <li><a href="<?= e($url) ?>" aria-label="<?= e($nombre) ?>" title="<?= e($nombre) ?>"<?= $externo ? ' target="_blank" rel="noopener"' : '' ?>><svg aria-hidden="true" focusable="false"><use href="#<?= e($icono) ?>"/></svg></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="foot__bot">
      <span>&copy; <?= date('Y') ?> · <?= e($site['profile']['location']) ?></span>
      <span>Desarrollo web para negocios de <?= e($site['profile']['area']) ?></span>
    </div>
  </div>
</footer>
<script src="<?= e(asset('assets/js/main.js')) ?>" defer></script>
</body>
</html>
