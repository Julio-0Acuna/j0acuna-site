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

// Limpieza de archivos temporales.
foreach (glob($GLOBALS['demos_tmp'] . '/*') as $f) {
    @unlink($f);
}
@rmdir($GLOBALS['demos_tmp']);
