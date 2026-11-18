<?php
declare(strict_types=1);

// Bodega Surtido (Renca): negocio ficticio de la versión Plataforma web.
// Distribuidora y almacén online: precios por unidad para la casa y precios por mayor (con aprobación)
// para almacenes, colegios y oficinas. Reparto en camión desde el día siguiente (día validado en el servidor).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$horario = [
    1 => ['08:00', '18:00'],
    2 => ['08:00', '18:00'],
    3 => ['08:00', '18:00'],
    4 => ['08:00', '18:00'],
    5 => ['08:00', '18:00'],
    6 => ['09:00', '14:00'],
    7 => null,
];

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/almacen/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_almacen_plataforma',
    'prefijo_pedido' => 'BS',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Bodega Surtido',
        'eslogan' => 'Distribuidora y almacén online, con reparto en camión',
        'comuna' => 'Renca',
        'horario' => $horario,
    ],

    // Clientes "por mayor": almacenes de barrio, colegios, jardines y oficinas.
    'mayoristas' => true,
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Renca' => 2990,
        'Quilicura' => 3990,
        'Cerro Navia' => 3990,
        'Quinta Normal' => 3990,
        'Conchalí' => 3990,
        'Independencia' => 4490,
        'Pudahuel' => 4490,
        'Santiago' => 4990,
    ],
    'despacho_gratis_desde' => 60000,
    'pedido_minimo' => 15000,
    // Reparto o retiro desde mañana hasta 10 días, de lunes a sábado (sin feriados), con hora aproximada.
    'entrega' => [
        'modo' => 'fecha',
        'dias_min' => 1,
        'dias_max' => 10,
        'cerrado' => [7, '2026-12-08', '2026-12-25', '2027-01-01'],
        'horas' => ['09:00', '17:00'],
    ],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    // Almacén: sin menú QR ni modelos 3D.
    'menu_qr' => false,
    'modelos3d' => [],

    'textos' => [
        'titulo_inicio' => 'Distribuidora y almacén online en Renca',
        'titulo_destacados' => 'Lo que más se repone',
        'titulo_catalogo' => 'Catálogo de la bodega',
        'buscar_placeholder' => 'Busca arroz, aceite, detergente…',
        'sello_destacado' => 'Más vendido',
        'ver_catalogo' => 'Ver el catálogo',
        // Clientes por mayor: "Negocio" (almacenes, colegios, oficinas).
        'mayorista' => 'Negocio',
        'mayoristas' => 'Negocios',
        'mayorista_titulo' => '¿Compras para un almacén, colegio u oficina?',
        'mayorista_texto' => 'Con una cuenta de negocio ves precios por mayor y productos por caja, saco y fardo. La aprobamos en el día, normalmente en una hora.',
        'mayorista_quien' => 'Almacenes, colegios, jardines y oficinas que compran por caja.',
        'crear_cuenta_mayorista' => 'Crear cuenta de negocio',
        'precio_mayorista' => 'Precio por mayor',
        'cuenta_aprobada' => 'Aprobada: ves precios por mayor',
        'registro_pendiente' => 'Cuenta creada. Tu cuenta de negocio queda pendiente hasta que la aprobemos; mientras, ves los precios de tienda.',
        'mayorista_aprobado' => 'Negocio aprobado: ya ve sus precios por mayor.',
        'mayoristas_aprobados' => 'Negocios aprobados',
        'mayoristas_esperan' => '{n} negocio(s) esperan aprobación',
        // Retiro en bodega o reparto en camión, con día.
        'retiro_titulo' => 'Retiro en bodega',
        'retiro_texto' => 'Sin costo. Retiras con tu número de pedido en el mesón de la bodega.',
        'despacho_titulo' => 'Reparto en camión',
        'fecha_titulo' => 'Día de reparto o retiro',
        'fecha_ayuda' => 'Repartimos de lunes a sábado desde el día siguiente. Pedido mínimo $15.000; reparto gratis desde $60.000.',
        'hora_titulo' => 'Hora aproximada',
        'hora_cualquiera' => 'Cualquier hora del día',
        'hora_entrega_fecha' => 'Desde las {hora}',
        'pedido_titulo' => '¡Pedido en bodega!',
        'pedido_exito_prueba' => '¡Pago de prueba aprobado! Tu pedido ya está en preparación en la bodega.',
        'pedido_exito_al_recibir' => '¡Pedido recibido! Pagas cuando llegue el reparto.',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Compra para la casa: dos direcciones guardadas y pedidos anteriores para repetir.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Negocio', 'detalle' => 'Almacén de barrio aprobado: ve precios por mayor y productos por caja, saco y fardo.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueño', 'detalle' => 'Panel: pedidos por estado y día de reparto, productos, stock, negocios por aprobar y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que el dueño puede elegir al crear o editar un producto (no hay subida de archivos).
    'fotos' => [
        '1652262968340-b735524f9ae4' => 'Bolsas de papel con pan, tomates y plátanos',
        '1510776537653-6d0da167186c' => 'Café molido en un frasco',
        '1621506289937-a8e4df240d0b' => 'Jugo de naranja y fruta cortada',
        '1586201375761-83865001e31c' => 'Granos de arroz',
        '1720468750623-39e9a09f5067' => 'Botellas de aceite',
        '1770732851655-a789335a31c8' => 'Azúcar granulada',
        '1610725664285-7c57e6eeac3f' => 'Saco de harina',
        '1587287720536-ce7b84c23d35' => 'Fideos spaghetti',
        '1763368392508-3d4bddfdd20a' => 'Lentejas',
        '1599249478506-ae175f801b1e' => 'Porotos',
        '1585849835117-1446a1514ea6' => 'Bolsitas de té',
        '1550583724-b2692b85b150' => 'Vaso de leche',
        '1576186726183-9bd8aa9fa6d9' => 'Botella de leche',
        '1502630039397-c104a63cc224' => 'Huevos en su caja',
        '1760393339745-18360fc2b6e3' => 'Docena de huevos',
        '1655662844300-e59c3d2e7587' => 'Queso',
        '1590912710024-6d51a6771abd' => 'Mantequilla',
        '1571212515416-fef01fc43637' => 'Yogur natural',
        '1568657624422-1b8713e79461' => 'Bebidas en botella',
        '1616118132534-381148898bb4' => 'Botella de agua',
        '1649005011845-ef225c89da86' => 'Bidón de detergente',
        '1631524254770-03abe3f42a0d' => 'Rollos de papel higiénico',
        '1618038483079-bfe64dcb17f1' => 'Esponja con lavaloza',
        '1563453392212-326f5e854473' => 'Limpiador con gatillo',
        '1583907659441-addbe699e921' => 'Limpiador de limón',
        '1589924691995-400dc9ecc119' => 'Plato con alimento para perro',
        '1764249453874-46864677b10e' => 'Croquetas',
        '1616668983570-a971956d8928' => 'Gato comiendo',
        '1518977676601-b53f82aba655' => 'Papas',
        '1580201092675-a0a6a6cafbb1' => 'Cebollas',
        '1582284540020-8acbe03f4924' => 'Tomates',
        '1590502593747-42a996133562' => 'Limones',
        '1573566291259-fd494a326b60' => 'Paltas',
        '1587293852726-70cdb56c2866' => 'Cajas en estanterías de bodega',
        '1573209680076-bd7ec7007616' => 'Pallets de madera',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Bodega Surtido',
        'categoria' => 'Distribuidora de abarrotes',
        'comuna' => 'Renca',
        'horario' => $horario,
        'nota' => 4.7,
        'resenas' => [
            ['Almacén Doña Pía (almacén)', 5, 'Hago el pedido el domingo en la noche desde el celular y el lunes temprano llega el camión. Los precios por caja me dejan margen.', 'hace 1 semana'],
            ['Marcela V.', 5, 'Compro para la casa una vez al mes: la canasta familiar sale más barata que en el supermercado y la traen a la puerta.', 'hace 3 semanas'],
            ['Oficina contable (empresa)', 4, 'El kit de cafetería resolvió la compra de la oficina. Ojo con el pedido mínimo, pero el reparto es puntual.', 'hace 2 meses'],
        ],
    ],
];
