<?php
/**
 * Propuesta A de /proyectos y /proyectos/<seccion>: "Vitrina de proyectos".
 * Como /servicios: las secciones en un índice a la izquierda y una vitrina grande a la derecha,
 * con la captura del proyecto en un marco de navegador, una tira con los proyectos de la sección
 * y la ficha (para quién, dónde, qué resolví, tecnologías y enlaces). Avanza sola, con pausa.
 * Debajo, los 16 rubros en un muro de capturas que van mostrando sus 3 versiones.
 * Con ?seccion=<id> es la página de una sección (la vitrina solo con sus proyectos).
 * Sin JavaScript: el índice son enlaces a cada sección y las fichas se ven todas, apiladas.
 * Es una maqueta: noindex y sin JSON-LD.
 */
require_once __DIR__ . '/_proyectos-comun.php';

$v = 'a';
$site = portfolio_load_site();
$secciones = portfolio_sections($site);
$rubros = pp_rubros();
$sitios = pp_total_sitios($rubros);
$total = count(portfolio_visible_projects($site));

$sid = is_string($_GET['seccion'] ?? null) ? $_GET['seccion'] : '';
$actual = null;
if ($sid !== '') {
    $actual = preg_match('/^[a-z0-9-]+$/', $sid) ? portfolio_find_section($site, $sid) : null;
    if ($actual === null) {
        pp_404($site, 'Sección no encontrada', 'Esta sección no existe o todavía no tiene proyectos.', ['Ver todas las secciones', pp_listado_url($v)]);
        return;
    }
}

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
        $r = pp_rubro_de($p, $rubros);
        if ($r !== null) {
            $usados[] = $r['slug'];
        }
        $porSeccion[$s['id']][] = count($items);
        $items[] = [
            's' => $s, 'p' => $p, 'r' => $r, 'id' => 'p-' . $p['id'], 'corto' => pp_corto($p),
            'full' => $p['image_url'] !== '' ? asset($p['image_url']) : '', 'mini' => $p['image_url'] !== '' ? pp_mini($p['image_url']) : '',
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
                'full' => $vm['img'], 'mini' => $vm['mini'], 'barra' => pp_barra($vm['url']), 'alt' => $vm['negocio'] . ', ejemplo ' . $vm['plan'],
            ];
        }
    }
}
$n = count($items);
$verRubros = $actual === null || $actual['id'] === 'ejemplos';

if ($actual === null) {
    $titulo = 'Portafolio de proyectos web';
    $lead = $total . ' proyectos funcionando y ' . $sitios . ' sitios de ejemplo. Elige una sección o deja que la vitrina avance sola; cada proyecto muestra cómo está hecho.';
    $migas = [['Proyectos', pp_listado_url($v)]];
    $head = ['Proyectos (propuesta A) | ' . $site['profile']['name'], 'Proyectos de Julio Acuña, desarrollador web en Santiago: sistemas para restaurantes, tiendas online, páginas web e inteligencia artificial.', pp_listado_url($v), ''];
} else {
    $titulo = $actual['nombre'];
    $lead = $actual['resumen'] . ($actual['id'] === 'ejemplos'
        ? ' ' . count($rubros) . ' rubros, ' . $sitios . ' sitios que puedes abrir.'
        : ' ' . ($actual['cantidad'] === 1 ? 'Un proyecto.' : $actual['cantidad'] . ' proyectos.'));
    $migas = [['Proyectos', pp_listado_url($v)], [$actual['nombre'], pp_listado_url($v, $actual['id'])]];
    $head = [$actual['nombre'] . ': proyectos (propuesta A) | ' . $site['profile']['name'], $actual['resumen'] . ' Proyectos de Julio Acuña, desarrollador web en Santiago.', pp_listado_url($v, $actual['id']), $actual['portada']];
}

pp_head($v, ...$head);
render_partial('nav', ['site' => $site, 'actual' => 'proyectos']);
?>
<main id="main" class="pa">
  <header class="pa-hd">
    <div class="wrap">
      <?php pp_migas($migas); ?>
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
                  <a class="pa-s<?= $esActual ? ' is-on' : '' ?>" href="<?= e(pp_listado_url($v, $s['id'])) ?>"<?= $dentro ? ' data-sec="' . e($s['id']) . '" data-desde="' . $porSeccion[$s['id']][0] . '"' : '' ?><?= $actual !== null && $actual['id'] === $s['id'] ? ' aria-current="page"' : '' ?>>
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
                    <a class="pa-p__img" href="<?= e($p !== null ? pp_detalle_url($v, $p) : $r['url']) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e($it['mini']) ?>" width="600" height="375" alt="" loading="lazy"></a>
                  <?php endif; ?>

                  <?php if ($p !== null):
                      [$paraEt, $para] = pp_para($p);
                      $lugar = pp_lugar($p);
                  ?>
                    <h3 id="h-<?= e($it['id']) ?>"><a href="<?= e(pp_detalle_url($v, $p)) ?>"><?= e($p['title']) ?></a></h3>
                    <p class="pa-p__sum"><?= e($p['description']) ?></p>
                    <dl class="pa-facts">
                      <div><dt>Sección</dt><dd><a href="<?= e(pp_listado_url($v, $s['id'])) ?>"><?= e($s['nombre']) ?></a></dd></div>
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
                          <a href="<?= e($ver['url']) ?>" data-full="<?= e($ver['img']) ?>" data-barra="<?= e(pp_barra($ver['url'])) ?>">
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
                      <a class="btn btn--light" href="<?= e(pp_detalle_url($v, $p)) ?>">Ver el proyecto<span class="sr">: <?= e($p['title']) ?></span><svg aria-hidden="true"><use href="#i-arr"/></svg></a>
                      <?php if ($p['demo_url'] !== '' && $r === null): ?>
                        <a class="btn btn--glass" href="<?= e($p['demo_url']) ?>"<?= pp_externo($p['demo_url']) ?>>Abrir el sitio<svg aria-hidden="true"><use href="#<?= pp_externo($p['demo_url']) !== '' ? 'i-out' : 'i-arr' ?>"/></svg></a>
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

  <?php if ($verRubros && $rubros): ?>
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
                <span class="pa-m__img"><img class="is-on" src="<?= e($vs[$ini]['mini']) ?>" width="600" height="375" alt="" loading="lazy"><span class="pa-m__tag" aria-hidden="true" data-tag><?= e($vs[$ini]['corto']) ?></span></span>
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
  <?php endif; ?>

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
