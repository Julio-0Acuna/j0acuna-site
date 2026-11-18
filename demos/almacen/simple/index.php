<?php
declare(strict_types=1);

// Almacén El Quitasol · Simple-page: almacén de barrio con la lista de la semana por WhatsApp.
// La lista funciona sin JavaScript: el formulario se envía aquí, se valida y se redirige a WhatsApp (303).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version);
const QS_MAX_CANTIDAD = 20;

/** Enlace de WhatsApp con la nota de pedido de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);
$waGeneral = $wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.');

$ahora = new DateTimeImmutable('now', new DateTimeZone(DEMO_ZONA_HORARIA));
$hoy = (int) $ahora->format('N');
$estado = demo_horario_estado($n['horario'], $ahora);
[$ofertaTitulo, $ofertaPrecios] = $n['ofertas'][$hoy];
$diaHoy = mb_strtolower(DEMO_DIAS[$hoy]);

/** Precio de hoy (con la oferta del día, si la tiene). */
$precio = static fn (string $id): int => (int) ($ofertaPrecios[$id] ?? $n['productos'][$id]['precio']);
/** "Arroz grado 1 (1 kg)" */
$etiqueta = static fn (string $id): string => $n['productos'][$id]['nombre'] . ' (' . $n['productos'][$id]['formato'] . ')';
/** Valor de las canastas comprando cada cosa por separado (precio normal). */
$porSeparado = static function (array $p) use ($n): int {
    $total = 0;
    foreach ($p['contiene'] ?? [] as $id => $cant) {
        $total += (int) $n['productos'][$id]['precio'] * $cant;
    }
    return $total;
};
$costoDespacho = static fn (int $subtotal): int => $subtotal >= $n['gratis_desde'] ? 0 : (int) $n['despacho'];

/* ---------------------------------------------------------------- Formulario de la lista */
$valores = ['c' => [], 'entrega' => 'despacho', 'direccion' => '', 'bloque' => 'manana', 'nombre' => '', 'pago' => 'efectivo', 'otros' => ''];
$errores = [];
$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';

// "Sumar a mi lista" sin JavaScript: ?agregar=<id> deja ese producto con 1 unidad.
if ($metodo === 'GET' && is_string($_GET['agregar'] ?? null) && isset($n['productos'][$_GET['agregar']])) {
    $valores['c'][$_GET['agregar']] = 1;
}

