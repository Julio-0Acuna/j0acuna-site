<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/_comun/comun.php';
require_once dirname(__DIR__) . '/_receta.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$n = require __DIR__ . '/_datos.php';
$version = $rubro['versiones']['simple']['plan'];
$nota = demo_nota($rubro['rubro_nombre'], $version);

/** Enlace de WhatsApp con la nota de pedido de prueba. */
$wa = static fn (string $texto): string => demo_wa($texto . "\n" . $nota);
$waGeneral = $wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.');
$ahora = new DateTimeImmutable('now', new DateTimeZone(DEMO_ZONA_HORARIA));

/**
 * Semana de turno de noche: la que empieza el segundo lunes de cada mes (de lunes 09:00 a lunes 09:00).
 * Devuelve ['activo' => bool, 'inicio' => lunes, 'fin' => lunes siguiente] del turno en curso o del próximo.
 */
function em_turno(DateTimeImmutable $ahora): array
{
    $turnoDelMes = static function (DateTimeImmutable $dia): DateTimeImmutable {
        $primero = $dia->modify('first day of this month')->setTime(9, 0);
        $lunes = (int) $primero->format('N') === 1 ? $primero : $primero->modify('next monday')->setTime(9, 0);
        return $lunes->modify('+7 days');
    };
    $inicio = $turnoDelMes($ahora);
    if ($ahora >= $inicio->modify('+7 days')) {
        $inicio = $turnoDelMes($ahora->modify('first day of next month'));
    }
    $fin = $inicio->modify('+7 days');
    return ['activo' => $ahora >= $inicio && $ahora < $fin, 'inicio' => $inicio, 'fin' => $fin];
}

$turno = em_turno($ahora);
$estado = demo_horario_estado($n['horario'], $ahora);
if ($turno['activo'] && !$estado['abierto']) {
    $estado = ['abierto' => true, 'texto' => 'De turno · te atendemos por la ventanilla hasta las ' . $n['turno']['hasta']];
}
$domingoTurno = $turno['fin']->modify('-1 day');
$turnoTexto = $turno['activo']
    ? 'Esta semana estamos de turno: atendemos toda la noche por la ventanilla, hasta el ' . demo_fecha_larga($turno['fin']) . ' a las ' . $n['turno']['hasta'] . '.'
    : 'Próximo turno: del ' . demo_fecha_larga($turno['inicio']) . ' al ' . demo_fecha_larga($domingoTurno) . '.';

// Reserva de receta (sin JavaScript): valida en el servidor y redirige a WhatsApp con el mensaje armado.
$opcReceta = [
    'negocio' => $n['nombre'],
    'saludo' => 'Hola, quiero reservar un medicamento con receta en {negocio}.',
    'entregas' => ['retiro' => 'Retiro en el mesón', 'despacho' => 'Despacho en el barrio'],
    'comunas' => $n['despacho']['comunas'],
    'cierre' => 'Muestro la receta al retirar o al recibir, para que la revise el químico farmacéutico.',
    'nota' => demo_nota($rubro['rubro_nombre'], $version, 'Reserva'),
];
[$errores, $previo] = fa_receta_atender($opcReceta, ['cajas' => '1', 'tipo' => 'simple', 'entrega' => 'retiro']);
$val = static fn (string $k): string => (string) ($previo[$k] ?? '');
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="em-error" id="err-' . demo_e($k) . '">' . demo_e($errores[$k]) . '</p>' : '';
$inv = static fn (string $k): string => isset($errores[$k]) ? ' aria-invalid="true" aria-describedby="err-' . demo_e($k) . '"' : '';

