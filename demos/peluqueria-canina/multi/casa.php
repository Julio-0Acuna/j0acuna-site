<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = br();

br_abrir('casa', 'La casa y el equipo', 'La casa de ' . $n['nombre'] . ' en ' . $n['comuna'] . ': dos salas, una tina con hidromasaje tibio, secado sin jaulas y un equipo de tres groomers. Demo de spa canino.');
?>
<main id="contenido">
  <?php br_titular('03', 'La casa', 'Un spa <em>a escala de perro</em>', 'Abrimos Bruma en ' . $n['desde'] . ' con una idea simple: que el baño sea un buen rato y no un trámite. Por eso atendemos poco, con calma y siempre con la misma groomer de principio a fin.'); ?>

  <section class="br-seccion br-seccion--sin-arriba" aria-labelledby="salas-titulo">
    <div class="br-ancho br-salas">
      <figure class="br-ovalo br-salas__foto">
        <?= demo_img($n['fotos']['sillon'], 'Pomerania color crema sentado en un sillón negro de peluquería junto al lavatorio, con un juguete en el hocico', ['ancho' => 700, 'alto' => 900, 'sizes' => '(max-width: 900px) 80vw, 420px', 'anchos' => [400, 700, 1000]]) ?>
      </figure>
      <div>
        <h2 id="salas-titulo">Dos salas, <em>nunca más de dos perros</em></h2>
        <p>Cada sala tiene su tina con hidromasaje, su mesa de corte y su ventana. Mientras un perro está en el ritual, nadie más espera a su lado: así no hay ladridos, olores ajenos ni nervios contagiados.</p>
        <ul class="br-detalles">
          <li><strong>Agua a 32 °C</strong><span>Medimos la temperatura en cada baño.</span></li>
          <li><strong>Productos suaves</strong><span>Shampoo de avena o neutro, sin perfumes fuertes.</span></li>
          <li><strong>Secado silencioso</strong><span>Soplador a baja potencia, toalla y pausas.</span></li>
          <li><strong>Sin jaulas</strong><span>Nunca dejamos a un perro solo secándose.</span></li>
        </ul>
      </div>
    </div>
  </section>

  <section class="br-seccion br-seccion--terrazo" aria-labelledby="equipo-titulo">
    <div class="br-ancho">
      <header class="br-encabezado br-encabezado--centro">
        <p class="br-sobre">El equipo</p>
        <h2 id="equipo-titulo">Tres groomers, <em>cada una con su oficio</em></h2>
        <p>Al reservar puedes elegir con quién. Si no tienes preferencia, te asignamos según el ritual y la raza.</p>
      </header>
      <ul class="br-equipo">
        <?php foreach ($n['equipo'] as $p): ?>
        <li class="br-persona">
          <span class="br-persona__inicial" aria-hidden="true"><?= demo_e($p['inicial']) ?></span>
          <h3><?= demo_e($p['nombre']) ?></h3>
          <p class="br-meta"><?= demo_e($p['rol']) ?></p>
          <p><?= demo_e($p['texto']) ?></p>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="br-seccion" aria-labelledby="promesa-titulo">
    <div class="br-ancho br-promesa">
      <div>
        <p class="br-sobre">Lo que prometemos</p>
        <h2 id="promesa-titulo">Si no lo está pasando bien, <em>paramos</em></h2>
        <p>Algunos perros llegan con miedo al secador o a las tijeras. Les damos tiempo, premios y pausas. Si aun así lo vemos muy estresado, paramos, te llamamos y decidimos juntos: a veces es mejor terminar otro día.</p>
        <p>Y si vemos algo en la piel, los oídos o los dientes que nos preocupa, te lo contamos para que lo vea su veterinario. No somos clínica y no recetamos nada.</p>
        <a class="br-btn" href="<?= demo_e(BR_BASE . 'reserva.php') ?>">Reservar un ritual</a>
      </div>
      <div class="br-promesa__fotos">
        <figure class="br-ovalo"><?= demo_img($n['fotos']['siesta'], 'Golden retriever durmiendo sobre un sillón gris', ['ancho' => 520, 'alto' => 650, 'anchos' => [320, 520, 800], 'sizes' => '(max-width: 700px) 44vw, 260px']) ?></figure>
        <figure class="br-ovalo"><?= demo_img($n['fotos']['mano'], 'Cachorro caniche blanco recién peinado sobre la mano de su groomer', ['ancho' => 520, 'alto' => 650, 'anchos' => [320, 520, 800], 'sizes' => '(max-width: 700px) 44vw, 260px']) ?></figure>
      </div>
    </div>
  </section>
</main>
<?php br_cerrar();
