<?php
declare(strict_types=1);

// /demos/creditos.php : muestra demos/CREDITOS.md (fotos, modelos 3D y librerías de los ejemplos).
require_once __DIR__ . '/_comun/portafolio.php';

$site = portfolio_load_site();

/** Enlaces, código y URL sueltas dentro de una línea ya escapada. */
function creditos_en_linea(string $texto): string
{
    $html = e($texto);
    $html = preg_replace('/`([^`]+)`/', '<code>$1</code>', $html) ?? $html;
    $html = preg_replace('/\[([^\]]+)\]\((https:\/\/[^)\s]+)\)/', '<a href="$2" rel="noopener">$1</a>', $html) ?? $html;
    return preg_replace('/(?<![">])(https:\/\/[^\s<|]+)/', '<a href="$1" rel="noopener">$1</a>', $html) ?? $html;
}

/** Markdown simple de CREDITOS.md (títulos, párrafos, listas y tablas) a HTML escapado. */
function creditos_html(string $md): string
{
    $out = '';
    $tabla = [];
    $lista = [];
    $cerrar = static function () use (&$out, &$tabla, &$lista): void {
        if ($tabla) {
            $out .= '<div class="tabla"><table><thead><tr>';
            foreach (array_shift($tabla) as $c) {
                $out .= '<th scope="col">' . creditos_en_linea($c) . '</th>';
            }
            $out .= '</tr></thead><tbody>';
            foreach ($tabla as $fila) {
                $out .= '<tr>' . implode('', array_map(static fn (string $c): string => '<td>' . creditos_en_linea($c) . '</td>', $fila)) . '</tr>';
            }
            $out .= '</tbody></table></div>';
            $tabla = [];
        }
        if ($lista) {
            $out .= '<ul>' . implode('', array_map(static fn (string $i): string => '<li>' . creditos_en_linea($i) . '</li>', $lista)) . '</ul>';
            $lista = [];
        }
    };
    foreach (preg_split('/\R/', $md) as $linea) {
        $linea = trim($linea);
        if (str_starts_with($linea, '|')) {
            if (!preg_match('/^\|[\s|:-]+\|$/', $linea)) {
                $tabla[] = array_map('trim', explode('|', trim($linea, '|')));
            }
            continue;
        }
        if (str_starts_with($linea, '- ')) {
            $lista[] = substr($linea, 2);
            continue;
        }
        $cerrar();
        if ($linea === '' || str_starts_with($linea, '# ')) {
            continue;
        }
        $out .= str_starts_with($linea, '## ')
            ? '<h2>' . creditos_en_linea(substr($linea, 3)) . '</h2>'
            : '<p>' . creditos_en_linea($linea) . '</p>';
    }
    $cerrar();
    return $out;
}

render_partial('head', [
    'title' => 'Créditos de los ejemplos por rubro | ' . $site['profile']['name'],
    'description' => 'Autores y licencias de las fotos, modelos 3D y librerías usadas en los ejemplos por rubro.',
    'canonical' => '/demos/creditos.php',
    'extra_head' => '<meta name="robots" content="noindex, follow">' . demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro',
      'titulo' => 'Créditos de fotos y librerías',
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Créditos', '/demos/creditos.php']],
  ]); ?>
  <section class="section-band pt-0 demos-creditos">
    <div class="container">
      <?= creditos_html((string) file_get_contents(__DIR__ . '/CREDITOS.md')) ?>
    </div>
  </section>
</main>
<?php render_partial('footer', ['site' => $site]);
