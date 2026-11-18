<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version);

/** Enlace de WhatsApp con la nota de pedido de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);
$waGeneral = $wa('Hola, tengo una consulta para ' . $n['nombre'] . '.');
$estado = demo_horario_estado($n['horario']);

/**
 * "Cotiza tu lista" (sin JavaScript): valida en el servidor y redirige a WhatsApp con la lista
 * armada, una línea por producto. Devuelve [url|null, errores, valores enviados].
 */
function la_cotizacion(array $post, array $n, string $nota): array
{
    $linea = static function (string $k, int $max) use ($post): string {
        $v = $post[$k] ?? '';
        return is_string($v) ? mb_substr(trim(preg_replace('/\s+/u', ' ', $v) ?? ''), 0, $max) : '';
    };
    $crudo = is_string($post['lista'] ?? null) ? mb_substr(str_replace("\r", '', $post['lista']), 0, 800) : '';
    $items = [];
    foreach (explode("\n", $crudo) as $l) {
        $l = trim(preg_replace('/\s+/u', ' ', $l) ?? '');
        $l = ltrim($l, "-•*· \t");
        if ($l !== '') {
            $items[] = mb_substr($l, 0, 80);
        }
    }
    $previo = [
        'lista' => $crudo,
        'entrega' => $linea('entrega', 10),
        'comuna' => $linea('comuna', 30),
        'calle' => $linea('calle', 100),
        'nombre' => $linea('nombre', 40),
    ];
    $errores = [];
    if (!$items) {
        $errores['lista'] = 'Escribe al menos un producto, uno por línea (ej.: 2 sacos de cemento).';
    } elseif (count($items) > 30) {
        $errores['lista'] = 'La lista puede tener hasta 30 líneas. Si es más larga, mándanos una foto por WhatsApp.';
    }
    if (!in_array($previo['entrega'], ['retiro', 'despacho'], true)) {
        $errores['entrega'] = 'Elige retiro en el local o despacho.';
    }
    if ($previo['entrega'] === 'despacho') {
        if (!isset($n['comunas_despacho'][$previo['comuna']])) {
            $errores['comuna'] = 'Elige una comuna de la lista.';
        }
        if (mb_strlen($previo['calle']) < 4) {
            $errores['calle'] = 'Escribe la calle y el número para el despacho.';
        }
    }
    if ($errores) {
        return [null, $errores, $previo];
    }
    $lineas = ['Hola, quiero cotizar esta lista en ' . $n['nombre'] . ':'];
    foreach ($items as $it) {
        $lineas[] = '• ' . $it;
    }
    if ($previo['entrega'] === 'despacho') {
        $lineas[] = 'Despacho: ' . $previo['calle'] . ', ' . $previo['comuna'] . ' (' . demo_clp($n['comunas_despacho'][$previo['comuna']]) . ')';
    } else {
        $lineas[] = 'Entrega: retiro en el local';
    }
    if ($previo['nombre'] !== '') {
        $lineas[] = 'Nombre: ' . $previo['nombre'];
    }
    $lineas[] = $nota;
    return [demo_wa(implode("\n", $lineas)), [], $previo];
}

$errores = [];
$previo = ['entrega' => 'retiro'];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    [$destino, $errores, $previo] = la_cotizacion($_POST, $n, $nota);
    if ($destino !== null) {
        header('Location: ' . $destino, true, 303);
        exit;
    }
    http_response_code(422);
}
$val = static fn (string $k): string => (string) ($previo[$k] ?? '');
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="la-error" id="err-' . demo_e($k) . '">' . demo_e($errores[$k]) . '</p>' : '';
$inv = static fn (string $k): string => isset($errores[$k]) ? ' aria-invalid="true" aria-describedby="err-' . demo_e($k) . '"' : '';
$esDespacho = $val('entrega') === 'despacho';

