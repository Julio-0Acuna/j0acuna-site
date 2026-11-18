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

/* ---------------------------------------------------------------- Textos, fecha de entrega y resumen */

/** Martes 17 de noviembre de 2026, 10:00 en Santiago. */
function demos_martes(): int
{
    return (new DateTimeImmutable('2026-11-17 10:00', new DateTimeZone('America/Santiago')))->getTimestamp();
}

t('m_texto: valores por defecto iguales a los textos de siempre', function () {
    m_config(demos_cfg());
    eq(m_texto('mayorista'), 'Mayorista');
    eq(m_texto('mayoristas'), 'Mayoristas');
    eq(m_texto('crear_cuenta_mayorista'), 'Crear cuenta mayorista');
    eq(m_texto('solo_mayoristas'), 'Solo mayoristas');
    eq(m_texto('mayoristas_esperan', null, ['n' => 2]), '2 mayorista(s) esperan aprobación');
    eq(m_texto('hora_entrega', null, ['hora' => '13:30']), 'Hoy a las 13:30');
    eq(m_texto('cuando_pronto'), 'Lo antes posible');
    eq(m_texto('pedido_exito_prueba'), '¡Pago de prueba aprobado! Tu pedido ya está en la cocina.');
    eq(m_texto('pedido_exito_al_recibir'), '¡Pedido recibido! Pagas al recibir.');
    eq(m_texto('qr_pie'), 'Código QR del menú');
    eq(m_texto('qr_titulo', 'Propio de la vista'), 'Propio de la vista', 'el valor por defecto de una vista del rubro gana a la tabla');
    eq(m_texto('clave-que-no-existe'), '');
});

t('m_texto: el rubro cambia textos y el nombre del mayorista se propaga', function () {
    m_config(['textos' => ['mayorista' => 'Empresa', 'mayoristas' => 'Empresas', 'qr_pie' => 'Código QR de la carta', 'hora_entrega' => 'Hoy, {hora} hrs']] + demos_cfg());
    eq(m_texto('mayorista'), 'Empresa');
    eq(m_texto('solo_mayoristas'), 'Solo empresas');
    eq(m_texto('solo_para_mayoristas'), 'Solo para empresas');
    eq(m_texto('mayoristas_esperan', null, ['n' => 1]), '1 empresa(s) esperan aprobación');
    eq(m_texto('qr_pie'), 'Código QR de la carta');
    eq(m_texto('qr_titulo', 'Propio de la vista'), 'Propio de la vista');
    eq(m_texto('hora_entrega', null, ['hora' => '20:00']), 'Hoy, 20:00 hrs');
    eq(m_minuscula('Precio Club'), 'precio Club');
    eq(m_minuscula('Ñandú'), 'ñandú');
    m_config(demos_cfg());
});

t('m_fecha_texto: fechas en español, larga y corta', function () {
    eq(m_fecha_texto('2026-11-21'), 'sábado 21 de noviembre');
    eq(m_fecha_texto('2026-11-21', false), 'sáb 21/11');
    eq(m_fecha_texto('2027-01-04'), 'lunes 4 de enero');
    eq(m_fecha_texto('2026-02-30'), '', 'fecha inexistente');
    eq(m_fecha_texto('mañana'), '');
    eq(m_fecha_texto(''), '');
});

t('m_fechas_entrega: desde hoy + dias_min, sin días cerrados ni feriados, hasta dias_max', function () {
    $ahora = demos_martes();
    eq(m_fechas_entrega(demos_cfg(), $ahora), [], 'modo hoy (por defecto): sin fechas');
    $cfg = demos_cfg();
    $cfg['entrega'] = ['modo' => 'fecha', 'dias_min' => 1, 'dias_max' => 7, 'cerrado' => [1, '2026-11-20']];
    $f = m_fechas_entrega($cfg, $ahora);
    eq(array_keys($f), ['2026-11-18', '2026-11-19', '2026-11-21', '2026-11-22', '2026-11-24']);
    eq($f['2026-11-18'], 'Mañana, mié 18/11');
    eq($f['2026-11-19'], 'Jue 19/11');
    // Sin 'cerrado': se cierran los días sin horario del negocio. dias_min 0 incluye hoy.
    $cfg = demos_cfg();
    $cfg['negocio']['horario'] = [1 => null, 2 => ['10:00', '20:00'], 3 => ['10:00', '20:00'], 4 => ['10:00', '20:00'], 5 => ['10:00', '20:00'], 6 => ['10:00', '14:00'], 7 => null];
    $cfg['entrega'] = ['modo' => 'fecha', 'dias_max' => 6];
    $f = m_fechas_entrega($cfg, $ahora);
    eq(array_keys($f), ['2026-11-17', '2026-11-18', '2026-11-19', '2026-11-20', '2026-11-21']);
    eq($f['2026-11-17'], 'Hoy, mar 17/11');
    eq(m_entrega_cfg($cfg)['dias_min'], 0);
    eq(m_entrega_cfg(['entrega' => ['modo' => 'otro']])['modo'], 'hoy', 'modo desconocido = hoy');
});

t('checkout con fecha: exige una fecha de la lista y la guarda en el pedido', function () {
    $cfg = demos_cfg();
    $cfg['entrega'] = ['modo' => 'fecha', 'dias_min' => 1, 'dias_max' => 5, 'cerrado' => []];
    m_config($cfg);
    m_db_asegurar($cfg, true);
    $db = m_db_abrir($cfg['db']);
    $cli = m_usuario_por_email($db, 'c@demo.cl');
    $fechas = array_keys(m_fechas_entrega($cfg));
    eq(count($fechas), 5);
    $hoy = (new DateTimeImmutable('today', new DateTimeZone('America/Santiago')))->format('Y-m-d');
    $base = ['entrega' => 'retiro', 'pago' => 'al_recibir', 'nota' => 'Sin nueces'];
    foreach (['falta' => null, 'hoy (dias_min 1)' => $hoy, 'pasada' => '2020-01-01', 'arreglo' => ['x'], 'texto' => '<b>'] as $caso => $valor) {
        $post = $valor === null ? $base : $base + ['fecha_entrega' => $valor];
        [$d, $e] = m_checkout_validar($db, $cfg, $cli, $post, 5000);
        eq($d, null, $caso);
        ok(isset($e['fecha_entrega']), "fecha $caso rechazada");
    }
    [$d, $e] = m_checkout_validar($db, $cfg, $cli, $base + ['fecha_entrega' => $fechas[1], 'cuando' => '13:00'], 5000);
    eq($e, []);
    eq($d['fecha_entrega'], $fechas[1]);
    $agua = (int) $db->query("SELECT id FROM productos WHERE nombre = 'Agua'")->fetchColumn();
    $_SESSION = ['carrito' => [$agua => 2]];
    [$id, $error] = m_pedido_crear($db, $cfg, $cli, $d, false);
    eq($error, '');
    $p = m_pedido($db, $id);
    eq($p['fecha_entrega'], $fechas[1]);
    eq($p['cuando'], '13:00');
    // La fecha dejó de estar disponible (por ejemplo, pasó la medianoche en la pantalla de pago).
    $_SESSION = ['carrito' => [$agua => 1]];
    [$id2, $error2] = m_pedido_crear($db, $cfg, $cli, ['fecha_entrega' => '2020-01-01'] + $d, false);
    eq($id2, null);
    eq($error2, 'Ese día ya no está disponible. Elige otro.');
    // Modo hoy: la fecha enviada se ignora y el pedido queda sin fecha.
    [$d, $e] = m_checkout_validar($db, demos_cfg(), $cli, $base + ['fecha_entrega' => $fechas[0]], 5000);
    eq($e, []);
    eq($d['fecha_entrega'], '');
    m_config(demos_cfg());
});

t('seed: fecha_entrega en días desde la creación o como fecha fija', function () {
    $cfg = demos_cfg();
    $seed = require $cfg['seed'];
    $seed['pedidos'][] = ['usuario' => 'c@demo.cl', 'hace_min' => 1, 'estado' => 'nuevo', 'entrega' => 'retiro', 'pago' => 'al_recibir', 'fecha_entrega' => 2, 'items' => [['agua', 1]]];
    $seed['pedidos'][] = ['usuario' => 'c@demo.cl', 'hace_min' => 1, 'estado' => 'nuevo', 'entrega' => 'retiro', 'pago' => 'al_recibir', 'fecha_entrega' => '2026-12-24', 'items' => [['agua', 1]]];
    $ruta = $GLOBALS['demos_tmp'] . '/seed-fecha.sqlite';
    m_db_crear($ruta, $seed, $cfg, demos_martes());
    $db = m_db_abrir($ruta);
    eq($db->query('SELECT fecha_entrega FROM pedidos ORDER BY id')->fetchAll(PDO::FETCH_COLUMN), ['', '2026-11-19', '2026-12-24']);
    $db = null;
});

