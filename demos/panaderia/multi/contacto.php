<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = ml();
$estado = demo_horario_estado($n['horario']);
ml_abrir('contacto', 'Contacto y horario', 'Horario, encargos, despacho y cómo llegar a ' . $n['nombre'] . ' en ' . $n['comuna'] . ', Santiago. Demo de sitio para panadería de masa madre.');
?>
<main id="contenido">
  <header class="ml-titular">
    <div class="ml-ancho">
      <p class="ml-etiqueta"><span class="ml-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
      <h1>Pasa a buscar <em>tu hogaza</em></h1>
      <p>Estamos en <?= demo_e($n['comuna']) ?>. Abrimos temprano y cerramos cuando se acaba el pan (o a la hora de abajo, lo que pase primero).</p>
    </div>
  </header>

  <section class="ml-seccion ml-seccion--sin-arriba">
    <div class="ml-ancho ml-contacto">
      <div class="ml-tarjeta">
        <p class="ml-etiqueta">Horario</p>
        <?php demo_horario_tabla($n['horario'], 'demo-horario ml-horario'); ?>
        <p class="ml-pequeno">Lunes cerrado: es el día en que alimentamos la masa madre y descansa el horno.</p>
      </div>
      <div class="ml-tarjeta">
        <p class="ml-etiqueta">Encargos y despacho</p>
        <p>Encarga hasta las 18:00 para el día siguiente. Despachamos de martes a sábado en <?= demo_e(implode(', ', array_keys($n['comunas_despacho']))) ?>.</p>
        <div class="ml-acciones ml-acciones--columna">
          <a class="ml-btn" href="<?= demo_e(ML_BASE . 'panes.php') ?>"><span>Armar un encargo</span><?= ml_flecha() ?></a>
          <a class="ml-btn ml-btn--linea" href="<?= demo_e(ml_wa('Hola, quiero hacer un encargo en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener"><span>Escribir por WhatsApp</span></a>
          <a class="ml-btn ml-btn--linea" href="<?= demo_e(demo_tel()) ?>"><span>Llamar</span></a>
        </div>
      </div>
      <figure class="ml-contacto__foto">
        <?= demo_img($n['fotos']['miga'], 'Hogaza partida por la mitad mostrando la miga abierta', ['ancho' => 700, 'alto' => 700, 'anchos' => [400, 700, 1000], 'sizes' => '(max-width: 900px) 92vw, 30vw']) ?>
        <figcaption><span>Fig. 07</span> Miga abierta, señal de una buena fermentación</figcaption>
      </figure>
    </div>
  </section>

  <div class="ml-seccion ml-seccion--google">
    <div class="ml-ancho">
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
<?php ml_cerrar();
