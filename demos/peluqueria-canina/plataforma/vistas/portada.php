<?php
/**
 * Portada de Tina Rodante (reemplaza la del motor): el furgón, la ruta de la semana y los precios por tamaño.
 * @var array $cfg @var ?array $usuario @var string $q @var string $cat @var array $por_categoria
 */
if ($q !== '' || $cat !== '') {
    return;
}
$estado = demo_horario_estado($cfg['negocio']['horario']);
$fechas = m_fechas_entrega($cfg);
$horas = m_horas_entrega($cfg);
$proxima = $fechas ? reset($fechas) : '';
$hoy = (int) (new DateTimeImmutable('now', new DateTimeZone($cfg['zona_horaria'])))->format('N');
$ir = static fn (string $categoria): string => m_url('inicio', ['cat' => $categoria]) . '#menu';

// Catálogo visible para este cliente, por nombre (la portada solo se muestra sin búsqueda ni categoría; los productos
// no guardan el slug del seed). Si la dueña cambia el nombre de un servicio en el panel, su celda dice "A cotizar".
$porNombre = [];
foreach ($por_categoria as $lista) {
    foreach ($lista as $p) {
        $porNombre[$p['nombre']] = $p;
    }
}
// Tabla de precios por tamaño: fila => [tamaño => nombre del servicio].
$tamanos = ['pequeno' => ['Pequeño', 'hasta 10 kg'], 'mediano' => ['Mediano', '10 a 25 kg'], 'grande' => ['Grande', '25 a 40 kg'], 'gigante' => ['Gigante', 'más de 40 kg']];
$filas = [
    'Baño' => ['detalle' => 'Agua tibia, secado a mano, uñas y colonia', 'servicios' => ['pequeno' => 'Baño · perro pequeño', 'mediano' => 'Baño · perro mediano', 'grande' => 'Baño · perro grande', 'gigante' => 'Baño · perro gigante']],
    'Baño y corte' => ['detalle' => 'El baño más su corte de raza, a máquina y tijera', 'servicios' => ['pequeno' => 'Baño y corte · perro pequeño', 'mediano' => 'Baño y corte · perro mediano', 'grande' => 'Baño y corte · perro grande']],
    'Deslanado' => ['detalle' => 'Se suma al baño: para perros de doble manto', 'sin' => ['pequeno' => 'No aplica'], 'servicios' => ['mediano' => 'Deslanado · perro mediano', 'grande' => 'Deslanado · perro grande o gigante', 'gigante' => 'Deslanado · perro grande o gigante']],
];
$extras = ['Corte higiénico', 'Corte de uñas', 'Limpieza de oídos', 'Spa e hidratación', 'Desmotado (cada 15 min)'];
$agregar = static function (array $p, string $texto, string $clase): string {
    if ((int) $p['stock'] <= 0) {
        return '<span class="tr-agotado">' . m_e(m_texto('producto_agotado')) . '</span>';
    }
    return '<form method="post" action="' . m_e(m_url('carrito')) . '">' . m_csrf_campo()
        . '<input type="hidden" name="accion" value="agregar"><input type="hidden" name="producto_id" value="' . (int) $p['id'] . '">'
        . '<button class="' . m_e($clase) . '" type="submit" aria-label="' . m_e(m_texto('producto_agregar_aria', null, ['producto' => $p['nombre']])) . '">' . $texto . '</button></form>';
};
?>
<section class="tr-hero" aria-labelledby="tr-hero-titulo">
  <div class="tr-hero__texto">
    <p class="tr-chip<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span><?= m_e($estado['texto']) ?></p>
    <h1 id="tr-hero-titulo">La peluquería <span>llega a tu puerta.</span></h1>
    <p>Un furgón con tina, agua tibia y energía propia se estaciona afuera de tu casa. Bañamos y peinamos a tu perro adentro y te lo devolvemos regio en una hora y media, sin viajes ni jaulas.</p>
    <div class="tr-hero__acciones">
      <a class="t-btn tr-btn-girasol" href="#precios">Agendar el furgón</a>
      <a class="t-btn tr-btn-linea" href="<?= m_e($ir('tienda')) ?>">Comprar shampoo</a>
    </div>
    <?php if ($proxima !== '' && $horas): ?>
    <p class="tr-proxima"><span>Próxima hora libre</span><strong><?= m_e($proxima) ?></strong><small>desde las <?= m_e($horas[0]) ?></small></p>
    <?php endif; ?>
  </div>
  <div class="tr-hero__visual">
    <figure class="tr-hero__foto"><?= demo_img('1698949654875-544ecfef27ac', 'Perrito blanco de pelo corto recién peinado, sentado sobre un fondo amarillo', ['lazy' => false, 'ancho' => 800, 'alto' => 900, 'sizes' => '(max-width: 900px) 80vw, 420px', 'anchos' => [480, 800, 1100]]) ?></figure>
    <p class="tr-sticker" aria-hidden="true">guau<br><small>a domicilio</small></p>
  </div>
  <div class="tr-hero__camino" aria-hidden="true"><?php m_parcial('_furgon', ['clase' => 'tr-furgon--hero']); ?></div>
