<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = cp();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';

/** Texto en el que se busca cada servicio. */
$textoDe = static fn (array $s): string => $s['nombre'] . ' ' . $s['desc'] . ' ' . $n['categorias'][$s['cat']][0] . ' ' . implode(' ', array_keys($s['variantes']));

// Sin JavaScript el buscador funciona con ?q=: el servidor oculta lo que no coincide (demo_coincide, mismo criterio que buscador.js).
$coincide = static fn (array $s): bool => demo_coincide($textoDe($s), $q);
$hayResultados = count(array_filter($n['servicios'], $coincide)) > 0;

cp_abrir('especialidades', 'Especialidades y precios', 'Especialidades de ' . $n['nombre'] . ' con precio desde según especie o tamaño: medicina general, vacunas, felinos, dermatología, cardiología, imagenología, odontología, cirugía y exóticos. Demo de clínica veterinaria en ' . $n['comuna'] . '.');
?>
<main id="contenido" data-buscador-raiz>
  <?php cp_titular('Especialidades y precios', 'Elige qué necesita tu mascota', 'Precios «desde» según la especie o el tamaño. Agrega los servicios a tu reserva y, en el último paso, cuéntanos de tu mascota y elige el día.'); ?>

  <div class="cp-ancho cp-especialidades">
    <div class="cp-herramientas">
      <form class="cp-buscar" role="search" action="<?= demo_e(CP_BASE . 'especialidades.php') ?>" method="get">
        <label for="buscar-servicio">Buscar un servicio</label>
        <div class="cp-buscar__fila">
          <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          <input type="search" id="buscar-servicio" name="q" value="<?= demo_e($q) ?>" placeholder="Ej.: vacuna, gato, piel, conejo" maxlength="40" data-buscador autocomplete="off">
          <button type="submit">Buscar</button>
        </div>
      </form>
      <nav class="cp-categorias" aria-label="Especialidades">
        <?php foreach ($n['categorias'] as $clave => [$nombre]): ?>
        <a href="#cat-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></a>
        <?php endforeach; ?>
      </nav>
    </div>

    <p class="cp-sin-resultados" data-sin-resultados<?= $hayResultados ? ' hidden' : '' ?>>No encontramos servicios con esa búsqueda. Prueba con otra palabra o <a href="<?= demo_e(cp_wa('Hola, busco un servicio que no encontré en la web: ')) ?>" target="_blank" rel="noopener">pregúntanos por WhatsApp</a>.</p>

    <?php $i = 0; foreach ($n['categorias'] as $clave => [$nombre, $bajada]): $i++; ?>
    <?php
    $servicios = array_filter($n['servicios'], static fn (array $s): bool => $s['cat'] === $clave);
    $visibles = array_filter($servicios, $coincide);
    ?>
    <section class="cp-categoria" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $visibles ? '' : ' hidden' ?>>
      <header class="cp-categoria__cabeza">
        <span class="cp-categoria__num" aria-hidden="true"><?= sprintf('%02d', $i) ?></span>
        <div>
          <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
          <p><?= demo_e($bajada) ?></p>
        </div>
      </header>
      <ul class="cp-lista">
        <?php foreach ($servicios as $id => $s): ?>
        <li class="cp-servicio" id="s-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($textoDe($s)) ?>"<?= $coincide($s) ? '' : ' hidden' ?>>
          <div class="cp-servicio__foto"><?= demo_img($s['foto'], $s['alt'], ['ancho' => 480, 'alto' => 360, 'anchos' => [320, 480, 800], 'sizes' => '(max-width: 700px) 92vw, 220px']) ?></div>
          <div class="cp-servicio__texto">
            <p class="cp-meta"><svg viewBox="0 0 24 24" width="15" height="15" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2" stroke-linecap="round"/></svg> <?= demo_e($s['duracion']) ?></p>
            <h3><?= demo_e($s['nombre']) ?></h3>
            <p><?= demo_e($s['desc']) ?></p>
          </div>
          <div class="cp-servicio__accion"><?= cp_agregar($id, $s) ?></div>
        </li>
        <?php endforeach; ?>
      </ul>
    </section>
    <?php endforeach; ?>

    <p class="cp-aviso-medico">Los medicamentos y sus dosis los indica el médico después de evaluar a tu mascota. No recetamos por WhatsApp.</p>

    <aside class="cp-ir-reserva" aria-label="Tu reserva">
      <p><strong>¿Listo?</strong> Cuéntanos de tu mascota y elige el día y el bloque.</p>
      <a class="cp-btn cp-btn--lavanda" href="<?= demo_e(CP_BASE . 'reserva.php') ?>">Ir a mi reserva <span class="cp-reserva-btn__num" data-carrito-cantidad hidden>0</span></a>
    </aside>
  </div>
</main>
<?php cp_cerrar(['buscador']);
