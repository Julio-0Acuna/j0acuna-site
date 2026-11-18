<?php
// Uso (CLI): tools/php.sh tools/estado_rubros.php > ../docs/estado-rubros.md
// Tabla de avance de los ejemplos por rubro, calculada desde las carpetas de demos/.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/view.php';

$demos = dirname(__DIR__) . '/demos';
$hecho = static function (string $carpeta, string $version) use ($demos): bool {
    $dir = $demos . '/' . $carpeta . '/' . $version;
    return is_file($dir . '/index.php') || is_file($dir . '/index.html');
};
$marca = static fn (bool $si): string => $si ? '✅' : '⏳';

$filas = [];
$total = ['simple' => 0, 'multi' => 0, 'plataforma' => 0];
foreach (portfolio_rubros() as $r) {
    $carpeta = $r['disponible'] ? trim((string) parse_url($r['url'], PHP_URL_PATH), '/') : '';
    $carpeta = $carpeta !== '' ? basename($carpeta) : $r['slug'];
    $estado = [];
    foreach (array_keys($total) as $v) {
        $estado[$v] = $hecho($carpeta, $v);
        $total[$v] += $estado[$v] ? 1 : 0;
    }
    $filas[] = sprintf('| %s | %s | %s | %s |', $r['nombre'], $marca($estado['simple']), $marca($estado['multi']), $marca($estado['plataforma']));
}

$n = count($filas);
echo "# Estado de los ejemplos por rubro\n\n";
echo 'Actualizado: ' . date('d-m-Y H:i') . "\n\n";
echo "| Rubro | Simple-page | Multi-page | Plataforma web (e-commerce) |\n|---|---|---|---|\n";
echo implode("\n", $filas) . "\n\n";
echo sprintf("**Total:** %d de %d Simple-page · %d de %d Multi-page · %d de %d Plataforma web · %d de %d sitios.\n",
    $total['simple'], $n, $total['multi'], $n, $total['plataforma'], $n, array_sum($total), $n * 3);
echo "\n✅ hecho · ⏳ pendiente\n";