if ($metodo === 'POST') {
    $texto = static function (string $clave, int $max) {
        $v = $_POST[$clave] ?? '';
        if (!is_string($v)) {
            return null;
        }
        $v = trim((string) preg_replace('/\s+/u', ' ', $v));
        return mb_strlen($v) > $max ? null : $v;
    };
    $c = is_array($_POST['c'] ?? null) ? $_POST['c'] : [];
    foreach ($c as $id => $cant) {
        $id = (string) $id;
        if (!isset($n['productos'][$id])) {
            $errores['lista'] = 'Hay un producto que no está en nuestra lista. Recarga la página e inténtalo de nuevo.';
            continue;
        }
        $cant = is_string($cant) ? trim($cant) : '';
        if ($cant === '' || $cant === '0') {
            continue;
        }
        if (!ctype_digit($cant) || (int) $cant > QS_MAX_CANTIDAD) {
            $errores['lista'] = 'Las cantidades van de 0 a ' . QS_MAX_CANTIDAD . ' por producto.';
            continue;
        }
        $valores['c'][$id] = (int) $cant;
    }
    // Mismo orden que la lista.
    $valores['c'] = array_intersect_key(array_replace(array_fill_keys(array_keys($n['productos']), 0), $valores['c']), $valores['c']);

    $entrega = $_POST['entrega'] ?? '';
    if (!in_array($entrega, ['despacho', 'retiro'], true)) {
        $errores['entrega'] = 'Elige delivery o retiro en el almacén.';
    } else {
        $valores['entrega'] = $entrega;
    }
    $nombre = $texto('nombre', 60);
    $valores['nombre'] = (string) $nombre;
    if ($nombre === null || mb_strlen($nombre) < 2) {
        $errores['nombre'] = 'Escribe tu nombre (máximo 60 caracteres).';
    }
    if ($valores['entrega'] === 'despacho') {
        $direccion = $texto('direccion', 120);
        $valores['direccion'] = (string) $direccion;
        if ($direccion === null || mb_strlen($direccion) < 5) {
            $errores['direccion'] = 'Escribe la calle, el número y el depto. si tienes.';
        }
        $bloque = $_POST['bloque'] ?? '';
        if (!is_string($bloque) || !isset($n['bloques'][$bloque])) {
            $errores['bloque'] = 'Elige mañana o tarde.';
        } else {
            $valores['bloque'] = $bloque;
        }
    }
    $pago = $_POST['pago'] ?? '';
    if (!is_string($pago) || !isset($n['pagos'][$pago])) {
        $errores['pago'] = 'Elige cómo vas a pagar.';
    } else {
        $valores['pago'] = $pago;
    }
    $otros = $texto('otros', 200);
    $valores['otros'] = (string) $otros;
    if ($otros === null) {
        $errores['otros'] = 'Máximo 200 caracteres.';
    }

    $subtotal = 0;
    foreach ($valores['c'] as $id => $cant) {
        $subtotal += $precio($id) * $cant;
    }
    if (!isset($errores['lista']) && !$valores['c']) {
        $errores['lista'] = 'Tu lista está vacía: marca al menos un producto.';
    } elseif (!isset($errores['lista']) && $valores['entrega'] === 'despacho' && $subtotal < $n['minimo']) {
        $errores['lista'] = 'Para el delivery el pedido mínimo es ' . demo_clp($n['minimo']) . ' (te faltan ' . demo_clp($n['minimo'] - $subtotal) . '). Suma algo más o elige retiro en el almacén.';
    }

    if (!$errores) {
        $lineas = ['Hola, quiero pedir en ' . $n['nombre'] . ':'];
        foreach ($valores['c'] as $id => $cant) {
            $lineas[] = '• ' . $cant . ' × ' . $etiqueta($id) . ' – ' . demo_clp($precio($id) * $cant) . (isset($ofertaPrecios[$id]) ? ' (oferta del ' . $diaHoy . ')' : '');
        }
        $lineas[] = 'Subtotal: ' . demo_clp($subtotal);
        if ($valores['entrega'] === 'despacho') {
            $despacho = $costoDespacho($subtotal);
            $lineas[] = 'Delivery en el barrio: ' . ($despacho === 0 ? 'gratis' : demo_clp($despacho));
            $lineas[] = 'Total: ' . demo_clp($subtotal + $despacho);
            $lineas[] = 'Entrega: delivery a ' . $valores['direccion'] . ' · ' . $n['bloques'][$valores['bloque']];
        } else {
            $lineas[] = 'Total: ' . demo_clp($subtotal);
            $lineas[] = 'Entrega: retiro en el almacén';
        }
        $lineas[] = 'Pago: ' . $n['pagos'][$valores['pago']];
        if ($valores['otros'] !== '') {
            $lineas[] = 'Además: ' . $valores['otros'];
        }
        $lineas[] = 'Nombre: ' . $valores['nombre'];
        header('Location: ' . $wa(implode("\n", $lineas)), true, 303);
        exit;
    }
    http_response_code(422);
}

$subtotalActual = 0;
foreach ($valores['c'] as $id => $cant) {
    $subtotalActual += $precio($id) * $cant;
}
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="qs-error" id="err-' . demo_e($k) . '">' . demo_e($errores[$k]) . '</p>' : '';
$inval = static fn (string $k): string => isset($errores[$k]) ? ' aria-invalid="true" aria-describedby="err-' . demo_e($k) . '"' : '';
$qn = demo_normalizar($q);