/** Cruz verde de luces (7 × 7, brazos de 3): cada luz con su retardo según la distancia al centro. */
$cruz = static function (string $clase = ''): string {
    $html = '<span class="em-cruz ' . demo_e($clase) . '" aria-hidden="true">';
    for ($f = 0; $f < 7; $f++) {
        for ($c = 0; $c < 7; $c++) {
            $on = ($f >= 2 && $f <= 4) || ($c >= 2 && $c <= 4);
            $html .= $on ? '<i style="--d:' . max(abs($f - 3), abs($c - 3)) . '"></i>' : '<b></b>';
        }
    }
    return $html . '</span>';
};
$mortero = '<svg viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 21h38"/><path d="M8 21c0 9.4 7.2 15.6 16 15.6S40 30.4 40 21"/><path d="M18.5 36.2 17 42h14l-1.5-5.8"/><path d="m28 20 9.5-14" stroke-width="4"/><circle cx="38.6" cy="4.8" r="2.6" fill="currentColor" stroke="none"/></svg>';

demo_head([
    'titulo' => $n['nombre'] . ' · Farmacia en ' . $n['comuna'] . ' (demo)',
    'descripcion' => 'Demo de página web de una sola página para una farmacia de barrio en ' . $n['comuna'] . ', Santiago: remedios sin receta con precio, reserva de recetas por WhatsApp, turnos de noche, horario y despacho en el barrio.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Cabin:wght@400;500;600;700&family=Cutive+Mono&display=swap',
    'css' => ['/demos/farmacia/simple/estilo.css'],
    'tema' => '#123f33',
    'icono' => ['+', '#123f33', '#3ddc6e'],
    'clase_body' => 'em',
]);
demo_aviso($rubro);
?>
<header class="em-top">
  <div class="em-ancho em-top__fila">
    <a class="em-marca" href="#contenido">
      <?= $cruz('em-cruz--chica') ?>
      <span class="em-marca__nombre"><small>Farmacia</small>El Mortero</span>
    </a>
    <nav class="em-nav" aria-label="Secciones">
      <a href="#cajonera">Remedios</a>
      <a href="#receta">Recetas</a>
      <a href="#turnos">Turnos</a>
      <a href="#google">Ubicación</a>
    </nav>
    <a class="em-btn em-btn--chico" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Pedir</span></a>
  </div>
</header>

