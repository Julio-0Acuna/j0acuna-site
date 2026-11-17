<?php
declare(strict_types=1);

// Ayudas para las páginas del portafolio sobre los ejemplos (/demos/ y /demos/<rubro>/),
// que usan los partials del portafolio (includes/partials).
require_once dirname(__DIR__, 2) . '/includes/view.php';

/** <link> a los estilos de estas páginas, con versión de caché. */
function demos_css_portafolio(): string
{
    $archivo = __DIR__ . '/portafolio.css';
    return '<link rel="stylesheet" href="/demos/_comun/portafolio.css?v=' . (is_file($archivo) ? filemtime($archivo) : '1') . '">';
}
