<?php
/**
 * Propuesta B de /proyectos y /proyectos/<seccion>: "Destacados + archivo".
 * Arriba, los destacados como un mazo de ventanas de navegador: la del frente es el proyecto activo
 * y detrás asoman los siguientes; avanza solo con pestañas y pausa, como en la portada.
 * Después, el archivo: cada sección con sus proyectos en una galería de imágenes grandes, sin cajas,
 * con un filtro por sección que son enlaces (funciona sin JavaScript). En "Ejemplos por rubro",
 * los 16 rubros en tarjetas altas (sus 3 versiones apiladas) que avanzan solas.
 * Con ?seccion=<id> es la página de la sección: todos sus proyectos, sin el mazo.
 * Es una maqueta: noindex y sin JSON-LD.
 */
require_once __DIR__ . '/_proyectos-comun.php';

$v = 'b';
$site = portfolio_load_site();
$secciones = portfolio_sections($site);
$rubros = pp_rubros();
$sitios = pp_total_sitios($rubros);
$total = count(portfolio_visible_projects($site));
$porSeccion = 3;

$sid = is_string($_GET['seccion'] ?? null) ? $_GET['seccion'] : '';
$actual = null;
if ($sid !== '') {
    $actual = preg_match('/^[a-z0-9-]+$/', $sid) ? portfolio_find_section($site, $sid) : null;
    if ($actual === null) {
        pp_404($site, 'Sección no encontrada', 'Esta sección no existe o todavía no tiene proyectos.', ['Ver todas las secciones', pp_listado_url($v)]);
        return;
    }
}
$destacados = $actual === null ? portfolio_featured_projects($site, 6) : [];
$grupos = $actual !== null ? [$actual] : $secciones;
$grupos_rubro = ['comida' => 'Comida', 'comercio' => 'Comercio', 'servicios' => 'Servicios'];

if ($actual === null) {
    $titulo = 'Portafolio de proyectos web';
    $lead = $total . ' proyectos de un desarrollador web en ' . $site['profile']['ciudad'] . ': sistemas para restaurantes, tiendas online, páginas web e inteligencia artificial, y ' . $sitios . ' sitios de ejemplo por rubro.';
    $migas = [['Proyectos', pp_listado_url($v)]];
    $head = ['Proyectos (propuesta B) | ' . $site['profile']['name'], 'Proyectos de Julio Acuña, desarrollador web en Santiago: sistemas para restaurantes, tiendas online, páginas web e inteligencia artificial.', pp_listado_url($v), ''];
} else {
    $titulo = $actual['nombre'];
    $lead = $actual['resumen'];
    $migas = [['Proyectos', pp_listado_url($v)], [$actual['nombre'], pp_listado_url($v, $actual['id'])]];
    $head = [$actual['nombre'] . ': proyectos (propuesta B) | ' . $site['profile']['name'], $actual['resumen'] . ' Proyectos de Julio Acuña, desarrollador web en Santiago.', pp_listado_url($v, $actual['id']), $actual['portada']];
}

/** Una imagen del archivo: miniatura de 600 px y la grande para pantallas anchas. */
$imagen = static function (array $p, int $ancho, bool $primera = false): string {
    if ($p['image_url'] === '') {
        return '<span class="frame__ph" aria-hidden="true">' . e(mb_strtoupper(mb_substr($p['title'], 0, 2))) . '</span>';
    }
    $sizes = $ancho >= 12 ? '(min-width: 960px) 700px, 100vw' : '(min-width: 960px) ' . round($ancho / 12 * 100) . 'vw, 100vw';
    return '<img src="' . e(pp_mini($p['image_url'])) . '" srcset="' . e(pp_mini($p['image_url'])) . ' 600w, ' . e(asset($p['image_url'])) . ' 1200w" sizes="' . $sizes . '" width="1200" height="750" alt=""' . ($primera ? '' : ' loading="lazy"') . '>';
};