t('m_db_migrar agrega fecha_entrega a una base con el esquema anterior', function () {
    $ruta = $GLOBALS['demos_tmp'] . '/antigua.sqlite';
    $db = m_db_abrir($ruta);
    $db->exec("CREATE TABLE pedidos (id INTEGER PRIMARY KEY, cuando TEXT NOT NULL DEFAULT '')");
    $db->exec("INSERT INTO pedidos (cuando) VALUES ('12:00')");
    m_db_migrar($db);
    m_db_migrar($db);
    eq($db->query('SELECT fecha_entrega FROM pedidos')->fetchColumn(), '');
    $db = null;
});

t('resumen de WhatsApp: fecha de entrega y nota del cliente', function () {
    $cfg = demos_cfg();
    m_config($cfg);
    $p = ['codigo' => 'TP-1001', 'items' => [['cantidad' => 2, 'nombre' => 'Agua', 'precio_unitario' => 1000]], 'costo_despacho' => 0, 'total' => 2000,
        'entrega' => 'retiro', 'comuna' => '', 'cuando' => '17:00', 'fecha_entrega' => '2026-11-21', 'pago' => 'al_recibir', 'nota' => 'Sin nueces, por favor'];
    $t = m_pedido_resumen_texto($p);
    contiene($t, "Entrega: retiro en el local\nPara el sábado 21 de noviembre a las 17:00\nPago: al recibir\nNota: Sin nueces, por favor");
    $t = m_pedido_resumen_texto(['cuando' => ''] + $p);
    contiene($t, "\nPara el sábado 21 de noviembre\n");
    $t = m_pedido_resumen_texto(['fecha_entrega' => '', 'nota' => ''] + $p);
    contiene($t, "\nPara las 17:00\n");
    no_contiene($t, 'Nota:');
    unset($p['fecha_entrega']);
    contiene(m_pedido_resumen_texto($p), "\nPara las 17:00\n");
    m_config(['resumen_con_nota' => false] + $cfg);
    no_contiene(m_pedido_resumen_texto($p), 'Nota:');
    m_config($cfg);
});

/* ---------------------------------------------------------------- Vistas del motor (sin layout del rubro) */

/** Renderiza una vista del motor con la config actual (m_config) y datos de prueba. */
function demos_vista(string $nombre, array $vars = []): string
{
    ob_start();
    m_parcial($nombre, $vars + ['cfg' => m_config(), 'usuario' => null, 'unidades_carrito' => 0]);
    return (string) ob_get_clean();
}

/** Pedido de prueba (como lo devuelve m_pedido), con lo que cambie $extra. */
function demos_pedido(array $extra = []): array
{
    return $extra + [
        'id' => 7, 'codigo' => 'TP-1007', 'usuario_id' => 1, 'estado' => 'listo', 'entrega' => 'retiro', 'comuna' => '', 'direccion' => '',
        'cuando' => '13:00', 'fecha_entrega' => '', 'nota' => 'Sin cebolla', 'extras' => '', 'subtotal' => 10000, 'costo_despacho' => 0, 'total' => 10000,
        'pago' => 'al_recibir', 'pagado' => 0, 'creado_en' => demos_martes(), 'tipo_cliente' => 'particular', 'cliente' => 'Ana', 'cliente_empresa' => '',
        'items' => [['cantidad' => 1, 'nombre' => 'Muzza', 'precio_unitario' => 10000]],
    ];
}