/** Logo: una tuerca con una hoja de acacia. */
$logo = '<svg class="la-logo__icono" viewBox="0 0 48 48" width="44" height="44" aria-hidden="true" focusable="false">'
    . '<path d="M24 3 42 13.5v21L24 45 6 34.5v-21z" fill="#f2c230" stroke="#1b231f" stroke-width="2.4" stroke-linejoin="round"/>'
    . '<circle cx="24" cy="24" r="9.5" fill="#1f4a3a"/>'
    . '<path d="M24 17c4 2.6 4 11.4 0 14-4-2.6-4-11.4 0-14z" fill="#f7f1e6"/><path d="M24 18.5v12" stroke="#1f4a3a" stroke-width="1.2"/></svg>';

demo_head([
    'titulo' => $n['nombre'] . ' · Ferretería de barrio en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para una ferretería de barrio en ' . $n['comuna'] . ', Santiago: productos con precio, arriendo de herramientas, cotización de listas por WhatsApp, horario y mapa.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Bree+Serif&family=Kalam:wght@400;700&family=Mulish:wght@400;600;700;800&display=swap',
    'css' => ['/demos/ferreteria/simple/estilo.css'],
    'tema' => '#1f4a3a',
    'icono' => ['A', '#1f4a3a', '#f2c230'],
    'clase_body' => 'la',
]);
demo_aviso($rubro);
?>
<header class="la-top">
  <div class="la-envoltura la-top__fila">
    <a class="la-logo" href="#contenido" aria-label="<?= demo_e($n['nombre']) ?>, inicio">
      <?= $logo ?>
      <span class="la-logo__texto"><?= demo_e($n['nombre_corto']) ?><small>Ferretería · <?= demo_e($n['comuna']) ?> · desde <?= (int) $n['desde'] ?></small></span>
    </a>
    <nav class="la-nav" aria-label="Secciones">
      <a href="#productos">Productos</a>
      <a href="#arriendo">Arriendo</a>
      <a href="#cotiza">Cotiza tu lista</a>
      <a href="#consejos">Consejos</a>
      <a href="#horario">Horario</a>
    </nav>
    <a class="la-btn la-btn--chico" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>WhatsApp</span></a>
  </div>
</header>

