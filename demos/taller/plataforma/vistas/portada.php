<?php
/** Portada de Vía Rápida (reemplaza la del motor). @var array $cfg @var ?array $usuario @var string $q @var string $cat @var array $categorias */
if ($q !== '' || $cat !== '') {
    return;
}
$estado = demo_horario_estado($cfg['negocio']['horario']);
$fechas = m_fechas_entrega($cfg);
$horas = m_horas_entrega($cfg);
$proxima = $fechas ? reset($fechas) : '';
// Íconos de las categorías (trazos simples, 24×24).
$iconos = [
    'mantencion' => '<path d="M12 3c3 4 5 6.6 5 9.4A5 5 0 0 1 7 12.4C7 9.6 9 7 12 3z"/><path d="M10 13.5a2 2 0 0 0 2 2"/>',
    'frenos' => '<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="2.5"/><path d="M12 4v3M20 12h-3M12 20v-3M4 12h3"/>',
    'neumaticos' => '<circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4"/><path d="M12 3.5v4.5M12 16v4.5M3.5 12H8M16 12h4.5"/>',
    'diagnostico' => '<rect x="3" y="5" width="18" height="12" rx="1.5"/><path d="M6 13l3-3 2.5 2.5L15 9l3 3M9 20h6"/>',
    'cuidado' => '<path d="M7 3h7l4 4v14H7z"/><path d="M14 3v4h4M10 12l2 2 3.5-3.5"/>',
    'accesorios' => '<path d="M4 8h16l-1.5 11h-13z"/><path d="M9 8a3 3 0 0 1 6 0"/>',
];
?>
<section class="vr-hero" aria-labelledby="vr-hero-titulo">
  <div class="vr-hero__panel">
    <p class="vr-chip<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span><?= m_e($estado['texto']) ?></p>
    <h1 id="vr-hero-titulo">Tu auto listo. <span>Tu día intacto.</span></h1>
    <p>Reserva tu hora en línea, paga online o en el taller y llega directo al box. Mantención, frenos, neumáticos y diagnóstico en <?= m_e($cfg['negocio']['comuna']) ?>, o retiramos tu auto en casa.</p>
    <div class="vr-hero__acciones">
      <a class="t-btn t-btn--primario vr-btn-grande" href="#menu">Reservar hora</a>
      <?php if (!$usuario): ?>
      <a class="t-btn vr-btn-borde" href="<?= m_e(m_url('ingresar')) ?>">Probar con una cuenta</a>
      <?php elseif (!m_es_dueno($usuario)): ?>
      <a class="t-btn vr-btn-borde" href="<?= m_e(m_url('cuenta')) ?>">Mis servicios</a>
      <?php else: ?>
      <a class="t-btn vr-btn-borde" href="<?= m_e(m_url('panel')) ?>">Ir al panel</a>
      <?php endif; ?>
    </div>
  </div>
  <figure class="vr-hero__foto">
    <?= demo_img('1727893119356-1702fe921cf9', 'Centro de servicio amplio e iluminado con autos en los elevadores', ['lazy' => false, 'ancho' => 1000, 'alto' => 760, 'sizes' => '(max-width: 900px) 100vw, 55vw', 'anchos' => [600, 1000, 1400]]) ?>
    <?php if ($proxima !== '' && $horas): ?>
    <figcaption class="vr-ticket">
      <span class="vr-ticket__titulo">Próxima hora disponible</span>
      <strong><?= m_e($proxima) ?></strong>
      <span class="vr-ticket__hora">desde las <?= m_e($horas[0]) ?></span>
    </figcaption>
    <?php endif; ?>
  </figure>
</section>

<ol class="vr-pasos" aria-label="Cómo reservar en Vía Rápida">
  <li><span class="vr-pasos__num" aria-hidden="true">1</span><div><strong>Elige el servicio</strong><span>Precio a la vista para auto mediano. Suma accesorios si quieres.</span></div></li>
  <li><span class="vr-pasos__num" aria-hidden="true">2</span><div><strong>Día y hora</strong><span>De lunes a sábado, cada 30 minutos, hasta tres semanas antes.</span></div></li>
  <li><span class="vr-pasos__num" aria-hidden="true">3</span><div><strong>Paga como quieras</strong><span>En línea o en el taller. Te llega el resumen por WhatsApp.</span></div></li>
</ol>

<nav class="vr-atajos" aria-label="Servicios por categoría">
  <?php foreach ($categorias as $c): ?>
  <?php if (!isset($iconos[$c['slug']])) { continue; } ?>
  <a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu">
    <svg viewBox="0 0 24 24" width="30" height="30" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?= $iconos[$c['slug']] ?></svg>
    <span><?= m_e($c['nombre']) ?></span>
  </a>
  <?php endforeach; ?>
</nav>

<?php if (!m_es_mayorista($usuario) && !m_es_dueno($usuario)): ?>
<section class="vr-flota" aria-labelledby="vr-flota-titulo">
  <?= demo_img('1775865937358-c7d2972e78ec', 'Furgones blancos estacionados en fila, vistos desde el aire', ['ancho' => 800, 'alto' => 500, 'sizes' => '(max-width: 900px) 100vw, 40vw', 'clase' => 'vr-flota__foto']) ?>
  <div class="vr-flota__texto">
    <p class="vr-flota__sobre">Empresas</p>
    <h2 id="vr-flota-titulo"><?= m_e(m_texto('mayorista_titulo')) ?></h2>
    <p>Precios por volumen, revisión antes de ruta en 30 minutos e informe por vehículo para tus furgones y camionetas. Tu cuenta de flota queda activa cuando la aprobamos, normalmente el mismo día.</p>
    <a class="t-btn t-btn--primario" href="<?= m_e(m_url('registro')) ?>"><?= m_e(m_texto('crear_cuenta_mayorista')) ?></a>
  </div>
</section>
<?php endif; ?>
