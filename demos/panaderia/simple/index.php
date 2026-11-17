<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version);

/** Enlace de WhatsApp con la nota de pedido de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);
$waGeneral = $wa('Hola, quiero encargar pan en ' . $n['nombre'] . '.');
$estado = demo_horario_estado($n['horario']);

/** "2 h 05 min" o "35 min". */
$faltan = static function (int $minutos): string {
    $h = intdiv($minutos, 60);
    $m = $minutos % 60;
    return $h > 0 ? $h . ' h' . ($m > 0 ? ' ' . sprintf('%02d', $m) . ' min' : '') : $m . ' min';
};

// Hornadas de hoy: caliente (salió hace menos de 45 min), salió, próxima (con cuenta regresiva) o más tarde.
$ahora = new DateTimeImmutable('now', new DateTimeZone(DEMO_ZONA_HORARIA));
$dia = (int) $ahora->format('N');
$minAhora = (int) $ahora->format('G') * 60 + (int) $ahora->format('i');
$hornadas = [];
$hayProxima = false;
$hayCaliente = false;
foreach ($n['hornadas'][$dia] ?? [] as $hora) {
    $m = demo_minutos($hora);
    if ($minAhora >= $m && $minAhora < $m + 45) {
        $hornadas[] = ['hora' => $hora, 'estado' => 'caliente', 'texto' => '¡Recién salida, está caliente!'];
        $hayCaliente = true;
    } elseif ($minAhora >= $m + 45) {
        $hornadas[] = ['hora' => $hora, 'estado' => 'salio', 'texto' => 'Ya salió · aún queda pan'];
    } elseif (!$hayProxima) {
        $hornadas[] = ['hora' => $hora, 'estado' => 'proxima', 'texto' => 'Sale en ' . $faltan($m - $minAhora)];
        $hayProxima = true;
    } else {
        $hornadas[] = ['hora' => $hora, 'estado' => 'luego', 'texto' => 'Más tarde'];
    }
}
$manana = $n['hornadas'][$dia === 7 ? 1 : $dia + 1] ?? [];
$todasSalieron = !$hayProxima && !$hayCaliente;
$etiquetas = ['Primera hornada', 'Hornada del almuerzo', 'Hornada de la once'];

/** Botones de pedido rápido: ½, 1 y 2 kg (o 1 y 2 unidades). */
$opciones = static function (array $p): array {
    if ($p['modo'] === 'unidad') {
        return [['1 unidad', 1, '1'], ['2 unidades', 2, '2']];
    }
    return [['½ kg', 0.5, '½ kg de'], ['1 kg', 1, '1 kg de'], ['2 kg', 2, '2 kg de']];
};

demo_head([
    'titulo' => $n['nombre'] . ' · Panadería en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para una panadería de barrio en ' . $n['comuna'] . ', Santiago: pan por kilo, horario de hornadas, lista de precios con QR y encargos por WhatsApp.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Space+Mono:wght@400;700&family=Work+Sans:wght@400;500;600;700&display=swap',
    'css' => ['/demos/panaderia/simple/estilo.css'],
    'tema' => '#1d3a6e',
    'icono' => ['3', '#1d3a6e', '#f3e3c3'],
    'clase_body' => 'th',
]);
demo_aviso($rubro);
?>
<header class="th-top">
  <div class="th-envoltura th-top__fila">
    <a class="th-marca" href="#contenido" aria-label="<?= demo_e($n['nombre']) ?>, inicio">
      <span class="th-marca__sello" aria-hidden="true">
        <svg viewBox="0 0 48 48" width="30" height="30" focusable="false"><g fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M24 42V12"/><path d="M24 20c-6-1-9-5-9-10 5 0 9 3 9 10zM24 20c6-1 9-5 9-10-5 0-9 3-9 10zM24 30c-6-1-9-5-9-10 5 0 9 3 9 10zM24 30c6-1 9-5 9-10-5 0-9 3-9 10z"/></g></svg>
      </span>
      <span class="th-marca__texto"><small>Panadería</small><?= demo_e($n['nombre_corto']) ?></span>
    </a>
    <nav class="th-nav" aria-label="Secciones">
      <a href="#hornadas">Hornadas</a>
      <a href="#pan">Pan del día</a>
      <a href="#empanadas">Empanadas</a>
      <a href="#precios">Precios</a>
      <a href="#reparto">Reparto</a>
      <a href="#google">Ubicación</a>
    </nav>
    <a class="th-btn th-btn--chico" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Encargar</span></a>
  </div>
</header>

