<?php
declare(strict_types=1);

// Matiz, estudio de color (Providencia): negocio ficticio de la versión Multi-page.
// Fotos: IDs de Unsplash (ver demos/peluqueria/CREDITOS.md). Productos genéricos, sin marcas.
return [
    'nombre' => 'Matiz',
    'nombre_largo' => 'Matiz, estudio de color',
    'categoria' => 'Salón de belleza · estudio de color',
    'comuna' => 'Providencia',
    'eslogan' => 'Estudio de color y balayage',
    'desde' => 2017,
    'nota' => 4.9,
    // Lunes cerrado: el equipo se capacita.
    'horario' => [
        1 => null,
        2 => ['10:00', '20:00'],
        3 => ['10:00', '20:00'],
        4 => ['10:00', '20:00'],
        5 => ['10:00', '20:00'],
        6 => ['09:30', '18:00'],
        7 => null,
    ],
    // Horas de inicio que se ofrecen al reservar (los procesos largos empiezan temprano).
    'horas' => ['10:00', '11:30', '13:00', '14:30', '16:00', '17:30'],
    'coloristas' => [
        'antonia' => ['nombre' => 'Antonia Rivas', 'corto' => 'Antonia', 'rol' => 'Directora de color', 'fuerte' => 'Balayage y rubios que crecen sin marcar línea', 'anios' => 14],
        'matias' => ['nombre' => 'Matías Ojeda', 'corto' => 'Matías', 'rol' => 'Colorista', 'fuerte' => 'Platinados, decoloraciones y transición a canas', 'anios' => 9],
        'fernanda' => ['nombre' => 'Fernanda Lagos', 'corto' => 'Fernanda', 'rol' => 'Colorista', 'fuerte' => 'Cobrizos, rojos y colores fantasía', 'anios' => 7],
        'josefa' => ['nombre' => 'Josefa Mella', 'corto' => 'Josefa', 'rol' => 'Estilista', 'fuerte' => 'Corte, forma y pelo rizado', 'anios' => 11],
    ],
    'categorias' => [
        'aclarados' => ['Balayage y mechas', 'Luz donde la pide tu cara, con raíz difuminada para que el crecimiento no se note.'],
        'color' => ['Color y canas', 'Tintes sin amoníaco, cobres que no se apagan y la transición a canas, con calma.'],
        'corte' => ['Corte y forma', 'El corte que hace lucir el color: con diagnóstico de textura y secado incluido.'],
        'tratamientos' => ['Tratamientos', 'Para el pelo aclarado o cansado. Se suman a cualquier servicio.'],
        'casa' => ['Cuidado en casa', 'Para que el color dure: los mismos productos del estudio, sin marcas de por medio.'],
    ],
    // id => datos. min: duración en minutos (0 = producto). variantes: [largo => precio desde]. firma: aparece en el inicio.
    'servicios' => [
        'balayage' => ['cat' => 'aclarados', 'nombre' => 'Balayage a mano alzada', 'desc' => 'Aclarado pintado a mano, raíz difuminada y matiz a medida. Crece sin marcar línea.', 'foto' => '1785456283212-338f5bc44d50', 'alt' => 'Dos mujeres de espalda con el pelo largo ondulado, una rubia y otra con balayage miel', 'min' => 240, 'variantes' => ['Pelo corto' => 89990, 'Pelo medio' => 119990, 'Pelo largo' => 149990], 'firma' => true],
        'babylights' => ['cat' => 'aclarados', 'nombre' => 'Babylights con papel', 'desc' => 'Mechas finísimas con papel para un rubio luminoso y natural, como de verano.', 'foto' => '1605980625982-b128a7e7fde2', 'alt' => 'Cabeza llena de mechas con papel de aluminio, vista de espalda', 'min' => 210, 'variantes' => ['Pelo corto' => 99990, 'Pelo medio' => 129990, 'Pelo largo' => 159990], 'firma' => false],
        'rostro' => ['cat' => 'aclarados', 'nombre' => 'Iluminación de rostro', 'desc' => 'Dos mechones claros que enmarcan la cara. El primer paso ideal si nunca te has aclarado.', 'foto' => '1507101105822-7472b28e22ac', 'alt' => 'Mujer con el pelo castaño y puntas claras que enmarcan su cara', 'min' => 90, 'variantes' => ['Única' => 49990], 'firma' => false],
        'platinado' => ['cat' => 'aclarados', 'nombre' => 'Rubio platinado', 'desc' => 'Decoloración progresiva con protector de enlaces y matiz frío. Incluye prueba de mechón.', 'foto' => '1617391654484-2894196c2cc9', 'alt' => 'Colorista con guantes negros aplica decolorante en un pelo rubio', 'min' => 300, 'variantes' => ['Pelo corto' => 149990, 'Pelo medio' => 189990, 'Pelo largo' => 229990], 'firma' => false],
        'raiz' => ['cat' => 'color', 'nombre' => 'Retoque de raíz', 'desc' => 'Tinte sin amoníaco en la raíz hasta 3 cm, con baño de brillo en medios y puntas.', 'foto' => '1707720531504-ce087725861a', 'alt' => 'Colorista pinta con brocha un mechón sobre papel de aluminio', 'min' => 90, 'variantes' => ['Única' => 39990], 'firma' => false],
        'global' => ['cat' => 'color', 'nombre' => 'Color global', 'desc' => 'Cambio de tono completo, de raíz a puntas, con diagnóstico previo de tu pelo.', 'foto' => '1596911647169-7085321c81b8', 'alt' => 'Pelo largo castaño oscuro con ondas, visto de espalda', 'min' => 120, 'variantes' => ['Pelo corto' => 54990, 'Pelo medio' => 64990, 'Pelo largo' => 74990], 'firma' => false],
        'cobrizos' => ['cat' => 'color', 'nombre' => 'Cobrizos y rojos', 'desc' => 'Del cobre suave al rojo intenso, con pigmentos que no se apagan en dos lavados.', 'foto' => '1614020863825-28a0bb7e3c3c', 'alt' => 'Pelo cobrizo recogido en un nudo, de cerca', 'min' => 150, 'variantes' => ['Pelo corto' => 64990, 'Pelo medio' => 79990, 'Pelo largo' => 94990], 'firma' => true],
        'fantasia' => ['cat' => 'color', 'nombre' => 'Color fantasía', 'desc' => 'Rosa, lila o lo que imagines, sobre una base ya aclarada. Te enseñamos a mantenerlo.', 'foto' => '1769007783470-cf53b045bab1', 'alt' => 'Mujer de espalda con el pelo corto ondulado teñido de rosa', 'min' => 180, 'variantes' => ['Pelo corto' => 74990, 'Pelo medio' => 94990, 'Pelo largo' => 114990], 'firma' => false],
        'canas' => ['cat' => 'color', 'nombre' => 'Transición a canas', 'desc' => 'Te acompañamos a dejar el tinte: mechas grises y matiz para que el cambio sea parejo.', 'foto' => '1692801439915-ef9194bf0951', 'alt' => 'Mujer mayor sonriente con el pelo canoso corto y peinado', 'min' => 180, 'variantes' => ['Pelo corto' => 79990, 'Pelo medio' => 99990, 'Pelo largo' => 119990], 'firma' => true],
        'matiz' => ['cat' => 'color', 'nombre' => 'Matiz y brillo', 'desc' => 'Neutraliza amarillos o anaranjados y devuelve el brillo entre un color y otro.', 'foto' => '1605980625600-88b46abafa8d', 'alt' => 'Pelo largo con ondas en tono ceniza, visto de espalda', 'min' => 45, 'variantes' => ['Única' => 29990], 'firma' => false],
        'corte' => ['cat' => 'corte', 'nombre' => 'Corte y forma', 'desc' => 'Corte con diagnóstico de textura, lavado y secado. Pensado para que el color luzca.', 'foto' => '1647462741351-4e7a5e7317c7', 'alt' => 'Estilista corta con tijera un pelo corto mojado, en blanco y negro', 'min' => 60, 'variantes' => ['Pelo corto' => 29990, 'Pelo medio' => 34990, 'Pelo largo' => 39990], 'firma' => false],
        'ondas' => ['cat' => 'corte', 'nombre' => 'Brushing u ondas', 'desc' => 'Secado con cepillo o ondas al agua con tenaza, para salir lista a donde vayas.', 'foto' => '1629397685944-7073f5589754', 'alt' => 'Estilista hace ondas con una tenaza en el pelo de una clienta', 'min' => 45, 'variantes' => ['Pelo corto' => 19990, 'Pelo medio' => 24990, 'Pelo largo' => 29990], 'firma' => false],
        'rizos' => ['cat' => 'corte', 'nombre' => 'Corte para rizos', 'desc' => 'Corte en seco, rizo por rizo, con definición y la rutina explicada paso a paso.', 'foto' => '1783512785019-8bcb76a68d5a', 'alt' => 'Mujer de espalda mostrando su pelo largo, rizado y oscuro', 'min' => 90, 'variantes' => ['Única' => 44990], 'firma' => false],
        'enlaces' => ['cat' => 'tratamientos', 'nombre' => 'Reconstrucción de enlaces', 'desc' => 'Tratamiento en tres pasos que repara el pelo aclarado desde adentro.', 'foto' => '1769029257460-7052dc5c1731', 'alt' => 'Mujer revisa las puntas de su pelo con una lupa', 'min' => 40, 'variantes' => ['Única' => 24990], 'firma' => false],
        'hidratacion' => ['cat' => 'tratamientos', 'nombre' => 'Hidratación con vapor', 'desc' => 'Mascarilla, vapor y masaje de cuero cabelludo en el lavacabezas.', 'foto' => '1717160675643-53a7a2ebaa9f', 'alt' => 'Clienta relajada mientras le lavan el pelo en el lavacabezas', 'min' => 45, 'variantes' => ['Única' => 19990], 'firma' => false],
        'shampoo-color' => ['cat' => 'casa', 'nombre' => 'Shampoo para pelo teñido 300 ml', 'desc' => 'Sin sulfatos y con filtro UV. Alarga la vida del color.', 'foto' => '1655892810227-c0cffe1d9717', 'alt' => 'Botella blanca con dosificador apoyada en una pared con sombras', 'min' => 0, 'variantes' => ['Única' => 14990], 'firma' => false],
        'shampoo-violeta' => ['cat' => 'casa', 'nombre' => 'Shampoo matizador violeta 300 ml', 'desc' => 'Una vez por semana en rubios y canas: neutraliza el tono amarillo.', 'foto' => '1718205638706-1517259a63d4', 'alt' => 'Tres botellas blancas sin etiqueta sobre fondo gris', 'min' => 0, 'variantes' => ['Única' => 13990], 'firma' => false],
        'mascarilla' => ['cat' => 'casa', 'nombre' => 'Mascarilla de enlaces 200 ml', 'desc' => 'Cinco minutos, una vez por semana. Para pelo aclarado o decolorado.', 'foto' => '1763503834047-ac85c4105c0b', 'alt' => 'Pote abierto de mascarilla blanca sobre una roca', 'min' => 0, 'variantes' => ['Única' => 16990], 'firma' => false],
        'serum' => ['cat' => 'casa', 'nombre' => 'Sérum de brillo 50 ml', 'desc' => 'Dos gotas en medios y puntas para sellar y dar brillo sin engrasar.', 'foto' => '1793100123538-396050d9f5c1', 'alt' => 'Frasco gotario de vidrio ámbar con luz cálida de tarde', 'min' => 0, 'variantes' => ['Única' => 15990], 'firma' => false],
    ],
    // Carta de tonos del inicio: nombre, colores del mechón (claro y oscuro) y servicio que lleva a ese tono.
    'tonos' => [
        ['Miel', '#e2b26f', '#a8692e', 'balayage'],
        ['Cobre', '#d77a3e', '#8c3a17', 'cobrizos'],
        ['Chocolate', '#7a4a33', '#3a2219', 'global'],
        ['Ceniza', '#c9c1b4', '#857c70', 'matiz'],
        ['Platino', '#f3ede2', '#cfc4b0', 'platinado'],
        ['Rosa cuarzo', '#f0b9bd', '#c27a83', 'fantasia'],
        ['Plata', '#e4e4e2', '#9c9c9a', 'canas'],
    ],
    'trabajos' => [
        ['1554519934-e32b1629d9ee', 'Pelo largo rubio con balayage y ondas suaves, visto de espalda', 'Rubio miel · Antonia'],
        ['1638064432604-8da1fc75de09', 'Pelo con ondas en tono caramelo, de perfil', 'Caramelo · Antonia'],
        ['1586266195531-76cfa365cb43', 'Mujer con corte bob rojo intenso y flequillo', 'Rojo carmín · Fernanda'],
        ['1574773004910-1eeaabb62b55', 'Pelo largo y liso color lila, de perfil', 'Lila · Fernanda'],
        ['1785456390070-9960e3997066', 'Mujer con el pelo largo en tono ceniza peinándose frente a una ventana', 'Ceniza · Matías'],
    ],
    // Productos con despacho a domicilio (costo por comuna).
    'comunas_despacho' => [
        'Providencia' => 2990,
        'Ñuñoa' => 3490,
        'Santiago' => 3490,
        'Las Condes' => 3990,
        'Vitacura' => 4490,
        'La Reina' => 4490,
    ],
    'resenas' => [
        ['Daniela C.', 5, 'Llevaba años con el mismo tinte oscuro. Antonia me hizo balayage en dos sesiones y nadie cree que es el mismo pelo.', 'hace 1 semana'],
        ['Verónica M.', 5, 'Hice la transición a canas con Matías. Me explicó todo, sin apuro, y hoy me encanta mi pelo plata.', 'hace 1 mes'],
        ['Catalina R.', 4, 'El cobre sigue intenso después de seis semanas. Es caro, pero se nota el trabajo. Hay que reservar con tiempo.', 'hace 2 meses'],
    ],
    'preguntas' => [
        ['¿Cuánto demora un balayage?', 'Entre tres horas y media y cinco, según el largo y el color de partida. Por eso la mayoría de los procesos largos empiezan a las 10:00 o a las 11:30. Al reservar ves el tiempo estimado de tu cita.'],
        ['¿Por qué el precio dice «desde»?', 'Porque depende del largo, de la cantidad de pelo y de si hay color previo. En el diagnóstico te decimos el valor exacto antes de empezar; si sube, tú decides si seguimos.'],
        ['¿Qué es la prueba de mechón?', 'Aplicamos el producto en un mechón escondido y detrás de la oreja 48 horas antes. Así vemos cómo reacciona tu pelo y tu piel. Es gratis y la recomendamos para decoloraciones y primeras veces.'],
        ['Me teñí en casa, ¿igual pueden aclararme?', 'Sí, pero puede tomar más de una sesión. Cuéntanos qué tinte usaste y cuándo en el formulario de reserva o por WhatsApp, con una foto con luz natural.'],
        ['¿Qué pasa si tengo que cancelar?', 'Avísanos con 24 horas por WhatsApp y cambiamos la hora sin costo. Los procesos de más de tres horas se confirman con un abono que te devolvemos si cancelas a tiempo.'],
        ['¿Los productos de la tienda tienen marca?', 'Son los productos genéricos que usamos en el estudio, elegidos por su fórmula. Los retiras el día de tu hora o te los enviamos a domicilio.'],
    ],
    'fotos' => [
        'hero' => '1793328556262-79fab6efd759',
        'hero_b' => '1673545931906-dc923a11a5a4',
        'hero_c' => '1602549179763-ce6c9df961b7',
        'estudio' => '1781450090585-1a511b7066d9',
        'lavado' => '1637777269308-6a072f24e8a4',
        'antonia' => '1774660810744-fd0fbbf99765',
        'mechas' => '1712213396688-c6f2d536671f',
    ],
];
