<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version);

/** Enlace de WhatsApp con la nota de pedido de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);
$waGeneral = $wa('Hola, quiero pedir un presupuesto en ' . $n['nombre'] . '. Mi auto es (patente y modelo): ');
$estado = demo_horario_estado($n['horario']);

/**
 * Formulario de presupuesto (sin JavaScript): valida en el servidor y redirige a WhatsApp
 * con el mensaje armado. Devuelve [url|null, errores, valores enviados].
 */
function tm_presupuesto(array $post, array $n, string $nota): array
{
    $txt = static function (string $k, int $max) use ($post): string {
        $v = $post[$k] ?? '';
        return is_string($v) ? mb_substr(trim(preg_replace('/\s+/u', ' ', $v) ?? ''), 0, $max) : '';
    };
    $previo = [
        'patente' => $txt('patente', 10),
        'modelo' => $txt('modelo', 40),
        'anio' => $txt('anio', 4),
        'km' => $txt('km', 9),
        'servicio' => $txt('servicio', 20),
        'problema' => $txt('problema', 300),
        'nombre' => $txt('nombre', 40),
    ];
    $errores = [];
    $patente = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $previo['patente']) ?? '');
    if (!preg_match('/^[A-Z]{2}[A-Z0-9]{2}[0-9]{2}$/', $patente)) {
        $errores['patente'] = 'Escribe la patente con sus 6 caracteres, por ejemplo ABCD12 o AB1234.';
    }
    if (mb_strlen($previo['modelo']) < 2) {
        $errores['modelo'] = 'Cuéntanos la marca y el modelo del auto.';
    }
    $maxAnio = (int) date('Y') + 1;
    if ($previo['anio'] !== '' && (!ctype_digit($previo['anio']) || (int) $previo['anio'] < 1970 || (int) $previo['anio'] > $maxAnio)) {
        $errores['anio'] = 'El año debe ser un número entre 1970 y ' . $maxAnio . '.';
    }
    $km = preg_replace('/[.\s]/', '', $previo['km']) ?? '';
    if ($km !== '' && (!ctype_digit($km) || strlen($km) > 7)) {
        $errores['km'] = 'El kilometraje lleva solo números, por ejemplo 84000.';
    }
    if ($previo['servicio'] !== 'otro' && !isset($n['servicios'][$previo['servicio']])) {
        $errores['servicio'] = 'Elige un servicio de la lista.';
    }
    if ($errores) {
        return [null, $errores, $previo];
    }
    $lineas = ['Hola, quiero un presupuesto en ' . $n['nombre'] . '.'];
    if ($previo['servicio'] === 'otro') {
        $lineas[] = 'Servicio: no sé qué tiene, quiero un diagnóstico';
    } else {
        [$nombre, , $desde] = $n['servicios'][$previo['servicio']];
        $lineas[] = 'Servicio: ' . $nombre . ' (desde ' . demo_clp($desde) . ')';
    }
    $lineas[] = 'Patente: ' . $patente;
    $lineas[] = 'Auto: ' . $previo['modelo'] . ($previo['anio'] !== '' ? ' ' . $previo['anio'] : '');
    if ($km !== '') {
        $lineas[] = 'Kilometraje: ' . number_format((int) $km, 0, ',', '.') . ' km';
    }
    if ($previo['problema'] !== '') {
        $lineas[] = 'Qué le pasa: ' . $previo['problema'];
    }
    if ($previo['nombre'] !== '') {
        $lineas[] = 'Nombre: ' . $previo['nombre'];
    }
    $lineas[] = $nota;
    return [demo_wa(implode("\n", $lineas)), [], $previo];
}

$errores = [];
$previo = ['servicio' => is_string($_GET['servicio'] ?? null) && isset($n['servicios'][$_GET['servicio']]) ? $_GET['servicio'] : ''];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    [$destino, $errores, $previo] = tm_presupuesto($_POST, $n, $nota);
    if ($destino !== null) {
        header('Location: ' . $destino, true, 303);
        exit;
    }
    http_response_code(422);
}
$val = static fn (string $k): string => (string) ($previo[$k] ?? '');
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="tm-error" id="err-' . demo_e($k) . '">' . demo_e($errores[$k]) . '</p>' : '';
$inv = static fn (string $k): string => isset($errores[$k]) ? ' aria-invalid="true" aria-describedby="err-' . demo_e($k) . '"' : '';

