<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version);

/** Enlace de WhatsApp con la nota de pedido de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);
$waGeneral = $wa('Hola, quiero encargar una torta en ' . $n['nombre'] . '.');
$estado = demo_horario_estado($n['horario']);

// Vitrina del día: si hoy cerramos, se muestra la del próximo día abierto.
$ahora = new DateTimeImmutable('now', new DateTimeZone(DEMO_ZONA_HORARIA));
$diaVitrina = (int) $ahora->format('N');
$cuandoVitrina = 'Hoy';
for ($i = 0; $i < 7 && empty($n['horario'][$diaVitrina]); $i++) {
    $diaVitrina = $diaVitrina % 7 + 1;
    $cuandoVitrina = $i === 0 ? 'Mañana' : 'El ' . mb_strtolower(DEMO_DIAS[$diaVitrina]);
}
$hay = static fn (array $p): bool => $p['dias'] === [] || in_array($diaVitrina, $p['dias'], true);
$vitrina = $n['vitrina'];
usort($vitrina, static fn (array $a, array $b): int => (int) $hay($b) <=> (int) $hay($a));
/** "Solo mié, sáb y dom" */
$soloDias = static function (array $dias): string {
    $nombres = array_map(static fn (int $d): string => mb_strtolower(DEMO_DIAS_CORTOS[$d]), $dias);
    $ultimo = array_pop($nombres);
    return 'Solo ' . ($nombres ? implode(', ', $nombres) . ' y ' : '') . $ultimo;
};

/** Blonda de papel (SVG): borde festoneado y calado, como la que va bajo las tortas. */
$blonda = static function (int $festones = 30): string {
    $c = 200;
    $r = 186;
    $puntos = [];
    for ($i = 0; $i <= $festones; $i++) {
        $a = 2 * M_PI * $i / $festones;
        $puntos[] = [$c + $r * cos($a), $c + $r * sin($a)];
    }
    $radio = $r * sin(M_PI / $festones) * 1.05;
    $d = sprintf('M%.1f %.1f', ...$puntos[0]);
    for ($i = 1; $i <= $festones; $i++) {
        $d .= sprintf(' A%.1f %.1f 0 0 1 %.1f %.1f', $radio, $radio, ...$puntos[$i]);
    }
    $agujeros = '';
    for ($i = 0; $i < $festones; $i++) {
        $a = 2 * M_PI * ($i + .5) / $festones;
        $agujeros .= sprintf('<circle cx="%.1f" cy="%.1f" r="4.2"/>', $c + 168 * cos($a), $c + 168 * sin($a));
        $agujeros .= sprintf('<circle cx="%.1f" cy="%.1f" r="2.4"/>', $c + 150 * cos($a + M_PI / $festones), $c + 150 * sin($a + M_PI / $festones));
    }
    return '<svg class="de-blonda" viewBox="0 0 400 400" aria-hidden="true" focusable="false"><path class="de-blonda__papel" d="' . $d . ' Z"/>'
        . '<g class="de-blonda__calado">' . $agujeros . '</g><circle class="de-blonda__linea" cx="200" cy="200" r="140"/></svg>';
};

demo_head([
    'titulo' => $n['nombre'] . ' · Pastelería en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para una pastelería familiar en ' . $n['comuna'] . ', Santiago: tortas por encargo, vitrina del día, carta con QR y pedidos por WhatsApp.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Caveat:wght@500;700&family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,650;1,9..144,500&family=Karla:wght@400;500;700&display=swap',
    'css' => ['/demos/pasteleria/simple/estilo.css'],
    'tema' => '#bfdcea',
    'icono' => ['E', '#3a2217', '#f6e7c8'],
    'clase_body' => 'de',
]);
demo_aviso($rubro);
?>
<div class="de-toldo" aria-hidden="true"></div>
<header class="de-top">
  <div class="de-envoltura de-top__fila">
    <a class="de-marca" href="#contenido">
      <span class="de-marca__sello" aria-hidden="true"><span>DE</span></span>
      <span class="de-marca__nombre">Doña Elvira<small>Pastelería · desde <?= (int) $n['desde'] ?></small></span>
    </a>
    <nav class="de-nav" aria-label="Secciones">
      <a href="#vitrina">Vitrina</a>
      <a href="#tortas">Tortas</a>
      <a href="#carta">Carta</a>
      <a href="#horario">Horario</a>
      <a href="#google">Ubicación</a>
    </nav>
    <a class="de-btn de-btn--chico" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Encargar</span></a>
  </div>
