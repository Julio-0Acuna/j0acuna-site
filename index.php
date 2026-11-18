<?php
require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$featured = portfolio_featured_projects($site, 6);
$services = portfolio_visible_services($site, 'todos');
$wa = site_wa($site, $site['profile']['whatsapp_mensaje']);
$rubros = portfolio_rubros();

// Selector de versiones: el primer rubro con ejemplos (la pizzería es la referencia).
$referencia = null;
foreach ($rubros as $r) {
    if ($r['disponible'] && count($r['datos']['versiones'] ?? []) === 3) {
        $referencia = $r;
        if ($r['slug'] === 'pizzeria') {
            break;
        }
    }
}
$versiones = [
    'simple' => ['Una sola página, rápida en el celular', 'Menú con QR y botón de WhatsApp', 'Horario, mapa y sección de Google'],
    'multi' => ['Inicio, menú, nosotros, preguntas y contacto', 'Platos en 3D y «Ver en tu mesa»', 'Pedido armado que llega por WhatsApp'],
    'plataforma' => ['Tienda online con cuentas de clientes', 'Panel del dueño con pedidos y stock', 'Retiro en el local o despacho por comuna'],
];

// Dos capturas de servicios para la composición en 3D (tienda online y menú QR, si están).
$capturas = [];
foreach (['tienda-online', 'menu-qr-3d'] as $id) {
    foreach ($services as $s) {
        if ($s['id'] === $id && $s['imagen'] !== '') {
            $capturas[] = $s['imagen'];
        }
    }
}
if (count($capturas) < 2) {
    $capturas = array_slice(array_values(array_filter(array_column($services, 'imagen'))), 0, 2);
}

