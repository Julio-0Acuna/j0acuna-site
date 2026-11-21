<?php
declare(strict_types=1);

// Nori Club (Las Condes): negocio ficticio de la versión Plataforma web.
// Cadena de sushi con delivery y tienda online. Configuración del motor (demos/_motor).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$horario = [
    1 => ['12:00', '23:30'],
    2 => ['12:00', '23:30'],
    3 => ['12:00', '23:30'],
    4 => ['12:00', '23:30'],
    5 => ['12:00', '00:30'],
    6 => ['12:00', '00:30'],
    7 => ['12:30', '23:00'],
];

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/sushi/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_sushi_plataforma',
    'prefijo_pedido' => 'NC',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Nori Club',
        'eslogan' => 'Sushi a domicilio en Las Condes y el sector oriente',
        'comuna' => 'Las Condes',
        'horario' => $horario,
    ],

    // Mayoristas = empresas y eventos (bandejas por volumen, con aprobación del dueño).
    'mayoristas' => true,
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Las Condes' => 1990,
        'Vitacura' => 2490,
        'Providencia' => 2490,
        'La Reina' => 2490,
        'Ñuñoa' => 2990,
        'Lo Barnechea' => 2990,
        'Peñalolén' => 2990,
    ],
    'despacho_gratis_desde' => 25000,
    'pedido_minimo' => 8000,
    'horas_entrega' => ['12:30', '23:30'],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    'menu_qr' => true,
    'modelos3d' => ['sushi-combo-duo', 'sushi', 'sushi-california', 'sushi-salmon-palta', 'sushi-dragon-roll', 'sushi-ebi-furai', 'sushi-roll-masago', 'sushi-avocado-veggie', 'sushi-nigiri-salmon', 'sushi-nigiri-mix'],

    'textos' => [
        'titulo_inicio' => 'Sushi a domicilio en Las Condes',
        'titulo_destacados' => 'Los favoritos del club',
        'titulo_catalogo' => 'La carta',
        'buscar_placeholder' => 'Busca california, gyozas, ramen…',
        'sello_destacado' => 'Favorito',
        'mayorista_titulo' => '¿Pides para tu empresa o un evento?',
        'mayorista_texto' => 'Bandejas de 100 a 200 piezas para reuniones, cócteles y matrimonios. Crea una cuenta para empresas y, cuando la aprobemos, verás los precios por volumen y las bandejas para eventos.',
        'mayorista_quien' => 'Empresas, oficinas y productoras de eventos que piden bandejas por volumen.',
        'retiro_texto' => 'Retira en nuestro local de Las Condes, sin costo.',
        'qr_titulo' => 'La carta en cada bolsa',
        'qr_texto' => 'Este QR va impreso en las bolsas y en los palillos: abre la carta, muestra los rolls en 3D y permite repetir el pedido en un toque.',
        'pedido_titulo' => '¡Pedido recibido! Ya estamos enrollando.',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Compra con precios normales, tiene direcciones guardadas y pedidos anteriores.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Mayorista', 'detalle' => 'Empresa aprobada: ve precios por volumen y bandejas para eventos.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueño', 'detalle' => 'Panel: pedidos, productos, stock, empresas por aprobar y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que el dueño puede elegir al crear o editar un producto (no hay subida de archivos).
    'fotos' => [
        '1774635800472-41eaa93c1453' => 'Bandeja de rolls para delivery',
        '1788016634163-77b7c547b9d4' => 'Tabla grande de rolls',
        '1553621042-f6e147245754' => 'Barco de sushi',
        '1780291117619-c58ee7aa740e' => 'Rolls California',
        '1579584425555-c3ce17fd4351' => 'Rolls de salmón y palta',
        '1768326119762-20c2a9f5c1f2' => 'Roll cubierto de atún',
        '1582450871972-ab5ca641643d' => 'Dragon roll',
        '1761314026089-f39a799bb48f' => 'Roll de camarón',
        '1787589481636-a69533c81edb' => 'Rolls con masago',
        '1761315413078-1fd73894fe0e' => 'Roll envuelto en palta',
        '1641932982747-bd7be260a040' => 'Nigiri de salmón',
        '1563612116625-3012372fccce' => 'Nigiris surtidos',
        '1652955844784-572d10123757' => 'Sashimi de salmón',
        '1588182728399-e8f2df121744' => 'Gyozas',
        '1638866281450-3933540af86a' => 'Ramen tonkotsu',
        '1773817728546-f1a6c392d0f0' => 'Ramen picante',
        '1651328236028-77b585d3f4e5' => 'Ebi tempura',
        '1768326119213-e0ad875083a3' => 'Poke de salmón',
        '1768326119231-bf064c1b8fdf' => 'Poke de atún',
        '1717603545758-88cc454db69b' => 'Matcha helado',
        '1724052526175-4a7332bd10e9' => 'Postre de matcha',
        '1763647756796-af9230245bf8' => 'Bandejas para eventos',
        '1790761403214-3b98524ac18a' => 'Bandeja de nigiri y sashimi',
        '1709984110217-57d7d18e5299' => 'Makis vistos desde arriba',
        '1609183590563-7710ba1f90a9' => 'Gyozas crudas',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Nori Club',
        'categoria' => 'Restaurante de sushi con delivery',
        'comuna' => 'Las Condes',
        'horario' => $horario,
        'nota' => 4.7,
        'resenas' => [
            ['Martina S.', 5, 'Pedí el Combo Familia por la página y llegó en 35 minutos. Guarda mi dirección y repetir el pedido es un clic.', 'hace 6 días'],
            ['Felipe O.', 5, 'Encargamos bandejas de 100 piezas para la oficina con factura. Todo puntual y muy bien presentado.', 'hace 3 semanas'],
            ['Daniela R.', 4, 'El rainbow de atún es buenísimo. En la página vi cuando el pedido salió en camino.', 'hace 1 mes'],
        ],
    ],
];