/** Ícono del quitasol (logo). */
$quitasol = '<svg viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false"><path d="M4 22a20 20 0 0 1 40 0z" fill="#f4c430"/><path d="M12 22a12 20 0 0 1 12-20 12 20 0 0 1 12 20z" fill="#fbf6e9"/><path d="M20 22a4 20 0 0 1 4-20 4 20 0 0 1 4 20z" fill="#f4c430"/><path d="M4 22a20 20 0 0 1 40 0" fill="none" stroke="#143528" stroke-width="2.4" stroke-linejoin="round"/><path d="M24 22v17a4 4 0 0 1-8 0" fill="none" stroke="#143528" stroke-width="2.6" stroke-linecap="round"/></svg>';

demo_head([
    'titulo' => $n['nombre'] . ' · Almacén con delivery en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para un almacén de barrio en ' . $n['comuna'] . ', Santiago: ofertas del día, lista de la semana que se envía por WhatsApp, canastas armadas y delivery en el barrio.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Calistoga&family=Lexend:wght@400;500;700&display=swap',
    'css' => ['/demos/almacen/simple/estilo.css'],
    'tema' => '#1e4d3b',
    'icono' => ['Q', '#1e4d3b', '#f4c430'],
    'clase_body' => 'qs',
]);
demo_aviso($rubro);
?>
<header class="qs-cabecera">
  <div class="qs-toldo" aria-hidden="true"></div>
  <div class="qs-ancho qs-cabecera__fila">
    <a class="qs-marca" href="#contenido" aria-label="<?= demo_e($n['nombre']) ?>, inicio">
      <span class="qs-marca__icono"><?= $quitasol ?></span>
      <span class="qs-marca__nombre"><?= demo_e($n['nombre_corto']) ?><small>Almacén · <?= demo_e($n['comuna']) ?><span class="qs-marca__desde"> · desde <?= (int) $n['desde'] ?></span></small></span>
    </a>
    <nav class="qs-nav" aria-label="Secciones">
      <a href="#ofertas">Ofertas</a>
      <a href="#lista">Mi lista</a>
      <a href="#canastas">Canastas</a>
      <a href="#delivery">Delivery</a>
      <a href="#google">Ubicación</a>
    </nav>
    <a class="qs-btn qs-btn--wa qs-btn--chico" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Pedir</span></a>
  </div>
</header>

