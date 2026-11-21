<?php
declare(strict_types=1);

// Kilo Justo (Quilicura): negocio ficticio de la versión Plataforma web.
// Configuración del motor (demos/_motor). Venta por peso: los productos cuyo nombre termina en
// "(500 g)" o "(1 kg)" se venden en esas unidades y la tienda muestra un selector de gramos
// y el precio por kilo (ver vistas/_kilo.php).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$horario = [
    1 => ['09:00', '20:00'],
    2 => ['09:00', '20:00'],
    3 => ['09:00', '20:00'],
    4 => ['09:00', '20:00'],
    5 => ['09:00', '20:30'],
    6 => ['09:00', '20:30'],
    7 => ['10:00', '15:00'],
];

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/carniceria/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_carniceria_plataforma',
    'prefijo_pedido' => 'KJ',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Kilo Justo',
        'eslogan' => 'Carne por kilo, en tu puerta hoy',
        'comuna' => 'Quilicura',
        'horario' => $horario,
    ],

    'mayoristas' => true,
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Quilicura' => 1990,
        'Huechuraba' => 2490,
        'Conchalí' => 2490,
        'Renca' => 2490,
        'Independencia' => 2990,
        'Recoleta' => 2990,
        'Pudahuel' => 3490,
        'Colina' => 3490,
        'Santiago' => 3990,
    ],
    'despacho_gratis_desde' => 35000,
    'pedido_minimo' => 10000,
    'horas_entrega' => ['10:00', '20:00'],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    'menu_qr' => true,
    'modelos3d' => ['carne', 'carne-lomo-liso', 'carne-filete', 'carne-punta-ganso'],

    'textos' => [
        'titulo_inicio' => 'Carnicería online en Quilicura',
        'titulo_destacados' => 'Ofertas de la semana',
        'titulo_catalogo' => 'Catálogo',
        'buscar_placeholder' => 'Busca lomo, pollo, carbón…',
        'sello_destacado' => 'Oferta',
        'mayorista_titulo' => '¿Tienes un restaurante, una picada o un casino?',
        'mayorista_texto' => 'Vendemos cajas al vacío, molida por caja y hamburguesas para negocios, con precio mayorista en todo el catálogo. Crea tu cuenta mayorista y, cuando la aprobemos, verás tus precios.',
        'mayorista_quien' => 'Restaurantes, picadas, casinos y banquetería.',
        'retiro_texto' => 'Retira en nuestra bodega de Quilicura, sin costo.',
        'qr_titulo' => 'El catálogo en tu celular',
        'qr_texto' => 'Este QR va en la vitrina, en las bolsas y en cada caja que despachamos: abre el catálogo, muestra los cortes en 3D y deja repetir el pedido en un toque.',
        'qr_pie' => 'Código QR del catálogo',
        'pedido_titulo' => '¡Pedido recibido! Ya lo estamos pesando.',
    ],

    // Para qué sirve cada corte (se muestra en la tarjeta). Clave: nombre sin la unidad de peso.
    'usos' => [
        'Lomo vetado' => ['Parrilla', 'Sartén'],
        'Lomo liso' => ['Parrilla', 'Sartén', 'Horno'],
        'Filete' => ['Sartén', 'Horno'],
        'Punta de ganso' => ['Parrilla', 'Horno'],
        'Asado de tira' => ['Parrilla', 'Horno'],
        'Posta rosada' => ['Sartén', 'Olla'],
        'Molida especial 5 %' => ['Sartén', 'Olla'],
        'Carne para cazuela' => ['Olla'],
        'Chuleta de cerdo centro' => ['Parrilla', 'Sartén'],
        'Panceta' => ['Horno', 'Olla'],
        'Costillar de cerdo' => ['Parrilla', 'Horno'],
        'Pechuga deshuesada' => ['Sartén', 'Horno'],
        'Trutro entero' => ['Parrilla', 'Horno'],
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Compra por kilo con precios normales, tiene direcciones y pedidos anteriores.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Mayorista', 'detalle' => 'Restaurante aprobado: ve precios mayoristas y las cajas para negocios.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueño', 'detalle' => 'Panel: pedidos, productos, stock, mayoristas y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que el dueño puede elegir al crear o editar un producto (no hay subida de archivos).
    'fotos' => [
        '1690983322025-aab4f95a0269' => 'Lomos vetados',
        '1602470520998-f4a52199a3d6' => 'Lomo liso en láminas',
        '1602470521007-20293bd1fd31' => 'Filete',
        '1690983330548-e34b52aa8cb2' => 'Bifes con cuchillo',
        '1611354574034-9655b5b72d59' => 'Asado de tira',
        '1603048374877-b98f840ad441' => 'Cortes sobre piedra',
        '1609183556292-f208be1a9644' => 'Carne molida',
        '1600180786608-28d06391d25c' => 'Carne en cubos',
        '1628268909376-e8c44bb3153f' => 'Chuleta de cerdo',
        '1768528355780-a06f80911c1a' => 'Panceta',
        '1612156502174-bbbad9882af2' => 'Costillar de cerdo',
        '1672787153720-e85fe802fd9f' => 'Pollo entero',
        '1633096013004-e2cb4023b560' => 'Pechugas',
        '1759493321741-883fbf9f433c' => 'Trutros',
        '1770222341238-15b5665fa1ce' => 'Longanizas',
        '1598401863352-3de5501f4890' => 'Chorizos a la parrilla',
        '1529193591184-b1d58069ecdd' => 'Tabla parrillera',
        '1586058584825-c1e87ed735b4' => 'Corte a la parrilla',
        '1616252980327-ec70572e5df9' => 'Parrilla con bifes',
        '1762202742459-4fcdc4beaab4' => 'Brasas',
        '1566698629409-787a68fc5724' => 'Pan',
        '1601284702796-a27ffd3eff3c' => 'Choclos asados',
        '1623238913973-21e45cced554' => 'Papas',
        '1700227280140-ee5a75cc096b' => 'Especias',
        '1611038333075-2efd28705f42' => 'Carne al vacío',
        '1789925941783-c9145f82d8e7' => 'Bandejas de molida',
        '1612078894671-f11ba41d713e' => 'Hamburguesas',
        '1642497394469-188b0f4bcae6' => 'Pechuga entera',
        '1720676634387-60dd843e8758' => 'Carbón',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Kilo Justo',
        'categoria' => 'Carnicería y distribuidora de carnes',
        'comuna' => 'Quilicura',
        'horario' => $horario,
        'nota' => 4.7,
        'resenas' => [
            ['Paola R.', 5, 'Elegí 1,5 kg de asado de tira y 1 kg de lomo vetado en la web y llegó esa misma tarde, bien envasado y con el peso justo.', 'hace 3 días'],
            ['Marco T.', 5, 'Tenemos una picada y compramos la molida por caja. El precio mayorista y el despacho a primera hora nos cambiaron la semana.', 'hace 2 semanas'],
            ['Andrea L.', 4, 'Muy práctico repetir el pedido de cada semana desde mi cuenta. A veces el pollo se agota temprano.', 'hace 1 mes'],
        ],
    ],
];
