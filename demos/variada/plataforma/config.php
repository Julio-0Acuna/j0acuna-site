<?php
declare(strict_types=1);

// Mediodía (Huechuraba): negocio ficticio de la versión Plataforma web.
// Cocina de colaciones con tienda online: particulares y empresas (mayoristas), entrega con fecha.
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
// La cocina atiende de lunes a viernes; los repartos son entre las 12:00 y las 14:30.
$horario = [
    1 => ['08:30', '16:00'],
    2 => ['08:30', '16:00'],
    3 => ['08:30', '16:00'],
    4 => ['08:30', '16:00'],
    5 => ['08:30', '16:00'],
    6 => null,
    7 => null,
];

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/variada/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_variada_plataforma',
    'prefijo_pedido' => 'MD',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Mediodía',
        'eslogan' => 'Almuerzos caseros para tu oficina, con día y hora de entrega',
        'comuna' => 'Huechuraba',
        'horario' => $horario,
    ],

    'mayoristas' => true,
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Huechuraba' => 990,
        'Conchalí' => 1490,
        'Recoleta' => 1990,
        'Vitacura' => 1990,
        'Quilicura' => 1990,
        'Independencia' => 2490,
        'Las Condes' => 2490,
        'Providencia' => 2490,
    ],
    'despacho_gratis_desde' => 30000,
    'pedido_minimo' => 5990,
    'horas_entrega' => ['12:00', '14:30'],
    // Entrega con fecha: desde mañana hasta en 12 días, solo días hábiles (sin sábados, domingos ni feriados).
    'entrega' => [
        'modo' => 'fecha',
        'dias_min' => 1,
        'dias_max' => 12,
        'cerrado' => [6, 7, '2026-12-08', '2026-12-25', '2027-01-01'],
        'horas' => ['12:00', '14:30'],
    ],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    'menu_qr' => true,
    'modelos3d' => ['hamburguesa', 'coffee-break-15'],

    // Menú de la semana (portada): plato de fondo de la «Colación casera» según el día (1 = lunes).
    'semana' => [
        1 => ['Pollo arvejado con arroz', 'Ensalada de lechuga y zanahoria · jalea', '1749640566096-5d8098d452b4'],
        2 => ['Tallarines con salsa boloñesa', 'Ensalada chilena · fruta picada', '1622973536968-3ead9e780960'],
        3 => ['Bistec a la plancha con papas', 'Ensalada de choclo y repollo · flan', '1653982151807-a2ee87286ae1'],
        4 => ['Crema de zapallo y pollo al horno', 'Ensalada verde · arroz con leche', '1476718406336-bb5a9690ee2a'],
        5 => ['Salmón al horno con quinoa', 'Ensalada de pepino y tomate · fruta', '1606858374191-c18040e98ad7'],
    ],

    'textos' => [
        'titulo_inicio' => 'Colaciones para oficinas en Huechuraba',
        'titulo_destacados' => 'Lo que más piden los equipos',
        'titulo_catalogo' => 'Menú',
        'ver_catalogo' => 'Ver el menú',
        'buscar_placeholder' => 'Busca colación, ensalada, coffee break…',
        'sello_destacado' => 'Favorita',
        // Mayoristas = empresas (sustantivo femenino: se cambian también las claves con género).
        'mayorista' => 'Empresa',
        'mayoristas' => 'Empresas',
        'mayorista_titulo' => '¿Almuerzan juntos en tu empresa?',
        'mayorista_texto' => 'Crea una cuenta para empresas: cuando la aprobemos verás precio empresa en todas las colaciones y los packs para reuniones, coffee break y almuerzos de equipo.',
        'mayorista_quien' => 'Oficinas, colegios, faenas y equipos de trabajo.',
        'crear_cuenta_mayorista' => 'Crear cuenta para empresas',
        'precio_mayorista' => 'Precio empresa',
        'cuenta_aprobada' => 'Aprobada: ves precios para empresas',
        'registro_pendiente' => 'Cuenta creada. Tu cuenta de empresa queda pendiente hasta que la aprobemos; mientras, ves precios normales.',
        'mayorista_aprobado' => 'Empresa aprobada: ya ve sus precios.',
        'mayoristas_aprobados' => 'Empresas aprobadas',
        'mayoristas_esperan' => '{n} empresa(s) esperan aprobación',
        'retiro_titulo' => 'Retiro en la cocina',
        'retiro_texto' => 'Sin costo, en nuestra cocina de Huechuraba.',
        'despacho_titulo' => 'Despacho a tu oficina',
        'fecha_titulo' => '¿Para qué día?',
        'fecha_ayuda' => 'Cocinamos cada mañana: pide con al menos un día de anticipación. Repartimos de lunes a viernes, entre las 12:00 y las 14:30.',
        'hora_titulo' => '¿A qué hora?',
        'hora_cualquiera' => 'Entre 12:00 y 14:30',
        'hora_entrega_fecha' => 'A las {hora}',
        'qr_titulo' => 'El menú de la semana, pegado en la cocina de la oficina',
        'qr_texto' => 'Imprime este QR y ponlo en el comedor: cada persona ve el menú de la semana y pide su colación para mañana.',
        'qr_pie' => 'Código QR del menú',
        'pedido_titulo' => '¡Colación agendada!',
        'pedido_exito_prueba' => '¡Pago de prueba aprobado! Tu pedido quedó agendado.',
        'pedido_exito_al_recibir' => '¡Pedido agendado! Pagas al recibir.',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Pide con precios normales; tiene su oficina guardada y pedidos para repetir.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Empresa', 'detalle' => 'Empresa aprobada: ve precio empresa y los packs para reuniones y equipos.', 'email' => 'empresa@demo.cl', 'clave' => 'empresa123'],
        ['titulo' => 'Dueño', 'detalle' => 'Panel: pedidos por día de entrega, productos, stock, empresas y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que el dueño puede elegir al crear o editar un producto (no hay subida de archivos).
    'fotos' => [
        '1616645258469-ec681c17f3ee' => 'Bandeja de colación con arroz',
        '1616645297079-dfaf44a6f977' => 'Bandeja con apanado y arroz',
        '1543353071-c953d88f7033' => 'Colación vegetariana en envases',
        '1586190848861-99aa4a171e90' => 'Hamburguesa',
        '1757522745249-d9a075cf38de' => 'Ensalada con pollo',
        '1631311695255-8dde6bf96cb5' => 'Bowl de garbanzos',
        '1510431198580-7727c9fa1e3a' => 'Crema de zapallo',
        '1583577612013-4fecf7bf8f13' => 'Jugos naturales',
        '1780798465831-de1e4d58ee46' => 'Flan',
        '1666819691666-4be36926335e' => 'Colaciones en bandejas negras',
        '1633424411431-5eb8d0e96488' => 'Mini hamburguesas',
        '1555244162-803834f70033' => 'Buffet',
        '1543352632-5a4b24e4d2a6' => 'Colaciones en vidrio',
        '1653982151807-a2ee87286ae1' => 'Bistec con papas',
        '1749640566096-5d8098d452b4' => 'Pollo con arroz',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Mediodía',
        'categoria' => 'Servicio de colaciones para empresas',
        'comuna' => 'Huechuraba',
        'horario' => $horario,
        'nota' => 4.8,
        'resenas' => [
            ['Javiera L. (RR. HH.)', 5, 'Pedimos 25 colaciones cada viernes para el equipo. Elijo el día en la web, repito el pedido de la semana pasada y llega a las 12:30 en punto.', 'hace 1 semana'],
            ['Tomás E.', 5, 'La colación casera es como almorzar en la casa: porción justa, ensalada y postre. El precio empresa nos sale más barato que el casino.', 'hace 3 semanas'],
            ['Paula R.', 4, 'Muy rico el salmón de los viernes. Hay que pedir el día anterior, pero la página te deja agendar toda la semana.', 'hace 1 mes'],
        ],
    ],
];
