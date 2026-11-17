<?php
declare(strict_types=1);

/*
 * Datos iniciales de Kilo Justo (se cargan al crear la base y en cada reinicio por hora).
 * Productos genéricos, precios en CLP. Los cortes se venden en unidades de 500 g: el precio es
 * por 500 g (el kilo cuesta el doble). Fotos: IDs de Unsplash (ver demos/carniceria/CREDITOS.md).
 * Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 */
return [
    'categorias' => [
        ['slug' => 'vacuno', 'nombre' => 'Vacuno'],
        ['slug' => 'cerdo', 'nombre' => 'Cerdo'],
        ['slug' => 'pollo', 'nombre' => 'Pollo'],
        ['slug' => 'embutidos', 'nombre' => 'Embutidos'],
        ['slug' => 'packs', 'nombre' => 'Packs para asado'],
        ['slug' => 'carbon', 'nombre' => 'Carbón y acompañamientos'],
        ['slug' => 'restaurantes', 'nombre' => 'Para restaurantes'],
    ],

    'productos' => [
        ['slug' => 'lomo-vetado', 'categoria' => 'vacuno', 'nombre' => 'Lomo vetado (500 g)', 'descripcion' => 'Jugoso y con su veta de grasa. Bifes de 2,5 cm o la pieza entera.', 'precio' => 8990, 'precio_mayorista' => 7690, 'stock' => 60, 'foto' => '1690983322025-aab4f95a0269', 'modelo3d' => 'carne', 'destacado' => true],
        ['slug' => 'lomo-liso', 'categoria' => 'vacuno', 'nombre' => 'Lomo liso (500 g)', 'descripcion' => 'Magro, con su tapa de grasa. Bife a la plancha o entero al horno.', 'precio' => 8490, 'precio_mayorista' => 7290, 'stock' => 50, 'foto' => '1602470520998-f4a52199a3d6', 'modelo3d' => 'carne'],
        ['slug' => 'filete', 'categoria' => 'vacuno', 'nombre' => 'Filete (500 g)', 'descripcion' => 'El corte más tierno: medallones o entero para el horno.', 'precio' => 10990, 'precio_mayorista' => 9490, 'stock' => 30, 'foto' => '1602470521007-20293bd1fd31', 'modelo3d' => 'carne'],
        ['slug' => 'punta-ganso', 'categoria' => 'vacuno', 'nombre' => 'Punta de ganso (500 g)', 'descripcion' => 'Con su tapa de grasa, para la parrilla o el horno con papas.', 'precio' => 7490, 'precio_mayorista' => 6390, 'stock' => 40, 'foto' => '1690983330548-e34b52aa8cb2', 'modelo3d' => 'carne'],
        ['slug' => 'asado-tira', 'categoria' => 'vacuno', 'nombre' => 'Asado de tira (500 g)', 'descripcion' => 'Costilla en tiras de 3 cm. Fuego lento y paciencia.', 'precio' => 5990, 'precio_mayorista' => 4990, 'stock' => 80, 'foto' => '1611354574034-9655b5b72d59', 'destacado' => true],
        ['slug' => 'posta-rosada', 'categoria' => 'vacuno', 'nombre' => 'Posta rosada (500 g)', 'descripcion' => 'Magra y versátil: bistec, escalopa o carne mechada.', 'precio' => 5690, 'precio_mayorista' => 4790, 'stock' => 60, 'foto' => '1603048374877-b98f840ad441', 'modelo3d' => 'carne'],
        ['slug' => 'molida', 'categoria' => 'vacuno', 'nombre' => 'Molida especial 5 % (500 g)', 'descripcion' => 'Posta molida el mismo día. Para pino, hamburguesas y salsas.', 'precio' => 4990, 'precio_mayorista' => 4190, 'stock' => 100, 'foto' => '1609183556292-f208be1a9644', 'destacado' => true],
        ['slug' => 'cazuela', 'categoria' => 'vacuno', 'nombre' => 'Carne para cazuela (500 g)', 'descripcion' => 'Tapapecho y osobuco en trozos, con hueso para el caldo.', 'precio' => 3490, 'precio_mayorista' => 2890, 'stock' => 60, 'foto' => '1600180786608-28d06391d25c'],

        ['slug' => 'chuleta', 'categoria' => 'cerdo', 'nombre' => 'Chuleta de cerdo centro (500 g)', 'descripcion' => 'Corte grueso, ideal para la parrilla.', 'precio' => 3490, 'precio_mayorista' => 2890, 'stock' => 70, 'foto' => '1628268909376-e8c44bb3153f'],
        ['slug' => 'panceta', 'categoria' => 'cerdo', 'nombre' => 'Panceta (500 g)', 'descripcion' => 'Con cuero, en láminas o en trozo para el horno.', 'precio' => 3990, 'precio_mayorista' => 3290, 'stock' => 40, 'foto' => '1768528355780-a06f80911c1a'],
        ['slug' => 'costillar', 'categoria' => 'cerdo', 'nombre' => 'Costillar de cerdo (500 g)', 'descripcion' => 'Para la parrilla o al horno con salsa.', 'precio' => 3790, 'precio_mayorista' => 3090, 'stock' => 4, 'foto' => '1612156502174-bbbad9882af2'],

        ['slug' => 'pollo-entero', 'categoria' => 'pollo', 'nombre' => 'Pollo entero, aprox. 2,3 kg', 'descripcion' => 'Fresco, de productor de la zona central.', 'precio' => 7490, 'precio_mayorista' => 6290, 'stock' => 40, 'foto' => '1672787153720-e85fe802fd9f'],
        ['slug' => 'pechuga', 'categoria' => 'pollo', 'nombre' => 'Pechuga deshuesada (500 g)', 'descripcion' => 'Sin piel, entera o en filetes.', 'precio' => 3290, 'precio_mayorista' => 2690, 'stock' => 60, 'foto' => '1633096013004-e2cb4023b560'],
        ['slug' => 'trutro', 'categoria' => 'pollo', 'nombre' => 'Trutro entero (500 g)', 'descripcion' => 'Trutro largo con piel, para la parrilla o el horno.', 'precio' => 1990, 'precio_mayorista' => 1590, 'stock' => 80, 'foto' => '1759493321741-883fbf9f433c'],

        ['slug' => 'longaniza', 'categoria' => 'embutidos', 'nombre' => 'Longaniza parrillera, 6 unidades', 'descripcion' => 'Receta campesina, sin colorantes. Unos 600 g.', 'precio' => 5990, 'precio_mayorista' => 4890, 'stock' => 50, 'foto' => '1770222341238-15b5665fa1ce'],
        ['slug' => 'chorizo', 'categoria' => 'embutidos', 'nombre' => 'Chorizo parrillero, 6 unidades', 'descripcion' => 'Con ajo y ají de color, para el choripán. Unos 540 g.', 'precio' => 4990, 'precio_mayorista' => 3990, 'stock' => 50, 'foto' => '1598401863352-3de5501f4890'],

        ['slug' => 'pack-4', 'categoria' => 'packs', 'nombre' => 'Pack asado para 4', 'descripcion' => '1,6 kg: asado de tira, punta de ganso, 4 longanizas y carbón de espino 2,5 kg.', 'precio' => 25990, 'stock' => 20, 'foto' => '1529193591184-b1d58069ecdd', 'destacado' => true],
        ['slug' => 'pack-8', 'categoria' => 'packs', 'nombre' => 'Pack asado para 8', 'descripcion' => '3,2 kg: lomo vetado, asado de tira, punta de ganso, 8 chorizos y carbón 5 kg.', 'precio' => 47990, 'stock' => 15, 'foto' => '1586058584825-c1e87ed735b4'],
        ['slug' => 'pack-12', 'categoria' => 'packs', 'nombre' => 'Pack asado para 12', 'descripcion' => '4,8 kg: lomo vetado, asado de tira, punta de ganso, pollo trozado, 12 embutidos y carbón 10 kg.', 'precio' => 68990, 'stock' => 10, 'foto' => '1616252980327-ec70572e5df9'],

        ['slug' => 'carbon', 'categoria' => 'carbon', 'nombre' => 'Carbón de espino, saco 5 kg', 'descripcion' => 'Enciende rápido y da brasa pareja.', 'precio' => 6990, 'precio_mayorista' => 5490, 'stock' => 80, 'foto' => '1762202742459-4fcdc4beaab4'],
        ['slug' => 'pan', 'categoria' => 'carbon', 'nombre' => 'Pan amasado, bolsa de 6', 'descripcion' => 'Del horno de la esquina, horneado en la mañana.', 'precio' => 2490, 'stock' => 40, 'foto' => '1566698629409-787a68fc5724'],
        ['slug' => 'choclos', 'categoria' => 'carbon', 'nombre' => 'Choclos, malla de 4', 'descripcion' => 'Para la parrilla, con mantequilla y sal.', 'precio' => 2990, 'stock' => 30, 'foto' => '1601284702796-a27ffd3eff3c'],
        ['slug' => 'papas', 'categoria' => 'carbon', 'nombre' => 'Papas para asar, 2 kg', 'descripcion' => 'Lavadas, del tamaño justo para la brasa.', 'precio' => 2590, 'stock' => 30, 'foto' => '1623238913973-21e45cced554'],
        ['slug' => 'sal', 'categoria' => 'carbon', 'nombre' => 'Sal parrillera con hierbas, 500 g', 'descripcion' => 'Sal gruesa con orégano, ají y ajo deshidratado.', 'precio' => 1990, 'stock' => 40, 'foto' => '1700227280140-ee5a75cc096b'],

        ['slug' => 'caja-vetado', 'categoria' => 'restaurantes', 'nombre' => 'Lomo vetado al vacío, caja 5 kg', 'descripcion' => 'Piezas enteras envasadas al vacío, con fecha de faena. Para porcionar en tu cocina.', 'precio' => 84900, 'precio_mayorista' => 72900, 'stock' => 12, 'foto' => '1611038333075-2efd28705f42', 'solo_mayorista' => true],
        ['slug' => 'caja-molida', 'categoria' => 'restaurantes', 'nombre' => 'Molida especial 5 %, caja 10 kg', 'descripcion' => 'En bandejas de 1 kg selladas. Molida el día del despacho.', 'precio' => 89900, 'precio_mayorista' => 74900, 'stock' => 10, 'foto' => '1789925941783-c9145f82d8e7', 'solo_mayorista' => true],
        ['slug' => 'hamburguesas', 'categoria' => 'restaurantes', 'nombre' => 'Hamburguesas de 150 g, caja de 40', 'descripcion' => 'Vacuno 80/20, separadas con papel y congeladas.', 'precio' => 59900, 'precio_mayorista' => 48900, 'stock' => 15, 'foto' => '1612078894671-f11ba41d713e', 'solo_mayorista' => true],
        ['slug' => 'pechuga-caja', 'categoria' => 'restaurantes', 'nombre' => 'Pechuga deshuesada, caja 10 kg', 'descripcion' => 'Congelada en piezas sueltas, sin piel.', 'precio' => 59900, 'precio_mayorista' => 49900, 'stock' => 10, 'foto' => '1642497394469-188b0f4bcae6', 'solo_mayorista' => true],
        ['slug' => 'carbon-saco', 'categoria' => 'restaurantes', 'nombre' => 'Carbón de espino, saco 20 kg', 'descripcion' => 'Para parrillas de restaurante. Despacho a primera hora.', 'precio' => 21900, 'precio_mayorista' => 17900, 'stock' => 20, 'foto' => '1720676634387-60dd843e8758', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Camila Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Av. Ejemplo 1234, casa 5', 'Quilicura', 'Portón verde'], ['Trabajo', 'Calle Ficticia 77, oficina 3', 'Huechuraba', '']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Rodrigo Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Picada Los Cuatro Vientos (ficticia)',
            'direcciones' => [['Local', 'Pasaje Inventado 456', 'Renca', 'Entrada de proveedores por el costado']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueño de Kilo Justo', 'rol' => 'dueno'],
        // Solicitud mayorista por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Sofía Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Casino de empresa El Cruce (ficticio)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Pedidos de ejemplo: hace_min = minutos antes del reinicio. Cantidad en unidades de 500 g para los cortes.
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 4320, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Quilicura', 'direccion' => 'Av. Ejemplo 1234, casa 5 (Portón verde)', 'pago' => 'prueba', 'items' => [['asado-tira', 3], ['lomo-vetado', 2], ['carbon', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 11520, 'estado' => 'entregado', 'entrega' => 'retiro', 'pago' => 'al_recibir', 'items' => [['pack-4', 1], ['pan', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 2880, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Renca', 'direccion' => 'Pasaje Inventado 456', 'pago' => 'al_recibir', 'items' => [['caja-molida', 1], ['carbon-saco', 2]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 50, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Renca', 'direccion' => 'Pasaje Inventado 456', 'cuando' => '10:00', 'pago' => 'al_recibir', 'nota' => 'Recibe Rodrigo por la entrada de proveedores.', 'items' => [['caja-vetado', 1], ['hamburguesas', 1], ['chorizo', 4]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 28, 'estado' => 'preparando', 'entrega' => 'retiro', 'pago' => 'prueba', 'items' => [['pollo-entero', 1], ['molida', 2], ['chuleta', 2]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 7, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Quilicura', 'direccion' => 'Av. Ejemplo 1234, casa 5 (Portón verde)', 'pago' => 'prueba', 'items' => [['lomo-vetado', 3], ['longaniza', 1], ['choclos', 1]]],
    ],
];
