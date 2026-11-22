<?php
declare(strict_types=1);

// Veta Carnes Maduradas (Las Condes): negocio ficticio de la versión Multi-page.
// Precios en CLP. Los cortes por peso llevan 'kilo' (precio por kg) y 'pesos' (gramos a elegir);
// el resto lleva 'variantes' [nombre => precio]. Fotos: IDs de Unsplash (ver demos/carniceria/CREDITOS.md).
// Modelo 3D: demos/_comun/3d/carne.glb (todavía no existe; se muestra la foto con "Modelo 3D en preparación").
return [
    'nombre' => 'Veta Carnes Maduradas',
    'nombre_corto' => 'Veta',
    'categoria' => 'Carnicería de cortes madurados',
    'comuna' => 'Las Condes',
    'eslogan' => 'Carne madurada en seco, de 21 a 60 días, cortada a pedido',
    'nota' => 4.9,
    'horario' => [
        1 => null,
        2 => ['10:00', '20:00'],
        3 => ['10:00', '20:00'],
        4 => ['10:00', '20:00'],
        5 => ['10:00', '20:30'],
        6 => ['10:00', '20:30'],
        7 => ['10:00', '15:00'],
    ],
    'resenas' => [
        ['Ignacio V.', 5, 'El lomo vetado de 28 días cambió mi forma de hacer asado. Te explican el punto y el grosor ideal para tu parrilla.', 'hace 5 días'],
        ['Carolina M.', 5, 'Pedimos la caja para 8 por la web y llegó al día siguiente, todo envasado al vacío y con las etiquetas de días de maduración.', 'hace 2 semanas'],
        ['Felipe S.', 4, 'Precios de boutique, pero se nota en el sabor. El T-bone de 45 días es para ocasiones especiales.', 'hace 1 mes'],
    ],
    'comunas_despacho' => [
        'Las Condes' => 2990,
        'Vitacura' => 2990,
        'Lo Barnechea' => 3990,
        'Providencia' => 3490,
        'La Reina' => 3990,
        'Ñuñoa' => 4490,
    ],
    'usos' => ['parrilla' => 'Parrilla', 'sarten' => 'Sartén', 'horno' => 'Horno', 'olla' => 'Olla'],
    'categorias' => [
        'madurados' => ['Madurados en seco', 'De 21 a 60 días en nuestra cámara a 2 °C. Cortados al grosor que pidas.'],
        'parrilla' => ['Para la parrilla', 'Cortes de fuego lento y de fuego vivo.'],
        'horno' => ['Horno y olla', 'Piezas para el domingo largo.'],
        'casa' => ['De la casa', 'Hechos aquí con los recortes de nuestros madurados.'],
        'cajas' => ['Cajas parrilleras', 'El asado resuelto: cortes, embutidos y carbón en una caja.'],
        'despensa' => ['Despensa', 'Lo que falta junto a la parrilla.'],
    ],
    // id => datos. dias: días de maduración (0 = fresco). usos: claves de 'usos'. modelo: nombre del .glb ('' = sin 3D).
    'cortes' => [
        'lomo-vetado-28' => ['cat' => 'madurados', 'nombre' => 'Lomo vetado 28 días', 'desc' => 'Bife ancho con veta marcada. Notas de nuez y mantequilla; costra perfecta a fuego alto.', 'dias' => 28, 'kilo' => 47900, 'pesos' => [400, 800, 1200], 'usos' => ['parrilla', 'sarten'], 'foto' => '/demos/_comun/fotos3d/carne-oscuro.webp', 'modelo' => 'carne', 'firma' => true],
        'tomahawk-35' => ['cat' => 'madurados', 'nombre' => 'Tomahawk 35 días', 'desc' => 'Lomo vetado con el hueso de la costilla entero. Para compartir entre dos o tres.', 'dias' => 35, 'kilo' => 52900, 'pesos' => [1000, 1300], 'usos' => ['parrilla'], 'foto' => '1690618513975-324617013892', 'modelo' => '', 'firma' => true],
        'lomo-liso-21' => ['cat' => 'madurados', 'nombre' => 'Lomo liso 21 días', 'desc' => 'Bife angosto con su tapa de grasa. Tierno, de sabor limpio y profundo.', 'dias' => 21, 'kilo' => 42900, 'pesos' => [350, 700, 1050], 'usos' => ['parrilla', 'sarten'], 'foto' => '/demos/_comun/fotos3d/carne-lomo-liso-oscuro.webp', 'modelo' => 'carne-lomo-liso', 'firma' => false],
        't-bone-45' => ['cat' => 'madurados', 'nombre' => 'T-bone 45 días', 'desc' => 'Lomo liso y filete separados por el hueso. Sabor intenso, para conocedores.', 'dias' => 45, 'kilo' => 59900, 'pesos' => [700, 1000], 'usos' => ['parrilla'], 'foto' => '/demos/_comun/fotos3d/carne-t-bone-oscuro.webp', 'modelo' => 'carne-t-bone', 'firma' => true],
        'reserva-60' => ['cat' => 'madurados', 'nombre' => 'Reserva 60 días', 'desc' => 'Edición limitada de lomo vetado. Notas a queso azul y avellana. Pocas piezas por semana.', 'dias' => 60, 'kilo' => 74900, 'pesos' => [400, 800], 'usos' => ['parrilla', 'sarten'], 'foto' => '/demos/_comun/fotos3d/carne-reserva-60-oscuro.webp', 'modelo' => 'carne-reserva-60', 'firma' => false],
        'entrana' => ['cat' => 'parrilla', 'nombre' => 'Entraña', 'desc' => 'Fina y jugosa: dos minutos por lado a fuego vivo y a la tabla.', 'dias' => 0, 'kilo' => 29900, 'pesos' => [500, 1000], 'usos' => ['parrilla', 'sarten'], 'foto' => '1592686092916-672fa9e86866', 'modelo' => '', 'firma' => true],
        'punta-ganso-21' => ['cat' => 'parrilla', 'nombre' => 'Punta de ganso 21 días', 'desc' => 'Pieza entera con su tapa de grasa, para cortar en la mesa.', 'dias' => 21, 'kilo' => 28900, 'pesos' => [1200, 1800], 'usos' => ['parrilla', 'horno'], 'foto' => '/demos/_comun/fotos3d/carne-punta-ganso-oscuro.webp', 'modelo' => 'carne-punta-ganso', 'firma' => false],
        'asado-tira' => ['cat' => 'parrilla', 'nombre' => 'Asado de tira banderita', 'desc' => 'Costilla en tiras de 4 cm con hueso. Tres horas a fuego bajo.', 'dias' => 0, 'kilo' => 21900, 'pesos' => [1000, 2000], 'usos' => ['parrilla', 'horno'], 'foto' => '1565299524732-d2149c7eabf5', 'modelo' => '', 'firma' => false],
        'cordero' => ['cat' => 'parrilla', 'nombre' => 'Costillar de cordero', 'desc' => 'Cordero de la Patagonia, limpio a la francesa.', 'dias' => 0, 'kilo' => 32900, 'pesos' => [800, 1600], 'usos' => ['parrilla', 'horno'], 'foto' => '1717980651515-7796a793002f', 'modelo' => '', 'firma' => false],
        'roast-beef' => ['cat' => 'horno', 'nombre' => 'Lomo liso para roast beef', 'desc' => 'Pieza amarrada, lista para sellar y hornear 40 minutos.', 'dias' => 14, 'kilo' => 36900, 'pesos' => [1000, 1500], 'usos' => ['horno'], 'foto' => '1628497622763-36456bb4d56b', 'modelo' => '', 'firma' => false],
        'osobuco' => ['cat' => 'horno', 'nombre' => 'Osobuco de pierna', 'desc' => 'Cortado grueso, con su médula. Para la olla de domingo.', 'dias' => 0, 'kilo' => 14900, 'pesos' => [1000, 2000], 'usos' => ['olla', 'horno'], 'foto' => '', 'modelo' => '', 'firma' => false],
        'hamburguesas' => ['cat' => 'casa', 'nombre' => 'Hamburguesas de madurado', 'desc' => 'Lomo vetado y punta de ganso madurados, molidos gruesos. 180 g cada una.', 'dias' => 21, 'variantes' => ['Caja de 4' => 15900, 'Caja de 8' => 29900], 'usos' => ['parrilla', 'sarten'], 'foto' => '1612078894671-f11ba41d713e', 'modelo' => '', 'firma' => false],
        'chorizo' => ['cat' => 'casa', 'nombre' => 'Chorizo artesanal', 'desc' => 'Cerdo y vacuno, ajo asado y merkén suave. 120 g cada uno.', 'dias' => 0, 'variantes' => ['4 unidades' => 9900, '8 unidades' => 18900], 'usos' => ['parrilla'], 'foto' => '1766589152317-2bafe6f2923c', 'modelo' => '', 'firma' => false],
        'longaniza' => ['cat' => 'casa', 'nombre' => 'Longaniza de la casa', 'desc' => 'Receta campesina, ahumada en frío con madera de espino.', 'dias' => 0, 'variantes' => ['500 g' => 8900, '1 kg' => 16900], 'usos' => ['parrilla', 'sarten'], 'foto' => '1624772398061-bbfa87ec6b5a', 'modelo' => '', 'firma' => false],
        'caja-4' => ['cat' => 'cajas', 'nombre' => 'Caja para 4', 'desc' => '2 lomos vetados 28 días, lomo liso 21 días, 500 g de entraña, 4 chorizos y carbón de espino. Unos 2 kg de carne.', 'dias' => 0, 'variantes' => ['Única' => 89900], 'usos' => ['parrilla'], 'foto' => '1690983325551-b922137727be', 'modelo' => '', 'firma' => false],
        'caja-8' => ['cat' => 'cajas', 'nombre' => 'Caja para 8', 'desc' => 'Tomahawk 35 días, 2 lomos vetados, 2 lomos lisos, 1 kg de entraña, punta de ganso, 8 chorizos y carbón. Unos 4 kg.', 'dias' => 0, 'variantes' => ['Única' => 169900], 'usos' => ['parrilla'], 'foto' => '1666013943134-48dd4267a4c1', 'modelo' => '', 'firma' => false],
        'caja-12' => ['cat' => 'cajas', 'nombre' => 'Caja para 12', 'desc' => 'Tomahawk, T-bone 45 días, 3 lomos vetados, 3 lomos lisos, entraña, asado de tira, 12 chorizos y carbón. Unos 6 kg.', 'dias' => 0, 'variantes' => ['Única' => 239900], 'usos' => ['parrilla'], 'foto' => '1690983329845-638ec321647d', 'modelo' => '', 'firma' => false],
        'carbon' => ['cat' => 'despensa', 'nombre' => 'Carbón de espino', 'desc' => 'Saco de 10 kg. Brasa larga y pareja, sin chispas.', 'dias' => 0, 'variantes' => ['Única' => 12900], 'usos' => [], 'foto' => '1597353811858-fbb7ce0a9ca6', 'modelo' => '', 'firma' => false],
        'sal' => ['cat' => 'despensa', 'nombre' => 'Sal de mar en escamas', 'desc' => 'De la costa de Cáhuil, 250 g. Lo único que necesita un buen corte.', 'dias' => 0, 'variantes' => ['Única' => 6900], 'usos' => [], 'foto' => '1558394299-f2e6198506a6', 'modelo' => '', 'firma' => false],
        'chimichurri' => ['cat' => 'despensa', 'nombre' => 'Chimichurri de la casa', 'desc' => 'Perejil, orégano, ajo y aceite de oliva. Frasco de 250 ml.', 'dias' => 0, 'variantes' => ['Única' => 5900], 'usos' => [], 'foto' => '', 'modelo' => '', 'firma' => false],
    ],
    'fotos' => [
        'hero' => '1690983322840-3bde693e178d',
        'camara' => '1788178941299-b206d3f4c4c6',
        'maestro' => '1615937662601-4724eceda00f',
        'fuego' => '1614119068601-483274e9dcb7',
        'fila' => '1690983320937-ca293f1d1d97',
    ],
    // Línea de maduración (página La cámara): días => [título, texto]
    'maduracion' => [
        21 => ['Tierno y limpio', 'La carne se relaja y gana terneza. Sabor a vacuno puro, ideal para empezar.'],
        28 => ['Nuez y mantequilla', 'Aparecen las notas tostadas. Nuestro punto favorito para el lomo vetado.'],
        35 => ['Profundo', 'Más intenso y concentrado. La grasa se vuelve dulce en la parrilla.'],
        45 => ['Para conocedores', 'Notas a queso maduro y frutos secos. Se sirve en láminas finas.'],
        60 => ['Reserva', 'Pocas piezas por semana. Notas a queso azul y avellana.'],
    ],
    'preguntas' => [
        ['¿Qué es la maduración en seco?', 'Dejamos las piezas enteras en una cámara a 2 °C, con humedad y aire controlados, entre 21 y 60 días. La carne pierde agua, concentra su sabor y sus fibras se ablandan de forma natural.'],
        ['¿Por qué cuesta más que un corte fresco?', 'Durante la maduración la pieza pierde cerca de un 30 % de su peso y hay que retirar la costra exterior. Además, ocupa la cámara durante semanas. Pagas menos agua y más sabor.'],
        ['¿Hacen despacho?', 'Sí, en Las Condes, Vitacura, Lo Barnechea, Providencia, La Reina y Ñuñoa. Si pides antes de las 13:00 lo recibes el mismo día; si no, al día siguiente. Todo va envasado al vacío y refrigerado.'],
        ['¿Puedo pedir un grosor especial?', 'Claro. Escribe en la nota del pedido el grosor o el peso que buscas (por ejemplo, «bifes de 4 cm») y lo cortamos así. El precio final se ajusta al peso real.'],
        ['¿Cuánto dura en el refrigerador?', 'Envasado al vacío, entre 7 y 10 días a menos de 4 °C. También puedes congelarlo: lo mejor es descongelarlo lento, de un día para otro en el refrigerador.'],
        ['¿Qué es «Ver en 3D»?', 'En la carta de cortes puedes girar algunos cortes en 3D y, desde el celular, verlos sobre tu mesa con la cámara para imaginar el tamaño real.'],
        ['¿Cómo pago?', 'En la tienda aceptamos efectivo, débito y crédito. En los despachos, pagas al recibir con tarjeta o por transferencia.'],
    ],
];
