<?php
/**
 * Carrusel de rubros en tarjetas altas: filtros por grupo, avance automático, flechas y pausa.
 * Los rubros con demos/<rubro>/_rubro.php enlazan a su página; los demás dicen "Pronto" y abren WhatsApp.
 * @var array $site @var array $rubros (portfolio_rubros()) @var string $chips_id (opcional)
 */
$grupos = ['comida' => 'Comida', 'comercio' => 'Comercio', 'servicios' => 'Servicios'];
$cuenta = array_count_values(array_column($rubros, 'grupo'));
?>
<div class="rbx rv" data-rbx role="region" aria-roledescription="carrusel" aria-label="Rubros con ejemplos">
  <div class="wrap">
    <div class="chips" role="group" aria-label="Filtrar rubros">
      <button class="chip" type="button" data-f="todos" aria-pressed="true">Todos<span><?= count($rubros) ?></span></button>
      <?php foreach ($grupos as $g => $nombre): ?>
        <?php if (!empty($cuenta[$g])): ?>
          <button class="chip" type="button" data-f="<?= e($g) ?>" aria-pressed="false"><?= e($nombre) ?><span><?= (int) $cuenta[$g] ?></span></button>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
  <ul class="rb__track" tabindex="0" aria-label="Lista de rubros, se puede deslizar">
    <?php foreach ($rubros as $r): ?>
      <li class="rb<?= $r['disponible'] ? ' rb--on' : '' ?>" data-g="<?= e($r['grupo']) ?>">
        <?php if ($r['disponible']): ?>
          <a href="<?= e($r['url']) ?>" aria-label="<?= e($r['nombre']) ?>: ver los ejemplos">
        <?php else: ?>
          <a href="<?= e(rubro_wa($site, $r)) ?>"<?= str_starts_with(rubro_wa($site, $r), 'http') ? ' target="_blank" rel="noopener"' : '' ?> aria-label="<?= e($r['nombre']) ?>: ejemplo pronto. Pedir el mío por WhatsApp">
        <?php endif; ?>
          <?php if ($r['foto'] !== ''): ?>
            <img src="<?= e($r['foto']) ?>" width="600" height="830" alt="" loading="lazy">
          <?php endif; ?>
          <span class="rb__st<?= $r['disponible'] ? ' rb__st--on' : '' ?>"><i></i><?= $r['disponible'] ? 'Disponible' : 'Pronto' ?></span>
          <span class="rb__txt">
            <span class="rb__name"><?= e($r['nombre']) ?></span>
            <?php $planes = $r['versiones'] ?: ['Simple-page', 'Multi-page', 'Plataforma web']; ?>
            <span class="rb__v" aria-hidden="true">
              <?php foreach ($planes as $plan): ?><span><?= e(trim(preg_replace('/-page$|\s+web$/i', '', $plan))) ?></span><?php endforeach; ?>
            </span>
          </span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <div class="wrap rbx__ui">
    <div class="rbx__prog" aria-hidden="true"><i></i></div>
    <button class="ibtn" type="button" data-pause aria-pressed="false" aria-label="Pausar el avance automático"><svg aria-hidden="true"><use href="#i-pause"/></svg></button>
    <button class="ibtn" type="button" data-prev aria-label="Rubros anteriores"><svg aria-hidden="true"><use href="#i-prev"/></svg></button>
    <button class="ibtn" type="button" data-next aria-label="Rubros siguientes"><svg aria-hidden="true"><use href="#i-next"/></svg></button>
  </div>
  <p class="wrap rbx__nota">Fotos de referencia: Unsplash. Los negocios de los ejemplos son ficticios.</p>
</div>
