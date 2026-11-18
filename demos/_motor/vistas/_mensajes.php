<?php /* Mensajes flash (ok, error, info) y errores del formulario que la vista no mostró junto a su campo (m_errores_pendientes). */ ?>
<?php foreach (m_flashes() as [$tipo, $texto]): ?>
<p class="demo-mensaje demo-mensaje--<?= m_e(in_array($tipo, ['ok', 'error', 'info'], true) ? $tipo : 'info') ?>" role="<?= $tipo === 'error' ? 'alert' : 'status' ?>"><?= m_e($texto) ?></p>
<?php endforeach; ?>
<?php foreach (m_errores_pendientes() as $texto): ?>
<p class="demo-mensaje demo-mensaje--error" role="alert"><?= m_e($texto) ?></p>
<?php endforeach; ?>
