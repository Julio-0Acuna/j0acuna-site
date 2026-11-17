<?php
declare(strict_types=1);

// Panadería Las Tres Hornadas (Quinta Normal): negocio ficticio de la versión Simple-page.
// Fotos: IDs de Unsplash (ver demos/panaderia/CREDITOS.md). Precios en CLP, verosímiles.
return [
    'nombre' => 'Panadería Las Tres Hornadas',
    'nombre_corto' => 'Las Tres Hornadas',
    'categoria' => 'Panadería',
    'comuna' => 'Quinta Normal',
    'eslogan' => 'Pan caliente tres veces al día',
    'bajada' => 'Marraqueta crujiente, hallulla y pan amasado hechos aquí mismo, en la esquina de siempre. Pide por kilo por WhatsApp: te lo dejamos listo o te lo llevamos en Quinta Normal.',
    'nota' => 4.8,
    'horario' => [
        1 => ['06:30', '21:00'],
        2 => ['06:30', '21:00'],
        3 => ['06:30', '21:00'],
        4 => ['06:30', '21:00'],
        5 => ['06:30', '21:00'],
        6 => ['06:30', '21:00'],
        7 => ['07:30', '14:00'],
    ],
    // Hornadas por día (1 = lunes … 7 = domingo).
    'hornadas' => [
        1 => ['07:00', '12:30', '18:30'],
        2 => ['07:00', '12:30', '18:30'],
        3 => ['07:00', '12:30', '18:30'],
        4 => ['07:00', '12:30', '18:30'],
        5 => ['07:00', '12:30', '18:30'],
        6 => ['07:00', '12:30', '18:30'],
        7 => ['08:00', '11:30'],
    ],
    'fotos' => [
        'hero' => '1772758632784-7ccc90aa5328',
        'panadero' => '1560427183-4efd29c38997',
        'reparto' => '1567042661848-7161ce446f85',
        'canasto' => '1566698629409-787a68fc5724',
    ],
    // Pan del día. modo: kilo (botones ½, 1 y 2 kg) o unidad (1 y 2 unidades).
    'panes' => [
        ['id' => 'marraqueta', 'nombre' => 'Marraqueta', 'desc' => 'Crujiente por fuera y con miga blanda. La de siempre, para el desayuno y la once.', 'foto' => '1615585601987-296cdc0f9334', 'modo' => 'kilo', 'precio' => 2390, 'sello' => 'La más pedida'],
        ['id' => 'hallulla', 'nombre' => 'Hallulla', 'desc' => 'Redonda, suave y con su toque de manteca. Tostada con palta no tiene rival.', 'foto' => '1609889132680-4e2ea2befbef', 'modo' => 'kilo', 'precio' => 2390, 'sello' => ''],
        ['id' => 'amasado', 'nombre' => 'Pan amasado', 'desc' => 'Amasado a mano, denso y blandito. Sale caliente en la hornada de la tarde.', 'foto' => '1784492711981-356bfac710e8', 'modo' => 'kilo', 'precio' => 2990, 'sello' => 'Hornada de 18:30'],
        ['id' => 'colisa', 'nombre' => 'Colisa', 'desc' => 'Pan blanco, suave y brillante, que se separa en dos. El favorito de los niños.', 'foto' => '1671036437036-0b1f11c80941', 'modo' => 'kilo', 'precio' => 2590, 'sello' => ''],
        ['id' => 'integral', 'nombre' => 'Integral con semillas', 'pedido' => 'pan integral con semillas', 'desc' => 'Harina integral, linaza y maravilla. Más saciador y igual de rico.', 'foto' => '1626423642268-24cc183cbacb', 'modo' => 'kilo', 'precio' => 3490, 'sello' => ''],
        ['id' => 'molde', 'nombre' => 'Pan de molde casero', 'desc' => 'Molde de 700 g, rebanado en el momento si lo pides. Para los sándwiches del colegio.', 'foto' => '1598373182308-3270495d2f58', 'modo' => 'unidad', 'precio' => 2490, 'sello' => ''],
    ],
    'empanadas' => [
        ['nombre' => 'Pino al horno', 'pedido' => 'empanadas de pino al horno', 'desc' => 'Carne picada a cuchillo, cebolla, huevo, aceituna y pasas (si quieres).', 'foto' => '1624128082323-beb6b8b508db', 'unidad' => 2300, 'docena' => 25900],
        ['nombre' => 'Queso frita', 'pedido' => 'empanadas de queso fritas', 'desc' => 'Masa crocante y queso que se estira. Recién fritas desde las 11:30.', 'foto' => '1769254870299-338bfd99aabd', 'unidad' => 1500, 'docena' => 16900],
        ['nombre' => 'Pino para hornear', 'pedido' => 'empanadas de pino para hornear en casa', 'desc' => 'Te las llevas crudas y las horneas tú: 20 minutos a horno fuerte y salen como recién hechas.', 'foto' => '1624128082309-35000879a89f', 'unidad' => 1990, 'docena' => 21900],
    ],
    'dulces' => [
        ['nombre' => 'Berlín', 'desc' => 'Pasado por azúcar, relleno de crema pastelera o manjar.', 'foto' => '1589199750869-334ae4187406', 'precio' => '$900 c/u', 'mensaje' => 'Hola, quiero encargar 6 berlines (crema o manjar).'],
        ['nombre' => 'Galletones de avena', 'desc' => 'Grandes, con avena y chips de chocolate. Salen del horno a media tarde.', 'foto' => '1608711171015-40b192082efe', 'precio' => '$990 c/u', 'mensaje' => 'Hola, quiero encargar 4 galletones de avena ($3.960).'],
        ['nombre' => 'Trenza de leche', 'desc' => 'Pan dulce de leche y mantequilla, trenzado a mano. Para la once del domingo.', 'foto' => '1767065886426-832b32ce435a', 'precio' => '$3.990 c/u', 'mensaje' => 'Hola, quiero encargar 1 trenza de leche ($3.990).'],
    ],
    // Lista de precios (letrero de letras con QR): [nombre, precio].
    'lista' => [
        'Pan por kilo' => [
            ['Marraqueta', '$2.390'],
            ['Hallulla', '$2.390'],
            ['Colisa', '$2.590'],
            ['Dobladitas', '$2.890'],
            ['Pan amasado', '$2.990'],
            ['Integral con semillas', '$3.490'],
        ],
        'Por unidad' => [
            ['Pan amasado', '$350'],
            ['Pan de molde 700 g', '$2.490'],
            ['Trenza de leche', '$3.990'],
        ],
        'Empanadas' => [
            ['Pino al horno', '$2.300'],
            ['Queso frita', '$1.500'],
            ['Napolitana', '$2.200'],
            ['Pino para hornear', '$1.990'],
            ['Docena de pino', '$25.900'],
        ],
        'Dulces' => [
            ['Berlín', '$900'],
            ['Calzones rotos ¼ kg', '$1.990'],
            ['Chilenitos 6 u.', '$2.990'],
            ['Galletón de avena', '$990'],
        ],
    ],
    'reparto' => [
        'horas' => 'De 7:30 a 10:00 y de 18:30 a 20:30',
        'costo' => 990,
        'gratis_desde' => 8000,
        'minimo' => 3000,
        'cerca' => ['Quinta Normal', 'Lo Prado', 'Santiago (barrio Yungay)'],
    ],
    'resenas' => [
        ['Patricia G.', 5, 'La marraqueta de las siete es otra cosa: crujiente y tibia. Pido por WhatsApp y la paso a buscar camino al metro.', 'hace 1 semana'],
        ['Luis A.', 5, 'Encargué tres docenas de empanadas para un cumpleaños y llegaron calientes y a la hora. Muy amables.', 'hace 3 semanas'],
        ['Carolina M.', 4, 'El pan amasado de la tarde se acaba rápido, conviene encargarlo antes. Las dobladitas son mi debilidad.', 'hace 2 meses'],
    ],
];
