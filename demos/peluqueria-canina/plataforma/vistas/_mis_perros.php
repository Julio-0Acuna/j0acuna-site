<?php
/**
 * "Tus perros" en Mi cuenta: la ficha de cada perro armada con sus reservas (campos extra del checkout).
 * Agrupa por el campo "Mascota" y muestra tamaño, raza, pelo, visitas, último servicio, la próxima hora
 * agendada y una fecha sugerida para el siguiente baño (4 semanas con pelo largo o rizado, 6 con el resto).
 * @var array $pedidos (m_pedidos_de, del más nuevo al más antiguo) @var array $cfg
 */
$campoNombre = 'Mascota';
$perros = [];
foreach ($pedidos as $p) {
    $extras = [];
    foreach (m_pedido_extras($p) as [$etiqueta, $valor]) {
        $extras[$etiqueta] = $valor;
    }
    $nombre = trim($extras[$campoNombre] ?? '');
    if ($nombre === '' || $p['estado'] === 'cancelado') {
        continue;
    }
    $clave = mb_strtolower($nombre);
    $perros[$clave] ??= ['nombre' => $nombre, 'ficha' => $extras, 'visitas' => 0, 'ultimo' => null, 'proximo' => null];
    $perro = &$perros[$clave];
    if ($p['estado'] === 'entregado') {
        $perro['visitas']++;
        $perro['ultimo'] ??= $p; // el primero que aparece es el más reciente
    } elseif (($p['fecha_entrega'] ?? '') !== '') {
        $perro['proximo'] = $p; // queda el más antiguo de los pendientes: la próxima hora
    }
    unset($perro);
}
if (!$perros) {
    return;
}
$tz = new DateTimeZone($cfg['zona_horaria']);
?>
<section class="t-caja tr-perros" aria-labelledby="tr-perros-titulo">
  <h2 id="tr-perros-titulo">Tus perros</h2>
  <p class="t-pequeno">Su ficha se arma sola con cada reserva: así el furgón llega sabiendo cómo es y qué corte le gusta.</p>
  <ul class="tr-perros__lista">
    <?php foreach ($perros as $perro): $f = $perro['ficha']; $ultimo = $perro['ultimo']; ?>
    <li class="tr-perro">
      <div class="tr-perro__cabeza">
        <span class="tr-perro__inicial" aria-hidden="true"><?= m_e(mb_strtoupper(mb_substr($perro['nombre'], 0, 1))) ?></span>
        <div>
          <h3><?= m_e($perro['nombre']) ?></h3>
          <p><?= m_e(implode(' · ', array_filter([$f['Raza'] ?? '', $f['Tamaño'] ?? '', isset($f['Pelo']) ? 'pelo ' . mb_strtolower($f['Pelo']) : '']))) ?></p>
        </div>
      </div>
      <dl class="tr-perro__datos">
        <div><dt>Visitas</dt><dd><?= (int) $perro['visitas'] ?></dd></div>
        <?php if ($ultimo !== null && ($ultimo['fecha_entrega'] ?? '') !== ''): ?>
        <?php
        $completo = m_pedido(m_db(), (int) $ultimo['id']);
        $servicios = $completo ? array_map(static fn (array $i): string => (string) $i['nombre'], $completo['items']) : [];
        $semanas = in_array($f['Pelo'] ?? '', ['Largo', 'Rizado o lanudo'], true) ? 4 : 6;
        $sugerido = (new DateTimeImmutable($ultimo['fecha_entrega'], $tz))->modify('+' . ($semanas * 7) . ' days');
        ?>
        <div><dt>Último baño</dt><dd><?= m_e(m_fecha_texto($ultimo['fecha_entrega'], false)) ?></dd></div>
        <div class="tr-perro__ancho"><dt>Qué le hicimos</dt><dd><?= m_e(implode(', ', $servicios)) ?></dd></div>
        <?php if ($perro['proximo'] === null): ?>
        <div class="tr-perro__ancho"><dt>Próximo baño sugerido</dt><dd>Desde el <?= m_e(demo_fecha_larga($sugerido)) ?> (cada <?= $semanas ?> semanas)</dd></div>
        <?php endif; ?>
        <?php endif; ?>
        <?php if ($perro['proximo'] !== null): ?>
        <div class="tr-perro__ancho tr-perro__agendado"><dt>Próxima hora</dt><dd><?= m_e(m_fecha_texto($perro['proximo']['fecha_entrega'], false)) ?><?= ($perro['proximo']['cuando'] ?? '') !== '' ? ' a las ' . m_e($perro['proximo']['cuando']) : '' ?> · <?= m_e(m_estado_texto($perro['proximo']['estado'])) ?></dd></div>
        <?php endif; ?>
      </dl>
      <?php if ($ultimo !== null): ?>
      <form method="post" action="<?= m_e(m_url('cuenta')) ?>">
        <?= m_csrf_campo() ?>
        <input type="hidden" name="accion" value="repetir">
        <input type="hidden" name="pedido_id" value="<?= (int) $ultimo['id'] ?>">
        <button class="t-btn t-btn--chico tr-btn-girasol" type="submit">Agendar lo mismo para <?= m_e($perro['nombre']) ?></button>
      </form>
      <?php endif; ?>
    </li>
    <?php endforeach; ?>
  </ul>
</section>
