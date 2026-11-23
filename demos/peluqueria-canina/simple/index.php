<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version, 'Reserva');

/** Enlace de WhatsApp con la nota de reserva de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);
$waGeneral = $wa('Hola, quiero pedir hora en ' . $n['nombre'] . '. Mi perro se llama: ');
$estado = demo_horario_estado($n['horario']);
// Tamaños de la ficha: los de perro más "Gato" (se atienden los martes en la mañana).
$tamanos = $n['tamanos'] + ['gato' => ['Gato', 'pelo largo o corto', 'solo los martes en la mañana']];

/**
 * Días que se ofrecen para pedir hora: desde mañana, los próximos 10 días con atención.
 * @return array<string, string> ['2026-11-25' => 'Mañana, martes 25 de noviembre', ...]
 */
function lc_dias(array $horario, int $cuantos = 10): array
{
    $hoy = new DateTimeImmutable('today', new DateTimeZone(DEMO_ZONA_HORARIA));
    $dias = [];
    for ($i = 1; count($dias) < $cuantos && $i <= 30; $i++) {
        $d = $hoy->modify('+' . $i . ' days');
        if (empty($horario[(int) $d->format('N')])) {
            continue;
        }
        $texto = demo_fecha_larga($d);
        $dias[$d->format('Y-m-d')] = $i === 1 ? 'Mañana, ' . $texto : mb_strtoupper(mb_substr($texto, 0, 1)) . mb_substr($texto, 1);
    }
    return $dias;
}

/** Precio de un servicio para un tamaño, con el recargo del pelo en baños y cortes; null si no aplica. */
function lc_precio(array $n, string $servicio, string $tamano, string $pelo = ''): ?int
{
    $base = $n['servicios'][$servicio][2][$tamano] ?? null;
    if ($base === null) {
        return null;
    }
    $recargo = in_array($servicio, ['bano', 'corte', 'higienico'], true) ? (int) ($n['pelos'][$pelo][2] ?? 0) : 0;
    return $base + $recargo;
}

/**
 * Ficha de reserva (sin JavaScript): valida en el servidor y redirige a WhatsApp con el mensaje armado.
 * Devuelve [url|null, errores, valores enviados].
 */
function lc_reserva(array $post, array $n, array $tamanos, array $dias, string $nota): array
{
    $txt = static function (string $k, int $max) use ($post): string {
        $v = $post[$k] ?? '';
        return is_string($v) ? mb_substr(trim(preg_replace('/\s+/u', ' ', $v) ?? ''), 0, $max) : '';
    };
    $previo = [
        'servicio' => $txt('servicio', 20),
        'tamano' => $txt('tamano', 10),
        'pelo' => $txt('pelo', 10),
        'perro' => $txt('perro', 30),
        'raza' => $txt('raza', 40),
        'dia' => $txt('dia', 10),
        'bloque' => $txt('bloque', 10),
        'detalle' => $txt('detalle', 300),
        'nombre' => $txt('nombre', 40),
    ];
    $errores = [];
    if (!isset($n['servicios'][$previo['servicio']])) {
        $errores['servicio'] = 'Elige un servicio de la lista.';
    }
    if (!isset($tamanos[$previo['tamano']])) {
        $errores['tamano'] = 'Cuéntanos el tamaño de tu perro (o si es un gato).';
    } elseif (!isset($errores['servicio']) && lc_precio($n, $previo['servicio'], $previo['tamano']) === null) {
        $errores['tamano'] = 'El deslanado es para perros de doble manto, de tamaño mediano o más grandes.';
    }
    if ($previo['pelo'] !== '' && !isset($n['pelos'][$previo['pelo']])) {
        $errores['pelo'] = 'Elige un tipo de pelo de la lista.';
    }
    if (mb_strlen($previo['perro']) < 2) {
        $errores['perro'] = 'Cuéntanos cómo se llama.';
    }
    if (!isset($dias[$previo['dia']])) {
        $errores['dia'] = 'Elige uno de los días de la lista.';
    }
    if (!isset($n['bloques'][$previo['bloque']])) {
        $errores['bloque'] = 'Elige si prefieres la mañana o la tarde.';
    } elseif (isset($dias[$previo['dia']])) {
        $diaSemana = (int) (new DateTimeImmutable($previo['dia']))->format('N');
        if ($previo['tamano'] === 'gato' && ($diaSemana !== 2 || $previo['bloque'] !== 'manana')) {
            $errores['bloque'] = 'A los gatos los atendemos los martes en la mañana, cuando no hay perros en el local.';
        } elseif ($previo['bloque'] === 'tarde' && $diaSemana === 6) {
            $errores['bloque'] = 'Los sábados atendemos solo en la mañana.';
        }
    }
    if ($errores) {
        return [null, $errores, $previo];
    }
    [$servicio, , , $notaPrecio] = $n['servicios'][$previo['servicio']];
    [$tamNombre, $tamPeso] = $tamanos[$previo['tamano']];
    $precio = (int) lc_precio($n, $previo['servicio'], $previo['tamano'], $previo['pelo']);
    $lineas = [
        'Hola, quiero pedir hora en ' . $n['nombre'] . '.',
        'Servicio: ' . $servicio,
        'Tamaño: ' . $tamNombre . ($previo['tamano'] === 'gato' ? '' : ' (' . $tamPeso . ')'),
    ];
    if ($previo['pelo'] !== '') {
        $lineas[] = 'Pelo: ' . $n['pelos'][$previo['pelo']][0];
    }
    $lineas[] = 'Precio según la tabla: ' . demo_clp($precio) . ($notaPrecio !== '' ? ' (' . $notaPrecio . ')' : '');
    $lineas[] = ($previo['tamano'] === 'gato' ? 'Gato: ' : 'Perro: ') . $previo['perro'];
    if ($previo['raza'] !== '') {
        $lineas[] = 'Raza: ' . $previo['raza'];
    }
    $lineas[] = 'Día: ' . demo_fecha_larga(new DateTimeImmutable($previo['dia']));
    $lineas[] = 'Bloque: ' . $n['bloques'][$previo['bloque']];
    if ($previo['detalle'] !== '') {
        $lineas[] = 'Cómo es: ' . $previo['detalle'];
    }
    if ($previo['nombre'] !== '') {
        $lineas[] = 'Nombre: ' . $previo['nombre'];
    }
    $lineas[] = $nota;
    return [demo_wa(implode("\n", $lineas)), [], $previo];
}

