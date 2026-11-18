<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = cp();
$estado = demo_horario_estado($n['horario']);
$dias = cp_dias(1);
$proxima = $dias[0] ?? '';
$primeraFoto = [];
foreach ($n['servicios'] as $s) {
    $primeraFoto[$s['cat']] ??= $s;
}
$desdeCat = [];
foreach ($n['servicios'] as $s) {
    $desdeCat[$s['cat']] = min($desdeCat[$s['cat']] ?? PHP_INT_MAX, cp_desde($s));
}

cp_abrir('inicio', 'Clínica veterinaria en ' . $n['comuna'], 'Demo de sitio de varias páginas para una clínica veterinaria con especialidades en ' . $n['comuna'] . ', Santiago: medicina felina, dermatología, cardiología, imagenología, cirugía y exóticos, con reserva de hora por WhatsApp.');
?>
<main id="contenido">
  <section class="cp-hero" aria-labelledby="hero-titulo">
    <div class="cp-ancho cp-hero__grid">
      <div class="cp-hero__texto">
        <p class="cp-chip<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <h1 id="hero-titulo">Ocho especialidades, <em>una sola ficha</em> para tu mascota.</h1>
        <p class="cp-hero__bajada">Medicina general, felinos, dermatología, cardiología, imagenología, odontología, cirugía y exóticos en <?= demo_e($n['comuna']) ?>. Reserva en línea y te confirmamos por WhatsApp.</p>
        <div class="cp-acciones">
          <a class="cp-btn" href="<?= demo_e(CP_BASE . 'especialidades.php') ?>">Reservar una hora</a>
          <a class="cp-btn cp-btn--linea" href="<?= demo_e(CP_BASE . 'equipo.php') ?>">Conocer al equipo</a>
        </div>
      </div>
      <div class="cp-hero__fotos">
        <figure class="cp-capsula cp-capsula--grande">
          <?= demo_img($n['fotos']['hero'], 'Veterinaria con delantal blanco abrazando a un perrito chihuahua que la mira', ['lazy' => false, 'ancho' => 700, 'alto' => 1000, 'sizes' => '(max-width: 900px) 60vw, 330px', 'anchos' => [400, 700, 1000]]) ?>
        </figure>
        <figure class="cp-capsula cp-capsula--chica">
          <?= demo_img($n['fotos']['lobby'], 'Recepción blanca y luminosa de la clínica con un árbol en el centro', ['ancho' => 500, 'alto' => 700, 'sizes' => '(max-width: 900px) 40vw, 220px', 'anchos' => [300, 500, 800]]) ?>
        </figure>
        <?php if ($proxima !== ''): ?>
        <p class="cp-proxima"><span>Próxima hora disponible</span><strong><?= demo_e($proxima) ?></strong><small><?= demo_e($n['bloques'][0]) ?> · medicina general</small></p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="cp-cifras" aria-label="La clínica en números">
    <dl class="cp-ancho">
      <div><dt>Especialidades</dt><dd>8</dd></div>
      <div><dt>Médicos veterinarios</dt><dd>11</dd></div>
      <div><dt>Resultados de sangre</dt><dd>en el día</dd></div>
      <div><dt>Urgencias sin hora</dt><dd>7 días</dd></div>
    </dl>
  </section>

  <section class="cp-seccion" aria-labelledby="indice-titulo">
    <div class="cp-ancho">
      <header class="cp-encabezado cp-encabezado--fila">
        <div>
          <p class="cp-sobre">Especialidades</p>
          <h2 id="indice-titulo">¿Qué necesita tu mascota?</h2>
        </div>
        <p>Cada especialidad con su médico, su box y sus precios a la vista. Agrega lo que necesitas a tu reserva y elige el día.</p>
      </header>
      <ol class="cp-indice">
        <?php $i = 0; foreach ($n['categorias'] as $clave => [$nombre, $bajada]): $i++; $s = $primeraFoto[$clave] ?? null; ?>
        <li>
          <a class="cp-indice__fila" href="<?= demo_e(CP_BASE . 'especialidades.php#cat-' . $clave) ?>">
            <span class="cp-indice__num" aria-hidden="true"><?= sprintf('%02d', $i) ?></span>
            <span class="cp-indice__texto"><strong><?= demo_e($nombre) ?></strong><span><?= demo_e($bajada) ?></span></span>
            <span class="cp-indice__precio"><small>desde</small> <?= demo_e(demo_clp($desdeCat[$clave] ?? 0)) ?></span>
            <?php if ($s): ?><span class="cp-indice__foto" aria-hidden="true"><?= demo_img($s['foto'], '', ['ancho' => 240, 'alto' => 240, 'anchos' => [160, 240, 400], 'sizes' => '96px']) ?></span><?php endif; ?>
          </a>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section class="cp-seccion cp-seccion--niebla" aria-labelledby="ficha-titulo">
    <div class="cp-ancho cp-ficha">
      <figure class="cp-ficha__foto">
        <?= demo_img($n['fotos']['ficha'], 'Médico con delantal anotando en la ficha clínica sobre un portapapeles', ['ancho' => 800, 'alto' => 640, 'sizes' => '(max-width: 900px) 92vw, 46vw']) ?>
        <figcaption><span class="cp-sobre">Ficha compartida</span>Dermatología ve lo que anotó cardiología.</figcaption>
      </figure>
      <div>
        <p class="cp-sobre">Cómo trabajamos</p>
        <h2 id="ficha-titulo">Tu mascota no empieza de cero en cada box.</h2>
        <ol class="cp-pasos">
          <li><span>1</span><div><h3>Reservas con sus datos</h3><p>Nombre, especie y edad llegan antes que ustedes. Si es la primera vez, abrimos su ficha en cinco minutos.</p></div></li>
          <li><span>2</span><div><h3>Consulta sin apuro</h3><p>Te explicamos lo que vemos y lo que proponemos, con precio, antes de hacer cualquier examen.</p></div></li>
          <li><span>3</span><div><h3>Todo por escrito</h3><p>Indicaciones, resultados y próximos controles quedan en tu correo y en su ficha, que ve todo el equipo.</p></div></li>
        </ol>
      </div>
    </div>
  </section>

  <?php cp_urgencias(); ?>

  <section class="cp-seccion" aria-labelledby="equipo-titulo">
    <div class="cp-ancho">
      <header class="cp-encabezado cp-encabezado--fila">
        <div>
          <p class="cp-sobre">Equipo médico</p>
          <h2 id="equipo-titulo">Médicos con especialidad, y con nombre</h2>
        </div>
        <p><a class="cp-enlace" href="<?= demo_e(CP_BASE . 'equipo.php') ?>">Ver todo el equipo y la clínica</a></p>
      </header>
      <ul class="cp-equipo cp-equipo--tres">
        <?php foreach (array_slice($n['equipo'], 1, 3) as $m): ?>
        <li class="cp-medico">
          <figure class="cp-medico__foto"><?= demo_img($m['foto'], $m['alt'], ['ancho' => 500, 'alto' => 640, 'anchos' => [320, 500, 800], 'sizes' => '(max-width: 700px) 80vw, 30vw']) ?></figure>
          <h3><?= demo_e($m['nombre']) ?></h3>
          <p class="cp-medico__cargo"><?= demo_e($m['cargo']) ?></p>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <div class="cp-seccion cp-seccion--niebla">
    <div class="cp-ancho">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería la ficha de la clínica en Google Maps: horario, cómo llegar y lo que opinan los pacientes.']); ?>
    </div>
  </div>

  <section class="cp-cierre" aria-labelledby="cierre-titulo">
    <div class="cp-ancho cp-cierre__grid">
      <div>
        <h2 id="cierre-titulo">¿Le toca su control anual?</h2>
        <p>Vacunas, chequeo de mayores o una segunda opinión: elige la especialidad y el día, y te confirmamos por WhatsApp.</p>
        <div class="cp-acciones">
          <a class="cp-btn cp-btn--lavanda" href="<?= demo_e(CP_BASE . 'especialidades.php') ?>">Reservar una hora</a>
          <a class="cp-btn cp-btn--claro" href="<?= demo_e(cp_wa('Hola, tengo una consulta para ' . $n['nombre'] . ': ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> WhatsApp</a>
        </div>
      </div>
      <figure class="cp-capsula cp-capsula--horizontal">
        <?= demo_img($n['fotos']['cierre'], 'Perro blanco y un gatito naranjo mirándose de cerca', ['ancho' => 900, 'alto' => 560, 'sizes' => '(max-width: 900px) 92vw, 46vw']) ?>
      </figure>
    </div>
  </section>
</main>
<?php cp_cerrar();
