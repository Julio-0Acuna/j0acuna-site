<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version, 'Reserva');

/** Enlace de WhatsApp con la nota de reserva de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);
$waGeneral = $wa('Hola, quiero pedir una hora en ' . $n['nombre'] . '.');
$estado = demo_horario_estado($n['horario']);

/**
 * Días que se ofrecen para reservar: desde hoy (si aún queda más de una hora antes de cerrar), 12 días abiertos.
 * @return array<string, string> ['2026-11-19' => 'Mañana, miércoles 19 de noviembre', ...]
 */
function dl_dias(array $horario, int $cuantos = 12): array
{
    $tz = new DateTimeZone(DEMO_ZONA_HORARIA);
    $ahora = new DateTimeImmutable('now', $tz);
    $hoy = $ahora->setTime(0, 0);
    $minAhora = (int) $ahora->format('G') * 60 + (int) $ahora->format('i');
    $dias = [];
    for ($i = 0; count($dias) < $cuantos && $i <= 30; $i++) {
        $d = $hoy->modify('+' . $i . ' days');
        $h = $horario[(int) $d->format('N')] ?? null;
        if (!$h || ($i === 0 && $minAhora > demo_minutos($h[1]) - 60)) {
            continue;
        }
        $texto = demo_fecha_larga($d);
        $dias[$d->format('Y-m-d')] = match ($i) {
            0 => 'Hoy, ' . $texto,
            1 => 'Mañana, ' . $texto,
            default => mb_strtoupper(mb_substr($texto, 0, 1)) . mb_substr($texto, 1),
        };
    }
    return $dias;
}

/** Horas de la lista (cada 30 min); el servidor revisa que calcen con el horario del día elegido. */
function dl_horas(): array
{
    $horas = [];
    for ($m = 9 * 60 + 30; $m <= 19 * 60 + 30; $m += 30) {
        $horas[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
    }
    return $horas;
}

/**
 * Formulario de reserva (sin JavaScript): valida en el servidor y redirige a WhatsApp
 * con el mensaje armado. Devuelve [url|null, errores, valores enviados].
 */
function dl_reserva(array $post, array $n, string $nota, array $dias): array
{
    $txt = static function (string $k, int $max) use ($post): string {
        $v = $post[$k] ?? '';
        return is_string($v) ? mb_substr(trim(preg_replace('/\s+/u', ' ', $v) ?? ''), 0, $max) : '';
    };
    $previo = [
        'servicio' => $txt('servicio', 20),
        'barbero' => $txt('barbero', 20),
        'dia' => $txt('dia', 10),
        'hora' => $txt('hora', 5),
        'nombre' => $txt('nombre', 40),
        'nota' => $txt('nota', 200),
    ];
    $errores = [];
    if (!isset($n['servicios'][$previo['servicio']])) {
        $errores['servicio'] = 'Elige un servicio de la lista.';
    }
    if (!isset($n['barberos'][$previo['barbero']])) {
        $errores['barbero'] = 'Elige con quién te quieres atender.';
    }
    if (!isset($dias[$previo['dia']])) {
        $errores['dia'] = 'Elige uno de los días de la lista.';
    }
    if (!in_array($previo['hora'], dl_horas(), true)) {
        $errores['hora'] = 'Elige una hora de la lista.';
    } elseif (!isset($errores['dia']) && !isset($errores['servicio'])) {
        $d = new DateTimeImmutable($previo['dia'], new DateTimeZone(DEMO_ZONA_HORARIA));
        [$abre, $cierra] = $n['horario'][(int) $d->format('N')];
        $inicio = demo_minutos($previo['hora']);
        $fin = $inicio + $n['servicios'][$previo['servicio']][2];
        $ahora = new DateTimeImmutable('now', new DateTimeZone(DEMO_ZONA_HORARIA));
        if ($inicio < demo_minutos($abre) || $fin > demo_minutos($cierra)) {
            $errores['hora'] = 'Ese día atendemos de ' . $abre . ' a ' . $cierra . ': elige otra hora.';
        } elseif ($d->format('Y-m-d') === $ahora->format('Y-m-d') && $inicio < (int) $ahora->format('G') * 60 + (int) $ahora->format('i') + 30) {
            $errores['hora'] = 'Para hoy, elige una hora con al menos 30 minutos de anticipación.';
        }
    }
    if ($errores) {
        return [null, $errores, $previo];
    }
    [$servicio, , $minutos, $precio] = $n['servicios'][$previo['servicio']];
    $lineas = [
        'Hola, quiero reservar una hora en ' . $n['nombre'] . '.',
        'Servicio: ' . $servicio . ' (' . $minutos . ' min · ' . demo_clp($precio) . ')',
        'Barbero: ' . $n['barberos'][$previo['barbero']][0],
        'Día: ' . mb_strtolower($dias[$previo['dia']]),
        'Hora: ' . $previo['hora'],
    ];
    if ($previo['nombre'] !== '') {
        $lineas[] = 'Nombre: ' . $previo['nombre'];
    }
    if ($previo['nota'] !== '') {
        $lineas[] = 'Nota: ' . $previo['nota'];
    }
    $lineas[] = $nota;
    return [demo_wa(implode("\n", $lineas)), [], $previo];
}

$dias = dl_dias($n['horario']);
$errores = [];
$previo = [
    'servicio' => is_string($_GET['servicio'] ?? null) && isset($n['servicios'][$_GET['servicio']]) ? $_GET['servicio'] : '',
    'barbero' => 'cualquiera',
];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    [$destino, $errores, $previo] = dl_reserva($_POST, $n, $nota, $dias);
    if ($destino !== null) {
        header('Location: ' . $destino, true, 303);
        exit;
    }
    http_response_code(422);
}
$val = static fn (string $k): string => (string) ($previo[$k] ?? '');
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="dl-error" id="err-' . demo_e($k) . '">' . demo_e($errores[$k]) . '</p>' : '';
$inv = static fn (string $k): string => isset($errores[$k]) ? ' aria-invalid="true" aria-describedby="err-' . demo_e($k) . '"' : '';