</header>

<main id="contenido">
  <section class="de-hero">
    <div class="de-envoltura de-hero__grid">
      <div class="de-hero__texto">
        <p class="de-mano">Pastelería familiar en <?= demo_e($n['comuna']) ?></p>
        <h1><?= demo_e($n['nombre']) ?></h1>
        <p class="de-hero__eslogan"><?= demo_e($n['eslogan']) ?><span>, desde <?= (int) $n['desde'] ?>.</span></p>
        <p class="de-hero__bajada"><?= demo_e($n['bajada']) ?></p>
        <div class="de-acciones">
          <a class="de-btn" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Encargar por WhatsApp</span></a>
          <a class="de-btn de-btn--linea" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
        <p class="de-estado"><span class="de-estado__punto <?= $estado['abierto'] ? 'is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
      </div>
      <div class="de-hero__foto">
        <?= $blonda() ?>
        <?= demo_img($n['hero_foto'], 'Torta selva negra de tres pisos con guindas y crema', ['lazy' => false, 'ancho' => 900, 'alto' => 900, 'sizes' => '(max-width: 860px) 70vw, 400px', 'clase' => 'de-hero__img']) ?>
        <p class="de-hero__nota" aria-hidden="true">nuestra selva negra,<br>¡con guindas de verdad!</p>
        <svg class="de-hero__flecha" viewBox="0 0 120 80" aria-hidden="true" focusable="false"><path d="M6 10c30-8 70 0 92 40m0 0-2-16m2 16-15-6" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
    </div>
  </section>

  <section class="de-seccion de-vitrina" id="vitrina" aria-labelledby="vitrina-titulo">
    <div class="de-envoltura">
      <div class="de-encabezado">
        <p class="de-mano"><?= demo_e($cuandoVitrina) ?> <?= demo_e(mb_strtolower(DEMO_DIAS[$diaVitrina])) ?> en la vitrina</p>
        <h2 id="vitrina-titulo">Recién horneado</h2>
        <p>Lo que sale del horno cada día. Toca un precio y te lo guardamos por WhatsApp hasta que pases.</p>
      </div>
      <div class="de-vitrina__grid">
        <?php foreach ($vitrina as $p): $disponible = $hay($p); ?>
        <article class="de-producto<?= $disponible ? '' : ' is-otro-dia' ?>">
          <div class="de-producto__foto">
            <?= demo_img($p['foto'], $p['nombre'], ['ancho' => 600, 'alto' => 600, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 600px) 90vw, (max-width: 1000px) 45vw, 260px']) ?>
            <span class="de-producto__dia<?= $disponible ? ' is-hoy' : '' ?>"><?= demo_e($disponible ? $cuandoVitrina : $soloDias($p['dias'])) ?></span>
          </div>
          <h3><?= demo_e($p['nombre']) ?></h3>
          <p><?= demo_e($p['desc']) ?></p>
          <div class="de-producto__precios">
            <?php if ($p['entero'] > 0): ?>
            <a class="de-etiqueta" href="<?= demo_e($wa('Hola, ¿me guardan 1 trozo de ' . mb_strtolower($p['nombre']) . ' (' . demo_clp($p['trozo']) . ')? Paso a retirarlo.')) ?>" target="_blank" rel="noopener" aria-label="Apartar un trozo de <?= demo_e($p['nombre']) ?> por WhatsApp, <?= demo_e(demo_clp($p['trozo'])) ?>"><small>Trozo</small><?= demo_e(demo_clp($p['trozo'])) ?></a>
            <a class="de-etiqueta de-etiqueta--fuerte" href="<?= demo_e($wa('Hola, ¿me guardan 1 ' . mb_strtolower($p['nombre']) . ' entero (' . demo_clp($p['entero']) . ')? Paso a retirarlo.')) ?>" target="_blank" rel="noopener" aria-label="Apartar <?= demo_e($p['nombre']) ?> entero por WhatsApp, <?= demo_e(demo_clp($p['entero'])) ?>"><small>Entero</small><?= demo_e(demo_clp($p['entero'])) ?></a>
            <?php else: ?>
            <a class="de-etiqueta" href="<?= demo_e($wa('Hola, ¿me guardan ' . mb_strtolower($p['nombre']) . ' (' . mb_strtolower($p['unidad']) . ', ' . demo_clp($p['trozo']) . ')? Paso a retirar.')) ?>" target="_blank" rel="noopener" aria-label="Apartar <?= demo_e($p['nombre']) ?> por WhatsApp, <?= demo_e(demo_clp($p['trozo'])) ?>"><small><?= demo_e($p['unidad']) ?></small><?= demo_e(demo_clp($p['trozo'])) ?></a>
            <?php endif; ?>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="de-seccion de-tortas" id="tortas" aria-labelledby="tortas-titulo">
    <div class="de-cordel" aria-hidden="true"></div>
    <div class="de-envoltura">
      <div class="de-encabezado">
        <p class="de-mano">Para tu cumpleaños, bautizo o la once del domingo</p>
        <h2 id="tortas-titulo">Tortas por encargo</h2>
        <p>Elige el sabor y el tamaño: se abre WhatsApp con tu encargo escrito. Te confirmamos la fecha de retiro y el mensaje para la torta.</p>
      </div>
      <div class="de-tortas__lista">
        <?php foreach ($n['tortas'] as $t): ?>
        <article class="de-torta">
          <div class="de-torta__foto">
            <?= demo_img($t['foto'], 'Torta ' . $t['nombre'], ['ancho' => 600, 'alto' => 700, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 90vw, 280px']) ?>
            <?php if ($t['sello'] !== ''): ?><span class="de-torta__sello"><?= demo_e($t['sello']) ?></span><?php endif; ?>
          </div>
          <div class="de-torta__texto">
            <h3><?= demo_e($t['nombre']) ?></h3>
            <p><?= demo_e($t['desc']) ?></p>
            <p class="de-torta__elige" id="tam-<?= demo_e($t['id']) ?>">Elige el tamaño</p>
            <ul class="de-tamanos" aria-labelledby="tam-<?= demo_e($t['id']) ?>">
              <?php foreach ($t['precios'] as $personas => $precio): ?>
              <li><a href="<?= demo_e($wa('Hola, quiero encargar una torta ' . $t['nombre'] . ' para ' . $personas . ' personas (' . demo_clp($precio) . '). ¿Para qué día la puedo retirar?')) ?>" target="_blank" rel="noopener" aria-label="Encargar <?= demo_e($t['nombre']) ?> para <?= (int) $personas ?> personas por WhatsApp, <?= demo_e(demo_clp($precio)) ?>">
                <span><?= (int) $personas ?> personas</span><strong><?= demo_e(demo_clp($precio)) ?></strong>
              </a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <ol class="de-pasos" aria-label="Cómo encargar tu torta">
        <li><strong>Elige sabor y tamaño</strong><span>Toca el tamaño de la torta que quieres y se abre WhatsApp.</span></li>
        <li><strong>Cuéntanos la fecha y el mensaje</strong><span>Encarga con 48 horas de anticipación. El mensaje escrito en la torta no tiene costo.</span></li>
        <li><strong>Retira o te la llevamos</strong><span>Retiro en el local o despacho en San Miguel y comunas vecinas, en caja y con velitas.</span></li>
      </ol>
    </div>
  </section>

  <section class="de-seccion de-carta" id="carta" aria-labelledby="carta-titulo">
    <div class="de-envoltura de-carta__grid">
      <div class="de-libreta">
        <p class="de-mano">Todo lo que hacemos</p>
        <h2 id="carta-titulo">La carta</h2>
        <div class="de-libreta__columnas">
          <?php foreach ($n['carta'] as $categoria => $items): ?>
          <div class="de-libreta__grupo">
            <h3><?= demo_e($categoria) ?></h3>
            <ul>
              <?php foreach ($items as [$nombre, $detalle, $precio]): ?>
              <li>
                <span class="de-libreta__linea"><strong><?= demo_e($nombre) ?></strong><span class="de-libreta__puntos" aria-hidden="true"></span><span class="de-libreta__precio"><?= demo_e($precio) ?></span></span>
                <span class="de-libreta__detalle"><?= demo_e($detalle) ?> <a href="<?= demo_e($wa('Hola, quiero pedir: ' . $nombre . ' (' . $precio . ').')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($nombre) ?> por WhatsApp">Pedir</a></span>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <aside class="de-carta__lado" aria-label="Carta en tu celular">
        <div class="de-colgante">
          <?php demo_qr('#carta', 'Escanea y mira la carta en tu celular', 'de-qr'); ?>
        </div>
        <p>Este código va en el mesón y en las cajas de torta: lleva directo a la carta y a WhatsApp.</p>
        <div class="de-carta__fotos">
          <?php foreach ($n['carta_fotos'] as [$foto, $alt]): ?>
          <?= demo_img($foto, $alt, ['ancho' => 300, 'alto' => 300, 'anchos' => [300, 600], 'sizes' => '110px']) ?>
          <?php endforeach; ?>
        </div>
      </aside>
    </div>
  </section>

  <section class="de-seccion de-horario" id="horario" aria-labelledby="horario-titulo">
    <div class="de-envoltura de-horario__grid">
      <figure class="de-horario__foto">
        <?= demo_img($n['local_foto'], 'Pastelero atendiendo detrás de la vitrina de la pastelería', ['ancho' => 700, 'alto' => 820, 'anchos' => [480, 700, 1000], 'sizes' => '(max-width: 860px) 90vw, 40vw']) ?>
        <figcaption class="de-mano">Te esperamos con el café listo</figcaption>
      </figure>
      <div>
        <p class="de-mano">Pasa a vernos</p>
        <h2 id="horario-titulo">Horario y entregas</h2>
        <p class="de-estado"><span class="de-estado__punto <?= $estado['abierto'] ? 'is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario de-tabla-horario'); ?>
        <ul class="de-entregas">
          <li><strong>Retiro en el local</strong><span>Tu torta te espera en caja, con cordel y velitas.</span></li>
          <li><strong>Despacho</strong><span>San Miguel $2.500 · San Joaquín, La Cisterna y Pedro Aguirre Cerda $3.500.</span></li>
          <li><strong>Pago</strong><span>Efectivo, débito, crédito o transferencia. Los encargos se abonan al 50 %.</span></li>
        </ul>
      </div>
    </div>
  </section>

  <div class="de-seccion de-seccion--google">
    <div class="de-envoltura">
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

  <section class="de-cierre" aria-labelledby="cierre-titulo">
    <div class="de-envoltura">
      <p class="de-mano">¿Se viene un cumpleaños?</p>
      <h2 id="cierre-titulo">Encarga tu torta hoy</h2>
      <p>Escríbenos con 48 horas de anticipación y la tenemos lista, con tu mensaje escrito en manjar.</p>
      <div class="de-acciones">
        <a class="de-btn de-btn--crema" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Encargar por WhatsApp</span></a>
        <a class="de-btn de-btn--borde" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
      </div>
    </div>
  </section>
</main>

<footer class="de-pie">
  <div class="de-envoltura">
    <p class="de-pie__marca"><?= demo_e($n['nombre']) ?></p>
    <p><?= demo_e($n['comuna']) ?>, Santiago · Tortas por encargo por WhatsApp · Desde <?= (int) $n['desde'] ?></p>
    <?php demo_aviso_pie($rubro); ?>
  </div>
</footer>

<nav class="de-barra" aria-label="Encargar">
  <a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
</nav>
<?php demo_scripts(['qr']); ?>
</body>
</html>
