<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = al();
$numeros = array_flip(array_keys($n['tortas']));
$num = static fn (string $id): string => sprintf('N.º %02d', $numeros[$id] + 1);
$destacadas = ['chocolate-maqui', 'pistacho', 'lucuma-cafe'];

al_abrir('inicio', 'Pastelería de autor en ' . $n['comuna'], 'Demo de sitio de varias páginas para una pastelería de autor en ' . $n['comuna'] . ', Santiago: colección de tortas, encargos por WhatsApp, carta con QR y tortas en 3D.');
?>
<main id="contenido">
  <section class="al-portada">
    <div class="al-ancho al-portada__grid">
      <div class="al-portada__texto">
        <p class="al-sobre">Colección de primavera · 2026</p>
        <h1>Tortas de autor <em>con frutas de Chile</em></h1>
        <p class="al-portada__bajada">Lúcuma, maqui, miel de ulmo y limón de Pica. Pastelería hecha a mano en <?= demo_e($n['comuna']) ?>, por encargo o en la vitrina del atelier.</p>
        <div class="al-acciones">
          <a class="al-btn" href="<?= demo_e(AL_BASE . 'encargo.php') ?>">Encargar una torta</a>
          <a class="al-btn al-btn--linea" href="<?= demo_e(AL_BASE . 'tortas.php') ?>">Ver la colección</a>
        </div>
      </div>
      <figure class="al-portada__foto">
        <?= demo_img($n['fotos']['hero'], 'Torta desnuda de dos pisos decorada con higos frescos, flores y romero', ['lazy' => false, 'ancho' => 900, 'alto' => 1125, 'sizes' => '(max-width: 860px) 92vw, 46vw']) ?>
        <figcaption><span><?= demo_e($num('higos')) ?></span> Higos y miel de ulmo</figcaption>
      </figure>
    </div>
  </section>

  <section class="al-ingredientes" aria-label="Ingredientes de esta temporada">
    <ul class="al-ancho">
      <?php foreach ($n['ingredientes'] as [$nombre]): ?><li><?= demo_e($nombre) ?></li><?php endforeach; ?>
    </ul>
  </section>

  <section class="al-seccion" aria-labelledby="coleccion-titulo">
    <div class="al-ancho">
      <header class="al-encabezado al-encabezado--fila">
        <div>
          <p class="al-sobre">La colección</p>
          <h2 id="coleccion-titulo">Diez tortas, <em>una por cada fruta</em></h2>
        </div>
        <a class="al-enlace" href="<?= demo_e(AL_BASE . 'tortas.php') ?>">Ver la colección completa</a>
      </header>
      <div class="al-destacadas">
        <?php foreach ($destacadas as $i => $id): $t = $n['tortas'][$id]; ?>
        <article class="al-destacada<?= $i === 0 ? ' al-destacada--grande' : '' ?>">
          <a class="al-destacada__foto" href="<?= demo_e(AL_BASE . 'tortas.php#torta-' . $id) ?>" tabindex="-1" aria-hidden="true">
            <?= demo_img($t['foto'], '', ['ancho' => 800, 'alto' => 1000, 'anchos' => [480, 800, 1200], 'sizes' => $i === 0 ? '(max-width: 860px) 92vw, 50vw' : '(max-width: 860px) 92vw, 25vw']) ?>
          </a>
          <p class="al-numero"><?= demo_e($num($id)) ?></p>
          <h3><a href="<?= demo_e(AL_BASE . 'tortas.php#torta-' . $id) ?>"><?= demo_e($t['nombre']) ?></a></h3>
          <p class="al-notas"><?= demo_e($t['notas']) ?></p>
          <p class="al-precio">Desde <?= demo_e(demo_clp(al_desde($t))) ?> · <?= demo_e(al_porciones($t)) ?></p>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="al-seccion al-oscuro" aria-labelledby="atelier-titulo">
    <div class="al-ancho al-atelier">
      <div class="al-atelier__fotos">
        <figure class="al-atelier__f1"><?= demo_img($n['fotos']['manos'], 'Manos de la pastelera decorando pavlovas con frutos rojos', ['ancho' => 700, 'alto' => 875, 'sizes' => '(max-width: 860px) 60vw, 26vw']) ?></figure>
        <figure class="al-atelier__f2"><?= demo_img($n['fotos']['cacao'], 'Cacao espolvoreado sobre una torta de chocolate', ['ancho' => 600, 'alto' => 600, 'sizes' => '(max-width: 860px) 46vw, 20vw']) ?></figure>
      </div>
      <div class="al-atelier__texto">
        <p class="al-sobre">El atelier</p>
        <h2 id="atelier-titulo">Pequeño, lento <em>y de temporada</em></h2>
        <blockquote>
          <p>«Hacemos pocas tortas al día y cada una se arma a mano. Si la fruta no está en su punto, esa semana la torta no sale.»</p>
          <footer><?= demo_e($n['pastelera']) ?>, pastelera</footer>
        </blockquote>
        <a class="al-enlace al-enlace--claro" href="<?= demo_e(AL_BASE . 'nosotros.php') ?>">Conoce el atelier</a>
      </div>
    </div>
  </section>

  <section class="al-seccion" aria-labelledby="encargo-titulo">
    <div class="al-ancho al-encargo-intro">
      <div>
        <p class="al-sobre">Encargos</p>
        <h2 id="encargo-titulo">Tu torta, <em>a la medida</em></h2>
        <p>Elige una torta de la colección o pídenos un diseño propio. Completa el formulario y te llega el encargo armado a WhatsApp para confirmarlo.</p>
        <a class="al-btn" href="<?= demo_e(AL_BASE . 'encargo.php') ?>">Hacer un encargo</a>
      </div>
      <ol class="al-pasos">
        <li><span>01</span><strong>Elige torta y tamaño</strong><small>De 8 a 120 porciones.</small></li>
        <li><span>02</span><strong>Fecha y mensaje</strong><small>Con al menos 3 días de anticipación.</small></li>
        <li><span>03</span><strong>Retira o recibe</strong><small>En el atelier o con despacho refrigerado.</small></li>
      </ol>
    </div>
  </section>

  <section class="al-seccion al-seccion--rosa" aria-labelledby="carta-titulo">
    <div class="al-ancho al-dos">
      <figure class="al-marco">
        <?= demo_img($n['fotos']['vitrina'], 'Vitrina con tartaletas de fruta y pasteles de chocolate', ['ancho' => 800, 'alto' => 640, 'sizes' => '(max-width: 860px) 92vw, 45vw']) ?>
      </figure>
      <div>
        <p class="al-sobre">La vitrina</p>
        <h2 id="carta-titulo">Para hoy, <em>sin esperar</em></h2>
        <p>Tartaletas, pavlovas, macarons y cajas para regalar, hechos cada mañana. Arma tu pedido en la carta y envíalo por WhatsApp: retiro en el atelier o despacho en el sector oriente.</p>
        <a class="al-btn al-btn--linea" href="<?= demo_e(AL_BASE . 'carta.php') ?>">Ver la carta</a>
      </div>
    </div>
  </section>

  <div class="al-seccion al-seccion--google">
    <div class="al-ancho">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ]); ?>
    </div>
  </div>
</main>
<?php al_cerrar();
