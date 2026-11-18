<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version);

/** Enlace de WhatsApp con la nota de pedido de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);
$waGeneral = $wa('Hola, quiero reservar una hora en ' . $n['nombre'] . '. Mi mascota se llama: ');
$estado = demo_horario_estado($n['horario']);

/**
 * Días que se ofrecen para reservar: desde mañana, los próximos 10 días con atención.
 * @return array<string, string> ['2026-11-19' => 'Mañana, jueves 19 de noviembre', ...]
 */
function vc_dias(array $horario, int $cuantos = 10): array
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

/**
 * Ficha de reserva (sin JavaScript): valida en el servidor y redirige a WhatsApp con el mensaje armado.
 * Devuelve [url|null, errores, valores enviados].
 */
function vc_reserva(array $post, array $n, array $dias, string $nota): array
{
    $txt = static function (string $k, int $max) use ($post): string {
        $v = $post[$k] ?? '';
        return is_string($v) ? mb_substr(trim(preg_replace('/\s+/u', ' ', $v) ?? ''), 0, $max) : '';
    };
    $previo = [
        'servicio' => $txt('servicio', 20),
        'mascota' => $txt('mascota', 30),
        'especie' => $txt('especie', 10),
        'edad' => $txt('edad', 20),
        'dia' => $txt('dia', 10),
        'bloque' => $txt('bloque', 10),
        'motivo' => $txt('motivo', 300),
        'nombre' => $txt('nombre', 40),
    ];
    $errores = [];
    if (!isset($n['servicios'][$previo['servicio']])) {
        $errores['servicio'] = 'Elige un servicio de la lista.';
    }
    if (mb_strlen($previo['mascota']) < 2) {
        $errores['mascota'] = 'Cuéntanos cómo se llama tu mascota.';
    }
    if (!isset($n['especies'][$previo['especie']])) {
        $errores['especie'] = 'Elige si es perro, gato u otra especie.';
    }
    if (!isset($dias[$previo['dia']])) {
        $errores['dia'] = 'Elige uno de los días de la lista.';
    }
    if (!isset($n['bloques'][$previo['bloque']])) {
        $errores['bloque'] = 'Elige si prefieres la mañana o la tarde.';
    } elseif ($previo['bloque'] === 'tarde' && isset($dias[$previo['dia']]) && (int) (new DateTimeImmutable($previo['dia']))->format('N') === 6) {
        $errores['bloque'] = 'Los sábados atendemos solo en la mañana.';
    }
    if ($errores) {
        return [null, $errores, $previo];
    }
    [$servicio, , $desde] = $n['servicios'][$previo['servicio']];
    $lineas = [
        'Hola, quiero reservar una hora en ' . $n['nombre'] . '.',
        'Servicio: ' . $servicio . ' (desde ' . demo_clp($desde) . ')',
        'Mascota: ' . $previo['mascota'],
        'Especie: ' . $n['especies'][$previo['especie']],
    ];
    if ($previo['edad'] !== '') {
        $lineas[] = 'Edad: ' . $previo['edad'];
    }
    $lineas[] = 'Día: ' . demo_fecha_larga(new DateTimeImmutable($previo['dia']));
    $lineas[] = 'Bloque: ' . $n['bloques'][$previo['bloque']];
    if ($previo['motivo'] !== '') {
        $lineas[] = 'Motivo: ' . $previo['motivo'];
    }
    if ($previo['nombre'] !== '') {
        $lineas[] = 'Nombre: ' . $previo['nombre'];
    }
    $lineas[] = $nota;
    return [demo_wa(implode("\n", $lineas)), [], $previo];
}

$dias = vc_dias($n['horario']);
$errores = [];
$previo = ['servicio' => is_string($_GET['servicio'] ?? null) && isset($n['servicios'][$_GET['servicio']]) ? $_GET['servicio'] : ''];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    [$destino, $errores, $previo] = vc_reserva($_POST, $n, $dias, $nota);
    if ($destino !== null) {
        header('Location: ' . $destino, true, 303);
        exit;
    }
    http_response_code(422);
}
$val = static fn (string $k): string => (string) ($previo[$k] ?? '');
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="vc-error" id="err-' . demo_e($k) . '">' . demo_e($errores[$k]) . '</p>' : '';
$inv = static fn (string $k): string => isset($errores[$k]) ? ' aria-invalid="true" aria-describedby="err-' . demo_e($k) . '"' : '';
$reservar = static fn (string $id): string => '?servicio=' . rawurlencode($id) . '#reservar';

