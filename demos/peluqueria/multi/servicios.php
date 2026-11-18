<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = mz();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
$qn = demo_normalizar($q);

/** Texto en el que se busca cada servicio. */
$textoDe = static fn (array $s): string => $s['nombre'] . ' ' . $s['desc'] . ' ' . $n['categorias'][$s['cat']][0];

// Sin JavaScript el buscador funciona con ?q=: el servidor oculta lo que no coincide.
$coincide = static fn (array $s): bool => $qn === '' || str_contains(demo_normalizar($textoDe($s)), $qn);
$hayResultados = count(array_filter($n['servicios'], $coincide)) > 0;

mz_abrir('servicios', 'Servicios y precios', 'Servicios de ' . $n['nombre_largo'] . ' con duración y precio desde según el largo del pelo: balayage, babylights, platinado, cobrizos, transición a canas, corte y tratamientos. Demo de salón en ' . $n['comuna'] . '.');
?>
<main id="contenido" data-buscador-raiz>
  <?php mz_titular('01', 'Servicios y precios', 'La carta <em>del estudio</em>', 'Precios «desde» según el largo de tu pelo, con lavado y secado incluidos. Agrega lo que quieras a tu reserva; el tiempo total lo ves en el último paso.'); ?>

  <div class="mz-ancho mz-servicios">
    <div class="mz-herramientas">
      <form class="mz-buscar" role="search" action="<?= demo_e(MZ_BASE . 'servicios.php') ?>" method="get">
        <label for="buscar-servicio">Buscar en la carta</label>
        <div class="mz-buscar__fila">
          <input type="search" id="buscar-servicio" name="q" value="<?= demo_e($q) ?>" placeholder="Ej.: balayage, canas, rizos, shampoo" maxlength="40" data-buscador autocomplete="off">
          <button type="submit">Buscar</button>
        </div>
      </form>
      <nav class="mz-categorias" aria-label="Categorías de servicios">
        <?php foreach ($n['categorias'] as $clave => [$nombre]): ?>
        <a href="#cat-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></a>
        <?php endforeach; ?>
      </nav>
    </div>

    <p class="mz-sin-resultados" data-sin-resultados<?= $hayResultados ? ' hidden' : '' ?>>No encontramos nada con esa búsqueda. Prueba con otra palabra o <a href="<?= demo_e(mz_wa('Hola, busco un servicio que no encontré en la web: ')) ?>" target="_blank" rel="noopener">pregúntanos por WhatsApp</a>.</p>

    <?php foreach ($n['categorias'] as $clave => [$nombre, $bajada]): ?>
    <?php
    $servicios = array_filter($n['servicios'], static fn (array $s): bool => $s['cat'] === $clave);
    $visibles = array_filter($servicios, $coincide);
    ?>
    <section class="mz-categoria" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $visibles ? '' : ' hidden' ?>>
      <header class="mz-categoria__cabeza">
        <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
        <p><?= demo_e($bajada) ?></p>
      </header>
      <ul class="mz-lista">
        <?php foreach ($servicios as $id => $s): ?>
        <li class="mz-servicio" id="s-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($textoDe($s)) ?>"<?= $coincide($s) ? '' : ' hidden' ?>>
          <div class="mz-servicio__foto"><?= demo_img($s['foto'], $s['alt'], ['ancho' => 400, 'alto' => 500, 'anchos' => [300, 400, 700], 'sizes' => '(max-width: 700px) 30vw, 160px']) ?></div>
          <div class="mz-servicio__texto">
            <h3><?= demo_e($s['nombre']) ?></h3>
            <p><?= demo_e($s['desc']) ?></p>
            <?php if ($s['min'] > 0): ?><p class="mz-meta"><span class="mz-dur"><?= demo_e(mz_duracion($s['min'])) ?></span></p><?php endif; ?>
          </div>
          <div class="mz-servicio__accion"><?= mz_agregar($id, $s) ?></div>
        </li>
        <?php endforeach; ?>
      </ul>
    </section>
    <?php endforeach; ?>

    <aside class="mz-aviso-reserva" aria-label="Tu reserva">
      <p><strong>¿Listo?</strong> Elige colorista, día y hora en el último paso.</p>
      <a class="mz-btn" href="<?= demo_e(MZ_BASE . 'reserva.php') ?>">Ir a mi reserva <span class="mz-reserva-btn__num" data-carrito-cantidad hidden>0</span></a>
    </aside>
  </div>
</main>
<?php mz_cerrar(['buscador']);