$dias = lc_dias($n['horario']);
$errores = [];
$previo = [
    'servicio' => is_string($_GET['servicio'] ?? null) && isset($n['servicios'][$_GET['servicio']]) ? $_GET['servicio'] : '',
    'tamano' => is_string($_GET['tamano'] ?? null) && isset($tamanos[$_GET['tamano']]) ? $_GET['tamano'] : '',
];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    [$destino, $errores, $previo] = lc_reserva($_POST, $n, $tamanos, $dias, $nota);
    if ($destino !== null) {
        header('Location: ' . $destino, true, 303);
        exit;
    }
    http_response_code(422);
}
$val = static fn (string $k): string => (string) ($previo[$k] ?? '');
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="lc-error" id="err-' . demo_e($k) . '">' . demo_e($errores[$k]) . '</p>' : '';
$inv = static fn (string $k): string => isset($errores[$k]) ? ' aria-invalid="true" aria-describedby="err-' . demo_e($k) . '"' : '';
$pedir = static fn (string $id, string $tam = ''): string => '?servicio=' . rawurlencode($id) . ($tam !== '' ? '&tamano=' . rawurlencode($tam) : '') . '#reservar';
$precioTexto = static fn (?int $p): string => $p === null ? '—' : demo_clp($p);

/** Logo: carita de perro con chasquilla (flequillo) sobre un círculo rojo tomate. */
$logo = '<svg viewBox="0 0 48 48" width="46" height="46" aria-hidden="true" focusable="false"><circle cx="24" cy="24" r="23" fill="#d2391f"/>'
    . '<path fill="#fff" d="M13 13.5c-3.6 0-6 4.6-4.8 10.2.7 3.2 2.6 4.6 4.8 4 .4 6.4 4.8 11.3 11 11.3s10.6-4.9 11-11.3c2.2.6 4.1-.8 4.8-4 1.2-5.6-1.2-10.2-4.8-10.2-2.3 0-4.2 1.2-5.4 2.6-1.6-1-3.5-1.5-5.6-1.5s-4 .5-5.6 1.5c-1.2-1.4-3.1-2.6-5.4-2.6z"/>'
    . '<path fill="#1b2b4b" d="M14.6 19.4 17.4 14l2.4 4.6 2.2-5 2.2 5 2.2-5 2.2 5 2.4-4.6 2.8 5.4c-2.8 1.9-6.3 2.9-9.6 2.9s-6.8-1-9.6-2.9z"/>'
    . '<circle cx="19.2" cy="25.4" r="1.9" fill="#1b2b4b"/><circle cx="28.8" cy="25.4" r="1.9" fill="#1b2b4b"/>'
    . '<path fill="#1b2b4b" d="M21.4 30.2h5.2c.6 0 .9.7.5 1.1l-2.4 2.3a1 1 0 0 1-1.4 0l-2.4-2.3c-.4-.4-.1-1.1.5-1.1z"/>'
    . '<path d="M24 33.6v1.6M24 35.2c-1 1.3-2.6 1.6-3.8.8M24 35.2c1 1.3 2.6 1.6 3.8.8" stroke="#1b2b4b" stroke-width="1.3" fill="none" stroke-linecap="round"/></svg>';
