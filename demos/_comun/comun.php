<?php
declare(strict_types=1);

/*
 * Base común de los ejemplos por rubro (demos/<rubro>/<version>/).
 * Funciones de vista y componentes compartidos: aviso de demo, sección Google,
 * QR, botón "Ver en 3D" y enlaces de WhatsApp. Sin dependencias del portafolio.
 */

if (defined('DEMO_COMUN')) {
    return;
}
define('DEMO_COMUN', __DIR__);

/** Número que recibe los pedidos de prueba (Julio). */
const DEMO_WHATSAPP = '56973537642';
const DEMO_SITIO_JULIO = 'https://j0acuna.site';
/** Carpeta pública de los ejemplos y de esta base común. */
const DEMO_URL_DEMOS = '/demos/';
const DEMO_URL_COMUN = '/demos/_comun/';
const DEMO_ZONA_HORARIA = 'America/Santiago';

/** Librerías de CDN: versión fija + SRI. */
const DEMO_JS_QR = '<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js" integrity="sha384-mZT2gIty7ZDdOGkxfP6joZcYdMW1Jvj9dRlfpTmaJAKKXTqzygtB22k7FLe+KZC1" crossorigin="anonymous" defer></script>';
const DEMO_JS_MODEL_VIEWER = '<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js" integrity="sha256-GLLnEQNapHBe4oFTClXcIzj/Be7T1wrhbVQQUoKnXtU=" crossorigin="anonymous"></script>';

const DEMO_DIAS = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
const DEMO_DIAS_CORTOS = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'];

