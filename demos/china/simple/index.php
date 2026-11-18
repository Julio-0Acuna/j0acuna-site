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

// Índice de la carta por número, para los destacados.
$porNumero = [];
foreach ($n['carta'] as $categoria => $platos) {
    foreach ($platos as [$num, $plato, $detalle, $precio]) {
        $porNumero[$num] = ['plato' => $plato, 'detalle' => $detalle, 'precio' => $precio, 'categoria' => $categoria];
    }
}
$numero = static fn (int $num): string => str_pad((string) $num, 2, '0', STR_PAD_LEFT);
$pedirNumero = static fn (int $num) => $wa('Hola, quiero pedir el N.º ' . $num . ': ' . $porNumero[$num]['plato'] . ' (' . demo_clp($porNumero[$num]['precio']) . ').');

demo_head([
    'titulo' => $n['nombre'] . ' · Comida china en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para un restaurante chino de barrio en ' . $n['comuna'] . ', Santiago: carta numerada con QR, menús para 2, 4 y 6 personas, delivery hasta tarde y pedidos por WhatsApp.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Young+Serif&family=Rubik:wght@400;500;700&display=swap',
    'css' => ['/demos/china/simple/estilo.css'],
    'tema' => '#b3141c',
    'icono' => ['F', '#b3141c', '#f6c85f'],
    'clase_body' => 'fr',
]);
demo_aviso($rubro);
?>
<header class="fr-top">
  <div class="fr-envoltura fr-top__fila">
    <a class="fr-marca" href="#contenido">
      <span class="fr-marca__sello" aria-hidden="true">
        <svg viewBox="0 0 40 40" width="40" height="40" focusable="false"><path d="M20 3v4" stroke="currentColor" stroke-width="2"/><rect x="14" y="7" width="12" height="3" rx="1" fill="currentColor"/><ellipse cx="20" cy="21" rx="11" ry="11" fill="currentColor"/><path d="M15.5 11.5c-2.5 6-2.5 13 0 19M24.5 11.5c2.5 6 2.5 13 0 19M20 10v22" stroke="#b3141c" stroke-width="1.4" fill="none"/><rect x="14" y="31" width="12" height="3" rx="1" fill="currentColor"/><path d="M20 34v4" stroke="currentColor" stroke-width="2"/></svg>
      </span>
      <span class="fr-marca__nombre"><?= demo_e($n['nombre_corto']) ?><small>Restaurante chino<span class="fr-marca__comuna"> · <?= demo_e($n['comuna']) ?></span></small></span>
    </a>
    <nav class="fr-nav" aria-label="Secciones">
      <a href="#platos">Platos</a>
      <a href="#menus">Menús</a>
      <a href="#menu">Carta</a>
      <a href="#delivery">Delivery</a>
      <a href="#google">Ubicación</a>
    </nav>
    <a class="fr-btn fr-btn--chico" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Pedir</span></a>
  </div>
</header>