/** Perro de perfil (los tamaños de la tabla lo muestran más chico o más grande). */
$perro = static fn (int $ancho): string => '<svg class="lc-perrito" viewBox="0 0 64 44" width="' . $ancho . '" height="' . (int) round($ancho * 44 / 64) . '" aria-hidden="true" focusable="false" fill="currentColor">'
    . '<rect x="12" y="15" width="36" height="15" rx="7.5"/><circle cx="50" cy="13" r="8"/><path d="M54.5 10.5h6.5a3.2 3.2 0 0 1 0 6.4h-6z"/>'
    . '<path d="M44.6 6.2c-2.8 1.6-3.6 6-2.4 10.4l4.6-1.6c.2-3.4-.2-6.8-2.2-8.8z"/>'
    . '<rect x="14" y="25" width="5.4" height="17" rx="2.7"/><rect x="21.4" y="25" width="5.4" height="17" rx="2.7"/><rect x="37.4" y="25" width="5.4" height="17" rx="2.7"/><rect x="43.6" y="25" width="5.4" height="17" rx="2.7"/>'
    . '<path d="M14 19.5C9.4 18.8 6.2 14.6 6.4 9" fill="none" stroke="currentColor" stroke-width="3.6" stroke-linecap="round"/></svg>';
$huella = '<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="currentColor"><ellipse cx="6.2" cy="9.4" rx="2" ry="2.6" transform="rotate(-18 6.2 9.4)"/><ellipse cx="10.2" cy="5.6" rx="2" ry="2.7"/><ellipse cx="14.8" cy="5.6" rx="2" ry="2.7"/><ellipse cx="18.8" cy="9.4" rx="2" ry="2.6" transform="rotate(18 18.8 9.4)"/><path d="M12.5 11.5c-3 0-5.6 3.2-5.6 5.5 0 1.6 1.3 2.5 2.8 2.5 1.2 0 1.8-.6 2.8-.6s1.6.6 2.8.6c1.5 0 2.8-.9 2.8-2.5 0-2.3-2.6-5.5-5.6-5.5z"/></svg>';
$patito = '<svg viewBox="0 0 64 56" width="64" height="56" aria-hidden="true" focusable="false"><path d="M18 30c-6 0-10 5-8 11 2 7 10 11 22 11 14 0 22-6 22-15 0-6-4-9-9-9-2 0-4 .6-5 1.6 2-2.6 3-5.6 3-8.6C43 13 37.6 8 30.6 8S18 13 18 20c0 3.6 1.4 6.8 3.6 9.2A9 9 0 0 0 18 30z" fill="#ffd23f" stroke="#1b2b4b" stroke-width="2.4" stroke-linejoin="round"/><path d="M41 18.6c3.6-.6 7.4.4 9.2 2.4-2.6 2.2-6.4 3-9.8 2.2" fill="#f28a2e" stroke="#1b2b4b" stroke-width="2.4" stroke-linejoin="round"/><circle cx="33" cy="16.4" r="2.2" fill="#1b2b4b"/><path d="M22 38c4 4 12 5 18 1" fill="none" stroke="#1b2b4b" stroke-width="2.2" stroke-linecap="round"/></svg>';
$tijeras = '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M20 4 8.1 15.9M14.5 14.5 20 20M8.1 8.1 12 12"/></svg>';

demo_head([
    'titulo' => $n['nombre'] . ' · Peluquería canina en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para una peluquería canina de barrio en ' . $n['comuna'] . ', Santiago: baño, corte de raza, corte higiénico, deslanado, precios por tamaño y reserva de hora por WhatsApp.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Sansita:ital,wght@0,700;0,800;0,900;1,800&display=swap',
    'css' => ['/demos/peluqueria-canina/simple/estilo.css'],
    'tema' => '#dcedf6',
    'icono' => ['C', '#d2391f', '#ffffff'],
    'clase_body' => 'lc',
]);
demo_aviso($rubro);
?>
<header class="lc-top">
  <div class="lc-envoltura lc-top__fila">
    <a class="lc-marca" href="#contenido">
      <?= $logo ?>
      <span class="lc-marca__nombre">La Chasquilla<small>peluquería canina · <?= demo_e($n['comuna']) ?></small></span>
    </a>
    <nav class="lc-nav" aria-label="Secciones">
      <a href="#precios">Precios</a>
      <a href="#servicios">Servicios</a>
      <a href="#visita">La visita</a>
      <a href="#galeria">Galería</a>
      <a href="#horario">Horario</a>
    </nav>
    <a class="lc-btn lc-btn--chico" href="#reservar"><?= $tijeras ?><span>Pedir hora</span></a>
  </div>