</section>

<section class="tr-ruta" id="ruta" aria-labelledby="tr-ruta-titulo">
  <div class="tr-ruta__cabeza">
    <h2 id="tr-ruta-titulo">La ruta de la semana</h2>
    <p><?php if (!empty($cfg['ruta'][$hoy])): ?>Hoy el furgón anda por <strong><?= m_e(implode(' y ', $cfg['ruta'][$hoy])) ?></strong>.<?php else: ?>Hoy el furgón descansa: agenda para la semana.<?php endif; ?> Si eliges el día de tu comuna, es más fácil encontrar hora.</p>
  </div>
  <ol class="tr-ruta__dias">
    <?php foreach ($cfg['ruta'] as $dia => $comunas): ?>
    <li class="<?= $dia === $hoy ? 'is-hoy' : '' ?>"<?= $dia === $hoy ? ' aria-current="date"' : '' ?>>
      <strong><?= m_e(DEMO_DIAS[$dia]) ?><?= $dia === $hoy ? ' <em>hoy</em>' : '' ?></strong>
      <span><?= m_e(implode(' · ', $comunas)) ?></span>
      <?php if ($dia === 6): ?><small>solo en la mañana</small><?php endif; ?>
    </li>
    <?php endforeach; ?>
  </ol>
</section>

<section class="tr-precios" id="precios" aria-labelledby="tr-precios-titulo">
  <div class="tr-precios__cabeza">
    <p class="tr-sobre">Precios por tamaño</p>
    <h2 id="tr-precios-titulo">Elige el servicio y el tamaño de tu perro</h2>
    <p>El precio incluye todo lo que hacemos en el furgón. El traslado depende de tu comuna y es gratis en reservas sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?>.</p>
  </div>
  <div class="tr-tabla" role="table" aria-label="Precios por servicio y tamaño">
    <div class="tr-tabla__fila tr-tabla__fila--cabeza" role="row">
      <span role="columnheader">Servicio</span>
      <?php foreach ($tamanos as [$nombre, $peso]): ?><span role="columnheader"><?= m_e($nombre) ?><small><?= m_e($peso) ?></small></span><?php endforeach; ?>
    </div>
    <?php foreach ($filas as $servicio => $fila): ?>
    <div class="tr-tabla__fila" role="row">
      <span class="tr-tabla__servicio" role="rowheader"><strong><?= m_e($servicio) ?></strong><small><?= m_e($fila['detalle']) ?></small></span>
      <?php foreach ($tamanos as $tam => [$nombre]): $p = isset($fila['servicios'][$tam]) ? ($porNombre[$fila['servicios'][$tam]] ?? null) : null; ?>
      <span class="tr-tabla__celda" role="cell" data-tam="<?= m_e($nombre) ?>">
        <?php if ($p === null): ?><span class="tr-tabla__no"><?= m_e($fila['sin'][$tam] ?? 'A cotizar') ?></span>
        <?php else: ?><strong class="tr-tabla__precio"><?= m_e(m_clp((int) $p['precio_final'])) ?></strong><?= $agregar($p, 'Agregar', 'tr-mas') ?><?php endif; ?>
      </span>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="tr-extras">
    <p class="tr-extras__titulo">Se suman a cualquier baño</p>
    <ul>
      <?php foreach ($extras as $nombreExtra): if (!isset($porNombre[$nombreExtra])) { continue; } $p = $porNombre[$nombreExtra]; ?>
      <li><span><?= m_e($p['nombre']) ?> <b><?= m_e(m_clp((int) $p['precio_final'])) ?></b></span><?= $agregar($p, '+', 'tr-mas tr-mas--redondo') ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="tr-como" aria-labelledby="tr-como-titulo">
  <h2 id="tr-como-titulo">Así funciona</h2>
  <ol class="tr-pasos">
    <li><span class="tr-pasos__num" aria-hidden="true">1</span><div><strong>Agenda en la web</strong><span>Elige el servicio, el día y la hora, y cuéntanos de tu perro: tamaño, raza y pelo.</span></div></li>
    <li><span class="tr-pasos__num" aria-hidden="true">2</span><div><strong>Te avisamos en camino</strong><span>Media hora antes te escribimos por WhatsApp. Tú solo nos abres la puerta.</span></div></li>
    <li><span class="tr-pasos__num" aria-hidden="true">3</span><div><strong>Baño en el furgón</strong><span>Tina con agua tibia, secado a mano y corte, estacionados afuera de tu casa.</span></div></li>
    <li><span class="tr-pasos__num" aria-hidden="true">4</span><div><strong>De vuelta, regio</strong><span>Te lo entregamos con su pañoleta y queda anotado en su ficha de tu cuenta.</span></div></li>
  </ol>
