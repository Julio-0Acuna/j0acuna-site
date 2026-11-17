<?php
// Formulario de contacto: validación, correo y límite de envíos.
require_once __DIR__ . '/../includes/contacto.php';

function post_valido(array $cambios = []): array
{
    return array_replace([
        'nombre' => 'Ana Pérez',
        'negocio' => 'Pizzería «La Esquina»',
        'telefono' => '+56 9 1234 5678',
        'correo' => 'ana@example.cl',
        'mensaje' => "Hola, quiero una página web\npara mi pizzería.",
        'acepto' => '1',
        'sitio_web' => '',
    ], $cambios);
}

t('contacto valido', function () {
    $r = contacto_validar(post_valido());
    eq($r['ok'], true);
    eq($r['datos']['nombre'], 'Ana Pérez');
    eq($r['errores'], []);
});

t('contacto exige nombre, correo valido, mensaje y aceptar', function () {
    $r = contacto_validar(post_valido(['nombre' => ' ', 'correo' => 'no-es-correo', 'mensaje' => '', 'acepto' => '']));
    eq($r['ok'], false);
    ok(isset($r['errores']['nombre']), 'falta error nombre');
    ok(isset($r['errores']['correo']), 'falta error correo');
    ok(isset($r['errores']['mensaje']), 'falta error mensaje');
    ok(isset($r['errores']['acepto']), 'falta error acepto');
});

t('contacto limita largos', function () {
    $r = contacto_validar(post_valido(['nombre' => str_repeat('a', 81), 'mensaje' => str_repeat('b', 1501)]));
    ok(isset($r['errores']['nombre']));
    ok(isset($r['errores']['mensaje']));
});

t('contacto con trampa llena es spam', function () {
    $r = contacto_validar(post_valido(['sitio_web' => 'http://spam.example']));
    eq($r['ok'], false);
    eq($r['spam'], true);
});

t('telefono solo digitos espacios mas y guiones', function () {
    ok(isset(contacto_validar(post_valido(['telefono' => '<script>']))['errores']['telefono']));
    eq(contacto_validar(post_valido(['telefono' => '']))['ok'], true);
});

t('correo armado sin inyeccion de cabeceras', function () {
    $datos = contacto_validar(post_valido(['nombre' => "Ana\r\nBcc: victima@example.com"]))['datos'];
    $c = contacto_correo($datos, 'Julio0acuna123@gmail.com');
    eq($c['para'], 'Julio0acuna123@gmail.com');
    no_contiene($c['cabeceras'], "\r\nBcc:");
    no_contiene($c['asunto'], "\n");
    contiene($c['cabeceras'], 'Reply-To: ana@example.cl');
    contiene($c['cabeceras'], 'From: j0acuna.site <no-reply@j0acuna.site>');
    contiene($c['cuerpo'], 'Pizzería «La Esquina»');
    contiene($c['cuerpo'], '+56 9 1234 5678');
});

t('correo de respuesta con salto de linea se rechaza', function () {
    $r = contacto_validar(post_valido(['correo' => "ana@example.cl\r\nBcc: x@y.cl"]));
    ok(isset($r['errores']['correo']));
});

t('limite de envios por ip', function () {
    $dir = sys_get_temp_dir() . '/contacto_test_' . getmypid();
    @mkdir($dir);
    $archivo = $dir . '/limite.json';
    @unlink($archivo);
    for ($i = 0; $i < 3; $i++) {
        eq(contacto_limite('190.1.2.3', $archivo, 3, 3600, 1000 + $i), true, "envio $i");
    }
    eq(contacto_limite('190.1.2.3', $archivo, 3, 3600, 1010), false, 'cuarto envio');
    eq(contacto_limite('200.9.9.9', $archivo, 3, 3600, 1010), true, 'otra ip');
    eq(contacto_limite('190.1.2.3', $archivo, 3, 3600, 1000 + 3601), true, 'pasada la hora');
    $guardado = (string) file_get_contents($archivo);
    no_contiene($guardado, '190.1.2.3');
    @unlink($archivo);
    @rmdir($dir);
});

t('envio en modo archivo deja el correo en disco', function () {
    $dir = sys_get_temp_dir() . '/contacto_eml_' . getmypid();
    $c = contacto_correo(contacto_validar(post_valido())['datos'], 'Julio0acuna123@gmail.com');
    eq(contacto_enviar($c, 'archivo', $dir), true);
    $archivos = glob($dir . '/*.eml');
    eq(count($archivos), 1);
    contiene((string) file_get_contents($archivos[0]), 'Ana Pérez');
    array_map('unlink', $archivos);
    @rmdir($dir);
});
