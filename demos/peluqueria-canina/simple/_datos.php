<?php
declare(strict_types=1);

// Peluquería Canina La Chasquilla (La Granja): negocio ficticio de la versión Simple-page.
// Personas, precios y reseñas inventados. Fotos: IDs de Unsplash (ver demos/peluqueria-canina/CREDITOS.md).
return [
    'nombre' => 'Peluquería Canina La Chasquilla',
    'corto' => 'La Chasquilla',
    'categoria' => 'Peluquería canina',
    'comuna' => 'La Granja',
    'desde' => 2012,
    'nota' => 4.9,
    'horario' => [
        1 => ['10:00', '19:00'],
        2 => ['10:00', '19:00'],
        3 => ['10:00', '19:00'],
        4 => ['10:00', '19:00'],
        5 => ['10:00', '19:00'],
        6 => ['10:00', '14:00'],
        7 => null,
    ],
    // Bloques de llegada para la reserva. Los sábados solo hay mañana.
    'bloques' => [
        'manana' => 'Mañana · llegas entre 10:00 y 11:30',
        'tarde' => 'Tarde · llegas entre 15:00 y 16:30',
    ],
    // Tamaños: clave => [nombre, peso, ejemplos].
    'tamanos' => [
        'pequeno' => ['Pequeño', 'hasta 10 kg', 'yorkie, poodle toy, shih tzu'],
        'mediano' => ['Mediano', '10 a 25 kg', 'schnauzer, cocker, quiltro mediano'],
        'grande' => ['Grande', '25 a 40 kg', 'golden, labrador, ovejero'],
        'gigante' => ['Gigante', 'más de 40 kg', 'san bernardo, terranova, gran danés'],
    ],
    // Tipo de pelo: clave => [nombre, qué hacemos, recargo].
    'pelos' => [
        'corto' => ['Corto', 'Baño y cepillado para sacar el pelo muerto.', 0],
        'largo' => ['Largo', 'Más shampoo, más secado y peinado con cepillo.', 2000],
        'rizado' => ['Rizado o lanudo', 'Secado estirado y corte a tijera.', 2000],
        'doble' => ['Doble manto', 'Te recomendamos sumar el deslanado.', 0],
    ],
    // Servicios: id => [nombre, detalle, precios por tamaño (null = no aplica), nota del precio].
    // 'gato' es el precio para gatos (se atienden los martes en la mañana).
    'servicios' => [
        'bano' => ['Baño completo', 'Shampoo según su piel, enjuague con agua tibia, secado a mano, cepillado y colonia suave.', ['pequeno' => 9990, 'mediano' => 13990, 'grande' => 17990, 'gigante' => 23990, 'gato' => 14990], ''],
        'corte' => ['Baño y corte de raza', 'Todo el baño más el corte de su raza o el que tú quieras, a máquina y tijera.', ['pequeno' => 15990, 'mediano' => 21990, 'grande' => 27990, 'gigante' => 34990, 'gato' => 19990], ''],
        'higienico' => ['Baño y corte higiénico', 'Baño más el despeje de patas, ojos y zona íntima. Ideal entre un corte y otro.', ['pequeno' => 12990, 'mediano' => 16990, 'grande' => 20990, 'gigante' => 25990, 'gato' => 16990], ''],
        'deslanado' => ['Deslanado', 'Para perros de doble manto: sacamos el pelo muerto de abajo con baño y secado con soplador.', ['pequeno' => null, 'mediano' => 18990, 'grande' => 24990, 'gigante' => 31990, 'gato' => 17990], ''],
        'spa' => ['Spa e hidratación', 'Mascarilla hidratante y masaje con el cepillo de goma. Se suma a cualquier baño.', ['pequeno' => 4990, 'mediano' => 5990, 'grande' => 6990, 'gigante' => 7990, 'gato' => 4990], 'se suma al baño'],
        'desmotado' => ['Desmotado', 'Desarmamos nudos y motas con paciencia. Si hay demasiadas, te llamamos antes de rapar.', ['pequeno' => 3000, 'mediano' => 3000, 'grande' => 3000, 'gigante' => 3000, 'gato' => 3000], 'cada 15 min'],
        'unas' => ['Corte de uñas', 'Con lima al final para que no rayen el piso. Sin hora: pasa cuando quieras.', ['pequeno' => 3990, 'mediano' => 3990, 'grande' => 4990, 'gigante' => 4990, 'gato' => 3990], ''],
        'oidos' => ['Limpieza de oídos', 'Limpieza suave por fuera. Si vemos algo raro, te recomendamos ir al veterinario.', ['pequeno' => 2990, 'mediano' => 2990, 'grande' => 2990, 'gigante' => 2990, 'gato' => 2990], ''],
    ],
    // Las tarjetas grandes con foto (id del servicio => foto, alt, sello).
    'destacados' => [
        'bano' => ['foto' => '1672426637959-49f39230ad7e', 'alt' => 'Perro de pelo dorado cubierto de espuma dentro de la tina, con la cola en alto', 'sello' => 'El más pedido'],
        'corte' => ['foto' => '1694372550345-9149dddc390f', 'alt' => 'Perrito de pelo largo y desordenado mientras lo peinan con un cepillo', 'sello' => 'Con foto de referencia'],
        'deslanado' => ['foto' => '1547660351-51b6d384015f', 'alt' => 'Perro blanco de pelo abundante echado en un sillón al sol', 'sello' => 'Doble manto'],
        'spa' => ['foto' => '1629030502047-b6ac6d4a78b6', 'alt' => 'Perrito mojado envuelto en una toalla celeste, lamiéndose la nariz', 'sello' => 'Se suma al baño', 'pos' => '50% 14%'],
    ],
    'promesas' => [
        'Un perro a la vez en la tina',
        'Secado a mano, sin jaulas',
        'Te mandamos la foto por WhatsApp',
        'Precios por tamaño a la vista',
    ],
    // La visita, paso a paso.
    'pasos' => [
        ['Nos cuentas', 'Cómo le gusta el corte, si tiene alergias o si le asusta el secador.'],
        ['Baño tibio', 'Shampoo según su piel: avena para piel sensible y neutro para el resto.'],
        ['Secado a mano', 'Con toalla y secador a temperatura baja. Nunca lo dejamos solo en una jaula.'],
        ['Corte, uñas y oídos', 'Máquina y tijera, con un premio cada tanto para que lo pase bien.'],
        ['Foto y a la casa', 'Te escribimos por WhatsApp con su foto para que lo vengas a buscar.'],
    ],
    // Galería "salieron regios": [foto, alt, nombre, detalle].
    'galeria' => [
        ['1527526029430-319f10814151', 'Shih tzu blanco y negro con un lacito rojo en la cabeza, sacando la lengua', 'Pelusa', 'shih tzu · corte cachorro y lacito'],
        ['1772437883387-8076cf660c65', 'Poodle blanco recién peinado con pañoleta celeste y corbatita, sentado en un piso negro', 'Copito', 'poodle · corte osito'],
        ['1596490638517-b02cb65ade2c', 'Perro tricolor sonriente con una pañoleta roja, echado sobre fondo blanco', 'Rocky', 'quiltro · baño y deslanado'],
        ['1588112141571-959b20dbf541', 'Perro carlino mojado apoyado en el borde de la tina', 'Gordo', 'carlino · baño y uñas'],
        ['1581562324420-eff2f5aaa4b5', 'Cachorro poodle blanco con collar celeste levantando una pata', 'Mota', 'poodle · su primer baño'],
        ['1579112902044-211d42c6a4bb', 'Perro blanco y negro con chaqueta de cuero y pañoleta fucsia', 'Toto', 'border collie · baño y spa'],
    ],
    'fotos' => [
        'hero' => '1576033008321-bc748ab54838',
        'visita' => '1672426637981-a40a4a6dd6ee',
        'gato' => '1625279138836-e7311d5c863a',
        'cierre' => '1680374642616-c81406e974bd',
    ],
    'resenas' => [
        ['Daniela P.', 5, 'Al Bruno le tiene miedo el secador y la Fernanda lo secó con toalla y paciencia. Me mandaron la foto por WhatsApp y estaba regio.', 'hace 6 días'],
        ['Don Luis M.', 5, 'Llevo a mi golden hace cuatro años. El precio es el de la pizarra, sin sorpresas, y siempre vuelve oliendo rico.', 'hace 3 semanas'],
        ['Camila R.', 4, 'Muy cariñosas con la Pelusa y el lacito le encantó. Los sábados se llena, así que hay que pedir hora con tiempo.', 'hace 1 mes'],
    ],
];
