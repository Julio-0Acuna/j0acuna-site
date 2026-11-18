<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/_comun/comun.php';
require_once dirname(__DIR__) . '/_patente.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version);

/** Enlace de WhatsApp con la nota de pedido de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);
$waGeneral = $wa('Hola, quiero cotizar un repuesto en ' . $n['nombre'] . '.');
$estado = demo_horario_estado($n['horario']);

/**
 * "Cotiza con tu patente" (sin JavaScript): valida en el servidor y redirige a WhatsApp con la
 * cotización armada. Devuelve [url|null, errores, valores enviados].
 */
function lr_cotizacion(array $post, array $n, string $nota): array
{
    $previo = [
        'patente' => rp_linea($post['patente'] ?? '', 12),
        'repuesto' => rp_linea($post['repuesto'] ?? '', 200),
        'vehiculo' => rp_linea($post['vehiculo'] ?? '', 60),
        'anio' => rp_linea($post['anio'] ?? '', 4),
        'motor' => rp_linea($post['motor'] ?? '', 20),
        'entrega' => rp_linea($post['entrega'] ?? 'retiro', 10),
        'comuna' => rp_linea($post['comuna'] ?? '', 30),
        'calle' => rp_linea($post['calle'] ?? '', 100),
        'nombre' => rp_linea($post['nombre'] ?? '', 40),
    ];
    $errores = [];
    $patente = rp_patente_normalizar($previo['patente']);
    if (mb_strlen($previo['repuesto']) < 3) {
        $errores['repuesto'] = 'Cuéntanos qué repuesto necesitas (ej.: pastillas de freno delanteras).';
    }
    if ($patente !== '' && !rp_patente_valida($patente)) {
        $errores['patente'] = 'Esa patente no tiene el formato chileno: 4 letras y 2 números (BB·CD·12) o 2 letras y 4 números (AB·12·34).';
    }
    if ($patente === '' && mb_strlen($previo['vehiculo']) < 3) {
        $errores['vehiculo'] = 'Escribe la patente o, si no la tienes a mano, la marca y el modelo del auto.';
    }
    $anioMax = (int) date('Y') + 1;
    if ($previo['anio'] !== '' && (!ctype_digit($previo['anio']) || (int) $previo['anio'] < 1970 || (int) $previo['anio'] > $anioMax)) {
        $errores['anio'] = 'Escribe el año con 4 números (ej.: 2016).';
    }
    if (!in_array($previo['entrega'], ['retiro', 'despacho'], true)) {
        $errores['entrega'] = 'Elige retiro en el mesón o despacho.';
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
    $lineas = ['Hola, quiero cotizar en ' . $n['nombre'] . ':', 'Repuesto: ' . $previo['repuesto']];
    if ($patente !== '') {
        $lineas[] = 'Patente: ' . rp_patente_formato($patente);
    }
    if ($previo['vehiculo'] !== '') {
        $lineas[] = 'Vehículo: ' . $previo['vehiculo'];
    }
    if ($previo['anio'] !== '') {
        $lineas[] = 'Año: ' . $previo['anio'];
    }
    if ($previo['motor'] !== '') {
        $lineas[] = 'Motor: ' . $previo['motor'];
    }
    if ($previo['entrega'] === 'despacho') {
        $lineas[] = 'Despacho: ' . $previo['calle'] . ', ' . $previo['comuna'] . ' (' . demo_clp($n['comunas_despacho'][$previo['comuna']]) . ')';
    } else {
        $lineas[] = 'Entrega: retiro en el mesón';
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
    [$destino, $errores, $previo] = lr_cotizacion($_POST, $n, $nota);
    if ($destino !== null) {
        header('Location: ' . $destino, true, 303);
        exit;
    }
    http_response_code(422);
}
$val = static fn (string $k): string => (string) ($previo[$k] ?? '');
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="lr-error" id="err-' . demo_e($k) . '">' . demo_e($errores[$k]) . '</p>' : '';
$inv = static fn (string $k): string => isset($errores[$k]) ? ' aria-invalid="true" aria-describedby="err-' . demo_e($k) . '"' : '';
$despacho = $val('entrega') === 'despacho';
$desde = min($n['comunas_despacho']);

demo_head([
    'titulo' => $n['nombre'] . ' · Repuestos de auto en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para una tienda de repuestos de barrio en ' . $n['comuna'] . ', Santiago: cotización por patente por WhatsApp, repuestos con código y precio, horario y despacho.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,400;0,600;0,800;0,900;1,800&family=Overpass+Mono:wght@500;700&display=swap',
    'css' => ['/demos/repuestos/simple/estilo.css'],
    'tema' => '#1b1c1e',
    'icono' => ['R', '#c2461f', '#fff'],
    'clase_body' => 'lr',
]);
demo_aviso($rubro);
?>
<header class="lr-top">
  <div class="lr-envoltura lr-top__fila">
    <a class="lr-marca" href="#contenido">
      <svg class="lr-marca__icono" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false"><circle cx="24" cy="24" r="22" fill="#c2461f"/><circle cx="24" cy="24" r="7" fill="none" stroke="#fff" stroke-width="3"/><path d="M24 6v8M24 34v8M6 24h8M34 24h8" stroke="#fff" stroke-width="3" stroke-linecap="round"/><path d="M29 12.5l3.5 1.6-1.6 3.5M19 35.5l-3.5-1.6 1.6-3.5" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
      <span class="lr-marca__nombre"><?= demo_e($n['nombre_corto']) ?><small>Repuestos · <?= demo_e($n['comuna']) ?></small></span>
    </a>
    <nav class="lr-nav" aria-label="Secciones">
      <a href="#cotiza">Cotizar</a>
      <a href="#repuestos">Repuestos</a>
      <a href="#calidades">Original o alternativo</a>
      <a href="#horario">Horario</a>
      <a href="#google">Ubicación</a>
    </nav>
    <a class="lr-btn lr-btn--chico" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>WhatsApp</span></a>
  </div>
</header>

<main id="contenido">
  <section class="lr-hero" aria-labelledby="lr-hero-titulo">
    <div class="lr-cortina">
      <div class="lr-envoltura lr-hero__grid">
        <div class="lr-hero__texto">
          <p class="lr-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
          <h1 id="lr-hero-titulo"><span class="lr-hero__pre">Repuestos</span> <?= demo_e($n['nombre_corto']) ?></h1>
          <p class="lr-hero__eslogan"><?= demo_e($n['eslogan']) ?>.</p>
          <p class="lr-hero__bajada"><?= demo_e($n['bajada']) ?></p>
          <div class="lr-acciones">
            <a class="lr-btn lr-btn--blanco" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Cotizar por WhatsApp</span></a>
            <a class="lr-btn lr-btn--borde" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
          </div>
        </div>

        <form class="lr-rapida" method="post" action="#cotiza" aria-labelledby="lr-rapida-titulo">
          <p class="lr-rapida__titulo" id="lr-rapida-titulo">Cotiza con tu patente</p>
          <label class="lr-placa" for="patente-rapida">
            <span class="lr-placa__pais" aria-hidden="true">CHILE</span>
            <span class="demo-sr">Patente del vehículo</span>
            <input id="patente-rapida" name="patente" value="<?= demo_e($val('patente')) ?>" maxlength="9" autocomplete="off" autocapitalize="characters" spellcheck="false" placeholder="BB CD 12">
          </label>
          <label class="lr-rapida__campo" for="repuesto-rapido">¿Qué repuesto necesitas?</label>
          <input class="lr-rapida__input" id="repuesto-rapido" name="repuesto" value="<?= demo_e($val('repuesto')) ?>" maxlength="200" autocomplete="off" placeholder="Ej.: pastillas de freno delanteras">
          <input type="hidden" name="entrega" value="retiro">
          <button class="lr-btn lr-btn--bloque" type="submit"><?= demo_icono_wa() ?><span>Enviar al WhatsApp</span></button>
          <p class="lr-rapida__nota">¿Sin la patente a mano? <a href="#cotiza">Cotiza con marca, modelo y año</a>.</p>
        </form>
      </div>
      <div class="lr-cortina__riel" aria-hidden="true"><span></span><span></span></div>
    </div>
    <figure class="lr-hero__abierto">
      <?= demo_img($n['fotos']['hero'], 'Muro de la tienda con repuestos colgados: bombas, filtros y piezas de motor', ['lazy' => false, 'ancho' => 1400, 'alto' => 600, 'anchos' => [800, 1400, 2000], 'sizes' => '100vw']) ?>
    </figure>
  </section>

  <section class="lr-seccion" id="repuestos" aria-labelledby="repuestos-titulo">
    <div class="lr-envoltura">
      <div class="lr-cabeza">
        <p class="lr-antetitulo">Lo que más sale del mesón</p>
        <h2 id="repuestos-titulo">Repuestos con precio a la vista</h2>
        <p>Cada repuesto tiene su código. Mándanos el código y la patente, y te confirmamos que calza antes de que vengas.</p>
      </div>
      <ul class="lr-cajas">
        <?php foreach ($n['destacados'] as $p): ?>
        <li class="lr-caja">
          <p class="lr-caja__codigo"><span><?= demo_e($p['codigo']) ?></span><span class="lr-barras" aria-hidden="true"></span></p>
          <div class="lr-caja__foto">
            <?= demo_img($p['foto'], $p['alt'], ['ancho' => 600, 'alto' => 450, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 600px) 92vw, (max-width: 1000px) 45vw, 280px']) ?>
            <?php if ($p['sello'] !== ''): ?><span class="lr-caja__sello"><?= demo_e($p['sello']) ?></span><?php endif; ?>
          </div>
          <div class="lr-caja__cuerpo">
            <h3><?= demo_e($p['nombre']) ?></h3>
            <p class="lr-caja__calza"><span>Calza</span> <?= demo_e($p['calza']) ?></p>
            <p class="lr-caja__detalle"><?= demo_e($p['detalle']) ?></p>
            <p class="lr-caja__precio"><?= demo_e(demo_clp($p['precio'])) ?></p>
            <a class="lr-btn lr-btn--chico lr-btn--oscuro" href="<?= demo_e($wa('Hola, quiero cotizar ' . $p['codigo'] . ' ' . $p['nombre'] . ' (' . demo_clp($p['precio']) . '). La patente de mi auto es: ')) ?>" target="_blank" rel="noopener" aria-label="Cotizar <?= demo_e($p['nombre']) ?> por WhatsApp"><?= demo_icono_wa(18) ?><span>Cotizar</span></a>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
      <p class="lr-ayuda">Precios de referencia para el repuesto alternativo. El precio final depende de tu auto: por eso pedimos la patente.</p>
    </div>
  </section>

  <section class="lr-letrero" aria-labelledby="letrero-titulo">
    <div class="lr-envoltura">
      <h2 id="letrero-titulo" class="lr-letrero__titulo">Tenemos de todo un poco</h2>
      <ul class="lr-letrero__lista">
        <?php foreach ($n['categorias'] as $c): ?>
        <li><a href="<?= demo_e($wa('Hola, busco repuestos de ' . mb_strtolower($c) . '. La patente de mi auto es: ')) ?>" target="_blank" rel="noopener"><?= demo_e($c) ?></a></li>
        <?php endforeach; ?>
      </ul>
      <p class="lr-letrero__pie">Toca una categoría y pregúntanos por WhatsApp. Si no lo tenemos, lo conseguimos para el otro día.</p>
    </div>
  </section>

  <section class="lr-seccion lr-seccion--cotiza" id="cotiza" aria-labelledby="cotiza-titulo">
    <div class="lr-envoltura lr-cotiza">
      <div class="lr-cotiza__texto">
        <p class="lr-antetitulo">Sin vueltas</p>
        <h2 id="cotiza-titulo">Cotiza en un minuto</h2>
        <ol class="lr-pasos">
          <li><strong>La patente o el auto</strong><span>Con la patente sabemos el año y el motor. Si no la tienes, escribe marca, modelo y año.</span></li>
          <li><strong>Qué necesitas</strong><span>Con tus palabras: «las pastillas de adelante», «el filtro y el aceite».</span></li>
          <li><strong>Te respondemos</strong><span>Precio y stock en original y alternativo. Retiras en el mesón o te lo llevamos.</span></li>
        </ol>
      </div>

      <form class="lr-etiqueta" method="post" action="#cotiza" novalidate>
        <p class="lr-etiqueta__cabeza"><span>Cotización</span><span class="lr-barras" aria-hidden="true"></span></p>
        <?php if ($errores): ?><p class="lr-alerta" role="alert">Revisa los campos marcados.</p><?php endif; ?>
        <div class="lr-campo lr-campo--placa">
          <label for="patente">Patente <small>(recomendado)</small></label>
          <span class="lr-placa lr-placa--chica">
            <span class="lr-placa__pais" aria-hidden="true">CHILE</span>
            <input id="patente" name="patente" value="<?= demo_e($val('patente')) ?>" maxlength="9" autocomplete="off" autocapitalize="characters" spellcheck="false" placeholder="BB CD 12"<?= $inv('patente') ?>>
          </span>
          <?= $err('patente') ?>
        </div>
        <div class="lr-campo">
          <label for="repuesto">¿Qué repuesto necesitas?</label>
          <textarea id="repuesto" name="repuesto" rows="2" maxlength="200" placeholder="Ej.: pastillas de freno delanteras y líquido de frenos"<?= $inv('repuesto') ?>><?= demo_e($val('repuesto')) ?></textarea>
          <?= $err('repuesto') ?>
        </div>
        <fieldset class="lr-grupo">
          <legend>Si no tienes la patente</legend>
          <div class="lr-campo">
            <label for="vehiculo">Marca y modelo</label>
            <input id="vehiculo" name="vehiculo" value="<?= demo_e($val('vehiculo')) ?>" maxlength="60" autocomplete="off" placeholder="Como aparece en el padrón"<?= $inv('vehiculo') ?>>
            <?= $err('vehiculo') ?>
          </div>
          <div class="lr-fila">
            <div class="lr-campo">
              <label for="anio">Año</label>
              <input id="anio" name="anio" value="<?= demo_e($val('anio')) ?>" inputmode="numeric" maxlength="4" autocomplete="off" placeholder="2016"<?= $inv('anio') ?>>
              <?= $err('anio') ?>
            </div>
            <div class="lr-campo">
              <label for="motor">Motor</label>
              <input id="motor" name="motor" value="<?= demo_e($val('motor')) ?>" maxlength="20" autocomplete="off" placeholder="1.6 bencina">
            </div>
          </div>
        </fieldset>
        <fieldset class="lr-grupo lr-grupo--entrega">
          <legend>Entrega</legend>
          <label class="lr-opcion"><input type="radio" name="entrega" value="retiro"<?= $despacho ? '' : ' checked' ?>> <span>Retiro en el mesón</span></label>
          <label class="lr-opcion"><input type="radio" name="entrega" value="despacho"<?= $despacho ? ' checked' : '' ?>> <span>Despacho en moto, desde <?= demo_e(demo_clp($desde)) ?></span></label>
          <?= $err('entrega') ?>
          <div class="lr-fila">
            <div class="lr-campo">
              <label for="comuna">Comuna <small>(si es despacho)</small></label>
              <select id="comuna" name="comuna"<?= $inv('comuna') ?>>
                <option value="">Elige la comuna</option>
                <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
                <option value="<?= demo_e($comuna) ?>"<?= $val('comuna') === $comuna ? ' selected' : '' ?>><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
                <?php endforeach; ?>
              </select>
              <?= $err('comuna') ?>
            </div>
            <div class="lr-campo">
              <label for="calle">Calle y número</label>
              <input id="calle" name="calle" value="<?= demo_e($val('calle')) ?>" maxlength="100" autocomplete="off"<?= $inv('calle') ?>>
              <?= $err('calle') ?>
            </div>
          </div>
        </fieldset>
        <div class="lr-campo">
          <label for="nombre">Tu nombre <small>(opcional)</small></label>
          <input id="nombre" name="nombre" value="<?= demo_e($val('nombre')) ?>" maxlength="40" autocomplete="off">
        </div>
        <button class="lr-btn lr-btn--bloque" type="submit"><?= demo_icono_wa() ?><span>Enviar cotización por WhatsApp</span></button>
        <p class="lr-etiqueta__pie">Es una demo: no guardamos tus datos. Se abre WhatsApp con tu cotización escrita.</p>
      </form>
    </div>
  </section>

  <section class="lr-seccion lr-seccion--gris" id="calidades" aria-labelledby="calidades-titulo">
    <div class="lr-envoltura">
      <div class="lr-cabeza">
        <p class="lr-antetitulo">Te explicamos sin letra chica</p>
        <h2 id="calidades-titulo">Original, alternativo o económico</h2>
        <p>Ejemplo con las pastillas de freno delanteras de un city car. Siempre te damos al menos dos opciones.</p>
      </div>
      <div class="lr-calidades">
        <?php foreach ($n['calidades'] as $c): ?>
        <article class="lr-calidad<?= $c['subtitulo'] === 'Lo que más vendemos' ? ' lr-calidad--destacada' : '' ?>">
          <p class="lr-calidad__sub"><?= demo_e($c['subtitulo']) ?></p>
          <h3><?= demo_e($c['titulo']) ?></h3>
          <p><?= demo_e($c['texto']) ?></p>
          <p class="lr-calidad__precio"><small>Pastillas delanteras</small><?= demo_e(demo_clp($c['precio'])) ?></p>
          <p class="lr-calidad__dura" aria-label="Duración: <?= (int) $c['nivel'] ?> de 3"><?php for ($i = 1; $i <= 3; $i++): ?><span class="<?= $i <= $c['nivel'] ? 'is-lleno' : '' ?>"></span><?php endfor; ?> <small>Duración</small></p>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="lr-seccion" id="meson" aria-labelledby="meson-titulo">
    <div class="lr-envoltura lr-meson">
      <figure class="lr-meson__foto">
        <?= demo_img($n['fotos']['meson'], 'Don Beto revisando una pieza de metal en el banco de trabajo', ['ancho' => 800, 'alto' => 900, 'anchos' => [480, 800, 1200], 'sizes' => '(max-width: 860px) 92vw, 45vw']) ?>
        <figcaption>Don Beto, en el mesón desde 1991 (persona ficticia).</figcaption>
      </figure>
      <div class="lr-meson__texto">
        <p class="lr-antetitulo">El mesón de la rotonda</p>
        <h2 id="meson-titulo">Repuestos de barrio, con nombre y apellido</h2>
        <p>Atendemos a vecinos, taxistas y maestros del barrio desde hace más de treinta años. Antes de venderte algo miramos el repuesto viejo, comparamos medidas y te explicamos qué conviene.</p>
        <ul class="lr-garantias">
          <li><strong>Si no calza, te lo cambiamos.</strong> Con la boleta y sin instalar, durante 30 días.</li>
          <li><strong>Batería y plumillas instaladas gratis</strong> en la puerta del local.</li>
          <li><strong>Lo que no tenemos, lo pedimos</strong> y llega al otro día hábil.</li>
          <li><strong>Pago</strong> en efectivo, débito, crédito o transferencia.</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="lr-seccion lr-seccion--gris" id="horario" aria-labelledby="horario-titulo">
    <div class="lr-envoltura lr-horario">
      <div class="lr-cartel">
        <h2 id="horario-titulo">Horario</h2>
        <p class="lr-cartel__estado"><span class="lr-punto <?= $estado['abierto'] ? 'is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario lr-tabla-horario'); ?>
      </div>
      <div class="lr-despacho">
        <h3>Despacho en moto</h3>
        <p>Si pides antes de las 15:00, llega el mismo día. Pagas al recibir.</p>
        <ul>
          <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
          <li><span><?= demo_e($comuna) ?></span><strong><?= demo_e(demo_clp($costo)) ?></strong></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </section>

  <div class="lr-seccion">
    <div class="lr-envoltura">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería tu ficha en Google Maps: horario, cómo llegar y lo que dicen los clientes del barrio.']); ?>
    </div>
  </div>

  <section class="lr-cierre" aria-labelledby="cierre-titulo">
    <div class="lr-envoltura">
      <h2 id="cierre-titulo">¿Se te prendió una luz en el tablero?</h2>
      <p>Mándanos la patente y una foto del repuesto viejo. Te respondemos con precio y stock.</p>
      <div class="lr-acciones">
        <a class="lr-btn lr-btn--blanco" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Escribir por WhatsApp</span></a>
        <a class="lr-btn lr-btn--borde" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
      </div>
    </div>
  </section>
</main>

<footer class="lr-pie">
  <div class="lr-envoltura">
    <p class="lr-pie__marca"><?= demo_e($n['nombre']) ?></p>
    <p><?= demo_e($n['comuna']) ?>, Santiago · Cotizaciones por WhatsApp · Repuestos genéricos para autos, camionetas y furgones</p>
    <?php demo_aviso_pie($rubro); ?>
  </div>
</footer>

<nav class="lr-barra" aria-label="Contacto rápido">
  <a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
  <a href="#cotiza"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><rect x="3" y="7" width="18" height="10" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M7 12h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg><span>Patente</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
</nav>
<?php demo_scripts(); ?>
</body>
</html>
