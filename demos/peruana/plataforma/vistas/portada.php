<?php
/** Portada de Causa Común (reemplaza la del motor). @var array $cfg @var ?array $usuario @var string $q @var string $cat */
if ($q !== '' || $cat !== '') {
    return;
}
$desde = min($cfg['comunas']);
// Próxima entrega: el primer día que ofrece el checkout (el motor saca "Hoy" cuando ya no quedan horas).
$fechas = m_fechas_entrega($cfg);
$primera = (string) array_key_first($fechas);
$esEmpresa = m_es_mayorista($usuario);
?>
<section class="cc-hero" aria-labelledby="cc-hero-titulo">
  <div class="cc-hero__texto">
    <p class="cc-etiqueta">Cocina peruana · <?= m_e($cfg['negocio']['comuna']) ?></p>
    <h1 id="cc-hero-titulo">Comida peruana para tu casa <span>y tu oficina</span></h1>
    <p class="cc-hero__bajada">Ceviche, lomo saltado, chaufa y causas hechos cada día. Pide para hoy o deja tu almuerzo agendado hasta con 6 días, con despacho desde <?= m_e(m_clp((int) $desde)) ?> y gratis sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?>.</p>
    <div class="cc-hero__acciones">
      <a class="t-btn t-btn--primario cc-btn-grande" href="#menu">Pedir ahora</a>
      <?php if (!$usuario): ?><a class="t-btn cc-btn-linea" href="<?= m_e(m_url('ingresar')) ?>">Probar con una cuenta</a><?php endif; ?>
    </div>
    <?php if ($primera !== ''): ?>
    <p class="cc-hero__proximo"><span aria-hidden="true">●</span> Próxima entrega: <strong><?= m_e($fechas[$primera]) ?></strong></p>
    <?php endif; ?>
  </div>
  <div class="cc-hero__plato">
    <div class="cc-hero__capas" aria-hidden="true"><span></span><span></span><span></span></div>
    <figure class="cc-hero__foto">
      <?= demo_img('1766456127047-806cdecdc139', 'Causa limeña con crema de ají amarillo, choclo y palta', ['lazy' => false, 'ancho' => 900, 'alto' => 900, 'sizes' => '(max-width: 860px) 80vw, 440px']) ?>
    </figure>
    <p class="cc-hero__sello"><strong>Causa</strong> limeña<span><?= m_e(m_clp(5990)) ?></span></p>
  </div>
</section>

<section class="cc-puertas" aria-label="Dos formas de pedir">
  <a class="cc-puerta cc-puerta--casa" href="#menu">
    <span class="cc-puerta__sobre">Para la casa</span>
    <strong>El almuerzo o la once, hoy mismo</strong>
    <span>Elige tus platos, el día y la hora. Paga en línea o al recibir y sigue tu pedido: recibido, preparando, en camino.</span>
    <span class="cc-puerta__ir">Ver la carta</span>
  </a>
  <a class="cc-puerta cc-puerta--oficina" id="empresas" href="<?= m_e($esEmpresa ? m_url('inicio', ['cat' => 'empresas']) . '#menu' : m_url('registro')) ?>">
    <span class="cc-puerta__sobre">Para la oficina</span>
    <strong>Colaciones desde <?= m_e(m_clp(5490)) ?> con cuenta empresa</strong>
    <span>Packs de 10 colaciones, bandejas para reuniones y bidones de chicha. Agenda la semana y repite el pedido en un toque.</span>
    <span class="cc-puerta__ir"><?= $esEmpresa ? 'Ver productos para empresas' : m_e(m_texto('crear_cuenta_mayorista')) ?></span>
  </a>
</section>

<ol class="cc-pasos" aria-label="Cómo funciona">
  <li><strong>Elige el día y la hora</strong><span>Hoy o hasta 6 días más, de lunes a sábado.</span></li>
  <li><strong>Paga como quieras</strong><span>En línea (en la demo, de prueba) o al recibir.</span></li>
  <li><strong>Síguelo en tu cuenta</strong><span>Y repítelo la próxima semana con un clic.</span></li>
</ol>