$icoTijera = '<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M8.6 7.6 20 18M8.6 16.4 20 6"/></g></svg>';

demo_head([
    'titulo' => $n['nombre'] . ' · Barbería en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para una barbería de barrio en ' . $n['comuna'] . ', Santiago: lista de precios con duración, planes del mes y reserva de hora por WhatsApp.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800&family=Ultra&family=Yellowtail&display=swap',
    'css' => ['/demos/barberia/simple/estilo.css'],
    'tema' => '#1f4a42',
    'icono' => ['L', '#c0392b', '#f6efe2'],
    'clase_body' => 'dl',
]);
demo_aviso($rubro);
?>
<header class="dl-top">
  <div class="dl-envoltura dl-top__fila">
    <a class="dl-marca" href="#contenido">
      <span class="dl-poste" aria-hidden="true"></span>
      <span class="dl-marca__texto"><small>Barbería</small>Don Lalo</span>
    </a>
    <nav class="dl-nav" aria-label="Secciones">
      <a href="#precios">Precios</a>
      <a href="#reservar">Reservar</a>
      <a href="#barberia">La barbería</a>
      <a href="#trabajos">Trabajos</a>
      <a href="#horario">Horario</a>
    </nav>
    <a class="dl-btn dl-btn--chico" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>WhatsApp</span></a>
  </div>
</header>