<main id="contenido">
  <section class="fr-hero" aria-labelledby="fr-titulo">
    <div class="fr-faroles" aria-hidden="true">
      <span class="fr-farol"></span><span class="fr-farol fr-farol--chico"></span><span class="fr-farol fr-farol--medio"></span>
    </div>
    <div class="fr-envoltura fr-hero__grid">
      <div class="fr-hero__texto">
        <p class="fr-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <h1 id="fr-titulo"><small>Restaurante chino</small> Farol Rojo</h1>
        <p class="fr-hero__eslogan"><?= demo_e($n['eslogan']) ?></p>
        <p class="fr-hero__bajada"><?= demo_e($n['bajada']) ?></p>
        <div class="fr-acciones">
          <a class="fr-btn" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir por WhatsApp</span></a>
          <a class="fr-btn fr-btn--crema" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
        <p class="fr-hero__tip"><strong>¿Ya sabes tu número?</strong> Escríbenos «el 14» y listo: la carta está numerada.</p>
      </div>
      <div class="fr-hero__fotos">
        <figure class="fr-luna">
          <?= demo_img($n['fotos']['hero'], 'Pollo agridulce brillante con pimentón y cebolla, recién salido del wok', ['lazy' => false, 'ancho' => 900, 'alto' => 900, 'sizes' => '(max-width: 760px) 80vw, 460px']) ?>
        </figure>
        <figure class="fr-luna fr-luna--chica">
          <?= demo_img($n['fotos']['hero_chica'], 'Wantán frito dorado en una bandeja roja', ['ancho' => 400, 'alto' => 400, 'anchos' => [300, 400, 600], 'sizes' => '180px']) ?>
        </figure>
        <p class="fr-sello" aria-hidden="true"><span>Desde</span><strong><?= (int) $n['desde'] ?></strong></p>
      </div>
    </div>
  </section>

  <div class="fr-greca" aria-hidden="true"></div>

  <section class="fr-seccion" id="platos" aria-labelledby="platos-titulo">
    <div class="fr-envoltura">
      <header class="fr-encabezado">
        <p class="fr-antetitulo">Los de siempre</p>
        <h2 id="platos-titulo">Los más pedidos del barrio</h2>
        <p>Cada plato tiene su número, igual que en la carta del mesón. Toca «Pedir» y se abre WhatsApp con tu pedido escrito.</p>
      </header>
      <div class="fr-platos">
        <?php foreach ($n['destacados'] as $d): $p = $porNumero[$d['n']]; ?>
        <article class="fr-plato">
          <div class="fr-plato__foto">
            <?= demo_img($d['foto'], $d['alt'], ['ancho' => 600, 'alto' => 600, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 560px) 90vw, (max-width: 1000px) 45vw, 260px']) ?>
            <?php if ($d['sello'] !== ''): ?><span class="fr-plato__sello"><?= demo_e($d['sello']) ?></span><?php endif; ?>
          </div>
          <p class="fr-plato__num" aria-hidden="true"><?= demo_e($numero($d['n'])) ?></p>
          <h3><span class="demo-sr">N.º <?= (int) $d['n'] ?>: </span><?= demo_e($p['plato']) ?></h3>
          <p class="fr-plato__detalle"><?= demo_e($p['detalle']) ?></p>
          <div class="fr-plato__pie">
            <strong><?= demo_e(demo_clp($p['precio'])) ?></strong>
            <a class="fr-pedir" href="<?= demo_e($pedirNumero($d['n'])) ?>" target="_blank" rel="noopener" aria-label="Pedir el número <?= (int) $d['n'] ?>, <?= demo_e($p['plato']) ?>, por WhatsApp">Pedir el <?= (int) $d['n'] ?></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="fr-seccion fr-laca" id="menus" aria-labelledby="menus-titulo">
    <div class="fr-envoltura">
      <header class="fr-encabezado fr-encabezado--centro">
        <p class="fr-antetitulo">Para compartir</p>
        <h2 id="menus-titulo">Menús para 2, 4 y 6 personas</h2>
        <p>La mesa completa en un solo pedido: entradas, arroz, tallarines y los platos de fondo que más salen.</p>
      </header>
      <div class="fr-menus">
        <?php foreach ($n['menus'] as $m): $porPersona = (int) round($m['precio'] / $m['personas'] / 10) * 10; ?>
        <article class="fr-menu<?= !empty($m['destacado']) ? ' fr-menu--destacado' : '' ?>" aria-labelledby="menu-<?= (int) $m['personas'] ?>">
          <div class="fr-menu__foto"><?= demo_img($m['foto'], $m['alt'], ['ancho' => 700, 'alto' => 440, 'anchos' => [480, 700, 1000], 'sizes' => '(max-width: 860px) 90vw, 33vw']) ?></div>
          <div class="fr-menu__cuerpo">
            <?php if (!empty($m['destacado'])): ?><p class="fr-menu__cinta">El favorito de los viernes</p><?php endif; ?>
            <h3 id="menu-<?= (int) $m['personas'] ?>"><span class="fr-menu__cifra"><?= (int) $m['personas'] ?></span> <span>personas</span></h3>
            <ul>
              <?php foreach ($m['incluye'] as $item): ?><li><?= demo_e($item) ?></li><?php endforeach; ?>
            </ul>
            <p class="fr-menu__precio"><strong><?= demo_e(demo_clp($m['precio'])) ?></strong><span>≈ <?= demo_e(demo_clp($porPersona)) ?> por persona</span></p>
            <a class="fr-btn fr-btn--oro fr-btn--bloque" href="<?= demo_e($wa('Hola, quiero el Menú para ' . $m['personas'] . ' personas (' . demo_clp($m['precio']) . ').')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Pedir menú para <?= (int) $m['personas'] ?></span></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="fr-seccion fr-carta-seccion" id="menu" aria-labelledby="menu-titulo">
    <div class="fr-envoltura">
      <div class="fr-carta">
        <header class="fr-carta__cabeza">
          <p class="fr-antetitulo">Carta completa</p>
          <h2 id="menu-titulo">Pide por número</h2>
          <p>Escríbenos el número y la cantidad («2 del 1 y uno del 14») o toca «Pedir» al lado de cada plato.</p>
        </header>
        <div class="fr-carta__cuerpo">
          <div class="fr-carta__columnas">
            <?php foreach ($n['carta'] as $categoria => $platos): ?>
            <div class="fr-carta__grupo">
              <h3><?= demo_e($categoria) ?></h3>
              <ul>
                <?php foreach ($platos as [$num, $plato, $detalle, $precio]): ?>
                <li>
                  <span class="fr-carta__num" aria-hidden="true"><?= demo_e($numero($num)) ?></span>
                  <span class="fr-carta__texto">
                    <span class="fr-carta__linea"><strong><span class="demo-sr">N.º <?= (int) $num ?>: </span><?= demo_e($plato) ?></strong><span class="fr-carta__puntos" aria-hidden="true"></span><span class="fr-carta__precio"><?= demo_e(demo_clp($precio)) ?></span></span>
                    <span class="fr-carta__detalle"><?= demo_e($detalle) ?> <a href="<?= demo_e($pedirNumero($num)) ?>" target="_blank" rel="noopener" aria-label="Pedir el número <?= (int) $num ?>, <?= demo_e($plato) ?>, por WhatsApp">Pedir</a></span>
                  </span>
                </li>
                <?php endforeach; ?>
              </ul>
            </div>
            <?php endforeach; ?>
          </div>
          <aside class="fr-carta__lado" aria-label="La carta en tu celular">
            <?php demo_qr('#menu', 'Escanea y abre la carta en tu celular', 'fr-qr'); ?>
            <p>Este código va en las mesas, en la caja y pegado en cada bolsa de delivery: el cliente vuelve a pedir en un toque.</p>
            <figure class="fr-carta__foto">
              <?= demo_img($n['fotos']['sopa'], 'Sopa wantán con cebollín en una bandeja de madera', ['ancho' => 500, 'alto' => 500, 'anchos' => [300, 500, 800], 'sizes' => '(max-width: 860px) 60vw, 240px']) ?>
              <figcaption>N.º 05 · Sopa wantán</figcaption>
            </figure>
          </aside>
        </div>
      </div>
    </div>
  </section>

  <section class="fr-delivery" id="delivery" aria-labelledby="delivery-titulo">
    <figure class="fr-delivery__foto">
      <?= demo_img($n['fotos']['moto'], 'Repartidor en moto roja con caja de delivery, de noche en la ciudad', ['ancho' => 1000, 'alto' => 700, 'anchos' => [600, 1000, 1400], 'sizes' => '(max-width: 860px) 100vw, 50vw']) ?>
    </figure>
    <div class="fr-delivery__texto">
      <p class="fr-antetitulo">Delivery hasta tarde</p>
      <h2 id="delivery-titulo">Viernes y sábado, hasta la 1:30</h2>
      <p>De domingo a jueves despachamos hasta las 23:30. Lo preparamos al momento y sale en caja térmica: el wantán llega crujiente.</p>
      <ul class="fr-comunas" aria-label="Costo de despacho por comuna">
        <?php foreach ($n['despacho'] as $comuna => $costo): ?>
        <li><span><?= demo_e($comuna) ?></span><strong><?= demo_e(demo_clp($costo)) ?></strong></li>
        <?php endforeach; ?>
      </ul>
      <p class="fr-delivery__gratis">Gratis en <?= demo_e($n['comuna']) ?> sobre <?= demo_e(demo_clp($n['despacho_gratis'])) ?>.</p>
      <a class="fr-btn fr-btn--oro" href="<?= demo_e($wa('Hola, quiero pedir delivery. ¿Llegan a mi dirección?')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir delivery</span></a>
    </div>
  </section>

  <section class="fr-seccion" id="horario" aria-labelledby="horario-titulo">
    <div class="fr-envoltura fr-horario">
      <div class="fr-horario__tabla">
        <p class="fr-antetitulo">Te esperamos</p>
        <h2 id="horario-titulo">Horario</h2>
        <p class="fr-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario fr-tabla-horario'); ?>
        <p class="fr-pequeno">La cocina recibe pedidos hasta 30 minutos antes del cierre.</p>
      </div>
      <ul class="fr-datos">
        <li><strong>En el local</strong><span>Comedor para 50 personas y mesas redondas para cumpleaños.</span></li>
        <li><strong>Para llevar</strong><span>Pide por WhatsApp y retira en 20 minutos, sin hacer fila.</span></li>
        <li><strong>Pago</strong><span>Efectivo, débito, crédito o transferencia.</span></li>
      </ul>
      <figure class="fr-horario__foto">
        <?= demo_img($n['fotos']['wok'], 'Cocinero salteando en un wok con una gran llamarada', ['ancho' => 600, 'alto' => 760, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 860px) 90vw, 30vw']) ?>
        <figcaption>Todo sale del wok, a fuego alto.</figcaption>
      </figure>
    </div>
  </section>

  <div class="fr-seccion fr-seccion--papel">
    <div class="fr-envoltura">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería tu ficha en Google Maps: horario, cómo llegar y lo que dicen tus clientes.']); ?>
    </div>
  </div>

  <section class="fr-cierre" aria-labelledby="cierre-titulo">
    <div class="fr-envoltura">
      <h2 id="cierre-titulo">¿Qué número va hoy?</h2>
      <p>Escríbenos y en 20 minutos está listo para retirar.</p>
      <div class="fr-acciones">
        <a class="fr-btn fr-btn--oro" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir por WhatsApp</span></a>
        <a class="fr-btn fr-btn--borde" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
      </div>
    </div>
  </section>
</main>

<footer class="fr-pie">
  <div class="fr-envoltura">
    <p class="fr-pie__marca"><?= demo_e($n['nombre']) ?></p>
    <p><?= demo_e($n['comuna']) ?>, Santiago · Desde <?= (int) $n['desde'] ?> · Pedidos por WhatsApp</p>
    <?php demo_aviso_pie($rubro); ?>
  </div>
</footer>

<nav class="fr-barra" aria-label="Pedir">
  <a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
</nav>
<?php demo_scripts(['qr']); ?>
</body>
</html>
