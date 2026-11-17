<?php
declare(strict_types=1);

// Maki Neón (San Miguel): negocio ficticio de la versión Simple-page.
// Sushi de barrio con delivery nocturno. Fotos: IDs de Unsplash (ver demos/sushi/CREDITOS.md).
return [
    'nombre' => 'Maki Neón',
    'categoria' => 'Restaurante de sushi con delivery',
    'comuna' => 'La Florida',
    'eslogan' => 'Sushi de barrio hasta las 3 de la mañana',
    'bajada' => 'Rolls enrollados al momento, gyozas a la plancha y ramen caliente. Pide por WhatsApp y te lo llevamos en moto por La Florida y alrededores, de noche y de madrugada.',
    'nota' => 4.8,
    'horario' => [
        1 => ['18:30', '00:30'],
        2 => ['18:30', '01:30'],
        3 => ['18:30', '01:30'],
        4 => ['18:30', '01:30'],
        5 => ['18:30', '03:00'],
        6 => ['18:30', '03:00'],
        7 => ['18:30', '00:30'],
    ],
    'hero_foto' => '1781478990255-f4f61976a1dc',
    // Rolls destacados: precio de 10 piezas.
    'destacados' => [
        ['id' => 'acevichado', 'nombre' => 'Neón acevichado', 'desc' => 'Camarón tempura y palta, cubierto de salmón flameado con salsa acevichada.', 'foto' => '1761314025679-1bc4e2f6f7fe', 'alt' => 'Rolls de sushi flameados con soplete sobre fondo oscuro', 'precio' => 7990, 'sello' => 'Flameado'],
        ['id' => 'philadelphia', 'nombre' => 'Philadelphia clásico', 'desc' => 'Salmón, queso crema y cebollín, envuelto en arroz y sésamo.', 'foto' => '1579871494447-9811cf80d66c', 'alt' => 'Rolls de salmón con queso crema y cebollín en un plato', 'precio' => 6490, 'sello' => 'El más pedido'],
        ['id' => 'ebi-crunch', 'nombre' => 'Ebi crunch', 'desc' => 'Camarón furai y palta, roll frito en panko con salsa teriyaki.', 'foto' => '1761314026068-f07ec1ce6e2b', 'alt' => 'Roll de camarón tempura tomado con palillos', 'precio' => 6990, 'sello' => 'Frito'],
        ['id' => 'avocado-night', 'nombre' => 'Avocado night', 'desc' => 'Kanikama y pepino, envuelto en palta con papas hilo crocantes.', 'foto' => '1761315413309-2add8b1b0b1e', 'alt' => 'Rolls envueltos en palta con papas hilo crocantes', 'precio' => 6990, 'sello' => ''],
        ['id' => 'spicy-salmon', 'nombre' => 'Spicy salmón', 'desc' => 'Salmón picado con sriracha de la casa, palta, masago y mayo picante.', 'foto' => '1617196035154-1e7e6e28b0db', 'alt' => 'Rolls con salsa picante y masago sobre bandeja negra', 'precio' => 7490, 'sello' => 'Picante'],
        ['id' => 'nigiri-salmon', 'nombre' => 'Nigiri de salmón', 'desc' => 'Cuatro piezas de salmón fresco sobre arroz de sushi, con un toque de wasabi.', 'foto' => '1562158074-d49fbeffcc91', 'alt' => 'Dos nigiris de salmón sobre fondo negro', 'precio' => 4990, 'sello' => '4 piezas'],
    ],
    'promos' => [
        ['titulo' => 'Combo Dúo', 'piezas' => '30 piezas', 'texto' => 'Philadelphia, ebi crunch y avocado night, diez de cada uno. Con soya, jengibre y wasabi.', 'precio' => 15990, 'foto' => '1617196034183-421b4917c92d', 'alt' => 'Rolls de salmón y palta servidos en fila, uno tomado con palillos', 'tono' => 'rosa', 'mensaje' => 'Hola, quiero el Combo Dúo de 30 piezas ($15.990).'],
        ['titulo' => 'Tabla Noctámbula', 'piezas' => '50 piezas', 'texto' => 'Cinco rolls a elección y diez nigiris para compartir. La de la junta del viernes.', 'precio' => 27990, 'foto' => '1611143669185-af224c5e3252', 'alt' => 'Tabla grande de sushi con rolls y nigiris vista desde arriba', 'tono' => 'cian', 'mensaje' => 'Hola, quiero la Tabla Noctámbula de 50 piezas ($27.990).'],
        ['titulo' => 'Miércoles de gyozas', 'piezas' => '2×1', 'texto' => 'Pide seis gyozas de cerdo y te llevas otras seis. Solo los miércoles, hasta el cierre.', 'precio' => 4490, 'foto' => '1738681336104-608b4e7dc3b0', 'alt' => 'Gyozas doradas a la plancha en un plato negro', 'tono' => 'limon', 'mensaje' => 'Hola, quiero la promo Miércoles de gyozas 2×1 ($4.490).'],
    ],
    // Menú completo (sección #menu): [nombre, detalle, precio]
    'menu' => [
        'Rolls · 10 piezas' => [
            ['California', 'Kanikama, palta y pepino, con sésamo', 5490],
            ['Philadelphia clásico', 'Salmón, queso crema y cebollín', 6490],
            ['Veggie', 'Palta, pepino, zanahoria y queso crema', 5290],
            ['Tori teriyaki', 'Pollo teriyaki, queso crema y cebollín', 5990],
            ['Ebi crunch', 'Camarón furai y palta, frito en panko', 6990],
            ['Avocado night', 'Kanikama y pepino, envuelto en palta', 6990],
            ['Spicy salmón', 'Salmón picante, palta y masago', 7490],
            ['Neón acevichado', 'Camarón tempura, salmón flameado y salsa acevichada', 7990],
        ],
        'Nigiris y sashimi' => [
            ['Nigiri de salmón · 4 piezas', 'Con un toque de wasabi', 4990],
            ['Nigiri de atún · 4 piezas', 'Atún rojo sobre arroz de sushi', 5490],
            ['Nigiri de camarón · 4 piezas', 'Camarón cocido abierto en mariposa', 4490],
            ['Sashimi de salmón · 9 cortes', 'Con daikon, limón y soya', 7990],
            ['Hand roll de salmón', 'Cono de nori con salmón, palta y queso crema', 3990],
        ],
        'Calientes y para picar' => [
            ['Gyozas de cerdo · 6', 'A la plancha, con salsa ponzu', 4490],
            ['Gyozas de verduras · 6', 'Repollo, zanahoria y shiitake', 4290],
            ['Ramen tonkotsu', 'Caldo de cerdo, chashu, huevo marinado y cebollín', 8990],
            ['Ramen picante de miso', 'Miso, carne molida, maíz y aceite de ají', 9290],
            ['Ebi tempura · 5 camarones', 'Con salsa tentsuyu', 5990],
            ['Edamame', 'Con sal de mar', 2990],
        ],
        'Para compartir' => [
            ['Combo Dúo · 30 piezas', 'Tres rolls de la casa', 15990],
            ['Tabla Noctámbula · 50 piezas', 'Cinco rolls a elección y diez nigiris', 27990],
            ['Tabla Medianoche · 80 piezas', 'Ocho rolls a elección, para 4 a 5 personas', 39990],
        ],
        'Bebidas y postres' => [
            ['Bebida en lata 350 cc', 'Varios sabores', 1290],
            ['Té verde helado 500 cc', 'Preparado en el local, poco dulce', 2290],
            ['Limonada de jengibre 500 cc', 'Limón, jengibre y menta', 2490],
            ['Mochi de té verde · 2', 'Masa de arroz rellena de helado', 2990],
        ],
    ],
    'menu_fotos' => [
        ['1526318896980-cf78c088247c', 'Ramen con huevo y cerdo, servido con palillos'],
        ['1611810174991-5cdd99a2c6b2', 'Edamame con wasabi y jengibre en platos blancos'],
        ['1710962169574-d1b52a2670e2', 'Nigiri tomado con palillos sobre fondo negro'],
    ],
    'letrero_foto' => '1639650538773-ffe1d8ad9d3f',
    'cierre_foto' => '1646196603168-ed92068477c3',
    'comunas' => [
        'La Florida' => 1490,
        'Macul' => 1990,
        'La Granja' => 1990,
        'Peñalolén' => 2490,
        'Puente Alto (norte)' => 2490,
        'San Joaquín' => 2490,
    ],
    'resenas' => [
        ['Fernanda G.', 5, 'Pedimos a la una de la mañana después de un cumpleaños y llegó en 35 minutos. El acevichado flameado es otro nivel.', 'hace 1 semana'],
        ['Kevin A.', 5, 'El mejor Philadelphia de La Florida. Pedir por WhatsApp es rapidísimo y te confirman al tiro.', 'hace 3 semanas'],
        ['Javiera P.', 4, 'La Tabla Noctámbula alcanza para cuatro. Los viernes se demora un poco más, pero te avisan.', 'hace 2 meses'],
    ],
];
