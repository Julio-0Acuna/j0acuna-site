<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = ku();
ku_abrir('nosotros', 'La barra', 'La historia de ' . $n['nombre'] . ', barra de omakase de ocho asientos en ' . $n['comuna'] . '. Demo de sitio de varias páginas para restaurante de sushi.');
?>
<main id="contenido">
  <?php ku_titular('La barra', 'Una barra de hinoki <em>y ocho asientos</em>', 'Abrimos en ' . demo_e($n['comuna']) . ' con una idea sencilla: servir el sushi como se sirve en las barras pequeñas de Japón, pieza a pieza y frente a quien lo come.', '席'); ?>

  <section class="ku-seccion ku-seccion--sin-arriba">
    <div class="ku-ancho">
      <figure class="ku-foto ku-foto--panorama">
        <?= demo_img($n['fotos']['salon'], 'Salón de pilares de madera clara con una mesa servida con platos oscuros', ['lazy' => false, 'ancho' => 1400, 'alto' => 700, 'anchos' => [800, 1400, 2000], 'sizes' => '(max-width: 1200px) 100vw, 1160px']) ?>
        <figcaption>El salón: pilares de madera clara, luz baja y una mesa para seis.</figcaption>
      </figure>
    </div>
  </section>

  <section class="ku-seccion ku-seccion--sin-arriba">
    <div class="ku-ancho ku-dos">
      <div class="ku-prosa">
        <p class="ku-sobre">El itamae</p>
        <h2>Cortar, esperar, <em>servir</em></h2>
        <p>Rodrigo pasó veinte años detrás de barras en Santiago y Lima antes de abrir Kumo. Cada mañana va a buscar el pescado y decide el recorrido de la noche según lo que encuentra.</p>
        <p>En la barra no hay apuro: cada nigiri se arma frente a ti, se pinta con nikiri y se sirve en tu plato. Se come con la mano o con palillos, en un bocado y apenas llega.</p>
        <ul class="ku-lista">
          <li><span>米</span>Arroz de grano corto, cocido tres veces al día</li>
          <li><span>魚</span>Pescado de caleta, elegido cada mañana</li>
          <li><span>山</span>Wasabi rallado al momento, nunca de pasta</li>
        </ul>
      </div>
      <figure class="ku-foto ku-foto--alta">
        <?= demo_img($n['fotos']['plato'], 'Atún marinado con hojas de shiso y cítrico, servido en cerámica clara', ['ancho' => 800, 'alto' => 1000, 'sizes' => '(max-width: 900px) 92vw, 45vw']) ?>
      </figure>
    </div>
  </section>

  <section class="ku-seccion ku-claro">
    <div class="ku-ancho ku-dos ku-dos--invertido">
      <figure class="ku-foto">
        <?= demo_img($n['fotos']['neta'], 'Caja de madera con cortes de pescado fresco listos para el servicio', ['ancho' => 900, 'alto' => 700, 'sizes' => '(max-width: 900px) 92vw, 45vw']) ?>
      </figure>
      <div class="ku-prosa">
        <p class="ku-sobre">El lugar</p>
        <h2>Silencio, madera <em>y luz baja</em></h2>
        <p>Ocho asientos en la barra y una mesa baja para seis, que se reserva entera. Dos turnos por noche, a las 19:30 y a las 21:30, para que nadie tenga que apurarse.</p>
        <a class="ku-btn ku-btn--tinta" href="<?= demo_e(ku_wa_reserva()) ?>" target="_blank" rel="noopener"><span>Reservar por WhatsApp</span></a>
      </div>
    </div>
  </section>
</main>
<?php ku_cerrar();
