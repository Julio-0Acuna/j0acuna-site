<?php
declare(strict_types=1);

/*
 * Datos iniciales de Nori Club (se cargan al crear la base y en cada reinicio por hora).
 * Productos genéricos, precios en CLP. Fotos: IDs de Unsplash (ver demos/sushi/CREDITOS.md).
 * Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 * "Mayorista" = empresas y eventos: precios por volumen y bandejas grandes.
 */
return [
    'categorias' => [
        ['slug' => 'combos', 'nombre' => 'Combos y tablas'],
        ['slug' => 'rolls', 'nombre' => 'Rolls · 10 piezas'],
        ['slug' => 'nigiri', 'nombre' => 'Nigiris y sashimi'],
        ['slug' => 'calientes', 'nombre' => 'Calientes'],
        ['slug' => 'poke', 'nombre' => 'Poke bowls'],
        ['slug' => 'bebidas', 'nombre' => 'Bebidas y postres'],
        ['slug' => 'empresas', 'nombre' => 'Empresas y eventos'],
    ],

    'productos' => [
        ['slug' => 'combo-duo', 'categoria' => 'combos', 'nombre' => 'Combo Dúo · 2 personas', 'descripcion' => '30 piezas: nigiris de salmón, makis y California, con 4 gyozas de regalo.', 'precio' => 16990, 'precio_mayorista' => 14990, 'stock' => 40, 'foto' => '/demos/_comun/fotos3d/sushi-combo-duo.webp', 'modelo3d' => 'sushi-combo-duo', 'destacado' => true],
        ['slug' => 'combo-familia', 'categoria' => 'combos', 'nombre' => 'Combo Familia · 4 personas', 'descripcion' => '60 piezas: seis rolls de la casa, ocho nigiris y dos porciones de gyozas.', 'precio' => 32990, 'precio_mayorista' => 28990, 'stock' => 30, 'foto' => '/demos/_comun/fotos3d/sushi.webp', 'modelo3d' => 'sushi', 'destacado' => true],
        ['slug' => 'tabla-club', 'categoria' => 'combos', 'nombre' => 'Tabla Club · 80 piezas', 'descripcion' => 'Rolls, nigiris y sashimi para 5 a 6 personas, con salsas de la casa.', 'precio' => 42990, 'precio_mayorista' => 37990, 'stock' => 15, 'foto' => '1553621042-f6e147245754', 'modelo3d' => ''],

        ['slug' => 'california', 'categoria' => 'rolls', 'nombre' => 'California', 'descripcion' => 'Kanikama, palta y pepino, envuelto en sésamo.', 'precio' => 5990, 'precio_mayorista' => 4990, 'stock' => 60, 'foto' => '/demos/_comun/fotos3d/sushi-california.webp', 'modelo3d' => 'sushi-california'],
        ['slug' => 'salmon-palta', 'categoria' => 'rolls', 'nombre' => 'Salmón palta', 'descripcion' => 'Uramaki de palta y queso crema, cubierto de láminas de salmón.', 'precio' => 6990, 'precio_mayorista' => 5790, 'stock' => 50, 'foto' => '/demos/_comun/fotos3d/sushi-salmon-palta.webp', 'modelo3d' => 'sushi-salmon-palta'],
        ['slug' => 'rainbow', 'categoria' => 'rolls', 'nombre' => 'Rainbow de atún', 'descripcion' => 'Kanikama y palta, cubierto de atún rojo y mayo spicy.', 'precio' => 7990, 'precio_mayorista' => 6690, 'stock' => 40, 'foto' => '1768326119762-20c2a9f5c1f2', 'modelo3d' => '', 'destacado' => true],
        ['slug' => 'dragon', 'categoria' => 'rolls', 'nombre' => 'Dragon roll', 'descripcion' => 'Camarón tempura, cubierto de palta y salsa de anguila.', 'precio' => 8490, 'precio_mayorista' => 7090, 'stock' => 30, 'foto' => '/demos/_comun/fotos3d/sushi-dragon-roll.webp', 'modelo3d' => 'sushi-dragon-roll'],
        ['slug' => 'ebi-furai', 'categoria' => 'rolls', 'nombre' => 'Ebi furai roll', 'descripcion' => 'Roll apanado de camarón y queso crema, cubierto de guacamole y masago.', 'precio' => 6990, 'precio_mayorista' => 5790, 'stock' => 45, 'foto' => '/demos/_comun/fotos3d/sushi-ebi-furai.webp', 'modelo3d' => 'sushi-ebi-furai'],
        ['slug' => 'masago', 'categoria' => 'rolls', 'nombre' => 'Roll masago', 'descripcion' => 'Salmón, pepino y queso crema, cubierto de salmón sellado, masago y mayo picante.', 'precio' => 7490, 'precio_mayorista' => 6190, 'stock' => 4, 'foto' => '/demos/_comun/fotos3d/sushi-roll-masago.webp', 'modelo3d' => 'sushi-roll-masago'],
        ['slug' => 'avocado-veggie', 'categoria' => 'rolls', 'nombre' => 'Avocado veggie', 'descripcion' => 'Hosomakis de palta, pepino y rabanito encurtido, con jengibre y wasabi.', 'precio' => 5790, 'precio_mayorista' => 4790, 'stock' => 35, 'foto' => '/demos/_comun/fotos3d/sushi-avocado-veggie.webp', 'modelo3d' => 'sushi-avocado-veggie'],

        ['slug' => 'nigiri-salmon', 'categoria' => 'nigiri', 'nombre' => 'Nigiri de salmón · 4 piezas', 'descripcion' => 'Salmón fresco sobre arroz de sushi, con un toque de wasabi.', 'precio' => 4990, 'precio_mayorista' => 4190, 'stock' => 50, 'foto' => '/demos/_comun/fotos3d/sushi-nigiri-salmon.webp', 'modelo3d' => 'sushi-nigiri-salmon'],
        ['slug' => 'nigiri-mix', 'categoria' => 'nigiri', 'nombre' => 'Nigiri mix · 10 piezas', 'descripcion' => 'Salmón, atún, camarón y pez mantequilla.', 'precio' => 11990, 'precio_mayorista' => 9990, 'stock' => 25, 'foto' => '/demos/_comun/fotos3d/sushi-nigiri-mix.webp', 'modelo3d' => 'sushi-nigiri-mix', 'destacado' => true],
        ['slug' => 'sashimi', 'categoria' => 'nigiri', 'nombre' => 'Sashimi de salmón · 12 cortes', 'descripcion' => 'Cortes gruesos, con daikon, limón y soya.', 'precio' => 10990, 'precio_mayorista' => 9290, 'stock' => 25, 'foto' => '1652955844784-572d10123757'],

        ['slug' => 'gyozas', 'categoria' => 'calientes', 'nombre' => 'Gyozas de cerdo · 6', 'descripcion' => 'A la plancha, con salsa ponzu.', 'precio' => 4490, 'precio_mayorista' => 3690, 'stock' => 60, 'foto' => '1588182728399-e8f2df121744', 'destacado' => true],
        ['slug' => 'ramen', 'categoria' => 'calientes', 'nombre' => 'Ramen tonkotsu', 'descripcion' => 'Caldo de cerdo de doce horas, chashu, huevo marinado y nori.', 'precio' => 9490, 'stock' => 20, 'foto' => '1638866281450-3933540af86a'],
        ['slug' => 'ramen-picante', 'categoria' => 'calientes', 'nombre' => 'Ramen picante de miso', 'descripcion' => 'Miso, aceite de ají, carne molida, huevo y cebollín.', 'precio' => 9790, 'stock' => 20, 'foto' => '1773817728546-f1a6c392d0f0'],
        ['slug' => 'ebi-tempura', 'categoria' => 'calientes', 'nombre' => 'Ebi tempura · 6', 'descripcion' => 'Camarones en tempura crujiente, con salsa tentsuyu.', 'precio' => 6490, 'precio_mayorista' => 5490, 'stock' => 30, 'foto' => '1651328236028-77b585d3f4e5'],

        ['slug' => 'poke-salmon', 'categoria' => 'poke', 'nombre' => 'Poke de salmón', 'descripcion' => 'Arroz de sushi, salmón, palta, edamame, mango y sésamo.', 'precio' => 8990, 'stock' => 25, 'foto' => '1768326119213-e0ad875083a3'],
        ['slug' => 'poke-atun', 'categoria' => 'poke', 'nombre' => 'Poke de atún', 'descripcion' => 'Arroz de sushi, atún marinado, palta, edamame y cebolla crispy.', 'precio' => 9490, 'stock' => 20, 'foto' => '1768326119231-bf064c1b8fdf'],

        ['slug' => 'matcha', 'categoria' => 'bebidas', 'nombre' => 'Matcha latte helado', 'descripcion' => 'Té matcha batido con leche y hielo. 400 cc.', 'precio' => 3490, 'stock' => 40, 'foto' => '1717603545758-88cc454db69b'],
        ['slug' => 'te-verde', 'categoria' => 'bebidas', 'nombre' => 'Té verde helado 500 cc', 'descripcion' => 'Preparado en el local, poco dulce.', 'precio' => 2290, 'stock' => 60, 'foto' => ''],
        ['slug' => 'bebida', 'categoria' => 'bebidas', 'nombre' => 'Bebida en lata 350 cc', 'descripcion' => 'Varios sabores. Indica cuál en la nota.', 'precio' => 1490, 'precio_mayorista' => 1190, 'stock' => 90, 'foto' => ''],
        ['slug' => 'mochi', 'categoria' => 'bebidas', 'nombre' => 'Mochi de matcha', 'descripcion' => 'Masa de arroz rellena de crema de té verde, con salsa de frutilla.', 'precio' => 3990, 'stock' => 25, 'foto' => '1724052526175-4a7332bd10e9'],

        ['slug' => 'bandeja-oficina', 'categoria' => 'empresas', 'nombre' => 'Bandeja Oficina · 100 piezas', 'descripcion' => 'Rolls y nigiris surtidos para 10 a 12 personas, con salsas y palillos.', 'precio' => 54990, 'precio_mayorista' => 46990, 'stock' => 20, 'foto' => '1763647756796-af9230245bf8', 'solo_mayorista' => true],
        ['slug' => 'bandeja-coctel', 'categoria' => 'empresas', 'nombre' => 'Bandeja Cóctel · 150 bocados', 'descripcion' => 'Nigiris, sashimi y rolls en bocado, para cócteles y matrimonios.', 'precio' => 82990, 'precio_mayorista' => 69990, 'stock' => 12, 'foto' => '1790761403214-3b98524ac18a', 'solo_mayorista' => true],
        ['slug' => 'makis-200', 'categoria' => 'empresas', 'nombre' => 'Makis surtidos · 200 piezas', 'descripcion' => 'Hosomaki de salmón, palta y kanikama para eventos grandes.', 'precio' => 89990, 'precio_mayorista' => 74990, 'stock' => 10, 'foto' => '1709984110217-57d7d18e5299', 'solo_mayorista' => true],
        ['slug' => 'gyozas-congeladas', 'categoria' => 'empresas', 'nombre' => 'Gyozas congeladas · bolsa 50 u.', 'descripcion' => 'Listas para la plancha. Para casinos y cafeterías.', 'precio' => 24990, 'precio_mayorista' => 19990, 'stock' => 30, 'foto' => '1609183590563-7710ba1f90a9', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Valentina Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Av. Ejemplo 4321, depto. 1102', 'Las Condes', 'Torre A, conserjería'], ['Trabajo', 'Calle Ficticia 88, oficina 501', 'Providencia', '']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Tomás Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Andes Consultores (ficticia)',
            'direcciones' => [['Oficina', 'Av. Inventada 2000, piso 12', 'Las Condes', 'Recepción']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueño de Nori Club', 'rol' => 'dueno'],
        // Solicitud de empresa por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Josefina Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Productora Eventos Cumbre (ficticia)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Pedidos de ejemplo: hace_min = minutos antes del reinicio.
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 4320, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Las Condes', 'direccion' => 'Av. Ejemplo 4321, depto. 1102 (Torre A, conserjería)', 'pago' => 'prueba', 'items' => [['combo-duo', 1], ['matcha', 2]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 12960, 'estado' => 'entregado', 'entrega' => 'retiro', 'pago' => 'al_recibir', 'items' => [['rainbow', 1], ['gyozas', 1], ['ramen', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 2880, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Las Condes', 'direccion' => 'Av. Inventada 2000, piso 12 (Recepción)', 'pago' => 'al_recibir', 'items' => [['bandeja-oficina', 2], ['bebida', 24]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 48, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Las Condes', 'direccion' => 'Av. Inventada 2000, piso 12 (Recepción)', 'pago' => 'al_recibir', 'cuando' => '13:00', 'nota' => 'Reunión de directorio: entregar en recepción a las 13:00.', 'items' => [['bandeja-coctel', 1], ['gyozas-congeladas', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 27, 'estado' => 'preparando', 'entrega' => 'retiro', 'pago' => 'prueba', 'items' => [['combo-familia', 1], ['mochi', 2]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 8, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Providencia', 'direccion' => 'Calle Ficticia 88, oficina 501', 'pago' => 'prueba', 'items' => [['nigiri-mix', 1], ['poke-salmon', 1], ['te-verde', 2]]],
    ],
];