<main id="contenido">
  <section class="dl-hero" aria-labelledby="hero-titulo">
    <div class="dl-envoltura dl-hero__grid">
      <div class="dl-hero__texto">
        <p class="dl-guion">Barbería de barrio</p>
        <h1 id="hero-titulo">Corte de siempre, conversa de siempre.</h1>
        <p class="dl-hero__bajada">En la misma esquina de <?= demo_e($n['comuna']) ?> desde <?= (int) $n['desde'] ?>. Corte clásico, degradado, barba y afeitado con toalla caliente, a precio de barrio. Pide tu hora por WhatsApp o llega y espera tu turno.</p>
        <p class="dl-estado-linea"><span class="dl-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <div class="dl-acciones">
          <a class="dl-btn" href="#reservar"><?= $icoTijera ?><span>Reservar hora</span></a>
          <a class="dl-btn dl-btn--borde" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
      </div>
      <figure class="dl-hero__espejo">
        <div class="dl-espejo">
          <?= demo_img($n['fotos']['hero'], 'Barbero riéndose mientras le corta el pelo a un cliente en la barbería', ['lazy' => false, 'ancho' => 800, 'alto' => 1000, 'sizes' => '(max-width: 860px) 86vw, 420px', 'anchos' => [480, 800, 1100]]) ?>
        </div>
        <p class="dl-sticker" aria-hidden="true"><span>Corte</span><strong>$9.000</strong></p>
        <figcaption>Nico, el hijo de Don Lalo, en el sillón del medio</figcaption>
      </figure>
    </div>
    <div class="dl-ajedrez" aria-hidden="true"></div>
  </section>

  <section class="dl-seccion" id="precios" aria-labelledby="precios-titulo">
    <div class="dl-envoltura">
      <header class="dl-encabezado">
        <p class="dl-guion">La pizarra</p>
        <h2 id="precios-titulo">Precios claros, sin sorpresas</h2>
        <p>Lo que dice la pizarra es lo que pagas. Al lado de cada servicio, cuánto dura, para que sepas a qué hora sales.</p>
      </header>
      <div class="dl-pizarra">
        <p class="dl-pizarra__titulo">Lista de precios</p>
        <ul class="dl-precios">
          <?php foreach ($n['servicios'] as $id => [$nombre, $detalle, $minutos, $precio]): ?>
          <li class="dl-precio">
            <strong class="dl-precio__nombre"><?= demo_e($nombre) ?></strong>
            <span class="dl-precio__detalle"><?= (int) $minutos ?> min · <?= demo_e($detalle) ?></span>
            <span class="dl-precio__valor"><?= demo_e(demo_clp($precio)) ?></span>
            <a class="dl-precio__reservar" href="?servicio=<?= demo_e($id) ?>#reservar" aria-label="Reservar <?= demo_e($nombre) ?>">Reservar</a>
          </li>
          <?php endforeach; ?>
        </ul>
        <p class="dl-pizarra__pie">Efectivo, débito o transferencia · Con hora o por orden de llegada</p>
      </div>

      <div class="dl-planes" aria-label="Planes del mes">
        <?php foreach ($n['planes'] as [$plan, $incluye, $precio, $detalle]): ?>
        <article class="dl-plan">
          <p class="dl-plan__sello">Plan del mes</p>
          <h3><?= demo_e($plan) ?></h3>
          <p class="dl-plan__incluye"><?= demo_e($incluye) ?></p>
          <p class="dl-plan__precio"><?= demo_e(demo_clp($precio)) ?> <small>al mes</small></p>
          <p><?= demo_e($detalle) ?></p>
          <a class="dl-enlace" href="<?= demo_e($wa('Hola, quiero el ' . $plan . ' (' . $incluye . ', ' . demo_clp($precio) . ' al mes) en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Lo quiero</a>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="dl-seccion dl-seccion--azulejo" id="servicios" aria-labelledby="servicios-titulo">
    <div class="dl-envoltura">
      <header class="dl-encabezado">
        <p class="dl-guion">Lo más pedido</p>
        <h2 id="servicios-titulo">Lo que sale de este sillón</h2>
      </header>
      <div class="dl-destacados">
        <?php foreach ($n['destacados'] as $id => $d): [$nombre, $detalle, $minutos, $precio] = $n['servicios'][$id]; ?>
        <article class="dl-destacado">
          <div class="dl-destacado__foto">
            <?= demo_img($d['foto'], $d['alt'], ['ancho' => 600, 'alto' => 750, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 86vw, 25vw']) ?>
            <span class="dl-destacado__precio"><?= demo_e(demo_clp($precio)) ?></span>
          </div>
          <h3><?= demo_e($nombre) ?></h3>
          <p><?= (int) $minutos ?> min · <?= demo_e($detalle) ?></p>
          <a class="dl-enlace" href="?servicio=<?= demo_e($id) ?>#reservar">Reservar este</a>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="dl-seccion" id="reservar" aria-labelledby="reservar-titulo">
    <div class="dl-envoltura dl-reserva">
      <div class="dl-reserva__intro">
        <p class="dl-guion">Reserva tu hora</p>
        <h2 id="reservar-titulo">Elige servicio, barbero, día y hora</h2>
        <p>Al enviar se abre WhatsApp con tu reserva escrita. Te confirmamos al tiro; si la hora está tomada, te ofrecemos la más cercana.</p>
        <ol class="dl-pasos">
          <li><span><strong>Completa la ficha</strong> con lo que quieres hacerte.</span></li>
          <li><span><strong>Envía el WhatsApp</strong> que se arma solo.</span></li>
          <li><span><strong>Llega a tu hora</strong> y pasa directo al sillón.</span></li>
        </ol>
        <p class="dl-reserva__nota">¿Sin hora? También sirve: llegas, sacas número y esperas tu turno con un café.</p>
        <p class="dl-reserva__nota dl-reserva__nota--demo">Es una demo: no guardamos nada. El mensaje llega al WhatsApp de quien hizo este sitio.</p>
      </div>
      <form class="dl-ticket" method="post" action="#reservar" novalidate>
        <p class="dl-ticket__cabeza"><span>Ticket de reserva</span><span class="dl-ticket__num">N.º 0187</span></p>
        <?php if ($errores): ?><p class="dl-alerta" role="alert">Revisa los datos marcados.</p><?php endif; ?>
        <div class="dl-campo">
          <label for="servicio">Servicio</label>
          <select id="servicio" name="servicio" required<?= $inv('servicio') ?>>
            <option value="">Elige un servicio</option>
            <?php foreach ($n['servicios'] as $id => [$nombre, , $minutos, $precio]): ?>
            <option value="<?= demo_e($id) ?>"<?= $val('servicio') === $id ? ' selected' : '' ?>><?= demo_e($nombre) ?> · <?= (int) $minutos ?> min · <?= demo_e(demo_clp($precio)) ?></option>
            <?php endforeach; ?>
          </select>
          <?= $err('servicio') ?>
        </div>
        <fieldset class="dl-campo dl-barberos"<?= isset($errores['barbero']) ? ' aria-describedby="err-barbero"' : '' ?>>
          <legend>¿Con quién?</legend>
          <?php foreach ($n['barberos'] as $id => [$nombre, $detalle]): ?>
          <label class="dl-barbero"><input type="radio" name="barbero" value="<?= demo_e($id) ?>"<?= $val('barbero') === $id ? ' checked' : '' ?>><span><strong><?= demo_e($nombre) ?></strong><small><?= demo_e($detalle) ?></small></span></label>
          <?php endforeach; ?>
          <?= $err('barbero') ?>
        </fieldset>
        <div class="dl-ticket__fila">
          <div class="dl-campo">
            <label for="dia">Día</label>
            <select id="dia" name="dia" required<?= $inv('dia') ?>>
              <option value="">Elige el día</option>
              <?php foreach ($dias as $f => $texto): ?>
              <option value="<?= demo_e($f) ?>"<?= $val('dia') === $f ? ' selected' : '' ?>><?= demo_e($texto) ?></option>
              <?php endforeach; ?>
            </select>
            <?= $err('dia') ?>
          </div>
          <div class="dl-campo">
            <label for="hora">Hora</label>
            <select id="hora" name="hora" required<?= $inv('hora') ?>>
              <option value="">Elige la hora</option>
              <?php foreach (dl_horas() as $h): ?>
              <option value="<?= demo_e($h) ?>"<?= $val('hora') === $h ? ' selected' : '' ?>><?= demo_e($h) ?></option>
              <?php endforeach; ?>
            </select>
            <?= $err('hora') ?>
          </div>
        </div>
        <p class="dl-ayuda">Lunes cerrado. Domingo de 10:00 a 14:00 y sábado hasta las 18:00.</p>
        <div class="dl-campo">
          <label for="nombre">Tu nombre <small>(opcional)</small></label>
          <input id="nombre" name="nombre" maxlength="40" autocomplete="off" value="<?= demo_e($val('nombre')) ?>">
        </div>
        <div class="dl-campo">
          <label for="nota">¿Algo más? <small>(opcional)</small></label>
          <textarea id="nota" name="nota" rows="2" maxlength="200" placeholder="Ej.: degradado bajo, dejar largo arriba"><?= demo_e($val('nota')) ?></textarea>
        </div>
        <button class="dl-btn dl-btn--bloque" type="submit"><?= demo_icono_wa() ?><span>Enviar por WhatsApp</span></button>
      </form>
    </div>
  </section>

  <section class="dl-seccion dl-seccion--crema" id="barberia" aria-labelledby="barberia-titulo">
    <div class="dl-envoltura dl-local">
      <figure class="dl-local__foto">
        <?= demo_img($n['fotos']['local'], 'Sillón de barbero de cuero negro sobre el piso de baldosas blancas y negras', ['ancho' => 700, 'alto' => 900, 'sizes' => '(max-width: 860px) 86vw, 40vw']) ?>
        <figcaption>El sillón de siempre, sobre el piso de ajedrez que Don Lalo puso en 1987</figcaption>
      </figure>
      <div class="dl-local__texto">
        <p class="dl-guion">La barbería</p>
        <h2 id="barberia-titulo">Treinta y tantos años en la misma esquina</h2>
        <p>Eduardo, Don Lalo para el barrio, abrió con un sillón, un espejo y una radio. Hoy son tres sillones, la misma radio y su hijo Nico, que aprendió mirando y ahora hace los degradados que piden los jóvenes del barrio.</p>
        <p>Aquí no hay apuro: se conversa de fútbol, se toma un café y se sale bien cortado. Si vienes por primera vez, cuéntanos cómo te lo quieres dejar; si eres de la casa, ya sabemos.</p>
        <dl class="dl-cifras">
          <div><dt>Desde</dt><dd><?= (int) $n['desde'] ?></dd></div>
          <div><dt>Sillones</dt><dd>3</dd></div>
          <div><dt>Corte</dt><dd><?= demo_e(demo_clp($n['servicios']['clasico'][3])) ?></dd></div>
        </dl>
      </div>
    </div>
  </section>

  <section class="dl-seccion" id="trabajos" aria-labelledby="trabajos-titulo">
    <div class="dl-envoltura">
      <header class="dl-encabezado">
        <p class="dl-guion">Trabajos recientes</p>
        <h2 id="trabajos-titulo">Así salen de aquí</h2>
      </header>
      <ul class="dl-galeria">
        <?php foreach ($n['galeria'] as $i => [$foto, $titulo, $alt]): ?>
        <li class="dl-galeria__item<?= $i === 0 ? ' dl-galeria__item--grande' : '' ?>">
          <figure>
            <?= demo_img($foto, $alt, ['ancho' => $i === 0 ? 900 : 500, 'alto' => $i === 0 ? 1000 : 560, 'anchos' => $i === 0 ? [600, 900, 1200] : [400, 600, 800], 'sizes' => $i === 0 ? '(max-width: 700px) 92vw, 45vw' : '(max-width: 700px) 46vw, 22vw']) ?>
            <figcaption><?= demo_e($titulo) ?></figcaption>
          </figure>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="dl-seccion dl-seccion--crema" id="horario" aria-labelledby="horario-titulo">
    <div class="dl-envoltura dl-horario">
      <div class="dl-letrero">
        <p class="dl-guion">Horario</p>
        <h2 id="horario-titulo">Cuándo estamos</h2>
        <p class="dl-estado-linea"><span class="dl-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario dl-tabla-horario'); ?>
      </div>
      <ul class="dl-datos">
        <li><strong>Con hora o por orden de llegada</strong><span>Con hora pasas directo. Sin hora, sacas número y esperas tu turno.</span></li>
        <li><strong>Mientras esperas</strong><span>Café, el diario y la radio con el partido. Wifi para los más jóvenes.</span></li>
        <li><strong>Pago</strong><span>Efectivo, débito o transferencia. Los planes se pagan a principio de mes.</span></li>
      </ul>
    </div>
  </section>

  <div class="dl-seccion">
    <div class="dl-envoltura">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería la ficha de la barbería en Google Maps: horario, cómo llegar y lo que dicen los vecinos.']); ?>
    </div>
  </div>

  <section class="dl-cierre" aria-labelledby="cierre-titulo">
    <div class="dl-envoltura">
      <p class="dl-guion">¿Te toca corte?</p>
      <h2 id="cierre-titulo">Te guardamos el sillón.</h2>
      <div class="dl-acciones">
        <a class="dl-btn dl-btn--claro" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
        <a class="dl-btn dl-btn--borde-claro" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
      </div>
    </div>
  </section>
</main>

<footer class="dl-pie">
  <div class="dl-envoltura">
    <p class="dl-pie__marca"><span class="dl-poste" aria-hidden="true"></span><?= demo_e($n['nombre']) ?></p>
    <p><?= demo_e($n['comuna']) ?>, Santiago · Reservas por WhatsApp</p>
    <?php demo_aviso_pie($rubro); ?>
  </div>
</footer>

<nav class="dl-barra" aria-label="Contacto rápido">
  <a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
  <a href="#reservar"><?= $icoTijera ?><span>Reservar</span></a>
</nav>
<?php demo_scripts(); ?>
</body>
</html>
