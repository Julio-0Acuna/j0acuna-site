<?php
declare(strict_types=1);

/*
 * Datos iniciales de Bodega Surtido (se cargan al crear la base y en cada reinicio por hora).
 * Abarrotes genéricos, sin marcas, con precio de tienda y precio por mayor; los productos por caja,
 * saco y fardo son solo para negocios aprobados. Precios en CLP. Fotos: IDs de Unsplash (ver CREDITOS.md).
 * Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 */
return [
    'categorias' => [
        ['slug' => 'canastas', 'nombre' => 'Canastas y kits'],
        ['slug' => 'despensa', 'nombre' => 'Despensa'],
        ['slug' => 'lacteos', 'nombre' => 'Lácteos y huevos'],
        ['slug' => 'bebidas', 'nombre' => 'Bebidas y jugos'],
        ['slug' => 'aseo', 'nombre' => 'Aseo y limpieza'],
        ['slug' => 'mascotas', 'nombre' => 'Mascotas'],
        ['slug' => 'verduras', 'nombre' => 'Frutas y verduras'],
        ['slug' => 'cajas', 'nombre' => 'Por caja, saco y fardo'],
    ],

    'productos' => [
        ['slug' => 'canasta-familiar', 'categoria' => 'canastas', 'nombre' => 'Canasta familiar de la semana', 'descripcion' => 'Arroz 2 kg, aceite 1 L, fideos ×4, azúcar 1 kg, harina 1 kg, leche ×6, huevos 12, lentejas 1 kg y té 100 bolsitas.', 'precio' => 32990, 'precio_mayorista' => 29990, 'stock' => 25, 'foto' => '1652262968340-b735524f9ae4', 'destacado' => true],
        ['slug' => 'kit-oficina', 'categoria' => 'canastas', 'nombre' => 'Kit cafetería para oficina', 'descripcion' => 'Café molido 500 g ×2, té 100 bolsitas ×2, azúcar 2 kg y galletas ×6. Para unas 20 personas al mes.', 'precio' => 34990, 'precio_mayorista' => 31490, 'stock' => 15, 'foto' => '1510776537653-6d0da167186c', 'destacado' => true],
        ['slug' => 'colaciones', 'categoria' => 'canastas', 'nombre' => 'Colaciones para curso (30 niños)', 'descripcion' => 'Jugo de 200 ml, galletas y fruta para 30 niños, en cajas por curso. Encárgalas con un día de anticipación.', 'precio' => 44990, 'precio_mayorista' => 39990, 'stock' => 10, 'foto' => '1621506289937-a8e4df240d0b'],

        ['slug' => 'arroz', 'categoria' => 'despensa', 'nombre' => 'Arroz grado 1, 1 kg', 'descripcion' => 'Grano largo, bolsa de 1 kg.', 'precio' => 1390, 'precio_mayorista' => 1190, 'stock' => 200, 'foto' => '1586201375761-83865001e31c', 'destacado' => true],
        ['slug' => 'aceite', 'categoria' => 'despensa', 'nombre' => 'Aceite vegetal 1 L', 'descripcion' => 'Botella de 1 litro, para freír y aliñar.', 'precio' => 2290, 'precio_mayorista' => 1990, 'stock' => 150, 'foto' => '1720468750623-39e9a09f5067', 'destacado' => true],
        ['slug' => 'azucar', 'categoria' => 'despensa', 'nombre' => 'Azúcar 1 kg', 'descripcion' => 'Granulada, bolsa de 1 kg.', 'precio' => 1190, 'precio_mayorista' => 1040, 'stock' => 160, 'foto' => '1770732851655-a789335a31c8'],
        ['slug' => 'harina', 'categoria' => 'despensa', 'nombre' => 'Harina sin polvos de hornear 1 kg', 'descripcion' => 'Para pan amasado, sopaipillas y queques.', 'precio' => 1090, 'precio_mayorista' => 950, 'stock' => 120, 'foto' => '1610725664285-7c57e6eeac3f'],
        ['slug' => 'fideos', 'categoria' => 'despensa', 'nombre' => 'Fideos spaghetti 400 g', 'descripcion' => 'Cocción en 9 minutos.', 'precio' => 890, 'precio_mayorista' => 760, 'stock' => 300, 'foto' => '1587287720536-ce7b84c23d35'],
        ['slug' => 'lentejas', 'categoria' => 'despensa', 'nombre' => 'Lentejas 1 kg', 'descripcion' => 'Lentejas pardinas, cosecha nueva.', 'precio' => 2190, 'precio_mayorista' => 1890, 'stock' => 80, 'foto' => '1763368392508-3d4bddfdd20a'],
        ['slug' => 'porotos', 'categoria' => 'despensa', 'nombre' => 'Porotos tórtola 1 kg', 'descripcion' => 'Para porotos con riendas o granados.', 'precio' => 2590, 'precio_mayorista' => 2250, 'stock' => 60, 'foto' => '1599249478506-ae175f801b1e'],
        ['slug' => 'te', 'categoria' => 'despensa', 'nombre' => 'Té negro, 100 bolsitas', 'descripcion' => 'Caja de 100 bolsitas.', 'precio' => 2490, 'precio_mayorista' => 2150, 'stock' => 90, 'foto' => '1585849835117-1446a1514ea6'],
        ['slug' => 'cafe', 'categoria' => 'despensa', 'nombre' => 'Café molido 500 g', 'descripcion' => 'Tueste medio, para cafetera de goteo o prensa.', 'precio' => 5990, 'precio_mayorista' => 5290, 'stock' => 40, 'foto' => '1510776537653-6d0da167186c'],

        ['slug' => 'leche', 'categoria' => 'lacteos', 'nombre' => 'Leche entera 1 L', 'descripcion' => 'Larga vida, en caja.', 'precio' => 1090, 'precio_mayorista' => 950, 'stock' => 240, 'foto' => '1550583724-b2692b85b150'],
        ['slug' => 'huevos', 'categoria' => 'lacteos', 'nombre' => 'Huevos de gallina, 12 un.', 'descripcion' => 'Huevos color, de granjas de la zona central.', 'precio' => 3290, 'precio_mayorista' => 2950, 'stock' => 70, 'foto' => '1502630039397-c104a63cc224'],
        ['slug' => 'huevos-30', 'categoria' => 'lacteos', 'nombre' => 'Huevos de gallina, bandeja de 30', 'descripcion' => 'Bandeja de cartón, huevos color.', 'precio' => 7490, 'precio_mayorista' => 6790, 'stock' => 30, 'foto' => '1760393339745-18360fc2b6e3'],
        ['slug' => 'queso', 'categoria' => 'lacteos', 'nombre' => 'Queso gauda laminado 250 g', 'descripcion' => 'Láminas para sándwich.', 'precio' => 2990, 'precio_mayorista' => 2590, 'stock' => 45, 'foto' => '1655662844300-e59c3d2e7587'],
        ['slug' => 'mantequilla', 'categoria' => 'lacteos', 'nombre' => 'Mantequilla 250 g', 'descripcion' => 'Con sal.', 'precio' => 2590, 'precio_mayorista' => 2250, 'stock' => 50, 'foto' => '1590912710024-6d51a6771abd'],
        ['slug' => 'yogur', 'categoria' => 'lacteos', 'nombre' => 'Yogur natural 1 kg', 'descripcion' => 'Sin azúcar.', 'precio' => 1990, 'precio_mayorista' => 1750, 'stock' => 40, 'foto' => '1571212515416-fef01fc43637'],

        ['slug' => 'cola', 'categoria' => 'bebidas', 'nombre' => 'Bebida cola 3 L', 'descripcion' => 'Botella desechable de 3 litros, para compartir.', 'precio' => 2590, 'precio_mayorista' => 2190, 'stock' => 120, 'foto' => '1568657624422-1b8713e79461'],
        ['slug' => 'agua', 'categoria' => 'bebidas', 'nombre' => 'Agua mineral sin gas 1,6 L', 'descripcion' => 'Botella de 1,6 litros.', 'precio' => 990, 'precio_mayorista' => 790, 'stock' => 200, 'foto' => '1616118132534-381148898bb4'],
        ['slug' => 'jugo', 'categoria' => 'bebidas', 'nombre' => 'Jugo de naranja 1,5 L', 'descripcion' => 'Néctar de naranja.', 'precio' => 1890, 'precio_mayorista' => 1590, 'stock' => 90, 'foto' => '1621506289937-a8e4df240d0b'],

        ['slug' => 'detergente', 'categoria' => 'aseo', 'nombre' => 'Detergente líquido 3 L', 'descripcion' => 'Para ropa blanca y de color, unos 40 lavados.', 'precio' => 6990, 'precio_mayorista' => 5990, 'stock' => 60, 'foto' => '1649005011845-ef225c89da86', 'destacado' => true],
        ['slug' => 'papel', 'categoria' => 'aseo', 'nombre' => 'Papel higiénico doble hoja, 12 rollos', 'descripcion' => 'Rollos de 30 metros.', 'precio' => 5990, 'precio_mayorista' => 5190, 'stock' => 80, 'foto' => '1631524254770-03abe3f42a0d'],
        ['slug' => 'lavaloza', 'categoria' => 'aseo', 'nombre' => 'Lavaloza concentrado 750 ml', 'descripcion' => 'Rinde el doble que el lavaloza común.', 'precio' => 1690, 'precio_mayorista' => 1450, 'stock' => 100, 'foto' => '1618038483079-bfe64dcb17f1'],
        ['slug' => 'limpiador', 'categoria' => 'aseo', 'nombre' => 'Limpiador multiuso con gatillo 900 ml', 'descripcion' => 'Para cocina, baño y superficies lavables.', 'precio' => 1990, 'precio_mayorista' => 1690, 'stock' => 70, 'foto' => '1563453392212-326f5e854473'],
        ['slug' => 'desengrasante', 'categoria' => 'aseo', 'nombre' => 'Desengrasante de cocina 1 L', 'descripcion' => 'Aroma limón.', 'precio' => 2490, 'precio_mayorista' => 2150, 'stock' => 3, 'foto' => '1583907659441-addbe699e921'],

        ['slug' => 'perro', 'categoria' => 'mascotas', 'nombre' => 'Alimento para perro adulto 3 kg', 'descripcion' => 'Croquetas para razas medianas.', 'precio' => 7990, 'precio_mayorista' => 6990, 'stock' => 40, 'foto' => '1589924691995-400dc9ecc119'],
        ['slug' => 'perro-15', 'categoria' => 'mascotas', 'nombre' => 'Alimento para perro adulto 15 kg', 'descripcion' => 'Saco de 15 kg, croquetas para razas medianas.', 'precio' => 29990, 'precio_mayorista' => 26490, 'stock' => 12, 'foto' => '1764249453874-46864677b10e'],
        ['slug' => 'gato', 'categoria' => 'mascotas', 'nombre' => 'Alimento para gato adulto 1,5 kg', 'descripcion' => 'Croquetas con pescado.', 'precio' => 5490, 'precio_mayorista' => 4790, 'stock' => 30, 'foto' => '1616668983570-a971956d8928'],

        ['slug' => 'papas', 'categoria' => 'verduras', 'nombre' => 'Papas, malla de 2 kg', 'descripcion' => 'Papa blanca para cocer o freír.', 'precio' => 1990, 'precio_mayorista' => 1690, 'stock' => 60, 'foto' => '1518977676601-b53f82aba655'],
        ['slug' => 'cebollas', 'categoria' => 'verduras', 'nombre' => 'Cebollas 1 kg', 'descripcion' => 'Cebolla de guarda.', 'precio' => 990, 'precio_mayorista' => 850, 'stock' => 60, 'foto' => '1580201092675-a0a6a6cafbb1'],
        ['slug' => 'tomates', 'categoria' => 'verduras', 'nombre' => 'Tomates 1 kg', 'descripcion' => 'Tomate larga vida.', 'precio' => 1490, 'precio_mayorista' => 1290, 'stock' => 50, 'foto' => '1582284540020-8acbe03f4924'],
        ['slug' => 'limones', 'categoria' => 'verduras', 'nombre' => 'Limones 1 kg', 'descripcion' => 'Jugosos, de temporada.', 'precio' => 1690, 'precio_mayorista' => 1450, 'stock' => 40, 'foto' => '1590502593747-42a996133562'],
        ['slug' => 'paltas', 'categoria' => 'verduras', 'nombre' => 'Paltas hass 1 kg', 'descripcion' => 'Llegan duras: maduran en dos o tres días.', 'precio' => 3990, 'precio_mayorista' => 3490, 'stock' => 4, 'foto' => '1573566291259-fd494a326b60'],

        ['slug' => 'caja-aceite', 'categoria' => 'cajas', 'nombre' => 'Aceite vegetal 1 L, caja de 12', 'descripcion' => 'Caja cerrada de 12 botellas: $1.916 cada una.', 'precio' => 26990, 'precio_mayorista' => 22990, 'stock' => 40, 'foto' => '1720468750623-39e9a09f5067', 'solo_mayorista' => true],
        ['slug' => 'saco-arroz', 'categoria' => 'cajas', 'nombre' => 'Arroz grado 1, saco de 25 kg', 'descripcion' => 'Para fraccionar o para casinos: $1.040 el kilo.', 'precio' => 29990, 'precio_mayorista' => 25990, 'stock' => 30, 'foto' => '1586201375761-83865001e31c', 'solo_mayorista' => true],
        ['slug' => 'pack-cola', 'categoria' => 'cajas', 'nombre' => 'Bebida cola 3 L, pack de 6', 'descripcion' => 'Pack retractilado: $2.082 cada botella.', 'precio' => 14990, 'precio_mayorista' => 12490, 'stock' => 50, 'foto' => '1568657624422-1b8713e79461', 'solo_mayorista' => true],
        ['slug' => 'fardo-papel', 'categoria' => 'cajas', 'nombre' => 'Papel higiénico, fardo de 48 rollos', 'descripcion' => 'Doble hoja, 4 paquetes de 12.', 'precio' => 21990, 'precio_mayorista' => 18990, 'stock' => 25, 'foto' => '1631524254770-03abe3f42a0d', 'solo_mayorista' => true],
        ['slug' => 'caja-leche', 'categoria' => 'cajas', 'nombre' => 'Leche entera 1 L, caja de 12', 'descripcion' => 'Caja cerrada: $916 cada litro.', 'precio' => 12490, 'precio_mayorista' => 10990, 'stock' => 60, 'foto' => '1576186726183-9bd8aa9fa6d9', 'solo_mayorista' => true],
        ['slug' => 'caja-fideos', 'categoria' => 'cajas', 'nombre' => 'Fideos spaghetti 400 g, caja de 20', 'descripcion' => 'Caja de 20 paquetes: $725 cada uno.', 'precio' => 16990, 'precio_mayorista' => 14490, 'stock' => 40, 'foto' => '1587287720536-ce7b84c23d35', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Paula Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Av. Ejemplo 1234', 'Renca', 'Pasaje interior, reja negra'], ['Trabajo', 'Calle Ficticia 88, of. 12', 'Quilicura', '']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Héctor Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Almacén Los Pinos (ficticio)',
            'direcciones' => [['Almacén', 'Pasaje Inventado 456', 'Cerro Navia', 'Cortina metálica verde, descargar por el costado']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueño de Bodega Surtido', 'rol' => 'dueno'],
        // Solicitud de negocio por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Andrea Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Colegio Demo Norte (ficticio)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Pedidos de ejemplo: hace_min = minutos antes del reinicio; fecha_entrega = días desde el reinicio.
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 43200, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Renca', 'direccion' => 'Av. Ejemplo 1234 (Pasaje interior, reja negra)', 'fecha_entrega' => -29, 'cuando' => '10:00', 'pago' => 'prueba', 'nota' => 'Tocar el timbre de la reja', 'items' => [['canasta-familiar', 1], ['detergente', 1], ['papel', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 10080, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Cerro Navia', 'direccion' => 'Pasaje Inventado 456 (Cortina metálica verde)', 'fecha_entrega' => -6, 'cuando' => '09:00', 'pago' => 'al_recibir', 'nota' => 'Factura a nombre del almacén', 'items' => [['caja-aceite', 2], ['saco-arroz', 1], ['pack-cola', 4], ['caja-leche', 2]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 4320, 'estado' => 'entregado', 'entrega' => 'retiro', 'fecha_entrega' => -2, 'cuando' => '12:00', 'pago' => 'al_recibir', 'nota' => '', 'items' => [['perro-15', 1], ['gato', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 120, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Cerro Navia', 'direccion' => 'Pasaje Inventado 456 (Cortina metálica verde)', 'fecha_entrega' => 1, 'cuando' => '09:00', 'pago' => 'al_recibir', 'nota' => 'Antes de las 11, abrimos a las 9', 'items' => [['fardo-papel', 2], ['caja-fideos', 1], ['huevos-30', 3]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 40, 'estado' => 'preparando', 'entrega' => 'despacho', 'comuna' => 'Quilicura', 'direccion' => 'Calle Ficticia 88, of. 12', 'fecha_entrega' => 2, 'cuando' => '15:00', 'pago' => 'prueba', 'nota' => 'Para la oficina', 'items' => [['kit-oficina', 1], ['agua', 6]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 10, 'estado' => 'nuevo', 'entrega' => 'retiro', 'fecha_entrega' => 1, 'cuando' => '', 'pago' => 'prueba', 'nota' => '', 'items' => [['aceite', 2], ['arroz', 4], ['leche', 6], ['huevos', 1]]],
    ],
];
