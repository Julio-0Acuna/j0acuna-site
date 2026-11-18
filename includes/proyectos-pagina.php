<?php
/**
 * Página de proyectos (vitrina, propuesta A elegida por Julio el 18/11/2026), compartida por
 * proyectos.php (todas las secciones) y seccion.php (una sección).
 * Variables de quien incluye: $site, $actual (null o la sección) y $head (title, description,
 * canonical, og_image y jsonld para el partial head). En /proyectos va primero "Ejemplos por rubro"
 * y debajo el portafolio con la vitrina (pedido de Julio).
 * Sin JavaScript: el índice son enlaces a cada sección y las fichas se ven todas, apiladas.
 */
require_once __DIR__ . '/proyectos.php';

$secciones = portfolio_sections($site);
$rubros = prj_rubros();
$sitios = prj_total_sitios($rubros);
$total = count(portfolio_visible_projects($site));

$actual = $actual ?? null;

// Vitrina: los proyectos (en el orden de las secciones) o solo los de la sección. En "Ejemplos por
// rubro" van también los rubros que todavía no tienen su proyecto (hoy: los 15 además de la pizzería).
$enVitrina = $actual !== null ? [$actual] : $secciones;
$items = [];
$porSeccion = [];
$version = static function (array $r): array {
    foreach ($r['versiones'] as $ver) {
        if ($ver['clave'] === 'multi') {
            return $ver;
        }
    }
    return $r['versiones'][0];
};
foreach ($enVitrina as $s) {
    $lista = portfolio_section_projects($site, $s['id']);
    $usados = [];
    foreach ($lista as $p) {
        $r = prj_rubro_de($p, $rubros);
        if ($r !== null) {
            $usados[] = $r['slug'];
        }
        $porSeccion[$s['id']][] = count($items);
        $items[] = [
            's' => $s, 'p' => $p, 'r' => $r, 'id' => 'p-' . $p['id'], 'corto' => prj_corto($p),
            'full' => $p['image_url'] !== '' ? asset($p['image_url']) : '', 'mini' => $p['image_url'] !== '' ? prj_mini($p['image_url']) : '',
            'barra' => proyecto_barra($p), 'alt' => 'Pantalla de ' . $p['title'],
        ];
    }
    if ($s['id'] === 'ejemplos') {
        foreach ($rubros as $r) {
            if (in_array($r['slug'], $usados, true)) {
                continue;
            }
            $vm = $version($r);
            $porSeccion[$s['id']][] = count($items);
            $items[] = [
                's' => $s, 'p' => null, 'r' => $r, 'id' => 'r-' . $r['slug'], 'corto' => $r['nombre'],
                'full' => $vm['img'], 'mini' => $vm['mini'], 'barra' => prj_barra($vm['url']), 'alt' => $vm['negocio'] . ', ejemplo ' . $vm['plan'],
            ];
        }
    }
}
$n = count($items);
$verRubros = $actual === null || $actual['id'] === 'ejemplos';

if ($actual === null) {
    $titulo = 'Portafolio de proyectos web';
    $lead = $total . ' proyectos funcionando y ' . $sitios . ' sitios de ejemplo. Elige una sección o deja que la vitrina avance sola; cada proyecto muestra cómo está hecho.';
    $migas = [['Proyectos', prj_listado_url()]];
} else {
    $titulo = $actual['nombre'];
    $lead = $actual['resumen'] . ($actual['id'] === 'ejemplos'
        ? ' ' . count($rubros) . ' rubros, ' . $sitios . ' sitios que puedes abrir.'
        : ' ' . ($actual['cantidad'] === 1 ? 'Un proyecto.' : $actual['cantidad'] . ' proyectos.'));
    $migas = [['Proyectos', prj_listado_url()], [$actual['nombre'], prj_listado_url($actual['id'])]];
}

