<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = dp();
dp_abrir('plancha', 'La plancha', 'La historia de ' . $n['nombre'] . ', sanguchería y fuente de soda en ' . $n['comuna'] . ': churrascos a la plancha y palta molida cada hora. Demo de sitio de varias páginas.');
?>
<main id="contenido">
  <header class="dp-titular">
    <div class="dp-ancho">
      <p class="dp-mono dp-sobre">Nosotros</p>
      <h1>La plancha</h1>
      <p>Una fuente de soda chica en <?= demo_e($n['comuna']) ?>, con una plancha de dos metros, doce pisos en la barra y una regla: la palta se muele en el momento.</p>
    </div>
  </header>

  <section class="dp-seccion dp-seccion--sin-arriba">
    <div class="dp-ancho dp-dos">
      <figure class="dp-marco">
        <?= demo_img($n['fotos']['smash'], 'Espátula aplastando una hamburguesa sobre la plancha caliente', ['lazy' => false, 'ancho' => 800, 'alto' => 900, 'sizes' => '(max-width: 860px) 90vw, 45vw']) ?>
      </figure>
      <div class="dp-prosa">
        <h2>Como en las fuentes de soda de antes</h2>
        <p>Crecimos comiendo churrascos en la barra de las fuentes de soda del centro. Cuando abrimos Doble Palta quisimos lo mismo, pero sin atajos: carne laminada al momento, pan de la panadería de la esquina tostado en la misma plancha, y mayo casera batida cada mañana.</p>
        <p>Las hamburguesas se aplastan contra el fierro caliente para que queden con costra. No las cocinamos antes: cada una entra a la plancha cuando llega tu comanda.</p>
        <ul class="dp-lista">
          <li>Palta Hass molida cada hora, con sal y limón</li>
          <li>Marraqueta y frica del día</li>
          <li>Carne molida y laminada cada mañana</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="dp-seccion dp-negro">
    <div class="dp-ancho dp-dos dp-dos--invertido">
      <figure class="dp-marco">
        <?= demo_img($n['fotos']['barra'], 'Barra de fuente de soda con pisos altos y mesón de madera', ['ancho' => 800, 'alto' => 800, 'sizes' => '(max-width: 860px) 90vw, 45vw']) ?>
      </figure>
      <div class="dp-prosa">
        <p class="dp-mono dp-sobre">El local</p>
        <h2>Doce pisos en la barra</h2>
        <p>Comer en la barra mirando la plancha es la mitad de la gracia. También tenemos seis mesas para grupos y una ventana para retirar sin entrar.</p>
        <a class="dp-btn" href="<?= demo_e(DP_BASE . 'carta.php') ?>">Ver la carta</a>
      </div>
    </div>
  </section>

  <section class="dp-seccion">
    <div class="dp-ancho dp-dos">
      <figure class="dp-marco dp-marco--palta">
        <?= demo_img($n['fotos']['palta'], 'Palta en láminas sobre pan, junto a paltas enteras', ['ancho' => 800, 'alto' => 700, 'sizes' => '(max-width: 860px) 90vw, 45vw']) ?>
      </figure>
      <div class="dp-prosa">
        <p class="dp-mono dp-sobre">El nombre</p>
        <h2>¿Por qué Doble Palta?</h2>
        <p>Porque en Chile un sánguche sin palta está incompleto, y porque la nuestra viene en doble porción si la pides. Es la única pregunta que hacemos dos veces: «¿con harta palta?».</p>
      </div>
    </div>
  </section>
</main>
<?php dp_cerrar();
