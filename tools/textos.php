<?php
// Uso: tools/php.sh tools/textos.php > ../docs/textos-para-revisar.md
// Exporta perfil, servicios y proyectos en Markdown para que Julio los revise.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/view.php';

$s = portfolio_load_site();
$md = static fn (string $t): string => str_replace(["\r", "\n", '|'], [' ', ' ', '\|'], $t);

echo "# Textos del portafolio para revisar\n\n";
echo "Marca lo que quieras cambiar (o escríbelo en el chat). Nada de esto está publicado todavía.\n\n";

echo "## Perfil\n\n";
foreach (['role' => 'Rol', 'headline' => 'Titular', 'bio' => 'Presentación', 'tagline' => 'Descripción para Google', 'contact_text' => 'Texto de contacto', 'whatsapp_mensaje' => 'Mensaje de WhatsApp'] as $k => $label) {
    echo "- **$label:** " . $md($s['profile'][$k]) . "\n";
}
echo "- **WhatsApp:** {$s['links']['whatsapp']} · **Correo:** {$s['links']['email']}\n\n";

echo "## Servicios\n\n";
foreach (portfolio_visible_services($s, 'todos') as $srv) {
    echo "### {$srv['titulo']}\n";
    echo "_Para: {$srv['para']} · nivel: " . ($srv['nivel'] ?: '—') . "_\n\n";
    echo $srv['resumen'] . "\n\n";
    foreach ($srv['puntos'] as $p) {
        echo "- $p\n";
    }
    echo "\n";
}

echo "## Proyectos publicados\n\n";
foreach (portfolio_visible_projects($s) as $p) {
    $marcas = ($p['destacado'] ? '★ destacado · ' : '') . ($p['en_restaurantes'] ? '🍽 en /restaurantes · ' : '');
    echo "### {$p['orden']}. {$p['title']}\n";
    echo "_{$marcas}" . category_name($p['categoria_id']) . ' · ' . ($p['mostrar_cliente'] && $p['cliente'] ? $p['cliente'] : 'Cliente confidencial') . ($p['credito'] ? " · {$p['credito']}" : '') . "_\n\n";
    echo "**Descripción:** {$p['description']}\n\n";
    foreach (['problema' => 'El problema', 'solucion' => 'La solución', 'resultado' => 'El resultado'] as $k => $label) {
        if ($p[$k] !== '') {
            echo "**$label:** {$p[$k]}\n\n";
        }
    }
    if ($p['funcionalidades']) {
        echo "**Qué incluye:** " . implode(' · ', $p['funcionalidades']) . "\n\n";
    }
}

echo "## Ocultos (no se publican)\n\n";
foreach ($s['projects'] as $p) {
    if (!$p['published']) {
        echo "- {$p['title']}\n";
    }
}
