<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = dp();
$firmas = array_filter($n['platos'], static fn (array $p): bool => $p['firma']);
$estado = demo_horario_estado($n['horario']);
$estrella = $n['platos']['doble-palta'];

dp_abrir('inicio', 'Sanguchería en ' . $n['comuna'], 'Demo de sitio de varias páginas para una sanguchería y fuente de soda en ' . $n['comuna'] . ', Santiago: carta con QR, hamburguesas en 3D y pedido por WhatsApp.');
?>
<main id="contenido">
  <section class="dp-portada" aria-labelledby="dp-portada-titulo">
    <div class="dp-ancho dp-portada__grid">
      <div class="dp-portada__texto">
        <p class="dp-mono dp-portada__sobre"><span class="dp-punto <?= $estado['abierto'] ? 'is-abierto' : '' ?>" aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <h1 id="dp-portada-titulo">
          <span class="dp-portada__l1">Sánguches</span>
          <span class="dp-portada__l2">a la plancha</span>
          <span class="dp-portada__l3">con palta de verdad</span>
        </h1>
        <p class="dp-portada__bajada">Churrascos, lomitos, completos y hamburguesas aplastadas en la plancha, en <?= demo_e($n['comuna']) ?>. Arma tu comanda en la carta y te llega escrita a WhatsApp.</p>
        <div class="dp-acciones">
          <a class="dp-btn" href="<?= demo_e(DP_BASE . 'carta.php') ?>">Ver la carta</a>
          <a class="dp-btn dp-btn--linea" href="<?= demo_e(dp_wa('Hola, quiero hacer un pedido en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> Pedir por WhatsApp</a>
        </div>
      </div>
      <div class="dp-portada__foto">
        <span class="dp-portada__marco"><?= demo_img($n['fotos']['hero'], 'Hamburguesa con carne, queso derretido y cebolla en pan brillante', ['lazy' => false, 'ancho' => 900, 'alto' => 900, 'sizes' => '(max-width: 860px) 90vw, 500px']) ?></span>
        <a class="dp-ticket dp-portada__ticket" href="<?= demo_e(DP_BASE . 'carta.php#plato-doble-palta') ?>">
          <span class="dp-mono">N.º <?= demo_e($estrella['num']) ?> · la de la casa</span>
          <strong><?= demo_e($estrella['nombre']) ?></strong>
          <span class="dp-mono">desde <?= demo_e(demo_clp(dp_desde($estrella))) ?></span>
        </a>
      </div>
    </div>
  </section>

  <section class="dp-datos" aria-label="Cómo trabajamos">
    <div class="dp-ancho dp-datos__fila">
      <p><strong>60 min</strong><span>molemos palta fresca cada hora</span></p>
      <p><strong>12 min</strong><span>de la plancha a tu mano</span></p>
      <p><strong>0 %</strong><span>de pasta de palta envasada</span></p>
    </div>
  </section>

  <section class="dp-seccion" aria-labelledby="firmas-titulo">
    <div class="dp-ancho">
      <header class="dp-encabezado">
        <p class="dp-mono dp-sobre">Los que más salen</p>
        <h2 id="firmas-titulo">De la casa</h2>
      </header>
      <div class="dp-firmas">
        <?php foreach ($firmas as $id => $p): ?>
        <a class="dp-firma" href="<?= demo_e(DP_BASE . 'carta.php#plato-' . $id) ?>">
          <span class="dp-firma__num dp-mono">N.º <?= demo_e($p['num']) ?></span>
          <span class="dp-firma__foto"><?= demo_img($p['foto'], $p['nombre'], ['ancho' => 600, 'alto' => 600, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 760px) 86vw, 30vw']) ?></span>
          <span class="dp-firma__nombre"><?= demo_e($p['nombre']) ?></span>
          <span class="dp-firma__desc"><?= demo_e($p['desc']) ?></span>
          <span class="dp-firma__precio dp-mono">desde <?= demo_e(demo_clp(dp_desde($p))) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="dp-seccion dp-seccion--mayo" aria-labelledby="estilos-titulo">
    <div class="dp-ancho">
      <header class="dp-encabezado">
        <p class="dp-mono dp-sobre">Cómo se arma</p>
        <h2 id="estilos-titulo">Elige el estilo</h2>
        <p class="dp-encabezado__texto">Todo sánguche y completo se pide en uno de estos estilos. En la carta lo eliges con un toque.</p>
      </header>
      <ul class="dp-estilos">
        <?php foreach ($n['estilos'] as $nombre => [$detalle, $clave]): ?>
        <li class="dp-estilo dp-estilo--<?= demo_e($clave) ?>">
          <span class="dp-estilo__capas" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
          <h3><?= demo_e($nombre) ?></h3>
          <p><?= demo_e($detalle) ?></p>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="dp-seccion dp-negro" aria-labelledby="plancha-titulo">
    <div class="dp-ancho dp-dos">
      <figure class="dp-marco">
        <?= demo_img($n['fotos']['plancha'], 'Carne laminada dorándose sobre la plancha, con pinzas', ['ancho' => 800, 'alto' => 800, 'sizes' => '(max-width: 860px) 90vw, 45vw']) ?>
      </figure>
      <div>
        <p class="dp-mono dp-sobre">La plancha</p>
        <h2 id="plancha-titulo">Dos metros de fierro caliente</h2>
        <p>Abrimos en 2019 con una idea fija: que el churrasco vuelva a ser como el de las fuentes de soda de antes. Carne laminada al momento, pan tostado en la misma plancha y palta molida cada hora.</p>
        <a class="dp-enlace" href="<?= demo_e(DP_BASE . 'plancha.php') ?>">Conoce la plancha</a>
      </div>
    </div>
  </section>

  <section class="dp-seccion dp-palta" aria-labelledby="pedir-titulo">
    <div class="dp-ancho dp-pedir">
      <div>
        <p class="dp-mono dp-sobre">Para llevar o despacho</p>
        <h2 id="pedir-titulo">Arma tu comanda</h2>
        <p>Elige en la carta, agrega lo que quieras y envía el pedido por WhatsApp. Despachamos en <?= demo_e($n['comuna']) ?> y comunas cercanas desde <?= demo_e(demo_clp(min($n['comunas_despacho']))) ?>.</p>
        <p class="dp-pequeno">También estamos en las principales apps de delivery.</p>
      </div>
      <div class="dp-acciones">
        <a class="dp-btn dp-btn--negro" href="<?= demo_e(DP_BASE . 'carta.php') ?>">Ir a la carta</a>
        <a class="dp-btn dp-btn--linea-negro" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel(18) ?> Llamar</a>
      </div>
    </div>
  </section>

  <div class="dp-seccion">
    <div class="dp-ancho">
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
<?php dp_cerrar();
