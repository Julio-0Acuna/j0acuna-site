<?php
declare(strict_types=1);

// Merengue Club (Macul): negocio ficticio de la versión Plataforma web.
// Configuración del motor (demos/_motor), más 'dias_encargo' para la fecha de entrega (ver _encargo.php).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$horario = [
    1 => ['10:00', '20:00'],
    2 => ['10:00', '20:00'],
    3 => ['10:00', '20:00'],
    4 => ['10:00', '20:00'],
    5 => ['10:00', '20:30'],
    6 => ['10:00', '20:30'],
    7 => ['10:00', '14:00'],
];

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/pasteleria/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_pasteleria_plataforma',
    'prefijo_pedido' => 'MC',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Merengue Club',
        'eslogan' => 'Tortas y cajas dulces a domicilio',
        'comuna' => 'Macul',
        'horario' => $horario,
    ],

    'mayoristas' => true,
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Macul' => 2490,
        'Ñuñoa' => 2990,
        'Peñalolén' => 2990,
        'San Joaquín' => 2990,
        'La Florida' => 3490,
        'La Reina' => 3490,
        'Providencia' => 3990,
        'Santiago' => 3990,
    ],
    'despacho_gratis_desde' => 35000,
    'pedido_minimo' => 8000,
    'horas_entrega' => ['10:00', '19:30'],
    // Fecha de entrega en el checkout: cuántos días hacia adelante se pueden elegir (desde mañana).
    'dias_encargo' => 21,
    // vistas/pedido.php ya agrega al WhatsApp la nota separada en día, mensaje y resto (ver _encargo.php).
    'resumen_con_nota' => false,
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    'menu_qr' => true,
    'modelos3d' => ['torta'],

    'textos' => [
        'titulo_inicio' => 'Tortas a domicilio en Macul',
        'titulo_destacados' => 'Las más pedidas',
        'titulo_catalogo' => 'La tienda',
        'buscar_placeholder' => 'Busca frutilla, cupcakes, sin gluten…',
        'sello_destacado' => 'Favorita',
        'mayorista_titulo' => '¿Tienes una cafetería?',
        'mayorista_texto' => 'Vendemos tortas precortadas, brownies en bandeja y galletas a granel para cafeterías, casinos y banqueteras. Crea tu cuenta mayorista y, cuando la aprobemos, verás precios especiales.',
        'mayorista_quien' => 'Cafeterías, casinos y banqueteras.',
        'retiro_texto' => 'Retira en nuestro local de Macul, sin costo.',
        'qr_titulo' => 'La tienda, en cada caja',
        'qr_texto' => 'Este QR va en la tapa de cada caja y en el mesón: abre la tienda, muestra las tortas en 3D y permite repetir el pedido en un toque.',
        'pedido_titulo' => '¡Tu pedido entró al horno!',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Compra con precios normales, tiene direcciones y pedidos anteriores.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Mayorista', 'detalle' => 'Cafetería aprobada: ve precios mayoristas y productos para cafeterías.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueño', 'detalle' => 'Panel: pedidos, productos, stock, mayoristas y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que el dueño puede elegir al crear o editar un producto (no hay subida de archivos).
    'fotos' => [
        '1621303837174-89787a7d4729' => 'Torta rosada con cono de helado',
        '1761296152332-88ada22be48a' => 'Torta de colores pastel en trozos',
        '1562440499-64c9a111f713' => 'Torta con macarons',
        '1604413191066-4dd20bedf486' => 'Torta de menta con confeti',
        '1597520595747-23260411dc4e' => 'Torta de rosas con cupcakes',
        '1558301211-0d8c8ddee6ec' => 'Torta rosada con chorreado',
        '1650419424455-d0513aaf0dd6' => 'Torta desnuda con frutillas',
        '1779885580469-28385974ca80' => 'Trozo de red velvet',
        '1771016833998-358dc4cab681' => 'Caja de cupcakes',
        '1563729784474-d77dbb933a9e' => 'Cupcakes rosados',
        '1576618148400-f54bed99fcfd' => 'Cupcake de limón',
        '1760124056928-a9960b1b1dd2' => 'Macarons',
        '1657418830273-40c19cfff4d7' => 'Galletas grandes',
        '1636743715220-d8f8dd900b87' => 'Brownies',
        '1612845575953-f4b1e3d63160' => 'Galletas de almendra',
        '1765946026563-4f9db06eb328' => 'Mesa dulce',
        '1515823662972-da6a2e4d3002' => 'Café con croissants',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Merengue Club',
        'categoria' => 'Pastelería con despacho',
        'comuna' => 'Macul',
        'horario' => $horario,
        'nota' => 4.7,
        'resenas' => [
            ['Javiera P.', 5, 'Elegí la fecha y el mensaje en la web, pagué y la torta llegó el sábado justo a la hora. La de frutilla con cono es un éxito en los cumpleaños.', 'hace 5 días'],
            ['Tomás H.', 4, 'Muy buenos los cupcakes. Me gusta que guarda mis direcciones y puedo repetir el pedido.', 'hace 3 semanas'],
            ['Carolina B.', 5, 'Compramos brownies y tortas precortadas para nuestra cafetería con precio mayorista. Siempre puntuales.', 'hace 1 mes'],
        ],
    ],
];
