<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = al();
al_abrir('nosotros', 'El atelier', 'La historia de ' . $n['nombre'] . ', pastelería de autor en ' . $n['comuna'] . ' que trabaja con lúcuma, maqui, miel de ulmo y otras frutas de Chile. Demo de sitio de varias páginas.');
al_titular('El atelier', 'Una cocina pequeña, <em>frutas de todo Chile</em>', 'Abrimos en ' . demo_e($n['comuna']) . ' con una idea simple: pastelería francesa hecha con lo mejor que da cada temporada en Chile.');
?>
<main id="contenido">
  <section class="al-seccion al-seccion--sin-arriba">
    <div class="al-ancho al-dos">
      <figure class="al-marco al-marco--alto">
        <?= demo_img($n['fotos']['manga'], 'Pastelera rellenando merengues con una manga en el mesón del atelier', ['lazy' => false, 'ancho' => 800, 'alto' => 1000, 'sizes' => '(max-width: 860px) 92vw, 45vw']) ?>
      </figure>
      <div class="al-prosa">
        <p class="al-sobre">La pastelera</p>
        <h2><?= demo_e($n['pastelera']) ?></h2>
        <p>Josefina aprendió pastelería francesa en Lyon y volvió a Chile con una pregunta: ¿por qué no hacer un entremet con maqui o una tarta con limón de Pica? Así nació el atelier, en una cocina de diez metros cuadrados y con una vitrina de seis tortas.</p>
        <p>Hoy trabajamos cuatro personas. Seguimos haciendo pocas tortas al día, todas a mano, y cambiamos la colección con cada temporada: higos en otoño, cítricos en invierno, frutillas y berries en primavera.</p>
        <ul class="al-lista">
          <li>Mantequilla, crema y huevos de campo de productores del sur</li>
          <li>Chocolate de origen y sin grasas vegetales</li>
          <li>Fruta comprada cada mañana, nunca congelada</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="al-seccion al-oscuro" aria-labelledby="ingredientes-titulo">
    <div class="al-ancho">
      <header class="al-encabezado">
        <p class="al-sobre">Despensa chilena</p>
        <h2 id="ingredientes-titulo">Seis ingredientes <em>que nos definen</em></h2>
      </header>
      <ol class="al-despensa">
        <?php foreach ($n['ingredientes'] as $i => [$nombre, $texto]): ?>
        <li><span><?= sprintf('%02d', $i + 1) ?></span><h3><?= demo_e($nombre) ?></h3><p><?= demo_e($texto) ?></p></li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section class="al-seccion">
    <div class="al-ancho al-dos al-dos--invertido">
      <figure class="al-marco">
        <?= demo_img($n['fotos']['cacao'], 'Cacao espolvoreado con colador sobre una torta de chocolate', ['ancho' => 800, 'alto' => 800, 'sizes' => '(max-width: 860px) 92vw, 45vw']) ?>
      </figure>
      <div class="al-prosa">
        <p class="al-sobre">Visítanos</p>
        <h2>Una vitrina <em>y seis mesas</em></h2>
        <p>En el atelier puedes tomar un café con una pieza de la vitrina, conversar tu torta de matrimonio con Josefina o retirar tu encargo. Te recomendamos reservar la degustación de matrimonios por WhatsApp.</p>
        <div class="al-acciones">
          <a class="al-btn" href="<?= demo_e(al_wa('Hola, quiero reservar una degustación de tortas de matrimonio en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Reservar degustación</a>
          <a class="al-btn al-btn--linea" href="<?= demo_e(AL_BASE . 'contacto.php') ?>">Horario y ubicación</a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php al_cerrar();
