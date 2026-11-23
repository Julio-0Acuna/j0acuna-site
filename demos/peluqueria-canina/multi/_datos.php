<?php
declare(strict_types=1);

// Bruma, spa canino (Lo Barnechea): negocio ficticio de la versión Multi-page.
// Personas, precios y reseñas inventados. Fotos: IDs de Unsplash (ver demos/peluqueria-canina/CREDITOS.md).
// Los textos no prometen tratamientos médicos: si hay una afección de piel u oídos, primero va su veterinario.
return [
    'nombre' => 'Bruma, spa canino',
    'corto' => 'Bruma',
    'categoria' => 'Spa y peluquería canina',
    'comuna' => 'Lo Barnechea',
    'eslogan' => 'Spa canino boutique en Lo Barnechea',
    'desde' => 2019,
    'nota' => 4.9,
    'horario' => [
        1 => null,
        2 => ['10:00', '19:00'],
        3 => ['10:00', '19:00'],
        4 => ['10:00', '19:00'],
        5 => ['10:00', '19:00'],
        6 => ['09:30', '18:00'],
        7 => null,
    ],
    // Horas de inicio de un ritual (una sala por perro).
    'horas' => ['10:00', '11:30', '13:00', '15:00', '16:30'],
    // Traslado ida y vuelta desde la casa: costo por comuna.
    'traslado' => [
        'Lo Barnechea' => 6990,
        'Vitacura' => 7990,
        'Las Condes' => 8990,
        'Colina (Chicureo)' => 9990,
        'Providencia' => 10990,
    ],
    'traslado_gratis' => 70000,
    'tamanos' => ['Pequeño · hasta 10 kg', 'Mediano · 10 a 25 kg', 'Grande · 25 a 40 kg', 'Gigante · más de 40 kg', 'Gato'],
    'pelos' => ['Corto', 'Largo', 'Rizado o lanudo', 'Doble manto', 'No estoy segura/o'],
    'equipo' => [
        ['nombre' => 'Antonia Valdés', 'inicial' => 'A', 'rol' => 'Directora y groomer', 'texto' => 'Doce años de oficio. Corte de raza a tijera, caniches y razas de pelo largo.'],
        ['nombre' => 'Matías Rojas', 'inicial' => 'M', 'rol' => 'Pelaje y razas grandes', 'texto' => 'Deslanado de temporada, goldens, samoyedos y perros de doble manto.'],
        ['nombre' => 'Josefina Paz', 'inicial' => 'J', 'rol' => 'Gatos y perros nerviosos', 'texto' => 'Manejo amable, pausas cuando hace falta y la sala silenciosa de los jueves.'],
    ],
    'resenas' => [
        ['Isidora M.', 5, 'Mi caniche volvió con el corte perfecto y oliendo a lavanda. El traslado llegó a la hora y me mandaron fotos durante el ritual.', 'hace 4 días'],
        ['Rodrigo F.', 5, 'Mi golden odia el secador y aquí lo secaron con calma, sin jaulas. El deslanado le cambió la vida en verano.', 'hace 3 semanas'],
        ['Catalina S.', 4, 'Precioso el lugar y muy cuidadosos con mi gata. Es más caro que una peluquería común, pero se nota en el trato.', 'hace 2 meses'],
    ],
    'fotos' => [
        'hero' => '1733964659477-35534815c626',
        'sillon' => '1791714887477-c71786e32fba',
        'tijeras' => '1791714642291-6d64088cc2ba',
        'toalla' => '1592887302112-b87aca16ad2c',
        'tina' => '1578584965679-79a238842dde',
        'siesta' => '1627376617965-b8c29ca91aca',
        'caniche' => '1652342516776-fcd4c54639c5',
        'mano' => '1583987303344-1f553edc7f11',
        'groomer' => '1625277743460-43716b93507a',
    ],
    // El ritual minuto a minuto (inicio).
    'ritual' => [
        ['00′', 'Recepción', 'Conversamos de su piel, su pelo y su carácter. Anotamos todo en su ficha.'],
        ['10′', 'Baño tibio', 'Agua a 32 °C con hidromasaje suave y el shampoo que le corresponde.'],
        ['35′', 'Mascarilla y masaje', 'Avena o karité según el pelo, con masaje de cepillo de goma.'],
        ['50′', 'Secado silencioso', 'Toalla, soplador a baja potencia y pausas. Nunca jaulas de secado.'],
        ['70′', 'Corte y terminación', 'Tijera, uñas con lima, oídos y una bruma suave de lavanda.'],
        ['90′', 'Foto y regreso', 'Te mandamos su foto y lo llevamos a la casa si pediste traslado.'],
    ],
    'categorias' => [
        'banos' => ['Rituales de baño', 'Agua tibia, el shampoo que le corresponde a su piel y secado silencioso.'],
        'tijera' => ['Corte y tijera', 'Corte de raza a tijera o a máquina, con terminación a mano.'],
        'pelaje' => ['Pelaje', 'Deslanado de temporada y desmotado, para doble manto y nudos.'],
        'detalles' => ['Detalles', 'Uñas, oídos, dientes y patitas: se suman a un ritual o van solos.'],
        'gatos' => ['Gatos', 'Los jueves, en la sala silenciosa y sin perros.'],
        'botica' => ['La botica', 'Lo que usamos en el spa, para seguir el cuidado en la casa.'],
    ],
    // Servicios: id => cat, nombre, desc, duracion, foto, alt, variantes (precio por tamaño u opción), destacado.
    'servicios' => [
        'ritual-bruma' => ['cat' => 'banos', 'nombre' => 'Ritual Bruma', 'desc' => 'Baño con hidromasaje tibio, mascarilla de avena, secado silencioso, cepillado y bruma de lavanda.', 'duracion' => '1 h 30 min', 'foto' => '1610461643300-a876423a9cd3', 'alt' => 'Perro cavalier durmiendo plácido sobre un cojín a rayas después del baño', 'variantes' => ['Pequeño' => 24990, 'Mediano' => 31990, 'Grande' => 39990, 'Gigante' => 49990], 'destacado' => 'I'],
        'bano-esencial' => ['cat' => 'banos', 'nombre' => 'Baño esencial', 'desc' => 'Baño tibio, acondicionador, secado y cepillado. Para mantenerlo regio entre un ritual y otro.', 'duracion' => '1 h', 'foto' => '1578584965679-79a238842dde', 'alt' => 'Cachorro golden con collar verde apoyado en el borde de la tina', 'variantes' => ['Pequeño' => 16990, 'Mediano' => 21990, 'Grande' => 27990, 'Gigante' => 34990]],
        'ozono' => ['cat' => 'banos', 'nombre' => 'Baño de burbujas de ozono', 'desc' => 'Microburbujas en la tina que limpian a fondo. Si tiene una afección de piel, primero su veterinario.', 'duracion' => '1 h 15 min', 'foto' => '1592887302112-b87aca16ad2c', 'alt' => 'Perro gris de pelo áspero envuelto en una toalla verde oscuro, mirando hacia un lado', 'variantes' => ['Pequeño' => 21990, 'Mediano' => 27990, 'Grande' => 33990, 'Gigante' => 41990]],
        'cachorro' => ['cat' => 'banos', 'nombre' => 'Primer baño de cachorro', 'desc' => 'Para cachorros de 3 a 6 meses con sus vacunas al día: baño corto, juego y muchos premios.', 'duracion' => '45 min', 'foto' => '1583987303344-1f553edc7f11', 'alt' => 'Cachorro caniche blanco recién peinado sobre la mano de su groomer', 'variantes' => ['Única' => 14990]],
        'corte-raza' => ['cat' => 'tijera', 'nombre' => 'Corte de raza a tijera', 'desc' => 'Incluye el Ritual Bruma. Caniches, bichón, schnauzer, westie y más, con el estándar de su raza o a tu gusto.', 'duracion' => '2 h 30 min', 'foto' => '1789846958504-3d731dfb33d4', 'alt' => 'Caniche blanco con corte de exposición y lazos rojos en las orejas, parado en el pasto', 'variantes' => ['Pequeño' => 39990, 'Mediano' => 49990, 'Grande' => 59990], 'destacado' => 'II'],
        'corte-cachorro' => ['cat' => 'tijera', 'nombre' => 'Corte osito', 'desc' => 'Incluye el baño. Pelo parejo y cara redonda, fácil de mantener en la casa.', 'duracion' => '2 h', 'foto' => '1651273427958-bf78352e39be', 'alt' => 'Caniche toy color damasco con corte osito, sobre fondo oscuro', 'variantes' => ['Pequeño' => 34990, 'Mediano' => 42990]],
        'higienico' => ['cat' => 'tijera', 'nombre' => 'Higiénico y patitas', 'desc' => 'Despeje de ojos, patas y zona íntima, sin baño. Ideal entre un corte y otro.', 'duracion' => '45 min', 'foto' => '1652342516776-fcd4c54639c5', 'alt' => 'Caniche gris con la cara y las orejas recién peinadas, en blanco y negro', 'variantes' => ['Pequeño' => 12990, 'Mediano' => 14990, 'Grande' => 16990, 'Gigante' => 18990]],
        'deslanado' => ['cat' => 'pelaje', 'nombre' => 'Deslanado de temporada', 'desc' => 'Incluye el baño. Sacamos el subpelo muerto con baño, soplador y carda, sin rapar: el doble manto lo protege del calor.', 'duracion' => '2 h', 'foto' => '1794123879942-8b472d993cca', 'alt' => 'Golden retriever feliz echado de espaldas en un sillón mientras lo cepillan', 'variantes' => ['Mediano' => 34990, 'Grande' => 44990, 'Gigante' => 54990], 'destacado' => 'III'],
        'desmotado' => ['cat' => 'pelaje', 'nombre' => 'Desmotado con paciencia', 'desc' => 'Desarmamos nudos y motas a mano. Si son demasiadas, te llamamos antes de cortar.', 'duracion' => 'cada 30 min', 'foto' => '1787813583412-48f6101e92e3', 'alt' => 'Groomer cepillando con cuidado a un pomerania blanco echado sobre una manta gris', 'variantes' => ['Cada 30 min' => 9990]],
        'unas' => ['cat' => 'detalles', 'nombre' => 'Uñas con lima', 'desc' => 'Corte y lima suave para que no rayen el piso ni se enganchen.', 'duracion' => '20 min', 'foto' => '1675430426271-d74b542f21e4', 'alt' => 'Perrito blanco de pelo esponjoso envuelto en una manta a cuadros mientras lo peinan', 'variantes' => ['Única' => 6990]],
        'oidos' => ['cat' => 'detalles', 'nombre' => 'Limpieza de oídos', 'desc' => 'Limpieza suave por fuera. Si vemos algo raro, te recomendamos ir a su veterinario.', 'duracion' => '15 min', 'foto' => '1612830565936-6388483d801b', 'alt' => 'Perro de pelo largo con las orejas levantadas por la mano de su dueña, en blanco y negro', 'variantes' => ['Única' => 5990]],
        'dientes' => ['cat' => 'detalles', 'nombre' => 'Cepillado de dientes', 'desc' => 'Con pasta para perros y cepillo de dedo. No reemplaza la limpieza dental del veterinario.', 'duracion' => '15 min', 'foto' => '1791714887477-c71786e32fba', 'alt' => 'Pomerania color crema con un juguete en el hocico, sentado en un sillón negro de peluquería', 'variantes' => ['Única' => 6990]],
        'aromaterapia' => ['cat' => 'detalles', 'nombre' => 'Masaje y bruma de lavanda', 'desc' => 'Quince minutos más de masaje con cepillo de goma y una bruma suave al terminar.', 'duracion' => '15 min', 'foto' => '1779899833023-d2f608715ffd', 'alt' => 'Perro blanco asomado bajo una toalla oscura, en blanco y negro', 'variantes' => ['Única' => 7990]],
        'gato-bano' => ['cat' => 'gatos', 'nombre' => 'Ritual felino', 'desc' => 'Baño tibio, secado con toalla y soplador silencioso, cepillado y uñas. Solo los jueves.', 'duracion' => '1 h 30 min', 'foto' => '1782927459467-d1c5b29f710b', 'alt' => 'Gato de pelo largo y ojos azules echado sobre un cuaderno', 'variantes' => ['Pelo corto' => 29990, 'Pelo largo' => 36990]],
        'gato-corte' => ['cat' => 'gatos', 'nombre' => 'Corte león o higiénico felino', 'desc' => 'Para gatos de pelo largo con nudos o que sufren el calor. Incluye el ritual felino.', 'duracion' => '1 h 45 min', 'foto' => '1625279138876-8910c2af9a30', 'alt' => 'Groomer sosteniendo a un gato de pelo largo blanco y café', 'variantes' => ['Única' => 44990]],
        'shampoo-avena' => ['cat' => 'botica', 'nombre' => 'Shampoo de avena 500 ml', 'desc' => 'El del Ritual Bruma: suave, sin perfume fuerte, para pieles sensibles.', 'duracion' => 'para la casa', 'foto' => '1783149173371-d852d86563c4', 'alt' => 'Dispensador de vidrio acanalado con bomba dorada junto a una ventana', 'variantes' => ['Única' => 14990]],
        'acondicionador' => ['cat' => 'botica', 'nombre' => 'Acondicionador desenredante 300 ml', 'desc' => 'Para pelo largo y rizado: menos nudos entre una visita y otra.', 'duracion' => 'para la casa', 'foto' => '1772038786507-647371ac686b', 'alt' => 'Dos botellas con bomba, una negra y una celeste, sobre una repisa oscura', 'variantes' => ['Única' => 12990]],
        'bruma-lavanda' => ['cat' => 'botica', 'nombre' => 'Bruma de lavanda 150 ml', 'desc' => 'Dos o tres disparos sobre el pelo seco. Aroma suave que dura el día.', 'duracion' => 'para la casa', 'foto' => '1631390179406-0bfe17e9f89d', 'alt' => 'Frascos morados con atomizador y gotario sobre fondo verde claro', 'variantes' => ['Única' => 11990]],
        'balsamo' => ['cat' => 'botica', 'nombre' => 'Bálsamo de almohadillas 60 g', 'desc' => 'Protege las patitas del pavimento caliente y del frío.', 'duracion' => 'para la casa', 'foto' => '1597931752949-98c74b5b159f', 'alt' => 'Botella blanca con bomba negra sobre una tela café arrugada', 'variantes' => ['Única' => 9990]],
    ],
    'preguntas' => [
        ['¿Usan jaulas de secado?', 'No. Secamos con toalla y soplador a baja potencia, con pausas, y siempre con alguien al lado. Ningún perro queda solo en una jaula.'],
        ['¿Cuántos perros atienden a la vez?', 'Uno por sala. Tenemos dos salas, así que nunca hay más de dos perros en el spa al mismo tiempo, y los jueves en la mañana son solo para gatos.'],
        ['¿Cómo funciona el traslado?', 'Lo buscamos en tu casa en un auto con jaula acolchada y aire acondicionado, y te lo devolvemos al terminar. Cuesta lo mismo de ida y vuelta según tu comuna, y es gratis en reservas sobre $70.000.'],
        ['Mi perro es nervioso, ¿lo pueden atender?', 'Sí. Cuéntanos en la reserva y le damos la primera hora del día, con el spa en silencio. Si en algún momento lo vemos muy estresado, paramos y te llamamos.'],
        ['¿Hacen tratamientos para la piel o los oídos?', 'No somos veterinarios. Usamos productos suaves para pieles sensibles, pero si tiene heridas, hongos o una otitis, primero debe verlo su veterinario.'],
        ['¿Por qué no rapan a los perros de doble manto?', 'El doble manto los protege del calor y del sol. Rapar lo daña y a veces no vuelve a crecer igual. Les hacemos deslanado: sacamos el subpelo muerto y quedan frescos.'],
        ['¿Puedo quedarme mientras lo atienden?', 'Preferimos que no: la mayoría de los perros se relaja más sin sus dueños a la vista. Te mandamos fotos durante el ritual.'],
        ['¿Cómo pago?', 'Con débito, crédito o transferencia al terminar. Si cancelas con menos de 24 horas, cobramos el traslado si ya estaba en camino.'],
    ],
];
