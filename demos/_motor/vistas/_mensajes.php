<?php /* Mensajes flash (ok, error, info). */ ?>
<?php foreach (m_flashes() as [$tipo, $texto]): ?>
<p class="demo-mensaje demo-mensaje--<?= m_e(in_array($tipo, ['ok', 'error', 'info'], true) ? $tipo : 'info') ?>" role="<?= $tipo === 'error' ? 'alert' : 'status' ?>"><?= m_e($texto) ?></p>
<?php endforeach; ?>