<main id="contenido">
  <section class="th-hero" aria-labelledby="th-titulo">
    <div class="th-envoltura th-hero__grid">
      <div class="th-hero__texto">
        <p class="th-chip"><span class="th-punto <?= $estado['abierto'] ? 'is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <h1 id="th-titulo"><small>Panadería</small> Las Tres <span>Hornadas</span></h1>
        <p class="th-hero__eslogan"><?= demo_e($n['eslogan']) ?></p>
        <p class="th-hero__bajada"><?= demo_e($n['bajada']) ?></p>
        <div class="th-acciones">
          <a class="th-btn" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Encargar por WhatsApp</span></a>
          <a class="th-btn th-btn--linea" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
        <p class="th-hero__firma">Quinta Normal · desde la primera hornada de las 7:00</p>
      </div>
      <div class="th-hero__bolsa">
        <figure class="th-bolsa">
          <?= demo_img($n['fotos']['hero'], 'Marraquetas recién horneadas, doradas y crujientes, apiladas en la panadería', ['lazy' => false, 'ancho' => 900, 'alto' => 820, 'sizes' => '(max-width: 860px) 88vw, 480px', 'clase' => 'th-bolsa__foto']) ?>
          <figcaption class="th-bolsa__impreso"><span>Las Tres Hornadas</span><small>Pan del día · <?= demo_e($n['comuna']) ?></small></figcaption>
        </figure>
        <p class="th-timbre<?= $hayCaliente ? ' is-caliente' : '' ?>" aria-hidden="true">
          <span><?= $hayCaliente ? '¡Está' : 'Pan' ?></span><strong><?= $hayCaliente ? 'caliente!' : 'caliente' ?></strong><small>7:00 · 12:30 · 18:30</small>
        </p>
      </div>
    </div>
  </section>

  <section class="th-hornadas" id="hornadas" aria-labelledby="hornadas-titulo">
    <div class="th-envoltura">
      <div class="th-hornadas__cabeza">
        <p class="th-antetitulo th-antetitulo--claro">Horno encendido desde las 4:30</p>
        <h2 id="hornadas-titulo">Hornadas de hoy</h2>
        <p><?= $todasSalieron && $manana ? 'Las hornadas de hoy ya salieron. Mañana la primera es a las ' . demo_e($manana[0]) . '. Encarga ahora y te la guardamos.' : 'El pan sale del horno a estas horas. Encarga antes y te lo guardamos calientito.' ?></p>
      </div>
      <ol class="th-tickets">
        <?php foreach ($hornadas as $i => $h): ?>
        <li class="th-ticket is-<?= demo_e($h['estado']) ?>"<?= $h['estado'] === 'proxima' || $h['estado'] === 'caliente' ? ' aria-current="time"' : '' ?>>
          <span class="th-ticket__nombre"><?= demo_e($etiquetas[$i] ?? 'Hornada') ?></span>
          <strong class="th-ticket__hora"><?= demo_e($h['hora']) ?></strong>
          <span class="th-ticket__estado"><?= demo_e($h['texto']) ?></span>
        </li>
        <?php endforeach; ?>
      </ol>
      <p class="th-hornadas__nota">Domingo: hornadas a las 8:00 y 11:30. Empanadas fritas desde las 11:30 todos los días.</p>
    </div>
  </section>

  <section class="th-seccion" id="pan" aria-labelledby="pan-titulo">
    <div class="th-envoltura">
      <header class="th-cabeza">
        <p class="th-antetitulo">Se vende por kilo</p>
        <h2 id="pan-titulo">El pan de hoy</h2>
        <p>Toca la cantidad y se abre WhatsApp con tu encargo escrito. Te avisamos cuando esté listo para retirar.</p>
      </header>
      <div class="th-panes">
        <?php foreach ($n['panes'] as $p): $pedido = $p['pedido'] ?? mb_strtolower($p['nombre']); ?>
        <article class="th-pan">
          <div class="th-pan__foto">
            <?= demo_img($p['foto'], $p['nombre'] . ' recién horneado', ['ancho' => 600, 'alto' => 480, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 640px) 90vw, (max-width: 1000px) 45vw, 340px']) ?>
            <?php if ($p['sello'] !== ''): ?><span class="th-pan__sello"><?= demo_e($p['sello']) ?></span><?php endif; ?>
          </div>
          <div class="th-pan__cuerpo">
            <h3><?= demo_e($p['nombre']) ?></h3>
            <p><?= demo_e($p['desc']) ?></p>
            <p class="th-pan__precio"><strong><?= demo_e(demo_clp($p['precio'])) ?></strong> <?= $p['modo'] === 'kilo' ? 'el kilo' : 'la unidad' ?></p>
            <div class="th-pan__pedir" role="group" aria-label="Encargar <?= demo_e($p['nombre']) ?>">
              <?php foreach ($opciones($p) as [$etiqueta, $factor, $frase]): $monto = (int) round($p['precio'] * $factor); ?>
              <a href="<?= demo_e($wa('Hola, quiero ' . $frase . ' ' . $pedido . ' (' . demo_clp($monto) . ').')) ?>" target="_blank" rel="noopener" aria-label="Encargar <?= demo_e($etiqueta) ?> de <?= demo_e($p['nombre']) ?> por WhatsApp, <?= demo_e(demo_clp($monto)) ?>">
                <span><?= demo_e($etiqueta) ?></span><small><?= demo_e(demo_clp($monto)) ?></small>
              </a>
              <?php endforeach; ?>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="th-seccion th-empanadas" id="empanadas" aria-labelledby="empanadas-titulo">
    <div class="th-envoltura">
      <header class="th-cabeza th-cabeza--centro">
        <p class="th-antetitulo">Todos los días desde las 11:30</p>
        <h2 id="empanadas-titulo">Empanadas por docena</h2>
        <p>Para el almuerzo, la once o el cumpleaños. Encárgalas con un día de aviso y te las tenemos calientes a la hora.</p>
      </header>
      <div class="th-empanadas__lista">
        <?php foreach ($n['empanadas'] as $emp): ?>
        <article class="th-empanada">
          <div class="th-empanada__foto"><?= demo_img($emp['foto'], 'E' . mb_substr($emp['pedido'], 1), ['ancho' => 500, 'alto' => 500, 'anchos' => [300, 500, 800], 'sizes' => '(max-width: 760px) 60vw, 240px']) ?></div>
          <h3><?= demo_e($emp['nombre']) ?></h3>
          <p><?= demo_e($emp['desc']) ?></p>
          <p class="th-empanada__precios"><span><?= demo_e(demo_clp($emp['unidad'])) ?> c/u</span><strong><?= demo_e(demo_clp($emp['docena'])) ?> la docena</strong></p>
          <a class="th-btn th-btn--chico th-btn--crema" href="<?= demo_e($wa('Hola, quiero encargar 1 docena de ' . $emp['pedido'] . ' (' . demo_clp($emp['docena']) . ').')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Encargar docena</span></a>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="th-seccion th-dulces" id="dulces" aria-labelledby="dulces-titulo">
    <div class="th-envoltura th-dulces__grid">
      <header class="th-cabeza">
        <p class="th-antetitulo">Para la once</p>
        <h2 id="dulces-titulo">Dulces de la casa</h2>
        <p>Berlines, galletas y trenzas que salen junto con el pan de la tarde. También calzones rotos y chilenitos: míralos en la lista de precios.</p>
        <figure class="th-dulces__canasto">
          <?= demo_img($n['fotos']['canasto'], 'Canasto de mimbre con panes y dulces variados', ['ancho' => 600, 'alto' => 450, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 900px) 90vw, 380px']) ?>
        </figure>
      </header>
      <ul class="th-dulces__lista">
        <?php foreach ($n['dulces'] as $d): ?>
        <li class="th-dulce">
          <?= demo_img($d['foto'], $d['nombre'], ['ancho' => 400, 'alto' => 400, 'anchos' => [300, 400, 700], 'sizes' => '(max-width: 640px) 34vw, 160px', 'clase' => 'th-dulce__foto']) ?>
          <div>
            <h3><?= demo_e($d['nombre']) ?></h3>
            <p><?= demo_e($d['desc']) ?></p>
            <p class="th-dulce__pie"><strong><?= demo_e($d['precio']) ?></strong>
              <a href="<?= demo_e($wa($d['mensaje'])) ?>" target="_blank" rel="noopener" aria-label="Encargar <?= demo_e($d['nombre']) ?> por WhatsApp">Encargar</a></p>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="th-seccion th-precios" id="precios" aria-labelledby="precios-titulo">
    <div class="th-envoltura">
      <header class="th-cabeza">
        <p class="th-antetitulo">Igual que el letrero del mesón</p>
        <h2 id="precios-titulo">Lista de precios</h2>
      </header>
      <div class="th-precios__grid">
        <div class="th-letrero">
          <div class="th-letrero__tablero">
            <?php foreach ($n['lista'] as $grupo => $items): ?>
            <section class="th-letrero__grupo" aria-label="<?= demo_e($grupo) ?>">
              <h3><?= demo_e($grupo) ?></h3>
              <ul>
                <?php foreach ($items as [$nombre, $precio]): ?>
                <li><span><?= demo_e($nombre) ?></span><span><?= demo_e($precio) ?></span></li>
                <?php endforeach; ?>
              </ul>
            </section>
            <?php endforeach; ?>
          </div>
        </div>
        <aside class="th-boleta" aria-label="Lista de precios en tu celular">
          <p class="th-boleta__cabeza">Las Tres Hornadas<br><small><?= demo_e($n['comuna']) ?> · lista al día</small></p>
          <?php demo_qr('#precios', 'Escanea y mira la lista de precios en tu celular', 'th-qr'); ?>
          <p>Este código va pegado en el mesón y en la vitrina: tus clientes ven los precios del día sin hacer fila.</p>
          <a class="th-btn th-btn--chico" href="<?= demo_e($wa('Hola, quiero hacer un encargo de la lista de precios.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Encargar de la lista</span></a>
        </aside>
      </div>
    </div>
  </section>

  <section class="th-seccion th-reparto" id="reparto" aria-labelledby="reparto-titulo">
    <div class="th-envoltura th-reparto__grid">
      <figure class="th-reparto__foto">
        <?= demo_img($n['fotos']['reparto'], 'Manos sosteniendo un pan recién horneado para entregar', ['ancho' => 700, 'alto' => 800, 'anchos' => [480, 700, 1000], 'sizes' => '(max-width: 900px) 90vw, 420px']) ?>
      </figure>
      <div>
        <p class="th-antetitulo">Pan a tu puerta</p>
        <h2 id="reparto-titulo">Reparto en el barrio</h2>
        <p class="th-reparto__bajada">Te llevamos el pan de la mañana antes de que salgas, y el de la once cuando vuelves.</p>
        <ul class="th-recibos">
          <li class="th-recibo">
            <p class="th-recibo__titulo">Horario de reparto</p>
            <p><?= demo_e($n['reparto']['horas']) ?></p>
          </li>
          <li class="th-recibo">
            <p class="th-recibo__titulo">Costo</p>
            <p><?= demo_e(demo_clp($n['reparto']['costo'])) ?> en <?= demo_e(implode(', ', $n['reparto']['cerca'])) ?>. Gratis sobre <?= demo_e(demo_clp($n['reparto']['gratis_desde'])) ?>.</p>
          </li>
          <li class="th-recibo">
            <p class="th-recibo__titulo">Encargos grandes</p>
            <p>Pan amasado por cien, empanadas por docena y bandejas para eventos, con un día de aviso. Pedido mínimo <?= demo_e(demo_clp($n['reparto']['minimo'])) ?>.</p>
          </li>
        </ul>
        <div class="th-acciones">
          <a class="th-btn" href="<?= demo_e($wa('Hola, quiero pan con reparto a domicilio en ' . $n['comuna'] . '.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir reparto</span></a>
        </div>
      </div>
    </div>
  </section>

  <section class="th-seccion th-horario" id="horario" aria-labelledby="horario-titulo">
    <div class="th-envoltura th-horario__grid">
      <div>
        <p class="th-antetitulo">Abrimos temprano</p>
        <h2 id="horario-titulo">Horario</h2>
        <p class="th-estado-linea"><span class="th-punto <?= $estado['abierto'] ? 'is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario th-tabla-horario'); ?>
        <p class="th-pequeno">Pago con efectivo, débito, crédito o transferencia. Feriados: abrimos como día domingo.</p>
      </div>
      <figure class="th-horario__foto">
        <?= demo_img($n['fotos']['panadero'], 'Panadero sacando bandejas de pan del horno', ['ancho' => 800, 'alto' => 600, 'anchos' => [480, 800, 1200], 'sizes' => '(max-width: 900px) 90vw, 560px']) ?>
        <figcaption>El horno se prende a las 4:30 para que la primera hornada salga a las 7:00.</figcaption>
      </figure>
    </div>
  </section>

  <div class="th-seccion th-seccion--google">
    <div class="th-envoltura">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería tu ficha en Google Maps: horario, cómo llegar y lo que dicen tus vecinos.']); ?>
    </div>
  </div>

  <section class="th-cierre" aria-labelledby="cierre-titulo">
    <div class="th-envoltura">
      <h2 id="cierre-titulo">¿Te guardamos una marraqueta?</h2>
      <p>Escríbenos y tu encargo queda apartado de la próxima hornada.</p>
      <div class="th-acciones">
        <a class="th-btn th-btn--crema" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Encargar por WhatsApp</span></a>
        <a class="th-btn th-btn--borde-claro" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
      </div>
    </div>
  </section>
</main>

<footer class="th-pie">
  <div class="th-envoltura">
    <p class="th-pie__marca"><?= demo_e($n['nombre']) ?></p>
    <p><?= demo_e($n['comuna']) ?>, Santiago · Pan por kilo, empanadas y encargos por WhatsApp</p>
    <?php demo_aviso_pie($rubro); ?>
  </div>
</footer>

<nav class="th-barra" aria-label="Encargar">
  <a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
</nav>
<?php demo_scripts(['qr']); ?>
</body>
</html>