/** Íconos de línea (24×24) de cada servicio. */
$iconos = [
    'consulta' => '<path d="M6 3v6a4 4 0 0 0 8 0V3"/><path d="M10 13v2.5a5 5 0 0 0 10 0V13"/><circle cx="20" cy="11" r="2"/>',
    'control' => '<path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/><path d="M8.5 11.5h2l1-2 1.5 4 1-2h1.5"/>',
    'vacuna-perro' => '<path d="m14.5 4.5 5 5M17 7l-9 9-3.5.5.5-3.5 9-9M5 19l-2 2M9.5 11.5l2 2M12 9l2 2M18.5 2.5l3 3"/>',
    'vacuna-gato' => '<path d="m14.5 4.5 5 5M17 7l-9 9-3.5.5.5-3.5 9-9M5 19l-2 2M9.5 11.5l2 2M12 9l2 2M18.5 2.5l3 3"/>',
    'antirrabica' => '<path d="M12 3 5 6v5c0 4.5 3 8 7 10 4-2 7-5.5 7-10V6z"/><path d="m9 12 2 2 4-4"/>',
    'desparasitacion' => '<path d="M12 3s6 6.4 6 11a6 6 0 0 1-12 0c0-4.6 6-11 6-11z"/><path d="M9.5 15a2.5 2.5 0 0 0 2.5 2.5"/>',
    'esterilizacion' => '<rect x="3" y="8.5" width="18" height="7" rx="3.5" transform="rotate(-45 12 12)"/><path d="M10.5 10.5h.01M13.5 13.5h.01M10.5 13.5h.01M13.5 10.5h.01"/>',
    'microchip' => '<rect x="7" y="7" width="10" height="10" rx="2"/><path d="M10 3v4M14 3v4M10 17v4M14 17v4M3 10h4M3 14h4M17 10h4M17 14h4"/>',
    'peluqueria' => '<circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M20 4 8.1 15.9M14.5 14.5 20 20M8.1 8.1 12 12"/>',
    'urgencia' => '<path d="M7 18v-6a5 5 0 0 1 10 0v6M5 21h14M12 2.5v2M4.2 6.2l1.4 1.4M19.8 6.2l-1.4 1.4"/>',
];
$svg = static fn (string $trazos, int $tam = 26): string => '<svg viewBox="0 0 24 24" width="' . $tam . '" height="' . $tam . '" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $trazos . '</svg>';
$huella = '<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="currentColor"><ellipse cx="6.2" cy="9.4" rx="2" ry="2.6" transform="rotate(-18 6.2 9.4)"/><ellipse cx="10.2" cy="5.6" rx="2" ry="2.7"/><ellipse cx="14.8" cy="5.6" rx="2" ry="2.7"/><ellipse cx="18.8" cy="9.4" rx="2" ry="2.6" transform="rotate(18 18.8 9.4)"/><path d="M12.5 11.5c-3 0-5.6 3.2-5.6 5.5 0 1.6 1.3 2.5 2.8 2.5 1.2 0 1.8-.6 2.8-.6s1.6.6 2.8.6c1.5 0 2.8-.9 2.8-2.5 0-2.3-2.6-5.5-5.6-5.5z"/></svg>';
$cascabel = '<svg viewBox="0 0 40 40" width="40" height="40" aria-hidden="true" focusable="false"><circle cx="20" cy="7" r="3.4" fill="none" stroke="currentColor" stroke-width="2.4"/><circle cx="20" cy="23" r="13" fill="#f2b440" stroke="currentColor" stroke-width="2.4"/><path d="M7.6 20.5c7.6 3.4 17.2 3.4 24.8 0" fill="none" stroke="currentColor" stroke-width="2.2"/><circle cx="20" cy="28.6" r="2.3" fill="currentColor"/><path d="M20 30.4v5.4" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>';

