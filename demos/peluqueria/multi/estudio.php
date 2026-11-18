<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = mz();

mz_abrir('estudio', 'El estudio y las coloristas', 'Conoce ' . $n['nombre_largo'] . ', salón de autor de color y balayage en ' . $n['comuna'] . ': las coloristas, su especialidad y cómo trabajan. Demo de sitio web para peluquerías.');
?>
<main id="contenido">
  <?php mz_titular('02', 'El estudio', 'Cuatro sillones, <em>una obsesión</em>', 'Abrimos en ' . $n['desde'] . ' en una casa antigua de ' . $n['comuna'] . ' con una idea simple: hacer solo color, y hacerlo muy bien.'); ?>

  <section class="mz-seccion mz-seccion--sin-arriba" aria-labelledby="historia-titulo">
    <div class="mz-ancho mz-historia">
      <figure class="mz-historia__foto">
        <?= demo_img($n['fotos']['estudio'], 'Interior del estudio en blanco y negro, con sillones y espejos con forma de arco', ['lazy' => false, 'ancho' => 900, 'alto' => 1100, 'sizes' => '(max-width: 860px) 92vw, 46vw']) ?>
        <figcaption>El salón principal: luz norte toda la tarde, ideal para ver el color real.</figcaption>
      </figure>
      <div class="mz-historia__texto">
        <h2 id="historia-titulo" class="demo-sr">Nuestra historia</h2>
        <p class="mz-capitular">Antonia trabajó diez años en salones grandes, donde el balayage se hacía con apuro entre un brushing y otro. Matiz nació para lo contrario: citas largas, una colorista por clienta y tiempo para conversar antes de abrir el primer pote.</p>
        <p>Hoy somos cuatro. Cada una tiene su especialidad, pero todas trabajamos con el mismo método: diagnóstico con luz natural, prueba de mechón cuando hace falta y tintes sin amoníaco. Lo que no nos convence, no lo hacemos, aunque esté de moda.</p>
        <dl class="mz-cifras">
          <div><dt>Clientas al día</dt><dd>12</dd></div>
          <div><dt>Coloristas</dt><dd>4</dd></div>
          <div><dt>Prueba de mechón</dt><dd>Gratis</dd></div>
        </dl>
      </div>
    </div>
  </section>

  <section class="mz-seccion mz-seccion--papel" aria-labelledby="equipo-titulo">
    <div class="mz-ancho">
      <header class="mz-encabezado mz-encabezado--fila">
        <div>
          <p class="mz-sobre">Quién te atiende</p>
          <h2 id="equipo-titulo">Las <em>coloristas</em></h2>
        </div>
        <p>Al reservar eliges con quién. Si no sabes, marca «La primera disponible» y te asignamos según tu servicio.</p>
      </header>
      <div class="mz-equipo">
        <figure class="mz-equipo__foto">
          <?= demo_img($n['fotos']['antonia'], 'Antonia, con tatuajes en los brazos, peina un recogido a una clienta', ['ancho' => 700, 'alto' => 900, 'sizes' => '(max-width: 860px) 92vw, 38vw']) ?>
          <figcaption>Antonia, en una tarde de novias.</figcaption>
        </figure>
        <ol class="mz-coloristas">
          <?php foreach ($n['coloristas'] as $id => $c): ?>
          <li>
            <p class="mz-coloristas__rol"><?= demo_e($c['rol']) ?> · <?= (int) $c['anios'] ?> años de oficio</p>
            <h3><?= demo_e($c['nombre']) ?></h3>
            <p><?= demo_e($c['fuerte']) ?>.</p>
            <a class="mz-enlace" href="<?= demo_e(mz_wa('Hola, quiero una hora con ' . $c['nombre'] . ' en ' . $n['nombre_largo'] . '.')) ?>" target="_blank" rel="noopener">Pedir hora con <?= demo_e($c['corto']) ?></a>
          </li>
          <?php endforeach; ?>
        </ol>
      </div>
    </div>
  </section>

  <section class="mz-seccion" aria-labelledby="valores-titulo">
    <div class="mz-ancho mz-valores">
      <h2 id="valores-titulo">Lo que <em>sí</em> y lo que <em>no</em></h2>
      <ul>
        <li><strong>Sí:</strong> tintes sin amoníaco y protector de enlaces en todas las decoloraciones.</li>
        <li><strong>Sí:</strong> decirte que tu rubio soñado toma dos sesiones, si es así.</li>
        <li><strong>Sí:</strong> café de grano, wifi y una manta para los procesos largos.</li>
        <li><strong>No:</strong> cambiar el precio a mitad del proceso sin preguntarte.</li>
        <li><strong>No:</strong> secarte con apuro para que entre la siguiente.</li>
      </ul>
      <figure class="mz-valores__foto">
        <?= demo_img($n['fotos']['lavado'], 'Fila de lavacabezas negros en blanco y negro', ['ancho' => 700, 'alto' => 500, 'sizes' => '(max-width: 860px) 92vw, 40vw']) ?>
      </figure>
    </div>
  </section>

  <section class="mz-seccion mz-cierre" aria-labelledby="cierre-titulo">
    <div class="mz-ancho mz-cierre__caja">
      <h2 id="cierre-titulo">¿Te sentamos <em>esta semana?</em></h2>
      <p>Elige tus servicios y la colorista. Te confirmamos por WhatsApp.</p>
      <div class="mz-acciones"><a class="mz-btn" href="<?= demo_e(MZ_BASE . 'servicios.php') ?>">Ver servicios y reservar</a></div>
    </div>
  </section>
</main>
<?php mz_cerrar();