<main id="contenido">
  <section class="qs-hero" aria-labelledby="qs-hero-titulo">
    <div class="qs-ancho qs-hero__grid">
      <div class="qs-hero__texto">
        <p class="qs-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <h1 id="qs-hero-titulo"><small>Almacén</small> El Quitasol</h1>
        <p class="qs-hero__eslogan"><?= demo_e($n['eslogan']) ?></p>
        <p class="qs-hero__bajada"><?= demo_e($n['bajada']) ?></p>
        <div class="qs-acciones">
          <a class="qs-btn" href="#lista">Armar mi lista</a>
          <a class="qs-btn qs-btn--wa" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
          <a class="qs-btn qs-btn--linea" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
        <ul class="qs-datos">
          <li><strong><?= demo_e(demo_clp($n['minimo'])) ?></strong><span>pedido mínimo para delivery</span></li>
          <li><strong><?= demo_e(demo_clp($n['despacho'])) ?></strong><span>el despacho, gratis desde <?= demo_e(demo_clp($n['gratis_desde'])) ?></span></li>
          <li><strong>2 repartos</strong><span>al día, de lunes a sábado</span></li>
        </ul>
      </div>
      <figure class="qs-hero__foto">
        <?= demo_img($n['fotos']['hero'], 'Cajones de fruta y verdura bajo un quitasol a rayas amarillas en la vereda del almacén', ['lazy' => false, 'ancho' => 900, 'alto' => 1125, 'sizes' => '(max-width: 860px) 92vw, 440px', 'anchos' => [480, 900, 1300]]) ?>
        <figcaption class="qs-cartel">
          <span class="qs-cartel__dia">Hoy <?= demo_e($diaHoy) ?></span>
          <strong><?= demo_e($ofertaTitulo) ?></strong>
          <a href="#ofertas">Ver las ofertas</a>
        </figcaption>
      </figure>
    </div>
  </section>

  <section class="qs-ofertas" id="ofertas" aria-labelledby="ofertas-titulo">
    <div class="qs-ancho">
      <div class="qs-titular qs-titular--claro">
        <p class="qs-antetitulo">Ofertas de hoy · <?= demo_e(demo_fecha_larga($ahora)) ?></p>
        <h2 id="ofertas-titulo"><?= demo_e($ofertaTitulo) ?></h2>
        <p>Cada día de la semana bajamos el precio de algo distinto. Las ofertas valen solo hoy y ya van con su precio en tu lista.</p>
      </div>
      <ol class="qs-semana" aria-label="Ofertas de cada día">
        <?php foreach ($n['ofertas'] as $d => [$titulo]): $tema = explode(' de ', $titulo, 2)[1] ?? $titulo; ?>
        <li<?= $d === $hoy ? ' class="is-hoy" aria-current="date"' : '' ?>><abbr title="<?= demo_e(DEMO_DIAS[$d]) ?>"><?= demo_e(DEMO_DIAS_CORTOS[$d]) ?></abbr><span><?= demo_e(mb_strtoupper(mb_substr($tema, 0, 1)) . mb_substr($tema, 1)) ?></span></li>
        <?php endforeach; ?>
      </ol>
      <div class="qs-etiquetas">
        <?php foreach ($ofertaPrecios as $id => $oferta): $p = $n['productos'][$id]; ?>
        <article class="qs-etiqueta">
          <div class="qs-etiqueta__foto">
            <?= demo_img($p['foto'], $p['nombre'] . ', ' . $p['formato'], ['ancho' => 600, 'alto' => 600, 'anchos' => [320, 600, 900], 'sizes' => '(max-width: 700px) 80vw, 300px']) ?>
            <span class="qs-etiqueta__ahorro">−<?= demo_e(demo_clp($p['precio'] - $oferta)) ?></span>
          </div>
          <div class="qs-etiqueta__cuerpo">
            <h3><?= demo_e($p['nombre']) ?></h3>
            <p class="qs-etiqueta__formato"><?= demo_e($p['formato']) ?></p>
            <p class="qs-etiqueta__precio"><s aria-label="Precio normal"><?= demo_e(demo_clp($p['precio'])) ?></s> <strong><?= demo_e(demo_clp($oferta)) ?></strong></p>
            <a class="qs-btn qs-btn--chico" href="?agregar=<?= demo_e(rawurlencode($id)) ?>#lista" data-agregar="<?= demo_e($id) ?>">Sumar a mi lista</a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="qs-lista" id="lista" aria-labelledby="lista-titulo">
    <div class="qs-ancho">
      <div class="qs-titular">
        <p class="qs-antetitulo">Pedido de la semana</p>
        <h2 id="lista-titulo">Arma tu lista</h2>
        <p>Marca cuánto quieres de cada cosa. Al enviar se abre WhatsApp con tu lista escrita, el total y tu dirección, y te confirmamos al tiro. La lista queda guardada en este teléfono para la próxima semana.</p>
      </div>
      <?php if ($errores): ?>
      <p class="demo-mensaje demo-mensaje--error" role="alert"><?= demo_e($errores['lista'] ?? 'Revisa los datos marcados en tu lista.') ?></p>
      <?php endif; ?>

      <div class="qs-lista__grid" data-buscador-raiz>
        <div class="qs-lista__productos">
          <form class="qs-buscar" role="search" method="get" action="#lista">
            <label class="demo-sr" for="qs-q">Buscar en la lista</label>
            <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <input type="search" id="qs-q" name="q" value="<?= demo_e($q) ?>" maxlength="40" placeholder="Busca: arroz, pan, detergente…" data-buscador autocomplete="off">
            <button type="submit" class="qs-buscar__btn">Buscar</button>
          </form>
          <nav class="qs-chips" aria-label="Categorías de la lista">
            <?php foreach ($n['categorias'] as $slug => [$nombreCat]): ?>
            <a href="#cat-<?= demo_e($slug) ?>"><?= demo_e($nombreCat) ?></a>
            <?php endforeach; ?>
          </nav>
          <p class="qs-sin-resultados" data-sin-resultados<?= $qn === '' ? ' hidden' : '' ?>>No lo tenemos en la lista. Escríbelo en «¿Algo más?» y te decimos si hay.</p>

          <?php foreach ($n['categorias'] as $slug => [$nombreCat, $fotoCat, $notaCat]): ?>
          <section class="qs-grupo" id="cat-<?= demo_e($slug) ?>" data-grupo aria-labelledby="cat-<?= demo_e($slug) ?>-titulo">
            <header class="qs-grupo__cabeza">
              <?= demo_img($fotoCat, '', ['ancho' => 160, 'alto' => 160, 'anchos' => [160, 320], 'sizes' => '64px', 'clase' => 'qs-grupo__foto']) ?>
              <div>
                <h3 id="cat-<?= demo_e($slug) ?>-titulo"><?= demo_e($nombreCat) ?></h3>
                <p><?= demo_e($notaCat) ?></p>
              </div>
            </header>
            <ul class="qs-filas">
              <?php foreach ($n['productos'] as $id => $p): ?>
              <?php if ($p['cat'] !== $slug) { continue; } ?>
              <?php
                  $texto = $p['nombre'] . ' ' . $p['formato'] . ' ' . $nombreCat;
                  $oculto = $qn !== '' && !str_contains(demo_normalizar($texto), $qn);
                  $esOferta = isset($ofertaPrecios[$id]);
                  $cant = (int) ($valores['c'][$id] ?? 0);
              ?>
              <li class="qs-fila<?= $esOferta ? ' is-oferta' : '' ?><?= $cant > 0 ? ' is-elegido' : '' ?>" id="p-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($texto) ?>"<?= $oculto ? ' hidden' : '' ?>>
                <?= demo_img($p['foto'], '', ['ancho' => 120, 'alto' => 120, 'anchos' => [120, 240], 'sizes' => '56px', 'clase' => 'qs-fila__foto']) ?>
                <div class="qs-fila__info">
                  <label for="c-<?= demo_e($id) ?>"><?= demo_e($p['nombre']) ?></label>
                  <span><?= demo_e($p['formato']) ?><?php if ($esOferta): ?> · <em class="qs-sello">Oferta de hoy</em><?php endif; ?></span>
                  <?php if (!empty($p['contiene'])): ?>
                  <small><?= demo_e(implode(', ', array_map(static fn (string $cid, int $k): string => ($k > 1 ? $k . ' × ' : '') . mb_strtolower($n['productos'][$cid]['nombre']), array_keys($p['contiene']), $p['contiene']))) ?></small>
                  <?php endif; ?>
                </div>
                <p class="qs-fila__precio"><?php if ($esOferta): ?><s aria-label="Precio normal"><?= demo_e(demo_clp($p['precio'])) ?></s> <?php endif; ?><strong><?= demo_e(demo_clp($precio($id))) ?></strong></p>
                <div class="qs-cant">
                  <button type="button" class="qs-cant__btn" data-menos aria-label="Quitar uno de <?= demo_e($p['nombre']) ?>" hidden>−</button>
                  <input type="number" id="c-<?= demo_e($id) ?>" name="c[<?= demo_e($id) ?>]" form="qs-form" inputmode="numeric" min="0" max="<?= QS_MAX_CANTIDAD ?>" step="1" placeholder="0" value="<?= $cant > 0 ? $cant : '' ?>" data-precio="<?= $precio($id) ?>" data-nombre="<?= demo_e($etiqueta($id)) ?>">
                  <button type="button" class="qs-cant__btn" data-mas aria-label="Agregar uno de <?= demo_e($p['nombre']) ?>" hidden>+</button>
                </div>
              </li>
              <?php endforeach; ?>
            </ul>
          </section>
          <?php endforeach; ?>
        </div>

        <aside class="qs-nota" id="mi-lista" aria-labelledby="mi-lista-titulo">
          <div class="qs-nota__papel">
            <span class="qs-nota__iman" aria-hidden="true"></span>
            <h3 id="mi-lista-titulo">Mi lista</h3>
            <ul class="qs-nota__items" data-lista-items aria-live="polite">
              <?php if (!$valores['c']): ?>
              <li class="qs-nota__vacia">Todavía no marcas nada. Usa los botones + de la lista.</li>
              <?php else: foreach ($valores['c'] as $id => $cant): ?>
              <li><span><?= (int) $cant ?> × <?= demo_e($etiqueta($id)) ?></span><span><?= demo_e(demo_clp($precio($id) * $cant)) ?></span></li>
              <?php endforeach; endif; ?>
            </ul>
            <dl class="qs-nota__totales">
              <div><dt>Subtotal</dt><dd data-subtotal><?= demo_e(demo_clp($subtotalActual)) ?></dd></div>
              <div data-fila-despacho><dt>Delivery</dt><dd data-despacho><?= demo_e($costoDespacho($subtotalActual) === 0 ? 'Gratis' : demo_clp($costoDespacho($subtotalActual))) ?></dd></div>
              <div class="qs-nota__total"><dt>Total</dt><dd data-total><?= demo_e(demo_clp($subtotalActual + ($valores['entrega'] === 'despacho' ? $costoDespacho($subtotalActual) : 0))) ?></dd></div>
            </dl>
            <div class="qs-minimo" data-minimo hidden>
              <div class="qs-minimo__barra" aria-hidden="true"><span data-minimo-barra></span></div>
              <p data-minimo-texto></p>
            </div>
            <a class="qs-nota__seguir" href="#datos">Completar mis datos y enviar</a>
          </div>
        </aside>

        <form id="qs-form" class="qs-form" method="post" action="./#lista" novalidate>
            <h3 class="qs-form__titulo" id="datos">Tus datos para el pedido</h3>
            <fieldset class="qs-entrega">
              <legend>¿Cómo lo recibes?</legend>
              <div class="qs-entrega__opciones">
              <label class="qs-opcion"><input type="radio" name="entrega" value="despacho"<?= $valores['entrega'] === 'despacho' ? ' checked' : '' ?>> <span><strong>Delivery en el barrio</strong><small><?= demo_e(demo_clp($n['despacho'])) ?>, gratis desde <?= demo_e(demo_clp($n['gratis_desde'])) ?>. Mínimo <?= demo_e(demo_clp($n['minimo'])) ?>.</small></span></label>
              <label class="qs-opcion"><input type="radio" name="entrega" value="retiro"<?= $valores['entrega'] === 'retiro' ? ' checked' : '' ?>> <span><strong>Retiro en el almacén</strong><small>Sin costo. Te avisamos cuando esté lista.</small></span></label>
              </div>
              <?= $err('entrega') ?>
            </fieldset>
            <div class="qs-campo qs-campo--mitad" data-solo-despacho<?= $valores['entrega'] === 'retiro' ? ' hidden' : '' ?>>
              <label for="direccion">Dirección</label>
              <input id="direccion" name="direccion" maxlength="120" autocomplete="off" placeholder="Calle, número y depto." value="<?= demo_e($valores['direccion']) ?>"<?= $inval('direccion') ?>>
              <?= $err('direccion') ?>
            </div>
            <div class="qs-campo qs-campo--mitad" data-solo-despacho<?= $valores['entrega'] === 'retiro' ? ' hidden' : '' ?>>
              <label for="bloque">¿Cuándo te la llevamos?</label>
              <select id="bloque" name="bloque"<?= $inval('bloque') ?>>
                <?php foreach ($n['bloques'] as $k => $b): ?><option value="<?= demo_e($k) ?>"<?= $valores['bloque'] === $k ? ' selected' : '' ?>><?= demo_e($b) ?></option><?php endforeach; ?>
              </select>
              <?= $err('bloque') ?>
            </div>
            <div class="qs-campo qs-campo--mitad">
              <label for="nombre">Tu nombre</label>
              <input id="nombre" name="nombre" maxlength="60" autocomplete="off" required value="<?= demo_e($valores['nombre']) ?>"<?= $inval('nombre') ?>>
              <?= $err('nombre') ?>
            </div>
            <div class="qs-campo qs-campo--mitad">
              <label for="pago">Pago al recibir</label>
              <select id="pago" name="pago"<?= $inval('pago') ?>>
                <?php foreach ($n['pagos'] as $k => $pg): ?><option value="<?= demo_e($k) ?>"<?= $valores['pago'] === $k ? ' selected' : '' ?>><?= demo_e($pg) ?></option><?php endforeach; ?>
              </select>
              <?= $err('pago') ?>
            </div>
            <div class="qs-campo">
              <label for="otros">¿Algo más? <small>(opcional)</small></label>
              <textarea id="otros" name="otros" rows="2" maxlength="200" placeholder="Ej.: 1 kg de porotos, pan bien tostado"<?= $inval('otros') ?>><?= demo_e($valores['otros']) ?></textarea>
              <?= $err('otros') ?>
            </div>
            <p class="qs-error" data-form-error role="alert" hidden></p>
            <button class="qs-btn qs-btn--wa qs-btn--bloque" type="submit"><?= demo_icono_wa() ?><span>Enviar mi lista por WhatsApp</span></button>
            <p class="qs-pequeno">Demo: no guardamos tus datos. La lista se arma aquí y se envía por WhatsApp.</p>
        </form>
      </div>
    </div>
    <a class="qs-barra" href="#mi-lista" data-barra hidden><span data-barra-n>0 productos</span><strong data-barra-total>$0</strong><span class="qs-barra__ver">Ver mi lista</span></a>
  </section>

  <section class="qs-canastas" id="canastas" aria-labelledby="canastas-titulo">
    <div class="qs-ancho">
      <div class="qs-titular">
        <p class="qs-antetitulo">Para no pensar</p>
        <h2 id="canastas-titulo">Canastas armadas</h2>
        <p>Te la dejamos lista en una caja. Si quieres cambiar algo, dínoslo en el mensaje y lo ajustamos.</p>
      </div>
      <div class="qs-canastas__grid">
        <?php foreach ($n['productos'] as $id => $p): ?>
        <?php if ($p['cat'] !== 'canastas') { continue; } $separado = $porSeparado($p); ?>
        <article class="qs-canasta">
          <div class="qs-canasta__foto">
            <?= demo_img($p['foto'], $p['nombre'] . ': productos del almacén listos para entregar', ['ancho' => 700, 'alto' => 520, 'anchos' => [400, 700, 1000], 'sizes' => '(max-width: 760px) 92vw, 33vw']) ?>
            <p class="qs-canasta__precio"><strong><?= demo_e(demo_clp($p['precio'])) ?></strong><small>ahorras <?= demo_e(demo_clp($separado - $p['precio'])) ?></small></p>
          </div>
          <div class="qs-canasta__cuerpo">
            <h3><?= demo_e($p['nombre']) ?></h3>
            <p class="qs-canasta__para"><?= demo_e(mb_strtoupper(mb_substr($p['formato'], 0, 1)) . mb_substr($p['formato'], 1)) ?> · por separado <?= demo_e(demo_clp($separado)) ?></p>
            <ul>
              <?php foreach ($p['contiene'] as $cid => $k): ?><li><?= (int) $k ?> × <?= demo_e($etiqueta($cid)) ?></li><?php endforeach; ?>
            </ul>
            <div class="qs-canasta__acciones">
              <a class="qs-btn qs-btn--wa qs-btn--chico" href="<?= demo_e($wa('Hola, quiero la ' . $p['nombre'] . ' (' . demo_clp($p['precio']) . ') de ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>La quiero</span></a>
              <a class="qs-btn qs-btn--linea qs-btn--chico" href="?agregar=<?= demo_e(rawurlencode($id)) ?>#lista" data-agregar="<?= demo_e($id) ?>">Sumar a mi lista</a>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="qs-delivery" id="delivery" aria-labelledby="delivery-titulo">
    <div class="qs-ancho qs-delivery__grid">
      <div class="qs-letrero">
        <p class="qs-antetitulo">Delivery en el barrio</p>
        <h2 id="delivery-titulo">Te lo llevamos a la casa</h2>
        <dl class="qs-letrero__datos">
          <div><dt>Pedido mínimo</dt><dd><?= demo_e(demo_clp($n['minimo'])) ?></dd></div>
          <div><dt>Despacho</dt><dd><?= demo_e(demo_clp($n['despacho'])) ?> <small>gratis desde <?= demo_e(demo_clp($n['gratis_desde'])) ?></small></dd></div>
          <div><dt>Dónde</dt><dd><?= demo_e(ucfirst($n['radio'])) ?> <small>en <?= demo_e($n['comuna']) ?></small></dd></div>
          <div><dt>Cuándo</dt><dd>Lunes a sábado <small>de 10:00 a 13:00 y de 17:00 a 20:30</small></dd></div>
          <div><dt>Pago</dt><dd>Al recibir <small>efectivo, débito, crédito o transferencia</small></dd></div>
        </dl>
      </div>
      <div class="qs-pasos-caja">
        <h3>Así de simple</h3>
        <ol class="qs-pasos">
          <li><strong>Arma tu lista</strong><span>Marca lo que necesitas o elige una canasta armada.</span></li>
          <li><strong>Envíala por WhatsApp</strong><span>Llega escrita, con el total y tu dirección.</span></li>
          <li><strong>Te la llevamos</strong><span>En la mañana o en la tarde. Pagas cuando llega.</span></li>
        </ol>
        <h3>Horario del almacén</h3>
        <?php demo_horario_tabla($n['horario'], 'demo-horario qs-horario'); ?>
      </div>
    </div>
  </section>

  <div class="qs-google">
    <div class="qs-ancho">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Busca «' . $n['nombre'] . '» en Google Maps: horario, cómo llegar y lo que dicen los vecinos.']); ?>
    </div>
  </div>