/** HTML de todas las vistas del motor, en sus estados más completos. */
function demos_vistas_todas(): string
{
    $_SESSION = ['csrf' => 'x'];
    $producto = ['id' => 1, 'nombre' => 'Muzza', 'descripcion' => 'Rica', 'precio' => 10000, 'precio_mayorista' => 8000, 'precio_final' => 8000,
        'stock' => 3, 'foto' => '', 'modelo3d' => '', 'visible' => 1, 'destacado' => 1, 'solo_mayorista' => 0, 'categoria_id' => 1,
        'categoria_slug' => 'pizzas', 'categoria_nombre' => 'Pizzas', 'codigo' => 'CD-1'];
    $agotado = ['id' => 2, 'nombre' => 'Masa', 'stock' => 0, 'solo_mayorista' => 1, 'destacado' => 0] + $producto;
    $cliente = ['id' => 1, 'nombre' => 'Ana', 'email' => 'a@demo.cl', 'rol' => 'cliente', 'tipo' => 'mayorista', 'estado' => 'activo', 'empresa' => 'Café', 'telefono' => ''];
    $dueno = ['rol' => 'dueno', 'tipo' => 'particular', 'estado' => 'activo'] + $cliente;
    $carrito = ['lineas' => [['producto' => $producto, 'cantidad' => 1, 'precio' => 8000, 'total' => 8000]], 'subtotal' => 8000, 'unidades' => 1, 'avisos' => []];
    $direccion = ['id' => 3, 'etiqueta' => 'Casa', 'calle' => 'Calle 1', 'comuna' => 'Maipú', 'referencia' => 'Depto 2'];
    $categorias = [['id' => 1, 'slug' => 'pizzas', 'nombre' => 'Pizzas']];
    $pedidos = [demos_pedido(), demos_pedido(['id' => 8, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Maipú', 'pago' => 'prueba', 'cuando' => '']),
        demos_pedido(['id' => 9, 'estado' => 'preparando', 'fecha_entrega' => '2026-11-21'])];
    $ventas = ['total' => 10000, 'pedidos' => 1, 'promedio' => 10000, 'top' => [['nombre' => 'Muzza', 'unidades' => 1, 'monto' => 10000]]];
    $conteo = ['nuevo' => 1, 'preparando' => 1, 'listo' => 1, 'entregado' => 0, 'cancelado' => 0];
    $html = '';
    $html .= demos_vista('carrito', ['carrito' => $carrito, 'usuario' => $cliente]);
    $html .= demos_vista('carrito', ['carrito' => ['lineas' => [], 'subtotal' => 0, 'unidades' => 0, 'avisos' => []]]);
    foreach ([[], ['calle' => 'Falta la calle.', 'comuna' => 'No despachamos a esa comuna.']] as $errores) {
        $html .= demos_vista('checkout', ['carrito' => $carrito, 'direcciones' => [$direccion], 'errores' => $errores, 'previo' => [], 'horas' => ['13:00'], 'usuario' => $cliente]);
    }
    $html .= demos_vista('checkout', ['carrito' => $carrito, 'direcciones' => [], 'errores' => [], 'previo' => [], 'horas' => ['13:00'], 'usuario' => $cliente,
        'con_fecha' => true, 'fechas' => ['2026-11-21' => 'Sáb 21/11']]);
    $html .= demos_vista('pago', ['carrito' => $carrito, 'datos' => ['entrega' => 'despacho', 'comuna' => 'Maipú'], 'despacho' => 1500, 'total' => 9500]);
    foreach ([$cliente, $dueno] as $u) {
        foreach (['nuevo', 'listo', 'cancelado'] as $estado) {
            $html .= demos_vista('pedido', ['pedido' => demos_pedido(['estado' => $estado]), 'wa' => 'https://wa.me/1', 'usuario' => $u]);
        }
        $html .= demos_vista('pedido', ['pedido' => demos_pedido(['entrega' => 'despacho', 'comuna' => 'Maipú', 'direccion' => 'Calle 1', 'cuando' => '', 'pagado' => 1]), 'wa' => 'https://wa.me/1', 'usuario' => $u]);
    }
    foreach (['pendiente', 'rechazado', 'activo'] as $estado) {
        $html .= demos_vista('cuenta', ['usuario' => ['estado' => $estado] + $cliente, 'direcciones' => [$direccion], 'pedidos' => $pedidos, 'errores' => ['etiqueta' => 'Falta'], 'previo' => []]);
    }
    $html .= demos_vista('cuenta', ['usuario' => $dueno, 'direcciones' => [], 'pedidos' => [], 'errores' => [], 'previo' => []]);
    $html .= demos_vista('cuenta', ['usuario' => ['tipo' => 'particular'] + $cliente, 'direcciones' => [], 'pedidos' => [], 'errores' => [], 'previo' => []]);
    $html .= demos_vista('error', ['titulo' => 'T', 'mensaje' => 'M']);
    $html .= demos_vista('ingresar', ['error' => 'Error X', 'email' => '', 'volver' => 'checkout']);
    foreach ([null, ['tipo' => 'mayorista', 'estado' => 'pendiente', 'rol' => 'cliente'] + $cliente] as $u) {
        $html .= demos_vista('inicio', ['usuario' => $u, 'categorias' => $categorias, 'por_categoria' => ['pizzas' => [$producto, $agotado]], 'destacados' => [$producto],
            'q' => '', 'cat' => '', 'hay_solo_mayorista' => true]);
    }
    $html .= demos_vista('inicio', ['categorias' => $categorias, 'por_categoria' => [], 'destacados' => [], 'q' => 'xyz', 'cat' => '', 'hay_solo_mayorista' => false]);
    foreach (['resumen', 'pedidos', 'productos', 'mayoristas'] as $ver) {
        $html .= demos_vista('panel', ['ver' => $ver, 'estado' => '', 'ventas' => $ventas, 'conteo' => $conteo, 'pedidos' => $ver === 'resumen' ? [] : $pedidos,
            'productos' => [$producto, ['visible' => 0] + $agotado], 'pendientes' => $ver === 'resumen' ? [] : [$cliente], 'aprobados' => [$cliente], 'usuario' => $dueno]);
    }
    $html .= demos_vista('panel', ['ver' => 'resumen', 'estado' => '', 'ventas' => ['top' => []] + $ventas, 'conteo' => $conteo, 'pedidos' => $pedidos,
        'productos' => [], 'pendientes' => [$cliente], 'aprobados' => [], 'usuario' => $dueno]);
    foreach ([null, 1] as $id) {
        $html .= demos_vista('panel_producto', ['producto' => $id ? $producto : null, 'id' => $id, 'categorias' => $categorias, 'fotos' => ['a' => 'Imagen A'], 'errores' => ['nombre' => 'Falta']]);
    }
    $html .= demos_vista('registro', ['errores' => ['general' => 'Límite'], 'previo' => [], 'volver' => 'inicio']);
    $html .= demos_vista('portada');
    $html .= demos_vista('layout', ['contenido' => '', 'titulo' => 'T', 'usuario' => null]);
    $html .= demos_vista('layout', ['contenido' => '', 'titulo' => 'T', 'usuario' => $cliente]);
    $html .= demos_vista('layout', ['contenido' => '', 'titulo' => 'T', 'usuario' => $dueno]);
    return $html;
}

t('textos: cada clave de M_TEXTOS se puede cambiar por config', function () {
    m_config(demos_cfg());
    // Valores por defecto: los textos de siempre, letra por letra.
    eq(m_texto('checkout_titulo'), 'Finalizar compra');
    eq(m_texto('checkout_como_recibes'), '¿Cómo lo recibes?');
    eq(m_texto('checkout_confirmar'), 'Confirmar pedido');
    eq(m_texto('checkout_costo_comuna'), 'Costo según comuna.');
    eq(m_texto('pago_al_recibir'), 'Pagar al recibir');
    eq(m_texto('pago_al_recibir_texto'), 'Efectivo, débito o crédito.');
    eq(m_texto('carrito_titulo'), 'Tu carrito');
    eq(m_texto('carrito_gratis_retiro'), 'Gratis (retiro)');
    eq(m_texto('panel_titulo'), 'Panel del dueño');
    eq(m_texto('registro_empresa'), 'Nombre del negocio');
    eq(m_estado_texto('nuevo'), 'Nuevo');
    eq(m_estado_texto('listo'), 'Listo');
    eq(m_estado_texto('listo', 'retiro'), 'Listo para retiro');
    eq(m_estado_texto('listo', 'despacho'), 'En camino');
    eq(m_estado_texto('preparando', 'despacho'), 'Preparando');
    eq(m_estado_texto('cancelado'), 'Cancelado');
    eq(m_estado_texto('no-existe'), 'no-existe');
    foreach (M_ESTADOS as $clave => $nombre) {
        eq(m_estado_texto($clave), $nombre, "estado $clave");
    }
    ok(count(M_TEXTOS) > 150, 'M_TEXTOS tiene los textos de todas las vistas (' . count(M_TEXTOS) . ')');
    $textos = [];
    foreach (array_keys(M_TEXTOS) as $k) {
        $textos[$k] = 'X-' . $k;
    }
    m_config(['textos' => $textos] + demos_cfg());
    foreach (array_keys(M_TEXTOS) as $k) {
        eq(m_texto($k), 'X-' . $k, $k);
    }
    eq(m_estado_texto('listo', 'retiro'), 'X-estado_listo_retiro');
    eq(m_estado_texto('listo'), 'X-estado_listo');
    m_config(demos_cfg());
});

t('textos: vistas sin texto fijo', function () {
    $textos = [];
    foreach (array_keys(M_TEXTOS) as $k) {
        $textos[$k] = 'Z' . $k;
    }
    m_config(['textos' => $textos, 'rubro' => ['rubro_nombre' => 'prueba', 'url_rubro' => '/demos/'], 'menu_qr' => false] + demos_cfg());
    $html = demos_vistas_todas();
    foreach (['checkout_titulo', 'checkout_confirmar', 'checkout_como_recibes', 'checkout_tu_pedido', 'checkout_costo_comuna', 'checkout_nota',
        'pago_al_recibir', 'pago_al_recibir_texto', 'carrito_titulo', 'carrito_continuar', 'panel_titulo', 'panel_pedidos', 'panel_pedidos_nuevos',
        'registro_empresa', 'estado_listo_retiro', 'estado_listo_despacho', 'estado_nuevo', 'pedido_paso_nuevo', 'cuenta_pedidos', 'ingresar_titulo',
        'pago_aprobar', 'panel_producto_nuevo', 'inicio_todo', 'layout_carrito'] as $k) {
        contiene($html, 'Z' . $k);
    }
    // tienda.js recibe sus textos desde la vista del checkout.
    contiene($html, 'data-texto-gratis-retiro="Zcarrito_gratis_retiro"');
    contiene($html, 'data-texto-elige-comuna="Zcheckout_elige_comuna"');
    // Ningún texto por defecto queda fijo en lo visible (salvo los muy cortos o con marcadores).
    $visible = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    foreach (M_TEXTOS as $k => $defecto) {
        if (mb_strlen($defecto) < 4 || str_contains($defecto, '{')) {
            continue;
        }
        if (str_contains($visible, $defecto)) {
            throw new FalloPrueba("texto fijo en una vista: $k = $defecto");
        }
    }
    // Textos fijos de siempre que ya no deben aparecer en ninguna vista.
    foreach (['Finalizar compra', '¿Cómo lo recibes?', 'Confirmar pedido', 'Tu carrito', 'Panel del dueño', 'Nombre del negocio', 'Listo para retiro',
        'En camino', 'Recibido', 'Pagar al recibir', 'Vaciar carrito', 'Ventas de hoy', 'Mis pedidos', 'Cuentas de prueba', 'Crear producto', 'Agotado'] as $fijo) {
        no_contiene($visible, $fijo);
    }
    m_config(demos_cfg());
});

/* ---------------------------------------------------------------- Campos extra del checkout */

/** Config de prueba con campos extra de todos los tipos. */
function demos_cfg_campos(): array
{
    return ['checkout_campos' => [
        ['nombre' => 'patente', 'etiqueta' => 'Patente', 'patron' => '/^[A-Z]{2,4}\d{2,4}$/', 'normalizar' => 'patente', 'max' => 8, 'ayuda' => 'Ej.: ABCD12'],
        ['nombre' => 'vehiculo', 'etiqueta' => 'Vehículo', 'max' => 10],
        ['nombre' => 'anio', 'etiqueta' => 'Año', 'tipo' => 'numero', 'min' => 1950, 'max' => 2027],
        ['nombre' => 'barbero', 'etiqueta' => 'Barbero', 'tipo' => 'opcion', 'opciones' => ['benjamin' => 'Benjamín', 'nico' => 'Nico']],
        ['nombre' => 'mayor', 'etiqueta' => 'Soy mayor de 18 años', 'tipo' => 'casilla'],
        ['nombre' => 'km', 'etiqueta' => 'Kilometraje', 'tipo' => 'numero', 'requerido' => false],
    ]] + demos_cfg();
}

t('checkout_campos: valida tipos', function () {
    $cfg = demos_cfg_campos();
    m_config($cfg);
    [$extras, $e] = m_checkout_campos_validar($cfg, ['extra' => ['patente' => 'ab-12 34 xx', 'vehiculo' => str_repeat('x', 500), 'anio' => '1800', 'barbero' => 'otro', 'km' => 'mucho']], 'retiro');
    eq(array_keys($e), ['extra_patente', 'extra_vehiculo', 'extra_anio', 'extra_barbero', 'extra_mayor', 'extra_km']);
    eq($extras, []);
    contiene($e['extra_vehiculo'], 'hasta 10 caracteres');
    contiene($e['extra_anio'], 'entre 1950 y 2027');
    contiene($e['extra_mayor'], 'Soy mayor de 18 años');
    // Sin datos: faltan los requeridos; el opcional vacío no da error.
    [, $e] = m_checkout_campos_validar($cfg, [], 'retiro');
    eq(array_keys($e), ['extra_patente', 'extra_vehiculo', 'extra_anio', 'extra_barbero', 'extra_mayor']);
    // Valores que no son texto (arreglos) se rechazan sin romper nada.
    [, $e] = m_checkout_campos_validar($cfg, ['extra' => ['patente' => ['x'], 'anio' => ['1'], 'barbero' => ['nico'], 'mayor' => ['1']]], 'retiro');
    ok(isset($e['extra_patente'], $e['extra_anio'], $e['extra_barbero'], $e['extra_mayor']), 'arreglos rechazados');
    [, $e] = m_checkout_campos_validar($cfg, ['extra' => 'no-es-arreglo'], 'retiro');
    eq(count($e), 5);
    // Todo bien: patente normalizada, texto limpio, número sin puntos, texto de la opción y «Sí» en la casilla.
    [$extras, $e] = m_checkout_campos_validar($cfg, ['extra' => ['patente' => 'ab-12 34', 'vehiculo' => '  Corolla   15 ', 'anio' => '2015', 'barbero' => 'benjamin', 'mayor' => '1', 'km' => '84.000']], 'retiro');
    eq($e, []);
    eq($extras, [['Patente', 'AB1234'], ['Vehículo', 'Corolla 15'], ['Año', '2015'], ['Barbero', 'Benjamín'], ['Soy mayor de 18 años', 'Sí'], ['Kilometraje', '84000']]);
    [$extras] = m_checkout_campos_validar($cfg, ['extra' => ['patente' => 'KXPL47', 'vehiculo' => 'Kia', 'anio' => '2020', 'barbero' => 'nico', 'mayor' => 'on', 'km' => '']], 'retiro');
    eq(count($extras), 5, 'el opcional vacío no va al pedido');
    // Error propio del campo.
    $cfg['checkout_campos'][4]['error'] = 'Solo vendemos a mayores de 18 años.';
    [, $e] = m_checkout_campos_validar($cfg, [], 'retiro');
    eq($e['extra_mayor'], 'Solo vendemos a mayores de 18 años.');
    // Sin checkout_campos no cambia nada.
    eq(m_checkout_campos_validar(demos_cfg(), ['extra' => ['x' => 'y']], 'retiro'), [[], []]);
    eq(m_checkout_campos(demos_cfg()), []);
});

t('checkout_campos: solo despacho no se exige en retiro', function () {
    $cfg = ['checkout_campos' => [
        ['nombre' => 'obra', 'etiqueta' => 'Dirección de la obra', 'solo' => 'despacho'],
        ['nombre' => 'patente', 'etiqueta' => 'Patente', 'solo' => 'retiro'],
        ['nombre' => 'empresa', 'etiqueta' => 'Empresa', 'requerido' => false],
    ]] + demos_cfg();
    m_config($cfg);
    eq(array_keys(m_checkout_campos($cfg)), ['obra', 'patente', 'empresa']);
    eq(array_keys(m_checkout_campos($cfg, 'retiro')), ['patente', 'empresa']);
    eq(array_keys(m_checkout_campos($cfg, 'despacho')), ['obra', 'empresa']);
    eq(array_keys(m_checkout_campos($cfg, '')), ['empresa'], 'sin entrega válida: solo los comunes');
    [, $e] = m_checkout_campos_validar($cfg, [], 'retiro');
    eq(array_keys($e), ['extra_patente']);
    [, $e] = m_checkout_campos_validar($cfg, [], 'despacho');
    eq(array_keys($e), ['extra_obra']);
    m_db_asegurar($cfg, true);
    $db = m_db_abrir($cfg['db']);
    $cli = m_usuario_por_email($db, 'c@demo.cl');
    [$d, $e] = m_checkout_validar($db, $cfg, $cli, ['entrega' => 'retiro', 'pago' => 'al_recibir', 'extra' => ['patente' => 'AB12', 'obra' => 'no se guarda']], 5000);
    eq($e, []);
    eq($d['extras'], [['Patente', 'AB12']]);
    [$d, $e] = m_checkout_validar($db, $cfg, $cli, ['entrega' => 'despacho', 'pago' => 'al_recibir', 'calle' => 'Calle 1', 'comuna' => 'Maipú', 'extra' => ['patente' => 'AB12']], 5000);
    eq($d, null);
    eq(array_keys($e), ['extra_obra']);
    m_config(demos_cfg());
});

t('checkout_campos: nombre inválido lanza LogicException', function () {
    $malos = [
        ['nombre' => 'Patente'], ['nombre' => '1abc'], ['nombre' => 'con-guion'], ['nombre' => str_repeat('a', 32)], ['nombre' => ''], [],
        ['nombre' => 'r'], ['nombre' => 'x', 'tipo' => 'fecha'], ['nombre' => 'x', 'tipo' => 'opcion'], ['nombre' => 'x', 'solo' => 'mesa'],
        ['nombre' => 'x', 'normalizar' => 'rut'], ['nombre' => 'x', 'patron' => '/sin cerrar'], ['nombre' => 'x', 'tipo' => 'numero', 'min' => 5, 'max' => 1],
    ];
    foreach ($malos as $campo) {
        $lanzo = false;
        try {
            m_config(['checkout_campos' => [['etiqueta' => 'X'] + $campo]] + demos_cfg());
        } catch (LogicException) {
            $lanzo = true;
        }
        ok($lanzo, 'acepta ' . json_encode($campo));
    }
    $lanzo = false;
    try {
        m_config(['checkout_campos' => [['nombre' => 'x'], ['nombre' => 'x']]] + demos_cfg());
    } catch (LogicException) {
        $lanzo = true;
    }
    ok($lanzo, 'nombre repetido');
    m_config(['checkout_campos' => [['nombre' => str_repeat('a', 31), 'etiqueta' => 'Largo']]] + demos_cfg());
    eq(m_texto('checkout_titulo'), 'Finalizar compra', 'la config sigue usable');
    m_config(demos_cfg());
});

t('extras: se guardan como JSON y m_pedido_extras los devuelve en orden', function () {
    $cfg = demos_cfg_campos();
    m_config($cfg);
    m_db_asegurar($cfg, true);
    $db = m_db_abrir($cfg['db']);
    $cli = m_usuario_por_email($db, 'c@demo.cl');
    $agua = (int) $db->query("SELECT id FROM productos WHERE nombre = 'Agua'")->fetchColumn();
    [$d, $e] = m_checkout_validar($db, $cfg, $cli, ['entrega' => 'retiro', 'pago' => 'al_recibir',
        'extra' => ['patente' => 'kx-pl 47', 'vehiculo' => 'Kia "Río"', 'anio' => '2020', 'barbero' => 'nico', 'mayor' => '1']], 5000);
    eq($e, []);
    $_SESSION = ['carrito' => [$agua => 1]];
    [$id, $error] = m_pedido_crear($db, $cfg, $cli, $d, false);
    eq($error, '');
    $p = m_pedido($db, $id);
    $esperado = [['Patente', 'KXPL47'], ['Vehículo', 'Kia "Río"'], ['Año', '2020'], ['Barbero', 'Nico'], ['Soy mayor de 18 años', 'Sí']];
    eq(json_decode($p['extras'], true), $esperado);
    eq(m_pedido_extras($p), $esperado);
    // Pedido sin extras (y los del seed): columna vacía.
    eq((string) $db->query('SELECT extras FROM pedidos ORDER BY id LIMIT 1')->fetchColumn(), '');
    // Datos dañados o raros: se ignoran; los textos largos se recortan.
    eq(m_pedido_extras([]), []);
    eq(m_pedido_extras(['extras' => '']), []);
    eq(m_pedido_extras(['extras' => 'no es json']), []);
    eq(m_pedido_extras(['extras' => '{"a":"b"}']), []);
    eq(m_pedido_extras(['extras' => '[["a"],{"x":1},["b","c"],[["x"],"y"],["d",5]]']), [['b', 'c'], ['d', '5']]);
    $largo = m_pedido_extras(['extras' => json_encode([[str_repeat('e', 100), str_repeat('v', 500)]])]);
    eq([mb_strlen($largo[0][0]), mb_strlen($largo[0][1])], [60, 300]);
    // El seed acepta 'extras' => [['Etiqueta', 'valor']].
    $seed = require $cfg['seed'];
    $seed['pedidos'][0]['extras'] = [['Patente', 'ABCD12']];
    $ruta = $GLOBALS['demos_tmp'] . '/seed-extras.sqlite';
    m_db_crear($ruta, $seed, $cfg, demos_martes());
    $db2 = m_db_abrir($ruta);
    eq(m_pedido_extras(m_pedido($db2, 1)), [['Patente', 'ABCD12']]);
    $db2 = null;
    m_config(demos_cfg());
});

t('m_db_migrar agrega pedidos.extras a una base anterior', function () {
    $ruta = $GLOBALS['demos_tmp'] . '/antigua-extras.sqlite';
    $db = m_db_abrir($ruta);
    $db->exec("CREATE TABLE pedidos (id INTEGER PRIMARY KEY, cuando TEXT NOT NULL DEFAULT '')");
    $db->exec("INSERT INTO pedidos (cuando) VALUES ('12:00')");
    m_db_migrar($db);
    eq($db->query('SELECT extras FROM pedidos')->fetchColumn(), '');
    $db = null;
});

t('resumen WhatsApp incluye "Etiqueta: valor" de cada extra', function () {
    m_config(demos_cfg());
    $p = ['codigo' => 'TP-1001', 'items' => [['cantidad' => 1, 'nombre' => 'Agua', 'precio_unitario' => 1000]], 'costo_despacho' => 0, 'total' => 1000,
        'entrega' => 'retiro', 'comuna' => '', 'cuando' => '17:00', 'fecha_entrega' => '', 'pago' => 'al_recibir', 'nota' => 'Sin nueces',
        'extras' => json_encode([['Patente', 'AB1234'], ['Vehículo', 'Corolla']])];
    contiene(m_pedido_resumen_texto($p), "Para las 17:00\nPatente: AB1234\nVehículo: Corolla\nPago: al recibir\nNota: Sin nueces");
    no_contiene(m_pedido_resumen_texto(['extras' => ''] + $p), 'Patente');
});

t('extras con <script> y comillas salen escapados en panel y pedido', function () {
    m_config(['rubro' => ['rubro_nombre' => 'prueba', 'url_rubro' => '/demos/']] + demos_cfg());
    $_SESSION = ['csrf' => 'x'];
    $malo = '<script>alert(1)</script> "doble" \'simple\'';
    $pedido = demos_pedido(['extras' => json_encode([['Dato "raro"', $malo]])]);
    $dueno = ['id' => 3, 'nombre' => 'D', 'rol' => 'dueno', 'tipo' => 'particular', 'estado' => 'activo'];
    $html = demos_vista('pedido', ['pedido' => $pedido, 'wa' => m_wa(m_pedido_resumen_texto($pedido)), 'usuario' => $dueno])
        . demos_vista('panel', ['ver' => 'pedidos', 'estado' => '', 'ventas' => [], 'conteo' => array_fill_keys(array_keys(M_ESTADOS), 0),
            'pedidos' => [$pedido], 'productos' => [], 'pendientes' => [], 'aprobados' => [], 'usuario' => $dueno])
        . demos_vista('cuenta', ['usuario' => ['email' => 'a@demo.cl', 'empresa' => ''] + $dueno, 'direcciones' => [], 'pedidos' => [$pedido], 'errores' => [], 'previo' => []]);
    no_contiene($html, '<script>alert');
    no_contiene($html, '"doble"');
    eq(substr_count($html, '&lt;script&gt;alert(1)&lt;/script&gt; &quot;doble&quot; &#039;simple&#039;'), 3, 'pedido, panel y cuenta');
    contiene($html, 'Dato &quot;raro&quot;');
    contiene($html, rawurlencode('Dato "raro": ' . $malo));
    m_config(demos_cfg());
});

/* ---------------------------------------------------------------- Pedido mínimo y despacho gratis */

t('minimo: falta calcula lo que falta', function () {
    $cfg = ['pedido_minimo' => 15000] + demos_cfg();
    eq(m_falta_minimo($cfg, 9000, 'retiro'), 6000);
    eq(m_falta_minimo($cfg, 9000, 'despacho'), 6000);
    eq(m_falta_minimo($cfg, 15000, 'despacho'), 0);
    eq(m_falta_minimo($cfg, 20000, 'retiro'), 0);
    $cfg['pedido_minimo_solo_despacho'] = true;
    eq(m_falta_minimo($cfg, 9000, 'retiro'), 0, 'solo despacho: el retiro no tiene mínimo');
    eq(m_falta_minimo($cfg, 9000, 'despacho'), 6000);
    eq(m_falta_minimo(['pedido_minimo' => 0] + demos_cfg(), 100, 'despacho'), 0);
    eq(m_falta_minimo([], 100, 'retiro'), 0);
    eq(m_config_base()['pedido_minimo_solo_despacho'], false);
});

t('gratis: lo que falta para despacho gratis', function () {
    eq(m_falta_gratis(['despacho_gratis_desde' => 20000], 15000), 5000);
    eq(m_falta_gratis(['despacho_gratis_desde' => 20000], 20000), 0);
    eq(m_falta_gratis(['despacho_gratis_desde' => 20000], 25000), 0);
    eq(m_falta_gratis(['despacho_gratis_desde' => 0], 100), 0);
    eq(m_falta_gratis([], 100), 0);
});

t('minimo: el checkout rechaza un despacho bajo el mínimo y, si no es solo despacho, también el retiro', function () {
    $cfg = ['pedido_minimo' => 15000, 'pedido_minimo_solo_despacho' => true] + demos_cfg();
    m_config($cfg);
    m_db_asegurar($cfg, true);
    $db = m_db_abrir($cfg['db']);
    $cli = m_usuario_por_email($db, 'c@demo.cl');
    $agua = (int) $db->query("SELECT id FROM productos WHERE nombre = 'Agua'")->fetchColumn();
    $despacho = ['entrega' => 'despacho', 'pago' => 'al_recibir', 'calle' => 'Calle 1', 'comuna' => 'Maipú'];
    [$d, $e] = m_checkout_validar($db, $cfg, $cli, $despacho, 9000);
    eq($d, null);
    eq($e['minimo'], 'Para despacho, el pedido mínimo es $15.000: te faltan $6.000. El retiro no tiene mínimo.');
    [$d, $e] = m_checkout_validar($db, $cfg, $cli, ['entrega' => 'retiro', 'pago' => 'al_recibir'], 9000);
    eq($e, [], 'retiro bajo el mínimo pasa');
    $_SESSION = ['carrito' => [$agua => 2]];
    [$id, $error] = m_pedido_crear($db, $cfg, $cli, $d, false);
    eq($error, '');
    ok($id > 0);
    // Aunque el navegador lo deje pasar (o la sesión traiga datos viejos), el servidor no crea un despacho bajo el mínimo.
    $_SESSION = ['carrito' => [$agua => 2]];
    [$id, $error] = m_pedido_crear($db, $cfg, $cli, ['entrega' => 'despacho', 'comuna' => 'Maipú', 'direccion' => 'Calle 1', 'cuando' => '', 'nota' => '', 'pago' => 'al_recibir'], false);
    eq($id, null);
    eq($error, 'El pedido mínimo es $15.000.');
    // Sin 'pedido_minimo_solo_despacho' (como hoy en los rubros): el mínimo vale para todo y, como siempre,
    // lo corta m_pedido_crear (la acción vuelve al carrito con el mensaje), no la validación.
    $cfg['pedido_minimo_solo_despacho'] = false;
    [$d, $e] = m_checkout_validar($db, $cfg, $cli, ['entrega' => 'retiro', 'pago' => 'al_recibir'], 9000);
    eq($e, []);
    $_SESSION = ['carrito' => [$agua => 2]];
    [$id, $error] = m_pedido_crear($db, $cfg, $cli, $d, false);
    eq([$id, $error], [null, 'El pedido mínimo es $15.000.'], 'retiro bajo el mínimo');
    eq(m_aviso_minimo($cfg, 9000, 'retiro'), 'Te faltan $6.000 para el pedido mínimo de $15.000.');
    [, $e] = m_checkout_validar($db, $cfg, $cli, $despacho, 15000);
    eq($e, []);
    m_config(demos_cfg());
});

t('carrito: avisa lo que falta para el mínimo y para despacho gratis', function () {
    $_SESSION = ['csrf' => 'x'];
    $p = ['id' => 1, 'nombre' => 'Agua', 'precio' => 9000, 'precio_mayorista' => null, 'stock' => 9, 'foto' => ''];
    $carrito = ['lineas' => [['producto' => $p, 'cantidad' => 1, 'precio' => 9000, 'total' => 9000]], 'subtotal' => 9000, 'unidades' => 1, 'avisos' => []];
    m_config(['pedido_minimo' => 15000, 'despacho_gratis_desde' => 20000] + demos_cfg());
    $h = demos_vista('carrito', ['carrito' => $carrito]);
    contiene($h, 'Te faltan $6.000 para el pedido mínimo de $15.000.');
    contiene($h, 'Te faltan $11.000 para despacho gratis.');
    no_contiene($h, 'href="/demos/test/?r=checkout"');
    contiene($h, 'aria-disabled="true"');
    m_config(['pedido_minimo' => 15000, 'pedido_minimo_solo_despacho' => true, 'despacho_gratis_desde' => 0] + demos_cfg());
    $h = demos_vista('carrito', ['carrito' => $carrito]);
    contiene($h, 'Para despacho, el pedido mínimo es $15.000: te faltan $6.000. El retiro no tiene mínimo.');
    contiene($h, 'href="/demos/test/?r=checkout"');
    no_contiene($h, 'aria-disabled');
    no_contiene($h, 'despacho gratis');
    // Sobre el mínimo y sobre el gratis: sin avisos, como siempre.
    m_config(['pedido_minimo' => 5000, 'despacho_gratis_desde' => 8000] + demos_cfg());
    $h = demos_vista('carrito', ['carrito' => $carrito]);
    no_contiene($h, 'Te faltan');
    contiene($h, 'href="/demos/test/?r=checkout"');
    m_config(demos_cfg());
});

/* ---------------------------------------------------------------- Fechas y horas de entrega reales */

/** Instante fijo en Santiago: demos_ahora('2026-11-17 18:10'). */
function demos_ahora(string $fechaHora): int
{
    return (new DateTimeImmutable($fechaHora, new DateTimeZone('America/Santiago')))->getTimestamp();
}

/** demos_cfg() con 'entrega' propio (array_replace: la base ya trae 'entrega'). */
function demos_cfg_entrega(array $entrega, array $extra = []): array
{
    return array_replace(demos_cfg(), ['entrega' => $entrega], $extra);
}

t('horas: hoy no ofrece horas pasadas', function () {
    $cfg = ['horas_entrega' => ['12:00', '23:00']] + demos_cfg();
    $h = m_horas_entrega($cfg, '', demos_ahora('2026-11-17 18:10'));
    eq($h[0], '19:00', '18:10 + 30 min de margen');
    eq(end($h), '23:00');
    eq(m_horas_entrega($cfg, '', demos_ahora('2026-11-17 09:00'))[0], '12:00', 'antes de abrir: todas');
    eq(count(m_horas_entrega($cfg, '', demos_ahora('2026-11-17 09:00'))), 23);
    eq(m_horas_entrega($cfg, '', demos_ahora('2026-11-17 22:45')), [], 'cocina cerrada: ninguna');
    eq(m_horas_entrega($cfg, '', demos_ahora('2026-11-17 22:30')), ['23:00'], 'justo en el margen');
    eq(m_horas_entrega(demos_cfg_entrega(['margen_minutos' => 0], ['horas_entrega' => ['12:00', '23:00']]), '', demos_ahora('2026-11-17 18:10'))[0], '18:30');
    eq(m_horas_entrega(demos_cfg_entrega(['margen_minutos' => 90], ['horas_entrega' => ['12:00', '23:00']]), '', demos_ahora('2026-11-17 18:10'))[0], '20:00');
    // margen_minutos false: todas las horas siempre (como antes).
    eq(count(m_horas_entrega(demos_cfg_entrega(['margen_minutos' => false], ['horas_entrega' => ['12:00', '23:00']]), '', demos_ahora('2026-11-17 22:45'))), 23);
    eq(m_entrega_cfg(demos_cfg())['margen_minutos'], 30);
    ok(m_horas_entrega($cfg) === m_horas_entrega($cfg, ''), 'sin $ahora usa la hora actual');
});

t('horas: otro día ofrece todas', function () {
    $cfg = demos_cfg_entrega(['modo' => 'fecha', 'horas' => ['10:00', '12:00'], 'cerrado' => []]);
    $todas = ['10:00', '10:30', '11:00', '11:30', '12:00'];
    eq(m_horas_entrega($cfg, '2026-11-18', demos_ahora('2026-11-17 18:10')), $todas, 'mañana');
    eq(m_horas_entrega($cfg, '2026-11-20', demos_ahora('2026-11-17 11:50')), $todas);
    eq(m_horas_entrega($cfg, '', demos_ahora('2026-11-17 18:10')), $todas, 'modo fecha sin día: todas (el checkout las filtra al elegir hoy)');
    eq(m_horas_entrega($cfg, '2026-11-17', demos_ahora('2026-11-17 11:10')), ['12:00'], 'hoy: solo las que quedan');
    eq(m_horas_entrega($cfg, '2026-11-16', demos_ahora('2026-11-17 09:00')), [], 'ayer: ninguna');
    eq(m_horas_entrega($cfg, 'mañana', demos_ahora('2026-11-17 09:00')), [], 'fecha no válida: ninguna');
});

t('horas: pasan la medianoche', function () {
    $cfg = ['horas_entrega' => ['12:30', '02:00']] + demos_cfg();
    $h = m_horas_entrega($cfg, '', demos_ahora('2026-11-17 10:00'));
    eq($h[0], '12:30');
    eq(array_slice($h, -5), ['00:00', '00:30', '01:00', '01:30', '02:00']);
    eq(count($h), 28);
    eq(count(array_unique($h)), 28, 'sin horas repetidas');
    eq(m_horas_entrega(['horas_entrega' => ['00:00', '24:00']] + demos_cfg(), '', demos_ahora('2026-11-17 00:00'))[0], '00:30', 'día completo');
    eq(count(m_horas_entrega(demos_cfg_entrega(['margen_minutos' => false], ['horas_entrega' => ['00:00', '24:00']]))), 48, '24 horas sin repetir las 00:00');
});

t('horas: 00:40 con cierre 02:00 es el servicio de ayer', function () {
    $cfg = ['horas_entrega' => ['12:30', '02:00']] + demos_cfg();
    eq(m_horas_entrega($cfg, '', demos_ahora('2026-11-18 00:40')), ['01:30', '02:00']);
    eq(m_horas_entrega($cfg, '', demos_ahora('2026-11-17 23:50')), ['00:30', '01:00', '01:30', '02:00']);
    eq(m_horas_entrega($cfg, '', demos_ahora('2026-11-18 01:45')), [], 'cerrando: ya no quedan');
    eq(m_horas_entrega($cfg, '', demos_ahora('2026-11-18 03:00'))[0], '12:30', 'después del cierre: el servicio de hoy completo');
    // Modo fecha: el día elegido es el día en que empieza el servicio.
    $cfg = demos_cfg_entrega(['modo' => 'fecha', 'horas' => ['12:30', '02:00'], 'cerrado' => []]);
    eq(m_horas_entrega($cfg, '2026-11-17', demos_ahora('2026-11-18 00:40')), ['01:30', '02:00'], 'servicio del martes, el miércoles 00:40');
    eq(m_horas_entrega($cfg, '2026-11-18', demos_ahora('2026-11-18 00:40'))[0], '12:30', 'servicio del miércoles completo');
});

t('fechas: hoy sale de la lista si ya no quedan horas', function () {
    $cfg = demos_cfg_entrega(['modo' => 'fecha', 'dias_min' => 0, 'dias_max' => 3, 'cerrado' => [], 'horas' => ['12:00', '23:00']]);
    $f = m_fechas_entrega($cfg, demos_ahora('2026-11-17 23:50'));
    eq(array_key_first($f), '2026-11-18');
    eq($f['2026-11-18'], 'Mañana, mié 18/11');
    eq(count($f), 3);
    $f = m_fechas_entrega($cfg, demos_ahora('2026-11-17 10:00'));
    eq(array_key_first($f), '2026-11-17');
    eq($f['2026-11-17'], 'Hoy, mar 17/11');
    // Con margen_minutos false, hoy sigue en la lista (como antes).
    $cfg['entrega']['margen_minutos'] = false;
    eq(array_key_first(m_fechas_entrega($cfg, demos_ahora('2026-11-17 23:50'))), '2026-11-17');
});

t('checkout: rechaza horas y días que no se ofrecen en ese momento', function () {
    $cfg = ['horas_entrega' => ['12:00', '23:00']] + demos_cfg();
    m_config($cfg);
    m_db_asegurar($cfg, true);
    $db = m_db_abrir($cfg['db']);
    $cli = m_usuario_por_email($db, 'c@demo.cl');
    $base = ['entrega' => 'retiro', 'pago' => 'al_recibir'];
    $a = demos_ahora('2026-11-17 18:10');
    [, $e] = m_checkout_validar($db, $cfg, $cli, $base + ['cuando' => '12:00'], 5000, $a);
    ok(isset($e['cuando']), 'modo hoy: hora pasada');
    [$d, $e] = m_checkout_validar($db, $cfg, $cli, $base + ['cuando' => '19:00'], 5000, $a);
    eq($e, []);
    eq($d['cuando'], '19:00');
    [, $e] = m_checkout_validar($db, $cfg, $cli, $base + ['cuando' => ''], 5000, $a);
    eq($e, [], 'lo antes posible');
    // Modo fecha: la hora se revisa contra el día elegido.
    $cfg = demos_cfg_entrega(['modo' => 'fecha', 'dias_min' => 0, 'dias_max' => 3, 'cerrado' => [], 'horas' => ['12:00', '23:00']]);
    [, $e] = m_checkout_validar($db, $cfg, $cli, $base + ['fecha_entrega' => '2026-11-17', 'cuando' => '12:00'], 5000, $a);
    ok(isset($e['cuando']), 'hoy a una hora pasada');
    [, $e] = m_checkout_validar($db, $cfg, $cli, $base + ['fecha_entrega' => '2026-11-18', 'cuando' => '12:00'], 5000, $a);
    eq($e, [], 'mañana a esa hora sí');
    [, $e] = m_checkout_validar($db, $cfg, $cli, $base + ['fecha_entrega' => '2026-11-17'], 5000, demos_ahora('2026-11-17 23:50'));
    ok(isset($e['fecha_entrega']), 'hoy ya no se ofrece a las 23:50');
    m_config(demos_cfg());
});

t('checkout: el día de hoy lleva sus horas para que tienda.js oculte las pasadas', function () {
    $_SESSION = ['csrf' => 'x'];
    m_config(demos_cfg());
    $p = ['id' => 1, 'nombre' => 'Agua', 'precio' => 9000, 'precio_mayorista' => null, 'stock' => 9, 'foto' => ''];
    $carrito = ['lineas' => [['producto' => $p, 'cantidad' => 1, 'precio' => 9000, 'total' => 9000]], 'subtotal' => 9000, 'unidades' => 1, 'avisos' => []];
    $h = demos_vista('checkout', ['carrito' => $carrito, 'direcciones' => [], 'errores' => [], 'previo' => [], 'horas' => ['12:00', '12:30', '13:00'],
        'usuario' => ['nombre' => 'Ana', 'tipo' => 'particular', 'estado' => 'activo'], 'con_fecha' => true,
        'fechas' => ['2026-11-17' => 'Hoy, mar 17/11', '2026-11-18' => 'Mañana, mié 18/11'], 'horas_por_fecha' => ['2026-11-17' => ['13:00']]]);
    contiene($h, '<option value="2026-11-17" data-horas="13:00" selected>Hoy, mar 17/11</option>');
    contiene($h, '<option value="2026-11-18">Mañana, mié 18/11</option>');
});

t('seed: fecha_entrega salta días cerrados', function () {
    $viernes = demos_ahora('2026-11-20 10:00');
    $cfg = demos_cfg_entrega(['modo' => 'fecha', 'cerrado' => [7, '2026-11-23']]);
    eq(m_seed_fecha(2, $cfg, $viernes), '2026-11-24', 'domingo cerrado y lunes feriado: martes');
    eq(m_seed_fecha(1, $cfg, $viernes), '2026-11-21');
    eq(m_seed_fecha('2026-11-22', $cfg, $viernes), '2026-11-22', 'una fecha fija se respeta');
    $cfg = demos_cfg_entrega(['modo' => 'fecha'], ['negocio' => ['nombre' => 'X', 'horario' => [1 => ['9:00', '18:00'], 2 => ['9:00', '18:00'], 3 => ['9:00', '18:00'], 4 => ['9:00', '18:00'], 5 => ['9:00', '18:00'], 6 => ['9:00', '14:00'], 7 => null]]]);
    eq(m_seed_fecha(2, $cfg, $viernes), '2026-11-23', 'sin cerrado: los días sin horario');
    eq(m_seed_fecha(2, demos_cfg(), $viernes), '2026-11-22', 'sin días cerrados: igual que antes');
    eq(m_seed_fecha(-1, demos_cfg_entrega(['modo' => 'fecha', 'cerrado' => [7]]), demos_ahora('2026-11-23 10:00')), '2026-11-21', 'un pedido pasado sigue en el pasado');
});

/* ---------------------------------------------------------------- Catálogo: código, búsqueda por palabras, chips, ids */

/** Tienda de prueba con códigos de producto (otro archivo de base). */
function demos_cfg_catalogo(): array
{
    $cfg = demos_cfg();
    $seed = require $cfg['seed'];
    $seed['categorias'][] = ['slug' => 'frenos', 'nombre' => 'Frenos'];
    $seed['productos'][] = ['slug' => 'pastillas', 'categoria' => 'frenos', 'nombre' => 'Pastillas de freno delanteras', 'descripcion' => 'Para Yaris 2010–2018', 'codigo' => 'CD-1101', 'precio' => 25990, 'stock' => 8];
    $seed['productos'][] = ['slug' => 'disco', 'categoria' => 'frenos', 'nombre' => 'Disco de freno', 'codigo' => 'DF 220', 'precio' => 39990, 'stock' => 2];
    $cfg['seed'] = $GLOBALS['demos_tmp'] . '/seed-catalogo.php';
    $cfg['db'] = $GLOBALS['demos_tmp'] . '/catalogo.sqlite';
    file_put_contents($cfg['seed'], '<?php return ' . var_export($seed, true) . ';');
    return $cfg;
}

t('demo_coincide: cada palabra debe estar, sin tildes, y los códigos con o sin guion', function () {
    ok(demo_coincide('Pastillas de freno delanteras', 'pastilla freno'));
    ok(demo_coincide('Pastillas de freno delanteras', 'FRENO   pastillas'));
    ok(!demo_coincide('Pastillas de freno delanteras', 'pastilla trasera'));
    ok(demo_coincide('Ñoquis de papa', 'noquis'));
    ok(demo_coincide('Cód. CD-1101', 'cd1101'));
    ok(demo_coincide('Cód. CD1101', 'CD-1101'));
    ok(demo_coincide('Cód. CD-1101', 'cd 1101'));
    ok(demo_coincide('Lo que sea', ''), 'búsqueda vacía');
    ok(!demo_coincide('Disco DF 220', 'df-221'));
});

t('catalogo: busca por palabras sueltas', function () {
    $cfg = demos_cfg_catalogo();
    m_db_asegurar($cfg, true);
    $db = m_db_abrir($cfg['db']);
    eq(array_column(m_catalogo($db, null, 'pastilla freno'), 'nombre'), ['Pastillas de freno delanteras']);
    eq(array_column(m_catalogo($db, null, 'freno'), 'nombre'), ['Pastillas de freno delanteras', 'Disco de freno']);
    eq(array_column(m_catalogo($db, null, 'yaris freno'), 'nombre'), ['Pastillas de freno delanteras'], 'palabras en nombre y descripción');
    eq(m_catalogo($db, null, 'pastilla trasera'), []);
    eq(array_column(m_catalogo($db, null, 'frenos'), 'nombre'), ['Pastillas de freno delanteras', 'Disco de freno'], 'por categoría');
    eq(count(m_catalogo($db, null, 'masa')), 0, 'solo mayorista: no aparece para un particular');
});

t('catalogo: busca por código con o sin guion', function () {
    $cfg = demos_cfg_catalogo();
    m_db_asegurar($cfg, true);
    $db = m_db_abrir($cfg['db']);
    foreach (['cd1101', 'CD-1101', 'cd 1101', '1101'] as $q) {
        eq(array_column(m_catalogo($db, null, $q), 'codigo'), ['CD-1101'], $q);
    }
    eq(array_column(m_catalogo($db, null, 'df220'), 'codigo'), ['DF 220']);
    eq((string) $db->query("SELECT codigo FROM productos WHERE nombre = 'Agua'")->fetchColumn(), '', 'sin código en el seed: vacío');
});

t('catalogo: categoría sin productos visibles para el usuario no da chip', function () {
    $cfg = demos_cfg();
    m_config($cfg);
    m_db_asegurar($cfg, true);
    $db = m_db_abrir($cfg['db']);
    $may = m_usuario_por_email($db, 'm@demo.cl');
    eq(m_categorias_visibles($db, null), ['pizzas']);
    eq(m_categorias_visibles($db, m_usuario_por_email($db, 'c@demo.cl')), ['pizzas']);
    eq(m_categorias_visibles($db, $may), ['pizzas', 'negocios']);
    $_SESSION = ['csrf' => 'x'];
    $vars = ['categorias' => m_categorias($db), 'por_categoria' => [], 'destacados' => [], 'q' => '', 'cat' => '', 'hay_solo_mayorista' => true];
    $h = demos_vista('inicio', $vars + ['categorias_visibles' => m_categorias_visibles($db, null)]);
    contiene($h, '?cat=pizzas#menu');
    no_contiene($h, 'cat=negocios');
    $h = demos_vista('inicio', $vars + ['usuario' => $may, 'categorias_visibles' => m_categorias_visibles($db, $may)]);
    contiene($h, 'cat=negocios');
    $h = demos_vista('inicio', $vars);
    contiene($h, 'cat=negocios', 'sin categorias_visibles (vista vieja): todas, como antes');
    m_config(demos_cfg());
});

t('producto: código se ve como "Cód. X" solo si existe', function () {
    m_config(demos_cfg());
    $_SESSION = ['csrf' => 'x'];
    $p = ['id' => 4, 'nombre' => 'Pastillas', 'descripcion' => '', 'precio' => 1000, 'precio_mayorista' => null, 'precio_final' => 1000, 'stock' => 9,
        'foto' => '', 'modelo3d' => '', 'solo_mayorista' => 0, 'destacado' => 0, 'codigo' => 'CD-1101'];
    $h = demos_vista('_producto', ['p' => $p]);
    contiene($h, '<p class="t-producto__codigo">Cód. CD-1101</p>');
    contiene($h, 't-producto__foto t-producto__foto--vacia', 'sin foto: recuadro marcado');
    $h = demos_vista('_producto', ['p' => ['codigo' => ''] + $p]);
    no_contiene($h, 'Cód.');
    $h = demos_vista('_producto', ['p' => array_replace(array_diff_key($p, ['codigo' => 1]), ['foto' => '123-abc'])]);
    no_contiene($h, 'Cód.');
    no_contiene($h, 'foto--vacia');
    $h = demos_vista('_producto', ['p' => $p, 'con_id' => true, 'prefijo' => 'd']);
    contiene($h, 'id="dp4"');
    contiene(demos_vista('_producto', ['p' => $p, 'con_id' => true]), 'id="p4"');
});

t('panel: el producto guarda un código opcional de hasta 30 caracteres', function () {
    $cfg = demos_cfg();
    m_config($cfg);
    m_db_asegurar($cfg, true);
    $db = m_db_abrir($cfg['db']);
    $post = ['nombre' => 'Filtro', 'descripcion' => '', 'categoria_id' => '1', 'precio' => '5000', 'stock' => '3', 'codigo' => '  FA-77 ', 'visible' => '1'];
    [$id, $e] = m_producto_guardar($db, $cfg, null, $post);
    eq($e, []);
    eq(m_producto($db, $id)['codigo'], 'FA-77');
    [, $e] = m_producto_guardar($db, $cfg, $id, ['codigo' => str_repeat('x', 31)] + $post);
    ok(isset($e['codigo']), 'más de 30 caracteres');
    [, $e] = m_producto_guardar($db, $cfg, $id, ['codigo' => ''] + $post);
    eq($e, []);
    eq(m_producto($db, $id)['codigo'], '');
    $_SESSION = ['csrf' => 'x'];
    contiene(demos_vista('panel_producto', ['producto' => m_producto($db, $id), 'id' => $id, 'categorias' => m_categorias($db), 'fotos' => [], 'errores' => []]), 'name="codigo"');
});

/* ---------------------------------------------------------------- Panel del dueño */

t('panel: la lista de pedidos muestra la nota recortada y los extras', function () {
    m_config(demos_cfg());
    $_SESSION = ['csrf' => 'x'];
    $nota = str_repeat('Sin cebolla, por favor. ', 6);
    $pedidos = [
        demos_pedido(['nota' => $nota, 'extras' => json_encode([['Patente', 'AB1234'], ['Vehículo', 'Corolla']])]),
        demos_pedido(['id' => 8, 'codigo' => 'TP-1008', 'nota' => 'Corta', 'extras' => '']),
        demos_pedido(['id' => 9, 'codigo' => 'TP-1009', 'nota' => '', 'extras' => '']),
    ];
    $h = demos_vista('panel', ['ver' => 'pedidos', 'estado' => '', 'ventas' => [], 'conteo' => array_fill_keys(array_keys(M_ESTADOS), 0),
        'pedidos' => $pedidos, 'productos' => [], 'pendientes' => [], 'aprobados' => [], 'usuario' => ['rol' => 'dueno']]);
    contiene($h, 'Nota: ' . m_e(rtrim(mb_substr($nota, 0, 80))) . '…</p>');
    no_contiene($h, m_e(mb_substr($nota, 0, 81)));
    contiene($h, '>Patente: AB1234 · Vehículo: Corolla</p>');
    contiene($h, '>Nota: Corta</p>');
    eq(substr_count($h, 'Nota: '), 2, 'sin nota no hay línea');
    eq(m_recortar('abc', 80), 'abc');
    eq(m_recortar(str_repeat('á', 81), 80), str_repeat('á', 80) . '…');
    eq(m_recortar('hola mundo ', 5), 'hola…');
});

/* ---------------------------------------------------------------- Base común: nota de reserva y 24 horas */

t('demo_nota: pedido por defecto y reserva o cotización', function () {
    eq(demo_nota('barbería', 'Multi-page'), '(Pedido de prueba desde la demo de barbería – Multi-page)');
    eq(demo_nota('barbería', 'Multi-page', 'Reserva'), '(Reserva de prueba desde la demo de barbería – Multi-page)');
    eq(demo_nota('ferretería', 'Simple-page', 'Cotización'), '(Cotización de prueba desde la demo de ferretería – Simple-page)');
});

t('horario de 24 horas: abierto siempre y «24 horas» en la tabla', function () {
    $tz = new DateTimeZone('America/Santiago');
    $h24 = array_fill(1, 7, ['00:00', '24:00']);
    foreach (['2026-11-17 00:00', '2026-11-17 03:15', '2026-11-17 23:59', '2026-11-22 12:00'] as $cuando) {
        eq(demo_horario_estado($h24, new DateTimeImmutable($cuando, $tz)), ['abierto' => true, 'texto' => 'Abierto las 24 horas'], $cuando);
    }
    eq(demo_horario_filas($h24), [['Lun a Dom', '24 horas']]);
    // Mezcla: 24 horas de lunes a sábado, domingo con horario.
    $mix = [1 => ['00:00', '24:00'], 2 => ['00:00', '24:00'], 3 => ['00:00', '24:00'], 4 => ['00:00', '24:00'], 5 => ['00:00', '24:00'], 6 => ['00:00', '24:00'], 7 => ['10:00', '20:00']];
    eq(demo_horario_filas($mix), [['Lun a Sáb', '24 horas'], ['Dom', '10:00 – 20:00']]);
    // Domingo 2026-11-22 a las 08:00: el sábado terminó a las 24:00 y el domingo abre a las 10:00.
    eq(demo_horario_estado($mix, new DateTimeImmutable('2026-11-22 08:00', $tz)), ['abierto' => false, 'texto' => 'Cerrado · abre hoy a las 10:00']);
    eq(demo_horario_estado($mix, new DateTimeImmutable('2026-11-22 21:00', $tz)), ['abierto' => false, 'texto' => 'Cerrado · abre mañana a las 00:00']);
    eq(demo_horario_estado($mix, new DateTimeImmutable('2026-11-21 23:30', $tz))['texto'], 'Abierto las 24 horas');
    // Lo de siempre no cambia.
    $h = [1 => ['12:00', '22:00'], 2 => ['12:00', '22:00'], 3 => ['12:00', '22:00'], 4 => ['12:00', '22:00'], 5 => ['12:00', '22:00'], 6 => ['12:00', '22:00'], 7 => ['12:00', '22:00']];
    eq(demo_horario_filas($h), [['Lun a Dom', '12:00 – 22:00']]);
    eq(demo_horario_estado($h, new DateTimeImmutable('2026-11-17 13:00', $tz))['texto'], 'Abierto · cierra a las 22:00');
});

t('demo.css: el título del perfil de Google es más específico que los h3 de cada rubro', function () {
    $css = (string) file_get_contents(__DIR__ . '/../demos/_comun/demo.css');
    contiene($css, '.demo-google .demo-google__perfil h3 { margin: 0 0 4px; font-size: 22px; font-weight: 500; color: #202124; font-family: inherit; }');
});

// Limpieza de archivos temporales.
foreach (glob($GLOBALS['demos_tmp'] . '/*') as $f) {
    @unlink($f);
}
@rmdir($GLOBALS['demos_tmp']);
