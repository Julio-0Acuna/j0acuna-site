<?php
declare(strict_types=1);

// Carnicería El Aromo (Independencia): negocio ficticio de la versión Simple-page.
// Precios por kilo en CLP. Fotos: IDs de Unsplash (ver demos/carniceria/CREDITOS.md).
return [
    'nombre' => 'Carnicería El Aromo',
    'nombre_corto' => 'El Aromo',
    'categoria' => 'Carnicería',
    'comuna' => 'Independencia',
    'desde' => 1979,
    'eslogan' => 'Carne fresca, cortada a la vista y pesada delante tuyo',
    'bajada' => 'Tres generaciones en la misma esquina de Independencia. Elige el corte y los gramos, te lo dejamos listo para retirar o te lo llevamos el mismo día.',
    'nota' => 4.8,
    'horario' => [
        1 => ['08:30', '20:30'],
        2 => ['08:30', '20:30'],
        3 => ['08:30', '20:30'],
        4 => ['08:30', '20:30'],
        5 => ['08:30', '21:00'],
        6 => ['08:30', '21:00'],
        7 => ['09:00', '14:00'],
    ],
    'fotos' => [
        'hero' => '1625643269470-5d3e7b69fa34',
        'pizarra' => '1597760665504-82545eb8d32b',
        'mostrador' => '1560166444-441876015a70',
    ],
    // Gramos que se pueden pedir de cada corte (selector).
    'gramos' => [250, 500, 750, 1000, 1500, 2000, 3000],
    'gramos_defecto' => 1000,
    'usos' => [
        'parrilla' => 'Parrilla',
        'olla' => 'Olla',
        'horno' => 'Horno',
        'sarten' => 'Sartén',
    ],
    // Cortes de vacuno: slug => [nombre, para qué sirve, precio por kilo, usos, foto, sello]
    'cortes' => [
        'lomo-vetado' => ['nombre' => 'Lomo vetado', 'desc' => 'Jugoso y con su veta de grasa: el rey de la parrilla.', 'kilo' => 17990, 'usos' => ['parrilla', 'sarten'], 'foto' => '1690983321750-ad6f6d59a84b', 'sello' => 'El más pedido'],
        'lomo-liso' => ['nombre' => 'Lomo liso', 'desc' => 'Magro y tierno, con su capa de grasa. Bife a la plancha o entero al horno.', 'kilo' => 16490, 'usos' => ['parrilla', 'sarten', 'horno'], 'foto' => '1602470521006-59ab77068b0d', 'sello' => ''],
        'filete' => ['nombre' => 'Filete', 'desc' => 'El corte más tierno. Medallones, carpaccio o entero envuelto en tocino.', 'kilo' => 21990, 'usos' => ['sarten', 'horno'], 'foto' => '1602470521007-20293bd1fd31', 'sello' => ''],
        'asado-tira' => ['nombre' => 'Asado de tira', 'desc' => 'Costilla cortada en tiras de 3 cm. Fuego lento y paciencia.', 'kilo' => 11990, 'usos' => ['parrilla', 'horno'], 'foto' => '1625604086988-6e41981275fa', 'sello' => 'Oferta'],
        'punta-ganso' => ['nombre' => 'Punta de ganso', 'desc' => 'Con su tapa de grasa, entera a la parrilla o al horno con papas.', 'kilo' => 14990, 'usos' => ['parrilla', 'horno'], 'foto' => '1690983330536-3b0089d07cf9', 'sello' => ''],
        'posta-rosada' => ['nombre' => 'Posta rosada', 'desc' => 'Magra y versátil: bistec, escalopa o carne mechada.', 'kilo' => 11490, 'usos' => ['sarten', 'olla'], 'foto' => '1613454320437-0c228c8b1723', 'sello' => ''],
        'plateada' => ['nombre' => 'Plateada', 'desc' => 'Para el horno o la olla, se deshace después de dos horas.', 'kilo' => 10990, 'usos' => ['horno', 'olla'], 'foto' => '1448907503123-67254d59ca4f', 'sello' => ''],
        'molida' => ['nombre' => 'Molida especial 5 %', 'desc' => 'Posta molida al momento, delante tuyo. Para pino, hamburguesas y salsas.', 'kilo' => 9990, 'usos' => ['sarten', 'olla'], 'foto' => '1609183556292-f208be1a9644', 'sello' => 'Al momento'],
        'cazuela' => ['nombre' => 'Tapapecho para cazuela', 'desc' => 'En trozos, con su hueso aparte para el caldo. Ideal para cazuela y estofado.', 'kilo' => 8990, 'usos' => ['olla'], 'foto' => '1600180786489-2e9a5b4041b5', 'sello' => ''],
    ],
    // Pizarra de ofertas: [producto, detalle, precio, slug para pedir (o '')]
    'ofertas' => [
        ['Asado de tira', 'el kilo, hasta el sábado', '$10.990', 'asado-tira'],
        ['Molida corriente 10 %', 'pack de 3 kilos', '$21.990', ''],
        ['Pollo entero', 'el kilo', '$2.990', ''],
        ['Longaniza de la casa', '2 kilos', '$15.990', ''],
        ['Chuleta de cerdo centro', 'el kilo', '$6.490', ''],
        ['Carbón de espino', 'saco de 5 kilos', '$5.990', ''],
    ],
    // Pollo, cerdo, embutidos y para el asado: [nombre, detalle, precio, foto]
    'tambien' => [
        'Pollo' => [
            ['Pollo entero', 'Fresco, de 2 a 2,5 kg', '$3.290 el kg', '1587593810167-a84920ea0781'],
            ['Pechuga deshuesada', 'Sin piel, en filetes o entera', '$6.490 el kg', '1682991136736-a2b44623eeba'],
        ],
        'Cerdo' => [
            ['Chuleta centro', 'Corte grueso para la parrilla', '$6.990 el kg', '1611059263765-f57653f3bba3'],
            ['Panceta', 'Con cuero, para la olla o crujiente al horno', '$7.990 el kg', '1623047437095-27418540c288'],
        ],
        'Embutidos de la casa' => [
            ['Longaniza', 'Receta de la familia, sin colorantes', '$8.990 el kg', '1771243108040-696d2544adf3'],
            ['Chorizo parrillero', 'Con ají y ajo, para el choripán', '$7.490 el kg', '1770222463664-64ed52a56c7b'],
        ],
        'Para el asado' => [
            ['Carbón de espino', 'Saco de 5 kilos, enciende rápido', '$6.990', '1781292428336-1be78d2867e0'],
            ['Pan amasado', 'Bolsa de 6, del horno del barrio', '$2.490', '1609889132680-4e2ea2befbef'],
        ],
    ],
    // Packs para asado (unos 400 g de carne por persona).
    'packs' => [
        ['personas' => 4, 'precio' => 24990, 'kilos' => '1,6 kg de carne', 'trae' => ['600 g de asado de tira', '500 g de punta de ganso', '4 longanizas', 'Carbón de espino 2,5 kg'], 'foto' => '1607623814075-e51df1bdc82f'],
        ['personas' => 8, 'precio' => 46990, 'kilos' => '3,2 kg de carne', 'trae' => ['1 kg de lomo vetado', '1 kg de asado de tira', '800 g de punta de ganso', '8 chorizos parrilleros', 'Carbón de espino 5 kg'], 'foto' => '1767065703762-cfb5aef34d69', 'destacado' => true],
        ['personas' => 12, 'precio' => 67990, 'kilos' => '4,8 kg de carne', 'trae' => ['1,5 kg de lomo vetado', '1,5 kg de asado de tira', '1 kg de punta de ganso', '12 longanizas y chorizos', '2 pollos trozados', 'Carbón de espino 10 kg'], 'foto' => '1555939594-58d7cb561ad1'],
    ],
    // Lista de precios completa (sección con QR): categoría => [[producto, precio]]
    'lista' => [
        'Vacuno (el kg)' => [
            ['Filete', '$21.990'], ['Lomo vetado', '$17.990'], ['Lomo liso', '$16.490'], ['Punta de ganso', '$14.990'],
            ['Asado de tira', '$11.990'], ['Posta rosada', '$11.490'], ['Posta negra', '$11.490'], ['Plateada', '$10.990'],
            ['Molida especial 5 %', '$9.990'], ['Molida corriente 10 %', '$7.990'], ['Tapapecho', '$8.990'], ['Osobuco', '$6.990'],
        ],
        'Pollo y cerdo (el kg)' => [
            ['Pollo entero', '$3.290'], ['Pechuga deshuesada', '$6.490'], ['Trutro entero', '$3.790'], ['Alitas', '$4.290'],
            ['Chuleta centro', '$6.990'], ['Pulpa de cerdo', '$6.490'], ['Panceta', '$7.990'], ['Costillar', '$7.490'],
        ],
        'Embutidos (el kg)' => [
            ['Longaniza de la casa', '$8.990'], ['Chorizo parrillero', '$7.490'], ['Prieta', '$6.990'], ['Salchicha de la casa', '$6.490'],
        ],
        'Para el asado' => [
            ['Carbón de espino 5 kg', '$6.990'], ['Pan amasado (6 u.)', '$2.490'], ['Sal de mar gruesa 1 kg', '$1.490'], ['Pebre de la casa 250 g', '$1.990'],
        ],
    ],
    'comunas_despacho' => ['Independencia', 'Recoleta', 'Conchalí', 'Santiago Centro', 'Renca'],
    'resenas' => [
        ['Marcela P.', 5, 'Don Hernán te aconseja el corte según lo que vas a cocinar. La molida especial la muelen al momento y se nota.', 'hace 1 semana'],
        ['Cristián A.', 5, 'Pedí el pack para 8 por WhatsApp para un cumpleaños y llegó todo listo, hasta con el carbón. Precio justo.', 'hace 3 semanas'],
        ['Rosa M.', 4, 'Carnicería de barrio de las de antes. Los sábados hay fila, conviene pedir temprano por WhatsApp y pasar a buscar.', 'hace 2 meses'],
    ],
];
