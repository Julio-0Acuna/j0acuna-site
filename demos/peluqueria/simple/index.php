<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version);

/** Enlace de WhatsApp con la nota de pedido de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);
$waGeneral = $wa('Hola, quiero pedir una hora en ' . $n['nombre'] . '.');
$estado = demo_horario_estado($n['horario']);

/** Duración legible: 75 → "1 h 15 min". */
function py_duracion(int $min): string
{
    $h = intdiv($min, 60);
    $m = $min % 60;
    return trim(($h > 0 ? $h . ' h' : '') . ($m > 0 ? ' ' . $m . ' min' : ''));
}

/**
 * Días que se ofrecen para reservar: hoy (si queda tarde por delante) y los siguientes días abiertos.
 * @return array<string, string> ['2026-11-20' => 'Mañana, jueves 20 de noviembre', ...]
 */
function py_dias(array $horario, int $cuantos = 12): array
{
    $tz = new DateTimeZone(DEMO_ZONA_HORARIA);
    $ahora = new DateTimeImmutable('now', $tz);
    $hoy = $ahora->setTime(0, 0);
    $dias = [];
    for ($i = 0; count($dias) < $cuantos && $i <= 30; $i++) {
        $d = $hoy->modify('+' . $i . ' days');
        $h = $horario[(int) $d->format('N')] ?? null;
        if (empty($h)) {
            continue;
        }
        // Hoy solo si faltan al menos dos horas para cerrar.
        if ($i === 0 && ((int) $ahora->format('G') * 60 + (int) $ahora->format('i')) > demo_minutos($h[1]) - 120) {
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

/**
 * Formulario de reserva (sin JavaScript): valida en el servidor y redirige a WhatsApp
 * con el mensaje armado. Devuelve [url|null, errores, valores enviados].
 */
function py_reserva(array $post, array $n, array $dias, string $nota): array
{
    $txt = static function (string $k, int $max) use ($post): string {
        $v = $post[$k] ?? '';
        return is_string($v) ? mb_substr(trim(preg_replace('/\s+/u', ' ', $v) ?? ''), 0, $max) : '';
    };
    $previo = [
        'servicio' => $txt('servicio', 20),
        'con' => $txt('con', 20),
        'dia' => $txt('dia', 10),
        'bloque' => $txt('bloque', 12),
        'nombre' => $txt('nombre', 40),
        'comentario' => $txt('comentario', 300),
    ];
    $errores = [];
    if (!isset($n['servicios'][$previo['servicio']])) {
        $errores['servicio'] = 'Elige un servicio de la lista.';
    }
    if ($previo['con'] !== '' && !isset($n['equipo'][$previo['con']])) {
        $errores['con'] = 'Elige a alguien del equipo o «Me da lo mismo».';
    }
    if (!isset($dias[$previo['dia']])) {
        $errores['dia'] = 'Elige uno de los días disponibles.';
    }
    if (!isset($n['bloques'][$previo['bloque']])) {
        $errores['bloque'] = 'Elige mañana, mediodía o tarde.';
    }
    if (mb_strlen($previo['nombre']) < 2) {
        $errores['nombre'] = 'Escribe tu nombre para anotarte en la agenda.';
    }
    if ($errores) {
        return [null, $errores, $previo];
    }
    [, $servicio, , $minutos, $desde] = $n['servicios'][$previo['servicio']];
    $lineas = [
        'Hola, quiero reservar una hora en ' . $n['nombre'] . '.',
        'Servicio: ' . $servicio . ' (desde ' . demo_clp($desde) . ' · ' . py_duracion($minutos) . ')',
        'Con: ' . ($previo['con'] === '' ? 'quien esté libre' : $n['equipo'][$previo['con']]['nombre']),
        'Día: ' . preg_replace('/^(Hoy|Mañana), /u', '', $dias[$previo['dia']]),
        'Bloque: ' . $n['bloques'][$previo['bloque']],
        'Nombre: ' . $previo['nombre'],
    ];
    if ($previo['comentario'] !== '') {
        $lineas[] = 'Comentario: ' . $previo['comentario'];
    }
    $lineas[] = $nota;
    return [demo_wa(implode("\n", $lineas)), [], $previo];
}

$dias = py_dias($n['horario']);
$errores = [];
$previo = ['servicio' => is_string($_GET['servicio'] ?? null) && isset($n['servicios'][$_GET['servicio']]) ? $_GET['servicio'] : ''];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    [$destino, $errores, $previo] = py_reserva($_POST, $n, $dias, $nota);
    if ($destino !== null) {
        header('Location: ' . $destino, true, 303);
        exit;
    }
    http_response_code(422);
}
$val = static fn (string $k): string => (string) ($previo[$k] ?? '');
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="py-error" id="err-' . demo_e($k) . '">' . demo_e($errores[$k]) . '</p>' : '';
$inv = static fn (string $k): string => isset($errores[$k]) ? ' aria-invalid="true" aria-describedby="err-' . demo_e($k) . '"' : '';
$reservarUrl = static fn (string $id): string => '?servicio=' . rawurlencode($id) . '#reserva';

$icoTijera = '<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M8.2 7.9 20 18M8.2 16.1 20 6"/></g></svg>';

demo_head([
    'titulo' => $n['nombre'] . ' · Peluquería en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para una peluquería de barrio en ' . $n['comuna'] . ', Santiago: servicios con duración y precio desde, promociones, reserva de hora por WhatsApp y productos.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Corben:wght@400;700&family=Gochi+Hand&family=Lexend:wght@400;500;600&display=swap',
    'css' => ['/demos/peluqueria/simple/estilo.css'],
    'tema' => '#0e5f5f',
    'icono' => ['Y', '#e4572e', '#fff5ea'],
    'clase_body' => 'py',
]);
demo_aviso($rubro);
?>
<header class="py-top">
  <div class="py-envoltura py-top__fila">
    <a class="py-marca" href="#contenido">
      <span class="py-marca__sello" aria-hidden="true"><?= $icoTijera ?></span>
      <span class="py-marca__nombre">Yanet<small>peluquería · <?= demo_e($n['comuna']) ?></small></span>
    </a>
    <nav class="py-nav" aria-label="Secciones">
      <a href="#servicios">Precios</a>
      <a href="#promos">Promos</a>
      <a href="#equipo">Equipo</a>
      <a href="#productos">Productos</a>
      <a href="#google">Horario</a>
    </nav>
    <a class="py-btn py-btn--chico" href="#reserva"><span>Reservar hora</span></a>
  </div>
</header>

<main id="contenido">
  <section class="py-hero" aria-labelledby="hero-titulo">
    <div class="py-envoltura py-hero__grid">
      <div class="py-hero__texto">
        <p class="py-sobre">Peluquería de barrio · <?= demo_e($n['comuna']) ?> · desde <?= (int) $n['desde'] ?></p>
        <h1 id="hero-titulo">Entras con ganas de cambio. <span>Sales brillando.</span></h1>
        <p class="py-hero__bajada">Corte, brushing dominicano, color, trenzas y uñas a pasos de tu casa. Reserva tu hora en un minuto o pasa sin hora en la mañana: siempre hay café.</p>
        <div class="py-acciones">
          <a class="py-btn" href="#reserva"><?= $icoTijera ?><span>Reservar hora</span></a>
          <a class="py-btn py-btn--wa" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
          <a class="py-btn py-btn--claro" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
        <ul class="py-hero__datos" aria-label="En pocas palabras">
          <li><strong><?= number_format((float) $n['nota'], 1, ',', '') ?> ★</strong><span>en Google (ejemplo)</span></li>
          <li><strong>3</strong><span>sillones</span></li>
          <li><strong>$8.000</strong><span>corte desde</span></li>
        </ul>
      </div>
      <figure class="py-espejo">
        <div class="py-espejo__vidrio">
          <?= demo_img($n['equipo']['yanet']['foto'], 'Yanet, la dueña, sonríe y apunta con unas tijeras en su peluquería', ['lazy' => false, 'ancho' => 800, 'alto' => 1000, 'sizes' => '(max-width: 860px) 86vw, 440px', 'anchos' => [480, 800, 1100]]) ?>
        </div>
        <p class="py-postit" aria-hidden="true"><?= demo_e($estado['abierto'] ? '¡Abiertas! ' . mb_strtolower(preg_replace('/^Abierto · /u', '', $estado['texto'])) : preg_replace('/^Cerrado · /u', '', $estado['texto'])) ?></p>
        <figcaption><strong>Yanet</strong>, dueña y reina del brushing</figcaption>
      </figure>
    </div>
  </section>

  <p class="demo-sr"><?= demo_e($estado['texto']) ?></p>

  <section class="py-cinta" aria-label="Servicios">
    <ul class="py-envoltura">
      <?php foreach (['Corte', 'Brushing', 'Color', 'Mechas', 'Trenzas', 'Keratina', 'Uñas', 'Cejas'] as $c): ?><li><?= demo_e($c) ?></li><?php endforeach; ?>
    </ul>
  </section>

  <section class="py-seccion" id="servicios" aria-labelledby="servicios-titulo">
    <div class="py-envoltura">
      <header class="py-encabezado">
        <p class="py-sobre">Lista de precios</p>
        <h2 id="servicios-titulo">Lo que hacemos y cuánto demora</h2>
        <p>Precios «desde» para pelo corto o medio. Si tu pelo es largo o muy abundante te avisamos antes de empezar, sin sorpresas.</p>
      </header>

      <div class="py-destacados">
        <?php foreach ($n['destacados'] as $id => $d): [, $nombre, $detalle, $min, $desde] = $n['servicios'][$id]; ?>
        <article class="py-destacado">
          <div class="py-destacado__foto">
            <?= demo_img($d['foto'], $d['alt'], ['ancho' => 600, 'alto' => 720, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 640px) 80vw, (max-width: 1000px) 45vw, 25vw']) ?>
            <span class="py-mano" aria-hidden="true"><?= demo_e($d['nota']) ?></span>
          </div>
          <div class="py-destacado__texto">
            <h3><?= demo_e($nombre) ?></h3>
            <p><?= demo_e($detalle) ?></p>
            <p class="py-precio"><span class="py-dur"><?= demo_e(py_duracion($min)) ?></span> <span><small>desde</small> <?= demo_e(demo_clp($desde)) ?></span></p>
            <a class="py-enlace" href="<?= demo_e($reservarUrl($id)) ?>">Reservar <span class="demo-sr"><?= demo_e($nombre) ?></span></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <div class="py-lista">
        <?php foreach ($n['categorias'] as $cat => $nombreCat): ?>
        <section class="py-lista__grupo" aria-labelledby="cat-<?= demo_e($cat) ?>">
          <h3 id="cat-<?= demo_e($cat) ?>"><?= demo_e($nombreCat) ?></h3>
          <ul>
            <?php foreach ($n['servicios'] as $id => [$c, $nombre, $detalle, $min, $desde]): if ($c !== $cat) { continue; } ?>
            <li class="py-fila">
              <div class="py-fila__texto">
                <strong><?= demo_e($nombre) ?></strong>
                <span><?= demo_e($detalle) ?></span>
              </div>
              <span class="py-dur"><?= demo_e(py_duracion($min)) ?></span>
              <span class="py-fila__precio"><small>desde</small> <?= demo_e(demo_clp($desde)) ?></span>
              <a class="py-fila__reservar" href="<?= demo_e($reservarUrl($id)) ?>" aria-label="Reservar <?= demo_e($nombre) ?>">Reservar</a>
            </li>
            <?php endforeach; ?>
          </ul>
        </section>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="py-seccion py-seccion--espejo" id="promos" aria-labelledby="promos-titulo">
    <div class="py-envoltura py-promos">
      <div class="py-promos__intro">
        <p class="py-sobre py-sobre--claro">Escritas en el espejo</p>
        <h2 id="promos-titulo">Las promos de la semana</h2>
        <p>Como siempre: con plumón en el espejo grande, para que nadie se las pierda. Válidas reservando por WhatsApp o en el mesón.</p>
        <p class="py-sinhora"><strong>¿Sin hora?</strong> De lunes a viernes, de 10:00 a 12:00, atendemos cortes y cejas por orden de llegada.</p>
      </div>
      <ul class="py-promos__vidrio">
        <?php foreach ($n['promos'] as $i => [$cuando, $que]): ?>
        <li class="py-promo py-promo--<?= $i + 1 ?>"><span><?= demo_e($cuando) ?></span><strong><?= demo_e($que) ?></strong></li>
        <?php endforeach; ?>
        <li class="py-promo__firma" aria-hidden="true">— Yanet ♥</li>
      </ul>
    </div>
  </section>

  <section class="py-seccion py-seccion--menta" id="reserva" aria-labelledby="reserva-titulo">
    <div class="py-envoltura py-reserva">
      <div class="py-reserva__intro">
        <p class="py-sobre">Reserva por WhatsApp</p>
        <h2 id="reserva-titulo">Pide tu hora en un minuto</h2>
        <p>Elige el servicio, con quién y cuándo. Se abre WhatsApp con el mensaje listo y te confirmamos la hora exacta dentro del bloque.</p>
        <ol class="py-pasitos">
          <li>Completa el ticket</li>
          <li>Envía el mensaje</li>
          <li>Te confirmamos la hora</li>
        </ol>
        <p class="py-pequeno">Es una demo: no guardamos nada. El mensaje llega al WhatsApp de quien hizo este sitio.</p>
      </div>
      <form class="py-ticket" method="post" action="#reserva" novalidate>
        <p class="py-ticket__cabeza"><span>Ticket de reserva</span><span class="py-ticket__num" aria-hidden="true">N° 047</span></p>
        <?php if ($errores): ?><p class="py-alerta" role="alert">Revisa los datos marcados.</p><?php endif; ?>
        <div class="py-ticket__grid">
          <div class="py-campo py-campo--ancho">
            <label for="servicio">Servicio</label>
            <select id="servicio" name="servicio" required<?= $inv('servicio') ?>>
              <option value="">Elige un servicio</option>
              <?php foreach ($n['categorias'] as $cat => $nombreCat): ?>
              <optgroup label="<?= demo_e($nombreCat) ?>">
                <?php foreach ($n['servicios'] as $id => [$c, $nombre, , $min, $desde]): if ($c !== $cat) { continue; } ?>
                <option value="<?= demo_e($id) ?>"<?= $val('servicio') === $id ? ' selected' : '' ?>><?= demo_e($nombre) ?> · desde <?= demo_e(demo_clp($desde)) ?></option>
                <?php endforeach; ?>
              </optgroup>
              <?php endforeach; ?>
            </select>
            <?= $err('servicio') ?>
          </div>
          <fieldset class="py-campo py-campo--ancho py-quien"<?= $inv('con') ?>>
            <legend>¿Con quién?</legend>
            <div class="py-quien__opciones">
              <label class="py-chip"><input type="radio" name="con" value=""<?= $val('con') === '' ? ' checked' : '' ?>> <span>Me da lo mismo</span></label>
              <?php foreach ($n['equipo'] as $id => $p): ?>
              <label class="py-chip"><input type="radio" name="con" value="<?= demo_e($id) ?>"<?= $val('con') === $id ? ' checked' : '' ?>> <span><?= demo_e($p['nombre']) ?></span></label>
              <?php endforeach; ?>
            </div>
            <?= $err('con') ?>
          </fieldset>
          <div class="py-campo">
            <label for="dia">Día</label>
            <select id="dia" name="dia" required<?= $inv('dia') ?>>
              <option value="">Elige un día</option>
              <?php foreach ($dias as $f => $texto): ?><option value="<?= demo_e($f) ?>"<?= $val('dia') === $f ? ' selected' : '' ?>><?= demo_e($texto) ?></option><?php endforeach; ?>
            </select>
            <?= $err('dia') ?>
          </div>
          <div class="py-campo">
            <label for="bloque">Bloque</label>
            <select id="bloque" name="bloque" required<?= $inv('bloque') ?>>
              <option value="">Elige un bloque</option>
              <?php foreach ($n['bloques'] as $id => $texto): ?><option value="<?= demo_e($id) ?>"<?= $val('bloque') === $id ? ' selected' : '' ?>><?= demo_e($texto) ?></option><?php endforeach; ?>
            </select>
            <?= $err('bloque') ?>
          </div>
          <div class="py-campo py-campo--ancho">
            <label for="nombre">Tu nombre</label>
            <input id="nombre" name="nombre" maxlength="40" required autocomplete="off" value="<?= demo_e($val('nombre')) ?>"<?= $inv('nombre') ?>>
            <?= $err('nombre') ?>
          </div>
          <div class="py-campo py-campo--ancho">
            <label for="comentario">¿Algo que debamos saber? <small>(opcional)</small></label>
            <textarea id="comentario" name="comentario" rows="3" maxlength="300" placeholder="Ej.: pelo bajo los hombros, me tiño hace 2 meses, quiero llevar una foto…"><?= demo_e($val('comentario')) ?></textarea>
          </div>
        </div>
        <button class="py-btn py-btn--bloque py-btn--wa" type="submit"><?= demo_icono_wa() ?><span>Enviar por WhatsApp</span></button>
      </form>
    </div>
  </section>

  <section class="py-seccion" id="equipo" aria-labelledby="equipo-titulo">
    <div class="py-envoltura">
      <header class="py-encabezado">
        <p class="py-sobre">El equipo</p>
        <h2 id="equipo-titulo">Tres sillones, tres manos de confianza</h2>
        <p>Una peluquería chica, de las que se acuerdan de cómo te gusta el flequillo. Atendemos mujeres, hombres y todo el que quiera verse bien.</p>
      </header>
      <ul class="py-equipo">
        <?php foreach ($n['equipo'] as $id => $p): ?>
        <li class="py-persona">
          <div class="py-persona__foto"><?= demo_img($p['foto'], $p['alt'], ['ancho' => 480, 'alto' => 480, 'anchos' => [320, 480, 720], 'sizes' => '(max-width: 640px) 60vw, 240px']) ?></div>
          <h3><?= demo_e($p['nombre']) ?></h3>
          <p class="py-persona__rol"><?= demo_e($p['rol']) ?></p>
          <p><?= demo_e($p['texto']) ?></p>
          <a class="py-enlace" href="<?= demo_e($wa('Hola, quiero una hora con ' . $p['nombre'] . ' en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Pedir hora con <?= demo_e($p['nombre']) ?></a>
        </li>
        <?php endforeach; ?>
      </ul>
      <figure class="py-salon">
        <?= demo_img($n['fotos']['salon'], 'Tres sillones color frambuesa frente a un mesón blanco con espejo largo', ['ancho' => 1200, 'alto' => 640, 'sizes' => '(max-width: 1100px) 100vw, 1060px', 'anchos' => [600, 1000, 1400]]) ?>
        <figcaption class="py-mano">Nuestro rincón en <?= demo_e($n['comuna']) ?> ✂</figcaption>
      </figure>
    </div>
  </section>

  <section class="py-seccion py-seccion--crema" id="galeria" aria-labelledby="galeria-titulo">
    <div class="py-envoltura">
      <header class="py-encabezado">
        <p class="py-sobre">Así salieron</p>
        <h2 id="galeria-titulo">Pelos felices de la semana</h2>
      </header>
      <ul class="py-galeria">
        <?php foreach ($n['galeria'] as $i => [$foto, $alt]): ?>
        <li class="py-galeria__item py-galeria__item--<?= $i + 1 ?>"><?= demo_img($foto, $alt, ['ancho' => 600, 'alto' => 750, 'anchos' => [360, 600, 900], 'sizes' => '(max-width: 640px) 46vw, 30vw']) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="py-seccion" id="productos" aria-labelledby="productos-titulo">
    <div class="py-envoltura">
      <header class="py-encabezado">
        <p class="py-sobre">En el mesón</p>
        <h2 id="productos-titulo">Para que te dure en casa</h2>
        <p>Los mismos productos que usamos en el salón. Pídelos por WhatsApp y te los dejamos listos para retirar.</p>
      </header>
      <ul class="py-productos">
        <?php foreach ($n['productos'] as [$nombre, $detalle, $precio, $foto, $alt]): ?>
        <li class="py-producto">
          <div class="py-producto__foto"><?= demo_img($foto, $alt, ['ancho' => 480, 'alto' => 480, 'anchos' => [320, 480, 720], 'sizes' => '(max-width: 640px) 44vw, 220px']) ?></div>
          <h3><?= demo_e($nombre) ?></h3>
          <p><?= demo_e($detalle) ?></p>
          <p class="py-producto__pie"><strong><?= demo_e(demo_clp($precio)) ?></strong>
            <a class="py-enlace" href="<?= demo_e($wa('Hola, quiero pedir: ' . $nombre . ' (' . demo_clp($precio) . '). Lo retiro en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($nombre) ?> por WhatsApp">Pedir</a></p>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="py-seccion py-seccion--google" aria-label="Horario y ubicación">
    <div class="py-envoltura">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Busca «' . $n['nombre'] . '» en Google Maps: horario, cómo llegar y lo que dicen las clientas.', 'clase' => 'py-google']); ?>
    </div>
  </section>
</main>

<footer class="py-pie">
  <div class="py-envoltura py-pie__grid">
    <div>
      <p class="py-marca__nombre py-pie__marca">Yanet<small>peluquería · <?= demo_e($n['comuna']) ?></small></p>
      <p>Peluquería de barrio desde <?= (int) $n['desde'] ?>. <?= demo_e($n['comuna']) ?>, Santiago.</p>
    </div>
    <div>
      <p class="py-pie__titulo">Horario</p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario py-pie__horario'); ?>
    </div>
    <div>
      <p class="py-pie__titulo">Reserva</p>
      <p><a href="#reserva">Pedir hora aquí</a></p>
      <p><a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= demo_e(demo_tel()) ?>">Llamar</a></p>
    </div>
  </div>
  <div class="py-envoltura"><?php demo_aviso_pie($rubro); ?></div>
</footer>

<a class="py-flotante" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener" aria-label="Escribir por WhatsApp"><?= demo_icono_wa(26) ?></a>
<?php demo_scripts(); ?>
</body>
</html>
