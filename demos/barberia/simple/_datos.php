<?php
declare(strict_types=1);

// Barbería Don Lalo (Quinta Normal): negocio ficticio de la versión Simple-page.
// Fotos: IDs de Unsplash (ver demos/barberia/CREDITOS.md). Sin marcas ni letreros legibles, solo adultos.
return [
    'nombre' => 'Barbería Don Lalo',
    'categoria' => 'Barbería',
    'comuna' => 'Quinta Normal',
    'desde' => 1987,
    'nota' => 4.8,
    // Lunes cerrado, como las barberías de antes.
    'horario' => [
        1 => null,
        2 => ['10:00', '20:00'],
        3 => ['10:00', '20:00'],
        4 => ['10:00', '20:00'],
        5 => ['10:00', '20:00'],
        6 => ['09:30', '18:00'],
        7 => ['10:00', '14:00'],
    ],
    'fotos' => [
        'hero' => '1747832512459-5566e6d0ee5a',
        'local' => '1611313151697-d626e818dddf',
    ],
    // Servicios de la pizarra: id => [nombre, detalle, minutos, precio].
    'servicios' => [
        'clasico' => ['Corte clásico', 'Tijera y máquina, como siempre. Con lavado y una pasada de pomada.', 30, 9000],
        'fade' => ['Degradado / fade', 'Bajo, medio o alto, a máquina y terminado a navaja.', 40, 11000],
        'corte-barba' => ['Corte + barba', 'El corte que quieras y la barba perfilada a tijera y máquina.', 50, 15000],
        'barba' => ['Perfilado y diseño de barba', 'Contorno a navaja, largo parejo y aceite para terminar.', 25, 7000],
        'afeitado' => ['Afeitado con toalla caliente', 'Dos toallas calientes, espuma a brocha, navaja y bálsamo.', 35, 10000],
        'cejas' => ['Cejas', 'Limpieza y perfilado con navaja o máquina chica.', 10, 3000],
        'tinte' => ['Tinte de barba', 'Cubre canas en tono natural. Prueba de alergia el día antes.', 30, 8000],
        'rapado' => ['Rapado a máquina', 'Un solo número parejo, con contorno a navaja.', 20, 6000],
    ],
    // Planes del mes (se piden por WhatsApp).
    'planes' => [
        ['Plan Vecino', '2 cortes al mes', 16000, 'Ahorras $2.000 y te guardamos tu hora fija cada 15 días.'],
        ['Plan Caballero', '2 cortes + 2 barbas al mes', 26000, 'Ahorras $4.000. El favorito de los que llevan barba.'],
    ],
    // Lo más pedido: tarjetas con foto (id del servicio => foto).
    'destacados' => [
        'clasico' => ['foto' => '1593702275687-f8b402bf1fb5', 'alt' => 'Barbero de camisa blanca cortando con máquina el costado de un cliente con barba'],
        'fade' => ['foto' => '1640301133543-41fe25ad6450', 'alt' => 'Barbero con tatuajes en las manos haciendo un degradado alto con máquina y peine'],
        'corte-barba' => ['foto' => '1599011176306-4a96f1516d4d', 'alt' => 'Barbero recortando con tijera la barba de un cliente con capa a rayas'],
        'afeitado' => ['foto' => '1733995471058-3d6ff2013de3', 'alt' => 'Cliente con bigote reclinado y la cara con espuma de afeitar'],
    ],
    'barberos' => [
        'lalo' => ['Don Lalo', 'Clásicos, tijera y navaja. Atiende desde 1987.'],
        'nico' => ['Nico', 'Degradados, diseños y barbas. El hijo de Don Lalo.'],
        'cualquiera' => ['El que esté libre', 'Te atiende el primero que se desocupe.'],
    ],
    'galeria' => [
        ['1701891123509-e983d6c45259', 'Contorno de barba a máquina', 'Barbero marcando con máquina la línea de la barba de un cliente con capa roja'],
        ['1701885183616-cf00e2db1a3b', 'Raya marcada a navaja', 'Barbero sosteniendo peine y tijera junto a una raya recién marcada en el costado'],
        ['1669568921948-f0346e0ee73e', 'Perfilado de patillas', 'Primer plano de una máquina chica marcando la patilla y el contorno de la oreja'],
        ['1783908688260-0c47404926f3', 'Corte clásico con peine', 'Barbero con guantes negros cortando con peine y tijera'],
        ['1630827020718-3433092696e7', 'Degradado con barba completa', 'Hombre de perfil con degradado bajo, pelo peinado hacia atrás y barba tupida'],
    ],
    'resenas' => [
        ['Héctor M.', 5, 'Me corto aquí hace más de quince años. Don Lalo sigue cortando igual de bien y la conversa del fútbol no falla.', 'hace 2 semanas'],
        ['Kevin R.', 5, 'Nico me hizo un degradado impecable. Reservé por WhatsApp para el sábado y no esperé nada.', 'hace 1 mes'],
        ['Jorge P.', 4, 'Buen precio y trato de barrio. Los sábados en la mañana se llena: mejor pedir hora.', 'hace 2 meses'],
    ],
];