render_partial('head', [
    'title' => $site['profile']['name'] . ' | Desarrollador web en ' . $site['profile']['ciudad'] . ', Chile',
    'description' => $site['profile']['tagline'],
    'og_image' => 'assets/img/og/og-inicio.jpg',
    'canonical' => '/',
    'jsonld' => [jsonld_business($site)],
]);
render_partial('nav', ['site' => $site, 'actual' => 'inicio']);
?>
<main id="main">
  <section class="hero" id="inicio">
    <div class="wrap hero__in">
      <p class="badge"><i aria-hidden="true"></i><?= e($site['profile']['ciudad']) ?> · negocios de <?= e($site['profile']['area']) ?></p>
      <h1><?= titulo_grad($site['profile']['headline'], 2) ?></h1>
      <p class="sub">Soy <?= e($site['profile']['name']) ?>, desarrollador web en <?= e($site['profile']['ciudad']) ?>. Página web, menú QR, pedidos por WhatsApp, tiendas online y sistemas a medida, pensados para el celular.</p>
      <div class="actions">
        <?php if ($wa !== ''): ?>
          <a class="btn btn--light" href="<?= e($wa) ?>" target="_blank" rel="noopener"><svg aria-hidden="true"><use href="#i-wa"/></svg>Escríbeme por WhatsApp</a>
        <?php endif; ?>
        <a class="btn btn--glass" href="#rubros">Ver ejemplos<svg aria-hidden="true"><use href="#i-down"/></svg></a>
      </div>
    </div>
    <div class="stage" id="stage" role="img" aria-label="Tres celulares con los ejemplos de pizzería: Simple-page, Multi-page y Plataforma web">
      <div class="ph ph--l"><span class="ph__tag">Simple-page</span><img src="<?= e(asset('assets/img/inicio/movil-simple.webp')) ?>" width="620" height="1256" alt=""></div>
      <div class="ph ph--r"><span class="ph__tag">Plataforma web</span><img src="<?= e(asset('assets/img/inicio/movil-plataforma.webp')) ?>" width="620" height="1256" alt=""></div>
      <div class="ph ph--c"><span class="ph__tag">Multi-page</span><img src="<?= e(asset('assets/img/inicio/movil-multi.webp')) ?>" width="620" height="1256" alt="" fetchpriority="high"></div>
    </div>
  </section>

  <hr class="hr">

  <section class="sec" id="rubros" aria-labelledby="t-rubros">
    <div class="wrap">
      <div class="sec__head rv">
        <p class="kicker"><b>01</b> Ejemplos por rubro</p>
        <h2 class="h2" id="t-rubros">Elige tu rubro. <span class="soft">Mira cómo se vería tu negocio.</span></h2>
      </div>
    </div>
    <?php render_partial('rubros-carrusel', ['site' => $site, 'rubros' => $rubros]); ?>

    <?php if ($referencia !== null): $vs = $referencia['datos']['versiones']; ?>
      <div class="wrap">
        <div class="vx" data-vx>
          <div class="vx__txt rv">
            <p class="kicker"><b>3</b> versiones por rubro</p>
            <h3>Parte simple. Crece cuando tu negocio lo pida.</h3>
            <div class="seg" role="tablist" aria-label="Versiones del ejemplo de <?= e(mb_strtolower($referencia['nombre'])) ?>">
              <?php $k = 0; foreach ($vs as $clave => $v): ?>
                <button type="button" role="tab" id="vx-tab-<?= e($clave) ?>" aria-controls="vx-<?= e($clave) ?>" aria-selected="<?= $k === 0 ? 'true' : 'false' ?>" tabindex="<?= $k === 0 ? '0' : '-1' ?>" data-img="<?= $k ?>" data-url="<?= e($referencia['slug'] . ' · ' . mb_strtolower($v['plan'])) ?>"><?= e($v['plan']) ?></button>
              <?php $k++; endforeach; ?>
            </div>
            <?php $k = 0; foreach ($vs as $clave => $v): ?>
              <div class="vx__panel" role="tabpanel" id="vx-<?= e($clave) ?>" aria-labelledby="vx-tab-<?= e($clave) ?>"<?= $k > 0 ? ' hidden' : '' ?>>
                <ul>
                  <?php foreach ($versiones[$clave] ?? [] as $linea): ?><li><?= e($linea) ?></li><?php endforeach; ?>
                </ul>
                <p class="vx__ej">Ejemplo: <strong><?= e($v['negocio']) ?></strong>, <?= e($v['comuna']) ?> (negocio ficticio).</p>
                <a class="more" href="<?= e($v['url']) ?>">Abrir el ejemplo<svg aria-hidden="true"><use href="#i-arr"/></svg></a>
              </div>
            <?php $k++; endforeach; ?>
          </div>
          <figure class="frame rv" aria-label="Vista previa de la versión elegida">
            <div class="frame__bar" aria-hidden="true"><i></i><i></i><i></i><span data-url><?= e($referencia['slug'] . ' · ' . mb_strtolower(reset($vs)['plan'])) ?></span></div>
            <div class="frame__img">
              <?php $k = 0; foreach ($vs as $clave => $v): ?>
                <img<?= $k === 0 ? ' class="is-on"' : '' ?> src="<?= e($referencia['url'] . 'img/' . $v['captura'] . '.webp') ?>" width="800" height="600" alt="<?= e($v['negocio']) ?>, ejemplo <?= e($v['plan']) ?>" loading="lazy">
              <?php $k++; endforeach; ?>
            </div>
          </figure>
        </div>
      </div>
    <?php endif; ?>
  </section>

  <?php if ($services): ?>
    <hr class="hr">
    <section class="sec" id="servicios" aria-labelledby="t-svc">
      <div class="wrap">
        <div class="sec__head sec__head--row rv">
          <p class="kicker"><b>02</b> Servicios</p>
          <h2 class="h2" id="t-svc">Todo lo que tu negocio <span class="soft">necesita para vender en internet.</span></h2>
          <a class="more" href="/servicios">Ver todos los servicios<svg aria-hidden="true"><use href="#i-arr"/></svg></a>
        </div>
        <div class="svc<?= count($capturas) < 2 ? ' svc--solo' : '' ?>">
          <ul class="svc__list">
            <?php foreach ($services as $s): ?>
              <li class="rv">
                <a class="svc__item" href="/servicios#<?= e($s['id']) ?>">
                  <span class="ico" aria-hidden="true"><?php render_partial('icono', ['icono' => servicio_icono($s), 'tam' => 22]); ?></span>
                  <div><h3><?= e($s['nombre_corto']) ?></h3><p><?= e(servicio_linea($s)) ?></p></div>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
          <?php if (count($capturas) >= 2): ?>
            <div class="svc__art rv" aria-hidden="true">
              <div class="shot shot--a"><img src="<?= e(asset($capturas[0])) ?>" width="1200" height="750" alt="" loading="lazy"></div>
              <div class="shot shot--b"><img src="<?= e(asset($capturas[1])) ?>" width="1200" height="750" alt="" loading="lazy"></div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php if ($featured): ?>
    <hr class="hr">
    <section class="sec" id="destacados" aria-labelledby="t-proy">
      <div class="wrap">
        <div class="sec__head sec__head--row rv">
          <p class="kicker"><b>03</b> Proyectos destacados</p>
          <h2 class="h2" id="t-proy">Sistemas que se usan <span class="soft">todos los días.</span></h2>
          <a class="more" href="/proyectos">Ver todos los proyectos<svg aria-hidden="true"><use href="#i-arr"/></svg></a>
        </div>
        <?php $varios = count($featured) > 1; ?>
        <div class="pj rv"<?= $varios ? ' data-carrusel data-intervalo="6500" role="region" aria-roledescription="carrusel" aria-label="Proyectos destacados"' : '' ?>>
          <div class="pj__stage">
            <?php foreach ($featured as $i => $p): ?>
              <article class="pj__s<?= $i === 0 ? ' is-on' : '' ?>" id="pj-<?= e($p['id']) ?>" data-slide<?= $varios ? ' role="tabpanel" aria-labelledby="pt-' . e($p['id']) . '"' : '' ?>>
                <a class="frame" href="<?= e(project_url($p)) ?>" tabindex="-1" aria-hidden="true">
                  <span class="frame__bar"><i></i><i></i><i></i><span><?= e(proyecto_barra($p)) ?></span></span>
                  <span class="frame__img">
                    <?php if ($p['image_url'] !== ''): ?>
                      <img src="<?= e(asset($p['image_url'])) ?>" width="1200" height="750" alt="" loading="lazy">
                    <?php else: ?>
                      <span class="frame__ph"><?= e(mb_strtoupper(mb_substr($p['title'], 0, 2))) ?></span>
                    <?php endif; ?>
                  </span>
                </a>
                <div class="pj__txt">
                  <p class="kicker"><?= e(implode(' · ', array_filter([category_name($p['categoria_id']), $p['pais']]))) ?></p>
                  <h3><?= e($p['title']) ?></h3>
                  <p><?= e($p['description']) ?></p>
                  <a class="more" href="<?= e(project_url($p)) ?>">Ver proyecto<span class="sr">: <?= e($p['title']) ?></span><svg aria-hidden="true"><use href="#i-arr"/></svg></a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
          <?php if ($varios): ?>
            <div class="pj__ui">
              <div class="pj__tabs" role="tablist" aria-label="Elegir proyecto">
                <?php foreach ($featured as $i => $p): ?>
                  <button class="pj__tab" type="button" role="tab" id="pt-<?= e($p['id']) ?>" aria-controls="pj-<?= e($p['id']) ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"<?= $i === 0 ? '' : ' tabindex="-1"' ?>><small><?= sprintf('%02d', $i + 1) ?></small><?= e(proyecto_corto($p)) ?></button>
                <?php endforeach; ?>
              </div>
              <button class="ibtn" type="button" data-pausa aria-pressed="false" aria-label="Pausar el carrusel"><svg aria-hidden="true"><use href="#i-pause"/></svg></button>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <hr class="hr">
  <?php render_partial('pasos', ['numero' => '04', 'titulo_id' => 't-proc']); ?>

  <?php render_partial('cta', [
      'site' => $site,
      'titulo' => '¿Conversamos?',
      'texto' => $site['profile']['contact_text'],
      'secundario' => ['Enviar un mensaje', '/contacto'],
  ]); ?>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $site['profile']['whatsapp_mensaje']]);
render_partial('footer', ['site' => $site]);
