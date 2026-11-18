<?php
declare(strict_types=1);

// Almacén El Quitasol (San Joaquín): negocio ficticio de la versión Simple-page.
// Productos genéricos, sin marcas. Fotos: IDs de Unsplash (ver demos/almacen/CREDITOS.md).
return [
    'nombre' => 'Almacén El Quitasol',
    'nombre_corto' => 'El Quitasol',
    'categoria' => 'Almacén de barrio',
    'comuna' => 'San Joaquín',
    'desde' => 1994,
    'eslogan' => 'El almacén de la esquina, ahora también por WhatsApp',
    'bajada' => 'Pan dos veces al día, verduras de la Vega y todo lo de la despensa. Arma tu lista de la semana aquí y te la llevamos a la casa.',
    'nota' => 4.8,
    'horario' => [
        1 => ['08:30', '21:30'],
        2 => ['08:30', '21:30'],
        3 => ['08:30', '21:30'],
        4 => ['08:30', '21:30'],
        5 => ['08:30', '21:30'],
        6 => ['08:30', '21:30'],
        7 => ['09:30', '14:30'],
    ],

    // Delivery en el barrio.
    'minimo' => 8000,
    'despacho' => 1000,
    'gratis_desde' => 25000,
    'radio' => '10 cuadras a la redonda',
    'bloques' => [
        'manana' => 'Mañana, de 10:00 a 13:00',
        'tarde' => 'Tarde, de 17:00 a 20:30',
    ],
    'pagos' => [
        'efectivo' => 'Efectivo',
        'debito' => 'Débito o crédito (llevamos la máquina)',
        'transferencia' => 'Transferencia',
    ],

    'fotos' => [
        'hero' => '1567131349667-933eb56baec0',
    ],

    // slug => [nombre, foto, nota corta]
    'categorias' => [
        'canastas' => ['Canastas armadas', '1652262968340-b735524f9ae4', 'Listas para pedir de una vez.'],
        'despensa' => ['Despensa', '1592178036182-5400889dfc74', 'Lo que no puede faltar en la cocina.'],
        'pan' => ['Pan del día', '1772758632784-7ccc90aa5328', 'Sale caliente a las 8:00 y a las 18:00.'],
        'lacteos' => ['Lácteos, huevos y fiambres', '1760393339745-18360fc2b6e3', 'Huevos de campo y fiambre cortado al momento.'],
        'verduras' => ['Frutas y verduras', '1582284540020-8acbe03f4924', 'Llegan de la Vega los martes y los viernes.'],
        'bebidas' => ['Bebidas', '1568657624422-1b8713e79461', 'Heladas en el refrigerador.'],
        'aseo' => ['Aseo y limpieza', '1631524254770-03abe3f42a0d', 'Para la casa y la ropa.'],
        'mascotas' => ['Mascotas', '1589924691995-400dc9ecc119', 'Alimento seco en bolsa.'],
    ],

    // id => cat, nombre, formato, precio (CLP), foto. Las canastas llevan 'contiene' [id => cantidad].
    'productos' => [
        'canasta-once' => ['cat' => 'canastas', 'nombre' => 'Canasta de once', 'formato' => 'para 4 personas', 'precio' => 10990, 'foto' => '1652262968340-b735524f9ae4',
            'contiene' => ['amasado' => 1, 'jamon' => 1, 'queso' => 1, 'mantequilla' => 1, 'te' => 1]],
        'canasta-semana' => ['cat' => 'canastas', 'nombre' => 'Canasta de la semana', 'formato' => 'para una familia', 'precio' => 18990, 'foto' => '1594917156256-37703f0c993c',
            'contiene' => ['arroz' => 1, 'fideos' => 2, 'aceite' => 1, 'azucar' => 1, 'leche' => 4, 'huevos' => 1, 'tomates' => 1, 'papas' => 1, 'cebollas' => 1, 'marraqueta' => 1]],
        'canasta-mes' => ['cat' => 'canastas', 'nombre' => 'Despensa del mes', 'formato' => 'para llenar la alacena', 'precio' => 45990, 'foto' => '1567966221845-e91f7d888354',
            'contiene' => ['arroz' => 3, 'aceite' => 2, 'azucar' => 2, 'harina' => 2, 'fideos' => 6, 'lentejas' => 2, 'te' => 1, 'leche' => 12, 'detergente' => 1, 'papel' => 1]],

        'arroz' => ['cat' => 'despensa', 'nombre' => 'Arroz grado 1', 'formato' => '1 kg', 'precio' => 1390, 'foto' => '1586201375761-83865001e31c'],
        'aceite' => ['cat' => 'despensa', 'nombre' => 'Aceite vegetal', 'formato' => '1 L', 'precio' => 2290, 'foto' => '1720468750623-39e9a09f5067'],
        'azucar' => ['cat' => 'despensa', 'nombre' => 'Azúcar', 'formato' => '1 kg', 'precio' => 1190, 'foto' => '1770732851655-a789335a31c8'],
        'harina' => ['cat' => 'despensa', 'nombre' => 'Harina sin polvos de hornear', 'formato' => '1 kg', 'precio' => 1090, 'foto' => '1610725664285-7c57e6eeac3f'],
        'fideos' => ['cat' => 'despensa', 'nombre' => 'Fideos spaghetti', 'formato' => '400 g', 'precio' => 890, 'foto' => '1587287720536-ce7b84c23d35'],
        'lentejas' => ['cat' => 'despensa', 'nombre' => 'Lentejas', 'formato' => '1 kg', 'precio' => 2190, 'foto' => '1763368392508-3d4bddfdd20a'],
        'te' => ['cat' => 'despensa', 'nombre' => 'Té negro', 'formato' => '100 bolsitas', 'precio' => 2490, 'foto' => '1585849835117-1446a1514ea6'],

        'marraqueta' => ['cat' => 'pan', 'nombre' => 'Marraqueta', 'formato' => '1 kg', 'precio' => 2400, 'foto' => '1772758632784-7ccc90aa5328'],
        'amasado' => ['cat' => 'pan', 'nombre' => 'Pan amasado', 'formato' => 'bolsa de 6', 'precio' => 1800, 'foto' => '1776099536340-780ba11e159d'],

        'leche' => ['cat' => 'lacteos', 'nombre' => 'Leche entera', 'formato' => '1 L', 'precio' => 1090, 'foto' => '1550583724-b2692b85b150'],
        'huevos' => ['cat' => 'lacteos', 'nombre' => 'Huevos de gallina', 'formato' => '12 un.', 'precio' => 3290, 'foto' => '1760393339745-18360fc2b6e3'],
        'queso' => ['cat' => 'lacteos', 'nombre' => 'Queso laminado', 'formato' => '250 g', 'precio' => 2990, 'foto' => '1655662844300-e59c3d2e7587'],
        'jamon' => ['cat' => 'lacteos', 'nombre' => 'Jamón de pierna', 'formato' => '250 g', 'precio' => 2790, 'foto' => '1609518317991-10acee259279'],
        'mantequilla' => ['cat' => 'lacteos', 'nombre' => 'Mantequilla', 'formato' => '250 g', 'precio' => 2590, 'foto' => '1590912710024-6d51a6771abd'],
        'yogur' => ['cat' => 'lacteos', 'nombre' => 'Yogur natural', 'formato' => '1 kg', 'precio' => 1990, 'foto' => '1571212515416-fef01fc43637'],

        'tomates' => ['cat' => 'verduras', 'nombre' => 'Tomates', 'formato' => '1 kg', 'precio' => 1490, 'foto' => '1582284540020-8acbe03f4924'],
        'paltas' => ['cat' => 'verduras', 'nombre' => 'Paltas hass', 'formato' => '1 kg', 'precio' => 3990, 'foto' => '1573566291259-fd494a326b60'],
        'papas' => ['cat' => 'verduras', 'nombre' => 'Papas', 'formato' => 'malla de 2 kg', 'precio' => 1990, 'foto' => '1518977676601-b53f82aba655'],
        'cebollas' => ['cat' => 'verduras', 'nombre' => 'Cebollas', 'formato' => '1 kg', 'precio' => 990, 'foto' => '1580201092675-a0a6a6cafbb1'],
        'limones' => ['cat' => 'verduras', 'nombre' => 'Limones', 'formato' => '1 kg', 'precio' => 1690, 'foto' => '1590502593747-42a996133562'],
        'platanos' => ['cat' => 'verduras', 'nombre' => 'Plátanos', 'formato' => '1 kg', 'precio' => 1290, 'foto' => '1603833665858-e61d17a86224'],

        'cola' => ['cat' => 'bebidas', 'nombre' => 'Bebida cola', 'formato' => '3 L', 'precio' => 2590, 'foto' => '1568657624422-1b8713e79461'],
        'jugo' => ['cat' => 'bebidas', 'nombre' => 'Jugo de naranja', 'formato' => '1,5 L', 'precio' => 1890, 'foto' => '1621506289937-a8e4df240d0b'],
        'agua' => ['cat' => 'bebidas', 'nombre' => 'Agua mineral sin gas', 'formato' => '1,6 L', 'precio' => 990, 'foto' => '1616118132534-381148898bb4'],

        'detergente' => ['cat' => 'aseo', 'nombre' => 'Detergente líquido', 'formato' => '3 L', 'precio' => 6990, 'foto' => '1649005011845-ef225c89da86'],
        'papel' => ['cat' => 'aseo', 'nombre' => 'Papel higiénico doble hoja', 'formato' => '12 rollos', 'precio' => 5990, 'foto' => '1631524254770-03abe3f42a0d'],
        'lavaloza' => ['cat' => 'aseo', 'nombre' => 'Lavaloza', 'formato' => '750 ml', 'precio' => 1690, 'foto' => '1618038483079-bfe64dcb17f1'],
        'limpiador' => ['cat' => 'aseo', 'nombre' => 'Limpiador multiuso', 'formato' => '900 ml', 'precio' => 1990, 'foto' => '1563453392212-326f5e854473'],

        'perro' => ['cat' => 'mascotas', 'nombre' => 'Alimento para perro adulto', 'formato' => '3 kg', 'precio' => 7990, 'foto' => '1589924691995-400dc9ecc119'],
        'gato' => ['cat' => 'mascotas', 'nombre' => 'Alimento para gato adulto', 'formato' => '1,5 kg', 'precio' => 5490, 'foto' => '1616668983570-a971956d8928'],
    ],

    // Oferta de cada día de la semana (1 = lunes ... 7 = domingo): id => precio del día.
    'ofertas' => [
        1 => ['Lunes de verduras', ['tomates' => 990, 'paltas' => 2990, 'papas' => 1490]],
        2 => ['Martes de lácteos', ['leche' => 890, 'yogur' => 1590, 'queso' => 2490]],
        3 => ['Miércoles de despensa', ['arroz' => 1090, 'aceite' => 1890, 'fideos' => 690]],
        4 => ['Jueves de aseo', ['detergente' => 5490, 'papel' => 4990, 'lavaloza' => 1290]],
        5 => ['Viernes de once', ['amasado' => 1500, 'jamon' => 2290, 'mantequilla' => 2190]],
        6 => ['Sábado de bebidas', ['cola' => 2190, 'jugo' => 1490, 'agua' => 790]],
        7 => ['Domingo de desayuno', ['huevos' => 2890, 'marraqueta' => 2100, 'te' => 1990]],
    ],

    'resenas' => [
        ['Patricia M.', 5, 'Mando la lista por WhatsApp el lunes en la mañana y a mediodía la tengo en la casa. El pan amasado de la tarde es lo mejor del barrio.', 'hace 5 días'],
        ['Jorge A.', 5, 'Almacén de los de antes, pero con delivery. Siempre tienen de todo y la señora Gladys se acuerda de lo que llevo cada semana.', 'hace 3 semanas'],
        ['Camila R.', 4, 'Las ofertas del día valen la pena, sobre todo el lunes de verduras. A la hora de once se llena: conviene pedir antes.', 'hace 1 mes'],
    ],
];
