<?php
/**
 * Muro de los rubros con ejemplo: cada captura va mostrando sus versiones (assets/js/proyectos.js).
 * @var array $rubros (prj_rubros()) @var ?array $actual (null en /proyectos; la sección en /proyectos/<id>)
 */
?>
  <section class="pa-ru" id="rubros" aria-labelledby="t-rubros">
    <div class="wrap">
      <div class="pa-ru__hd">
        <h2 class="h2" id="t-rubros"><?= $actual === null ? 'Ejemplos por rubro' : 'Los ' . count($rubros) . ' rubros' ?></h2>
        <p class="pa-ru__lead"><?= count($rubros) ?> rubros con tres versiones cada uno: Simple-page, Multi-page y Plataforma web. Ábrelos y recórrelos como si fueran tuyos.</p>
        <button class="pa-pausa pa-ru__pausa" type="button" data-muro-pausa aria-pressed="false"><svg aria-hidden="true"><use href="#i-pause"/></svg><span data-pausa-txt>Pausar</span></button>
      </div>
      <ul class="pa-muro" data-muro aria-label="Rubros con ejemplos">
        <?php foreach ($rubros as $i => $r):
            $vs = $r['versiones'];
            $ini = $i % count($vs);
        ?>
          <li class="pa-m" data-m data-ini="<?= $ini ?>">
            <a class="pa-m__main" href="<?= e($r['url']) ?>">
              <span class="pa-m__img"><img class="is-on" src="<?= e($vs[$ini]['mini']) ?>" width="600" height="375" alt=""<?= $actual === null && $i < 4 ? '' : ' loading="lazy"' ?>><span class="pa-m__tag" aria-hidden="true" data-tag><?= e($vs[$ini]['corto']) ?></span></span>
              <span class="pa-m__n"><?= e($r['nombre']) ?><span class="sr">: ver los <?= count($vs) ?> ejemplos</span></span>
            </a>
            <ul class="pa-m__v">
              <?php foreach ($vs as $k => $ver): ?>
                <li><a href="<?= e($ver['url']) ?>" data-img="<?= e($ver['mini']) ?>"<?= $k === $ini ? ' class="is-on"' : '' ?>><span aria-hidden="true"><?= e($ver['corto']) ?></span><span class="sr"><?= e($r['nombre']) ?>, <?= e($ver['plan']) ?>: <?= e($ver['negocio']) ?></span></a></li>
              <?php endforeach; ?>
            </ul>
          </li>
        <?php endforeach; ?>
      </ul>
      <p class="pa-ru__nota">Los negocios de los ejemplos son ficticios. <a href="/demos/">Ver todos los ejemplos en una página</a></p>
    </div>
  </section>
