<?php
/**
 * Propuesta A de /proyecto/<id>: detalle con el lenguaje de la vitrina.
 * Arriba el título y la bajada en una fila (como /servicios); después la captura grande en marco de
 * navegador (con pestañas que avanzan solas si hay varias) junto a una ficha fija: para quién, dónde,
 * tecnologías y enlaces. Sigue la historia (problema, solución, resultado), qué incluye, el código
 * real ("Así está hecho", partial code-block + Prism) y una tira de otros proyectos con el siguiente.
 * Es una maqueta: noindex y sin JSON-LD.
 */
require_once __DIR__ . '/_proyectos-comun.php';

$v = 'a';
$site = portfolio_load_site();
$id = is_string($_GET['id'] ?? null) ? $_GET['id'] : '';
$p = preg_match('/^[a-z0-9-]+$/', $id) ? portfolio_find_project($site, $id) : null;
if ($p === null) {
    pp_404($site, 'Proyecto no encontrado', 'Este proyecto no existe o ya no está publicado.', ['Ver todos los proyectos', pp_listado_url($v)]);
    return;
}

$rubros = pp_rubros();
$galeria = pp_galeria($p, $rubros);
$seccion = portfolio_find_section($site, $p['categoria_id']);
$migas = pp_migas_detalle($v, $p, $seccion);
$snippet = portfolio_snippet($p);
$mensaje = 'Hola Julio, vi el proyecto «' . $p['title'] . '» y quiero algo así para mi negocio.';
$wa = site_wa($site, $mensaje);
[$paraEt, $para] = pp_para($p);
$lugar = pp_lugar($p);
[, $sig] = pp_vecinos($site, $p);
$otros = array_values(array_filter(pp_otros($site, $p, 9), static fn (array $o): bool => $sig === null || $o['id'] !== $sig['id']));
$otros = array_slice($otros, 0, 8);
$historia = array_filter(['El problema' => $p['problema'], 'La solución' => $p['solucion'], 'El resultado' => $p['resultado']], static fn (string $t): bool => $t !== '');
$varias = count($galeria) > 1;
$externo = pp_externo($p['demo_url']) !== '';

