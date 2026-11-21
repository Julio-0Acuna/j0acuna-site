<?php
declare(strict_types=1);

// Cohete Pizza (Maipú): negocio ficticio de la versión Plataforma web.
// Configuración del motor (demos/_motor). Para otro rubro: copiar esta carpeta y cambiar
// config.php, seed.php, vistas/ y estilo.css.
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$horario = [
    1 => ['12:00', '23:30'],
    2 => ['12:00', '23:30'],
    3 => ['12:00', '23:30'],
    4 => ['12:00', '23:30'],
    5 => ['12:00', '01:00'],
    6 => ['12:00', '01:00'],
    7 => ['12:30', '23:00'],
];

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/pizzeria/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_pizzeria_plataforma',
    'prefijo_pedido' => 'CP',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Cohete Pizza',
        'eslogan' => 'Pizza caliente en tu puerta en 30 minutos',
        'comuna' => 'Maipú',
        'horario' => $horario,
    ],

    'mayoristas' => true,
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Maipú' => 1490,
        'Cerrillos' => 1990,
        'Estación Central' => 2490,
        'Pudahuel' => 2490,
        'Lo Prado' => 2490,
        'Quinta Normal' => 2990,
        'Padre Hurtado' => 2990,
        'Santiago' => 3490,
    ],
    'despacho_gratis_desde' => 20000,
    'pedido_minimo' => 5000,
    'horas_entrega' => ['12:30', '23:30'],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    'menu_qr' => true,
    'modelos3d' => ['pizza-pepperoni', 'pizza-suprema', 'pizza-pollo-bbq', 'pizza-doble-queso', 'pizza-veggie', 'pizza-chilena', 'calzone', 'helado'],

    'textos' => [
        'titulo_inicio' => 'Pizza a domicilio en Maipú',
        'titulo_destacados' => 'Los más pedidos',
        'titulo_catalogo' => 'Menú',
        'buscar_placeholder' => 'Busca pepperoni, veggie, helado…',
        'sello_destacado' => 'Top',
        'mayorista_titulo' => '¿Tienes un restaurante o un café?',
        'mayorista_texto' => 'Vendemos masa en bollos, salsa de la casa y pizzas precocidas al por mayor. Crea tu cuenta mayorista y, cuando la aprobemos, verás precios especiales.',
        'mayorista_quien' => 'Restaurantes, cafés y casinos que compran al por mayor.',
        'retiro_texto' => 'Retira en nuestro local de Maipú, sin costo.',
        'qr_titulo' => 'El menú, en cada mesa y cada caja',
        'qr_texto' => 'Este QR va impreso en las cajas y en el mesón: abre el menú, muestra las pizzas en 3D y permite pedir de nuevo en un toque.',
        'pedido_titulo' => '¡Pedido despegando!',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Compra con precios normales, tiene direcciones y pedidos anteriores.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Mayorista', 'detalle' => 'Café aprobado: ve precios mayoristas y productos para negocios.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueño', 'detalle' => 'Panel: pedidos, productos, stock, mayoristas y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que el dueño puede elegir al crear o editar un producto (no hay subida de archivos).
    'fotos' => [
        '1542282811-943ef1a977c3' => 'Pizza pepperoni',
        '1615719413546-198b25453f85' => 'Pizza con pimentón',
        '1588315029754-2dd089d39a1a' => 'Pizza sobre papel',
        '1600028068383-ea11a7a101f3' => 'Porción con queso',
        '1565299624946-b28f40a0ae38' => 'Pizza con verduras',
        '1506354666786-959d6d497f1a' => 'Pizza de carne',
        '1566843972142-a7fcb70de55a' => 'Pizza en tabla',
        '1604382354936-07c5d9983bd3' => 'Pizza fondo naranjo',
        '1536964549204-cce9eab227bd' => 'Calzone',
        '1601924582970-9238bcb495d9' => 'Pizza en caja',
        '1556679343-c7306c1976bc' => 'Té helado',
        '1497534446932-c925b458314e' => 'Jugo rojo',
        '1586040140378-b5634cb4c8fc' => 'Copa de helado',
        '1604745186348-f2ced902a93c' => 'Bollos de masa',
        '1472476443507-c7a5948772fc' => 'Salsa de tomate',
        '1596924699736-be026ddb5b0c' => 'Mozzarella',
        '1576458088443-04a19bb13da6' => 'Porciones de pizza',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Cohete Pizza',
        'categoria' => 'Pizzería con delivery',
        'comuna' => 'Maipú',
        'horario' => $horario,
        'nota' => 4.6,
        'resenas' => [
            ['Ignacio T.', 5, 'Llegó en 25 minutos y bien caliente. La app para pedir es muy fácil y guarda mi dirección.', 'hace 4 días'],
            ['Paula C.', 4, 'La Suprema es enorme. En la página del pedido vi cuándo salió en camino.', 'hace 2 semanas'],
            ['Héctor R.', 5, 'Compramos masa al por mayor para nuestro café: buen precio y siempre a tiempo.', 'hace 1 mes'],
        ],
    ],
];
