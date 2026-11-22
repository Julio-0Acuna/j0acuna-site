<?php
declare(strict_types=1);

// Doble Palta (Recoleta): negocio ficticio de la versión Multi-page. Sanguchería y fuente de soda.
// Fotos: IDs de Unsplash (ver demos/variada/CREDITOS.md). Modelos 3D: demos/_comun/3d/<modelo>.glb.
return [
    'nombre' => 'Doble Palta',
    'categoria' => 'Sanguchería y fuente de soda',
    'comuna' => 'Recoleta',
    'eslogan' => 'Sánguches a la plancha con palta de verdad',
    'nota' => 4.8,
    'horario' => [
        1 => ['12:00', '23:00'],
        2 => ['12:00', '23:00'],
        3 => ['12:00', '23:00'],
        4 => ['12:00', '23:00'],
        5 => ['12:00', '01:00'],
        6 => ['12:00', '01:00'],
        7 => ['13:00', '21:00'],
    ],
    'resenas' => [
        ['Ignacia M.', 5, 'El churrasco italiano tiene palta de verdad, molida en el momento, y el pan llega tostado. La chorrillana para dos alcanza para tres.', 'hace 6 días'],
        ['Rodrigo F.', 5, 'Armé el pedido en la página y llegó al WhatsApp con todo escrito: sin errores ni llamadas. La Doble Palta doble, brutal.', 'hace 2 semanas'],
        ['Carla P.', 4, 'Muy buenos los jugos naturales. Los viernes en la noche hay que esperar un rato por mesa.', 'hace 1 mes'],
    ],
    'comunas_despacho' => [
        'Recoleta' => 1490,
        'Independencia' => 1990,
        'Santiago' => 2490,
        'Providencia' => 2490,
        'Conchalí' => 2490,
        'Huechuraba' => 2990,
    ],
    // Los estilos de la casa (cómo se arma cada sánguche).
    'estilos' => [
        'Italiano' => ['Tomate, palta y mayo casera.', 'italiano'],
        'Chacarero' => ['Porotos verdes, tomate, ají verde y mayo.', 'chacarero'],
        'Luco' => ['Queso derretido sobre la carne, nada más.', 'luco'],
        'Dinámico' => ['Tomate, palta, chucrut, americana y mayo.', 'dinamico'],
    ],
    'categorias' => [
        'sanguches' => ['Sánguches a la plancha', 'En marraqueta o frica recién tostada. Tú eliges el estilo.'],
        'hamburguesas' => ['Hamburguesas', 'Carne molida cada mañana y aplastada en la plancha. Gíralas en 3D.'],
        'completos' => ['Completos y as', 'Pan de completo del día y palta molida a mano cada hora.'],
        'compartir' => ['Para compartir', 'Papas cortadas a mano y fritas dos veces.'],
        'bebidas' => ['Jugos y batidos', 'Fruta natural licuada al momento.'],
    ],
    // id => datos. variantes: [nombre => precio]. modelo: nombre del .glb (vacío = sin 3D). num: número en la carta.
    'platos' => [
        'churrasco' => ['cat' => 'sanguches', 'num' => '01', 'nombre' => 'Churrasco', 'desc' => 'Posta laminada a la plancha, en marraqueta o frica.', 'foto' => '1702119614788-bae35a7be313', 'variantes' => ['Italiano' => 7490, 'Chacarero' => 7790, 'Luco' => 6990, 'Palta doble' => 7990], 'modelo' => '', 'firma' => true],
        'lomito' => ['cat' => 'sanguches', 'num' => '02', 'nombre' => 'Lomito', 'desc' => 'Lomo de cerdo laminado y jugoso, como en las fuentes de soda de antes.', 'foto' => '1567529855370-57ecd066454c', 'variantes' => ['Italiano' => 6990, 'Completo' => 6990, 'Chacarero' => 7290, 'Luco' => 6490], 'modelo' => '', 'firma' => false],
        'mechada' => ['cat' => 'sanguches', 'num' => '03', 'nombre' => 'Mechada', 'desc' => 'Carne mechada cocida seis horas en su jugo.', 'foto' => '1667055251919-88fd6ff02e86', 'variantes' => ['Luco' => 7290, 'Italiana' => 7690], 'modelo' => '', 'firma' => false],
        'ave' => ['cat' => 'sanguches', 'num' => '04', 'nombre' => 'Ave', 'desc' => 'Pechuga de pollo a la plancha con verduras de la feria.', 'foto' => '1615653633266-1ee0c84500b2', 'variantes' => ['Palta mayo' => 6290, 'Italiano' => 6490, 'Chacarero' => 6790], 'modelo' => '', 'firma' => false],
        'barros-jarpa' => ['cat' => 'sanguches', 'num' => '05', 'nombre' => 'Barros Jarpa', 'desc' => 'Jamón y queso fundido en marraqueta tostada.', 'foto' => '1716834092510-3be5db563920', 'variantes' => ['Única' => 4990], 'modelo' => '', 'firma' => false],

        'doble-palta' => ['cat' => 'hamburguesas', 'num' => '06', 'nombre' => 'La Doble Palta', 'desc' => 'Smash, queso cheddar, palta molida, tomate y mayo de la casa.', 'foto' => '1568901346375-23c9450c58cd', 'variantes' => ['Simple' => 7990, 'Doble carne' => 9990], 'modelo' => '', 'firma' => true],
        'chacarera' => ['cat' => 'hamburguesas', 'num' => '07', 'nombre' => 'Chacarera', 'desc' => 'Smash, porotos verdes, tomate, ají verde y queso.', 'foto' => '1607013251379-e6eecfffe234', 'variantes' => ['Simple' => 8290, 'Doble carne' => 10290], 'modelo' => '', 'firma' => false],
        'tocino' => ['cat' => 'hamburguesas', 'num' => '08', 'nombre' => 'Tocino y cebolla', 'desc' => 'Doble smash, tocino crocante, cebolla morada y cheddar.', 'foto' => '/demos/_comun/fotos3d/hamburguesa-tocino-cebolla.webp', 'variantes' => ['Simple' => 8490, 'Doble carne' => 10490], 'modelo' => 'hamburguesa-tocino-cebolla', 'firma' => false],
        'garbanzo' => ['cat' => 'hamburguesas', 'num' => '09', 'nombre' => 'Garbanzo', 'desc' => 'Hamburguesa de garbanzos y quinoa, palta, tomate y lechuga. Vegetariana.', 'foto' => '', 'variantes' => ['Única' => 7490], 'modelo' => '', 'firma' => false],

        'completo' => ['cat' => 'completos', 'num' => '10', 'nombre' => 'Completo', 'desc' => 'Vienesa de cerdo en pan de completo tibio.', 'foto' => '1678033391562-6ff8c834d1d5', 'variantes' => ['Italiano' => 3290, 'Completo' => 3290, 'Dinámico' => 3690], 'modelo' => '', 'firma' => true],
        'as' => ['cat' => 'completos', 'num' => '11', 'nombre' => 'As', 'desc' => 'Carne laminada a la plancha en pan de completo.', 'foto' => '1699728088600-6d684acbeada', 'variantes' => ['Italiano' => 5490, 'Chacarero' => 5790], 'modelo' => '', 'firma' => false],

        'chorrillana' => ['cat' => 'compartir', 'num' => '12', 'nombre' => 'Chorrillana', 'desc' => 'Papas fritas, churrasco, cebolla salteada y dos huevos fritos.', 'foto' => '1743193711514-4f7bc5d78d4d', 'variantes' => ['Para 2' => 11990, 'Para 4' => 18990], 'modelo' => '', 'firma' => false],
        'papas' => ['cat' => 'compartir', 'num' => '13', 'nombre' => 'Papas fritas', 'desc' => 'Cortadas a mano, con mayo de ajo.', 'foto' => '1630384060421-cb20d0e0649d', 'variantes' => ['Chicas' => 2990, 'Grandes' => 4490], 'modelo' => '', 'firma' => false],
        'empanaditas' => ['cat' => 'compartir', 'num' => '14', 'nombre' => 'Empanaditas de queso', 'desc' => 'Seis unidades, fritas al momento, con pebre.', 'foto' => '', 'variantes' => ['Única' => 3990], 'modelo' => '', 'firma' => false],

        'jugo' => ['cat' => 'bebidas', 'num' => '15', 'nombre' => 'Jugo natural', 'desc' => 'Fruta de estación licuada al momento. 500 cc.', 'foto' => '1600271886742-f049cd451bba', 'variantes' => ['Frutilla' => 3290, 'Mango' => 3290, 'Piña' => 3290, 'Frambuesa' => 3490], 'modelo' => '', 'firma' => false],
        'batido' => ['cat' => 'bebidas', 'num' => '16', 'nombre' => 'Batido', 'desc' => 'Leche, helado de vainilla y fruta. 400 cc.', 'foto' => '1611928237590-087afc90c6fd', 'variantes' => ['Frutilla' => 3990, 'Plátano' => 3990, 'Lúcuma' => 4290], 'modelo' => '', 'firma' => false],
        'limonada' => ['cat' => 'bebidas', 'num' => '17', 'nombre' => 'Limonada menta jengibre', 'desc' => 'Limón exprimido, menta y jengibre. 500 cc.', 'foto' => '', 'variantes' => ['Única' => 2990], 'modelo' => '', 'firma' => false],
        'bebida' => ['cat' => 'bebidas', 'num' => '18', 'nombre' => 'Bebida en lata', 'desc' => 'Varios sabores, 350 cc.', 'foto' => '', 'variantes' => ['Única' => 1490], 'modelo' => '', 'firma' => false],
    ],
    'fotos' => [
        'hero' => '1761076879115-97f22dc68755',
        'plancha' => '1592771584685-c3c2edcd5c31',
        'smash' => '1644447295447-31af467e866a',
        'barra' => '1794433981801-680b57055fb4',
        'palta' => '1612215047504-a6c07dbe4f7f',
    ],
    'preguntas' => [
        ['¿Hacen despacho a domicilio?', 'Sí, en Recoleta, Independencia, Santiago, Providencia, Conchalí y Huechuraba. Arma tu pedido en la carta y se envía por WhatsApp con el costo de despacho de tu comuna.'],
        ['¿Puedo retirar en el local?', 'Claro. Elige «Retiro en el local» y la hora. Un churrasco sale de la plancha en unos 12 minutos.'],
        ['¿La palta es de verdad?', 'Siempre. Molemos palta Hass cada hora con un poco de sal y limón; nunca usamos pasta envasada. Por eso nos llamamos así.'],
        ['¿En qué pan vienen los sánguches?', 'En marraqueta o frica recién tostada, de una panadería de Recoleta. En el pedido puedes elegir el pan.'],
        ['¿Tienen opciones vegetarianas?', 'Sí: la hamburguesa de garbanzos, las empanaditas de queso, las papas fritas y todos los jugos y batidos.'],
        ['¿Qué es «Ver en 3D»?', 'En la carta puedes girar las hamburguesas en 3D y, desde el celular, ponerlas sobre tu mesa con la cámara antes de pedir.'],
        ['¿Cómo pago?', 'En el local, efectivo, débito o crédito. En los despachos pagas al recibir o por transferencia.'],
    ],
];
