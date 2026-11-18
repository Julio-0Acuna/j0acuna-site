<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version);

/** Enlace de WhatsApp con la nota de pedido de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);
$waGeneral = $wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.');
$estado = demo_horario_estado($n['horario']);

// Menú del día: el de hoy (o, si hoy no hay, el del próximo día que haya).
$ahora = new DateTimeImmutable('now', new DateTimeZone(DEMO_ZONA_HORARIA));
$md = $n['menu_dia'];
$hoy = (int) $ahora->format('N');
$diaMenu = $hoy;
while (!isset($md['dias'][$diaMenu])) {
    $diaMenu = $diaMenu % 7 + 1;
}
$esHoy = $diaMenu === $hoy;
$nombreDia = mb_strtolower(DEMO_DIAS[$diaMenu]);
$menuHoy = $md['dias'][$diaMenu];
$etiquetaDia = $esHoy ? 'Hoy, ' . $nombreDia : ($diaMenu === $hoy % 7 + 1 ? 'Mañana, ' . $nombreDia : 'El ' . $nombreDia);
$pedirMenu = static fn (string $segundo, bool $postre) => $wa(
    'Hola, quiero 1 menú del día del ' . $nombreDia . ': ' . $menuHoy['entrada'] . ' + ' . $segundo
    . ($postre ? ' + ' . $menuHoy['postre'] : '') . ', con refresco (' . demo_clp($postre ? $md['precio_postre'] : $md['precio']) . ').'
);

demo_head([
    'titulo' => $n['nombre'] . ' · Comida peruana en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para una picantería peruana en ' . $n['comuna'] . ', Santiago: menú del día, carta con QR, promociones y pedidos por WhatsApp.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;700;800&family=Bungee&display=swap',
    'css' => ['/demos/peruana/simple/estilo.css'],
    'tema' => '#0e0c10',
    'icono' => ['R', '#0e0c10', '#ffe03a'],
    'clase_body' => 'ro',
]);
demo_aviso($rubro);
?>
<header class="ro-top">
  <div class="ro-envoltura ro-top__fila">
    <a class="ro-marca" href="#contenido" aria-label="<?= demo_e($n['nombre']) ?>, inicio">
      <svg class="ro-marca__rocoto" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false">
        <path d="M24 13c-9 0-14 7-14 15 0 9 6 15 14 15s14-6 14-15c0-8-5-15-14-15z" fill="#e8202a"/>
        <path d="M18 22c1.5-3 4-4.5 7-4.5" fill="none" stroke="#ff8a8a" stroke-width="2.5" stroke-linecap="round"/>
        <path d="M24 13c0-4 2-7 6-8" fill="none" stroke="#22d17a" stroke-width="3.5" stroke-linecap="round"/>
        <path d="M17 12c3 2 11 2 14 0" fill="none" stroke="#22d17a" stroke-width="3.5" stroke-linecap="round"/>
      </svg>
      <span class="ro-marca__nombre">Rocoto <span>de Oro</span></span>
    </a>
    <nav class="ro-nav" aria-label="Secciones">
      <a href="#menu-del-dia">Menú del día</a>
      <a href="#platos">Platos</a>
      <a href="#promos">Promos</a>
      <a href="#carta">Carta</a>
      <a href="#horario">Horario</a>
    </nav>
    <a class="ro-btn ro-btn--chico" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Pedir</span></a>
  </div>
</header>

<main id="contenido">
  <section class="ro-hero" aria-labelledby="ro-titulo">
    <div class="ro-envoltura ro-hero__grid">
      <div class="ro-hero__texto">
        <p class="ro-pegatina">Picantería · <?= demo_e($n['comuna']) ?></p>
        <h1 id="ro-titulo" class="ro-hero__titulo"><span class="ro-chicha">El Rocoto</span> <span class="ro-chicha ro-chicha--b">de Oro</span></h1>
        <p class="ro-hero__eslogan"><?= demo_e($n['eslogan']) ?></p>
        <p class="ro-hero__bajada"><?= demo_e($n['bajada']) ?></p>
        <p class="ro-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <div class="ro-acciones">
          <a class="ro-btn" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir por WhatsApp</span></a>
          <a class="ro-btn ro-btn--borde" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
        <a class="ro-boleto" href="#menu-del-dia">
          <span class="ro-boleto__dia"><?= demo_e($etiquetaDia) ?></span>
          <span class="ro-boleto__texto">Menú del día: <strong><?= demo_e($menuHoy['segundos'][0]) ?></strong> o <strong><?= demo_e($menuHoy['segundos'][1]) ?></strong></span>
          <span class="ro-boleto__precio"><?= demo_e(demo_clp($md['precio'])) ?></span>
        </a>
      </div>
      <div class="ro-hero__foto">
        <figure class="ro-marco">
          <?= demo_img($n['hero_foto'], 'Ceviche mixto con chicharrón de calamar, camote y choclo en un plato hondo', ['lazy' => false, 'ancho' => 900, 'alto' => 1000, 'sizes' => '(max-width: 820px) 88vw, 460px']) ?>
        </figure>
        <p class="ro-estrella" aria-hidden="true"><span>¡Ceviche fresco<br>todo el día!</span></p>
      </div>
    </div>
  </section>

  <div class="ro-cinta" aria-hidden="true">
    <div class="ro-cinta__pista">
      <?php for ($i = 0; $i < 2; $i++): ?>
      <span>Ceviche</span><span>Lomo saltado</span><span>Arroz chaufa</span><span>Anticuchos</span><span>Chicha morada</span><span>Causa limeña</span><span>Ají de gallina</span><span>Picarones</span>
      <?php endfor; ?>
    </div>
  </div>

  <section class="ro-dia" id="menu-del-dia" aria-labelledby="dia-titulo">
    <div class="ro-envoltura">
      <div class="ro-dia__cabeza">
        <div>
          <p class="ro-antetitulo"><?= demo_e($md['horario']) ?></p>
          <h2 id="dia-titulo" class="ro-titulo-cartel">Menú del día</h2>
          <p class="ro-dia__incluye"><?= demo_e($md['incluye']) ?>. Con postre: <?= demo_e(demo_clp($md['precio_postre'])) ?>.</p>
        </div>
        <p class="ro-precio-estallido"><span>Solo</span><strong><?= demo_e(demo_clp($md['precio'])) ?></strong></p>
      </div>

      <div class="ro-dia__grid">
        <article class="ro-hoy" aria-labelledby="hoy-titulo">
          <p class="ro-hoy__dia"><?= demo_e($etiquetaDia) ?></p>
          <?php if (!$esHoy): ?><p class="ro-hoy__aviso">Hoy <?= demo_e(mb_strtolower(DEMO_DIAS[$hoy])) ?> atendemos solo a la carta. Así viene el próximo menú:</p><?php endif; ?>
          <h3 id="hoy-titulo" class="demo-sr">Menú del <?= demo_e($nombreDia) ?></h3>
          <dl class="ro-hoy__lista">
            <div><dt>Entrada</dt><dd><?= demo_e($menuHoy['entrada']) ?></dd></div>
            <div><dt>Refresco</dt><dd>Chicha morada o maracuyá</dd></div>
            <div><dt>Postre</dt><dd><?= demo_e($menuHoy['postre']) ?> <small>(+ <?= demo_e(demo_clp($md['precio_postre'] - $md['precio'])) ?>)</small></dd></div>
          </dl>
          <p class="ro-hoy__elige">Elige tu segundo:</p>
          <ul class="ro-segundos">
            <?php foreach ($menuHoy['segundos'] as $segundo): ?>
            <li>
              <strong><?= demo_e($segundo) ?></strong>
              <span class="ro-segundos__acciones">
                <a class="ro-btn ro-btn--chico" href="<?= demo_e($pedirMenu($segundo, false)) ?>" target="_blank" rel="noopener" aria-label="Pedir menú del <?= demo_e($nombreDia) ?> con <?= demo_e($segundo) ?> por WhatsApp, <?= demo_e(demo_clp($md['precio'])) ?>"><?= demo_icono_wa(16) ?><span>Pedir · <?= demo_e(demo_clp($md['precio'])) ?></span></a>
                <a class="ro-enlace" href="<?= demo_e($pedirMenu($segundo, true)) ?>" target="_blank" rel="noopener">Con postre · <?= demo_e(demo_clp($md['precio_postre'])) ?></a>
              </span>
            </li>
            <?php endforeach; ?>
          </ul>
        </article>

        <div class="ro-semana">
          <h3 class="ro-semana__titulo">La semana</h3>
          <ol class="ro-semana__lista">
            <?php foreach ($md['dias'] as $d => $dia): ?>
            <li class="ro-semana__dia<?= $d === $diaMenu ? ' is-hoy' : '' ?>"<?= $d === $diaMenu ? ' aria-current="date"' : '' ?>>
              <p class="ro-semana__nombre"><?= demo_e(DEMO_DIAS[$d]) ?><?php if ($d === $hoy): ?> <span>hoy</span><?php endif; ?></p>
              <p><?= demo_e($dia['entrada']) ?></p>
              <p><strong><?= demo_e($dia['segundos'][0]) ?></strong> o <strong><?= demo_e($dia['segundos'][1]) ?></strong></p>
            </li>
            <?php endforeach; ?>
          </ol>
          <p class="ro-semana__nota">Domingo: solo a la carta, de 12:00 a 17:00.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="ro-platos" id="platos" aria-labelledby="platos-titulo">
    <div class="ro-envoltura">
      <p class="ro-antetitulo ro-antetitulo--neon">A la carta, todo el día</p>
      <h2 id="platos-titulo" class="ro-titulo-cartel ro-titulo-cartel--claro">Los que no fallan</h2>
      <div class="ro-platos__grid">
        <?php foreach ($n['estrellas'] as [$id, $plato, $detalle, $precio, $foto, $sello]): ?>
        <article class="ro-plato" id="plato-<?= demo_e($id) ?>">
          <div class="ro-plato__foto">
            <?= demo_img($foto, $plato, ['ancho' => 600, 'alto' => 480, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 640px) 92vw, (max-width: 1000px) 46vw, 340px']) ?>
            <p class="ro-plato__precio"><?= demo_e(demo_clp($precio)) ?></p>
            <?php if ($sello !== ''): ?><p class="ro-plato__sello"><?= demo_e($sello) ?></p><?php endif; ?>
          </div>
          <div class="ro-plato__texto">
            <h3><?= demo_e($plato) ?></h3>
            <p><?= demo_e($detalle) ?></p>
            <a class="ro-btn ro-btn--chico ro-btn--bloque" href="<?= demo_e($wa('Hola, quiero pedir 1 ' . $plato . ' (' . demo_clp($precio) . ').')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($plato) ?> por WhatsApp, <?= demo_e(demo_clp($precio)) ?>"><?= demo_icono_wa(16) ?><span>Lo quiero</span></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <p class="ro-ayuda">Toca «Lo quiero» y se abre WhatsApp con tu pedido escrito.</p>
    </div>
  </section>

  <div class="ro-flecos" aria-hidden="true"></div>

  <section class="ro-promos" id="promos" aria-labelledby="promos-titulo">
    <div class="ro-envoltura">
      <p class="ro-antetitulo">Para compartir</p>
      <h2 id="promos-titulo" class="ro-titulo-cartel">Promociones</h2>
      <div class="ro-carteles">
        <?php foreach ($n['promos'] as $promo): ?>
        <article class="ro-cartel ro-cartel--<?= demo_e($promo['color']) ?>">
          <h3><?= demo_e($promo['titulo']) ?></h3>
          <p class="ro-cartel__texto"><?= demo_e($promo['texto']) ?></p>
          <p class="ro-cartel__precio"><?php if ($promo['antes'] !== ''): ?><s aria-label="Antes <?= demo_e($promo['antes']) ?>"><?= demo_e($promo['antes']) ?></s> <?php endif; ?><strong><?= demo_e($promo['precio']) ?></strong></p>
          <?php if (!empty($promo['foto'])): ?>
          <div class="ro-cartel__foto"><?= demo_img($promo['foto'], $promo['titulo'], ['ancho' => 300, 'alto' => 300, 'anchos' => [300, 600], 'sizes' => '140px']) ?></div>
          <?php endif; ?>
          <a class="ro-btn ro-btn--negro" href="<?= demo_e($wa($promo['mensaje'])) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>La quiero</span></a>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="ro-carta" id="carta" aria-labelledby="carta-titulo">
    <div class="ro-envoltura">
      <p class="ro-antetitulo ro-antetitulo--neon">Todo lo que cocinamos</p>
      <h2 id="carta-titulo" class="ro-titulo-cartel ro-titulo-cartel--claro">La carta</h2>
      <div class="ro-carta__grid">
        <div class="ro-carta__columnas">
          <?php foreach ($n['carta'] as $categoria => $platos): ?>
          <div class="ro-carta__grupo">
            <h3><?= demo_e($categoria) ?></h3>
            <ul>
              <?php foreach ($platos as [$plato, $detalle, $precio]): ?>
              <li>
                <span class="ro-carta__linea"><strong><?= demo_e($plato) ?></strong><span class="ro-carta__puntos" aria-hidden="true"></span><span class="ro-carta__precio"><?= demo_e($precio) ?></span></span>
                <span class="ro-carta__detalle"><?= demo_e($detalle) ?>
                  <a href="<?= demo_e($wa('Hola, quiero pedir: ' . $plato . ' (' . $precio . ').')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($plato) ?> por WhatsApp">Pedir</a></span>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endforeach; ?>
        </div>
        <aside class="ro-carta__lado" aria-label="La carta en tu celular">
          <div class="ro-qr-caja">
            <p class="ro-qr-caja__titulo">La carta en tu mesa</p>
            <?php demo_qr('#carta', 'Escanea y abre esta carta en tu celular', 'ro-qr'); ?>
            <p>Este código va pegado en cada mesa y en la caja: tus clientes ven la carta y piden por WhatsApp.</p>
          </div>
          <figure class="ro-carta__foto">
            <?= demo_img($n['cancha_foto'], 'Cancha serrana tostada en un pocillo, con limones', ['ancho' => 600, 'alto' => 600, 'anchos' => [400, 600], 'sizes' => '(max-width: 900px) 92vw, 300px']) ?>
            <figcaption>Cancha tostada de cortesía con todos los ceviches.</figcaption>
          </figure>
        </aside>
      </div>
    </div>
  </section>

  <section class="ro-horario" id="horario" aria-labelledby="horario-titulo">
    <div class="ro-envoltura ro-horario__grid">
      <div>
        <p class="ro-antetitulo">Te esperamos</p>
        <h2 id="horario-titulo" class="ro-titulo-cartel">Horario y delivery</h2>
        <p class="ro-estado ro-estado--claro <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario ro-tabla-horario'); ?>
      </div>
      <ul class="ro-entregas">
        <li><strong>Para comer aquí</strong><span>28 puestos y la tele con el partido. Menú del día de 12:00 a 16:00.</span></li>
        <li><strong>Retiro en el local</strong><span>Pide por WhatsApp y pasa a buscarlo en 20 minutos.</span></li>
        <li><strong>Delivery propio</strong><span><?php $partes = []; foreach ($n['comunas_delivery'] as $c => $costo) { $partes[] = $c . ' ' . demo_clp($costo); } echo demo_e(implode(' · ', $partes)); ?>. Gratis sobre $20.000.</span></li>
        <li><strong>Pago</strong><span>Efectivo, débito, crédito o transferencia.</span></li>
      </ul>
    </div>
  </section>

  <div class="ro-google">
    <div class="ro-envoltura">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería tu ficha en Google Maps: horario, cómo llegar y reseñas de tus clientes.']); ?>
    </div>
  </div>

  <section class="ro-cierre" aria-labelledby="cierre-titulo">
    <div class="ro-envoltura">
      <h2 id="cierre-titulo">¿Se te antojó?</h2>
      <p>Escríbenos y tu ceviche sale de la cocina en 15 minutos.</p>
      <div class="ro-acciones">
        <a class="ro-btn ro-btn--negro" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir por WhatsApp</span></a>
        <a class="ro-btn ro-btn--borde-negro" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
      </div>
    </div>
  </section>
</main>

<footer class="ro-pie">
  <div class="ro-envoltura">
    <p class="ro-pie__marca"><?= demo_e($n['nombre']) ?></p>
    <p><?= demo_e($n['comuna']) ?>, Santiago · Comida peruana · Pedidos por WhatsApp</p>
    <?php demo_aviso_pie($rubro); ?>
  </div>
</footer>

<nav class="ro-barra" aria-label="Pedir">
  <a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
</nav>
<?php demo_scripts(['qr']); ?>
</body>
</html>
