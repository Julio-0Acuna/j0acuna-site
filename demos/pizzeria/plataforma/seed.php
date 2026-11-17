<?php
declare(strict_types=1);

/*
 * Datos iniciales de Cohete Pizza (se cargan al crear la base y en cada reinicio por hora).
 * Productos genéricos, precios en CLP. Fotos: IDs de Unsplash (ver demos/CREDITOS.md).
 * Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 */
return [
    'categorias' => [
        ['slug' => 'pizzas', 'nombre' => 'Pizzas familiares'],
        ['slug' => 'combos', 'nombre' => 'Combos'],
        ['slug' => 'acompanamientos', 'nombre' => 'Para acompañar'],
        ['slug' => 'bebidas', 'nombre' => 'Bebidas'],
        ['slug' => 'postres', 'nombre' => 'Postres'],
        ['slug' => 'negocios', 'nombre' => 'Para negocios'],
    ],

    'productos' => [
        ['slug' => 'pepperoni', 'categoria' => 'pizzas', 'nombre' => 'Pepperoni Cohete', 'descripcion' => 'Doble pepperoni, mozzarella y orégano. 8 porciones.', 'precio' => 10990, 'precio_mayorista' => 8990, 'stock' => 40, 'foto' => '1542282811-943ef1a977c3', 'modelo3d' => 'pizza', 'destacado' => true],
        ['slug' => 'suprema', 'categoria' => 'pizzas', 'nombre' => 'Suprema', 'descripcion' => 'Pepperoni, pimentón, cebolla, champiñón y aceitunas.', 'precio' => 12990, 'precio_mayorista' => 10490, 'stock' => 35, 'foto' => '1615719413546-198b25453f85', 'modelo3d' => 'pizza', 'destacado' => true],
        ['slug' => 'pollo-bbq', 'categoria' => 'pizzas', 'nombre' => 'Pollo BBQ', 'descripcion' => 'Pollo asado, salsa barbacoa, cebolla morada y choclo.', 'precio' => 12490, 'precio_mayorista' => 9990, 'stock' => 30, 'foto' => '1588315029754-2dd089d39a1a', 'modelo3d' => 'pizza'],
        ['slug' => 'doble-queso', 'categoria' => 'pizzas', 'nombre' => 'Doble queso', 'descripcion' => 'Mozzarella y queso gauda gratinados sobre salsa de la casa.', 'precio' => 9990, 'precio_mayorista' => 7990, 'stock' => 40, 'foto' => '1600028068383-ea11a7a101f3', 'modelo3d' => 'pizza', 'destacado' => true],
        ['slug' => 'veggie', 'categoria' => 'pizzas', 'nombre' => 'Veggie', 'descripcion' => 'Pimentón, champiñón, tomate cherry, aceitunas y rúcula.', 'precio' => 11490, 'precio_mayorista' => 9290, 'stock' => 25, 'foto' => '1565299624946-b28f40a0ae38', 'modelo3d' => 'pizza'],
        ['slug' => 'chilena', 'categoria' => 'pizzas', 'nombre' => 'Chilena', 'descripcion' => 'Carne mechada, cebolla caramelizada, tomate y orégano.', 'precio' => 12990, 'precio_mayorista' => 10490, 'stock' => 20, 'foto' => '1506354666786-959d6d497f1a', 'modelo3d' => 'pizza'],
        ['slug' => 'mitad', 'categoria' => 'pizzas', 'nombre' => 'Mitad y mitad', 'descripcion' => 'Dos sabores en una pizza. Escribe cuáles en la nota del pedido.', 'precio' => 11990, 'precio_mayorista' => 9690, 'stock' => 3, 'foto' => '1566843972142-a7fcb70de55a', 'modelo3d' => 'pizza'],

        ['slug' => 'combo-cohete', 'categoria' => 'combos', 'nombre' => 'Combo Cohete', 'descripcion' => '2 pizzas familiares a elección (clásicas) + bebida de 1,5 litros.', 'precio' => 21990, 'stock' => 30, 'foto' => '1601924582970-9238bcb495d9', 'destacado' => true],
        ['slug' => 'combo-pareja', 'categoria' => 'combos', 'nombre' => 'Combo Pareja', 'descripcion' => '1 pizza familiar + calzone + 2 tés helados.', 'precio' => 18490, 'stock' => 25, 'foto' => '1576458088443-04a19bb13da6'],

        ['slug' => 'calzone', 'categoria' => 'acompanamientos', 'nombre' => 'Calzone jamón y queso', 'descripcion' => 'Masa rellena y horneada, con salsa para untar.', 'precio' => 6490, 'precio_mayorista' => 4990, 'stock' => 20, 'foto' => '1536964549204-cce9eab227bd', 'modelo3d' => 'calzone'],
        ['slug' => 'palitos', 'categoria' => 'acompanamientos', 'nombre' => 'Palitos de ajo (8 u.)', 'descripcion' => 'Con mantequilla de ajo y perejil.', 'precio' => 3490, 'precio_mayorista' => 2690, 'stock' => 30, 'foto' => ''],

        ['slug' => 'te-helado', 'categoria' => 'bebidas', 'nombre' => 'Té helado de limón 500 cc', 'descripcion' => 'Preparado en el local, poco dulce.', 'precio' => 1990, 'stock' => 60, 'foto' => '1556679343-c7306c1976bc'],
        ['slug' => 'jugo', 'categoria' => 'bebidas', 'nombre' => 'Jugo de frambuesa 500 cc', 'descripcion' => 'Fruta natural y hielo.', 'precio' => 2290, 'stock' => 40, 'foto' => '1497534446932-c925b458314e'],
        ['slug' => 'bebida', 'categoria' => 'bebidas', 'nombre' => 'Bebida 1,5 litros', 'descripcion' => 'Varios sabores. Indica cuál en la nota.', 'precio' => 2490, 'precio_mayorista' => 1990, 'stock' => 80, 'foto' => ''],

        ['slug' => 'helado', 'categoria' => 'postres', 'nombre' => 'Copa de helado con crumble', 'descripcion' => 'Helado de vainilla, crumble de galleta y caramelo.', 'precio' => 2990, 'stock' => 25, 'foto' => '1586040140378-b5634cb4c8fc', 'modelo3d' => 'helado'],

        ['slug' => 'bollos', 'categoria' => 'negocios', 'nombre' => 'Bollos de masa para pizza (caja 20 u.)', 'descripcion' => 'Masa de 48 horas, lista para estirar. Bollos de 250 g, congelados.', 'precio' => 15990, 'precio_mayorista' => 12990, 'stock' => 50, 'foto' => '1604745186348-f2ced902a93c', 'solo_mayorista' => true],
        ['slug' => 'salsa', 'categoria' => 'negocios', 'nombre' => 'Salsa de tomate de la casa (balde 5 kg)', 'descripcion' => 'Tomate, ajo, orégano y aceite de oliva. Sin conservantes.', 'precio' => 18990, 'precio_mayorista' => 14990, 'stock' => 30, 'foto' => '1472476443507-c7a5948772fc', 'solo_mayorista' => true],
        ['slug' => 'mozzarella', 'categoria' => 'negocios', 'nombre' => 'Mozzarella fresca (bolsa 2 kg)', 'descripcion' => 'Ideal para pizza: funde parejo y no suelta agua.', 'precio' => 24990, 'precio_mayorista' => 19990, 'stock' => 20, 'foto' => '1596924699736-be026ddb5b0c', 'solo_mayorista' => true],
        ['slug' => 'precocidas', 'categoria' => 'negocios', 'nombre' => 'Pizzas precocidas familiares (caja 6 u.)', 'descripcion' => 'Base con salsa, lista para tu ingrediente y 8 minutos de horno.', 'precio' => 39990, 'precio_mayorista' => 32990, 'stock' => 15, 'foto' => '1604382354936-07c5d9983bd3', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Camila Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Av. Ejemplo 1234, depto. 52', 'Maipú', 'Torre B'], ['Trabajo', 'Calle Ficticia 77', 'Estación Central', '']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Rodrigo Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Café Las Acacias (ficticio)',
            'direcciones' => [['Local', 'Pasaje Inventado 456', 'Cerrillos', 'Entrada por el costado']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueño de Cohete Pizza', 'rol' => 'dueno'],
        // Solicitud mayorista por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Sofía Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Fuente de soda El Andén (ficticio)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Pedidos de ejemplo: hace_min = minutos antes del reinicio.
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 4320, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Maipú', 'direccion' => 'Av. Ejemplo 1234, depto. 52 (Torre B)', 'pago' => 'prueba', 'items' => [['pepperoni', 2], ['te-helado', 2]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 11520, 'estado' => 'entregado', 'entrega' => 'retiro', 'pago' => 'al_recibir', 'items' => [['combo-cohete', 1], ['helado', 2]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 2880, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Cerrillos', 'direccion' => 'Pasaje Inventado 456', 'pago' => 'al_recibir', 'items' => [['bollos', 3], ['salsa', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 55, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Cerrillos', 'direccion' => 'Pasaje Inventado 456', 'pago' => 'al_recibir', 'nota' => 'Recibe Rodrigo en la caja.', 'items' => [['bollos', 2], ['mozzarella', 1], ['salsa', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 32, 'estado' => 'preparando', 'entrega' => 'retiro', 'cuando' => '', 'pago' => 'prueba', 'items' => [['suprema', 1], ['calzone', 1], ['jugo', 2]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 9, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Maipú', 'direccion' => 'Av. Ejemplo 1234, depto. 52 (Torre B)', 'pago' => 'prueba', 'items' => [['doble-queso', 1], ['palitos', 1], ['bebida', 1]]],
    ],
];
