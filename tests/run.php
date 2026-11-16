<?php
// Ejecuta todos los tests/test_*.php. Sale con código 1 si hay fallas.
require __DIR__ . '/lib.php';

$archivos = glob(__DIR__ . '/test_*.php');
sort($archivos);
foreach ($archivos as $archivo) {
    echo basename($archivo) . "\n";
    require $archivo;
}

$r = $GLOBALS['__tests'];
echo "\n{$r['ok']} ok / {$r['fail']} fallas\n";
exit($r['fail'] > 0 ? 1 : 0);