</main>

<footer class="qs-pie">
  <div class="qs-toldo qs-toldo--pie" aria-hidden="true"></div>
  <div class="qs-ancho qs-pie__grid">
    <div>
      <p class="qs-marca qs-marca--pie"><span class="qs-marca__icono"><?= $quitasol ?></span><span class="qs-marca__nombre"><?= demo_e($n['nombre_corto']) ?><small>Almacén de barrio desde <?= (int) $n['desde'] ?></small></span></p>
      <p><?= demo_e($n['comuna']) ?>, Santiago.</p>
    </div>
    <div>
      <p class="qs-pie__titulo">Horario</p>
      <?php demo_horario_tabla($n['horario'], 'demo-horario qs-pie__horario'); ?>
    </div>
    <div>
      <p class="qs-pie__titulo">Pedidos</p>
      <p><a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener">Escríbenos por WhatsApp</a></p>
      <p><a href="<?= demo_e(demo_tel()) ?>">Llámanos</a></p>
      <p><a href="#lista">Arma tu lista</a></p>
    </div>
  </div>
  <div class="qs-ancho"><?php demo_aviso_pie($rubro); ?></div>
</footer>
<script type="application/json" id="qs-config"><?= json_encode([
    'minimo' => $n['minimo'],
    'despacho' => $n['despacho'],
    'gratis' => $n['gratis_desde'],
    'clave' => 'demo-almacen-simple-lista',
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<?php demo_scripts(['buscador']); ?>
<script src="<?= demo_e(demo_asset('/demos/almacen/simple/lista.js')) ?>" defer></script>
</body>
</html>
