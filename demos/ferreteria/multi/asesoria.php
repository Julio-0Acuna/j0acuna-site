<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = pg();

pg_abrir('asesoria', 'Asesoría de color', 'Asesoría de ' . $n['nombre'] . ': colorista a domicilio, igualación de color, muestras de 250 ml y pintores de confianza. Tienda de pinturas en ' . $n['comuna'] . ', Santiago.');
?>
<main id="contenido">
  <?php pg_titular('Asesoría y nosotros', 'Una arquitecta y un pintor <em>detrás del mesón</em>', 'Pigmento nació en ' . $n['desde'] . ' cuando Valentina, arquitecta, y su papá Jorge, pintor de oficio por treinta años, juntaron lo que sabían: elegir bien el color y elegir bien la pintura.'); ?>

  <section class="pg-seccion pg-seccion--sin-arriba" aria-labelledby="historia-titulo">
    <div class="pg-ancho pg-historia">
      <figure class="pg-historia__foto"><?= demo_img($n['fotos']['verde'], 'Living con un muro verde profundo, un sillón verde limón y una lámpara de pie', ['ancho' => 800, 'alto' => 1000, 'anchos' => [480, 800, 1100], 'sizes' => '(max-width: 860px) 92vw, 42vw']) ?></figure>
      <div class="pg-historia__texto">
        <h2 id="historia-titulo">Pintar es fácil. <em>Elegir es lo difícil.</em></h2>
        <p>La mayoría de las personas que llegan a la tienda ya compró una vez el color equivocado: se veía bien en el abanico y en la casa quedó otra cosa. Por eso no vendemos solo pintura: te ayudamos a probar, a medir y a decidir.</p>
        <p>Trabajamos con pinturas de fabricación nacional que preparamos a máquina en la tienda, y con una red de maestros pintores que conocen bien lo que vendemos.</p>
        <blockquote class="pg-cita">
          <p>«Un color hay que verlo en el muro, con la luz de la tarde. Antes de eso, todo es teoría.»</p>
          <footer>Valentina, colorista de Pigmento</footer>
        </blockquote>
      </div>
    </div>
  </section>

  <section class="pg-seccion pg-seccion--papel" aria-labelledby="servicios-titulo">
    <div class="pg-ancho">
      <div class="pg-cabeza">
        <p class="pg-sobre">Servicios</p>
        <h2 id="servicios-titulo">Lo que hacemos <em>además de vender pintura</em></h2>
      </div>
      <ul class="pg-servicios">
        <?php foreach ($n['servicios'] as [$nombre, $detalle, $precio, $foto, $alt]): ?>
        <li class="pg-servicio">
          <?= demo_img($n['fotos'][$foto], $alt, ['ancho' => 600, 'alto' => 450, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 92vw, 45vw']) ?>
          <div>
            <h3><?= demo_e($nombre) ?></h3>
            <p><?= demo_e($detalle) ?></p>
            <p class="pg-servicio__precio"><?= demo_e($precio) ?></p>
            <a class="pg-enlace" href="<?= demo_e(pg_wa('Hola, quiero agendar: ' . $nombre . '.')) ?>" target="_blank" rel="noopener">Agendar por WhatsApp</a>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="pg-seccion" aria-labelledby="proceso-titulo">
    <div class="pg-ancho">
      <div class="pg-cabeza">
        <p class="pg-sobre">Cómo es una visita de colorista</p>
        <h2 id="proceso-titulo">De la idea al muro <em>en cuatro pasos</em></h2>
      </div>
      <ol class="pg-proceso">
        <li><span>01</span><h3>Conversamos</h3><p>Por WhatsApp nos cuentas qué ambientes quieres pintar y nos mandas fotos.</p></li>
        <li><span>02</span><h3>Visita</h3><p>Llevamos la carta física y miramos la luz de cada pieza, de día.</p></li>
        <li><span>03</span><h3>Muestras</h3><p>Te dejamos dos o tres muestras pintadas en el muro para mirarlas con calma.</p></li>
        <li><span>04</span><h3>Cotización</h3><p>Medimos y te enviamos la cotización con la cantidad exacta de galones.</p></li>
      </ol>
      <div class="pg-acciones">
        <a class="pg-btn" href="<?= demo_e(pg_wa('Hola, quiero agendar una visita de colorista. Comuna y ambientes: ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> Agendar visita</a>
        <a class="pg-btn pg-btn--linea" href="<?= demo_e(PG_BASE . 'colores.php') ?>">Ver la carta de colores</a>
      </div>
    </div>
  </section>
</main>
<?php pg_cerrar();
