<?php
declare(strict_types=1);

/*
 * Datos iniciales de Palé Distribuidora (se cargan al crear la base y en cada reinicio por hora).
 * Productos genéricos, sin marcas, precios en CLP por caja o formato. Fotos: IDs de Unsplash
 * (ver demos/botilleria/CREDITOS.md). 'codigo' es el código de bodega (PD-0001…): se ve como «Cód.» en la tarjeta
 * y se busca con o sin guion. Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 */
return [
    'categorias' => [
        ['slug' => 'cervezas', 'nombre' => 'Cervezas'],
        ['slug' => 'vinos', 'nombre' => 'Vinos y espumantes'],
        ['slug' => 'destilados', 'nombre' => 'Piscos y destilados'],
        ['slug' => 'bebidas', 'nombre' => 'Bebidas, agua y jugos'],
        ['slug' => 'hielo', 'nombre' => 'Hielo, carbón y snacks'],
        ['slug' => 'eventos', 'nombre' => 'Para eventos'],
        ['slug' => 'almacen', 'nombre' => 'Formato almacén'],
    ],

    'productos' => [
        ['slug' => 'lager-24', 'codigo' => 'PD-0001', 'categoria' => 'cervezas', 'nombre' => 'Cerveza lager lata 470 ml · caja 24', 'descripcion' => 'Rubia, suave y bien carbonatada. 24 latas para reponer el refrigerador.', 'precio' => 23990, 'precio_mayorista' => 19990, 'stock' => 80, 'foto' => '1777991642395-84d980dcb733', 'destacado' => true],
        ['slug' => 'lager-6', 'codigo' => 'PD-0002', 'categoria' => 'cervezas', 'nombre' => 'Cerveza lager lata 350 ml · pack 6', 'descripcion' => 'El pack chico, para la casa.', 'precio' => 5490, 'precio_mayorista' => 4590, 'stock' => 120, 'foto' => '1696739696228-eee49592ff07'],
        ['slug' => 'ambar-24', 'codigo' => 'PD-0003', 'categoria' => 'cervezas', 'nombre' => 'Cerveza ámbar botella 330 ml · caja 24', 'descripcion' => 'Tostada, con notas a caramelo. Botella desechable.', 'precio' => 27990, 'precio_mayorista' => 23490, 'stock' => 40, 'foto' => '1518542698889-ca82262f08d5'],
        ['slug' => 'retornable-12', 'codigo' => 'PD-0004', 'categoria' => 'cervezas', 'nombre' => 'Cerveza lager retornable 1 L · jaba 12', 'descripcion' => 'Precio sin envase: entrega tus botellas vacías o paga el depósito al recibir.', 'precio' => 19990, 'precio_mayorista' => 16490, 'stock' => 30, 'foto' => '1738696782363-5944608b10f9'],
        ['slug' => 'sin-alcohol-6', 'codigo' => 'PD-0005', 'categoria' => 'cervezas', 'nombre' => 'Cerveza sin alcohol lata 350 ml · pack 6', 'descripcion' => '0,0 % de alcohol, mismo sabor lager.', 'precio' => 5990, 'precio_mayorista' => 4990, 'stock' => 50, 'foto' => '1593023669551-3c9cb301ce81'],

        ['slug' => 'cabernet-12', 'codigo' => 'PD-0006', 'categoria' => 'vinos', 'nombre' => 'Vino tinto Cabernet Sauvignon 750 ml · caja 12', 'descripcion' => 'Valle Central. El tinto de la casa para restaurantes y eventos.', 'precio' => 47990, 'precio_mayorista' => 39990, 'stock' => 25, 'foto' => '1765850257843-aa029ab7769c', 'destacado' => true, 'modelo3d' => 'vino-cabernet-sauvignon'],
        ['slug' => 'carmenere-6', 'codigo' => 'PD-0007', 'categoria' => 'vinos', 'nombre' => 'Vino tinto reserva Carménère 750 ml · caja 6', 'descripcion' => 'Valle de Colchagua, ocho meses en barrica.', 'precio' => 35990, 'precio_mayorista' => 29990, 'stock' => 30, 'foto' => '1642340828763-822a676c1da3', 'modelo3d' => 'vino-tinto'],
        ['slug' => 'sauvignon-6', 'codigo' => 'PD-0008', 'categoria' => 'vinos', 'nombre' => 'Vino blanco Sauvignon Blanc 750 ml · caja 6', 'descripcion' => 'Valle de Casablanca, fresco y cítrico.', 'precio' => 29990, 'precio_mayorista' => 24990, 'stock' => 30, 'foto' => '1598866971869-22782ffd918e', 'modelo3d' => 'vino-sauvignon-blanc'],
        ['slug' => 'espumante-6', 'codigo' => 'PD-0009', 'categoria' => 'vinos', 'nombre' => 'Espumante brut 750 ml · caja 6', 'descripcion' => 'Para el brindis de matrimonios y fin de año.', 'precio' => 41990, 'precio_mayorista' => 35490, 'stock' => 18, 'foto' => '1580657274234-7339717f4541', 'modelo3d' => 'espumante-brut'],

        ['slug' => 'pisco-35', 'codigo' => 'PD-0010', 'categoria' => 'destilados', 'nombre' => 'Pisco 35° 1 L', 'descripcion' => 'Transparente, del Valle del Elqui. Para sour o con bebida.', 'precio' => 8990, 'precio_mayorista' => 7490, 'stock' => 60, 'foto' => '1597188446702-9b16067ad718', 'destacado' => true],
        ['slug' => 'ron-1l', 'codigo' => 'PD-0011', 'categoria' => 'destilados', 'nombre' => 'Ron dorado 1 L', 'descripcion' => 'Añejado tres años, para cócteles.', 'precio' => 10990, 'precio_mayorista' => 8990, 'stock' => 30, 'foto' => '1641605529473-293c62b3a148'],
        ['slug' => 'gin-700', 'codigo' => 'PD-0012', 'categoria' => 'destilados', 'nombre' => 'Gin seco 700 ml', 'descripcion' => 'Enebro, cilantro y cáscara de limón.', 'precio' => 14990, 'precio_mayorista' => 12490, 'stock' => 24, 'foto' => '1617524455170-ca63c7f0d472'],
        ['slug' => 'whisky-750', 'codigo' => 'PD-0013', 'categoria' => 'destilados', 'nombre' => 'Whisky escocés 750 ml', 'descripcion' => 'Mezcla de maltas y granos, suave.', 'precio' => 16990, 'precio_mayorista' => 14290, 'stock' => 18, 'foto' => '1470337458703-46ad1756a187'],

        ['slug' => 'bebida-retornable', 'codigo' => 'PD-0014', 'categoria' => 'bebidas', 'nombre' => 'Bebida retornable 2 L surtida · jaba 8', 'descripcion' => 'Cola, naranja y lima-limón. Precio sin envase.', 'precio' => 11990, 'precio_mayorista' => 9990, 'stock' => 40, 'foto' => '1571250315503-ecb14720ad5d'],
        ['slug' => 'agua-6', 'codigo' => 'PD-0015', 'categoria' => 'bebidas', 'nombre' => 'Agua mineral sin gas 1,6 L · pack 6', 'descripcion' => 'De vertiente, en botella reciclable.', 'precio' => 5990, 'precio_mayorista' => 4790, 'stock' => 90, 'foto' => '1616118133103-baf54a4790c6'],
        ['slug' => 'bidon-20', 'codigo' => 'PD-0016', 'categoria' => 'bebidas', 'nombre' => 'Agua purificada bidón 20 L', 'descripcion' => 'Para dispensador. Recarga con devolución del bidón vacío.', 'precio' => 3990, 'precio_mayorista' => 3290, 'stock' => 60, 'foto' => '1701478008235-da625f3d72d5'],
        ['slug' => 'jugo-6', 'codigo' => 'PD-0017', 'categoria' => 'bebidas', 'nombre' => 'Jugo néctar 1,5 L · pack 6', 'descripcion' => 'Durazno, naranja o piña: indica cuál en la nota.', 'precio' => 8990, 'precio_mayorista' => 7490, 'stock' => 40, 'foto' => '1613637069737-2cce919a4ab7'],

        ['slug' => 'hielo-10', 'codigo' => 'PD-0018', 'categoria' => 'hielo', 'nombre' => 'Hielo en cubos · saco 10 kg', 'descripcion' => 'Agua purificada, cubo macizo que dura más.', 'precio' => 4990, 'precio_mayorista' => 3990, 'stock' => 100, 'foto' => '1758001969632-c67b15c89f1f', 'destacado' => true],
        ['slug' => 'carbon-10', 'codigo' => 'PD-0019', 'categoria' => 'hielo', 'nombre' => 'Carbón vegetal · saco 10 kg', 'descripcion' => 'Espino y quillay, prende rápido y dura.', 'precio' => 9990, 'precio_mayorista' => 8290, 'stock' => 45, 'foto' => '1680027412524-dcabcd83dc59'],
        ['slug' => 'papas-1kg', 'codigo' => 'PD-0020', 'categoria' => 'hielo', 'nombre' => 'Papas fritas formato bar 1 kg', 'descripcion' => 'Corte liso, bolsa de servicio.', 'precio' => 6990, 'precio_mayorista' => 5790, 'stock' => 30, 'foto' => '1599490659213-e2b9527bd087'],
        ['slug' => 'mani-1kg', 'codigo' => 'PD-0021', 'categoria' => 'hielo', 'nombre' => 'Maní salado 1 kg', 'descripcion' => 'Tostado y salado, para picar.', 'precio' => 5490, 'precio_mayorista' => 4490, 'stock' => 30, 'foto' => '1697468921924-1d113bf7ba06'],
        ['slug' => 'limones-3kg', 'codigo' => 'PD-0022', 'categoria' => 'hielo', 'nombre' => 'Limones malla 3 kg', 'descripcion' => 'Para sour, limonada y ensaladas.', 'precio' => 4490, 'precio_mayorista' => 3790, 'stock' => 4, 'foto' => '1598960809231-5504c3b90a6f'],

        ['slug' => 'pack-evento', 'codigo' => 'PD-0023', 'categoria' => 'eventos', 'nombre' => 'Pack evento 50 personas', 'descripcion' => '3 cajas de cerveza lager, 1 caja de vino tinto, 4 sacos de hielo, 4 jabas de bebida y 6 packs de agua. Ajustamos el detalle por WhatsApp.', 'precio' => 199990, 'precio_mayorista' => 179990, 'stock' => 10, 'foto' => '1768508948990-f5866f800fad'],
        ['slug' => 'barril-30', 'codigo' => 'PD-0024', 'categoria' => 'eventos', 'nombre' => 'Barril de cerveza lager 30 L', 'descripcion' => 'Con dispensador y gas en préstamo (garantía al recibir). Se retira al día siguiente.', 'precio' => 89990, 'precio_mayorista' => 74990, 'stock' => 6, 'foto' => '1758588370915-8aadfb709d4f'],
        ['slug' => 'copas-25', 'codigo' => 'PD-0025', 'categoria' => 'eventos', 'nombre' => 'Arriendo de copas de vidrio · caja 25', 'descripcion' => 'Copa de vino o flauta. Se devuelven sin lavar al día siguiente.', 'precio' => 4990, 'precio_mayorista' => 3990, 'stock' => 40, 'foto' => '1511795267602-0424436b99c5', 'modelo3d' => 'copas'],

        ['slug' => 'lager-120', 'codigo' => 'PD-0026', 'categoria' => 'almacen', 'nombre' => 'Cerveza lager lata 470 ml · 5 cajas (120 latas)', 'descripcion' => 'Formato almacén, con precio escalonado por volumen.', 'precio' => 114990, 'precio_mayorista' => 94990, 'stock' => 20, 'foto' => '1601774557352-8f2df2c6290c', 'solo_mayorista' => true],
        ['slug' => 'pisco-12', 'codigo' => 'PD-0027', 'categoria' => 'almacen', 'nombre' => 'Pisco 35° 1 L · caja 12', 'descripcion' => 'Formato almacén. Factura con IVA y patente al día.', 'precio' => 99990, 'precio_mayorista' => 82990, 'stock' => 15, 'foto' => '1560188267-f511657b1a18', 'solo_mayorista' => true],
        ['slug' => 'hielo-fardo', 'codigo' => 'PD-0028', 'categoria' => 'almacen', 'nombre' => 'Hielo en cubos 2 kg · fardo de 10 bolsas', 'descripcion' => 'Para vender en tu congelador. Bolsa con tu precio sugerido.', 'precio' => 12990, 'precio_mayorista' => 9990, 'stock' => 30, 'foto' => '1505009258427-29298f4dc5f6', 'solo_mayorista' => true],
        ['slug' => 'bebidas-24', 'codigo' => 'PD-0029', 'categoria' => 'almacen', 'nombre' => 'Bebidas surtidas 1,5 L · pack 24', 'descripcion' => 'Formato almacén, sabores a elección.', 'precio' => 33990, 'precio_mayorista' => 27990, 'stock' => 20, 'foto' => '1594001227946-595ad950f18d', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Camila Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Av. Ejemplo 1234', 'Pudahuel', 'Casa esquina, reja verde'], ['Oficina', 'Calle Ficticia 77, of. 302', 'Estación Central', '']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Rodrigo Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Almacén Don Rodrigo (ficticio)',
            'direcciones' => [['Almacén', 'Pasaje Inventado 456', 'Lo Prado', 'Entrar por el portón lateral']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueño de Palé Distribuidora', 'rol' => 'dueno'],
        // Solicitud de negocio por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Sofía Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Productora de eventos Cuatro Vientos (ficticio)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Pedidos de ejemplo: hace_min = minutos antes del reinicio; fecha_entrega = días desde el reinicio.
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 4320, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Pudahuel', 'direccion' => 'Av. Ejemplo 1234 (Casa esquina, reja verde)', 'pago' => 'prueba', 'fecha_entrega' => -2, 'items' => [['lager-24', 1], ['hielo-10', 2], ['carbon-10', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 11520, 'estado' => 'entregado', 'entrega' => 'retiro', 'pago' => 'al_recibir', 'fecha_entrega' => -7, 'items' => [['cabernet-12', 1], ['bebida-retornable', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 2880, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Lo Prado', 'direccion' => 'Pasaje Inventado 456', 'pago' => 'al_recibir', 'fecha_entrega' => -1, 'items' => [['lager-120', 1], ['hielo-fardo', 2], ['pisco-12', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 60, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Lo Prado', 'direccion' => 'Pasaje Inventado 456', 'pago' => 'al_recibir', 'fecha_entrega' => 1, 'cuando' => '09:00', 'nota' => 'Recibe Rodrigo. Entrar por el portón lateral.', 'items' => [['lager-120', 1], ['bebidas-24', 1], ['agua-6', 4]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 35, 'estado' => 'preparando', 'entrega' => 'retiro', 'pago' => 'prueba', 'fecha_entrega' => 1, 'items' => [['espumante-6', 1], ['hielo-10', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 10, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Pudahuel', 'direccion' => 'Av. Ejemplo 1234 (Casa esquina, reja verde)', 'pago' => 'prueba', 'fecha_entrega' => 2, 'cuando' => '17:00', 'items' => [['pisco-35', 2], ['bebida-retornable', 1], ['limones-3kg', 1], ['hielo-10', 1]]],
    ],
];
