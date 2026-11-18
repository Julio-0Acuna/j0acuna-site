<?php
/** Portada de Hocico (reemplaza la del motor). @var array $cfg @var ?array $usuario @var string $q @var string $cat @var array $categorias */
if ($q !== '' || $cat !== '') {
    return;
}
$estado = demo_horario_estado($cfg['negocio']['horario']);
$fechas = m_fechas_entrega($cfg);
$horas = m_horas_entrega($cfg);
$proxima = $fechas ? reset($fechas) : '';
$ir = static fn (string $categoria): string => m_url('inicio', ['cat' => $categoria]) . '#menu';
// Accesos por mascota: [categoría, título, texto, foto, color].
$accesos = [
    ['perros', 'Perros', 'Alimento, premios, camas y paseo', '1583511655826-05700d52f4d9', 'rosa'],
    ['gatos', 'Gatos', 'Alimento, arena y rascadores', '1736604749191-266e9bdbb2ad', 'sol'],
    ['clinica', 'Clínica', 'Consultas, vacunas y esterilización', '1770836037289-e00e5f351d11', 'menta'],
    ['peluqueria', 'Peluquería', 'Baño, corte y uñas con hora', '1719464454959-9cf304ef4774', 'lila'],
];
?>
<section class="ho-hero" aria-labelledby="ho-hero-titulo">
  <div class="ho-hero__texto">
    <p class="ho-chip<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span><?= m_e($estado['texto']) ?></p>
    <h1 id="ho-hero-titulo">Todo para tu mascota. <span>Y su veterinaria.</span></h1>
    <p>Alimento, arena y accesorios con despacho en <?= m_e($cfg['negocio']['comuna']) ?> y alrededores, y hora en la clínica para vacunas, controles y peluquería. Todo en el mismo carrito.</p>
    <div class="ho-hero__acciones">
      <a class="t-btn t-btn--primario ho-btn-grande" href="<?= m_e($ir('clinica')) ?>">Agendar una hora</a>
      <a class="t-btn ho-btn-borde" href="<?= m_e($ir('perros')) ?>">Comprar alimento</a>
    </div>
    <ul class="ho-confianza">
      <li>Despacho al día siguiente</li>
      <li>Retiro gratis en tienda</li>
      <li><?= $usuario ? 'Repite tu pedido en un clic' : 'Paga en línea o al recibir' ?></li>
    </ul>
  </div>
  <div class="ho-hero__collage">
    <figure class="ho-baldosa ho-baldosa--a"><?= demo_img('1625316708582-7c38734be31d', 'Perro schnauzer gris lamiéndose el hocico sobre un fondo amarillo', ['lazy' => false, 'ancho' => 700, 'alto' => 800, 'sizes' => '(max-width: 900px) 56vw, 330px', 'anchos' => [400, 700, 1000]]) ?></figure>
    <figure class="ho-baldosa ho-baldosa--b"><?= demo_img('1592194996308-7b43878e84a6', 'Gatito de pelo largo estirando la pata hacia arriba sobre un fondo turquesa', ['lazy' => false, 'ancho' => 500, 'alto' => 500, 'sizes' => '(max-width: 900px) 40vw, 230px', 'anchos' => [300, 500, 800]]) ?></figure>
    <figure class="ho-baldosa ho-baldosa--c"><?= demo_img('1637211474672-100c8d149802', 'Perro pastor de pelo café y blanco con la lengua afuera sobre un fondo malva', ['ancho' => 500, 'alto' => 500, 'sizes' => '(max-width: 900px) 40vw, 230px', 'anchos' => [300, 500, 800]]) ?></figure>
    <?php if ($proxima !== '' && $horas): ?>
    <p class="ho-ticket"><span>Próxima hora en la clínica</span><strong><?= m_e($proxima) ?></strong><small>desde las <?= m_e($horas[0]) ?></small></p>
    <?php endif; ?>
    <p class="ho-sello" aria-hidden="true">guau<br>miau</p>
  </div>
</section>

<nav class="ho-accesos" aria-label="Compra por mascota o servicio">
  <?php foreach ($accesos as [$slug, $titulo, $texto, $foto, $color]): ?>
  <a class="ho-acceso ho-acceso--<?= m_e($color) ?>" href="<?= m_e($ir($slug)) ?>">
    <span class="ho-acceso__texto"><strong><?= m_e($titulo) ?></strong><span><?= m_e($texto) ?></span></span>
    <span class="ho-acceso__foto" aria-hidden="true"><?= demo_img($foto, '', ['ancho' => 240, 'alto' => 240, 'anchos' => [160, 240, 400], 'sizes' => '96px']) ?></span>
  </a>
  <?php endforeach; ?>
</nav>

<ol class="ho-pasos" aria-label="Cómo agendar una hora en Hocico">
  <li><span class="ho-pasos__num" aria-hidden="true">1</span><div><strong>Elige el servicio</strong><span>Vacuna, control o peluquería, con precio a la vista. Suma el alimento si quieres.</span></div></li>
  <li><span class="ho-pasos__num" aria-hidden="true">2</span><div><strong>Día y hora</strong><span>De lunes a sábado, cada 30 minutos, con el nombre, la especie y la edad de tu mascota.</span></div></li>
  <li><span class="ho-pasos__num" aria-hidden="true">3</span><div><strong>Paga como quieras</strong><span>En línea o en la tienda. Te llega el resumen por WhatsApp.</span></div></li>
</ol>

<p class="ho-aviso-farmacia"><strong>Farmacia responsable:</strong> los antiparasitarios y medicamentos requieren evaluación veterinaria. Se entregan después de un control y nunca indicamos dosis por la web.</p>

<?php if (!m_es_mayorista($usuario) && !m_es_dueno($usuario)): ?>
<section class="ho-refugio" aria-labelledby="ho-refugio-titulo">
  <?= demo_img('1548199973-03cce0bbc87b', 'Dos perros corriendo felices por un camino de tierra al atardecer', ['ancho' => 800, 'alto' => 560, 'sizes' => '(max-width: 900px) 100vw, 44vw', 'clase' => 'ho-refugio__foto']) ?>
  <div class="ho-refugio__texto">
    <p class="ho-refugio__sobre">Refugios y criaderos</p>
    <h2 id="ho-refugio-titulo"><?= m_e(m_texto('mayorista_titulo')) ?></h2>
    <p>Sacos de alimento y arena por volumen con despacho al refugio, jornadas de vacunación y esterilización por convenio con precio social. Tu cuenta queda activa cuando la aprobamos, normalmente el mismo día.</p>
    <a class="t-btn t-btn--primario" href="<?= m_e(m_url('registro')) ?>"><?= m_e(m_texto('crear_cuenta_mayorista')) ?></a>
  </div>
</section>
<?php endif; ?>
