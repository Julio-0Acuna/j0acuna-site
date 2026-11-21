<?php
declare(strict_types=1);

// Atelier Lúcuma (Vitacura): negocio ficticio de la versión Multi-page.
// Fotos: IDs de Unsplash (ver demos/pasteleria/CREDITOS.md). Modelo 3D: demos/_comun/3d/torta.glb.
return [
    'nombre' => 'Atelier Lúcuma',
    'categoria' => 'Pastelería de autor',
    'comuna' => 'Vitacura',
    'eslogan' => 'Pastelería de autor con frutas de Chile',
    'pastelera' => 'Josefina Arancibia',
    'nota' => 4.9,
    'horario' => [
        1 => null,
        2 => ['10:00', '19:30'],
        3 => ['10:00', '19:30'],
        4 => ['10:00', '19:30'],
        5 => ['10:00', '19:30'],
        6 => ['10:00', '19:30'],
        7 => ['10:00', '15:00'],
    ],
    // Encargos: días de anticipación mínimos y máximos.
    'anticipacion' => [3, 90],
    'resenas' => [
        ['Catalina V.', 5, 'Encargamos la de higos y miel de ulmo para el matrimonio civil: preciosa y nada empalagosa. El formulario de encargo es facilísimo.', 'hace 2 semanas'],
        ['Andrés M.', 5, 'Los macarons de lúcuma son lo mejor que he probado en Santiago. Llegaron en una caja impecable.', 'hace 1 mes'],
        ['Sofía R.', 4, 'La tarta de limón de Pica es un siete. Hay que encargar con tiempo, se llenan en diciembre.', 'hace 2 meses'],
    ],
    'comunas_despacho' => [
        'Vitacura' => 3900,
        'Las Condes' => 4500,
        'Lo Barnechea' => 5500,
        'Providencia' => 5500,
        'La Reina' => 6500,
    ],
    'ocasiones' => [
        'cumpleanos' => 'Cumpleaños',
        'matrimonio' => 'Matrimonios',
        'sin-gluten' => 'Sin gluten',
        'sin-azucar' => 'Sin azúcar añadida',
    ],
    // Colección de tortas (galería, encargos y carta). precios: tamaño => CLP. modelo: .glb en demos/_comun/3d/.
    'tortas' => [
        'higos' => ['nombre' => 'Higos y miel de ulmo', 'notas' => 'Bizcocho de almendra · crema de mascarpone · higos asados', 'desc' => 'Nuestra torta de firma: capas de almendra, mascarpone batido con miel de ulmo del sur e higos frescos asados.', 'foto' => '1565661834013-d196ca46e14e', 'ocasiones' => ['cumpleanos', 'matrimonio'], 'precios' => ['8 porciones' => 36900, '14 porciones' => 54900, '20 porciones' => 72900], 'modelo' => ''],
        'chocolate-maqui' => ['nombre' => 'Chocolate 70 % y maqui', 'notas' => 'Mousse de chocolate · gelée de maqui · perlas crujientes', 'desc' => 'Mousse de chocolate amargo con corazón de maqui de la Araucanía y base crocante de cacao.', 'foto' => '1602351447937-745cb720612f', 'ocasiones' => ['cumpleanos'], 'precios' => ['8 porciones' => 34900, '14 porciones' => 51900, '20 porciones' => 68900], 'modelo' => 'torta-chocolate-maqui'],
        'frutos-rojos' => ['nombre' => 'Frutos rojos y vainilla', 'notas' => 'Bizcocho de vainilla · crema diplomática · frutillas y arándanos', 'desc' => 'Dos pisos de bizcocho de vainilla de Tahití con crema diplomática y fruta fresca de la temporada.', 'foto' => '1535141192574-5d4897c12636', 'ocasiones' => ['cumpleanos', 'matrimonio'], 'precios' => ['14 porciones' => 56900, '20 porciones' => 74900, '30 porciones' => 104900], 'modelo' => 'torta'],
        'lucuma-cafe' => ['nombre' => 'Lúcuma, café y chocolate', 'notas' => 'Bizcocho de café · crema de lúcuma · ganache', 'desc' => 'La clásica torta chilena de lúcuma, reinventada: crema de lúcuma de Pica, bizcocho de café de grano y un velo de ganache.', 'foto' => '1621303837383-77d809baf08b', 'ocasiones' => ['cumpleanos'], 'precios' => ['8 porciones' => 32900, '14 porciones' => 48900, '20 porciones' => 64900], 'modelo' => 'torta-lucuma-cafe'],
        'pistacho' => ['nombre' => 'Pistacho y frambuesa', 'notas' => 'Entremet · mousse de frambuesa · dacquoise de pistacho', 'desc' => 'Un entremet brillante y fresco: mousse de frambuesa, centro de pistacho tostado y glaseado espejo.', 'foto' => '1761637604698-0f9e9d3278ee', 'ocasiones' => ['cumpleanos', 'sin-gluten'], 'precios' => ['8 porciones' => 38900, '14 porciones' => 57900], 'modelo' => 'entremet-pistacho-frambuesa'],
        'pavlova' => ['nombre' => 'Pavlova de frutillas y rosa', 'notas' => 'Merengue crujiente · crema de rosa · frutillas', 'desc' => 'Merengue horneado lento, crema batida con agua de rosas y frutillas de Chillán. Naturalmente sin gluten.', 'foto' => '1761637604365-d57cb1ab6f67', 'ocasiones' => ['cumpleanos', 'sin-gluten'], 'precios' => ['8 porciones' => 29900, '14 porciones' => 44900], 'modelo' => 'pavlova-frutillas'],
        'limon-pica' => ['nombre' => 'Tarta de limón de Pica', 'notas' => 'Sablé de mantequilla · crema de limón · merengue suizo', 'desc' => 'Masa sablé, crema de limón de Pica bien ácida y merengue suizo tostado al soplete.', 'foto' => '1765992314341-48ba688f8d49', 'ocasiones' => ['cumpleanos'], 'precios' => ['8 porciones' => 26900, '14 porciones' => 39900], 'modelo' => ''],
        'bosque' => ['nombre' => 'Tarta del bosque', 'notas' => 'Masa de almendra · crema de yogur · berries', 'desc' => 'Base de harina de almendra y crema de yogur griego endulzada con fruta. Sin gluten y sin azúcar añadida.', 'foto' => '1653491887846-781545ef38da', 'ocasiones' => ['sin-gluten', 'sin-azucar'], 'precios' => ['8 porciones' => 31900, '14 porciones' => 47900], 'modelo' => ''],
        'flores-blancas' => ['nombre' => 'Flores blancas', 'notas' => 'Tres pisos · vainilla y frutos rojos · flores naturales', 'desc' => 'Torta de matrimonio de tres pisos cubierta en crema de mantequilla suiza y flores blancas naturales.', 'foto' => '1764269710986-0b69be00d7ca', 'ocasiones' => ['matrimonio'], 'precios' => ['50 porciones' => 249000, '80 porciones' => 379000, '120 porciones' => 549000], 'modelo' => 'torta-novios-flores-blancas'],
        'naked-berries' => ['nombre' => 'Naked de berries', 'notas' => 'Cuatro pisos · bizcocho de limón · frutillas y flores', 'desc' => 'Torta desnuda de cuatro pisos con bizcocho de limón, crema chantilly y frutillas frescas. Para celebraciones grandes.', 'foto' => '1623428454614-abaf00244e52', 'ocasiones' => ['matrimonio'], 'precios' => ['50 porciones' => 229000, '80 porciones' => 349000, '120 porciones' => 499000], 'modelo' => 'naked-berries-4-pisos'],
    ],
    // Carta para pedir en línea (carrito): categoría => [título, bajada].
    'categorias' => [
        'individuales' => ['Pastelería individual', 'Hechas cada mañana; se acaban temprano.'],
        'cajas' => ['Cajas para regalar', 'Listas en cajas de cartón reciclado, con cinta.'],
        'tortas' => ['Tortas enteras', 'Con al menos 3 días de anticipación. Elige el tamaño.'],
        'cafe' => ['Café y té', 'Para acompañar en el atelier o para llevar.'],
    ],
    // Platos de la carta que no son tortas. variantes: nombre => precio.
    'platos' => [
        'tartaleta-berries' => ['cat' => 'individuales', 'nombre' => 'Tartaleta de berries', 'desc' => 'Sablé, crema pastelera de vainilla y berries frescos.', 'foto' => '1620980776848-84ac10194945', 'variantes' => ['Única' => 4900]],
        'pavlova-individual' => ['cat' => 'individuales', 'nombre' => 'Pavlova individual', 'desc' => 'Merengue crujiente, crema de rosa y frutas de la temporada.', 'foto' => '1634324040880-63dbf9a4e5ac', 'variantes' => ['Única' => 5200]],
        'cheesecake-maracuya' => ['cat' => 'individuales', 'nombre' => 'Cheesecake de maracuyá', 'desc' => 'Horneado a baja temperatura, con coulis de maracuyá.', 'foto' => '1524351199678-941a58a3df50', 'variantes' => ['Única' => 4800]],
        'caramelo-salado' => ['cat' => 'individuales', 'nombre' => 'Trozo de caramelo salado', 'desc' => 'Bizcocho de nuez, caramelo con sal de Cáhuil y crocante de cacao.', 'foto' => '1547414368-ac947d00b91d', 'variantes' => ['Única' => 4600]],
        'macarons' => ['cat' => 'cajas', 'nombre' => 'Macarons de la casa', 'desc' => 'Lúcuma, maqui, pistacho, frambuesa, limón y chocolate.', 'foto' => '1558326567-98ae2405596b', 'variantes' => ['Caja de 6' => 9900, 'Caja de 12' => 18500]],
        'tartaletas-frutilla' => ['cat' => 'cajas', 'nombre' => 'Mini tartaletas de frutilla', 'desc' => 'Cuatro tartaletas de frutilla y crema de vainilla.', 'foto' => '1771623492237-4010e49fc0d8', 'variantes' => ['Caja de 4' => 11900, 'Caja de 8' => 22900]],
        'merengues-florales' => ['cat' => 'cajas', 'nombre' => 'Merengues florales', 'desc' => 'Merengues suizos con flores comestibles. Sin gluten.', 'foto' => '1646067684321-2b6f6be84981', 'variantes' => ['Caja de 8' => 7900]],
        'alfajores-lucuma' => ['cat' => 'cajas', 'nombre' => 'Alfajores de lúcuma', 'desc' => 'Masa de maicena y manjar de lúcuma, bañados en chocolate blanco.', 'foto' => '', 'variantes' => ['Caja de 6' => 8400, 'Caja de 12' => 15900]],
        'espresso' => ['cat' => 'cafe', 'nombre' => 'Espresso de especialidad', 'desc' => 'Grano de Huila, tueste medio.', 'foto' => '', 'variantes' => ['Simple' => 2400, 'Doble' => 3100]],
        'capuchino' => ['cat' => 'cafe', 'nombre' => 'Capuchino', 'desc' => 'Con leche entera o de avena.', 'foto' => '', 'variantes' => ['Única' => 3600]],
        'te-hebras' => ['cat' => 'cafe', 'nombre' => 'Té en hebras', 'desc' => 'Negro, verde jazmín o rooibos con naranja.', 'foto' => '', 'variantes' => ['Única' => 3200]],
    ],
    'fotos' => [
        'hero' => '1565661834013-d196ca46e14e',
        'cacao' => '1512223792601-592a9809eed4',
        'manos' => '1675490456444-b2b5e2e4d8b0',
        'manga' => '1786114352364-b354a97bfbce',
        'vitrina' => '1583338917451-face2751d8d5',
    ],
    'ingredientes' => [
        ['Lúcuma', 'De Pica y del valle de Elqui: su crema es nuestra firma.'],
        ['Maqui', 'Berry nativo de la Araucanía, ácido y profundo.'],
        ['Miel de ulmo', 'Del sur de Chile, floral y suave.'],
        ['Limón de Pica', 'Pequeño, aromático y muy jugoso.'],
        ['Murta', 'Fruto del bosque valdiviano, para el otoño.'],
        ['Sal de Cáhuil', 'Cosechada a mano, para el caramelo.'],
    ],
    'preguntas' => [
        ['¿Con cuánta anticipación debo encargar?', 'Las tortas de la colección, con al menos 3 días. Las tortas de matrimonio, con 3 a 4 semanas. En diciembre conviene encargar antes porque los cupos se llenan.'],
        ['¿Puedo cambiar el diseño o el sabor?', 'Sí. En el formulario de encargo elige «Diseño a medida» y cuéntanos tu idea: te respondemos por WhatsApp con una propuesta y el precio.'],
        ['¿Tienen opciones sin gluten o sin azúcar?', 'La pavlova, el entremet de pistacho y la tarta del bosque son sin gluten. La tarta del bosque además es sin azúcar añadida. Trabajamos en una cocina con harina, así que no es apta para celíacos severos.'],
        ['¿Despachan?', 'Sí, en Vitacura, Las Condes, Lo Barnechea, Providencia y La Reina, en vehículo refrigerado. También puedes retirar en el atelier.'],
        ['¿Cómo se paga un encargo?', 'Con un abono del 50 % por transferencia al confirmar, y el resto al retirar o recibir.'],
        ['¿Qué es «Ver en 3D»?', 'En la colección y en la carta puedes girar algunas tortas en 3D y, desde el celular, verlas sobre tu mesa con la cámara antes de encargar.'],
    ],
];