$icoLlave = '<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.8-3.8a6 6 0 0 1-7.9 7.9l-6.9 6.9a2.1 2.1 0 0 1-3-3l6.9-6.9a6 6 0 0 1 7.9-7.9z"/></svg>';

demo_head([
    'titulo' => $n['nombre'] . ' · Taller mecánico en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para un taller mecánico de barrio en ' . $n['comuna'] . ', Santiago: servicios con precio desde, pauta de mantención por kilometraje y presupuesto por WhatsApp.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Public+Sans:wght@400;500;700&family=Zilla+Slab:wght@500;700&display=swap',
    'css' => ['/demos/taller/simple/estilo.css'],
    'tema' => '#23367d',
    'icono' => ['M', '#23367d', '#f1e9d8'],
    'clase_body' => 'tm',
]);
demo_aviso($rubro);
?>
<header class="tm-top">
  <div class="tm-envoltura tm-top__fila">
    <a class="tm-marca" href="#contenido">
      <span class="tm-marca__sello" aria-hidden="true"><?= $icoLlave ?></span>
      <span class="tm-marca__nombre"><?= demo_e($n['nombre']) ?><small>Mecánica de confianza · <?= demo_e($n['comuna']) ?></small></span>
    </a>
    <nav class="tm-nav" aria-label="Secciones">
      <a href="#servicios">Servicios</a>
      <a href="#kilometraje">Mantención</a>
      <a href="#presupuesto">Presupuesto</a>
      <a href="#taller">El taller</a>
      <a href="#horario">Horario</a>
    </nav>
    <a class="tm-btn tm-btn--chico" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Cotizar</span></a>
  </div>
</header>