demo_head([
    'titulo' => $n['nombre'] . ' · Veterinaria en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para una veterinaria de barrio en ' . $n['comuna'] . ', Santiago: consultas, vacunas para perro y gato, esterilización, peluquería y reserva de hora por WhatsApp.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Nanum+Pen+Script&family=Nunito+Sans:wght@400;600;700;800&display=swap',
    'css' => ['/demos/veterinaria/simple/estilo.css'],
    'tema' => '#fbf3e6',
    'icono' => ['C', '#b44a28', '#fbf3e6'],
    'clase_body' => 'vc',
]);
demo_aviso($rubro);
?>
<header class="vc-top">
  <div class="vc-envoltura vc-top__fila">
    <a class="vc-marca" href="#contenido">
      <span class="vc-marca__sello"><?= $cascabel ?></span>
      <span class="vc-marca__nombre">Cascabel<small>veterinaria de barrio · <?= demo_e($n['comuna']) ?></small></span>
    </a>
    <nav class="vc-nav" aria-label="Secciones">
      <a href="#servicios">Servicios</a>
      <a href="#vacunas">Vacunas</a>
      <a href="#esterilizacion">Esterilización</a>
      <a href="#clinica">La clínica</a>
      <a href="#horario">Horario</a>
    </nav>
    <a class="vc-btn vc-btn--chico" href="#reservar"><?= $huella ?><span>Reservar hora</span></a>
  </div>
</header>

