<?php
/**
 * Buscador de Cardán: por patente o por vehículo (tipo, año y motor). GET a la portada (#menu).
 * @var array $cfg @var array $veh (cd_vehiculo) @var string $modo 'portada' | 'lado'
 */
require_once __DIR__ . '/_vehiculo.php';
$modo ??= 'portada';
$id = static fn (string $campo): string => 'cd-' . $modo . '-' . $campo;
$ejemplos = array_slice(array_keys($cfg['patentes_demo'] ?? []), 0, 3);
?>
<div class="cd-buscador cd-buscador--<?= m_e($modo) ?>">
  <form class="cd-buscador__patente" method="get" action="<?= m_e($cfg['url_base']) ?>#menu" role="search" aria-label="Buscar por patente">
    <label for="<?= m_e($id('patente')) ?>" class="cd-buscador__etiqueta">Por patente</label>
    <div class="cd-placa">
      <span class="cd-placa__pais" aria-hidden="true">CHILE</span>
      <input id="<?= m_e($id('patente')) ?>" name="patente" maxlength="9" autocomplete="off" autocapitalize="characters" spellcheck="false" placeholder="BB CD 12" value="<?= m_e($veh['patente_previa'] !== '' ? $veh['patente_previa'] : ($veh['patente'] !== '' ? rp_patente_formato($veh['patente']) : '')) ?>">
      <button class="cd-placa__btn" type="submit">Buscar</button>
    </div>
    <?php if ($ejemplos): ?>
    <p class="cd-buscador__ejemplos">Patentes de ejemplo:
      <?php foreach ($ejemplos as $i => $p): ?><a href="<?= m_e(m_url('inicio', ['patente' => $p])) ?>#menu"><?= m_e(rp_patente_formato($p)) ?></a><?= $i < count($ejemplos) - 1 ? ' · ' : '' ?><?php endforeach; ?>
    </p>
    <?php endif; ?>
  </form>
  <form class="cd-buscador__vehiculo" method="get" action="<?= m_e($cfg['url_base']) ?>#menu" aria-label="Buscar por vehículo">
    <p class="cd-buscador__etiqueta">O por vehículo</p>
    <div class="cd-buscador__campos">
      <div class="t-campo">
        <label for="<?= m_e($id('tipo')) ?>">Tipo</label>
        <select id="<?= m_e($id('tipo')) ?>" name="tipo">
          <option value="">Todos</option>
          <?php foreach (RP_TIPOS as $clave => $nombre): ?><option value="<?= m_e($clave) ?>"<?= $veh['tipo'] === $clave ? ' selected' : '' ?>><?= m_e($nombre) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="t-campo">
        <label for="<?= m_e($id('anio')) ?>">Año</label>
        <input id="<?= m_e($id('anio')) ?>" name="anio" inputmode="numeric" maxlength="4" placeholder="2016" autocomplete="off" value="<?= $veh['anio'] > 0 ? (int) $veh['anio'] : '' ?>">
      </div>
      <div class="t-campo">
        <label for="<?= m_e($id('cc')) ?>">Motor</label>
        <select id="<?= m_e($id('cc')) ?>" name="cc">
          <option value="">Todos</option>
          <?php foreach (CD_CILINDRADAS as $cc): ?><option value="<?= m_e($cc) ?>"<?= $veh['cc'] === $cc ? ' selected' : '' ?>><?= m_e($cc) ?></option><?php endforeach; ?>
        </select>
      </div>
      <button class="t-btn cd-btn-verde" type="submit">Ver lo que calza</button>
    </div>
  </form>
</div>