pp_head($v, $p['title'] . ' (propuesta A) | ' . $site['profile']['name'], $p['description'], pp_detalle_url($v, $p), $p['image_url'], $snippet !== null ? prism_head() : '');
render_partial('nav', ['site' => $site, 'actual' => 'proyectos']);
?>
<main id="main" class="pa pa-det">
  <header class="pa-hd">
    <div class="wrap">
      <?php pp_migas($migas); ?>
      <div class="pa-hd__row">
        <h1><?= e($p['title']) ?></h1>
        <p class="pa-hd__lead"><?= e($p['description']) ?></p>
      </div>
    </div>
  </header>

  <div class="wrap pa-d">
    <div class="pa-d__gal">
      <?php if ($varias): ?>
        <div class="pa-gal" data-carrusel data-intervalo="5500" role="region" aria-roledescription="carrusel" aria-label="Capturas de <?= e($p['title']) ?>">
          <div class="pa-gal__stage">
            <?php foreach ($galeria as $k => $g): ?>
              <figure class="frame pa-frame pa-gal__s<?= $k === 0 ? ' is-on' : '' ?>" id="g-<?= $k ?>" role="tabpanel" aria-labelledby="gt-<?= $k ?>">
                <span class="frame__bar" aria-hidden="true"><i></i><i></i><i></i><span><?= e($g['barra']) ?></span></span>
                <span class="frame__img"><img src="<?= e($g['src']) ?>" width="1200" height="750" alt="<?= e($g['alt']) ?>"<?= $k === 0 ? ' fetchpriority="high"' : ' loading="lazy"' ?>></span>
              </figure>
            <?php endforeach; ?>
          </div>
          <div class="pa-gal__ui">
            <div class="pa-gal__tabs" role="tablist" aria-label="Elegir captura">
              <?php foreach ($galeria as $k => $g): ?>
                <button class="pa-gal__tab" type="button" role="tab" id="gt-<?= $k ?>" aria-controls="g-<?= $k ?>" aria-selected="<?= $k === 0 ? 'true' : 'false' ?>"<?= $k === 0 ? '' : ' tabindex="-1"' ?>>
                  <span class="pa-gal__mini" aria-hidden="true"><img src="<?= e($g['mini']) ?>" width="600" height="375" alt="" loading="lazy"></span>
                  <span class="pa-gal__lbl"><?= e($g['nombre']) ?><?php if ($g['detalle'] !== ''): ?><small><?= e($g['detalle']) ?></small><?php endif; ?></span>
                </button>
              <?php endforeach; ?>
            </div>
            <button class="ibtn" type="button" data-pausa aria-pressed="false" aria-label="Pausar el carrusel"><svg aria-hidden="true"><use href="#i-pause"/></svg></button>
          </div>
        </div>
      <?php elseif ($galeria): ?>
        <figure class="frame pa-frame pa-frame--uno">
          <span class="frame__bar" aria-hidden="true"><i></i><i></i><i></i><span><?= e($galeria[0]['barra']) ?></span></span>
          <span class="frame__img"><img src="<?= e($galeria[0]['src']) ?>" width="1200" height="750" alt="<?= e($galeria[0]['alt']) ?>" fetchpriority="high"></span>
        </figure>
      <?php endif; ?>
      <?php if ($p['imagen_ejemplo']): ?>
        <p class="image-note">Imagen con datos de ejemplo: el sistema real trabaja con información de clientes que no se muestra.</p>
      <?php endif; ?>
    </div>

    <aside class="pa-d__ficha" aria-labelledby="t-ficha">
      <h2 class="sr" id="t-ficha">Ficha del proyecto</h2>
      <dl class="pa-facts pa-facts--col">
        <?php if ($seccion !== null): ?><div><dt>Sección</dt><dd><a href="<?= e(pp_listado_url($v, $seccion['id'])) ?>"><?= e($seccion['nombre']) ?></a></dd></div><?php endif; ?>
        <div><dt><?= e($paraEt) ?></dt><dd><?= e($para) ?></dd></div>
        <?php if ($lugar !== ''): ?><div><dt>Dónde</dt><dd><?= e($lugar) ?></dd></div><?php endif; ?>
        <?php if ($p['technologies']): ?>
          <div><dt>Tecnologías</dt><dd><ul class="pa-tec"><?php foreach ($p['technologies'] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?></ul></dd></div>
        <?php endif; ?>
      </dl>
      <div class="pa-d__act">
        <?php if ($wa !== ''): ?>
          <a class="btn btn--light" href="<?= e($wa) ?>" target="_blank" rel="noopener"><svg aria-hidden="true"><use href="#i-wa"/></svg>Quiero algo así</a>
        <?php endif; ?>
        <?php if ($p['demo_url'] !== ''): ?>
          <a class="btn btn--glass" href="<?= e($p['demo_url']) ?>"<?= pp_externo($p['demo_url']) ?>><?= $externo ? 'Abrir el sitio' : 'Abrir los ejemplos' ?><svg aria-hidden="true"><use href="#<?= $externo ? 'i-out' : 'i-arr' ?>"/></svg></a>
        <?php endif; ?>
        <?php if ($p['repo_url'] !== ''): ?>
          <a class="btn btn--glass" href="<?= e($p['repo_url']) ?>" target="_blank" rel="noopener">Ver el código<svg aria-hidden="true"><use href="#i-out"/></svg></a>
        <?php endif; ?>
      </div>
      <?php if ($seccion !== null): ?>
        <a class="more pa-d__volver" href="<?= e(pp_listado_url($v, $seccion['id'])) ?>"><svg aria-hidden="true"><use href="#i-back"/></svg><?= e($seccion['nombre']) ?></a>
      <?php endif; ?>
    </aside>

    <div class="pa-d__main">
      <?php if ($historia): ?>
        <section class="pa-story" aria-labelledby="t-historia">
          <h2 class="pa-d__h" id="t-historia">Cómo fue</h2>
          <ol class="pa-story__list" style="--pasos: <?= count($historia) ?>">
            <?php foreach ($historia as $titulo => $texto): ?>
              <li><h3><?= e($titulo) ?></h3><p><?= e($texto) ?></p></li>
            <?php endforeach; ?>
          </ol>
        </section>
      <?php endif; ?>

      <?php if ($p['funcionalidades']): ?>
        <section class="pa-incl" aria-labelledby="t-incluye">
          <h2 class="pa-d__h" id="t-incluye">Qué incluye</h2>
          <ul class="checks pa-incl__list">
            <?php foreach ($p['funcionalidades'] as $f): ?>
              <li><?php render_partial('icono', ['icono' => 'check', 'tam' => 18]); ?><span><?= e($f) ?></span></li>
            <?php endforeach; ?>
          </ul>
        </section>
      <?php endif; ?>

      <?php if ($snippet !== null): ?>
        <?php render_partial('code-block', ['snippet' => $snippet]); ?>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($otros || $sig !== null): ?>
    <section class="pa-mas" aria-labelledby="t-mas">
      <div class="wrap">
        <?php if ($sig !== null): ?>
          <a class="pa-sig" href="<?= e(pp_detalle_url($v, $sig)) ?>">
            <span class="pa-sig__txt">
              <span class="pa-sig__lbl">Siguiente proyecto</span>
              <strong><?= e($sig['title']) ?></strong>
              <span class="pa-sig__sum"><?= e($sig['description']) ?></span>
            </span>
            <?php if ($sig['image_url'] !== ''): ?>
              <span class="frame pa-sig__img" aria-hidden="true">
                <span class="frame__bar"><i></i><i></i><i></i><span><?= e(proyecto_barra($sig)) ?></span></span>
                <span class="frame__img"><img src="<?= e(pp_mini($sig['image_url'])) ?>" width="600" height="375" alt="" loading="lazy"></span>
              </span>
            <?php endif; ?>
          </a>
        <?php endif; ?>
        <?php if ($otros): ?>
          <div class="pa-mas__hd">
            <h2 class="pa-d__h" id="t-mas">Más proyectos</h2>
            <a class="more" href="<?= e(pp_listado_url($v)) ?>">Ver todos<svg aria-hidden="true"><use href="#i-arr"/></svg></a>
          </div>
          <ul class="pa-mas__tira">
            <?php foreach ($otros as $o): ?>
              <li>
                <a class="pa-t" href="<?= e(pp_detalle_url($v, $o)) ?>">
                  <span class="pa-t__img"><?php if ($o['image_url'] !== ''): ?><img src="<?= e(pp_mini($o['image_url'])) ?>" width="600" height="375" alt="" loading="lazy"><?php endif; ?></span>
                  <span class="pa-t__n"><?= e(pp_corto($o)) ?></span>
                  <span class="pa-t__m"><?= e(category_name($o['categoria_id'])) ?></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </section>
  <?php endif; ?>

  <?php render_partial('cta', [
      'site' => $site,
      'titulo' => '¿Quieres algo así?',
      'texto' => 'Cuéntame qué necesita tu negocio y te digo cómo lo haría, sin compromiso.',
      'mensaje' => $mensaje,
      'secundario' => ['Ver más proyectos', pp_listado_url($v)],
  ]); ?>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $mensaje]);
if ($snippet !== null) {
    echo prism_scripts($snippet['lenguaje']);
}
render_partial('footer', ['site' => $site]);
