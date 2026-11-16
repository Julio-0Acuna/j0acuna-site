<?php
// Router para `php -S`: imita las reglas de .htaccess del servidor.
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$root = dirname(__DIR__);

if (preg_match('#^/(data|includes|tests|tools)(/|$)#', $path)) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

if (preg_match('#^/restaurantes/?$#', $path)) {
    require $root . '/restaurantes.php';
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

if ($path === '/' || $path === '/index.php') {
    require $root . '/index.php';
    return true;
}

http_response_code(404);
echo 'Not Found';
return true;