</section>

<section class="tr-porque" aria-labelledby="tr-porque-titulo">
  <h2 id="tr-porque-titulo" class="demo-sr">Por qué un furgón</h2>
  <article class="tr-razon tr-razon--rosa">
    <?= demo_img('1709771818873-57feeea88f41', 'Perrito con pañoleta floreada sobre un fondo rosado', ['ancho' => 400, 'alto' => 400, 'anchos' => [240, 400, 600], 'sizes' => '120px', 'clase' => 'tr-razon__foto']) ?>
    <div><h3>Sin viajes ni jaulas</h3><p>No sube al auto ni espera con otros perros. Lo atendemos solo, a pasos de su casa.</p></div>
  </article>
  <article class="tr-razon tr-razon--verde">
    <?= demo_img('1678153188688-0dc45722708a', 'Perrito con pañoleta verde sobre un fondo verde', ['ancho' => 400, 'alto' => 400, 'anchos' => [240, 400, 600], 'sizes' => '120px', 'clase' => 'tr-razon__foto']) ?>
    <div><h3>Ideal para mayores y nerviosos</h3><p>Perros de edad, con miedo al secador o que se marean en el auto: aquí todo es más corto y tranquilo.</p></div>
  </article>
  <article class="tr-razon tr-razon--agua">
    <?= demo_img('1678153184494-1f6fc14a673d', 'Perrito feliz con la lengua afuera y una pañoleta verde', ['ancho' => 400, 'alto' => 400, 'anchos' => [240, 400, 600], 'sizes' => '120px', 'clase' => 'tr-razon__foto']) ?>
    <div><h3>No necesitamos nada de tu casa</h3><p>El furgón lleva agua tibia, energía y desagüe propios. Solo un lugar donde estacionarnos.</p></div>
  </article>
</section>

<?php if (!m_es_mayorista($usuario) && !m_es_dueno($usuario)): ?>
<section class="tr-convenio" aria-labelledby="tr-convenio-titulo">
  <div>
    <p class="tr-sobre">Convenios</p>
    <h2 id="tr-convenio-titulo"><?= m_e(m_texto('mayorista_titulo')) ?></h2>
    <p>El furgón se instala un día en tu condominio y baña a seis perros seguidos; los vecinos se inscriben en conserjería. Criaderos y guarderías compran shampoo y toallas por volumen. La cuenta queda activa cuando la aprobamos, normalmente el mismo día.</p>
    <a class="t-btn tr-btn-girasol" href="<?= m_e(m_url('registro')) ?>"><?= m_e(m_texto('crear_cuenta_mayorista')) ?></a>
  </div>
  <?= demo_img('1508814437933-f0c7d18a9217', 'Dos perros con pañoletas azul y blanca sentados en un camino de tierra', ['ancho' => 800, 'alto' => 560, 'sizes' => '(max-width: 900px) 100vw, 44vw', 'clase' => 'tr-convenio__foto']) ?>
</section>
<?php endif; ?>
