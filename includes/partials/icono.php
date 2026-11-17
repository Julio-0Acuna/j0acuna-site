<?php
/**
 * Ícono SVG en línea, de trazo, que toma el color del texto.
 * @var string $icono (nombre del ícono) @var int $tam (opcional, 24) @var string $clase (opcional)
 */
$tam = (int) ($tam ?? 24);
$trazos = [
    'check' => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
    'pin' => '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
    'chat' => '<path d="M20.5 11.5a8.5 8.5 0 0 1-12.3 7.6L3.5 20.5l1.4-4.4A8.5 8.5 0 1 1 20.5 11.5z"/><path d="M8.5 11.5h.01M12 11.5h.01M15.5 11.5h.01"/>',
    'celular' => '<rect x="6.5" y="2.5" width="11" height="19" rx="2.5"/><path d="M10.5 18.5h3"/>',
    'pantalla' => '<rect x="2.5" y="4" width="19" height="12.5" rx="2"/><path d="M8 20.5h8M12 16.5v4"/>',
    'web' => '<rect x="2.5" y="4" width="19" height="16" rx="2.5"/><path d="M2.5 9h19M6 6.5h.01M8.5 6.5h.01"/>',
    'servidor' => '<rect x="3.5" y="3.5" width="17" height="7" rx="1.8"/><rect x="3.5" y="13.5" width="17" height="7" rx="1.8"/><path d="M7.5 7h.01M7.5 17h.01M11 7h5.5M11 17h5.5"/>',
    'datos' => '<ellipse cx="12" cy="5.5" rx="7.5" ry="2.8"/><path d="M4.5 5.5v13c0 1.5 3.4 2.8 7.5 2.8s7.5-1.3 7.5-2.8v-13"/><path d="M4.5 12c0 1.5 3.4 2.8 7.5 2.8s7.5-1.3 7.5-2.8"/>',
    'enchufe' => '<path d="M9 2.5v5M15 2.5v5"/><path d="M6 7.5h12v3.5a6 6 0 0 1-12 0z"/><path d="M12 17v4.5"/>',
    'chispa' => '<path d="M11 3l1.9 5.1L18 10l-5.1 1.9L11 17l-1.9-5.1L4 10l5.1-1.9z"/><path d="M18.5 14.5l.8 2.2 2.2.8-2.2.8-.8 2.2-.8-2.2-2.2-.8 2.2-.8z"/>',
    'pausa' => '<path d="M9 5v14M15 5v14"/>',
    'play' => '<path d="M7 4.5v15l12-7.5z"/>',
    'izquierda' => '<path d="M15 5l-7 7 7 7"/>',
    'derecha' => '<path d="M9 5l7 7-7 7"/>',
];
$trazo = $trazos[$icono ?? ''] ?? '';
if ($trazo !== ''):
?><svg class="icono<?= !empty($clase) ? ' ' . e($clase) : '' ?>" viewBox="0 0 24 24" width="<?= $tam ?>" height="<?= $tam ?>" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><?= $trazo ?></svg><?php
endif;
