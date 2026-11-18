<?php
declare(strict_types=1);

// Peluquería Yanet (Independencia): negocio ficticio de la versión Simple-page.
// Fotos: IDs de Unsplash (ver demos/peluqueria/CREDITOS.md). Productos genéricos, sin marcas.
return [
    'nombre' => 'Peluquería Yanet',
    'categoria' => 'Peluquería',
    'comuna' => 'Independencia',
    'desde' => 2011,
    'nota' => 4.8,
    'horario' => [
        1 => ['10:00', '20:00'],
        2 => ['10:00', '20:00'],
        3 => ['10:00', '20:00'],
        4 => ['10:00', '20:00'],
        5 => ['10:00', '20:30'],
        6 => ['09:30', '19:00'],
        7 => null,
    ],
    // Bloques que se ofrecen al reservar (los sábados cerramos a las 19:00).
    'bloques' => [
        'manana' => 'Mañana · 10:00 a 13:00',
        'mediodia' => 'Mediodía · 13:00 a 16:00',
        'tarde' => 'Tarde · 16:00 a 19:00',
    ],
    'equipo' => [
        'yanet' => ['nombre' => 'Yanet', 'rol' => 'Dueña · brushing y color', 'texto' => 'Llegó de Santo Domingo en 2009 con un secador y muchas ganas. Su brushing tiene fila los viernes.', 'foto' => '1761839256840-7780a45b85dc', 'alt' => 'Yanet, sonriente, apunta con unas tijeras en el salón', 'pos' => '50% 22%'],
        'karen' => ['nombre' => 'Karen', 'rol' => 'Cortes y alisados', 'texto' => 'Quince años cortando en el barrio. Te dice con cariño si ese corte de la foto te va a quedar.', 'foto' => '1595475716260-0f2c35f5a40f', 'alt' => 'Karen sentada en un sillón del salón, sonriendo', 'pos' => '55% 30%'],
        'dani' => ['nombre' => 'Dani', 'rol' => 'Peinados, uñas y cejas', 'texto' => 'La de los peinados de matrimonio y las uñas que duran tres semanas sin saltarse.', 'foto' => '1675034741473-afed58a142e8', 'alt' => 'Dani peinando a una clienta con el pelo largo y oscuro', 'pos' => '40% 25%'],
    ],
    'categorias' => [
        'pelo' => 'Corte y peinado',
        'color' => 'Color y alisado',
        'manos' => 'Uñas, cejas y pestañas',
    ],
    // id => [categoría, nombre, detalle, duración en minutos, precio desde]
    'servicios' => [
        'corte-mujer' => ['pelo', 'Corte de mujer', 'Lavado, corte y secado natural. Si traes una foto, mejor.', 45, 12000],
        'corte-hombre' => ['pelo', 'Corte de hombre', 'Tijera o máquina, con lavado y peinado.', 30, 8000],
        'brushing' => ['pelo', 'Brushing dominicano', 'Lavado, mascarilla, rolos y secado con cepillo: liso, con brillo y movimiento.', 75, 15000],
        'peinado' => ['pelo', 'Peinado de fiesta', 'Ondas, trenza o recogido para matrimonios, cumpleaños y fiestas.', 60, 25000],
        'trenzas' => ['pelo', 'Trenzas', 'Pegadas al cuero o sueltas tipo box braids. El tiempo depende del largo.', 180, 25000],
        'raiz' => ['color', 'Retoque de raíz', 'Tinte en la raíz hasta 3 cm. Cubre canas y empareja el tono.', 90, 22000],
        'color' => ['color', 'Color completo', 'Tinte de raíz a puntas con lavado y secado.', 120, 32000],
        'mechas' => ['color', 'Mechas o visos', 'Con gorra o papel, más matiz para que no queden amarillas.', 180, 45000],
        'alisado' => ['color', 'Alisado de keratina', 'Pelo liso y sin frizz por tres a cuatro meses. Sin formol.', 180, 45000],
        'hidratacion' => ['color', 'Hidratación profunda', 'Mascarilla con vapor y masaje. Ideal después del color.', 40, 12000],
        'manicure' => ['manos', 'Manicure con esmaltado permanente', 'Limado, cutículas y esmalte que dura hasta tres semanas.', 60, 13000],
        'pedicure' => ['manos', 'Pedicure', 'Remojo, limado, cutículas, piedra pómez y esmaltado.', 60, 15000],
        'cejas' => ['manos', 'Perfilado de cejas', 'Con pinza o hilo, según tu piel.', 20, 5000],
        'pestanas' => ['manos', 'Lifting de pestañas', 'Curvatura y tinte. Dura de seis a ocho semanas.', 60, 18000],
    ],
    // Destacados con foto (en este orden).
    'destacados' => [
        'brushing' => ['foto' => '1580618672591-eb180b1a973f', 'alt' => 'Estilista secando con cepillo redondo el pelo de una clienta', 'nota' => '¡El de los viernes!'],
        'trenzas' => ['foto' => '1702236240794-58dc4c6895e5', 'alt' => 'Clienta sonriente mientras le hacen trenzas pegadas', 'nota' => 'Con o sin extensiones'],
        'raiz' => ['foto' => '1707979577466-2d6109c68a45', 'alt' => 'Dos estilistas con guantes aplican tinte en el pelo de una clienta', 'nota' => 'Adiós canas'],
        'manicure' => ['foto' => '1457972729786-0411a3b2b626', 'alt' => 'Manicurista pintando las uñas de una clienta con esmalte burdeo', 'nota' => 'Dura 3 semanas'],
    ],
    // Promociones "escritas con plumón en el espejo".
    'promos' => [
        ['Martes y miércoles', 'Brushing a $12.000'],
        ['Corte + brushing', '$22.000 todos los días'],
        ['Mayores de 60', '15% en corte y color'],
    ],
    'galeria' => [
        ['1770419252624-e14e9f9de640', 'Mujer con trenzas largas y lentes de sol posando'],
        ['1747848214256-34a8cd21abac', 'Mujer con el pelo canoso y rizado, riendo'],
        ['1724863447921-1828f31227d0', 'Pelo rojo brillante enrollado en un cepillo redondo'],
        ['1485290334039-a3c69043e517', 'Mujer con corte pixie rubio'],
        ['1554519880-ffe46861d570', 'Pelo castaño largo con ondas y reflejos, visto de espalda'],
        ['1628292180187-3bac5c5b3cb6', 'Mujer con flequillo recto y reflejos claros'],
    ],
    'productos' => [
        ['Shampoo sin sulfatos 500 ml', 'Limpia sin resecar. Para pelo teñido o con keratina.', 9990, '1704307068094-c2c88c467014', 'Botella con dosificador sobre una mesa clara'],
        ['Mascarilla reparadora 250 g', 'Una vez por semana, cinco minutos y enjuagar.', 8990, '1779437141537-62577b216307', 'Pote de vidrio esmerilado junto a su caja, sobre una superficie clara'],
        ['Aceite de argán 50 ml', 'Dos gotas en las puntas, con el pelo húmedo o seco.', 7990, '1781344116880-029945a42b75', 'Frasco gotario de vidrio ámbar con etiqueta en blanco'],
        ['Protector térmico 200 ml', 'Antes del secador o la plancha. Protege hasta 220 °C.', 8490, '1636035999343-5c9b4c7aef76', 'Botella café de tapa dorada sobre fondo cobrizo'],
    ],
    'resenas' => [
        ['Marisela P.', 5, 'El brushing de Yanet me dura toda la semana. Reservé por la página un viernes en la tarde y no tuve que esperar.', 'hace 4 días'],
        ['Jorge A.', 5, 'Me corto el pelo con Karen hace años. Rápido, buen precio y siempre con conversación.', 'hace 3 semanas'],
        ['Carmen L.', 4, 'Me hice el retoque de raíz y quedó parejo. El sábado se llena, mejor pedir hora.', 'hace 1 mes'],
    ],
    'fotos' => [
        'salon' => '1521590832167-7bcbfaa6381f',
    ],
];