<main id="contenido">
  <section class="la-hero" aria-labelledby="hero-titulo">
    <div class="la-envoltura la-hero__grid">
      <div class="la-hero__texto">
        <p class="la-etiqueta-kraft">Atendida por sus dueños desde <?= (int) $n['desde'] ?></p>
        <h1 id="hero-titulo"><?= demo_e($n['nombre']) ?></h1>
        <p class="la-hero__eslogan"><?= demo_e($n['eslogan']) ?></p>
        <p class="la-hero__bajada"><?= demo_e($n['bajada']) ?></p>
        <div class="la-acciones">
          <a class="la-btn" href="#cotiza">Cotiza tu lista</a>
          <a class="la-btn la-btn--claro" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
        <p class="la-hero__estado"><span class="la-punto<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
      </div>
      <figure class="la-hero__foto">
        <span class="la-gancho la-gancho--izq" aria-hidden="true"></span><span class="la-gancho la-gancho--der" aria-hidden="true"></span>
        <?= demo_img($n['fotos']['hero'], 'Interior de una ferretería antigua con estantes llenos de cajas, una escalera de madera y baldes', ['lazy' => false, 'ancho' => 800, 'alto' => 1000, 'anchos' => [480, 800, 1100], 'sizes' => '(max-width: 760px) 86vw, 420px']) ?>
        <figcaption class="la-colgante"><span>Copia de llaves</span><strong>al momento</strong><em>desde $1.500</em></figcaption>
      </figure>
    </div>
  </section>

  <div class="la-huincha" aria-hidden="true"><div class="la-huincha__numeros"><?php for ($i = 1; $i <= 40; $i++): ?><span><?= $i * 10 ?></span><?php endfor; ?></div></div>

  <section class="la-seccion" id="productos" aria-labelledby="productos-titulo">
    <div class="la-envoltura">
      <div class="la-cabeza">
        <p class="la-antetitulo">Lo que más se lleva el barrio</p>
        <h2 id="productos-titulo">Productos con precio a la vista</h2>
        <p>Una muestra de lo que hay en el local. Toca «Pedir» y se abre WhatsApp con el producto escrito: lo separamos y te avisamos.</p>
      </div>
      <ul class="la-productos">
        <?php foreach ($n['productos'] as $id => [$nombre, $detalle, $precio, $foto, $alt, $sello]): ?>
        <li class="la-producto" id="p-<?= demo_e($id) ?>">
          <div class="la-producto__foto">
            <?= demo_img($foto, $alt, ['ancho' => 600, 'alto' => 600, 'anchos' => [320, 600, 900], 'sizes' => '(max-width: 560px) 46vw, (max-width: 1000px) 30vw, 260px']) ?>
            <?php if ($sello !== ''): ?><span class="la-producto__sello"><?= demo_e($sello) ?></span><?php endif; ?>
          </div>
          <p class="la-precio" aria-label="Precio <?= demo_e(demo_clp($precio)) ?>"><?= demo_e(demo_clp($precio)) ?></p>
          <h3><?= demo_e($nombre) ?></h3>
          <p><?= demo_e($detalle) ?></p>
          <a class="la-pedir" href="<?= demo_e($wa('Hola, quiero pedir: 1 × ' . $nombre . ' (' . demo_clp($precio) . '). ¿Lo tienen disponible?')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($nombre) ?> por WhatsApp"><?= demo_icono_wa(16) ?> Pedir</a>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="la-seccion la-tablero" id="pasillos" aria-labelledby="pasillos-titulo">
    <div class="la-envoltura">
      <div class="la-cabeza la-cabeza--claro">
        <p class="la-antetitulo">Recorre el local</p>
        <h2 id="pasillos-titulo">Ocho pasillos, miles de cosas chicas</h2>
        <p>Si buscas algo que no ves aquí, pregunta: lo más probable es que esté en una cajita detrás del mesón.</p>
      </div>
      <ol class="la-pasillos">
        <?php foreach ($n['pasillos'] as $i => [$nombre, $ejemplos]): ?>
        <li class="la-pasillo">
          <span class="la-pasillo__num" aria-hidden="true"><?= $i + 1 ?></span>
          <h3><span class="demo-sr">Pasillo <?= $i + 1 ?>: </span><?= demo_e($nombre) ?></h3>
          <p><?= demo_e($ejemplos) ?></p>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section class="la-seccion" id="arriendo" aria-labelledby="arriendo-titulo">
    <div class="la-envoltura">
      <div class="la-cabeza">
        <p class="la-antetitulo">No la compres para usarla una vez</p>
        <h2 id="arriendo-titulo">Arriendo de herramientas</h2>
        <p>Retiras en el mesón con tu carnet y una garantía en efectivo o transferencia, que te devolvemos al entregar. Medio día: hasta 4 horas.</p>
      </div>
      <ul class="la-arriendo">
        <?php foreach ($n['arriendo'] as $id => [$nombre, $detalle, $dia, $medio, $garantia, $foto, $alt]): ?>
        <li class="la-arriendo__item">
          <div class="la-arriendo__foto"><?= demo_img($foto, $alt, ['ancho' => 600, 'alto' => 450, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 92vw, 280px']) ?></div>
          <div class="la-arriendo__texto">
            <h3><?= demo_e($nombre) ?></h3>
            <p><?= demo_e($detalle) ?></p>
            <dl class="la-tarifas">
              <div><dt>Día</dt><dd><?= demo_e(demo_clp($dia)) ?></dd></div>
              <div><dt>Medio día</dt><dd><?= $medio > 0 ? demo_e(demo_clp($medio)) : '—' ?></dd></div>
              <div><dt>Garantía</dt><dd><?= demo_e(demo_clp($garantia)) ?></dd></div>
            </dl>
            <a class="la-pedir" href="<?= demo_e($wa('Hola, quiero arrendar: ' . $nombre . ' (' . demo_clp($dia) . ' por día). ¿Para qué día está disponible?')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(16) ?> Reservar</a>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="la-seccion la-kraft" id="cotiza" aria-labelledby="cotiza-titulo">
    <div class="la-envoltura la-cotiza">
      <div class="la-cotiza__texto">
        <p class="la-antetitulo">¿Tienes la lista del maestro?</p>
        <h2 id="cotiza-titulo">Cotiza tu lista</h2>
        <p>Escríbela como en la libreta, una cosa por línea y con la cantidad. Te llega a nuestro WhatsApp, te respondemos con el total y lo dejamos separado.</p>
        <ol class="la-pasos">
          <li>Escribe o pega la lista.</li>
          <li>Elige si la retiras o te la llevamos.</li>
          <li>Se abre WhatsApp con todo escrito: solo envías.</li>
        </ol>
        <p class="la-pequeno">Despacho en <?= demo_e($n['comuna']) ?> <?= demo_e(demo_clp($n['comunas_despacho'][$n['comuna']])) ?>, gratis sobre <?= demo_e(demo_clp($n['despacho_gratis'])) ?>. También a comunas vecinas.</p>
      </div>
      <form class="la-libreta" method="post" action="#cotiza" novalidate>
        <?php if ($errores): ?><p class="la-alerta" role="alert">Revisa lo marcado y vuelve a enviar.</p><?php endif; ?>
        <label class="la-libreta__titulo" for="lista">Mi lista de materiales</label>
        <textarea id="lista" name="lista" rows="8" maxlength="800" required placeholder="2 sacos de cemento&#10;1 galón de látex blanco&#10;10 tarugos de 8 mm con tornillo&#10;1 llave de paso 1/2&quot;"<?= $inv('lista') ?>><?= demo_e($val('lista')) ?></textarea>
        <?= $err('lista') ?>
        <fieldset class="la-entrega">
          <legend>¿Cómo la recibes?</legend>
          <label class="la-opcion"><input type="radio" name="entrega" value="retiro"<?= !$esDespacho ? ' checked' : '' ?>> <span><strong>Retiro en el local</strong><small>Sin costo. Te avisamos cuando esté lista.</small></span></label>
          <label class="la-opcion"><input type="radio" name="entrega" value="despacho"<?= $esDespacho ? ' checked' : '' ?>> <span><strong>Despacho</strong><small>Mismo día si pides antes de las 15:00.</small></span></label>
          <?= $err('entrega') ?>
        </fieldset>
        <div class="la-campos">
          <div class="la-campo">
            <label for="comuna">Comuna <small>(si es despacho)</small></label>
            <select id="comuna" name="comuna"<?= $inv('comuna') ?>>
              <option value="">Elige tu comuna</option>
              <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
              <option value="<?= demo_e($comuna) ?>"<?= $val('comuna') === $comuna ? ' selected' : '' ?>><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
              <?php endforeach; ?>
            </select>
            <?= $err('comuna') ?>
          </div>
          <div class="la-campo">
            <label for="calle">Calle y número <small>(si es despacho)</small></label>
            <input id="calle" name="calle" maxlength="100" autocomplete="off" value="<?= demo_e($val('calle')) ?>"<?= $inv('calle') ?>>
            <?= $err('calle') ?>
          </div>
          <div class="la-campo">
            <label for="nombre">Tu nombre <small>(opcional)</small></label>
            <input id="nombre" name="nombre" maxlength="40" autocomplete="off" value="<?= demo_e($val('nombre')) ?>">
          </div>
        </div>
        <button class="la-btn la-btn--bloque" type="submit"><?= demo_icono_wa() ?> Enviar lista por WhatsApp</button>
        <p class="la-pequeno">Demo: no guardamos tus datos. La lista se arma aquí y se envía por WhatsApp.</p>
      </form>
    </div>
  </section>

  <section class="la-seccion" id="consejos" aria-labelledby="consejos-titulo">
    <div class="la-envoltura">
      <div class="la-consejos__cabeza">
        <div class="la-cabeza">
          <p class="la-antetitulo">Asesoría sin costo</p>
          <h2 id="consejos-titulo">Pregúntele a don Hugo</h2>
          <p>Treinta y tantos años detrás del mesón dan para mucho. Antes de comprar de más, pregunte: estas son las consultas que más nos hacen.</p>
          <a class="la-btn" href="<?= demo_e($wa('Hola don Hugo, tengo una consulta: ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Hacer una consulta</span></a>
        </div>
        <figure class="la-meson">
          <?= demo_img($n['fotos']['meson'], 'Ferretería antigua con etiquetas colgando del techo, mangueras y una balanza', ['ancho' => 800, 'alto' => 600, 'anchos' => [480, 800, 1200], 'sizes' => '(max-width: 860px) 92vw, 46vw']) ?>
          <figcaption>Si no está a la vista, está en una cajita: pregunte.</figcaption>
        </figure>
      </div>
      <ul class="la-consejos">
        <?php foreach ($n['consejos'] as [$pregunta, $respuesta, $foto, $alt]): ?>
        <li class="la-consejo">
          <?php if ($foto !== null): ?><?= demo_img($n['fotos'][$foto], $alt, ['ancho' => 300, 'alto' => 300, 'anchos' => [200, 300, 600], 'sizes' => '96px', 'clase' => 'la-consejo__foto']) ?><?php endif; ?>
          <h3><?= demo_e($pregunta) ?></h3>
          <p><?= demo_e($respuesta) ?></p>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="la-seccion la-seccion--crema" id="servicios" aria-labelledby="servicios-titulo">
    <div class="la-envoltura la-servicios">
      <figure class="la-servicios__foto">
        <?= demo_img($n['fotos']['llaves'], 'Tablero con decenas de llaves vírgenes de colores listas para copiar', ['ancho' => 600, 'alto' => 800, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 760px) 60vw, 300px']) ?>
      </figure>
      <div>
        <p class="la-antetitulo">En el mesón</p>
        <h2 id="servicios-titulo">Servicios mientras esperas</h2>
        <ul class="la-servicios__lista">
          <?php foreach ($n['servicios'] as [$nombre, $detalle, $precio]): ?>
          <li><span><strong><?= demo_e($nombre) ?></strong><small><?= demo_e($detalle) ?></small></span><span class="la-servicios__precio"><?= demo_e($precio) ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </section>

  <section class="la-seccion" id="horario" aria-labelledby="horario-titulo">
    <div class="la-envoltura la-horario">
      <div>
        <p class="la-antetitulo">Te esperamos</p>
        <h2 id="horario-titulo">Horario y despacho</h2>
        <p class="la-estado-linea"><span class="la-punto<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario la-tabla-horario'); ?>
      </div>
      <div class="la-despacho">
        <h3>Despacho de tu lista</h3>
        <dl class="la-tarifas la-tarifas--lista">
          <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
          <div><dt><?= demo_e($comuna) ?></dt><dd><?= demo_e(demo_clp($costo)) ?></dd></div>
          <?php endforeach; ?>
        </dl>
        <p class="la-pequeno">Gratis en <?= demo_e($n['comuna']) ?> sobre <?= demo_e(demo_clp($n['despacho_gratis'])) ?>. Pagos con efectivo, débito, crédito o transferencia.</p>
      </div>
    </div>
  </section>

  <div class="la-seccion la-seccion--crema">
    <div class="la-envoltura">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería tu ficha en Google Maps: horario, cómo llegar y lo que dicen los vecinos.']); ?>
    </div>
  </div>

  <section class="la-cierre la-tablero" aria-labelledby="cierre-titulo">
    <div class="la-envoltura">
      <h2 id="cierre-titulo">¿Le falta una pieza?</h2>
      <p>Mándenos una foto por WhatsApp y le decimos si la tenemos, cuánto cuesta y cómo se instala.</p>
      <div class="la-acciones">
        <a class="la-btn" href="<?= demo_e($wa('Hola, les mando una foto de la pieza que necesito: ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Enviar una foto</span></a>
        <a class="la-btn la-btn--borde" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
      </div>
    </div>
  </section>
</main>

<footer class="la-pie">
  <div class="la-envoltura">
    <p class="la-pie__marca"><?= demo_e($n['nombre']) ?></p>
    <p><?= demo_e($n['comuna']) ?>, Santiago · Gasfitería, electricidad, pinturas, herramientas y arriendo</p>
    <?php demo_aviso_pie($rubro); ?>
  </div>
</footer>

<nav class="la-barra" aria-label="Contacto rápido">
  <a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
  <a href="#cotiza"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M6 3h9l4 4v14H6z M9 10h7M9 14h7M9 18h4" fill="none" stroke="currentColor" stroke-width="1.8"/></svg><span>Mi lista</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
</nav>
<?php demo_scripts(); ?>
</body>
</html>
