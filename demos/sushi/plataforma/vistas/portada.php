<?php
/** Portada de Nori Club en cajas tipo bento (reemplaza la del motor). @var array $cfg @var ?array $usuario @var string $q @var string $cat @var array $categorias */
if ($q !== '' || $cat !== '') {
    return;
}
$estado = demo_horario_estado($cfg['negocio']['horario']);
$desde = min($cfg['comunas']);
// El combo de la portada sale del catálogo (si el dueño cambia su precio, aquí se ve).
$combo = null;
foreach ($destacados ?? [] as $p) {
    if ($p['categoria_slug'] === 'combos') {
        $combo = $p;
        break;
    }
}
// Atajos de categoría con su foto (las de la carta).
$atajos = [
    'combos' => ['1788016634163-77b7c547b9d4', 'Combos'],
    'rolls' => ['1768326119762-20c2a9f5c1f2', 'Rolls'],
    'nigiri' => ['1641932982747-bd7be260a040', 'Nigiris'],
    'calientes' => ['1638866281450-3933540af86a', 'Ramen y gyozas'],
    'poke' => ['1768326119213-e0ad875083a3', 'Poke'],
    'bebidas' => ['1717603545758-88cc454db69b', 'Bebidas'],
];
?>
<section class="nc-bento" aria-labelledby="nc-titulo">
  <div class="nc-caja nc-caja--principal">
    <p class="nc-chip <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><?= m_e($estado['texto']) ?></p>
    <h1 id="nc-titulo">Sushi a domicilio en <span>Las Condes</span></h1>
    <p>Rolls, combos para 2 y 4, ramen y poke. Pide en la web, paga en línea o al recibir y sigue tu pedido hasta la puerta. Despacho desde <?= m_e(m_clp((int) $desde)) ?>, gratis sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?>.</p>
    <div class="nc-caja__acciones">
      <a class="t-btn nc-btn-salmon" href="#menu">Pedir ahora</a>
      <?php if (!$usuario): ?><a class="t-btn nc-btn-linea" href="<?= m_e(m_url('ingresar')) ?>">Probar con una cuenta</a><?php endif; ?>
    </div>
  </div>
  <a class="nc-caja nc-caja--foto" href="<?= m_e(m_url('inicio', ['cat' => 'combos'])) ?>#menu">
    <?= demo_img('1774635800472-41eaa93c1453', 'Bandeja de delivery con rolls de salmón, palta y queso crema', ['lazy' => false, 'ancho' => 900, 'alto' => 600, 'anchos' => [480, 900, 1200], 'sizes' => '(max-width: 860px) 92vw, 46vw']) ?>
    <span class="nc-caja__etiqueta">
      <?php if ($combo): ?><small><?= m_e($combo['nombre']) ?></small><strong><?= m_e(m_clp((int) $combo['precio_final'])) ?></strong>
      <?php else: ?><small>Combos para 2 y 4</small><strong>Ver combos</strong><?php endif; ?>
    </span>
  </a>
  <div class="nc-caja nc-caja--salmon">
    <p class="nc-caja__grande">35<small>min</small></p>
    <p>Promedio de entrega en Las Condes. Sigue tu pedido: recibido, enrollando, en camino.</p>
  </div>
  <a class="nc-caja nc-caja--wasabi" href="<?= m_e(m_es_mayorista($usuario) ? m_url('inicio', ['cat' => 'empresas']) . '#menu' : m_url('registro')) ?>">
    <p class="nc-caja__sobre">Empresas y eventos</p>
    <p class="nc-caja__titulo">Bandejas de 100 a 200 piezas</p>
    <span class="nc-caja__flecha" aria-hidden="true">→</span>
  </a>
</section>

<nav class="nc-atajos" aria-label="Ir a una categoría">
  <?php foreach ($atajos as $slug => [$foto, $nombre]): ?>
  <a href="<?= m_e(m_url('inicio', ['cat' => $slug])) ?>#menu">
    <?= demo_img($foto, '', ['ancho' => 160, 'alto' => 160, 'anchos' => [160, 320], 'sizes' => '72px']) ?>
    <span><?= m_e($nombre) ?></span>
  </a>
  <?php endforeach; ?>
</nav>