function demo_e(null|string|int|float $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Pesos chilenos: 12990 → "$12.990". */
function demo_clp(int $monto): string
{
    return ($monto < 0 ? '-$' : '$') . number_format(abs($monto), 0, ',', '.');
}

/** Nota que acompaña todo pedido o consulta hecho desde una demo. */
function demo_nota(string $rubroNombre, string $version): string
{
    return '(Pedido de prueba desde la demo de ' . $rubroNombre . ' – ' . $version . ')';
}

/** Enlace a WhatsApp de Julio con el texto codificado (equivale a encodeURIComponent). */
function demo_wa(string $mensaje): string
{
    return 'https://wa.me/' . DEMO_WHATSAPP . '?text=' . rawurlencode($mensaje);
}

function demo_tel(): string
{
    return 'tel:+' . DEMO_WHATSAPP;
}

/** Foto de Unsplash enlazada directamente (ver demos/CREDITOS.md). */
function demo_foto(string $id, int $ancho = 900): string
{
    if (str_starts_with($id, 'https://') || str_starts_with($id, '/')) {
        return $id;
    }
    return 'https://images.unsplash.com/photo-' . rawurlencode($id) . '?w=' . $ancho . '&q=70&auto=format&fit=crop';
}

/** srcset de una foto de Unsplash en varios anchos. */
function demo_srcset(string $id, array $anchos = [480, 900, 1400]): string
{
    if (str_starts_with($id, 'https://') || str_starts_with($id, '/')) {
        return '';
    }
    return implode(', ', array_map(static fn (int $w): string => demo_foto($id, $w) . ' ' . $w . 'w', $anchos));
}

/**
 * <img> de Unsplash. $opc: clase, lazy (bool, por defecto true), sizes, ancho, alto, anchos.
 */
function demo_img(string $id, string $alt, array $opc = []): string
{
    $lazy = $opc['lazy'] ?? true;
    $ancho = (int) ($opc['ancho'] ?? 900);
    $alto = (int) ($opc['alto'] ?? 600);
    $srcset = demo_srcset($id, $opc['anchos'] ?? [480, 900, 1400]);
    $html = '<img src="' . demo_e(demo_foto($id, $ancho)) . '"';
    if ($srcset !== '') {
        $html .= ' srcset="' . demo_e($srcset) . '" sizes="' . demo_e($opc['sizes'] ?? '(max-width: 700px) 100vw, 50vw') . '"';
    }
    $html .= ' alt="' . demo_e($alt) . '" width="' . $ancho . '" height="' . $alto . '"';
    $html .= $lazy ? ' loading="lazy" decoding="async"' : ' fetchpriority="high"';
    if (!empty($opc['clase'])) {
        $html .= ' class="' . demo_e($opc['clase']) . '"';
    }
    return $html . '>';
}

/** URL pública de un archivo de demos/ con versión para el caché (?v=fecha de cambio). */
function demo_asset(string $url): string
{
    $archivo = dirname(__DIR__) . '/' . substr($url, strlen(DEMO_URL_DEMOS));
    $v = is_file($archivo) ? (string) filemtime($archivo) : '1';
    return $url . '?v=' . $v;
}

/** Ícono SVG simple (letra sobre círculo) como data URI para el favicon. */
function demo_favicon(string $letra, string $fondo, string $color = '#fff'): string
{
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><circle cx="32" cy="32" r="32" fill="' . $fondo . '"/>'
        . '<text x="32" y="44" font-family="Arial,sans-serif" font-size="34" font-weight="700" text-anchor="middle" fill="' . $color . '">'
        . htmlspecialchars($letra, ENT_XML1) . '</text></svg>';
    return 'data:image/svg+xml,' . rawurlencode($svg);
}

/**
 * Abre el documento de una versión: <head> con noindex, fuentes y estilos.
 * $o: titulo, descripcion, fuentes (URL de Google Fonts o ''), css (lista de URL),
 *     tema (color), icono [letra, fondo, color], clase_body, extra_head (HTML ya escapado).
 */
function demo_head(array $o): void
{
    $css = array_merge([DEMO_URL_COMUN . 'demo.css'], $o['css'] ?? []);
    $icono = $o['icono'] ?? ['D', '#333333'];
    ?>
<!doctype html>
<html lang="es-CL">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= demo_e($o['titulo']) ?></title>
  <meta name="description" content="<?= demo_e($o['descripcion'] ?? '') ?>">
  <meta name="robots" content="noindex, follow">
  <meta name="theme-color" content="<?= demo_e($o['tema'] ?? '#ffffff') ?>">
  <link rel="icon" href="<?= demo_e(demo_favicon($icono[0], $icono[1], $icono[2] ?? '#fff')) ?>">
  <link rel="preconnect" href="https://images.unsplash.com">
  <?php if (!empty($o['fuentes'])): ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="<?= demo_e($o['fuentes']) ?>">
  <?php endif; ?>
  <?php foreach ($css as $hoja): ?>
  <link rel="stylesheet" href="<?= demo_e(str_starts_with($hoja, DEMO_URL_DEMOS) ? demo_asset($hoja) : $hoja) ?>">
  <?php endforeach; ?>
  <?= $o['extra_head'] ?? '' ?>
</head>
<body class="<?= demo_e($o['clase_body'] ?? '') ?>">
<a class="demo-saltar" href="#contenido">Saltar al contenido</a>
<?php
}

/**
 * Scripts comunes al final del <body>. $que: lista con 'qr', '3d', 'carrito', 'whatsapp'.
 * 'base' (demo.js) va siempre.
 */
function demo_scripts(array $que = []): void
{
    if (in_array('qr', $que, true) || in_array('3d', $que, true)) {
        echo DEMO_JS_QR, "\n";
    }
    if (in_array('3d', $que, true)) {
        echo DEMO_JS_MODEL_VIEWER, "\n";
    }
    echo '<script src="', demo_e(demo_asset(DEMO_URL_COMUN . 'js/demo.js')), '" defer></script>', "\n";
    if (in_array('carrito', $que, true)) {
        echo '<script src="', demo_e(demo_asset(DEMO_URL_COMUN . 'js/carrito.js')), '" defer></script>', "\n";
    }
    if (in_array('buscador', $que, true)) {
        echo '<script src="', demo_e(demo_asset(DEMO_URL_COMUN . 'js/buscador.js')), '" defer></script>', "\n";
    }
    if (in_array('3d', $que, true)) {
        echo '<script src="', demo_e(demo_asset(DEMO_URL_COMUN . 'js/menu3d.js')), '" defer></script>', "\n";
    }
}

/**
 * Aviso fijo de demo, arriba de todas las páginas.
 * $demo: rubro_nombre (ej. "pizzería"), url_rubro (ej. "/demos/pizzeria/").
 */
function demo_aviso(array $demo): void
{
    ?>
<div class="demo-aviso" role="note" aria-label="Aviso de sitio de demostración">
  <p>
    <span class="demo-aviso__punto" aria-hidden="true"></span>
    Sitio de demostración hecho por <a href="<?= demo_e(DEMO_SITIO_JULIO) ?>">Julio Acuña</a> · negocio ficticio
    <a class="demo-aviso__planes" href="<?= demo_e($demo['url_rubro']) ?>">Ver planes para <?= demo_e($demo['rubro_plural'] ?? $demo['rubro_nombre']) ?></a>
  </p>
</div>
<?php
}

/** Línea del pie con el mismo aviso (se repite abajo para que nadie lo confunda con un local real). */
function demo_aviso_pie(array $demo): void
{
    ?>
<p class="demo-aviso-pie">Negocio ficticio. Sitio de demostración hecho por <a href="<?= demo_e(DEMO_SITIO_JULIO) ?>">Julio Acuña</a> · <a href="<?= demo_e($demo['url_rubro']) ?>">ver los planes</a> · <a href="<?= demo_e(DEMO_URL_DEMOS . 'creditos.php') ?>">créditos de fotos</a></p>
<?php
}

/* ---------------------------------------------------------------- Horario */

/**
 * Agrupa días seguidos con el mismo horario.
 * $horario: [1..7 => ['12:30', '23:00'] | null]. Devuelve [['Lun a Jue', '12:30 – 23:00'], ...].
 */
function demo_horario_filas(array $horario): array
{
    $filas = [];
    $inicio = 1;
    for ($d = 1; $d <= 7; $d++) {
        $actual = $horario[$d] ?? null;
        $siguiente = $d < 7 ? ($horario[$d + 1] ?? null) : '__fin__';
        if ($siguiente === $actual) {
            continue;
        }
        $dias = $inicio === $d ? DEMO_DIAS_CORTOS[$d] : DEMO_DIAS_CORTOS[$inicio] . ($d - $inicio === 1 ? ' y ' : ' a ') . DEMO_DIAS_CORTOS[$d];
        $filas[] = [$dias, $actual ? $actual[0] . ' – ' . $actual[1] : 'Cerrado'];
        $inicio = $d + 1;
    }
    return $filas;
}

function demo_minutos(string $hhmm): int
{
    [$h, $m] = array_map('intval', explode(':', $hhmm));
    return $h * 60 + $m;
}

/**
 * ¿Está abierto? Devuelve ['abierto' => bool, 'texto' => 'Abierto · cierra a las 23:00'].
 * Soporta cierres después de medianoche (ej. 19:00 – 01:00).
 */
function demo_horario_estado(array $horario, ?DateTimeImmutable $ahora = null): array
{
    $ahora ??= new DateTimeImmutable('now', new DateTimeZone(DEMO_ZONA_HORARIA));
    $dia = (int) $ahora->format('N');
    $min = (int) $ahora->format('G') * 60 + (int) $ahora->format('i');
    $ayer = $dia === 1 ? 7 : $dia - 1;

    $h = $horario[$ayer] ?? null;
    if ($h && demo_minutos($h[1]) < demo_minutos($h[0]) && $min < demo_minutos($h[1])) {
        return ['abierto' => true, 'texto' => 'Abierto · cierra a las ' . $h[1]];
    }
    $h = $horario[$dia] ?? null;
    if ($h) {
        $abre = demo_minutos($h[0]);
        $cierra = demo_minutos($h[1]);
        $pasaMedianoche = $cierra < $abre;
        if ($min >= $abre && ($pasaMedianoche || $min < $cierra)) {
            return ['abierto' => true, 'texto' => 'Abierto · cierra a las ' . $h[1]];
        }
        if ($min < $abre) {
            return ['abierto' => false, 'texto' => 'Cerrado · abre hoy a las ' . $h[0]];
        }
    }
    for ($i = 1; $i <= 7; $i++) {
        $d = (($dia - 1 + $i) % 7) + 1;
        if (!empty($horario[$d])) {
            $cuando = $i === 1 ? 'mañana' : 'el ' . mb_strtolower(DEMO_DIAS[$d]);
            return ['abierto' => false, 'texto' => 'Cerrado · abre ' . $cuando . ' a las ' . $horario[$d][0]];
        }
    }
    return ['abierto' => false, 'texto' => 'Cerrado'];
}

function demo_horario_tabla(array $horario, string $clase = 'demo-horario'): void
{
    echo '<dl class="', demo_e($clase), '">';
    foreach (demo_horario_filas($horario) as [$dias, $horas]) {
        echo '<div><dt>', demo_e($dias), '</dt><dd>', demo_e($horas), '</dd></div>';
    }
    echo '</dl>';
}

/* ---------------------------------------------------------------- Google */

function demo_maps_busqueda(string $comuna): string
{
    return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($comuna) . '+Santiago';
}

function demo_maps_embed(string $comuna): string
{
    return 'https://www.google.com/maps?q=' . rawurlencode($comuna) . ',+Santiago,+Chile&output=embed';
}

function demo_estrellas(float $nota): string
{
    $llenas = (int) round($nota);
    return str_repeat('★', $llenas) . str_repeat('☆', 5 - $llenas);
}

/**
 * Sección "Encuéntranos en Google" (§6.13): tarjeta tipo Perfil de Empresa, mapa de la comuna
 * y reseñas de ejemplo. Sin datos estructurados (el negocio es inventado).
 * $n: nombre, categoria, comuna, horario, nota (ej. 4.8), resenas [[autor, nota, texto, hace]].
 * $opc: titulo, bajada, id (ancla), clase.
 */
function demo_google(array $n, array $opc = []): void
{
    $id = $opc['id'] ?? 'google';
    $estado = demo_horario_estado($n['horario']);
    $buscar = demo_maps_busqueda($n['comuna']);
    $nota = (float) ($n['nota'] ?? 4.8);
    $notaTexto = number_format($nota, 1, ',', '');
    ?>
<section class="demo-google <?= demo_e($opc['clase'] ?? '') ?>" id="<?= demo_e($id) ?>" aria-labelledby="<?= demo_e($id) ?>-titulo">
  <div class="demo-google__intro">
    <h2 id="<?= demo_e($id) ?>-titulo"><?= demo_e($opc['titulo'] ?? 'Encuéntranos en Google') ?></h2>
    <p><?= demo_e($opc['bajada'] ?? 'Busca «' . $n['nombre'] . '» en Google Maps: horario, cómo llegar y opiniones de clientes.') ?></p>
  </div>
  <div class="demo-google__grid">
    <article class="demo-google__perfil" aria-label="Perfil de Empresa en Google (ejemplo)">
      <p class="demo-google__tipo">Perfil de Empresa en Google · ejemplo</p>
      <h3><?= demo_e($n['nombre']) ?></h3>
      <p class="demo-google__nota">
        <strong><?= demo_e($notaTexto) ?></strong>
        <span class="demo-google__estrellas" aria-hidden="true"><?= demo_estrellas($nota) ?></span>
        <span class="demo-sr"><?= demo_e($notaTexto) ?> de 5 estrellas</span>
        <span class="demo-google__muestra">calificación de ejemplo</span>
      </p>
      <p class="demo-google__categoria"><?= demo_e($n['categoria']) ?> · <?= demo_e($n['comuna']) ?>, Santiago</p>
      <p class="demo-google__estado <?= $estado['abierto'] ? 'is-abierto' : 'is-cerrado' ?>"><?= demo_e($estado['texto']) ?></p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario demo-google__horario'); ?>
      <div class="demo-google__acciones">
        <a class="demo-google__btn demo-google__btn--principal" href="<?= demo_e($buscar) ?>" target="_blank" rel="noopener">Cómo llegar</a>
        <a class="demo-google__btn" href="<?= demo_e($buscar) ?>" target="_blank" rel="noopener">Déjanos tu reseña</a>
        <a class="demo-google__enlace" href="<?= demo_e($buscar) ?>" target="_blank" rel="noopener">Ver en Google</a>
      </div>
    </article>
    <div class="demo-google__mapa">
      <iframe src="<?= demo_e(demo_maps_embed($n['comuna'])) ?>" title="Mapa de <?= demo_e($n['comuna']) ?>, Santiago, en Google Maps" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
    </div>
  </div>
  <?php if (!empty($n['resenas'])): ?>
  <ul class="demo-google__resenas" aria-label="Reseñas de ejemplo">
    <?php foreach ($n['resenas'] as [$autor, $estrellas, $texto, $hace]): ?>
    <li class="demo-google__resena">
      <p class="demo-google__resena-cabeza">
        <span class="demo-google__avatar" aria-hidden="true"><?= demo_e(mb_substr($autor, 0, 1)) ?></span>
        <span><strong><?= demo_e($autor) ?></strong><br><small><?= demo_e($hace) ?></small></span>
        <span class="demo-google__sello">Reseña de ejemplo</span>
      </p>
      <p class="demo-google__estrellas" aria-label="<?= (int) $estrellas ?> de 5 estrellas"><?= demo_estrellas((float) $estrellas) ?></p>
      <p><?= demo_e($texto) ?></p>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</section>
<?php
}

/* ---------------------------------------------------------------- QR y 3D */

/**
 * Código QR generado en el navegador (qrcode-generator, cdnjs con SRI).
 * $destino: ruta o ancla relativa a la página (ej. '#menu'); el QR lleva la URL completa.
 * Sin JavaScript se ve el enlace.
 */
function demo_qr(string $destino, string $titulo, string $clase = ''): void
{
    ?>
<figure class="demo-qr <?= demo_e($clase) ?>">
  <div class="demo-qr__codigo" data-demo-qr="<?= demo_e($destino) ?>" data-demo-qr-titulo="<?= demo_e($titulo) ?>">
    <a href="<?= demo_e($destino) ?>"><?= demo_e($titulo) ?></a>
  </div>
  <figcaption><?= demo_e($titulo) ?></figcaption>
</figure>
<?php
}

/** Ruta del modelo 3D de un plato: demos/_comun/3d/<modelo>.glb (puede no existir todavía). */
function demo_modelo_url(string $modelo): string
{
    return DEMO_URL_COMUN . '3d/' . rawurlencode(basename($modelo, '.glb')) . '.glb';
}

/**
 * Botón "Ver en 3D" de un plato. Lo maneja js/menu3d.js: abre <model-viewer> con RA
 * o, si el .glb aún no existe, la foto con "Modelo 3D en preparación".
 * $p: id, nombre, modelo (ej. 'pizza'), foto (id de Unsplash o URL).
 */
function demo_boton_3d(array $p, string $clase = '', string $texto = 'Ver en 3D'): string
{
    return '<button type="button" class="demo-3d-btn ' . demo_e($clase) . '"'
        . ' data-3d-modelo="' . demo_e(demo_modelo_url($p['modelo'])) . '"'
        . ' data-3d-nombre="' . demo_e($p['nombre']) . '"'
        . ' data-3d-foto="' . demo_e(demo_foto($p['foto'], 900)) . '"'
        . ' data-3d-id="' . demo_e((string) $p['id']) . '"'
        . ' aria-haspopup="dialog">'
        . '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" d="M12 2.8 20 7.2v9.6L12 21.2 4 16.8V7.2zM4 7.2l8 4.6 8-4.6M12 11.8v9.4"/></svg>'
        . '<span>' . demo_e($texto) . '</span></button>';
}

/** Texto en minúsculas y sin tildes, para buscar ("Ñoquis" → "noquis"). */
function demo_normalizar(string $s): string
{
    $s = mb_strtolower(trim($s), 'UTF-8');
    return strtr($s, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
}

/** Ícono de WhatsApp (SVG en línea). */
function demo_icono_wa(int $tam = 20): string
{
    return '<svg viewBox="0 0 32 32" width="' . $tam . '" height="' . $tam . '" aria-hidden="true" focusable="false"><path fill="currentColor" d="M16 3a13 13 0 0 0-11.2 19.6L3 29l6.6-1.7A13 13 0 1 0 16 3zm0 23.6c-2 0-4-.5-5.7-1.6l-.4-.2-3.9 1 1-3.8-.3-.4A10.6 10.6 0 1 1 16 26.6zm5.8-7.9c-.3-.2-1.9-.9-2.2-1s-.5-.2-.7.2-.8 1-1 1.2-.4.2-.7 0a8.7 8.7 0 0 1-4.3-3.8c-.3-.6.3-.5.9-1.7.1-.2 0-.4 0-.5l-1-2.4c-.3-.6-.5-.5-.7-.5h-.6a1.2 1.2 0 0 0-.9.4 3.7 3.7 0 0 0-1.1 2.7 6.4 6.4 0 0 0 1.3 3.4 14.7 14.7 0 0 0 5.6 5c2.1.9 2.9 1 4 .8a3.4 3.4 0 0 0 2.2-1.6 2.8 2.8 0 0 0 .2-1.6c-.1-.1-.3-.2-.6-.4z"/></svg>';
}

function demo_icono_tel(int $tam = 20): string
{
    return '<svg viewBox="0 0 24 24" width="' . $tam . '" height="' . $tam . '" aria-hidden="true" focusable="false"><path fill="currentColor" d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1z"/></svg>';
}
