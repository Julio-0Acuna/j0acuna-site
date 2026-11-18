<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = eg();
$estado = demo_horario_estado($n['horario']);
$destacados = array_filter($n['servicios'], static fn (array $s): bool => !empty($s['destacado']));

eg_abrir('inicio', 'Barbería de caballeros en ' . $n['comuna'], 'Demo de página web de varias páginas para una barbería de caballeros old school en el ' . $n['barrio'] . ', ' . $n['comuna'] . ': corte, afeitado con toalla caliente, sala de espera con bebidas sin alcohol y reserva con barbero, día y bloque por WhatsApp.');
?>
<main id="contenido">
  <section class="eg-portada" aria-labelledby="portada-titulo">
    <?= demo_img($n['fotos']['portada'], 'Sillón de barbero antiguo junto a una puerta de dos hojas abierta, con la luz de la tarde entrando al salón', ['lazy' => false, 'ancho' => 1600, 'alto' => 1000, 'anchos' => [700, 1100, 1600, 2000], 'sizes' => '100vw', 'clase' => 'eg-portada__fondo']) ?>
    <div class="eg-ancho eg-portada__marco">
      <p class="eg-sobre"><?= demo_e(ucfirst($n['barrio'])) ?> · Santiago · Desde <?= (int) $n['desde'] ?></p>
      <h1 id="portada-titulo"><span class="eg-portada__el">El</span> Gabinete</h1>
      <p class="eg-portada__lema">Barbería de caballeros. Corte, navaja y toalla caliente, con hora y sin apuro.</p>
      <div class="eg-acciones">
        <a class="eg-btn" href="<?= demo_e(EG_BASE . 'servicios.php') ?>">Reservar hora</a>
        <a class="eg-btn eg-btn--linea" href="<?= demo_e(EG_BASE . 'la-casa.php') ?>">Conocer la casa</a>
      </div>
      <p class="eg-estado"><span class="eg-estado__punto<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
    </div>
  </section>

  <section class="eg-seccion" aria-labelledby="casa-titulo">
    <div class="eg-ancho eg-casa">
      <div class="eg-casa__texto">
        <p class="eg-sobre">La casa</p>
        <h2 id="casa-titulo">Un segundo piso con sillones de cuero, espejos con marco y olor a bálsamo</h2>
        <p>El Gabinete abrió en <?= (int) $n['desde'] ?> como peluquería de caballeros para los oficinistas del centro. Cambiaron los cortes y las corbatas; se quedaron la toalla caliente, la navaja asentada en cuero y la costumbre de recibir a cada cliente con un café.</p>
        <ul class="eg-sellos">
          <li><strong>Toalla caliente</strong><span>en todos los servicios de barba</span></li>
          <li><strong>Bebida de la casa</strong><span>café, té o limonada, sin alcohol</span></li>
          <li><strong>Hora puntual</strong><span>tu sillón te espera a la hora</span></li>
        </ul>
      </div>
      <figure class="eg-marco eg-casa__foto">
        <?= demo_img($n['fotos']['salon'], 'Salón de barbería con sillones de cuero capitoné, espejos y luz dorada', ['ancho' => 800, 'alto' => 1000, 'sizes' => '(max-width: 860px) 90vw, 40vw']) ?>
        <figcaption>El salón principal, con sus cuatro sillones</figcaption>
      </figure>
    </div>
  </section>

  <section class="eg-seccion eg-seccion--oscura" aria-labelledby="carta-titulo">
    <div class="eg-ancho">
      <?= eg_filete() ?>
      <header class="eg-encabezado eg-encabezado--centro">
        <p class="eg-sobre">Servicios de la casa</p>
        <h2 id="carta-titulo">La carta del barbero</h2>
        <p>Agrega a tu reserva lo que quieras hacerte. En el último paso eliges barbero, día y bloque.</p>
      </header>
      <ul class="eg-carta">
        <?php foreach ($destacados as $id => $s): ?>
        <li class="eg-carta__item">
          <div class="eg-carta__foto"><?= demo_img($s['foto'], $s['alt'], ['ancho' => 600, 'alto' => 760, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 90vw, 25vw']) ?></div>
          <div class="eg-carta__texto">
            <h3><?= demo_e($s['nombre']) ?></h3>
            <p class="eg-meta"><?= demo_e($s['dur']) ?></p>
            <p><?= demo_e($s['desc']) ?></p>
            <?= eg_agregar($id, $s) ?>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
      <p class="eg-centro"><a class="eg-btn eg-btn--linea" href="<?= demo_e(EG_BASE . 'servicios.php') ?>">Ver todos los servicios y precios</a></p>
    </div>
  </section>

  <section class="eg-seccion" aria-labelledby="ritual-titulo">
    <div class="eg-ancho eg-ritual">
      <figure class="eg-marco eg-ritual__foto">
        <?= demo_img($n['fotos']['ritual'], 'Foto en blanco y negro de un barbero afeitando con navaja a un cliente con espuma en la barba', ['ancho' => 900, 'alto' => 700, 'sizes' => '(max-width: 860px) 90vw, 45vw']) ?>
      </figure>
      <div>
        <p class="eg-sobre">El ritual</p>
        <h2 id="ritual-titulo">Así se afeita en El Gabinete</h2>
        <ol class="eg-pasos">
          <li><span class="eg-pasos__num" aria-hidden="true">I</span><div><h3>Toalla caliente</h3><p>Dos toallas a vapor abren el poro y ablandan la barba.</p></div></li>
          <li><span class="eg-pasos__num" aria-hidden="true">II</span><div><h3>Aceite y espuma</h3><p>Aceite previo y espuma armada a brocha, en taza, como siempre.</p></div></li>
          <li><span class="eg-pasos__num" aria-hidden="true">III</span><div><h3>Navaja</h3><p>Hoja nueva para cada cliente, a favor y después a contrapelo.</p></div></li>
          <li><span class="eg-pasos__num" aria-hidden="true">IV</span><div><h3>Toalla fría y bálsamo</h3><p>Cierra el poro y calma la piel. Sin alcohol, sin ardor.</p></div></li>
        </ol>
      </div>
    </div>
  </section>

  <section class="eg-seccion eg-seccion--cuero" aria-labelledby="espera-titulo">
    <div class="eg-ancho eg-espera">
      <div>
        <p class="eg-sobre">Sala de espera</p>
        <h2 id="espera-titulo">Llega antes. La sala es parte del servicio.</h2>
        <p>Sillones de cuero, el diario del día, música a volumen de conversación y una bebida por cuenta de la casa con tu hora. Todas sin alcohol.</p>
        <ul class="eg-bebidas-lista">
          <?php foreach ($n['bebidas'] as [$bebida]): ?><li><?= demo_e($bebida) ?></li><?php endforeach; ?>
          <?php foreach (array_slice($n['bebidas_mas'], 0, 2) as $bebida): ?><li><?= demo_e($bebida) ?></li><?php endforeach; ?>
        </ul>
        <a class="eg-enlace" href="<?= demo_e(EG_BASE . 'la-casa.php#bebidas') ?>">Ver la carta de bebidas</a>
      </div>
      <figure class="eg-marco eg-espera__foto">
        <?= demo_img($n['fotos']['espera'], 'Sillón de cuero café junto a una ventana, sobre un piso de baldosas a cuadros', ['ancho' => 700, 'alto' => 900, 'sizes' => '(max-width: 860px) 90vw, 36vw']) ?>
      </figure>
    </div>
  </section>

  <section class="eg-seccion" aria-labelledby="barberos-titulo">
    <div class="eg-ancho">
      <header class="eg-encabezado eg-encabezado--centro">
        <p class="eg-sobre">Los barberos</p>
        <h2 id="barberos-titulo">Tres sillones, tres oficios</h2>
        <p>Eliges con quién en tu reserva. Si te da lo mismo, te atiende el primero disponible.</p>
      </header>
      <ul class="eg-barberos">
        <?php foreach ($n['barberos'] as $b): ?>
        <li class="eg-barbero">
          <div class="eg-barbero__foto"><?= demo_img($b['foto'], $b['alt'], ['ancho' => 600, 'alto' => 760, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 90vw, 30vw']) ?></div>
          <h3><?= demo_e($b['nombre']) ?></h3>
          <p class="eg-meta"><?= demo_e($b['oficio']) ?></p>
          <p class="eg-barbero__dias"><?= demo_e($b['dias']) ?></p>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <div class="eg-seccion eg-seccion--oscura">
    <div class="eg-ancho">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería la ficha de El Gabinete en Google Maps: horario, cómo llegar y las opiniones de los clientes.']); ?>
    </div>
  </div>

  <section class="eg-cierre" aria-labelledby="cierre-titulo">
    <div class="eg-ancho">
      <?= eg_filete() ?>
      <h2 id="cierre-titulo">Su sillón lo espera, caballero.</h2>
      <div class="eg-acciones eg-acciones--centro">
        <a class="eg-btn" href="<?= demo_e(EG_BASE . 'servicios.php') ?>">Reservar hora</a>
        <a class="eg-btn eg-btn--linea" href="<?= demo_e(eg_wa('Hola, quiero reservar una hora en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> WhatsApp</a>
      </div>
    </div>
  </section>
</main>
<?php eg_cerrar();