</header>

<main id="contenido">
  <section class="lc-hero" aria-labelledby="hero-titulo">
    <span class="lc-burbuja lc-burbuja--a" aria-hidden="true"></span>
    <span class="lc-burbuja lc-burbuja--b" aria-hidden="true"></span>
    <span class="lc-burbuja lc-burbuja--c" aria-hidden="true"></span>
    <div class="lc-envoltura lc-hero__grid">
      <div class="lc-hero__texto">
        <p class="lc-etiqueta">Peluquería canina · <?= demo_e($n['comuna']) ?> · desde <?= (int) $n['desde'] ?></p>
        <h1 id="hero-titulo">Entra chascón, <em>sale regio.</em></h1>
        <p class="lc-hero__bajada">Baño con agua tibia, corte de raza o higiénico, uñas y oídos, con la tía Mónica y la Fernanda. Precios por tamaño a la vista y te mandamos una foto por WhatsApp cuando está listo.</p>
        <p class="lc-estado-linea"><span class="lc-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <div class="lc-acciones">
          <a class="lc-btn" href="#reservar"><?= $tijeras ?><span>Pedir hora</span></a>
          <a class="lc-btn lc-btn--claro" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
        <p class="lc-hero__dato"><strong>+3.000 baños</strong> en el barrio · un perro a la vez en la tina</p>
      </div>
      <div class="lc-hero__foto">
        <figure class="lc-marco">
          <?= demo_img($n['fotos']['hero'], 'Perro de pelo crespo color crema, mojado y sentado en la tina de acero de la peluquería, frente a la pared de azulejos', ['lazy' => false, 'ancho' => 900, 'alto' => 760, 'sizes' => '(max-width: 860px) 92vw, 520px', 'anchos' => [480, 900, 1300]]) ?>
          <figcaption>Simón, a mitad del baño</figcaption>
        </figure>
        <p class="lc-sticker"><span>Baño</span><strong>desde <?= demo_e(demo_clp($n['servicios']['bano'][2]['pequeno'])) ?></strong></p>
        <span class="lc-patito"><?= $patito ?></span>
      </div>
    </div>
  </section>

  <section class="lc-toalla" aria-label="Lo que prometemos">
    <ul class="lc-envoltura">
      <?php foreach ($n['promesas'] as $p): ?>
      <li><?= $huella ?><span><?= demo_e($p) ?></span></li>
      <?php endforeach; ?>
    </ul>
  </section>

  <section class="lc-seccion lc-seccion--azulejo" id="precios" aria-labelledby="precios-titulo">
    <div class="lc-envoltura">
      <header class="lc-encabezado">
        <p class="lc-sobre">La pizarra de precios</p>
        <h2 id="precios-titulo">Precios por tamaño, sin sorpresas</h2>
        <p>Pesamos a tu perro al llegar y cobramos lo que dice la tabla. Si tiene el pelo largo o rizado, el baño y los cortes suben <?= demo_e(demo_clp($n['pelos']['largo'][2])) ?>.</p>
      </header>

      <ul class="lc-tamanos" aria-label="Tamaños">
        <?php $anchos = ['pequeno' => 34, 'mediano' => 46, 'grande' => 58, 'gigante' => 70]; ?>
        <?php foreach ($n['tamanos'] as $clave => [$nombre, $peso, $ejemplos]): ?>
        <li class="lc-tamano">
          <span class="lc-tamano__dibujo"><?= $perro($anchos[$clave]) ?></span>
          <strong><?= demo_e($nombre) ?></strong>
          <span><?= demo_e($peso) ?></span>
          <small><?= demo_e($ejemplos) ?></small>
        </li>
        <?php endforeach; ?>
      </ul>

      <div class="lc-pizarra">
        <table class="lc-tarifa">
          <caption class="demo-sr">Precios de cada servicio según el tamaño del perro</caption>
          <thead>
            <tr>
              <th scope="col">Servicio</th>
              <?php foreach ($n['tamanos'] as [$nombre, $peso]): ?><th scope="col"><?= demo_e($nombre) ?><small><?= demo_e($peso) ?></small></th><?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($n['servicios'] as $id => [$nombre, $detalle, $precios, $notaPrecio]): ?>
            <tr>
              <th scope="row"><strong><?= demo_e($nombre) ?></strong><?php if ($notaPrecio !== ''): ?> <em><?= demo_e($notaPrecio) ?></em><?php endif; ?><span><?= demo_e($detalle) ?></span></th>
              <?php foreach ($n['tamanos'] as $tam => [$tamNombre]): ?>
              <td data-tam="<?= demo_e($tamNombre) ?>"><?php if ($precios[$tam] === null): ?><span class="lc-no-aplica" title="No aplica">—</span><span class="demo-sr">No aplica</span><?php else: ?><a href="<?= demo_e($pedir($id, $tam)) ?>" aria-label="Pedir hora: <?= demo_e($nombre) ?>, perro <?= demo_e(mb_strtolower($tamNombre)) ?>, <?= demo_e(demo_clp($precios[$tam])) ?>"><?= demo_e(demo_clp($precios[$tam])) ?></a><?php endif; ?></td>
              <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <p class="lc-pizarra__pie">Toca un precio para pedir esa hora. Los precios incluyen IVA.</p>
      </div>

      <div class="lc-pelos" aria-labelledby="pelos-titulo">
        <h3 id="pelos-titulo">¿Qué pelo tiene tu perro?</h3>
        <ul>
          <?php foreach ($n['pelos'] as $clave => [$nombre, $texto, $recargo]): ?>
          <li class="lc-pelo lc-pelo--<?= demo_e($clave) ?>">
            <span class="lc-pelo__muestra" aria-hidden="true"></span>
            <strong><?= demo_e($nombre) ?></strong>
            <span><?= demo_e($texto) ?></span>
            <em><?= $recargo > 0 ? '+' . demo_e(demo_clp($recargo)) : 'precio de tabla' ?></em>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </section>

  <section class="lc-seccion" id="servicios" aria-labelledby="servicios-titulo">
    <div class="lc-envoltura">
      <header class="lc-encabezado lc-encabezado--fila">
        <div>
          <p class="lc-sobre">Servicios</p>
          <h2 id="servicios-titulo">Lo que más nos piden</h2>
        </div>
        <p>Uñas y oídos sin hora: pasa cuando quieras dentro del horario. Para todo lo demás, pide hora y así no esperas.</p>
      </header>
      <div class="lc-destacados">
        <?php foreach ($n['destacados'] as $id => $d): [$nombre, $detalle, $precios] = $n['servicios'][$id]; $desde = min(array_filter($precios, static fn (?int $p): bool => $p !== null)); ?>
        <article class="lc-destacado">
          <div class="lc-destacado__foto"<?= isset($d['pos']) ? ' style="--pos: ' . demo_e($d['pos']) . '"' : '' ?>>
            <?= demo_img($d['foto'], $d['alt'], ['ancho' => 600, 'alto' => 520, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 90vw, (max-width: 1000px) 45vw, 25vw']) ?>
            <span class="lc-cinta"><?= demo_e($d['sello']) ?></span>
          </div>
          <div class="lc-destacado__texto">
            <h3><?= demo_e($nombre) ?></h3>
            <p><?= demo_e($detalle) ?></p>
            <p class="lc-desde"><small>desde</small> <?= demo_e(demo_clp($desde)) ?></p>
            <a class="lc-enlace" href="<?= demo_e($pedir($id)) ?>">Pedir este servicio</a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <aside class="lc-gatos" aria-labelledby="gatos-titulo">
        <figure class="lc-gatos__foto">
          <?= demo_img($n['fotos']['gato'], 'Gato persa naranjo y blanco en la mesa de peluquería mientras una peluquera lo corta con máquina', ['ancho' => 640, 'alto' => 480, 'anchos' => [400, 640, 900], 'sizes' => '(max-width: 760px) 90vw, 360px']) ?>
        </figure>
        <div>
          <p class="lc-sobre">¿Y los gatos?</p>
          <h3 id="gatos-titulo">Los martes en la mañana son de los gatos</h3>
          <p>Bañamos y cortamos gatos de pelo largo y corto cuando no hay perros en el local, sin ladridos ni apuro. Baño desde <?= demo_e(demo_clp($n['servicios']['bano'][2]['gato'])) ?>, deslanado <?= demo_e(demo_clp($n['servicios']['deslanado'][2]['gato'])) ?>.</p>
          <a class="lc-enlace" href="<?= demo_e($pedir('bano', 'gato')) ?>">Pedir hora para mi gato</a>
        </div>
      </aside>
    </div>
  </section>

  <section class="lc-seccion lc-seccion--crema" id="sellos" aria-labelledby="sellos-titulo">
    <div class="lc-envoltura lc-sellos">
      <div class="lc-sellos__texto">
        <p class="lc-sobre">Tarjeta de sellos</p>
        <h2 id="sellos-titulo">Cada 10 baños, el siguiente va por la casa</h2>
        <p>En cada visita te ponemos un sello. Si pierdes la tarjeta no importa: la Mónica lo anota todo en su cuaderno.</p>
      </div>
      <div class="lc-tarjeta" role="img" aria-label="Ejemplo de tarjeta de sellos con 7 de 10 baños">
        <p class="lc-tarjeta__cabeza"><span>La Chasquilla</span><span>Perro: <strong>Bruno</strong></span></p>
        <ol class="lc-tarjeta__sellos">
          <?php for ($i = 1; $i <= 10; $i++): ?>
          <li class="<?= $i <= 7 ? 'is-sellado' : ($i === 10 ? 'is-premio' : '') ?>" style="--giro: <?= ($i * 37) % 30 - 15 ?>deg"><?= $i <= 7 ? $huella : ($i === 10 ? 'gratis' : (string) $i) ?></li>
          <?php endfor; ?>
        </ol>
        <p class="lc-tarjeta__pie">Válida para baños y cortes · sin fecha de vencimiento</p>
      </div>
    </div>
  </section>

  <section class="lc-seccion" id="visita" aria-labelledby="visita-titulo">
    <div class="lc-envoltura lc-visita">
      <figure class="lc-visita__foto">
        <?= demo_img($n['fotos']['visita'], 'Peluquera inclinada sobre la tina bañando a un perro café de orejas paradas', ['ancho' => 760, 'alto' => 560, 'sizes' => '(max-width: 900px) 92vw, 46vw']) ?>
      </figure>
      <div>
        <header class="lc-encabezado">
          <p class="lc-sobre">La visita</p>
          <h2 id="visita-titulo">Así lo pasa tu perro con nosotras</h2>
        </header>
        <ol class="lc-pasos">
          <?php foreach ($n['pasos'] as $i => [$titulo, $texto]): ?>
          <li><span class="lc-pasos__num" aria-hidden="true"><?= $i + 1 ?></span><div><strong><?= demo_e($titulo) ?></strong><span><?= demo_e($texto) ?></span></div></li>
          <?php endforeach; ?>
        </ol>
        <p class="lc-pequeno">Un baño dura entre una y dos horas según el tamaño y el pelo. Si tu perro es nervioso, cuéntanos al pedir hora y le damos el primer turno, con el local tranquilo.</p>
      </div>
    </div>
  </section>

  <section class="lc-seccion lc-seccion--azulejo" id="galeria" aria-labelledby="galeria-titulo">
    <div class="lc-envoltura">
      <header class="lc-encabezado lc-encabezado--centro">
        <p class="lc-sobre">Galería</p>
        <h2 id="galeria-titulo">Salieron regios esta semana</h2>
        <p>Les sacamos la foto antes de devolverlos. Con permiso de sus dueños, aquí van algunos.</p>
      </header>
      <ul class="lc-galeria">
        <?php foreach ($n['galeria'] as $i => [$foto, $alt, $nombre, $detalle]): ?>
        <li class="lc-polaroid" style="--giro: <?= [-2.5, 1.8, -1.2, 2.4, -2, 1.4][$i % 6] ?>deg">
          <?= demo_img($foto, $alt, ['ancho' => 520, 'alto' => 560, 'anchos' => [320, 520, 800], 'sizes' => '(max-width: 600px) 44vw, (max-width: 1000px) 30vw, 220px']) ?>
          <p><strong><?= demo_e($nombre) ?></strong><span><?= demo_e($detalle) ?></span></p>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="lc-seccion lc-seccion--marino" id="reservar" aria-labelledby="reservar-titulo">
    <div class="lc-envoltura lc-reserva">
      <div class="lc-reserva__intro">
        <p class="lc-sobre">Pedir hora por WhatsApp</p>
        <h2 id="reservar-titulo">Pide su hora en un minuto</h2>
        <p>Completa la ficha y se abre WhatsApp con el mensaje listo y el precio según la tabla. Te confirmamos la hora exacta dentro del bloque que elijas.</p>
        <ul class="lc-reserva__datos">
          <li><?= $huella ?> Tráelo con collar o arnés y su correa.</li>
          <li><?= $huella ?> Si se pone nervioso, cuéntanos en la ficha.</li>
          <li><?= $huella ?> Si no puedes venir, avísanos y liberamos la hora.</li>
        </ul>
        <p class="lc-reserva__nota">Es una demo: no guardamos nada. El mensaje llega al WhatsApp de quien hizo este sitio.</p>
      </div>
      <form class="lc-ficha" method="post" action="#reservar" novalidate>
        <p class="lc-ficha__titulo"><span>Ficha de hora</span><span class="lc-ficha__sello">La Chasquilla</span></p>
        <?php if ($errores): ?><p class="lc-alerta" role="alert">Revisa los datos marcados.</p><?php endif; ?>
        <div class="lc-ficha__grid">
          <div class="lc-campo lc-campo--ancho">
            <label for="servicio">¿Qué le hacemos?</label>
            <select id="servicio" name="servicio" required<?= $inv('servicio') ?>>
              <option value="">Elige un servicio</option>
              <?php foreach ($n['servicios'] as $id => [$nombre]): ?>
              <option value="<?= demo_e($id) ?>"<?= $val('servicio') === $id ? ' selected' : '' ?>><?= demo_e($nombre) ?></option>
              <?php endforeach; ?>
            </select>
            <?= $err('servicio') ?>
          </div>
          <fieldset class="lc-campo lc-campo--ancho lc-chips"<?= isset($errores['tamano']) ? ' aria-describedby="err-tamano"' : '' ?>>
            <legend>Tamaño</legend>
            <?php foreach ($tamanos as $clave => [$nombre, $peso]): ?>
            <label class="lc-chip"><input type="radio" name="tamano" value="<?= demo_e($clave) ?>"<?= $val('tamano') === $clave ? ' checked' : '' ?> required> <span><?= demo_e($nombre) ?><small><?= demo_e($peso) ?></small></span></label>
            <?php endforeach; ?>
            <?= $err('tamano') ?>
          </fieldset>
          <div class="lc-campo">
            <label for="perro">¿Cómo se llama?</label>
            <input id="perro" name="perro" maxlength="30" required autocomplete="off" placeholder="Ej.: Bruno" value="<?= demo_e($val('perro')) ?>"<?= $inv('perro') ?>>
            <?= $err('perro') ?>
          </div>
          <div class="lc-campo">
            <label for="raza">Raza <small>(opcional)</small></label>
            <input id="raza" name="raza" maxlength="40" autocomplete="off" placeholder="Ej.: quiltro, schnauzer" value="<?= demo_e($val('raza')) ?>">
          </div>
          <div class="lc-campo lc-campo--ancho">
            <label for="pelo">Tipo de pelo <small>(opcional)</small></label>
            <select id="pelo" name="pelo"<?= $inv('pelo') ?>>
              <option value="">No sé: que lo vean ustedes</option>
              <?php foreach ($n['pelos'] as $clave => [$nombre, , $recargo]): ?>
              <option value="<?= demo_e($clave) ?>"<?= $val('pelo') === $clave ? ' selected' : '' ?>><?= demo_e($nombre) ?><?= $recargo > 0 ? ' · +' . demo_e(demo_clp($recargo)) : '' ?></option>
              <?php endforeach; ?>
            </select>
            <?= $err('pelo') ?>
          </div>
          <div class="lc-campo">
            <label for="dia">Día</label>
            <select id="dia" name="dia" required<?= $inv('dia') ?>>
              <option value="">Elige un día</option>
              <?php foreach ($dias as $fecha => $texto): ?>
              <option value="<?= demo_e($fecha) ?>"<?= $val('dia') === $fecha ? ' selected' : '' ?>><?= demo_e($texto) ?></option>
              <?php endforeach; ?>
            </select>
            <?= $err('dia') ?>
          </div>
          <div class="lc-campo">
            <label for="bloque">Bloque</label>
            <select id="bloque" name="bloque" required<?= $inv('bloque') ?>>
              <option value="">Mañana o tarde</option>
              <?php foreach ($n['bloques'] as $clave => $texto): ?>
              <option value="<?= demo_e($clave) ?>"<?= $val('bloque') === $clave ? ' selected' : '' ?>><?= demo_e($texto) ?></option>
              <?php endforeach; ?>
            </select>
            <?= $err('bloque') ?>
          </div>
          <div class="lc-campo lc-campo--ancho">
            <label for="detalle">¿Cómo es? ¿Algo que debamos saber? <small>(opcional)</small></label>
            <textarea id="detalle" name="detalle" rows="3" maxlength="300" placeholder="Ej.: le da miedo el secador, tiene la piel sensible, quiero el corte más corto atrás…"><?= demo_e($val('detalle')) ?></textarea>
          </div>
          <div class="lc-campo lc-campo--ancho">
            <label for="nombre">Tu nombre <small>(opcional)</small></label>
            <input id="nombre" name="nombre" maxlength="40" autocomplete="off" value="<?= demo_e($val('nombre')) ?>">
          </div>
        </div>
        <p class="lc-ficha__pequeno">Los sábados atendemos solo en la mañana. A los gatos, los martes en la mañana.</p>
        <button class="lc-btn lc-btn--bloque" type="submit"><?= demo_icono_wa() ?><span>Enviar por WhatsApp</span></button>
      </form>
    </div>
  </section>

  <section class="lc-seccion" id="horario" aria-labelledby="horario-titulo">
    <div class="lc-envoltura lc-casa">
      <div class="lc-casa__texto">
        <header class="lc-encabezado">
          <p class="lc-sobre">Somos de <?= demo_e($n['comuna']) ?></p>
          <h2 id="horario-titulo">Una peluquería chica, con la puerta abierta al barrio</h2>
        </header>
        <p>La tía Mónica empezó bañando a los perros de la cuadra en el patio de su casa en <?= (int) $n['desde'] ?>. Hoy tiene su local con tina de acero, dos mesas de corte y a su hija Fernanda, que hizo el curso de peluquería canina y se sabe todos los cortes de raza.</p>
        <p>Aquí no hay jaulas de secado ni apuro: un perro a la vez en la tina, toalla, secador a temperatura baja y un premio cuando se porta bien. Si vemos algo raro en la piel o en los oídos, te lo decimos para que lo vea su veterinario.</p>
      </div>
      <div class="lc-horario">
        <p class="lc-horario__titulo">Horario</p>
        <p class="lc-estado-linea"><span class="lc-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario lc-tabla-horario'); ?>
        <p class="lc-pequeno">Uñas y oídos sin hora, dentro del horario. Gatos: martes en la mañana.</p>
        <div class="lc-acciones">
          <a class="lc-btn" href="#reservar"><?= $tijeras ?><span>Pedir hora</span></a>
          <a class="lc-btn lc-btn--claro" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
      </div>
    </div>
  </section>

  <div class="lc-seccion lc-seccion--crema">
    <div class="lc-envoltura">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería la ficha de la peluquería en Google Maps: horario, cómo llegar y lo que dicen los vecinos.']); ?>
    </div>
  </div>

  <section class="lc-cierre" aria-labelledby="cierre-titulo">
    <?= demo_img($n['fotos']['cierre'], '', ['ancho' => 1400, 'alto' => 800, 'sizes' => '100vw', 'anchos' => [600, 1000, 1400], 'clase' => 'lc-cierre__foto']) ?>
    <div class="lc-envoltura lc-cierre__texto">
      <p class="lc-cierre__sobre">¿Ya anda chascón?</p>
      <h2 id="cierre-titulo">Tráelo y te lo devolvemos oliendo a rico.</h2>
      <div class="lc-acciones">
        <a class="lc-btn lc-btn--patito" href="#reservar"><?= $tijeras ?><span>Pedir hora</span></a>
        <a class="lc-btn lc-btn--borde" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
      </div>
    </div>
  </section>
</main>

<footer class="lc-pie">
  <div class="lc-envoltura lc-pie__grid">
    <div>
      <p class="lc-pie__marca"><?= $logo ?> La Chasquilla</p>
      <p>Peluquería canina de barrio en <?= demo_e($n['comuna']) ?>, Santiago.<br>Baño, corte de raza, deslanado, uñas y oídos.</p>
    </div>
    <div>
      <p class="lc-pie__titulo">Horario</p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario lc-pie__horario'); ?>
    </div>
    <div>
      <p class="lc-pie__titulo">Contacto</p>
      <p><a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
      <p><a href="#precios">Precios por tamaño</a> · <a href="#reservar">Pedir hora</a></p>
    </div>
  </div>
  <div class="lc-envoltura"><?php demo_aviso_pie($rubro); ?></div>
</footer>

<nav class="lc-barra" aria-label="Contacto rápido">
  <a href="#reservar"><?= $tijeras ?><span>Pedir hora</span></a>
  <a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
</nav>
<?php demo_scripts(); ?>
</body>
</html>
