<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = fo();
$firmas = array_filter($n['platos'], static fn (array $p): bool => $p['firma']);

fo_abrir('inicio', 'Pizza napolitana en ' . $n['comuna'], 'Demo de sitio de varias páginas para una pizzería napolitana en ' . $n['comuna'] . ', Santiago: menú con QR y platos en 3D, pedido por WhatsApp y reservas.');
?>
<main id="contenido">
  <section class="fo-portada">
    <div class="fo-ancho fo-portada__grid">
      <div class="fo-portada__texto">
        <p class="fo-sobre">Pizzería napolitana · <?= demo_e($n['comuna']) ?></p>
        <h1>La pizza que nace <em>del fuego</em></h1>
        <p class="fo-portada__bajada">Masa madre de 48 horas, tomate San Marzano y un horno a leña que llega a 450 °C. Noventa segundos y a la mesa.</p>
        <div class="fo-acciones">
          <a class="fo-btn" href="<?= demo_e(FO_BASE . 'menu.php') ?>">Ver el menú</a>
          <a class="fo-btn fo-btn--linea" href="<?= demo_e(fo_wa('Hola, quiero reservar una mesa en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Reservar mesa</a>
        </div>
      </div>
      <figure class="fo-arco fo-portada__foto">
        <?= demo_img($n['fotos']['hero'], 'Pizza saliendo del horno a leña sobre una pala de madera', ['lazy' => false, 'ancho' => 900, 'alto' => 1100, 'sizes' => '(max-width: 860px) 86vw, 460px']) ?>
      </figure>
    </div>
  </section>

  <section class="fo-cifras" aria-label="Cómo trabajamos">
    <div class="fo-ancho fo-cifras__fila">
      <p><strong>48 h</strong><span>de fermentación</span></p>
      <p><strong>450 °C</strong><span>horno a leña</span></p>
      <p><strong>90 s</strong><span>de horneado</span></p>
    </div>
  </section>

  <section class="fo-seccion" aria-labelledby="firmas-titulo">
    <div class="fo-ancho">
      <header class="fo-encabezado">
        <p class="fo-sobre">De la casa</p>
        <h2 id="firmas-titulo">Nuestras pizzas <em>de firma</em></h2>
      </header>
      <div class="fo-firmas">
        <?php foreach ($firmas as $id => $p): ?>
        <article class="fo-firma">
          <figure class="fo-arco fo-arco--chico">
            <?= demo_img($p['foto'], 'Pizza ' . $p['nombre'], ['ancho' => 600, 'alto' => 720, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 760px) 86vw, 30vw']) ?>
          </figure>
          <h3><?= demo_e($p['nombre']) ?></h3>
          <p><?= demo_e($p['desc']) ?></p>
          <p class="fo-firma__precio">Desde <?= demo_e(demo_clp(fo_desde($p))) ?></p>
        </article>
        <?php endforeach; ?>
      </div>
      <p class="fo-centro"><a class="fo-btn fo-btn--linea" href="<?= demo_e(FO_BASE . 'menu.php') ?>">Ver el menú completo</a></p>
    </div>
  </section>

  <section class="fo-seccion fo-oscuro" aria-labelledby="historia-titulo">
    <div class="fo-ancho fo-dos">
      <figure class="fo-marco">
        <?= demo_img($n['fotos']['masa'], 'Pan de masa madre recién horneado con corteza dorada', ['ancho' => 800, 'alto' => 800, 'sizes' => '(max-width: 860px) 86vw, 45vw']) ?>
      </figure>
      <div>
        <p class="fo-sobre">Nuestra historia</p>
        <h2 id="historia-titulo">Todo empieza <em>con la masa</em></h2>
        <p>Ottavia aprendió a hacer pizza en la cocina de su abuela. Hoy usamos la misma masa madre, harina tipo 00 y mucha paciencia: la masa descansa dos días antes de entrar al horno.</p>
        <a class="fo-enlace" href="<?= demo_e(FO_BASE . 'nosotros.php') ?>">Conoce Forno Ottavia</a>
      </div>
    </div>
  </section>

  <section class="fo-seccion" aria-labelledby="pedir-titulo">
    <div class="fo-ancho fo-pedir">
      <div>
        <p class="fo-sobre">Para llevar</p>
        <h2 id="pedir-titulo">Pide en línea, <em>recibe en casa</em></h2>
        <p>Arma tu pedido en el menú, elige retiro o despacho y envíalo por WhatsApp. Despachamos en Providencia y comunas cercanas.</p>
        <p class="fo-nota-apps">También nos encuentras en las principales apps de delivery.</p>
      </div>
      <div class="fo-acciones">
        <a class="fo-btn" href="<?= demo_e(FO_BASE . 'menu.php') ?>">Armar mi pedido</a>
        <a class="fo-btn fo-btn--linea" href="<?= demo_e(demo_tel()) ?>">Llamar</a>
      </div>
    </div>
  </section>

  <div class="fo-seccion fo-marfil">
    <div class="fo-ancho">
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
<?php fo_cerrar();