<main id="contenido">
  <section class="em-hero" aria-labelledby="em-titulo">
    <div class="em-ancho em-hero__grid">
      <div class="em-hero__texto">
        <p class="em-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span class="em-estado__luz" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <h1 id="em-titulo"><span class="em-hero__antes">Farmacia</span> El Mortero</h1>
        <p class="em-hero__eslogan">La farmacia del barrio desde <?= (int) $n['desde'] ?></p>
        <p class="em-hero__bajada">Remedios sin receta, vitaminas, lo de la guagua y todo para el botiquín, con el precio a la vista. Reserva tu receta por WhatsApp y te la tenemos lista en el mesón, o te la llevamos en <?= demo_e($n['comuna']) ?>.</p>
        <div class="em-acciones">
          <a class="em-btn" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir por WhatsApp</span></a>
          <a class="em-btn em-btn--borde" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
        </div>
      </div>
      <figure class="em-vitrina">
        <?= demo_img($n['fotos']['hero'], 'Mueble antiguo de farmacia, de madera, con frascos de vidrio en las repisas y cajoneras abajo', ['lazy' => false, 'ancho' => 900, 'alto' => 1000, 'anchos' => [480, 900, 1300], 'sizes' => '(max-width: 820px) 92vw, 460px']) ?>
        <figcaption class="em-vitrina__letras" aria-hidden="true"><span>Recetas</span><span class="em-vitrina__cruz">✚</span><span>Desde <?= (int) $n['desde'] ?></span></figcaption>
      </figure>
    </div>
    <ul class="em-vidriera" aria-label="Lo que encuentras en la farmacia">
      <?php foreach ($n['vidriera'] as $item): ?><li><?= demo_e($item) ?></li><?php endforeach; ?>
    </ul>
  </section>

  <section class="em-seccion em-cajonera" id="cajonera" aria-labelledby="cajonera-titulo">
    <div class="em-ancho">
      <div class="em-cabeza">
        <p class="em-antetitulo">Sin receta, con precio</p>
        <h2 id="cajonera-titulo">La cajonera</h2>
        <p>Lo que más se pide en el mesón, cajón por cajón. Toca «Pedir» y se abre WhatsApp con el producto escrito; lo retiras o te lo llevamos.</p>
      </div>
      <div class="em-cajones">
        <?php foreach ($n['cajones'] as $slug => [$num, $nombre, $desc, $productos]): ?>
        <article class="em-cajon" id="cajon-<?= demo_e($slug) ?>" aria-labelledby="cajon-<?= demo_e($slug) ?>-titulo">
          <div class="em-cajon__frente">
            <span class="em-cajon__num" aria-hidden="true"><?= demo_e($num) ?></span>
            <h3 class="em-cajon__etiqueta" id="cajon-<?= demo_e($slug) ?>-titulo"><?= demo_e($nombre) ?></h3>
            <span class="em-cajon__tirador" aria-hidden="true"></span>
          </div>
          <div class="em-cajon__papel">
            <p class="em-cajon__desc"><?= demo_e($desc) ?></p>
            <ul class="em-lista">
              <?php foreach ($productos as [$id, $prod, $detalle, $precio, $medicamento]): ?>
              <li class="em-item" id="p-<?= demo_e($id) ?>">
                <span class="em-item__texto">
                  <strong><?= demo_e($prod) ?></strong>
                  <small><?= demo_e($detalle) ?><?= $medicamento ? ' · <span class="em-sello">Venta directa</span>' : '' ?></small>
                </span>
                <span class="em-item__precio"><?= demo_e(demo_clp($precio)) ?></span>
                <a class="em-item__pedir" href="<?= demo_e($wa('Hola, quiero pedir: ' . $prod . ', ' . $detalle . ' (' . demo_clp($precio) . ').')) ?>" target="_blank" rel="noopener" aria-label="Pedir <?= demo_e($prod) ?> por WhatsApp, <?= demo_e(demo_clp($precio)) ?>">Pedir</a>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <p class="em-advertencia"><?= $mortero ?><span><strong>Consulta a tu químico farmacéutico.</strong> Lee las instrucciones antes de usar un medicamento. Los precios son de referencia y se confirman por WhatsApp.</span></p>
    </div>
  </section>

  <section class="em-seccion em-receta-seccion" id="receta" aria-labelledby="receta-titulo">
    <div class="em-ancho em-receta-grid">
      <div class="em-receta-intro">
        <p class="em-antetitulo">Medicamentos con receta</p>
        <h2 id="receta-titulo">Reserva tu receta</h2>
        <p>Los medicamentos con receta no se piden en la página: nos escribes, revisamos si los tenemos y te los dejamos apartados con su precio.</p>
        <ol class="em-pasos">
          <li><strong>Nos cuentas qué necesitas</strong><span>El nombre y la dosis, tal como aparecen en tu receta.</span></li>
          <li><strong>Te confirmamos stock y precio</strong><span>Por WhatsApp, normalmente en menos de una hora.</span></li>
          <li><strong>Retiras o te lo llevamos</strong><span>La receta se presenta al retirar o al recibir y la revisa el químico farmacéutico. Las recetas retenidas quedan en la farmacia, como pide la ley.</span></li>
        </ol>
        <p class="em-nota-receta">No envíes fotos de tu receta por la página: tráela o muéstrala al recibir.</p>
      </div>

      <form class="em-recetario" method="post" action="#receta" novalidate>
        <p class="em-recetario__cabeza"><span class="em-recetario__rp" aria-hidden="true">Rp.</span><span><strong><?= demo_e($n['nombre']) ?></strong><small>Reserva de medicamento con receta</small></span></p>
        <?php if ($errores): ?><p class="em-error em-error--caja" role="alert">Revisa los campos marcados.</p><?php endif; ?>
        <div class="em-campo">
          <label for="medicamento">Medicamento, como dice tu receta</label>
          <input id="medicamento" name="medicamento" maxlength="120" value="<?= demo_e($val('medicamento')) ?>" placeholder="Nombre, dosis y presentación" autocomplete="off"<?= $inv('medicamento') ?>>
          <?= $err('medicamento') ?>
        </div>
        <div class="em-campos-dos">
          <div class="em-campo">
            <label for="cajas">Cajas</label>
            <select id="cajas" name="cajas"<?= $inv('cajas') ?>>
              <?php for ($i = 1; $i <= 6; $i++): ?><option value="<?= $i ?>"<?= $val('cajas') === (string) $i ? ' selected' : '' ?>><?= $i ?></option><?php endfor; ?>
            </select>
            <?= $err('cajas') ?>
          </div>
          <div class="em-campo">
            <label for="tipo">Tipo de receta</label>
            <select id="tipo" name="tipo"<?= $inv('tipo') ?>>
              <?php foreach (FA_TIPOS_RECETA as $clave => $texto): ?><option value="<?= demo_e($clave) ?>"<?= $val('tipo') === $clave ? ' selected' : '' ?>><?= demo_e($texto) ?></option><?php endforeach; ?>
            </select>
            <?= $err('tipo') ?>
          </div>
        </div>
        <fieldset class="em-campo em-opciones"<?= isset($errores['entrega']) ? ' aria-describedby="err-entrega"' : '' ?>>
          <legend>¿Cómo lo recibes?</legend>
          <?php foreach ($opcReceta['entregas'] as $clave => $texto): ?>
          <label class="em-opcion"><input type="radio" name="entrega" value="<?= demo_e($clave) ?>"<?= $val('entrega') === $clave ? ' checked' : '' ?>> <span><?= demo_e($texto) ?></span></label>
          <?php endforeach; ?>
          <?= $err('entrega') ?>
        </fieldset>
        <div class="em-campos-dos">
          <div class="em-campo">
            <label for="comuna">Comuna <small>(si es despacho)</small></label>
            <select id="comuna" name="comuna"<?= $inv('comuna') ?>>
              <option value="">Elige tu comuna</option>
              <?php foreach ($n['despacho']['comunas'] as $c): ?><option value="<?= demo_e($c) ?>"<?= $val('comuna') === $c ? ' selected' : '' ?>><?= demo_e($c) ?></option><?php endforeach; ?>
            </select>
            <?= $err('comuna') ?>
          </div>
          <div class="em-campo">
            <label for="nombre">Tu nombre</label>
            <input id="nombre" name="nombre" maxlength="40" value="<?= demo_e($val('nombre')) ?>" autocomplete="off"<?= $inv('nombre') ?>>
            <?= $err('nombre') ?>
          </div>
        </div>
        <div class="em-campo">
          <label for="nota">Nota <small>(opcional)</small></label>
          <input id="nota" name="nota" maxlength="200" value="<?= demo_e($val('nota')) ?>" placeholder="Ej.: paso a buscarlo después de las 18:00" autocomplete="off">
        </div>
        <button class="em-btn em-btn--bloque" type="submit"><?= demo_icono_wa() ?><span>Reservar por WhatsApp</span></button>
        <p class="em-recetario__pie">Demo: no guardamos tus datos. El mensaje se arma aquí y se abre en WhatsApp.</p>
        <span class="em-timbre" aria-hidden="true">Revisa<br>Q.F.</span>
      </form>
    </div>
  </section>

  <section class="em-seccion em-meson" aria-labelledby="meson-titulo">
    <div class="em-ancho em-meson__grid">
      <figure class="em-retrato">
        <?= demo_img($n['fotos']['qf'], 'Químico farmacéutico de delantal blanco buscando un medicamento en la estantería', ['ancho' => 700, 'alto' => 820, 'anchos' => [400, 700, 1000], 'sizes' => '(max-width: 820px) 92vw, 400px']) ?>
        <?= demo_img($n['fotos']['atencion'], 'Atención en el mesón: un químico farmacéutico le muestra un frasco a una clienta mayor', ['ancho' => 420, 'alto' => 420, 'anchos' => [300, 420, 700], 'sizes' => '(max-width: 820px) 44vw, 190px', 'clase' => 'em-retrato__inset']) ?>
      </figure>
      <div>
        <p class="em-antetitulo">Atiende el químico farmacéutico</p>
        <h2 id="meson-titulo">Detrás del mesón</h2>
        <div class="em-credencial">
          <p class="em-credencial__nombre">Q.F. <?= demo_e($n['qf']) ?></p>
          <p class="em-credencial__cargo">Químico farmacéutico director técnico</p>
          <p>Hijo de don Aníbal, que abrió la farmacia en <?= (int) $n['desde'] ?>. Está en el mesón de lunes a sábado: pregúntale cómo guardar tus medicamentos, cómo leer tu receta o qué genéricos hay.</p>
        </div>
        <ul class="em-servicios">
          <?php foreach ($n['servicios'] as [$titulo, $texto]): ?>
          <li><strong><?= demo_e($titulo) ?></strong><span><?= demo_e($texto) ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </section>

  <section class="em-seccion em-turnos" id="turnos" aria-labelledby="turnos-titulo">
    <div class="em-ancho em-turnos__grid">
      <div>
        <p class="em-antetitulo em-antetitulo--claro">Horario y turnos</p>
        <h2 id="turnos-titulo">Abiertos todos los días</h2>
        <p class="em-turnos__estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span class="em-estado__luz" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario em-horario'); ?>
      </div>
      <div class="em-panel-turno">
        <?= $cruz('em-cruz--grande') ?>
        <p class="em-panel-turno__titulo"><?= $turno['activo'] ? 'De turno esta semana' : 'Turno de noche' ?></p>
        <p class="em-panel-turno__texto"><?= demo_e($turnoTexto) ?></p>
        <p class="em-panel-turno__chico">En turno atendemos de <?= demo_e($n['turno']['desde']) ?> a <?= demo_e($n['turno']['hasta']) ?> por la ventanilla: toca el timbre. Los turnos los coordina la Seremi de Salud y rotan entre las farmacias de la comuna.</p>
      </div>
    </div>
  </section>

  <div class="em-seccion em-seccion--google">
    <div class="em-ancho">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería la ficha de tu farmacia en Google Maps: horario, cómo llegar y reseñas de los vecinos.']); ?>
    </div>
  </div>

  <section class="em-cierre" aria-labelledby="cierre-titulo">
    <div class="em-ancho">
      <?= $cruz('em-cruz--cierre') ?>
      <h2 id="cierre-titulo">¿Te falta algo del botiquín?</h2>
      <p>Escríbenos: te confirmamos el precio y lo dejamos listo en el mesón.</p>
      <div class="em-acciones em-acciones--centro">
        <a class="em-btn em-btn--oro" href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>Pedir por WhatsApp</span></a>
        <a class="em-btn em-btn--borde-claro" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
      </div>
    </div>
  </section>
</main>

<footer class="em-pie">
  <div class="em-ancho">
    <p class="em-pie__marca"><?= demo_e($n['nombre']) ?></p>
    <p><?= demo_e($n['comuna']) ?>, Santiago · Desde <?= (int) $n['desde'] ?> · Químico farmacéutico director técnico: Q.F. <?= demo_e($n['qf']) ?></p>
    <p class="em-pie__legal">Consulta a tu químico farmacéutico. Lee las instrucciones antes de usar un medicamento. Los medicamentos con receta se entregan solo con la receta a la vista.</p>
    <?php demo_aviso_pie($rubro); ?>
  </div>
</footer>

<nav class="em-barra" aria-label="Pedir">
  <a href="<?= demo_e($waGeneral) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?><span>WhatsApp</span></a>
  <a href="#receta"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h9l3 3v15H6z"/><path d="M9 9h6M9 13h6M9 17h4"/></svg><span>Receta</span></a>
  <a href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel() ?><span>Llamar</span></a>
</nav>
<?php demo_scripts(); ?>
</body>
</html>