render_partial('head', $head + ['extra_head' => '<script src="' . e(asset('assets/js/proyectos.js')) . '" defer></script>']);
render_partial('nav', ['site' => $site, 'actual' => 'proyectos']);
?>
<main id="main" class="pa<?= $actual === null ? ' pa--lista' : '' ?>">
  <?php if ($actual === null): ?>
    <div class="wrap pa-migas"><?php prj_migas($migas); ?></div>
    <?php if ($rubros) { render_partial('proyectos-rubros', ['rubros' => $rubros, 'actual' => $actual]); } ?>
  <?php endif; ?>

  <header class="pa-hd">
    <div class="wrap">
      <?php if ($actual !== null) { prj_migas($migas); } ?>
      <div class="pa-hd__row">
        <h1><?= titulo_grad($titulo, $actual === null ? 2 : 1) ?></h1>
        <p class="pa-hd__lead"><?= e($lead) ?></p>
      </div>
    </div>
  </header>

  <?php if ($n > 0): ?>
    <section class="pa-vit" aria-labelledby="t-vitrina">
      <div class="wrap">
        <h2 class="sr" id="t-vitrina"><?= $actual === null ? 'Proyectos por sección' : 'Proyectos de ' . e($actual['nombre']) ?></h2>
        <div class="pa-grid" data-pv data-intervalo="7000" role="region" aria-roledescription="carrusel" aria-label="Proyectos">

          <div class="pa-idx">
            <p class="pa-idx__hd"><span>Secciones</span><span><?= count($secciones) ?></span></p>
            <ul class="pa-secs">
              <?php foreach ($secciones as $s):
                  $dentro = isset($porSeccion[$s['id']]);
                  $esActual = $dentro && ($items[0]['s']['id'] === $s['id']);
                  $cuenta = $s['id'] === 'ejemplos' ? count($rubros) : (int) $s['cantidad'];
              ?>
                <li>
                  <a class="pa-s<?= $esActual ? ' is-on' : '' ?>" href="<?= e(prj_listado_url($s['id'])) ?>"<?= $dentro ? ' data-sec="' . e($s['id']) . '" data-desde="' . $porSeccion[$s['id']][0] . '"' : '' ?><?= $actual !== null && $actual['id'] === $s['id'] ? ' aria-current="page"' : '' ?>>
                    <span class="pa-s__n"><?= e($s['nombre']) ?></span>
                    <span class="pa-s__c"><?= $cuenta ?><span class="sr"> <?= $s['id'] === 'ejemplos' ? 'rubros' : ($cuenta === 1 ? 'proyecto' : 'proyectos') ?></span></span>
                    <?php if ($dentro && count($porSeccion[$s['id']]) > 1): ?>
                      <span class="pa-s__dots" aria-hidden="true"><?php foreach ($porSeccion[$s['id']] as $k): ?><i data-dot="<?= $k ?>"></i><?php endforeach; ?></span>
                    <?php endif; ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
            <?php if ($n > 1): ?>
              <div class="pa-ctrl">
                <button class="pa-pausa" type="button" data-pausa aria-pressed="false"><svg aria-hidden="true"><use href="#i-pause"/></svg><span data-pausa-txt>Pausar</span></button>
                <button class="ibtn" type="button" data-prev aria-label="Proyecto anterior"><svg aria-hidden="true"><use href="#i-prev"/></svg></button>
                <button class="ibtn" type="button" data-next aria-label="Proyecto siguiente"><svg aria-hidden="true"><use href="#i-next"/></svg></button>
                <p class="pa-ctrl__n" aria-hidden="true"><span data-actual>1</span> de <?= $n ?></p>
              </div>
            <?php endif; ?>
          </div>

          <div class="pa-stage">
            <?php $i0 = $items[0]; ?>
            <figure class="frame pa-frame">
              <span class="frame__bar" aria-hidden="true"><i></i><i></i><i></i><span data-barra><?= e($i0['barra']) ?></span></span>
              <span class="frame__img" data-capas>
                <?php if ($i0['full'] !== ''): ?>
                  <img class="is-on" data-capa="0" src="<?= e($i0['full']) ?>" width="1200" height="750" alt="<?= e($i0['alt']) ?>" fetchpriority="high">
                <?php else: ?>
                  <span class="frame__ph is-on" data-capa="0" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($i0['corto'], 0, 2))) ?></span>
                <?php endif; ?>
              </span>
            </figure>

            <?php if ($n > 1): ?>
              <div class="pa-tiras">
                <?php foreach ($enVitrina as $s): if (empty($porSeccion[$s['id']])) { continue; } ?>
                  <div class="pa-tira<?= $i0['s']['id'] === $s['id'] ? ' is-on' : '' ?>" data-tira="<?= e($s['id']) ?>" aria-label="<?= $s['id'] === 'ejemplos' ? 'Rubros' : 'Proyectos de ' . e($s['nombre']) ?>">
                    <?php foreach ($porSeccion[$s['id']] as $k): $it = $items[$k]; ?>
                      <a class="pa-t<?= $k === 0 ? ' is-on' : '' ?>" href="#<?= e($it['id']) ?>" data-it="<?= $k ?>" data-full="<?= e($it['full']) ?>" data-barra="<?= e($it['barra']) ?>" data-alt="<?= e($it['alt']) ?>">
                        <span class="pa-t__img"><?php if ($it['mini'] !== ''): ?><img src="<?= e($it['mini']) ?>" width="600" height="375" alt="" loading="lazy"><?php endif; ?></span>
                        <span class="pa-t__n"><?= e($it['corto']) ?></span>
                        <i class="pa-t__bar" aria-hidden="true"></i>
                      </a>
                    <?php endforeach; ?>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <div class="pa-panels">
              <?php foreach ($items as $k => $it): $p = $it['p']; $r = $it['r']; $s = $it['s']; ?>
                <article class="pa-p<?= $k === 0 ? ' is-on' : '' ?>" id="<?= e($it['id']) ?>" aria-labelledby="h-<?= e($it['id']) ?>">
                  <?php if ($it['mini'] !== ''): ?>
                    <a class="pa-p__img" href="<?= e($p !== null ? prj_detalle_url($p) : $r['url']) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e($it['mini']) ?>" width="600" height="375" alt="" loading="lazy"></a>
                  <?php endif; ?>

                  <?php if ($p !== null):
                      [$paraEt, $para] = prj_para($p);
                      $lugar = prj_lugar($p);
                  ?>
                    <h3 id="h-<?= e($it['id']) ?>"><a href="<?= e(prj_detalle_url($p)) ?>"><?= e($p['title']) ?></a></h3>
                    <p class="pa-p__sum"><?= e($p['description']) ?></p>
                    <dl class="pa-facts">
                      <div><dt>Sección</dt><dd><a href="<?= e(prj_listado_url($s['id'])) ?>"><?= e($s['nombre']) ?></a></dd></div>
                      <div><dt><?= e($paraEt) ?></dt><dd><?= e($para) ?></dd></div>
                      <?php if ($lugar !== ''): ?><div><dt>Dónde</dt><dd><?= e($lugar) ?></dd></div><?php endif; ?>
                      <?php if ($p['resultado'] !== '' && $r === null): ?><div class="pa-facts__wide"><dt>Qué resolví</dt><dd><?= e($p['resultado']) ?></dd></div><?php endif; ?>
                      <?php if ($p['technologies']): ?>
                        <div class="pa-facts__wide"><dt>Tecnologías</dt><dd><ul class="pa-tec"><?php foreach ($p['technologies'] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?></ul></dd></div>
                      <?php endif; ?>
                    </dl>
                  <?php else: ?>
                    <h3 id="h-<?= e($it['id']) ?>"><a href="<?= e($r['url']) ?>"><?= e($r['nombre']) ?></a></h3>
                    <p class="pa-p__sum">Página web de ejemplo para <?= e($r['plural']) ?>, en tres versiones que puedes abrir y recorrer. Los negocios son ficticios.</p>
                  <?php endif; ?>

                  <?php if ($r !== null): ?>
                    <ul class="pa-vers" aria-label="Versiones del ejemplo">
                      <?php foreach ($r['versiones'] as $ver): ?>
                        <li>
                          <a href="<?= e($ver['url']) ?>" data-full="<?= e($ver['img']) ?>" data-barra="<?= e(prj_barra($ver['url'])) ?>">
                            <span class="pa-vers__img"><img src="<?= e($ver['mini']) ?>" width="600" height="375" alt="" loading="lazy"></span>
                            <span class="pa-vers__n"><?= e($ver['plan']) ?></span>
                            <span class="pa-vers__d"><?= e(implode(', ', array_filter([$ver['negocio'], $ver['comuna']]))) ?></span>
                          </a>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>

                  <div class="pa-p__act">
                    <?php if ($p !== null): ?>
                      <a class="btn btn--light" href="<?= e(prj_detalle_url($p)) ?>">Ver el proyecto<span class="sr">: <?= e($p['title']) ?></span><svg aria-hidden="true"><use href="#i-arr"/></svg></a>
                      <?php if ($p['demo_url'] !== '' && $r === null): ?>
                        <a class="btn btn--glass" href="<?= e($p['demo_url']) ?>"<?= prj_externo($p['demo_url']) ?>>Abrir el sitio<svg aria-hidden="true"><use href="#<?= prj_externo($p['demo_url']) !== '' ? 'i-out' : 'i-arr' ?>"/></svg></a>
                      <?php endif; ?>
                      <?php if ($p['repo_url'] !== ''): ?>
                        <a class="more" href="<?= e($p['repo_url']) ?>" target="_blank" rel="noopener">Ver el código<svg aria-hidden="true"><use href="#i-out"/></svg></a>
                      <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($r !== null): ?>
                      <a class="<?= $p !== null ? 'btn btn--glass' : 'btn btn--light' ?>" href="<?= e($r['url']) ?>">Abrir los ejemplos<span class="sr"> de <?= e(mb_strtolower($r['nombre'])) ?></span><svg aria-hidden="true"><use href="#i-arr"/></svg></a>
                    <?php endif; ?>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php if ($actual !== null && $verRubros && $rubros) { render_partial('proyectos-rubros', ['rubros' => $rubros, 'actual' => $actual]); } ?>

  <?php render_partial('cta', [
      'site' => $site,
      'titulo' => $actual === null ? '¿Quieres algo así para tu negocio?' : '¿Quieres algo así?',
      'texto' => $actual === null ? 'Cuéntame qué necesitas y te digo cómo lo haría.' : 'Cuéntame qué necesita tu negocio y te digo cómo lo haría.',
      'mensaje' => $actual === null ? $site['profile']['whatsapp_mensaje'] : 'Hola Julio, vi tus proyectos de «' . $actual['nombre'] . '» y quiero algo así.',
      'secundario' => ['Ver servicios', '/servicios'],
  ]); ?>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $site['profile']['whatsapp_mensaje']]);
render_partial('footer', ['site' => $site]);
