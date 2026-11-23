<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = br();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';

/** Texto en el que se busca cada servicio. */
$textoDe = static fn (array $s): string => $s['nombre'] . ' ' . $s['desc'] . ' ' . $n['categorias'][$s['cat']][0] . ' ' . implode(' ', array_keys($s['variantes']));

// Sin JavaScript el buscador funciona con ?q=: el servidor oculta lo que no coincide (demo_coincide, mismo criterio que buscador.js).
$coincide = static fn (array $s): bool => demo_coincide($textoDe($s), $q);
$hayResultados = count(array_filter($n['servicios'], $coincide)) > 0;
$romanos = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII'];

br_abrir('rituales', 'Rituales y precios', 'Carta de rituales de ' . $n['nombre'] . ' con precio según el tamaño del perro: baños con hidromasaje, corte de raza a tijera, deslanado, desmotado, uñas, oídos, gatos y botica. Demo de spa canino en ' . $n['comuna'] . '.');
?>
<main id="contenido" data-buscador-raiz>
  <?php br_titular('02', 'Rituales y precios', 'La <em>carta</em>', 'Precios según el tamaño de tu perro, con todo incluido. Agrega lo que quieras a tu reserva y, en el último paso, cuéntanos cómo es y elige el día.'); ?>

  <div class="br-ancho br-carta">
    <div class="br-herramientas">
      <form class="br-buscar" role="search" action="<?= demo_e(BR_BASE . 'rituales.php') ?>" method="get">
        <label for="buscar-ritual">Buscar en la carta</label>
        <div class="br-buscar__fila">
          <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
          <input type="search" id="buscar-ritual" name="q" value="<?= demo_e($q) ?>" placeholder="Ej.: tijera, gato, deslanado, uñas" maxlength="40" data-buscador autocomplete="off">
          <button type="submit">Buscar</button>
        </div>
      </form>
      <nav class="br-categorias" aria-label="Secciones de la carta">
        <?php foreach ($n['categorias'] as $clave => [$nombre]): ?>
        <a href="#cat-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></a>
        <?php endforeach; ?>
      </nav>
    </div>

    <p class="br-sin-resultados" data-sin-resultados<?= $hayResultados ? ' hidden' : '' ?>>No encontramos nada con esa búsqueda. Prueba con otra palabra o <a href="<?= demo_e(br_wa('Hola, busco un servicio que no encontré en la carta: ')) ?>" target="_blank" rel="noopener">pregúntanos por WhatsApp</a>.</p>

    <?php $i = 0; foreach ($n['categorias'] as $clave => [$nombre, $bajada]): ?>
    <?php
    $servicios = array_filter($n['servicios'], static fn (array $s): bool => $s['cat'] === $clave);
    $visibles = array_filter($servicios, $coincide);
    ?>
    <section class="br-categoria" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $visibles ? '' : ' hidden' ?>>
      <header class="br-categoria__cabeza">
        <span class="br-categoria__num" aria-hidden="true"><?= demo_e($romanos[$i++] ?? '') ?></span>
        <div>
          <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
          <p><?= demo_e($bajada) ?></p>
        </div>
      </header>
      <ul class="br-lista">
        <?php foreach ($servicios as $id => $s): ?>
        <li class="br-servicio" id="s-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($textoDe($s)) ?>"<?= $coincide($s) ? '' : ' hidden' ?>>
          <figure class="br-ovalo br-servicio__foto"><?= demo_img($s['foto'], $s['alt'], ['ancho' => 360, 'alto' => 450, 'anchos' => [240, 360, 600], 'sizes' => '(max-width: 700px) 30vw, 150px']) ?></figure>
          <div class="br-servicio__texto">
            <p class="br-meta"><?= demo_e($s['duracion']) ?></p>
            <h3><?= demo_e($s['nombre']) ?></h3>
            <p><?= demo_e($s['desc']) ?></p>
            <?php if (count($s['variantes']) > 1): ?>
            <p class="br-servicio__precios"><?php $partes = []; foreach ($s['variantes'] as $v => $p) { $partes[] = '<span>' . demo_e($v) . ' <b>' . demo_e(demo_clp($p)) . '</b></span>'; } echo implode('', $partes); ?></p>
            <?php endif; ?>
          </div>
          <div class="br-servicio__accion"><?= br_agregar($id, $s) ?></div>
        </li>
        <?php endforeach; ?>
      </ul>
    </section>
    <?php endforeach; ?>

    <p class="br-aviso">No somos una clínica veterinaria: si tu perro tiene heridas, hongos u otitis, primero debe verlo su veterinario. Usamos productos suaves para pieles sensibles.</p>

    <aside class="br-ir-reserva" aria-label="Tu reserva">
      <p><strong>¿Listo?</strong> Cuéntanos de tu perro y elige el día y la hora.</p>
      <a class="br-btn br-btn--durazno" href="<?= demo_e(BR_BASE . 'reserva.php') ?>">Ir a mi reserva <span class="br-reserva-btn__num" data-carrito-cantidad hidden>0</span></a>
    </aside>
  </div>
</main>
<?php br_cerrar(['buscador']);