<main id="contenido">
  <section class="vc-hero" aria-labelledby="hero-titulo">
    <div class="vc-envoltura vc-hero__grid">
      <div class="vc-hero__texto">
        <p class="vc-etiqueta"><span aria-hidden="true">✿</span> En <?= demo_e($n['comuna']) ?> desde <?= (int) $n['desde'] ?></p>
        <h1 id="hero-titulo">Aquí tu mascota <span>es vecina.</span></h1>
        <p class="vc-hero__bajada">Consultas, vacunas, esterilización y peluquería con la <?= demo_e($n['doctora']) ?> y su equipo. Te explicamos todo sin apuro, con los precios a la vista y el carnet siempre al día.</p>
        <p class="vc-estado-linea"><span class="vc-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <div class="vc-acciones">
          <a class="vc-btn" href="#reservar"><?= $huella ?><span>Reservar hora</span></a>
          <a class="vc-btn vc-btn--claro" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
        <p class="vc-hero__urgencia"><strong>¿Es una urgencia?</strong> Ven sin hora dentro de nuestro horario: pasas primero.</p>
      </div>
      <div class="vc-hero__foto">
        <span class="vc-mancha" aria-hidden="true"></span>
        <figure class="vc-blob">
          <?= demo_img($n['fotos']['hero'], 'Veterinaria sonriente abrazando a una gata blanca de pelo largo en el jardín', ['lazy' => false, 'ancho' => 800, 'alto' => 900, 'sizes' => '(max-width: 860px) 88vw, 460px', 'anchos' => [480, 800, 1100]]) ?>
        </figure>
        <aside class="vc-carnet-mini" aria-label="Ejemplo de carnet de salud">
          <p class="vc-carnet-mini__titulo">Carnet de salud</p>
          <p class="vc-carnet-mini__nombre">Mota <small>gata · 3 años</small></p>
          <ul>
            <li><span aria-hidden="true">✓</span> Triple felina</li>
            <li><span aria-hidden="true">✓</span> Antirrábica</li>
            <li><span aria-hidden="true">✓</span> Control sano</li>
          </ul>
          <p class="vc-firma">Dra. Paula R.</p>
        </aside>
        <p class="vc-sticker" aria-hidden="true"><span>sin apuro</span><strong><?= (int) $n['desde'] ?></strong></p>
      </div>
    </div>
  </section>

  <section class="vc-promesas" aria-label="Lo que prometemos">
    <ul class="vc-envoltura">
      <?php foreach ($n['promesas'] as $p): ?>
      <li><?= $huella ?><span><?= demo_e($p) ?></span></li>
      <?php endforeach; ?>
    </ul>
  </section>

  <section class="vc-seccion" id="servicios" aria-labelledby="servicios-titulo">
    <div class="vc-envoltura">
      <header class="vc-encabezado">
        <p class="vc-sobre">Servicios y precios</p>
        <h2 id="servicios-titulo">Lo que más nos piden</h2>
        <p>Precios «desde» con todo incluido. Si tu mascota necesita algo más, te lo decimos antes de hacerlo y tú decides.</p>
      </header>
      <div class="vc-destacados">
        <?php foreach ($n['destacados'] as $id => $d): [$nombre, $detalle, $desde] = $n['servicios'][$id]; ?>
        <article class="vc-destacado">
          <div class="vc-destacado__foto">
            <?= demo_img($d['foto'], $d['alt'], ['ancho' => 600, 'alto' => 480, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 90vw, 33vw']) ?>
            <span class="vc-cinta"><?= demo_e($d['sello']) ?></span>
          </div>
          <div class="vc-destacado__texto">
            <h3><?= demo_e($nombre) ?></h3>
            <p><?= demo_e($detalle) ?></p>
            <p class="vc-desde"><small>desde</small> <?= demo_e(demo_clp($desde)) ?></p>
            <a class="vc-enlace" href="<?= demo_e($reservar($id)) ?>">Reservar esta hora</a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <div class="vc-tarifario">
      <div class="vc-tarifario__cabeza"><h3>Tarifario completo</h3><p class="vc-mano">todo incluido, sin letra chica</p></div>
      <ul class="vc-servicios" aria-label="Todos los servicios">
        <?php foreach ($n['servicios'] as $id => [$nombre, $detalle, $desde]): ?>
        <li class="vc-servicio">
          <span class="vc-servicio__icono"><?= $svg($iconos[$id] ?? $iconos['consulta']) ?></span>
          <div class="vc-servicio__texto">
            <h3><?= demo_e($nombre) ?></h3>
            <p><?= demo_e($detalle) ?></p>
          </div>
          <p class="vc-servicio__pie">
            <span class="vc-desde"><small>desde</small> <?= demo_e(demo_clp($desde)) ?></span>
            <a class="vc-servicio__pedir" href="<?= demo_e($reservar($id)) ?>" aria-label="Reservar <?= demo_e($nombre) ?>">Reservar</a>
          </p>
        </li>
        <?php endforeach; ?>
      </ul>
      </div>
      <p class="vc-nota-medica">Los medicamentos y antiparasitarios siempre los indica la doctora después de revisar a tu mascota. No damos dosis por teléfono ni por WhatsApp.</p>
    </div>
  </section>

  <section class="vc-seccion vc-seccion--rosa" id="vacunas" aria-labelledby="vacunas-titulo">
    <div class="vc-envoltura">
      <header class="vc-encabezado vc-encabezado--centro">
        <p class="vc-sobre">Carnet de vacunas</p>
        <h2 id="vacunas-titulo">Una vacuna, un sticker, un carnet al día</h2>
        <p>Cada vacuna queda pegada en el carnet con su fecha. Te avisamos por WhatsApp cuando toca la siguiente, para que no tengas que acordarte.</p>
      </header>
      <div class="vc-libreta">
        <?php foreach (['perro' => 'Perros', 'gato' => 'Gatos'] as $especie => $titulo): ?>
        <section class="vc-pagina" aria-labelledby="carnet-<?= demo_e($especie) ?>">
          <h3 id="carnet-<?= demo_e($especie) ?>"><?= $huella ?> <?= demo_e($titulo) ?></h3>
          <ul>
            <?php foreach ($n['vacunas'][$especie] as $i => [$vacuna, $para, $precio]): ?>
            <li class="vc-vacuna">
              <span class="vc-vacuna__sticker" aria-hidden="true"><b><?= demo_e(mb_strtoupper(mb_substr($vacuna, 0, 3))) ?></b><small>lote · · ·</small></span>
              <div>
                <h4><?= demo_e($vacuna) ?></h4>
                <p><?= demo_e($para) ?></p>
              </div>
              <p class="vc-vacuna__precio"><?= demo_e(demo_clp($precio)) ?></p>
            </li>
            <?php endforeach; ?>
          </ul>
          <a class="vc-enlace" href="<?= demo_e($reservar($especie === 'perro' ? 'vacuna-perro' : 'vacuna-gato')) ?>">Agendar vacuna para <?= $especie === 'perro' ? 'mi perro' : 'mi gato' ?></a>
        </section>
        <?php endforeach; ?>
      </div>

      <div class="vc-primer" aria-labelledby="primer-titulo">
        <div class="vc-primer__fotos" aria-hidden="true">
          <?= demo_img($n['fotos']['cachorro'], '', ['ancho' => 400, 'alto' => 400, 'anchos' => [300, 400, 600], 'sizes' => '180px', 'clase' => 'vc-primer__a']) ?>
          <?= demo_img($n['fotos']['gatito'], '', ['ancho' => 400, 'alto' => 400, 'anchos' => [300, 400, 600], 'sizes' => '150px', 'clase' => 'vc-primer__b']) ?>
        </div>
        <div>
          <h3 id="primer-titulo">¿Llegó un cachorro o un gatito a la casa?</h3>
          <p>Así es su primer año con nosotros. Las fechas exactas las decide la doctora según su edad y su salud, y quedan escritas en el carnet.</p>
          <ol class="vc-pasos">
            <?php foreach ($n['primer_anio'] as $i => [$titulo, $texto]): ?>
            <li><span class="vc-pasos__num" aria-hidden="true"><?= $i + 1 ?></span><div><strong><?= demo_e($titulo) ?></strong><span><?= demo_e($texto) ?></span></div></li>
            <?php endforeach; ?>
          </ol>
          <a class="vc-btn" href="<?= demo_e($reservar('control')) ?>"><?= $huella ?><span>Reservar su primer control</span></a>
        </div>
      </div>
    </div>
  </section>

  <section class="vc-seccion vc-seccion--terracota" id="esterilizacion" aria-labelledby="esterilizacion-titulo">
    <div class="vc-envoltura vc-ester">
      <figure class="vc-ester__foto">
        <?= demo_img($n['fotos']['esterilizacion'], 'Gata gris con collar isabelino sentada tranquila en un piso de madera, en recuperación', ['ancho' => 700, 'alto' => 800, 'sizes' => '(max-width: 860px) 90vw, 40vw']) ?>
      </figure>
      <div>
        <header class="vc-encabezado vc-encabezado--claro">
          <p class="vc-sobre">Esterilización</p>
          <h2 id="esterilizacion-titulo">Un precio claro, sin sorpresas en la cuenta</h2>
          <p>Incluye la evaluación previa, la cirugía con anestesia y monitoreo, el collar o traje para la recuperación y el control a los 10 días.</p>
        </header>
        <dl class="vc-precios">
          <?php foreach ($n['esterilizacion'] as [$quien, $precio]): ?>
          <div><dt><?= demo_e($quien) ?></dt><dd><?= demo_e(demo_clp($precio)) ?></dd></div>
          <?php endforeach; ?>
        </dl>
        <p class="vc-ester__nota">Los exámenes de sangre previos se cotizan aparte ($22.000) y te los pedimos solo si la doctora los considera necesarios.</p>
        <a class="vc-btn vc-btn--crema" href="<?= demo_e($reservar('esterilizacion')) ?>"><?= $huella ?><span>Reservar evaluación</span></a>
      </div>
    </div>
  </section>

  <section class="vc-seccion vc-seccion--papel" id="reservar" aria-labelledby="reservar-titulo">
    <div class="vc-envoltura vc-reserva">
      <div class="vc-reserva__intro">
        <p class="vc-sobre">Reserva por WhatsApp</p>
        <h2 id="reservar-titulo">Reserva tu hora en un minuto</h2>
        <p>Completa la ficha y se abre WhatsApp con el mensaje listo. Te confirmamos la hora exacta dentro del bloque que elijas.</p>
        <ul class="vc-reserva__datos">
          <li><?= $huella ?> Si es la primera vez, trae el carnet de vacunas si lo tienes.</li>
          <li><?= $huella ?> Los gatos, siempre en transportadora.</li>
          <li><?= $huella ?> Si no puedes venir, avísanos y liberamos la hora.</li>
        </ul>
        <p class="vc-reserva__nota">Es una demo: no guardamos nada. El mensaje llega al WhatsApp de quien hizo este sitio.</p>
      </div>
      <form class="vc-ficha" method="post" action="#reservar" novalidate>
        <p class="vc-ficha__titulo"><span>Ficha de reserva</span><span class="vc-mano">¡nos vemos pronto!</span></p>
        <?php if ($errores): ?><p class="vc-alerta" role="alert">Revisa los datos marcados.</p><?php endif; ?>
        <div class="vc-ficha__grid">
          <div class="vc-campo vc-campo--ancho">
            <label for="servicio">¿Qué necesita?</label>
            <select id="servicio" name="servicio" required<?= $inv('servicio') ?>>
              <option value="">Elige un servicio</option>
              <?php foreach ($n['servicios'] as $id => [$nombre, , $desde]): ?>
              <option value="<?= demo_e($id) ?>"<?= $val('servicio') === $id ? ' selected' : '' ?>><?= demo_e($nombre) ?> · desde <?= demo_e(demo_clp($desde)) ?></option>
              <?php endforeach; ?>
            </select>
            <?= $err('servicio') ?>
          </div>
          <div class="vc-campo">
            <label for="mascota">Nombre de tu mascota</label>
            <input id="mascota" name="mascota" maxlength="30" required autocomplete="off" placeholder="Ej.: Canela" value="<?= demo_e($val('mascota')) ?>"<?= $inv('mascota') ?>>
            <?= $err('mascota') ?>
          </div>
          <div class="vc-campo">
            <label for="edad">Edad <small>(opcional)</small></label>
            <input id="edad" name="edad" maxlength="20" autocomplete="off" placeholder="Ej.: 3 años, 4 meses" value="<?= demo_e($val('edad')) ?>">
          </div>
          <fieldset class="vc-campo vc-campo--ancho vc-chips"<?= isset($errores['especie']) ? ' aria-describedby="err-especie"' : '' ?>>
            <legend>Especie</legend>
            <?php foreach ($n['especies'] as $clave => $nombre): ?>
            <label class="vc-chip"><input type="radio" name="especie" value="<?= demo_e($clave) ?>"<?= $val('especie') === $clave ? ' checked' : '' ?> required> <span><?= demo_e($nombre) ?></span></label>
            <?php endforeach; ?>
            <?= $err('especie') ?>
          </fieldset>
          <div class="vc-campo">
            <label for="dia">Día</label>
            <select id="dia" name="dia" required<?= $inv('dia') ?>>
              <option value="">Elige un día</option>
              <?php foreach ($dias as $fecha => $texto): ?>
              <option value="<?= demo_e($fecha) ?>"<?= $val('dia') === $fecha ? ' selected' : '' ?>><?= demo_e($texto) ?></option>
              <?php endforeach; ?>
            </select>
            <?= $err('dia') ?>
          </div>
          <div class="vc-campo">
            <label for="bloque">Bloque</label>
            <select id="bloque" name="bloque" required<?= $inv('bloque') ?>>
              <option value="">Mañana o tarde</option>
              <?php foreach ($n['bloques'] as $clave => $texto): ?>
              <option value="<?= demo_e($clave) ?>"<?= $val('bloque') === $clave ? ' selected' : '' ?>><?= demo_e($texto) ?></option>
              <?php endforeach; ?>
            </select>
            <?= $err('bloque') ?>
          </div>
          <div class="vc-campo vc-campo--ancho">
            <label for="motivo">¿Qué le pasa o qué te preocupa? <small>(opcional)</small></label>
            <textarea id="motivo" name="motivo" rows="3" maxlength="300" placeholder="Ej.: se rasca mucho las orejas, no quiere comer desde ayer…"><?= demo_e($val('motivo')) ?></textarea>
          </div>
          <div class="vc-campo vc-campo--ancho">
            <label for="nombre">Tu nombre <small>(opcional)</small></label>
            <input id="nombre" name="nombre" maxlength="40" autocomplete="off" value="<?= demo_e($val('nombre')) ?>">
          </div>
        </div>
        <p class="vc-ficha__pequeno">Los sábados atendemos solo en la mañana.</p>
        <button class="vc-btn vc-btn--bloque" type="submit"><?= demo_icono_wa() ?><span>Enviar por WhatsApp</span></button>
      </form>
    </div>
  </section>

  <section class="vc-seccion" id="clinica" aria-labelledby="clinica-titulo">
    <div class="vc-envoltura vc-clinica">
      <div class="vc-clinica__fotos">
        <figure class="vc-clinica__a">
          <?= demo_img($n['fotos']['equipo'], 'Dos veterinarios con pijama clínico azul atendiendo a un perro en la mesa de la consulta', ['ancho' => 800, 'alto' => 640, 'sizes' => '(max-width: 860px) 90vw, 46vw']) ?>
        </figure>
        <figure class="vc-clinica__b">
          <?= demo_img($n['fotos']['caricia'], 'Manos acariciando la cabeza de un perro que cierra los ojos tranquilo', ['ancho' => 500, 'alto' => 500, 'anchos' => [300, 500, 800], 'sizes' => '(max-width: 860px) 46vw, 22vw']) ?>
        </figure>
      </div>
      <div class="vc-clinica__texto">
        <header class="vc-encabezado">
          <p class="vc-sobre">La clínica</p>
          <h2 id="clinica-titulo">Una consulta chica, con la puerta abierta al barrio</h2>
        </header>
        <p>La doctora Paula abrió Cascabel en <?= (int) $n['desde'] ?>, en el mismo local donde antes estaba la librería de la esquina. Hoy atiende con el doctor Rodrigo y con Marta, que conoce a cada mascota por su nombre.</p>
        <p>Nos gusta explicar con calma, dibujar en un papel lo que pasa y mandarte por WhatsApp lo que conversamos. Si algo se puede resolver en casa, te lo decimos; si necesita un especialista, te derivamos sin vueltas.</p>
        <p class="vc-mano vc-mano--grande">«Aquí nadie es solo un paciente: es la Canela del 4, el Tobi de la feria.»</p>
        <dl class="vc-cifras">
          <div><dt>En el barrio desde</dt><dd><?= (int) $n['desde'] ?></dd></div>
          <div><dt>Mascotas con ficha</dt><dd>+4.000</dd></div>
          <div><dt>Médicos veterinarios</dt><dd>2</dd></div>
        </dl>
      </div>
    </div>
  </section>

  <section class="vc-seccion vc-seccion--papel" id="horario" aria-labelledby="horario-titulo">
    <div class="vc-envoltura vc-horario">
      <div class="vc-tarjeta">
        <header class="vc-encabezado">
          <p class="vc-sobre">Horario</p>
          <h2 id="horario-titulo">Cuándo atendemos</h2>
        </header>
        <p class="vc-estado-linea"><span class="vc-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario vc-tabla-horario'); ?>
        <p class="vc-pequeno">Con hora o sin hora. Las reservas tienen prioridad, salvo una urgencia.</p>
      </div>
      <div class="vc-tarjeta vc-tarjeta--urgencia">
        <p class="vc-sobre">Urgencias</p>
        <h3>Sin hora, en todo nuestro horario</h3>
        <p>De lunes a viernes de 10:00 a 20:00 y los sábados de 10:00 a 14:00, si tu mascota tiene una urgencia, ven directo: pasa primero.</p>
        <p><strong>No somos una clínica 24 horas.</strong> Fuera de horario, llámanos y la grabación te indica el servicio de urgencia más cercano; al día siguiente revisamos a tu mascota y vemos cómo sigue.</p>
        <a class="vc-btn vc-btn--cacao" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar a la clínica</span></a>
      </div>
    </div>
  </section>

  <div class="vc-seccion">
    <div class="vc-envoltura">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería la ficha de la veterinaria en Google Maps: horario, cómo llegar y lo que dicen los vecinos.']); ?>
    </div>
  </div>

  <section class="vc-cierre" aria-labelledby="cierre-titulo">
    <?= demo_img($n['fotos']['cierre'], '', ['ancho' => 1400, 'alto' => 800, 'sizes' => '100vw', 'anchos' => [600, 1000, 1400], 'clase' => 'vc-cierre__foto']) ?>
    <div class="vc-envoltura vc-cierre__texto">
      <p class="vc-mano vc-mano--grande">¿Le toca su control?</p>
      <h2 id="cierre-titulo">Te esperamos con una galleta en el mesón.</h2>
      <div class="vc-acciones">
        <a class="vc-btn vc-btn--crema" href="#reservar"><?= $huella ?><span>Reservar hora</span></a>
        <a class="vc-btn vc-btn--borde" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
      </div>
    </div>
  </section>
</main>

<footer class="vc-pie">
  <div class="vc-envoltura vc-pie__grid">
    <div>
      <p class="vc-pie__marca"><?= $cascabel ?> Cascabel</p>
      <p>Veterinaria de barrio en <?= demo_e($n['comuna']) ?>, Santiago.<br>Consultas, vacunas, esterilización y peluquería.</p>
    </div>
    <div>
      <p class="vc-pie__titulo">Horario</p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario vc-pie__horario'); ?>
    </div>
    <div>
      <p class="vc-pie__titulo">Contacto</p>
      <p><a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
      <p><a href="#reservar">Reservar hora</a></p>
    </div>
  </div>
  <div class="vc-envoltura"><?php demo_aviso_pie($rubro); ?></div>
</footer>

<nav class="vc-barra" aria-label="Contacto rápido">
  <a href="#reservar"><?= $huella ?><span>Reservar</span></a>
  <a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
</nav>
<?php demo_scripts(); ?>
</body>
</html>
