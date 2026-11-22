<?php
declare(strict_types=1);

/*
 * Datos iniciales de Wokazo (se cargan al crear la base y en cada reinicio por hora).
 * Productos genéricos, precios en CLP. Fotos: IDs de Unsplash (ver demos/china/CREDITOS.md).
 * Cuentas de prueba: contraseñas públicas, solo valen en esta demo. Los "mayoristas" son empresas
 * y organizadores de eventos: ven bandejas de cóctel, colaciones y precio por volumen.
 */
return [
    'categorias' => [
        ['slug' => 'menus', 'nombre' => 'Menús para compartir'],
        ['slug' => 'arroz', 'nombre' => 'Arroz y tallarines'],
        ['slug' => 'pollo', 'nombre' => 'Pollo'],
        ['slug' => 'carne', 'nombre' => 'Carne y cerdo'],
        ['slug' => 'picar', 'nombre' => 'Para picar'],
        ['slug' => 'bebidas', 'nombre' => 'Bebidas y postres'],
        ['slug' => 'eventos', 'nombre' => 'Empresas y eventos'],
    ],

    'productos' => [
        ['slug' => 'menu-2', 'categoria' => 'menus', 'nombre' => 'Menú para 2', 'descripcion' => 'Chaulafán de pollo, pollo mongoliano, cerdo agridulce, 10 wantanes fritos y 2 arrollados.', 'precio' => 18990, 'precio_mayorista' => 16990, 'stock' => 40, 'foto' => '1634864572872-a01c21e388d4', 'destacado' => true],
        ['slug' => 'menu-4', 'categoria' => 'menus', 'nombre' => 'Menú para 4', 'descripcion' => 'Chaulafán especial, tallarines salteados, pollo agridulce, carne mongoliana, 20 wantanes, 4 arrollados y bebida de 1,5 litros.', 'precio' => 33990, 'precio_mayorista' => 29990, 'stock' => 30, 'foto' => '1665110768132-4a372e2ebd16', 'destacado' => true],
        ['slug' => 'menu-6', 'categoria' => 'menus', 'nombre' => 'Menú para 6', 'descripcion' => 'Dos chaulafán especial, tallarines, pollo mongoliano, carne mongoliana, cerdo agridulce, chapsui, 30 wantanes, 6 arrollados y 2 bebidas.', 'precio' => 48990, 'precio_mayorista' => 42990, 'stock' => 20, 'foto' => '1675995642883-3e43c928e200', 'destacado' => true],

        ['slug' => 'chaulafan-pollo', 'categoria' => 'arroz', 'nombre' => 'Chaulafán de pollo', 'descripcion' => 'Arroz al wok con huevo, cebollín y pollo. Porción grande.', 'precio' => 5990, 'precio_mayorista' => 5290, 'stock' => 60, 'foto' => '1788603907442-253dd51b1aec'],
        ['slug' => 'chaulafan-especial', 'categoria' => 'arroz', 'nombre' => 'Chaulafán especial', 'descripcion' => 'Pollo, cerdo asado y camarón, salteado a fuego alto.', 'precio' => 7490, 'precio_mayorista' => 6490, 'stock' => 50, 'foto' => '1551326844-4df70f78d0e9'],
        ['slug' => 'chaulafan-camaron', 'categoria' => 'arroz', 'nombre' => 'Chaulafán de camarón', 'descripcion' => 'Camarones salteados, arvejas, zanahoria y huevo.', 'precio' => 7990, 'stock' => 30, 'foto' => '1630914441929-0d8ea69f95e6'],
        ['slug' => 'tallarines-pollo', 'categoria' => 'arroz', 'nombre' => 'Tallarines salteados con pollo', 'descripcion' => 'Fideo de huevo, pollo, verduras y salsa de soya.', 'precio' => 6490, 'precio_mayorista' => 5690, 'stock' => 50, 'foto' => '1772729219168-af0f0e57bb9c'],
        ['slug' => 'tallarines-especiales', 'categoria' => 'arroz', 'nombre' => 'Tallarines especiales', 'descripcion' => 'Pollo, carne, cerdo y camarón, con brotes de soya y sésamo.', 'precio' => 7990, 'stock' => 40, 'foto' => '1791408842505-0e14eb77f5d8'],
        ['slug' => 'tallarines-camaron', 'categoria' => 'arroz', 'nombre' => 'Tallarines con camarón', 'descripcion' => 'Camarón salteado con ajo, pimentón y cebollín.', 'precio' => 7990, 'stock' => 25, 'foto' => '1701856626197-69908fa4e39a'],
        ['slug' => 'chapsui', 'categoria' => 'arroz', 'nombre' => 'Chapsui de carne', 'descripcion' => 'Verduras salteadas al wok con lomo, apio y pimentón.', 'precio' => 6990, 'stock' => 35, 'foto' => '1702705481217-846e30915076'],

        ['slug' => 'pollo-mongoliano', 'categoria' => 'pollo', 'nombre' => 'Pollo mongoliano', 'descripcion' => 'Salteado con cebolla, cebollín y salsa de soya dulce.', 'precio' => 6990, 'precio_mayorista' => 6190, 'stock' => 45, 'foto' => '1673994801206-58e8763ab116'],
        ['slug' => 'pollo-agridulce', 'categoria' => 'pollo', 'nombre' => 'Pollo agridulce', 'descripcion' => 'Apanado, con piña, pimentón y salsa agridulce de la casa.', 'precio' => 6790, 'precio_mayorista' => 5990, 'stock' => 45, 'foto' => '1664138788119-bd4f073259d5'],
        ['slug' => 'pollo-sesamo', 'categoria' => 'pollo', 'nombre' => 'Pollo al sésamo', 'descripcion' => 'Crujiente, glaseado con miel y sésamo tostado.', 'precio' => 6990, 'stock' => 30, 'foto' => '1605491126264-ffea5a04e1f5'],
        ['slug' => 'pollo-limon', 'categoria' => 'pollo', 'nombre' => 'Pollo al limón', 'descripcion' => 'Apanado, con salsa de limón y cebollín.', 'precio' => 6790, 'stock' => 4, 'foto' => '1682622110433-65513a55d7da'],

        ['slug' => 'carne-mongoliana', 'categoria' => 'carne', 'nombre' => 'Carne mongoliana', 'descripcion' => 'Lomo salteado con pimentón y cebollín, levemente picante.', 'precio' => 7790, 'precio_mayorista' => 6890, 'stock' => 40, 'foto' => '1702705487239-10a1ca715454'],
        ['slug' => 'cerdo-agridulce', 'categoria' => 'carne', 'nombre' => 'Cerdo agridulce', 'descripcion' => 'Apanado, con pimentón, cebolla y piña.', 'precio' => 6790, 'precio_mayorista' => 5990, 'stock' => 40, 'foto' => '1654482921285-59834b69c0f4'],
        ['slug' => 'bowl-mongoliano', 'categoria' => 'carne', 'nombre' => 'Bowl mongoliano', 'descripcion' => 'Carne mongoliana sobre arroz graneado, para uno.', 'precio' => 5490, 'stock' => 30, 'foto' => '1662050890689-6f1e9183787f'],

        ['slug' => 'wantan', 'categoria' => 'picar', 'nombre' => 'Wantán frito (10 u.)', 'descripcion' => 'Relleno de cerdo, crujiente, con salsa agridulce.', 'precio' => 3990, 'precio_mayorista' => 3490, 'stock' => 80, 'foto' => '/demos/_comun/fotos3d/wantan-frito.webp', 'modelo3d' => 'wantan-frito'],
        ['slug' => 'sopa-wantan', 'categoria' => 'picar', 'nombre' => 'Sopa wantán', 'descripcion' => 'Caldo de pollo, wantanes de cerdo y camarón, repollo y cebollín.', 'precio' => 4490, 'stock' => 30, 'foto' => '1693860385571-98a2822424e6', 'modelo3d' => ''],
        ['slug' => 'arrollados', 'categoria' => 'picar', 'nombre' => 'Arrollados primavera (4 u.)', 'descripcion' => 'Repollo, zanahoria y cerdo, con salsa agridulce.', 'precio' => 4290, 'precio_mayorista' => 3690, 'stock' => 60, 'foto' => '1787273670681-b1886c2807a3'],
        ['slug' => 'camarones', 'categoria' => 'picar', 'nombre' => 'Camarones apanados (10 u.)', 'descripcion' => 'Con salsa agridulce y limón.', 'precio' => 6990, 'stock' => 25, 'foto' => ''],

        ['slug' => 'te-helado', 'categoria' => 'bebidas', 'nombre' => 'Té helado de jazmín 500 cc', 'descripcion' => 'Preparado en el local, poco dulce.', 'precio' => 1990, 'stock' => 60, 'foto' => '1601390395693-364c0e22031a'],
        ['slug' => 'limonada', 'categoria' => 'bebidas', 'nombre' => 'Limonada con jengibre 500 cc', 'descripcion' => 'Limón exprimido, jengibre y menta.', 'precio' => 2290, 'stock' => 40, 'foto' => '1555949366-819808d99159'],
        ['slug' => 'bebida', 'categoria' => 'bebidas', 'nombre' => 'Bebida 1,5 litros', 'descripcion' => 'Varios sabores. Indica cuál en la nota.', 'precio' => 2490, 'precio_mayorista' => 1990, 'stock' => 90, 'foto' => ''],
        ['slug' => 'lychees', 'categoria' => 'bebidas', 'nombre' => 'Lychees en almíbar', 'descripcion' => 'Bien helados, para terminar.', 'precio' => 2490, 'stock' => 30, 'foto' => '1783749924332-f8e0b74c04ec'],

        ['slug' => 'bandeja-wantan', 'categoria' => 'eventos', 'nombre' => 'Bandeja cóctel: 50 wantanes', 'descripcion' => 'Fritos al momento, con dos salsas. Para 15 a 20 personas.', 'precio' => 18990, 'precio_mayorista' => 15990, 'stock' => 30, 'foto' => '/demos/_comun/fotos3d/wantan.webp', 'modelo3d' => 'wantan', 'solo_mayorista' => true],
        ['slug' => 'bandeja-arrollados', 'categoria' => 'eventos', 'nombre' => 'Bandeja cóctel: 30 arrollados mini', 'descripcion' => 'Con salsa agridulce y de soya. Para 15 personas.', 'precio' => 16990, 'precio_mayorista' => 13990, 'stock' => 25, 'foto' => '1768701544400-dfa8ca509d10', 'solo_mayorista' => true],
        ['slug' => 'colaciones', 'categoria' => 'eventos', 'nombre' => 'Colación ejecutiva (caja de 10)', 'descripcion' => 'Diez almuerzos individuales: chaulafán de pollo con pollo agridulce o carne mongoliana. Con palillos.', 'precio' => 59990, 'precio_mayorista' => 49990, 'stock' => 15, 'foto' => '1775039983749-aa6003c8ecf9', 'solo_mayorista' => true],
        ['slug' => 'fuente-chaulafan', 'categoria' => 'eventos', 'nombre' => 'Fuente de chaulafán para 20', 'descripcion' => 'Chaulafán especial en fuente térmica desechable, con cucharón.', 'precio' => 54990, 'precio_mayorista' => 45990, 'stock' => 10, 'foto' => '1789999375808-db7192b683b0', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Daniela Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Av. Ejemplo 2020, casa 14', 'Puente Alto', 'Pasaje interior'], ['Donde mi mamá', 'Calle Ficticia 345', 'La Florida', '']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Cristián Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Oficina Contable Los Aromos (ficticia)',
            'direcciones' => [['Oficina', 'Pasaje Inventado 88, oficina 3', 'Puente Alto', 'Recepción en el segundo piso']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueño de Wokazo', 'rol' => 'dueno'],
        // Solicitud de empresa por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Valeria Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Productora de Eventos Luna (ficticia)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Pedidos de ejemplo: hace_min = minutos antes del reinicio.
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 4320, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Puente Alto', 'direccion' => 'Av. Ejemplo 2020, casa 14 (Pasaje interior)', 'pago' => 'prueba', 'items' => [['menu-4', 1], ['te-helado', 2]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 10080, 'estado' => 'entregado', 'entrega' => 'retiro', 'pago' => 'al_recibir', 'items' => [['chaulafan-especial', 1], ['pollo-mongoliano', 1], ['wantan', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 2880, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Puente Alto', 'direccion' => 'Pasaje Inventado 88, oficina 3', 'pago' => 'al_recibir', 'items' => [['colaciones', 2]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 70, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Puente Alto', 'direccion' => 'Pasaje Inventado 88, oficina 3', 'pago' => 'al_recibir', 'cuando' => '19:00', 'nota' => 'Cumpleaños de la oficina: con servilletas extra.', 'items' => [['bandeja-wantan', 2], ['bandeja-arrollados', 1], ['bebida', 4]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 28, 'estado' => 'preparando', 'entrega' => 'retiro', 'cuando' => '', 'pago' => 'prueba', 'items' => [['menu-2', 1], ['limonada', 2]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 6, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'La Florida', 'direccion' => 'Calle Ficticia 345', 'pago' => 'prueba', 'items' => [['carne-mongoliana', 1], ['chaulafan-pollo', 1], ['arrollados', 1]]],
    ],
];
