<?php
declare(strict_types=1);

// El Gabinete (Santiago Centro, barrio Lastarria): negocio ficticio de la versión Multi-page.
// Barbería de caballeros old school con sala de espera y bebidas sin alcohol.
// Fotos: IDs de Unsplash (ver demos/barberia/CREDITOS.md). Sin marcas ni letreros legibles, solo adultos.
return [
    'nombre' => 'El Gabinete',
    'categoria' => 'Barbería de caballeros',
    'comuna' => 'Santiago Centro',
    'barrio' => 'barrio Lastarria',
    'eslogan' => 'Barbería de caballeros desde 1958',
    'desde' => 1958,
    'nota' => 4.9,
    'horario' => [
        1 => ['10:00', '20:00'],
        2 => ['10:00', '20:00'],
        3 => ['10:00', '20:00'],
        4 => ['10:00', '20:00'],
        5 => ['10:00', '20:00'],
        6 => ['10:00', '18:00'],
        7 => null,
    ],
    // Bloques de una hora para la reserva (los sábados, hasta el de las 17:00).
    'bloques' => ['10:00 – 11:00', '11:00 – 12:00', '12:00 – 13:00', '13:00 – 14:00', '14:00 – 15:00', '15:00 – 16:00', '16:00 – 17:00', '17:00 – 18:00', '18:00 – 19:00', '19:00 – 20:00'],
    'fotos' => [
        'portada' => '1775494165568-42c0fbb4c782',
        'salon' => '1773904215697-e6c21fc27ac2',
        'sillon' => '1621645582931-d1d3e6564943',
        'ritual' => '1675746435874-e72d846bdca5',
        'espera' => '1605797063353-4cbcb6027f2c',
    ],
    'barberos' => [
        'aurelio' => ['nombre' => 'Don Aurelio', 'oficio' => 'Maestro barbero · navaja y afeitado clásico', 'dias' => 'Martes a sábado', 'foto' => '1648817976768-a2fdcba20f9a', 'alt' => 'Retrato de un barbero mayor de barba blanca sobre fondo negro', 'bio' => 'Cuarenta años de oficio. Aprendió a afeitar con navaja en este mismo salón y todavía asienta la hoja en el cuero antes de cada cliente.'],
        'tomas' => ['nombre' => 'Tomás', 'oficio' => 'Cortes clásicos y de tijera', 'dias' => 'Lunes a viernes', 'foto' => '1677543167033-af3c688aa4df', 'alt' => 'Retrato de un barbero con boina y barba, sonriendo', 'bio' => 'El de las rayas perfectas y los peinados con pomada. Si no sabes qué corte te queda bien, pregúntale a él.'],
        'joaquin' => ['nombre' => 'Joaquín', 'oficio' => 'Degradados y barba', 'dias' => 'Lunes a sábado', 'foto' => '1786913290381-7d2d0345f1bb', 'alt' => 'Retrato en blanco y negro de un barbero de bigote y pelo largo peinado', 'bio' => 'Degradados limpios que duran, y barbas con forma. Trabaja con máquina y termina siempre a navaja.'],
    ],
    // Categorías: clave => [nombre, bajada].
    'categorias' => [
        'cortes' => ['Cortes', 'Todos con lavado, masaje capilar y peinado con pomada.'],
        'barba' => ['Barba y navaja', 'Toalla caliente, espuma a brocha y navaja de hoja única.'],
        'detalles' => ['Detalles', 'Para sumar a tu corte o para venir entre visitas.'],
        'socio' => ['Socio del Gabinete', 'Planes mensuales con hora fija, prioridad en la agenda y bebida de la casa.'],
        'llevar' => ['Para llevar', 'Lo mismo que usamos en el sillón, en tamaño para la casa. Lo retiras al venir.'],
    ],
    // Servicios y productos: id => datos. 'variantes' => [nombre => precio]; 'Única' si no hay opciones.
    'servicios' => [
        'corte' => ['cat' => 'cortes', 'nombre' => 'Corte de caballero', 'desc' => 'Tijera y máquina, lavado, masaje capilar y peinado con pomada.', 'dur' => '45 min', 'variantes' => ['Pelo corto' => 18000, 'Pelo largo' => 21000], 'foto' => '1657105052497-f996284ffff8', 'alt' => 'Barbero entresacando con tijera y peine el pelo de la nuca', 'destacado' => true, 'modelo' => 'tijeras'],
        'degradado' => ['cat' => 'cortes', 'nombre' => 'Degradado a navaja', 'desc' => 'Fade bajo, medio o alto, terminado a navaja con toalla caliente en la nuca.', 'dur' => '50 min', 'variantes' => ['Única' => 20000], 'foto' => '1783908688260-0c47404926f3', 'alt' => 'Barbero con guantes negros cortando con peine y tijera', 'destacado' => true],
        'corte-barba' => ['cat' => 'cortes', 'nombre' => 'Corte y barba', 'desc' => 'El corte de caballero más el perfilado de barba con toalla caliente.', 'dur' => '75 min', 'variantes' => ['Única' => 30000], 'foto' => '1517832606299-7ae9b720a186', 'alt' => 'Foto en blanco y negro de un barbero recortando con tijera una barba tupida'],
        'afeitado' => ['cat' => 'barba', 'nombre' => 'Afeitado clásico con toalla caliente', 'desc' => 'Dos toallas calientes, aceite previo, espuma a brocha, navaja y bálsamo frío.', 'dur' => '40 min', 'variantes' => ['Única' => 18000], 'foto' => '1648221122279-5246dd0cf86c', 'alt' => 'Cliente reclinado con una toalla sobre los ojos mientras el barbero lo afeita', 'destacado' => true, 'modelo' => 'afeitadora'],
        'perfilado' => ['cat' => 'barba', 'nombre' => 'Perfilado y diseño de barba', 'desc' => 'Forma, largo y contorno a navaja, con aceite y bálsamo para terminar.', 'dur' => '30 min', 'variantes' => ['Única' => 14000], 'foto' => '1675746435874-e72d846bdca5', 'alt' => 'Foto en blanco y negro de un barbero pasando la navaja por una barba con espuma'],
        'ritual' => ['cat' => 'barba', 'nombre' => 'Ritual del Gabinete', 'desc' => 'Corte, afeitado o barba con toalla caliente, mascarilla y masaje de hombros.', 'dur' => '100 min', 'variantes' => ['Única' => 42000], 'foto' => '1761148438883-e34e0289a214', 'alt' => 'Barbero con guantes afeitando con navaja a un cliente entre el vapor de la toalla caliente', 'destacado' => true],
        'cejas' => ['cat' => 'detalles', 'nombre' => 'Cejas a navaja', 'desc' => 'Limpieza y forma natural, sin que se note.', 'dur' => '15 min', 'variantes' => ['Única' => 6000]],
        'tinte' => ['cat' => 'detalles', 'nombre' => 'Tinte de barba', 'desc' => 'Cubre canas en tono natural. Prueba de alergia 48 horas antes.', 'dur' => '30 min', 'variantes' => ['Única' => 15000]],
        'masaje' => ['cat' => 'detalles', 'nombre' => 'Lavado y masaje capilar', 'desc' => 'Agua tibia, tónico de romero y toalla caliente en la nuca.', 'dur' => '15 min', 'variantes' => ['Única' => 7000]],
        'socio-corte' => ['cat' => 'socio', 'nombre' => 'Socio Corte', 'desc' => 'Dos cortes de caballero al mes, con hora fija y prioridad en la agenda.', 'dur' => '2 visitas al mes', 'variantes' => ['Mensual' => 32000, 'Trimestral' => 90000]],
        'socio-completo' => ['cat' => 'socio', 'nombre' => 'Socio Corte y Barba', 'desc' => 'Dos cortes y dos arreglos de barba al mes, siempre con toalla caliente.', 'dur' => '2 visitas al mes', 'variantes' => ['Mensual' => 52000, 'Trimestral' => 148000]],
        'pomada' => ['cat' => 'llevar', 'nombre' => 'Pomada mate fijación media 100 g', 'desc' => 'Base de agua, sin brillo y fácil de lavar. Para cortes clásicos y texturizados.', 'dur' => '', 'variantes' => ['Única' => 14900], 'foto' => '1763503834047-ac85c4105c0b', 'alt' => 'Frasco abierto de pomada blanca sobre una piedra', 'modelo' => 'pomada'],
        'aceite' => ['cat' => 'llevar', 'nombre' => 'Aceite para barba 30 ml', 'desc' => 'Suaviza y da brillo natural. Aroma a madera y cítricos.', 'dur' => '', 'variantes' => ['Única' => 12900], 'foto' => '1773452451152-83e248bd61fe', 'alt' => 'Frasco gotario negro sin etiqueta en un pocillo de cerámica', 'modelo' => 'frasco'],
        'balsamo' => ['cat' => 'llevar', 'nombre' => 'Bálsamo after shave 100 ml', 'desc' => 'Sin alcohol: calma la piel después de la navaja.', 'dur' => '', 'variantes' => ['Única' => 13900], 'foto' => '1671493234254-15fc6c91aa87', 'alt' => 'Botella de vidrio ámbar sin etiqueta sobre un paño de lino', 'modelo' => 'frasco'],
        'jabon' => ['cat' => 'llevar', 'nombre' => 'Jabón de afeitar en taza 100 g', 'desc' => 'El mismo de nuestras espumas, para usar con brocha.', 'dur' => '', 'variantes' => ['Única' => 9900], 'foto' => '1524230616393-d6229fcd2eff', 'alt' => 'Navaja de barbero y brocha apoyadas en una taza de jabón de afeitar'],
        'brocha' => ['cat' => 'llevar', 'nombre' => 'Brocha de afeitar con mango de madera', 'desc' => 'Cerdas sintéticas suaves que arman espuma en segundos.', 'dur' => '', 'variantes' => ['Única' => 16900], 'foto' => '1632072907857-d2cae8f52888', 'alt' => 'Brocha de afeitar con mango de madera colgando de una cuerda'],
        'tonico' => ['cat' => 'llevar', 'nombre' => 'Tónico capilar de romero 150 ml', 'desc' => 'Refresca el cuero cabelludo después del lavado.', 'dur' => '', 'variantes' => ['Única' => 11900], 'foto' => '1671493234884-b1611bcf3e69', 'alt' => 'Dos botellas de vidrio ámbar sin etiqueta sobre un paño a rayas'],
    ],
    // Carta de la sala de espera: todas sin alcohol y por cuenta de la casa con tu hora.
    'bebidas' => [
        ['Café de grano', 'Espresso, cortado o americano, molido al momento.', '1579992357154-faf4bde95b3d', 'Taza de espresso con crema sobre su platillo y una cuchara negra'],
        ['Té helado con limón', 'Té negro frío, limón de pica y hojas de menta.', '1556679343-c7306c1976bc', 'Vaso de té helado con hielo, limón y menta sobre una tabla de madera'],
        ['Ginger ale de la casa', 'Jengibre, limón y agua con gas. Sin alcohol, con harto hielo.', '1684439670717-b1147a7e7534', 'Vaso alto con bebida ámbar y hielo sobre una barra oscura'],
    ],
    'bebidas_mas' => ['Té en hebras: negro, verde o rooibos', 'Limonada con menta y jengibre', 'Agua con o sin gas', 'Chocolate caliente en invierno'],
    'resenas' => [
        ['Andrés V.', 5, 'El afeitado con toalla caliente de Don Aurelio es otra cosa. Llegué a mi hora, me ofrecieron un café y salí como nuevo.', 'hace 4 días'],
        ['Matías O.', 5, 'Reservé por la web el sábado a las 11 con Joaquín. Degradado impecable y la sala de espera es un lujo.', 'hace 2 semanas'],
        ['Ricardo L.', 4, 'Caro comparado con una barbería de barrio, pero el servicio lo vale. Hay que reservar con varios días.', 'hace 1 mes'],
    ],
    'preguntas' => [
        ['¿Atienden sin hora?', 'Si hay un sillón libre, sí. Pero trabajamos con hora para que nadie espere: lo más seguro es reservar por la web o por WhatsApp.'],
        ['¿Puedo elegir a mi barbero?', 'Sí. En la reserva eliges a Don Aurelio, Tomás o Joaquín, o el primero disponible. Si tu barbero no está ese día, te avisamos y te ofrecemos otra hora.'],
        ['¿Con cuánta anticipación llego?', 'Cinco minutos antes basta. Así alcanzas a elegir tu bebida en la sala de espera.'],
        ['¿Qué pasa si no puedo ir?', 'Avísanos por WhatsApp con al menos tres horas de anticipación y movemos tu hora sin costo.'],
        ['¿Qué incluye ser Socio del Gabinete?', 'Dos visitas al mes con hora fija, prioridad en la agenda, bebida de la casa y 10 % de descuento en los productos para llevar. El plan trimestral sale más barato.'],
        ['¿Las bebidas tienen costo?', 'No. Con tu hora, una bebida de la carta va por cuenta de la casa. Todas son sin alcohol.'],
        ['¿Cómo se paga?', 'En el salón, con efectivo, débito, crédito o transferencia. Los planes de socio se pagan al comienzo de cada período.'],
        ['¿Venden tarjetas de regalo?', 'Sí, de cualquier servicio o monto. Pídela por WhatsApp y te la tenemos lista para retirar.'],
    ],
];
