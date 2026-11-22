<?php
declare(strict_types=1);

// Turno 24 (Santiago): negocio ficticio de la versión Multi-page. Minimarket abierto las 24 horas.
// Productos genéricos, sin marcas. Fotos: IDs de Unsplash (ver demos/almacen/CREDITOS.md).
$todoElDia = ['00:00', '24:00'];

return [
    'nombre' => 'Turno 24',
    'categoria' => 'Minimarket 24 horas',
    'comuna' => 'Santiago',
    'barrio' => 'Santiago Centro',
    'eslogan' => 'Minimarket abierto las 24 horas, todos los días',
    'nota' => 4.6,
    // Abierto las 24 horas: ['00:00', '24:00'] (Google dice «Abierto las 24 horas» y la tabla, «24 horas»).
    'horario' => array_fill(1, 7, $todoElDia),
    'despacho_horas' => ['08:00', '02:00'],
    'despacho_minutos' => 35,
    'minimo_despacho' => 10000,
    'comunas_despacho' => [
        'Santiago' => 1490,
        'Providencia' => 1990,
        'Independencia' => 2290,
        'Recoleta' => 2290,
        'Estación Central' => 2490,
        'Ñuñoa' => 2490,
    ],
    'resenas' => [
        ['Ignacio T.', 5, 'Se me acabó el hielo a la 1 de la mañana y en 30 minutos lo tenía en la puerta. Precios iguales que en el local.', 'hace 4 días'],
        ['Valentina C.', 5, 'La web es rapidísima: busco, agrego y mando el pedido por WhatsApp. Me avisan si falta algo antes de reemplazarlo.', 'hace 2 semanas'],
        ['Rodrigo P.', 4, 'Muy ordenado y siempre abierto. A la hora de salida de las oficinas hay fila, pero avanza rápido.', 'hace 1 mes'],
    ],
    'fotos' => [
        'hero' => '1583932644465-85803c7ed8ae',
        'verduras' => '1542838132-92c53300491e',
        'despensa' => '1592178036182-5400889dfc74',
        'frutas' => '1583258292688-d0213dc5a3a8',
    ],

    // slug => [número de pasillo, nombre, descripción, ícono SVG (trazos 24×24)]
    'pasillos' => [
        'despensa' => ['01', 'Despensa', 'Arroz, aceite, fideos, legumbres, café y té.', '<path d="M5 9h14l-1 11H6z"/><path d="M8 9V6a4 4 0 0 1 8 0v3"/>'],
        'lacteos' => ['02', 'Lácteos y huevos', 'Leche, yogur, mantequilla y huevos de campo.', '<path d="M8 3h8v3l2 3v12H6V9l2-3z"/><path d="M6 12h12"/>'],
        'pan' => ['03', 'Pan y fiambres', 'Pan horneado cada cuatro horas, queso y jamón.', '<path d="M4 13c0-4 3.6-7 8-7s8 3 8 7v5H4z"/><path d="M9 9.5 10 13M14 9.5 13 13"/>'],
        'frutas' => ['04', 'Frutas y verduras', 'Paltas, tomates, plátanos y lo de la ensalada.', '<path d="M12 7c4 0 7 3 7 7s-3 7-7 7-7-3-7-7 3-7 7-7z"/><path d="M12 7c0-2 1-3.5 3-4"/>'],
        'bebidas' => ['05', 'Bebidas y hielo', 'Bebidas, agua, jugos y hielo para la noche.', '<path d="M9 3h6M10 3v4l-2 3v11h8V10l-2-3V3"/><path d="M8 14h8"/>'],
        'picoteo' => ['06', 'Snacks y dulces', 'Cabritas, papas fritas, chocolate y galletas.', '<path d="M6 8h12l-1.5 13h-9z"/><path d="M8 8l1-4h6l1 4"/>'],
        'aseo' => ['07', 'Aseo y hogar', 'Papel, detergente, lavaloza y limpiadores.', '<path d="M9 3h5v4H9z"/><path d="M8 7h7l2 4v10H7V11z"/><path d="M14 3h3"/>'],
        'mascotas' => ['08', 'Mascotas', 'Alimento seco para perros y gatos.', '<circle cx="7" cy="9" r="2"/><circle cx="12" cy="6" r="2"/><circle cx="17" cy="9" r="2"/><path d="M8 17c0-3 2-5 4-5s4 2 4 5c0 2-2 3-4 3s-4-1-4-3z"/>'],
    ],

    // id => pasillo, nombre, desc, foto, variantes [nombre => precio], antes (precio normal de la primera variante, si está en oferta)
    'productos' => [
        'arroz' => ['cat' => 'despensa', 'nombre' => 'Arroz grado 1', 'desc' => 'Grano largo, para todos los días.', 'foto' => '1586201375761-83865001e31c', 'variantes' => ['1 kg' => 1490, '5 kg' => 6990]],
        'aceite' => ['cat' => 'despensa', 'nombre' => 'Aceite vegetal', 'desc' => 'Para freír y aliñar.', 'foto' => '/demos/_comun/fotos3d/aceite.webp', 'variantes' => ['1 L' => 2490, '5 L' => 10990], 'modelo' => 'aceite'],
        'fideos' => ['cat' => 'despensa', 'nombre' => 'Fideos spaghetti', 'desc' => 'Paquete de 400 g, cocción en 9 minutos.', 'foto' => '1587287720536-ce7b84c23d35', 'variantes' => ['400 g' => 990], 'antes' => 1190],
        'lentejas' => ['cat' => 'despensa', 'nombre' => 'Lentejas', 'desc' => 'Lentejas pardinas, bolsa de 1 kg.', 'foto' => '1763368392508-3d4bddfdd20a', 'variantes' => ['1 kg' => 2290]],
        'porotos' => ['cat' => 'despensa', 'nombre' => 'Porotos tórtola', 'desc' => 'Para porotos con riendas o granados.', 'foto' => '1599249478506-ae175f801b1e', 'variantes' => ['1 kg' => 2690]],
        'cafe' => ['cat' => 'despensa', 'nombre' => 'Café molido', 'desc' => 'Tueste medio, para cafetera o prensa.', 'foto' => '1510776537653-6d0da167186c', 'variantes' => ['250 g' => 3990, '500 g' => 6990]],
        'te' => ['cat' => 'despensa', 'nombre' => 'Té negro', 'desc' => 'En bolsitas.', 'foto' => '1585849835117-1446a1514ea6', 'variantes' => ['20 bolsitas' => 990, '100 bolsitas' => 2590]],
        'azucar' => ['cat' => 'despensa', 'nombre' => 'Azúcar', 'desc' => 'Granulada, bolsa de 1 kg.', 'foto' => '1770732851655-a789335a31c8', 'variantes' => ['1 kg' => 1290]],

        'leche' => ['cat' => 'lacteos', 'nombre' => 'Leche entera', 'desc' => 'Larga vida, en caja.', 'foto' => '/demos/_comun/fotos3d/leche.webp', 'variantes' => ['1 L' => 1190, 'Pack de 6' => 6490], 'modelo' => 'leche'],
        'huevos' => ['cat' => 'lacteos', 'nombre' => 'Huevos de gallina', 'desc' => 'Huevos blancos de campo.', 'foto' => '/demos/_comun/fotos3d/huevos.webp', 'variantes' => ['6 un.' => 1890, '12 un.' => 3490, '30 un.' => 7990], 'modelo' => 'huevos'],
        'yogur' => ['cat' => 'lacteos', 'nombre' => 'Yogur natural', 'desc' => 'Sin azúcar, pote de 1 kg.', 'foto' => '1571212515416-fef01fc43637', 'variantes' => ['1 kg' => 2190]],
        'mantequilla' => ['cat' => 'lacteos', 'nombre' => 'Mantequilla', 'desc' => 'Con sal, pan de 250 g.', 'foto' => '1590912710024-6d51a6771abd', 'variantes' => ['250 g' => 2790]],

        'marraqueta' => ['cat' => 'pan', 'nombre' => 'Marraqueta', 'desc' => 'Sale del horno a las 7:00, 11:00, 15:00, 19:00 y 23:00.', 'foto' => '1772758632784-7ccc90aa5328', 'variantes' => ['½ kg' => 1350, '1 kg' => 2600]],
        'amasado' => ['cat' => 'pan', 'nombre' => 'Pan amasado', 'desc' => 'Bolsa de 6, de la hornada de la tarde.', 'foto' => '1776099536340-780ba11e159d', 'variantes' => ['Bolsa de 6' => 1990]],
        'queso' => ['cat' => 'pan', 'nombre' => 'Queso gauda laminado', 'desc' => 'Cortado en el día.', 'foto' => '1655662844300-e59c3d2e7587', 'variantes' => ['250 g' => 3190]],
        'jamon' => ['cat' => 'pan', 'nombre' => 'Jamón de pierna', 'desc' => 'Cortado en el día.', 'foto' => '1609518317991-10acee259279', 'variantes' => ['250 g' => 2990]],

        'paltas' => ['cat' => 'frutas', 'nombre' => 'Paltas hass', 'desc' => 'Listas para comer o para mañana: dinos en la nota.', 'foto' => '1573566291259-fd494a326b60', 'variantes' => ['1 kg' => 4290], 'antes' => 4990],
        'tomates' => ['cat' => 'frutas', 'nombre' => 'Tomates', 'desc' => 'Para la ensalada a la chilena.', 'foto' => '1582284540020-8acbe03f4924', 'variantes' => ['1 kg' => 1690]],
        'platanos' => ['cat' => 'frutas', 'nombre' => 'Plátanos', 'desc' => 'Maduros, por kilo.', 'foto' => '1603833665858-e61d17a86224', 'variantes' => ['1 kg' => 1390]],
        'manzanas' => ['cat' => 'frutas', 'nombre' => 'Manzanas rojas', 'desc' => 'Crujientes, por kilo.', 'foto' => '1560806887-1e4cd0b6cbd6', 'variantes' => ['1 kg' => 1590]],
        'limones' => ['cat' => 'frutas', 'nombre' => 'Limones', 'desc' => 'Para el jugo o el aliño.', 'foto' => '1590502593747-42a996133562', 'variantes' => ['½ kg' => 990, '1 kg' => 1790]],

        'cola' => ['cat' => 'bebidas', 'nombre' => 'Bebida cola', 'desc' => 'Helada o al tiempo, como prefieras.', 'foto' => '/demos/_comun/fotos3d/bebida.webp', 'variantes' => ['1,5 L' => 1890, '3 L' => 2790], 'antes' => 2190, 'modelo' => 'bebida'],
        'agua' => ['cat' => 'bebidas', 'nombre' => 'Agua mineral', 'desc' => 'Botella de 1,6 L.', 'foto' => '1616118132534-381148898bb4', 'variantes' => ['Sin gas' => 1090, 'Con gas' => 1090]],
        'jugo' => ['cat' => 'bebidas', 'nombre' => 'Jugo de naranja', 'desc' => 'Néctar de naranja, 1,5 L.', 'foto' => '1621506289937-a8e4df240d0b', 'variantes' => ['1,5 L' => 1990]],
        'hielo' => ['cat' => 'bebidas', 'nombre' => 'Hielo en cubos', 'desc' => 'Bolsa de 2 kg, del congelador al despacho.', 'foto' => '1590430752967-d0e116909be1', 'variantes' => ['2 kg' => 1990]],

        'cabritas' => ['cat' => 'picoteo', 'nombre' => 'Cabritas', 'desc' => 'Dulces o saladas, bolsa de 100 g.', 'foto' => '1707063017149-a3cd268776c4', 'variantes' => ['Saladas' => 1290, 'Dulces' => 1290]],
        'papas-fritas' => ['cat' => 'picoteo', 'nombre' => 'Papas fritas onduladas', 'desc' => 'Bolsa de 180 g.', 'foto' => '1613919113640-25732ec5e61f', 'variantes' => ['180 g' => 2290]],
        'chocolate' => ['cat' => 'picoteo', 'nombre' => 'Chocolate de leche', 'desc' => 'Barra de 100 g.', 'foto' => '1623660053975-cf75a8be0908', 'variantes' => ['100 g' => 1490]],
        'galletas' => ['cat' => 'picoteo', 'nombre' => 'Galletas con chips de chocolate', 'desc' => 'Paquete de 200 g.', 'foto' => '1657418830273-40c19cfff4d7', 'variantes' => ['200 g' => 1490], 'antes' => 1790],
        'frutos-secos' => ['cat' => 'picoteo', 'nombre' => 'Mix de frutos secos', 'desc' => 'Nueces, almendras, maní y pasas.', 'foto' => '1543158181-1274e5362710', 'variantes' => ['200 g' => 3490]],

        'papel' => ['cat' => 'aseo', 'nombre' => 'Papel higiénico doble hoja', 'desc' => 'Rollos de 30 metros.', 'foto' => '1631524254770-03abe3f42a0d', 'variantes' => ['4 rollos' => 2290, '12 rollos' => 6290]],
        'detergente' => ['cat' => 'aseo', 'nombre' => 'Detergente líquido', 'desc' => 'Para ropa blanca y de color, 3 L.', 'foto' => '1649005011845-ef225c89da86', 'variantes' => ['3 L' => 7490], 'antes' => 8990],
        'lavaloza' => ['cat' => 'aseo', 'nombre' => 'Lavaloza concentrado', 'desc' => 'Botella de 750 ml.', 'foto' => '1618038483079-bfe64dcb17f1', 'variantes' => ['750 ml' => 1790]],
        'limpiador' => ['cat' => 'aseo', 'nombre' => 'Limpiador multiuso', 'desc' => 'Con gatillo, 900 ml.', 'foto' => '1563453392212-326f5e854473', 'variantes' => ['900 ml' => 2190]],
        'desengrasante' => ['cat' => 'aseo', 'nombre' => 'Desengrasante de cocina', 'desc' => 'Aroma limón, 1 L.', 'foto' => '1583907659441-addbe699e921', 'variantes' => ['1 L' => 2490]],

        'perro' => ['cat' => 'mascotas', 'nombre' => 'Alimento para perro adulto', 'desc' => 'Croquetas para razas medianas.', 'foto' => '1764249453874-46864677b10e', 'variantes' => ['1 kg' => 2990, '3 kg' => 8490]],
        'gato' => ['cat' => 'mascotas', 'nombre' => 'Alimento para gato adulto', 'desc' => 'Croquetas con pescado.', 'foto' => '1616668983570-a971956d8928', 'variantes' => ['1 kg' => 3490]],
    ],

    // Lo que más se lleva de noche (portada).
    'de_noche' => ['hielo', 'cola', 'cabritas', 'marraqueta'],

    'preguntas' => [
        ['¿De verdad abren las 24 horas?', 'Sí, todos los días del año, también domingos y feriados. Trabajamos en tres turnos y la caja nunca cierra.'],
        ['¿Hasta qué hora despachan?', 'Despachamos de 8:00 a 2:00 en Santiago, Providencia, Independencia, Recoleta, Estación Central y Ñuñoa. De 2:00 a 8:00 puedes armar tu pedido y retirarlo en el local.'],
        ['¿Hay pedido mínimo?', 'Para el despacho, el mínimo es $10.000. Para retiro en el local no hay mínimo.'],
        ['¿Cuánto se demora el despacho?', 'Unos 35 minutos desde que te confirmamos por WhatsApp. En horas punta (de 19:00 a 21:00) puede tardar un poco más; te avisamos.'],
        ['¿Qué pasa si falta un producto?', 'En el pedido eliges qué hacemos: reemplazarlo por uno parecido, llamarte antes o no reemplazarlo. Nunca cobramos algo que no llevamos.'],
        ['¿Cómo pago?', 'Al recibir, con efectivo, débito, crédito o transferencia. El repartidor lleva máquina para tarjetas.'],
        ['¿Venden alcohol o cigarrillos?', 'No. Somos un minimarket de abarrotes, aseo y comida para el día a día.'],
        ['¿Me dan boleta?', 'Sí, siempre. Si necesitas factura para tu empresa, escríbelo en la nota del pedido.'],
    ],
];
