<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = al();
$ocasion = is_string($_GET['ocasion'] ?? null) && isset($n['ocasiones'][$_GET['ocasion']]) ? $_GET['ocasion'] : '';
$numeros = array_flip(array_keys($n['tortas']));
$tortas = array_filter($n['tortas'], static fn (array $t): bool => $ocasion === '' || in_array($ocasion, $t['ocasiones'], true));

al_abrir('tortas', 'Colección de tortas de autor', 'Galería de tortas de ' . $n['nombre'] . ': higos y miel de ulmo, chocolate y maqui, lúcuma, pavlova y tortas de matrimonio, con precios por tamaño y vista en 3D. Demo de pastelería en ' . $n['comuna'] . '.');
al_titular('La colección', 'Tortas de autor, <em>pieza por pieza</em>', 'Cada torta nace de una fruta chilena de temporada. Elige la tuya y encárgala, o toca <strong>Ver en 3D</strong> para girarla y verla sobre tu mesa desde el celular.');
?>
<main id="contenido">
  <section class="al-seccion al-seccion--sin-arriba">
    <div class="al-ancho">
      <nav class="al-filtros" aria-label="Filtrar por ocasión">
        <a href="<?= demo_e(AL_BASE . 'tortas.php') ?>"<?= $ocasion === '' ? ' aria-current="page" class="is-activo"' : '' ?>>Todas</a>
        <?php foreach ($n['ocasiones'] as $clave => $nombre): ?>
        <a href="<?= demo_e(AL_BASE . 'tortas.php?ocasion=' . $clave) ?>"<?= $ocasion === $clave ? ' aria-current="page" class="is-activo"' : '' ?>><?= demo_e($nombre) ?></a>
        <?php endforeach; ?>
      </nav>
      <p class="al-cuenta" role="status"><?= count($tortas) ?> <?= count($tortas) === 1 ? 'torta' : 'tortas' ?><?= $ocasion !== '' ? ' para ' . demo_e(mb_strtolower($n['ocasiones'][$ocasion])) : ' en la colección' ?></p>

      <div class="al-galeria">
        <?php foreach ($tortas as $id => $t): ?>
        <article class="al-pieza" id="torta-<?= demo_e($id) ?>">
          <figure class="al-pieza__foto">
            <?= demo_img($t['foto'], 'Torta ' . $t['nombre'], ['ancho' => 800, 'alto' => 1000, 'anchos' => [480, 800, 1200], 'sizes' => '(max-width: 760px) 92vw, (max-width: 1100px) 46vw, 31vw']) ?>
          </figure>
          <p class="al-numero"><?= sprintf('N.º %02d', $numeros[$id] + 1) ?></p>
          <h2><?= demo_e($t['nombre']) ?></h2>
          <p class="al-notas"><?= demo_e($t['notas']) ?></p>
          <p class="al-pieza__desc"><?= demo_e($t['desc']) ?></p>
          <dl class="al-pieza__precios">
            <?php foreach ($t['precios'] as $tam => $precio): ?>
            <div><dt><?= demo_e($tam) ?></dt><dd><?= demo_e(demo_clp($precio)) ?></dd></div>
            <?php endforeach; ?>
          </dl>
          <div class="al-pieza__acciones">
            <a class="al-btn al-btn--chico" href="<?= demo_e(AL_BASE . 'encargo.php?torta=' . $id) ?>">Encargar<span class="demo-sr"> <?= demo_e($t['nombre']) ?></span></a>
            <?php if ($t['modelo'] !== ''): ?>
            <?= demo_boton_3d(['id' => $id, 'nombre' => $t['nombre'], 'modelo' => $t['modelo'], 'foto' => $t['foto']], 'al-btn-3d') ?>
            <?php endif; ?>
          </div>
          <ul class="al-etiquetas" aria-label="Ocasiones">
            <?php foreach ($t['ocasiones'] as $o): ?><li><?= demo_e($n['ocasiones'][$o]) ?></li><?php endforeach; ?>
          </ul>
        </article>
        <?php endforeach; ?>
      </div>

      <aside class="al-a-medida">
        <div>
          <p class="al-sobre">¿No está la que imaginas?</p>
          <h2>Diseño <em>a medida</em></h2>
          <p>Cuéntanos la ocasión, los sabores y cuántas personas son. Te respondemos por WhatsApp con una propuesta y el precio.</p>
        </div>
        <a class="al-btn" href="<?= demo_e(AL_BASE . 'encargo.php?torta=a-medida') ?>">Pedir un diseño a medida</a>
      </aside>
    </div>
  </section>
</main>
<?php al_cerrar(['3d']);