pp_head($v, ...$head);
render_partial('nav', ['site' => $site, 'actual' => 'proyectos']);
?>
<main id="main" class="pb">
  <header class="pb-hd">
    <div class="wrap">
      <?php pp_migas($migas); ?>
      <h1><?= titulo_grad($titulo, $actual === null ? 2 : 1) ?></h1>
      <p class="pb-hd__lead"><?= e($lead) ?><?php if ($actual !== null): ?> <span class="pb-hd__n"><?= $actual['id'] === 'ejemplos' ? count($rubros) . ' rubros, ' . $sitios . ' sitios.' : ($actual['cantidad'] === 1 ? 'Un proyecto.' : $actual['cantidad'] . ' proyectos.') ?></span><?php endif; ?></p>
    </div>
  </header>

  <?php if (count($destacados) > 1): ?>
    <section class="pb-dest" aria-labelledby="t-dest">
      <div class="wrap">
        <h2 class="sr" id="t-dest">Proyectos destacados</h2>
        <div class="pb-deck" data-carrusel data-intervalo="6500" role="region" aria-roledescription="carrusel" aria-label="Proyectos destacados">
          <div class="pb-deck__stage">
            <?php foreach ($destacados as $i => $p): ?>
              <article class="pb-ds<?= $i === 0 ? ' is-on' : '' ?>" id="pd-<?= e($p['id']) ?>" role="tabpanel" aria-labelledby="pdt-<?= e($p['id']) ?>">
                <a class="frame pb-ds__frame" href="<?= e(pp_detalle_url($v, $p)) ?>" tabindex="-1" aria-hidden="true">
                  <span class="frame__bar"><i></i><i></i><i></i><span><?= e(proyecto_barra($p)) ?></span></span>
                  <span class="frame__img">
                    <?php if ($p['image_url'] !== ''): ?>
                      <img src="<?= e(asset($p['image_url'])) ?>" width="1200" height="750" alt=""<?= $i === 0 ? ' fetchpriority="high"' : ($i < 3 ? '' : ' loading="lazy"') ?>>
                    <?php else: ?>
                      <span class="frame__ph"><?= e(mb_strtoupper(mb_substr($p['title'], 0, 2))) ?></span>
                    <?php endif; ?>
                  </span>
                </a>
                <div class="pb-ds__txt">
                  <h3><a href="<?= e(pp_detalle_url($v, $p)) ?>"><?= e($p['title']) ?></a></h3>
                  <p><?= e($p['description']) ?></p>
                  <p class="pb-ds__m"><?= e(category_name($p['categoria_id'])) ?><?= pp_lugar($p) !== '' ? ', ' . e(pp_lugar($p)) : '' ?></p>
                  <a class="more" href="<?= e(pp_detalle_url($v, $p)) ?>">Ver el proyecto<span class="sr">: <?= e($p['title']) ?></span><svg aria-hidden="true"><use href="#i-arr"/></svg></a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
          <div class="pb-deck__ui">
            <div class="pj__tabs" role="tablist" aria-label="Elegir proyecto destacado">
              <?php foreach ($destacados as $i => $p): ?>
                <button class="pj__tab" type="button" role="tab" id="pdt-<?= e($p['id']) ?>" aria-controls="pd-<?= e($p['id']) ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"<?= $i === 0 ? '' : ' tabindex="-1"' ?>><small><?= e(category_name($p['categoria_id'])) ?></small><?= e(pp_corto($p)) ?></button>
              <?php endforeach; ?>
            </div>
            <button class="ibtn" type="button" data-pausa aria-pressed="false" aria-label="Pausar el carrusel"><svg aria-hidden="true"><use href="#i-pause"/></svg></button>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section class="pb-arch" id="archivo" aria-label="<?= $actual === null ? 'Todos los proyectos' : 'Proyectos de ' . e($actual['nombre']) ?>">
    <nav class="pb-filtro" aria-label="Filtrar por sección">
      <div class="wrap">
        <ul class="chips">
          <li><a class="chip" href="<?= e(pp_listado_url($v)) ?>#archivo"<?= $actual === null ? ' aria-current="page"' : '' ?>>Todos<small><?= $total ?></small></a></li>
          <?php foreach ($secciones as $s): ?>
            <li><a class="chip" href="<?= e(pp_listado_url($v, $s['id'])) ?>"<?= $actual !== null && $actual['id'] === $s['id'] ? ' aria-current="page"' : '' ?>><?= e($s['nombre']) ?><small><?= $s['id'] === 'ejemplos' ? count($rubros) : (int) $s['cantidad'] ?></small></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </nav>

    <div class="wrap">
      <?php foreach ($grupos as $g):
          $todos = portfolio_section_projects($site, $g['id']);
          $lista = $actual === null ? array_slice($todos, 0, $porSeccion) : $todos;
          $anchos = pp_anchos(count($lista), $actual === null);
          $resto = count($todos) - count($lista);
          $esEjemplos = $g['id'] === 'ejemplos';
      ?>
        <section class="pb-g" id="s-<?= e($g['id']) ?>" aria-labelledby="g-<?= e($g['id']) ?>">
          <?php if ($actual === null): ?>
            <div class="pb-g__hd">
              <h2 id="g-<?= e($g['id']) ?>"><a href="<?= e(pp_listado_url($v, $g['id'])) ?>"><?= e($g['nombre']) ?></a></h2>
              <p><?= e($g['resumen']) ?></p>
              <a class="more" href="<?= e(pp_listado_url($v, $g['id'])) ?>"><?= $esEjemplos ? 'Ver la sección' : ($g['cantidad'] === 1 ? 'Ver el proyecto' : 'Ver los ' . (int) $g['cantidad']) ?><span class="sr"> de <?= e($g['nombre']) ?></span><svg aria-hidden="true"><use href="#i-arr"/></svg></a>
            </div>
          <?php else: ?>
            <h2 class="sr" id="g-<?= e($g['id']) ?>"><?= e($g['nombre']) ?></h2>
          <?php endif; ?>

          <ul class="pb-grid<?= $anchos === [8, 4, 4] ? ' pb-grid--trio' : '' ?>">
            <?php foreach ($lista as $k => $p): $w = $anchos[$k]; ?>
              <li class="pb-it pb-it--<?= $w ?><?= $k === 0 ? ' pb-it--first' : '' ?>">
                <a class="pb-it__a" href="<?= e(pp_detalle_url($v, $p)) ?>">
                  <span class="pb-it__img"><?= $imagen($p, $w === 'w' ? 12 : (int) $w) ?></span>
                  <span class="pb-it__txt">
                    <h3 class="pb-it__t"><?= e($p['title']) ?></h3>
                    <?php if ($w === 'w'): ?><span class="pb-it__d"><?= e($p['description']) ?></span><?php endif; ?>
                    <span class="pb-it__m"><?= e(pp_meta_corta($p)) ?></span>
                    <?php if ($w === 'w'): ?><span class="pb-it__mas">Ver el proyecto<svg aria-hidden="true"><use href="#i-arr"/></svg></span><?php endif; ?>
                  </span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
          <?php if ($resto > 0): ?>
            <p class="pb-g__mas"><a class="btn btn--glass" href="<?= e(pp_listado_url($v, $g['id'])) ?>">Ver los <?= (int) $g['cantidad'] ?> proyectos de <?= e(mb_strtolower($g['nombre'])) ?><svg aria-hidden="true"><use href="#i-arr"/></svg></a></p>
          <?php endif; ?>

          <?php if ($esEjemplos && $rubros):
              $cuenta = array_count_values(array_column($rubros, 'grupo'));
          ?>
            <div class="pb-ru">
              <div class="pb-ru__hd">
                <h3><?= count($rubros) ?> rubros, tres versiones cada uno</h3>
                <p>Simple-page, Multi-page y Plataforma web. Toca una para abrirla; los negocios son ficticios.</p>
              </div>
              <div class="rbx pb-rbx" data-rbx role="region" aria-roledescription="carrusel" aria-label="Rubros con ejemplos">
                <div class="chips" role="group" aria-label="Filtrar rubros">
                  <button class="chip" type="button" data-f="todos" aria-pressed="true">Todos<span><?= count($rubros) ?></span></button>
                  <?php foreach ($grupos_rubro as $gr => $nombre): if (empty($cuenta[$gr])) { continue; } ?>
                    <button class="chip" type="button" data-f="<?= e($gr) ?>" aria-pressed="false"><?= e($nombre) ?><span><?= (int) $cuenta[$gr] ?></span></button>
                  <?php endforeach; ?>
                </div>
                <ul class="rb__track" tabindex="0" aria-label="Lista de rubros, se puede deslizar">
                  <?php foreach ($rubros as $r): ?>
                    <li class="rb rb--on pb-rb" data-g="<?= e($r['grupo']) ?>">
                      <a class="pb-rb__hd" href="<?= e($r['url']) ?>"><span class="pb-rb__n"><?= e($r['nombre']) ?></span><span class="pb-rb__all">Ver el rubro<svg aria-hidden="true"><use href="#i-arr"/></svg></span></a>
                      <ul class="pb-rb__vs" aria-label="Versiones de <?= e(mb_strtolower($r['nombre'])) ?>">
                        <?php foreach ($r['versiones'] as $ver): ?>
                          <li>
                            <a class="pb-rb__v" href="<?= e($ver['url']) ?>">
                              <span class="pb-rb__img"><img src="<?= e($ver['mini']) ?>" width="600" height="375" alt="" loading="lazy"></span>
                              <span class="pb-rb__p"><?= e($ver['corto']) ?><span class="sr">: <?= e($ver['negocio']) ?>, ejemplo <?= e($ver['plan']) ?></span></span>
                            </a>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    </li>
                  <?php endforeach; ?>
                </ul>
                <div class="rbx__ui">
                  <div class="rbx__prog" aria-hidden="true"><i></i></div>
                  <button class="ibtn" type="button" data-pause aria-pressed="false" aria-label="Pausar el avance automático"><svg aria-hidden="true"><use href="#i-pause"/></svg></button>
                  <button class="ibtn" type="button" data-prev aria-label="Rubros anteriores"><svg aria-hidden="true"><use href="#i-prev"/></svg></button>
                  <button class="ibtn" type="button" data-next aria-label="Rubros siguientes"><svg aria-hidden="true"><use href="#i-next"/></svg></button>
                </div>
              </div>
              <p class="pb-ru__nota"><a href="/demos/">Ver todos los ejemplos en una página</a></p>
            </div>
          <?php endif; ?>
        </section>
      <?php endforeach; ?>

      <?php if ($actual !== null): ?>
        <p class="pb-volver"><a class="more" href="<?= e(pp_listado_url($v)) ?>"><svg aria-hidden="true"><use href="#i-back"/></svg>Todas las secciones</a></p>
      <?php endif; ?>
    </div>
  </section>

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
