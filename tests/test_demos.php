<?php
// Pruebas de la base común de los ejemplos (demos/_comun) y del motor de tiendas (demos/_motor).
require_once __DIR__ . '/../demos/_motor/motor.php';

$GLOBALS['demos_tmp'] = sys_get_temp_dir() . '/demos-test-' . bin2hex(random_bytes(4));
mkdir($GLOBALS['demos_tmp']);

/** Config mínima de una tienda de prueba con su seed en un archivo temporal. */
function demos_cfg(): array
{
    $dir = $GLOBALS['demos_tmp'];
    $seed = [
        'categorias' => [['slug' => 'pizzas', 'nombre' => 'Pizzas'], ['slug' => 'negocios', 'nombre' => 'Negocios']],
        'productos' => [
            ['slug' => 'muzza', 'categoria' => 'pizzas', 'nombre' => 'Muzza', 'precio' => 10000, 'precio_mayorista' => 8000, 'stock' => 3],
            ['slug' => 'agua', 'categoria' => 'pizzas', 'nombre' => 'Agua', 'precio' => 1000, 'stock' => 10],
            ['slug' => 'masa', 'categoria' => 'negocios', 'nombre' => 'Masa x20', 'precio' => 20000, 'precio_mayorista' => 15000, 'stock' => 5, 'solo_mayorista' => true],
        ],
        'usuarios' => [
            ['email' => 'c@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Cliente'],
            ['email' => 'm@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'May', 'tipo' => 'mayorista', 'estado' => 'activo', 'empresa' => 'Café'],
            ['email' => 'd@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueño', 'rol' => 'dueno'],
        ],
        'pedidos' => [
            ['usuario' => 'c@demo.cl', 'hace_min' => 5, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Maipú', 'pago' => 'prueba', 'items' => [['muzza', 1]]],
        ],
    ];
    file_put_contents($dir . '/seed.php', '<?php return ' . var_export($seed, true) . ';');
    return array_replace(m_config_base(), [
        'url_base' => '/demos/test/',
        'db' => $dir . '/tienda.sqlite',
        'seed' => $dir . '/seed.php',
        'negocio' => ['nombre' => 'Prueba'],
        'comunas' => ['Maipú' => 1500, 'Cerrillos' => 2000],
        'despacho_gratis_desde' => 20000,
        'prefijo_pedido' => 'TP',
        'mayoristas' => true,
    ]);
}

t('demo_clp formatea pesos chilenos', function () {
    eq(demo_clp(12990), '$12.990');
    eq(demo_clp(0), '$0');
    eq(demo_clp(1250000), '$1.250.000');
});

t('demo_nota y demo_wa arman el mensaje de prueba', function () {
    eq(demo_nota('pizzería', 'Simple-page'), '(Pedido de prueba desde la demo de pizzería – Simple-page)');
    $url = demo_wa("Hola, 1 pizza Ñuñoa & más\n" . demo_nota('pizzería', 'Multi-page'));
    contiene($url, 'https://wa.me/56973537642?text=');
    contiene($url, '%C3%91u%C3%B1oa%20%26%20m%C3%A1s%0A');
    no_contiene($url, ' ');
});

t('demo_horario_filas agrupa días con el mismo horario', function () {
    $h = [1 => null, 2 => ['12:30', '23:00'], 3 => ['12:30', '23:00'], 4 => ['12:30', '23:00'], 5 => ['12:30', '00:30'], 6 => ['12:30', '00:30'], 7 => ['13:00', '22:00']];
    eq(demo_horario_filas($h), [['Lun', 'Cerrado'], ['Mar a Jue', '12:30 – 23:00'], ['Vie y Sáb', '12:30 – 00:30'], ['Dom', '13:00 – 22:00']]);
});

t('demo_horario_estado entiende cierres después de medianoche', function () {
    $h = [1 => null, 2 => ['12:30', '23:00'], 3 => ['12:30', '23:00'], 4 => ['12:30', '23:00'], 5 => ['12:30', '00:30'], 6 => ['12:30', '00:30'], 7 => ['13:00', '22:00']];
    $tz = new DateTimeZone('America/Santiago');
    // 2026-11-15 es domingo: a las 00:15 sigue abierto el turno del sábado.
    $e = demo_horario_estado($h, new DateTimeImmutable('2026-11-15 00:15', $tz));
    eq($e['abierto'], true);
    eq($e['texto'], 'Abierto · cierra a las 00:30');
    // Lunes cerrado: abre el martes.
    $e = demo_horario_estado($h, new DateTimeImmutable('2026-11-16 15:00', $tz));
    eq($e, ['abierto' => false, 'texto' => 'Cerrado · abre mañana a las 12:30']);
    $e = demo_horario_estado($h, new DateTimeImmutable('2026-11-17 10:00', $tz));
    eq($e['texto'], 'Cerrado · abre hoy a las 12:30');
});

t('demo_google: tarjeta, mapa de la comuna y reseñas marcadas como ejemplo', function () {
    ob_start();
    demo_google(['nombre' => 'Pizzería «X» & Cía', 'categoria' => 'Pizzería', 'comuna' => 'Ñuñoa', 'horario' => [1 => ['12:00', '22:00']], 'nota' => 4.7, 'resenas' => [['Ana', 5, 'Rica', 'hace 1 día']]]);
    $h = ob_get_clean();
    contiene($h, 'Encuéntranos en Google');
    contiene($h, 'Pizzería «X» &amp; Cía');
    contiene($h, 'https://www.google.com/maps?q=%C3%91u%C3%B1oa,+Santiago,+Chile&amp;output=embed');
    contiene($h, 'https://www.google.com/maps/search/?api=1&amp;query=%C3%91u%C3%B1oa+Santiago');
    contiene($h, 'loading="lazy"');
    contiene($h, 'Reseña de ejemplo');
    contiene($h, 'Cómo llegar');
    contiene($h, 'Déjanos tu reseña');
    no_contiene($h, 'AggregateRating');
});

t('demo_boton_3d apunta al .glb en _comun/3d', function () {
    $b = demo_boton_3d(['id' => 'm1', 'nombre' => 'Margherita', 'modelo' => '../pizza', 'foto' => '123-abc']);
    contiene($b, 'data-3d-modelo="/demos/_comun/3d/pizza.glb"');
    contiene($b, 'Ver en 3D');
});

t('m_precio: mayorista aprobado paga precio mayorista; pendiente y particular no', function () {
    $p = ['precio' => 10000, 'precio_mayorista' => 8000];
    eq(m_precio($p, null), 10000);
    eq(m_precio($p, ['tipo' => 'particular', 'estado' => 'activo']), 10000);
    eq(m_precio($p, ['tipo' => 'mayorista', 'estado' => 'pendiente']), 10000);
    eq(m_precio($p, ['tipo' => 'mayorista', 'estado' => 'activo']), 8000);
    eq(m_precio(['precio' => 5000, 'precio_mayorista' => null], ['tipo' => 'mayorista', 'estado' => 'activo']), 5000);
});

t('m_puede_ver oculta productos solo mayoristas y los no visibles', function () {
    $solo = ['visible' => 1, 'solo_mayorista' => 1];
    eq(m_puede_ver($solo, null), false);
    eq(m_puede_ver($solo, ['tipo' => 'mayorista', 'estado' => 'pendiente']), false);
    eq(m_puede_ver($solo, ['tipo' => 'mayorista', 'estado' => 'activo']), true);
    eq(m_puede_ver(['visible' => 0, 'solo_mayorista' => 0], null), false);
});

t('m_costo_despacho por comuna, gratis desde un monto', function () {
    $cfg = ['comunas' => ['Maipú' => 1490, 'Cerrillos' => 1990], 'despacho_gratis_desde' => 20000];
    eq(m_costo_despacho($cfg, 'Maipú', 10000), 1490);
    eq(m_costo_despacho($cfg, 'Cerrillos', 19999), 1990);
    eq(m_costo_despacho($cfg, 'Cerrillos', 20000), 0);
    eq(m_costo_despacho($cfg, 'Las Condes', 10000), null);
    eq(m_costo_despacho(['comunas' => ['Maipú' => 1490]], 'Maipú', 99999), 1490);
});

t('MValidador limita largo y valida correo y contraseña', function () {
    $v = new MValidador(['nombre' => str_repeat('a', 61), 'email' => 'no-es-correo', 'clave' => 'corta']);
    $v->texto('nombre', 'el nombre', 60);
    $v->email('email');
    $v->clave('clave');
    eq(array_keys($v->errores), ['nombre', 'email', 'clave']);
    $v = new MValidador(['nombre' => '  Ana   María ', 'email' => 'ANA@Demo.cl', 'clave' => 'segura123']);
    eq($v->texto('nombre', 'el nombre', 60), 'Ana María');
    eq($v->email('email'), 'ana@demo.cl');
    ok($v->ok());
});

t('m_db_vencida: no existe, recién creada, y con más de una hora', function () {
    $cfg = demos_cfg();
    $ruta = $GLOBALS['demos_tmp'] . '/vencida.sqlite';
    eq(m_db_vencida($ruta, 3600), true);
    $t0 = 1_800_000_000;
    m_db_crear($ruta, require $cfg['seed'], $cfg, $t0);
    eq(m_db_vencida($ruta, 3600, $t0 + 60), false);
    eq(m_db_vencida($ruta, 3600, $t0 + 3599), false);
    eq(m_db_vencida($ruta, 3600, $t0 + 3600), true);
    eq(m_db_vencida($ruta, 0, $t0 + 999999), false, 'reinicio desactivado');
});

t('m_db_asegurar recrea la base vencida desde el seed (sin cron)', function () {
    $cfg = demos_cfg();
    @unlink($cfg['db']);
    eq(m_db_asegurar($cfg), true, 'crea la base si no existe');
    eq(m_db_asegurar($cfg), false, 'no la recrea si es nueva');
    $db = m_db_abrir($cfg['db']);
    $gen = $db->query("SELECT valor FROM meta WHERE clave = 'generacion'")->fetchColumn();
    $db->exec("INSERT INTO usuarios (email, nombre, clave_hash, creado_en) VALUES ('extra@demo.cl', 'X', 'x', 1)");
    $db->exec("UPDATE meta SET valor = '" . (time() - 3601) . "' WHERE clave = 'creada_en'");
    $db = null;
    eq(m_db_asegurar($cfg), true, 'la recrea si tiene más de una hora');
    $db = m_db_abrir($cfg['db']);
    eq((int) $db->query("SELECT COUNT(*) FROM usuarios WHERE email = 'extra@demo.cl'")->fetchColumn(), 0, 'datos nuevos borrados');
    eq((int) $db->query('SELECT COUNT(*) FROM usuarios')->fetchColumn(), 3);
    ok($db->query("SELECT valor FROM meta WHERE clave = 'generacion'")->fetchColumn() !== $gen, 'nueva generación');
    eq($db->query('SELECT codigo FROM pedidos')->fetchColumn(), 'TP-1001');
});

t('pedido: precio mayorista, stock descontado, sin stock rechazado', function () {
    $cfg = demos_cfg();
    m_db_asegurar($cfg, true);
    $db = m_db_abrir($cfg['db']);
    $may = m_usuario_por_email($db, 'm@demo.cl');
    $muzza = (int) $db->query("SELECT id FROM productos WHERE nombre = 'Muzza'")->fetchColumn();
    $masa = (int) $db->query("SELECT id FROM productos WHERE nombre = 'Masa x20'")->fetchColumn();
    $_SESSION = [];
    eq(m_carrito_poner($db, null, $masa, 1), 'Ese producto no está disponible.', 'solo mayorista');
    eq(m_carrito_poner($db, $may, $muzza, 2), '');
    eq(m_carrito_poner($db, $may, $masa, 1), '');
    $c = m_carrito($db, $may);
    eq($c['subtotal'], 2 * 8000 + 15000);
    [$id, $error] = m_pedido_crear($db, $cfg, $may, ['entrega' => 'despacho', 'comuna' => 'Maipú', 'direccion' => 'Calle 1', 'cuando' => '', 'nota' => '', 'pago' => 'al_recibir'], false);
    eq($error, '');
    $p = m_pedido($db, $id);
    eq((int) $p['costo_despacho'], 0, 'gratis sobre $20.000');
    eq((int) $p['total'], 31000);
    eq($p['tipo_cliente'], 'mayorista');
    eq((int) $db->query("SELECT stock FROM productos WHERE id = $muzza")->fetchColumn(), 1);
    eq(m_carrito_crudo(), [], 'carrito vacío después de comprar');
    // Quedan 1: pedir 3 queda ajustado a 1 con aviso.
    $cli = m_usuario_por_email($db, 'c@demo.cl');
    eq(m_carrito_poner($db, $cli, $muzza, 3), 'Solo quedan 1 de «Muzza».');
    // Otro compra la última antes: el pedido se rechaza por stock.
    $db->exec("UPDATE productos SET stock = 0 WHERE id = $muzza");
    $_SESSION['carrito'] = [$muzza => 1];
    [$id2, $error2] = m_pedido_crear($db, $cfg, $cli, ['entrega' => 'retiro', 'comuna' => '', 'direccion' => '', 'cuando' => '', 'nota' => '', 'pago' => 'al_recibir'], false);
    eq($id2, null);
    eq($error2, 'Tu carrito está vacío.');
});

t('estados del pedido y cancelación devuelve stock', function () {
    $cfg = demos_cfg();
    m_db_asegurar($cfg, true);
    $db = m_db_abrir($cfg['db']);
    $id = (int) $db->query('SELECT id FROM pedidos')->fetchColumn();
    foreach (['preparando', 'listo', 'entregado'] as $esperado) {
        ok(m_pedido_avanzar($db, $id));
        eq(m_pedido($db, $id)['estado'], $esperado);
    }
    eq(m_pedido_avanzar($db, $id), false, 'entregado es el final');
    eq(m_pedido_cancelar($db, $id), false, 'no se cancela lo entregado');
});

t('ventas del día suman sin cancelados', function () {
    $cfg = demos_cfg();
    m_db_asegurar($cfg, true);
    $db = m_db_abrir($cfg['db']);
    $db->exec('UPDATE pedidos SET creado_en = ' . time());
    $v = m_ventas_del_dia($db, $cfg);
    eq($v['pedidos'], 1);
    eq($v['total'], 10000 + 1500);
    m_pedido_cancelar($db, (int) $db->query('SELECT id FROM pedidos')->fetchColumn());
    eq(m_ventas_del_dia($db, $cfg)['total'], 0);
    eq((int) $db->query("SELECT stock FROM productos WHERE nombre = 'Muzza'")->fetchColumn(), 4, 'stock devuelto');
});

t('registro: mayorista queda pendiente y el límite por IP corta', function () {
    $cfg = demos_cfg();
    m_db_asegurar($cfg, true);
    $db = m_db_abrir($cfg['db']);
    [$id, $err] = m_registrar($db, $cfg, ['nombre' => 'Resto', 'email' => 'r@demo.cl', 'clave' => 'segura123', 'tipo' => 'mayorista', 'empresa' => 'Resto X']);
    eq($err, []);
    eq(m_usuario_por_id($db, $id)['estado'], 'pendiente');
    [, $err] = m_registrar($db, $cfg, ['nombre' => 'Otro', 'email' => 'R@demo.cl', 'clave' => 'segura123', 'tipo' => 'particular']);
    ok(isset($err['email']), 'correo repetido');
    ok(m_mayorista_decidir($db, $id, true));
    eq(m_usuario_por_id($db, $id)['estado'], 'activo');
    for ($i = 0; $i < 5; $i++) {
        eq(m_limite_superado($db, 'registro', 5, 3600, 'ip-x'), false);
        m_limite_registrar($db, 'registro', 'ip-x');
    }
    eq(m_limite_superado($db, 'registro', 5, 3600, 'ip-x'), true);
    eq(m_limite_superado($db, 'registro', 5, 3600, 'ip-y'), false, 'otra IP');
    eq(m_limite_superado($db, 'registro', 5, 3600, 'ip-x', time() + 3601), false, 'pasa la hora');
});

t('m_verificar_login usa password_hash', function () {
    $cfg = demos_cfg();
    m_db_asegurar($cfg, true);
    $db = m_db_abrir($cfg['db']);
    ok(m_verificar_login($db, 'C@demo.cl ', 'cliente123') !== null);
    eq(m_verificar_login($db, 'c@demo.cl', 'otra-clave'), null);
    eq(m_verificar_login($db, 'nadie@demo.cl', 'cliente123'), null);
    ok(str_starts_with(m_usuario_por_email($db, 'c@demo.cl')['clave_hash'], '$2y$'));
});

// Limpieza de archivos temporales.
foreach (glob($GLOBALS['demos_tmp'] . '/*') as $f) {
    @unlink($f);
}
@rmdir($GLOBALS['demos_tmp']);