<main id="contenido">
  <section class="tm-hero" aria-labelledby="hero-titulo">
    <div class="tm-envoltura tm-hero__grid">
      <div class="tm-hero__texto">
        <p class="tm-maquina">Orden de trabajo n.º 0001 · <?= demo_e($n['nombre']) ?> · <?= demo_e($n['comuna']) ?></p>
        <h1 id="hero-titulo">Primero te explicamos.<br> <span>Después reparamos.</span></h1>
        <p class="tm-hero__bajada">Taller de barrio desde <?= (int) $n['desde'] ?>. Mantención, frenos, embrague, scanner y todo lo que tu auto necesite, con presupuesto por escrito antes de tocar una sola tuerca.</p>
        <p class="tm-estado-linea"><span class="tm-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <div class="tm-acciones">
          <a class="tm-btn" href="#presupuesto"><?= $icoLlave ?><span>Pedir presupuesto</span></a>
          <a class="tm-btn tm-btn--claro" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
      </div>
      <figure class="tm-hero__foto">
        <span class="tm-clip" aria-hidden="true"></span>
        <?= demo_img($n['fotos']['hero'], 'Mecánica sonriente con una llave en la mano, en el taller', ['lazy' => false, 'ancho' => 800, 'alto' => 960, 'sizes' => '(max-width: 860px) 86vw, 440px', 'anchos' => [480, 800, 1100]]) ?>
        <figcaption><span class="tm-maquina">Marisol Fuentes</span> jefa de taller y mecánica hace 25 años</figcaption>
        <p class="tm-timbre" aria-hidden="true"><span>Presupuesto</span><strong>sin costo</strong></p>
      </figure>
    </div>
  </section>

  <section class="tm-promesas" aria-label="Lo que prometemos">
    <ul class="tm-envoltura">
      <?php foreach ($n['promesas'] as $p): ?>
      <li><span class="tm-check" aria-hidden="true">✓</span><?= demo_e($p) ?></li>
      <?php endforeach; ?>
    </ul>
  </section>

  <section class="tm-seccion" id="servicios" aria-labelledby="servicios-titulo">
    <div class="tm-envoltura">
      <header class="tm-encabezado">
        <p class="tm-maquina">Servicios y precios</p>
        <h2 id="servicios-titulo">Lo que más nos piden</h2>
        <p>Precios «desde» para un auto mediano, con mano de obra incluida. El valor exacto depende del modelo: te lo confirmamos por escrito antes de empezar.</p>
      </header>
      <div class="tm-destacados">
        <?php foreach ($n['destacados'] as $id => $d): [$nombre, $detalle, $desde] = $n['servicios'][$id]; ?>
        <article class="tm-destacado">
          <div class="tm-destacado__foto">
            <?= demo_img($d['foto'], $d['alt'], ['ancho' => 600, 'alto' => 450, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 90vw, 33vw']) ?>
            <span class="tm-cinta"><?= demo_e($d['sello']) ?></span>
          </div>
          <div class="tm-destacado__texto">
            <h3><?= demo_e($nombre) ?></h3>
            <p><?= demo_e($detalle) ?></p>
            <p class="tm-desde"><small>desde</small> <?= demo_e(demo_clp($desde)) ?></p>
            <a class="tm-enlace" href="<?= demo_e($wa('Hola, quiero cotizar: ' . $nombre . ' (desde ' . demo_clp($desde) . '). Mi auto es (patente y modelo): ')) ?>" target="_blank" rel="noopener">Cotizar por WhatsApp</a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <div class="tm-orden" role="group" aria-labelledby="orden-titulo">
        <div class="tm-orden__cabeza">
          <h3 id="orden-titulo">Orden de trabajo <span class="tm-maquina">· todos los servicios</span></h3>
          <dl class="tm-orden__campos" aria-hidden="true">
            <div><dt>Cliente</dt><dd>tú</dd></div>
            <div><dt>Patente</dt><dd>&nbsp;</dd></div>
            <div><dt>Atendió</dt><dd>Marisol</dd></div>
          </dl>
        </div>
        <ul class="tm-orden__lista">
          <?php foreach ($n['servicios'] as $id => [$nombre, $detalle, $desde]): ?>
          <li class="tm-orden__fila">
            <span class="tm-casilla" aria-hidden="true"></span>
            <div class="tm-orden__texto">
              <strong><?= demo_e($nombre) ?></strong>
              <span><?= demo_e($detalle) ?></span>
            </div>
            <span class="tm-orden__precio"><small>desde</small> <?= demo_e(demo_clp($desde)) ?></span>
            <a class="tm-orden__pedir" href="<?= demo_e($wa('Hola, quiero cotizar: ' . $nombre . ' (desde ' . demo_clp($desde) . '). Mi auto es (patente y modelo): ')) ?>" target="_blank" rel="noopener" aria-label="Cotizar <?= demo_e($nombre) ?> por WhatsApp">Cotizar</a>
          </li>
          <?php endforeach; ?>
        </ul>
        <p class="tm-orden__pie">Atendemos todas las marcas, bencineros y diésel livianos. Repuestos originales o alternativos de buena calidad, a tu elección.</p>
      </div>
    </div>
  </section>

  <section class="tm-seccion tm-seccion--azul" id="kilometraje" aria-labelledby="km-titulo">
    <div class="tm-envoltura">
      <header class="tm-encabezado tm-encabezado--claro">
        <p class="tm-maquina">Pauta de mantención</p>
        <h2 id="km-titulo">¿Cuántos kilómetros marca tu auto?</h2>
        <p>Busca el número más cercano a tu cuentakilómetros: ahí está lo que toca revisar y cuánto cuesta. Si tu auto tiene una pauta distinta, la seguimos.</p>
      </header>
      <ol class="tm-pauta">
        <?php foreach ($n['pauta'] as $km => [$incluye, $desde]): $kmTexto = number_format($km, 0, ',', '.'); ?>
        <li class="tm-km">
          <p class="tm-odo" aria-label="<?= demo_e($kmTexto) ?> kilómetros">
            <?php foreach (str_split(str_pad((string) $km, 6, '0', STR_PAD_LEFT)) as $digito): ?><span aria-hidden="true"><?= demo_e($digito) ?></span><?php endforeach; ?>
            <small aria-hidden="true">km</small>
          </p>
          <ul>
            <?php foreach ($incluye as $item): ?><li><?= demo_e($item) ?></li><?php endforeach; ?>
          </ul>
          <p class="tm-km__pie">
            <span class="tm-desde"><small>desde</small> <?= demo_e(demo_clp($desde)) ?></span>
            <a class="tm-enlace tm-enlace--claro" href="<?= demo_e($wa('Hola, quiero agendar la mantención de los ' . $kmTexto . ' km (desde ' . demo_clp($desde) . '). Mi auto es (patente y modelo): ')) ?>" target="_blank" rel="noopener" aria-label="Agendar la mantención de los <?= demo_e($kmTexto) ?> km por WhatsApp">Agendar</a>
          </p>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section class="tm-seccion" id="como" aria-labelledby="como-titulo">
    <div class="tm-envoltura tm-como">
      <figure class="tm-como__foto">
        <?= demo_img($n['fotos']['explica'], 'Mecánico sonriente reparando el tablero de un auto en el taller', ['ancho' => 800, 'alto' => 900, 'sizes' => '(max-width: 860px) 90vw, 45vw']) ?>
      </figure>
      <div>
        <header class="tm-encabezado">
          <p class="tm-maquina">Cómo trabajamos</p>
          <h2 id="como-titulo">Sin sorpresas en la cuenta</h2>
        </header>
        <ol class="tm-pasos">
          <?php foreach ($n['pasos'] as $i => [$titulo, $texto]): ?>
          <li><span class="tm-pasos__num" aria-hidden="true"><?= $i + 1 ?></span><div><h3><?= demo_e($titulo) ?></h3><p><?= demo_e($texto) ?></p></div></li>
          <?php endforeach; ?>
        </ol>
      </div>
    </div>
  </section>

  <section class="tm-seccion tm-seccion--papel" id="presupuesto" aria-labelledby="presupuesto-titulo">
    <div class="tm-envoltura tm-presupuesto">
      <div class="tm-presupuesto__intro">
        <p class="tm-maquina">Presupuesto por WhatsApp</p>
        <h2 id="presupuesto-titulo">Cuéntanos de tu auto</h2>
        <p>Completa la ficha y se abre WhatsApp con el mensaje listo. Te respondemos con el valor y el día en que te podemos atender, normalmente en menos de una hora.</p>
        <p class="tm-presupuesto__nota">Es una demo: no guardamos nada. El mensaje llega al WhatsApp de quien hizo este sitio.</p>
      </div>
      <form class="tm-ficha" method="post" action="#presupuesto" novalidate>
        <p class="tm-ficha__titulo"><span>Ficha del vehículo</span><span class="tm-maquina">copia cliente</span></p>
        <?php if ($errores): ?><p class="tm-alerta" role="alert">Revisa los datos marcados.</p><?php endif; ?>
        <div class="tm-ficha__grid">
          <div class="tm-campo">
            <label for="patente">Patente</label>
            <input id="patente" name="patente" maxlength="10" required autocomplete="off" autocapitalize="characters" placeholder="ABCD12" value="<?= demo_e($val('patente')) ?>"<?= $inv('patente') ?>>
            <?= $err('patente') ?>
          </div>
          <div class="tm-campo">
            <label for="modelo">Marca y modelo</label>
            <input id="modelo" name="modelo" maxlength="40" required autocomplete="off" placeholder="Ej.: city car, sedán, camioneta…" value="<?= demo_e($val('modelo')) ?>"<?= $inv('modelo') ?>>
            <?= $err('modelo') ?>
          </div>
          <div class="tm-campo">
            <label for="anio">Año <small>(opcional)</small></label>
            <input id="anio" name="anio" inputmode="numeric" maxlength="4" autocomplete="off" placeholder="2016" value="<?= demo_e($val('anio')) ?>"<?= $inv('anio') ?>>
            <?= $err('anio') ?>
          </div>
          <div class="tm-campo">
            <label for="km">Kilometraje <small>(opcional)</small></label>
            <input id="km" name="km" inputmode="numeric" maxlength="9" autocomplete="off" placeholder="84000" value="<?= demo_e($val('km')) ?>"<?= $inv('km') ?>>
            <?= $err('km') ?>
          </div>
          <div class="tm-campo tm-campo--ancho">
            <label for="servicio">¿Qué necesitas?</label>
            <select id="servicio" name="servicio" required<?= $inv('servicio') ?>>
              <option value="">Elige un servicio</option>
              <?php foreach ($n['servicios'] as $id => [$nombre, , $desde]): ?>
              <option value="<?= demo_e($id) ?>"<?= $val('servicio') === $id ? ' selected' : '' ?>><?= demo_e($nombre) ?> · desde <?= demo_e(demo_clp($desde)) ?></option>
              <?php endforeach; ?>
              <option value="otro"<?= $val('servicio') === 'otro' ? ' selected' : '' ?>>No sé qué tiene: quiero un diagnóstico</option>
            </select>
            <?= $err('servicio') ?>
          </div>
          <div class="tm-campo tm-campo--ancho">
            <label for="problema">¿Qué le notas? <small>(opcional)</small></label>
            <textarea id="problema" name="problema" rows="3" maxlength="300" placeholder="Ej.: chirría al frenar, se prendió una luz en el tablero…"><?= demo_e($val('problema')) ?></textarea>
          </div>
          <div class="tm-campo tm-campo--ancho">
            <label for="nombre">Tu nombre <small>(opcional)</small></label>
            <input id="nombre" name="nombre" maxlength="40" autocomplete="off" value="<?= demo_e($val('nombre')) ?>">
          </div>
        </div>
        <button class="tm-btn tm-btn--bloque" type="submit"><?= demo_icono_wa() ?><span>Enviar por WhatsApp</span></button>
      </form>
    </div>
  </section>

  <section class="tm-seccion" id="taller" aria-labelledby="taller-titulo">
    <div class="tm-envoltura tm-taller">
      <div class="tm-taller__texto">
        <header class="tm-encabezado">
          <p class="tm-maquina">El taller</p>
          <h2 id="taller-titulo">Un taller de barrio, de los de antes</h2>
        </header>
        <p>Marisol aprendió el oficio con su papá en este mismo galpón. Hoy atiende con su hermano Patricio y dos mecánicos más, y muchos clientes son los hijos de los clientes de entonces.</p>
        <p>Trabajamos ordenado, con herramientas a la vista y sin apuro innecesario: preferimos decirte «vuelve en seis meses» antes que cobrarte un arreglo que todavía no hace falta.</p>
        <dl class="tm-cifras">
          <div><dt>Desde</dt><dd><?= (int) $n['desde'] ?></dd></div>
          <div><dt>Elevadores</dt><dd>3</dd></div>
          <div><dt>Garantía</dt><dd>3 meses</dd></div>
        </dl>
      </div>
      <div class="tm-taller__fotos">
        <figure class="tm-polaroid tm-polaroid--a">
          <?= demo_img($n['fotos']['taller'], 'Banco de trabajo del taller con herramientas colgadas en la pared', ['ancho' => 700, 'alto' => 560, 'sizes' => '(max-width: 860px) 80vw, 36vw']) ?>
          <figcaption>El banco de trabajo</figcaption>
        </figure>
        <figure class="tm-polaroid tm-polaroid--b">
          <?= demo_img($n['fotos']['manos'], 'Manos con grasa ajustando una pieza del motor con una llave', ['ancho' => 500, 'alto' => 500, 'anchos' => [400, 600], 'sizes' => '(max-width: 860px) 50vw, 22vw']) ?>
          <figcaption>Manos a la obra</figcaption>
        </figure>
      </div>
    </div>
  </section>

  <section class="tm-seccion tm-seccion--papel" id="horario" aria-labelledby="horario-titulo">
    <div class="tm-envoltura tm-horario">
      <div>
        <header class="tm-encabezado">
          <p class="tm-maquina">Horario</p>
          <h2 id="horario-titulo">Cuándo atendemos</h2>
        </header>
        <p class="tm-estado-linea"><span class="tm-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario tm-tabla-horario'); ?>
      </div>
      <ul class="tm-datos">
        <li><strong>Con hora o sin hora</strong><span>Para un cambio de aceite o una revisión rápida puedes llegar sin avisar. Para trabajos largos, mejor pedir hora.</span></li>
        <li><strong>Mientras esperas</strong><span>Sala con café y wifi. Si el trabajo es largo, te avisamos por WhatsApp cuando esté listo.</span></li>
        <li><strong>Pago</strong><span>Efectivo, débito, crédito o transferencia. Boleta o factura.</span></li>
      </ul>
    </div>
  </section>

  <div class="tm-seccion">
    <div class="tm-envoltura">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería la ficha del taller en Google Maps: horario, cómo llegar y lo que dicen los clientes.']); ?>
    </div>
  </div>

  <section class="tm-cierre" aria-labelledby="cierre-titulo">
    <div class="tm-envoltura">
      <p class="tm-maquina">¿Se prendió una luz en el tablero?</p>
      <h2 id="cierre-titulo">Escríbenos antes de que sea caro.</h2>
      <div class="tm-acciones">
        <a class="tm-btn tm-btn--claro" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
        <a class="tm-btn tm-btn--borde" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
      </div>
    </div>
  </section>
</main>

<footer class="tm-pie">
  <div class="tm-envoltura">
    <p class="tm-pie__marca"><?= demo_e($n['nombre']) ?></p>
    <p><?= demo_e($n['comuna']) ?>, Santiago · Presupuestos por WhatsApp</p>
    <?php demo_aviso_pie($rubro); ?>
  </div>
</footer>

<nav class="tm-barra" aria-label="Contacto rápido">
  <a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
  <a href="#servicios"><?= $icoLlave ?><span>Precios</span></a>
</nav>
<?php demo_scripts(); ?>
</body>
</html>
