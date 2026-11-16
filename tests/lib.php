<?php
// Mini librería de pruebas: t(), eq(), ok(), contiene(), no_contiene().
$GLOBALS['__tests'] = ['ok' => 0, 'fail' => 0, 'actual' => ''];

final class FalloPrueba extends Exception {}

function t(string $nombre, callable $fn): void
{
    $GLOBALS['__tests']['actual'] = $nombre;
    try {
        $fn();
        $GLOBALS['__tests']['ok']++;
        echo "  ok   $nombre\n";
    } catch (Throwable $e) {
        $GLOBALS['__tests']['fail']++;
        $donde = $e instanceof FalloPrueba ? '' : ' [' . get_class($e) . ' en ' . basename($e->getFile()) . ':' . $e->getLine() . ']';
        echo "  FALLA $nombre: " . $e->getMessage() . "$donde\n";
    }
}

function eq(mixed $real, mixed $esperado, string $msg = ''): void
{
    if ($real !== $esperado) {
        throw new FalloPrueba(($msg !== '' ? "$msg: " : '') . 'esperado ' . var_export($esperado, true) . ', obtenido ' . var_export($real, true));
    }
}

function ok(bool $cond, string $msg = ''): void
{
    if (!$cond) {
        throw new FalloPrueba($msg !== '' ? $msg : 'condición falsa');
    }
}

function contiene(string $html, string $aguja): void
{
    if (strpos($html, $aguja) === false) {
        throw new FalloPrueba('no contiene: ' . $aguja);
    }
}

function no_contiene(string $html, string $aguja): void
{
    if (strpos($html, $aguja) !== false) {
        throw new FalloPrueba('contiene y no debería: ' . $aguja);
    }
}
