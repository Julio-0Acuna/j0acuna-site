<?php
// Router para `php -S`: imita las reglas de .htaccess del servidor.
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$root = dirname(__DIR__);

if (preg_match('#(^|/)(data|includes|tests|tools)(/|$)#', $path)) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

$rutas = [
    '#^/restaurantes/?$#' => 'restaurantes.php',
    '#^/servicios/?$#' => 'servicios.php',
    '#^/proyectos/?$#' => 'proyectos.php',
    '#^/contacto/?$#' => 'contacto.php',
];
foreach ($rutas as $patron => $archivo) {
    if (preg_match($patron, $path)) {
        require $root . '/' . $archivo;
        return true;
    }
}

if (preg_match('#^/proyectos/([a-z0-9-]+)/?$#', $path, $m)) {
    $_GET['id'] = $m[1];
    require $root . '/seccion.php';
    return true;
}

if (preg_match('#^/proyecto/([a-z0-9-]+)/?$#', $path, $m)) {
    $_GET['id'] = $m[1];
    require $root . '/proyecto.php';
    return true;
}

if ($path !== '/' && is_file($root . $path)) {
    return false;
}

if ($path !== '/' && is_dir($root . $path)) {
    $dir = rtrim($root . $path, '/');
    if (substr($path, -1) !== '/') {
        header('Location: ' . $path . '/', true, 301);
        return true;
    }
    if (is_file($dir . '/index.php')) {
        chdir($dir);
        require $dir . '/index.php';
        return true;
    }
    if (is_file($dir . '/index.html')) {
        header('Content-Type: text/html; charset=utf-8');
        readfile($dir . '/index.html');
        return true;
    }
}

// Archivos .php dentro de subcarpetas (ej. /demos/pizzeria/multi/menu.php).
if (substr($path, -4) === '.php' && is_file($root . $path) && strpos(realpath($root . $path), realpath($root)) === 0) {
    chdir(dirname($root . $path));
    require $root . $path;
    return true;
}

if ($path === '/' || $path === '/index.php') {
    require $root . '/index.php';
    return true;
}

http_response_code(404);
echo 'Not Found';
return true;
