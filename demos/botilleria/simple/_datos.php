<?php
declare(strict_types=1);

// Botillería La Pasada (Peñalolén): negocio ficticio de la versión Simple-page.
// Productos genéricos, sin marcas. Fotos: IDs de Unsplash (ver demos/botilleria/CREDITOS.md).
return [
    'nombre' => 'Botillería La Pasada',
    'nombre_corto' => 'La Pasada',
    'categoria' => 'Botillería con delivery',
    'comuna' => 'Peñalolén',
    'nota' => 4.7,
    'horario' => [
        1 => ['11:00', '23:00'],
        2 => ['11:00', '23:00'],
        3 => ['11:00', '23:00'],
        4 => ['11:00', '23:00'],
        5 => ['11:00', '01:00'],
        6 => ['11:00', '01:00'],
        7 => ['12:00', '22:00'],
    ],
    'despacho' => 1500,
    'despacho_gratis' => 25000,
    'fotos' => [
        'hero' => '1593021970768-8a57eda873ce',
        'hielo' => '1782179186167-8fe5542d0767',
        'cubo' => '1654146218080-40d0ec9cce46',
        'moto' => '1617347454431-f49d7ff5c3b1',
        'abierto' => '1676917824872-83f2fa04898b',
    ],
    // Lo que más sale: se pide con cantidad (?pedir=id&cantidad=n → WhatsApp con el total).
    'productos' => [
        'lager-6' => ['nombre' => 'Cerveza lager 6 latas 470 ml', 'desc' => 'Pack de seis latas, directo del refrigerador.', 'precio' => 6990, 'foto' => '1642294541583-c58730caa762', 'sello' => 'La que más sale'],
        'ambar-6' => ['nombre' => 'Cerveza ámbar 6 botellas 330 ml', 'desc' => 'Maltosa y suave, con notas a caramelo.', 'precio' => 7490, 'foto' => '1644085159285-5fd924740cb3', 'sello' => ''],
        'carmenere' => ['nombre' => 'Vino tinto reserva Carménère 750 ml', 'desc' => 'Valle Central. Para el asado o la once.', 'precio' => 5990, 'foto' => '1635674686922-521a81fb0e12', 'sello' => 'Para el asado'],
        'pisco-35' => ['nombre' => 'Pisco 35° 700 ml', 'desc' => 'Transparente, del Valle del Elqui.', 'precio' => 5990, 'foto' => '1591704951890-0862b2e98acb', 'sello' => ''],
        'hielo-2' => ['nombre' => 'Hielo en cubos 2 kg', 'desc' => 'Cubos de agua purificada, siempre en el congelador.', 'precio' => 1990, 'foto' => '1590430752967-d0e116909be1', 'sello' => 'Siempre hay'],
        'bebida-3' => ['nombre' => 'Bebida 3 L sabores surtidos', 'desc' => 'Cola, naranja, lima-limón o frambuesa: elige en el chat.', 'precio' => 2990, 'foto' => '1568657624422-1b8713e79461', 'sello' => ''],
        'agua-16' => ['nombre' => 'Agua mineral 1,6 L', 'desc' => 'Con o sin gas.', 'precio' => 1290, 'foto' => '1616118132534-381148898bb4', 'sello' => ''],
        'papas-380' => ['nombre' => 'Papas fritas 380 g', 'desc' => 'Corte liso, con sal de mar.', 'precio' => 2990, 'foto' => '1613919113640-25732ec5e61f', 'sello' => ''],
        'mani-400' => ['nombre' => 'Maní salado 400 g', 'desc' => 'Tostado y salado, para picar.', 'precio' => 2490, 'foto' => '1596503799872-5c5f052c27b9', 'sello' => ''],
        'carbon-5' => ['nombre' => 'Carbón vegetal 5 kg', 'desc' => 'Prende rápido y dura toda la parrilla.', 'precio' => 5990, 'foto' => '1697970684485-eea7cccfa61f', 'sello' => ''],
        'limones' => ['nombre' => 'Limones malla 1 kg', 'desc' => 'Para el pisco sour, la limonada o la ensalada.', 'precio' => 1990, 'foto' => '1590502593747-42a996133562', 'sello' => ''],
    ],
    'packs' => [
        [
            'id' => 'asado', 'titulo' => 'Pack Asado', 'para' => 'Para 6 personas', 'precio' => 29990, 'normal' => 32930,
            'foto' => '1586058584825-c1e87ed735b4', 'alt' => 'Carne a la parrilla sobre el carbón encendido',
            'trae' => ['2 packs de cerveza lager 6 latas', '1 vino tinto reserva Carménère', 'Carbón vegetal 5 kg', 'Hielo en cubos 2 kg', 'Bebida 3 L', 'Limones malla 1 kg'],
        ],
        [
            'id' => 'cumple', 'titulo' => 'Pack Cumpleaños', 'para' => 'Para 10 personas', 'precio' => 30990, 'normal' => 34890,
            'foto' => '1777332546833-c41530f7d014', 'alt' => 'Mesa de cumpleaños decorada con globos azules y torta',
            'trae' => ['2 piscos 35° 700 ml', '4 bebidas 3 L', '2 bolsas de hielo 2 kg', 'Papas fritas y maní salado', 'Vasos 20 unidades'],
        ],
        [
            'id' => 'familia', 'titulo' => 'Pack Familia', 'para' => 'Sin alcohol', 'precio' => 14990, 'normal' => 17120,
            'foto' => '1690983321954-30481504365f', 'alt' => 'Jarras y vasos con jugos de colores sobre una mesa',
            'trae' => ['2 bebidas 3 L', '2 jugos néctar 1,5 L', '2 aguas minerales 1,6 L', 'Hielo en cubos 2 kg', 'Papas fritas 380 g'],
        ],
    ],
    // Promociones de fin de semana (cupones).
    'promos' => [
        ['dias' => 'Viernes y sábado', 'titulo' => '2 packs de lager', 'precio' => '$12.990', 'detalle' => 'Dos packs de 6 latas de 470 ml. Antes $13.980.', 'mensaje' => 'Hola, quiero la promo de fin de semana: 2 packs de cerveza lager 6 latas por $12.990.'],
        ['dias' => 'Viernes y sábado', 'titulo' => 'Hielo de regalo', 'precio' => 'Gratis', 'detalle' => 'Una bolsa de 2 kg en pedidos sobre $20.000.', 'mensaje' => 'Hola, quiero hacer un pedido sobre $20.000 con la bolsa de hielo de regalo.'],
        ['dias' => 'Domingo', 'titulo' => 'Carbón + hielo', 'precio' => '$6.990', 'detalle' => 'Carbón 5 kg y hielo 2 kg para el asado del domingo.', 'mensaje' => 'Hola, quiero la promo del domingo: carbón 5 kg + hielo 2 kg por $6.990.'],
    ],
    // Lista de precios completa (sección #precios): [nombre, detalle, precio].
    'precios' => [
        'Cervezas' => [
            ['Cerveza lager 6 latas 470 ml', 'Pack', 6990],
            ['Cerveza lager lata 470 ml', 'Unidad', 1290],
            ['Cerveza ámbar 6 botellas 330 ml', 'Pack', 7490],
            ['Cerveza sin alcohol 6 latas 350 ml', 'Pack', 5490],
            ['Cerveza artesanal botella 500 ml', 'Rubia, roja o negra', 2990],
        ],
        'Vinos y espumantes' => [
            ['Vino tinto reserva Carménère 750 ml', 'Valle Central', 5990],
            ['Vino tinto Cabernet Sauvignon 750 ml', 'Valle del Maipo', 4490],
            ['Vino blanco Sauvignon Blanc 750 ml', 'Valle de Casablanca', 4490],
            ['Vino tinto en caja 2 L', 'Para la mesa', 4990],
            ['Espumante brut 750 ml', 'Para brindar', 6990],
        ],
        'Piscos y destilados' => [
            ['Pisco 35° 700 ml', 'Transparente', 5990],
            ['Pisco 40° 750 ml', 'Reservado', 7990],
            ['Ron dorado 750 ml', 'Añejado en barrica', 8990],
            ['Gin seco 700 ml', 'Con botánicos', 11990],
            ['Whisky 750 ml', 'Mezcla escocesa', 12990],
        ],
        'Bebidas, agua y jugos' => [
            ['Bebida 3 L', 'Sabores surtidos', 2990],
            ['Bebida 1,5 L', 'Sabores surtidos', 1990],
            ['Agua mineral 1,6 L', 'Con o sin gas', 1290],
            ['Agua tónica 1,5 L', 'Para el gin', 1990],
            ['Jugo néctar 1,5 L', 'Durazno, naranja o piña', 1790],
        ],
        'Hielo y carbón' => [
            ['Hielo en cubos 2 kg', 'Bolsa', 1990],
            ['Hielo en cubos 5 kg', 'Bolsa grande', 3990],
            ['Carbón vegetal 2,5 kg', 'Saco chico', 3490],
            ['Carbón vegetal 5 kg', 'Saco', 5990],
            ['Pastillas para encender', '24 unidades', 1490],
        ],
        'Para picar' => [
            ['Papas fritas 380 g', 'Corte liso', 2990],
            ['Maní salado 400 g', 'Tostado', 2490],
            ['Mix de frutos secos 200 g', 'Almendras, nueces y pasas', 3490],
            ['Limones malla 1 kg', 'Frescos', 1990],
            ['Vasos desechables 20 u.', 'Compostables', 1490],
        ],
    ],
    // Sectores del barrio con tiempo estimado de entrega.
    'sectores' => [
        ['Peñalolén Alto', '25–35 min'],
        ['Lo Hermida', '25–35 min'],
        ['La Faena', '30–40 min'],
        ['San Luis', '30–40 min'],
        ['Quebrada de Macul', '35–45 min'],
    ],
    'resenas' => [
        ['Javiera P.', 5, 'Un sábado a las diez de la noche pedí hielo y bebidas: llegaron en 25 minutos y bien helados. Me pidieron el carnet en la puerta, como corresponde.', 'hace 1 semana'],
        ['Rodrigo M.', 5, 'El pack asado sale a cuenta y trae carbón, hielo y limones. Atención de barrio de verdad.', 'hace 3 semanas'],
        ['Carolina V.', 4, 'Buenos precios y siempre tienen hielo. El viernes en la noche se demoran un poco más, pero avisan.', 'hace 2 meses'],
    ],
];
