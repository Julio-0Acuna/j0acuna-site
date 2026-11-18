<?php
/**
 * Propuesta B de /proyecto/<id>: el detalle como caso de estudio.
 * Título grande y una fila de datos sin cajas; la captura a todo el ancho en un marco de navegador
 * (con pestañas que avanzan solas si hay varias); la historia en filas (problema, solución, resultado)
 * junto a una ficha fija con tecnologías y enlaces; qué incluye, el código real ("Así está hecho",
 * partial code-block + Prism) y otros proyectos en la misma galería del archivo.
 * Es una maqueta: noindex y sin JSON-LD.
 */
require_once __DIR__ . '/_proyectos-comun.php';

$v = 'b';
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
$otros = pp_otros($site, $p, 3);
$anchos = pp_anchos(count($otros), true);
$historia = array_filter(['El problema' => $p['problema'], 'La solución' => $p['solucion'], 'El resultado' => $p['resultado']], static fn (string $t): bool => $t !== '');
$varias = count($galeria) > 1;
$externo = pp_externo($p['demo_url']) !== '';

pp_head($v, $p['title'] . ' (propuesta B) | ' . $site['profile']['name'], $p['description'], pp_detalle_url($v, $p), $p['image_url'], $snippet !== null ? prism_head() : '');
render_partial('nav', ['site' => $site, 'actual' => 'proyectos']);
?>
<main id="main" class="pb pb-det">
  <header class="pb-dh">
    <div class="wrap">
      <?php pp_migas($migas); ?>
      <h1><?= e($p['title']) ?></h1>
      <p class="pb-dh__lead"><?= e($p['description']) ?></p>
      <dl class="pb-datos">
        <?php if ($seccion !== null): ?><div><dt>Sección</dt><dd><a href="<?= e(pp_listado_url($v, $seccion['id'])) ?>"><?= e($seccion['nombre']) ?></a></dd></div><?php endif; ?>
        <div><dt><?= e($paraEt) ?></dt><dd><?= e($para) ?></dd></div>
        <?php if ($lugar !== ''): ?><div><dt>Dónde</dt><dd><?= e($lugar) ?></dd></div><?php endif; ?>
      </dl>
    </div>
  </header>

  <div class="wrap pb-hero">
    <?php if ($varias): ?>
      <div class="pb-gal" data-carrusel data-intervalo="5500" role="region" aria-roledescription="carrusel" aria-label="Capturas de <?= e($p['title']) ?>">
        <div class="pb-gal__stage">
          <?php foreach ($galeria as $k => $g): ?>
            <figure class="frame pb-frame pb-gal__s<?= $k === 0 ? ' is-on' : '' ?>" id="g-<?= $k ?>" role="tabpanel" aria-labelledby="gt-<?= $k ?>">
              <span class="frame__bar" aria-hidden="true"><i></i><i></i><i></i><span><?= e($g['barra']) ?></span></span>
              <span class="frame__img"><img src="<?= e($g['src']) ?>" width="1200" height="750" alt="<?= e($g['alt']) ?>"<?= $k === 0 ? ' fetchpriority="high"' : ' loading="lazy"' ?>></span>
            </figure>
          <?php endforeach; ?>
        </div>
        <div class="pb-gal__ui">
          <div class="pj__tabs" role="tablist" aria-label="Elegir captura">
            <?php foreach ($galeria as $k => $g): ?>
              <button class="pj__tab" type="button" role="tab" id="gt-<?= $k ?>" aria-controls="g-<?= $k ?>" aria-selected="<?= $k === 0 ? 'true' : 'false' ?>"<?= $k === 0 ? '' : ' tabindex="-1"' ?>><small><?= e($g['nombre']) ?></small><?= e($g['detalle'] !== '' ? $g['detalle'] : $g['nombre']) ?></button>
            <?php endforeach; ?>
          </div>
          <button class="ibtn" type="button" data-pausa aria-pressed="false" aria-label="Pausar el carrusel"><svg aria-hidden="true"><use href="#i-pause"/></svg></button>
        </div>
      </div>
    <?php elseif ($galeria): ?>
      <figure class="frame pb-frame">
        <span class="frame__bar" aria-hidden="true"><i></i><i></i><i></i><span><?= e($galeria[0]['barra']) ?></span></span>
        <span class="frame__img"><img src="<?= e($galeria[0]['src']) ?>" width="1200" height="750" alt="<?= e($galeria[0]['alt']) ?>" fetchpriority="high"></span>
      </figure>
    <?php endif; ?>
    <?php if ($p['imagen_ejemplo']): ?>
      <p class="image-note">Imagen con datos de ejemplo: el sistema real trabaja con información de clientes que no se muestra.</p>
    <?php endif; ?>
  </div>

  <div class="wrap pb-dg">
    <div class="pb-dmain">
      <?php if ($historia): ?>
        <section class="pb-story" aria-label="Cómo fue">
          <?php foreach ($historia as $titulo => $texto): ?>
            <div class="pb-story__row">
              <h2><?= e($titulo) ?></h2>
              <p><?= e($texto) ?></p>
            </div>
          <?php endforeach; ?>
        </section>
      <?php endif; ?>

      <?php if ($p['funcionalidades']): ?>
        <section class="pb-incl" aria-labelledby="t-incluye">
          <h2 id="t-incluye">Qué incluye</h2>
          <ul class="pb-incl__list">
            <?php foreach ($p['funcionalidades'] as $f): ?>
              <li><?php render_partial('icono', ['icono' => 'check', 'tam' => 20]); ?><span><?= e($f) ?></span></li>
            <?php endforeach; ?>
          </ul>
        </section>
      <?php endif; ?>

      <?php if ($snippet !== null): ?>
        <?php render_partial('code-block', ['snippet' => $snippet]); ?>
      <?php endif; ?>
    </div>

    <aside class="pb-dside" aria-labelledby="t-ficha">
      <h2 class="sr" id="t-ficha">Tecnologías y enlaces</h2>
      <?php if ($p['technologies']): ?>
        <p class="pb-dside__t">Hecho con</p>
        <ul class="pb-tec">
          <?php foreach ($p['technologies'] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <div class="pb-dside__act">
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
        <a class="more pb-dside__volver" href="<?= e(pp_listado_url($v, $seccion['id'])) ?>"><svg aria-hidden="true"><use href="#i-back"/></svg><?= e($seccion['nombre']) ?></a>
      <?php endif; ?>
    </aside>
  </div>

  <?php if ($otros): ?>
    <section class="pb-otros" aria-labelledby="t-otros">
      <div class="wrap">
        <div class="pb-g__hd">
          <h2 id="t-otros">Otros proyectos</h2>
          <a class="more" href="<?= e(pp_listado_url($v)) ?>">Ver todos<svg aria-hidden="true"><use href="#i-arr"/></svg></a>
        </div>
        <ul class="pb-grid<?= $anchos === [8, 4, 4] ? ' pb-grid--trio' : '' ?>">
          <?php foreach ($otros as $k => $o): $w = $anchos[$k]; ?>
            <li class="pb-it pb-it--<?= $w ?><?= $k === 0 ? ' pb-it--first' : '' ?>">
              <a class="pb-it__a" href="<?= e(pp_detalle_url($v, $o)) ?>">
                <span class="pb-it__img"><?php if ($o['image_url'] !== ''): ?><img src="<?= e(pp_mini($o['image_url'])) ?>" srcset="<?= e(pp_mini($o['image_url'])) ?> 600w, <?= e(asset($o['image_url'])) ?> 1200w" sizes="(min-width: 960px) 50vw, 60vw" width="1200" height="750" alt="" loading="lazy"><?php endif; ?></span>
                <span class="pb-it__txt">
                  <h3 class="pb-it__t"><?= e($o['title']) ?></h3>
                  <span class="pb-it__m"><?= e(category_name($o['categoria_id'])) ?></span>
                </span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
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
