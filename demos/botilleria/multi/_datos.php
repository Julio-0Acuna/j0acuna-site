<?php
declare(strict_types=1);

// Cava Ocre (Santiago Centro): negocio ficticio de la versión Multi-page.
// Tienda de vinos y destilados de especialidad con catas. Productos genéricos, sin marcas.
// Fotos: IDs de Unsplash (ver demos/botilleria/CREDITOS.md).

/** Precio de la caja de 6 botellas: 10 % de descuento, redondeado a la centena y terminado en 90. */
$caja = static fn (int $botella): int => (int) (round($botella * 6 * 0.9 / 100) * 100 - 10);
/** Variantes de un vino: botella sola o caja de 6. */
$vino = static fn (int $botella): array => ['Botella' => $botella, 'Caja de 6' => $caja($botella)];

return [
    'nombre' => 'Cava Ocre',
    'categoria' => 'Tienda de vinos y destilados',
    'comuna' => 'Santiago Centro',
    'eslogan' => 'Vinos y destilados de especialidad, con catas cada semana',
    'nota' => 4.9,
    'horario' => [
        1 => ['15:00', '20:30'],
        2 => ['11:00', '20:30'],
        3 => ['11:00', '20:30'],
        4 => ['11:00', '22:00'],
        5 => ['11:00', '21:30'],
        6 => ['11:00', '21:30'],
        7 => null,
    ],
    'resenas' => [
        ['Ignacia R.', 5, 'Fui a la cata de Carménère con una amiga: cinco vinos, buen pan, agua y explicaciones sin pretensiones. Salimos con dos botellas que jamás habríamos elegido solas.', 'hace 5 días'],
        ['Tomás A.', 5, 'Les pedí un regalo para mi suegro con presupuesto de $30.000 y me armaron una caja con tarjeta escrita a mano. Llegó al día siguiente.', 'hace 1 mes'],
        ['Paula G.', 4, 'Mucha variedad de valles chicos y destilados chilenos. Los jueves de cata se llena: hay que reservar con tiempo.', 'hace 2 meses'],
    ],
    'comunas_despacho' => [
        'Santiago' => 2990,
        'Providencia' => 3490,
        'Recoleta' => 3490,
        'Independencia' => 3490,
        'Ñuñoa' => 3990,
        'Estación Central' => 3990,
        'Las Condes' => 4990,
    ],
    'fotos' => [
        'hero' => '1724882207681-9e7e8c3dd45c',
        'cava' => '1672835423473-a70011cf49de',
        'sala' => '1609238000857-303bf54099b1',
        'cata_servir' => '1737904942495-8612f6b923a6',
        'cata_grupo' => '1764236307312-6ead0505ad3a',
        'copa_corchos' => '1729100889311-816a197db333',
        'regalo' => '1764764138587-189f22804ec4',
        'destilados' => '1615887023544-3a566f29d822',
    ],
    'categorias' => [
        'tintos' => ['Tintos', 'De los valles grandes y de los secanos chicos.'],
        'blancos' => ['Blancos y rosados', 'Frescos, de costa y de altura.'],
        'espumantes' => ['Espumantes', 'Método tradicional, para brindar sin apuro.'],
        'destilados' => ['Destilados', 'Piscos, gin, whisky, ron y vermut de pequeños productores.'],
        'accesorios' => ['Accesorios y regalos', 'Para servir, guardar y regalar.'],
    ],
    // id => datos. origen: valle · cepa o estilo. variantes: [nombre => precio]. destacado: va en "La selección".
    'productos' => [
        'carmenere-cachapoal' => ['cat' => 'tintos', 'nombre' => 'Carménère reserva', 'origen' => 'Valle del Cachapoal · 2022 · 750 ml', 'notas' => 'Pimienta negra, guinda madura y un final suave a tabaco.', 'foto' => '1553361371-9b22f78e8b1d', 'variantes' => $vino(9990), 'destacado' => true],
        'cabernet-maipo' => ['cat' => 'tintos', 'nombre' => 'Cabernet Sauvignon gran reserva', 'origen' => 'Alto Maipo · 2020 · 750 ml', 'notas' => 'Cassis, menta y taninos firmes. Para guardar o decantar.', 'foto' => '1554230561-31bdc707b537', 'variantes' => $vino(16900), 'destacado' => false],
        'pais-itata' => ['cat' => 'tintos', 'nombre' => 'País de secano', 'origen' => 'Valle del Itata · 2023 · 750 ml', 'notas' => 'Ligero, frutal y rústico, de parras de más de cien años.', 'foto' => '1676024461918-ecf0b55674c7', 'variantes' => $vino(11500), 'destacado' => true],
        'syrah-san-antonio' => ['cat' => 'tintos', 'nombre' => 'Syrah costero', 'origen' => 'Valle de San Antonio · 2021 · 750 ml', 'notas' => 'Mora, aceituna negra y una frescura que pide cordero.', 'foto' => '1610458034932-dc165f29499e', 'variantes' => $vino(14900), 'destacado' => false],
        'ensamblaje-colchagua' => ['cat' => 'tintos', 'nombre' => 'Ensamblaje de autor', 'origen' => 'Valle de Colchagua · 2019 · 750 ml', 'notas' => 'Carménère, Syrah y Malbec: 18 meses en barrica usada.', 'foto' => '1553703189-2f8ce8a11bcc', 'variantes' => $vino(24900), 'destacado' => false],
        'sauvignon-casablanca' => ['cat' => 'blancos', 'nombre' => 'Sauvignon Blanc', 'origen' => 'Valle de Casablanca · 2024 · 750 ml', 'notas' => 'Pomelo, hierba recién cortada y mucha acidez.', 'foto' => '1597905722448-a1df7c00000a', 'variantes' => $vino(8990), 'destacado' => false],
        'chardonnay-limari' => ['cat' => 'blancos', 'nombre' => 'Chardonnay costero', 'origen' => 'Valle del Limarí · 2023 · 750 ml', 'notas' => 'Tiza, pera y un toque salino del Pacífico.', 'foto' => '1662738221889-2344690f7dc1', 'variantes' => $vino(12900), 'destacado' => true],
        'rosado-cinsault' => ['cat' => 'blancos', 'nombre' => 'Rosado de Cinsault', 'origen' => 'Valle del Itata · 2024 · 750 ml', 'notas' => 'Frutilla, pétalos y un final seco. Para la tarde.', 'foto' => '1547595628-c61a29f496f0', 'variantes' => $vino(9500), 'destacado' => false],
        'espumante-brut' => ['cat' => 'espumantes', 'nombre' => 'Espumante brut', 'origen' => 'Valle de Casablanca · método tradicional · 750 ml', 'notas' => 'Burbuja fina, pan tostado y manzana verde.', 'foto' => '1706106591925-8818b8bf7fc0', 'variantes' => $vino(15900), 'destacado' => false],
        'espumante-rose' => ['cat' => 'espumantes', 'nombre' => 'Espumante rosé extra brut', 'origen' => 'Valle del Biobío · método tradicional · 750 ml', 'notas' => 'Frambuesa, cáscara de naranja y una acidez muy viva.', 'foto' => '1619143840338-98685ced6310', 'variantes' => $vino(17500), 'destacado' => false],
        'pisco-elqui' => ['cat' => 'destilados', 'nombre' => 'Pisco reservado 40°', 'origen' => 'Valle del Elqui · moscatel · 700 ml', 'notas' => 'Floral y limpio. Nuestro favorito para el sour.', 'foto' => '1772565449255-8ea17f1ee5c8', 'variantes' => ['Botella' => 13900], 'destacado' => true],
        'pisco-roble' => ['cat' => 'destilados', 'nombre' => 'Pisco envejecido en roble 40°', 'origen' => 'Valle del Limarí · 3 años · 700 ml', 'notas' => 'Vainilla, nuez y uva pasa. Para beber solo, a sorbos.', 'foto' => '1758607010215-d25156d5e1f8', 'variantes' => ['Botella' => 19900], 'destacado' => false],
        'gin-nativo' => ['cat' => 'destilados', 'nombre' => 'Gin de botánicos nativos', 'origen' => 'Destilería de la Región de Los Ríos · 700 ml', 'notas' => 'Maqui, canelo y cáscara de limón de Pica.', 'foto' => '1675370734208-120b43d3c7ac', 'variantes' => ['Botella' => 21900], 'destacado' => false],
        'whisky-malta' => ['cat' => 'destilados', 'nombre' => 'Whisky de malta 12 años', 'origen' => 'Escocia, Highlands · 700 ml', 'notas' => 'Miel, naranja confitada y un humo muy leve.', 'foto' => '1767536196118-9f0592594dcc', 'variantes' => ['Botella' => 34900], 'destacado' => false],
        'ron-anejo' => ['cat' => 'destilados', 'nombre' => 'Ron añejo 7 años', 'origen' => 'Caribe · solera · 700 ml', 'notas' => 'Cacao, plátano maduro y especias dulces.', 'foto' => '1701252760183-295a928b3441', 'variantes' => ['Botella' => 19900], 'destacado' => false],
        'vermut-rojo' => ['cat' => 'destilados', 'nombre' => 'Vermut rojo artesanal', 'origen' => 'Valle de Curicó · 750 ml', 'notas' => 'Base de País, ajenjo y naranja amarga. Con hielo y una rodaja.', 'foto' => '1603048004567-8109c99d4e7d', 'variantes' => ['Botella' => 12900], 'destacado' => false],
        'sacacorchos' => ['cat' => 'accesorios', 'nombre' => 'Sacacorchos de sommelier', 'origen' => 'Doble palanca · acero y madera', 'notas' => 'El que usamos en las catas: firme y fácil de llevar.', 'foto' => '1669643219874-e2372fd75cc5', 'variantes' => ['Unidad' => 7900], 'destacado' => false],
        'decantador' => ['cat' => 'accesorios', 'nombre' => 'Decantador de vidrio', 'origen' => '1,5 litros · soplado a mano', 'notas' => 'Para airear tintos jóvenes y separar sedimentos.', 'foto' => '1591496534942-8ea0cb46182b', 'variantes' => ['Unidad' => 18900], 'destacado' => false],
        'copas' => ['cat' => 'accesorios', 'nombre' => 'Copas de cristal para cata', 'origen' => 'Set de 2 · 450 ml', 'notas' => 'Cáliz amplio y borde fino: la misma de nuestras catas.', 'foto' => '1639770921770-2f63b6361c36', 'variantes' => ['Set de 2' => 14900], 'destacado' => false],
        'caja-regalo' => ['cat' => 'accesorios', 'nombre' => 'Caja de regalo con tarjeta', 'origen' => 'Cartón kraft · 1 o 2 botellas', 'notas' => 'Con viruta, hilo, sello de lacre y una tarjeta escrita a mano.', 'foto' => '1764764138818-0b22ab4d4023', 'variantes' => ['1 botella' => 5900, '2 botellas' => 7900], 'destacado' => false],
    ],
    // Catas: se calculan las próximas fechas (día de la semana + semanas desde hoy), ver co_catas().
    'catas' => [
        ['id' => 'cata-carmenere', 'titulo' => 'Carménère: la cepa que volvió', 'dia' => 4, 'semana' => 0, 'hora' => '19:30', 'precio' => 18000, 'cupos' => 16, 'quedan' => 5, 'foto' => '1598306442928-4d90f32c6866', 'texto' => 'Cinco Carménère de Cachapoal, Colchagua y Maule, de reserva a gran reserva. Por qué se confundió con Merlot por un siglo y cómo se reconoce.', 'incluye' => ['5 vinos en porciones de cata', 'Quesos, pan de masa madre y agua', '10 % de descuento en lo que compres esa noche']],
        ['id' => 'cata-destilados', 'titulo' => 'Pisco y gin de autor', 'dia' => 4, 'semana' => 1, 'hora' => '19:30', 'precio' => 22000, 'cupos' => 14, 'quedan' => 9, 'foto' => '1675370734208-120b43d3c7ac', 'texto' => 'Cuatro piscos (transparentes y de guarda) y dos gin con botánicos nativos. Aprendemos a oler un destilado y a preparar un sour equilibrado.', 'incluye' => ['6 destilados en porciones de 15 ml', 'Un sour preparado en la mesa', 'Picoteo y agua sin límite']],
        ['id' => 'cata-espumantes', 'titulo' => 'Espumantes para fin de año', 'dia' => 6, 'semana' => 1, 'hora' => '17:00', 'precio' => 20000, 'cupos' => 18, 'quedan' => 2, 'foto' => '1706106591925-8818b8bf7fc0', 'texto' => 'Del brut al extra brut, del método tanque al tradicional. Cinco burbujas chilenas para elegir con calma qué abrir en las fiestas.', 'incluye' => ['5 espumantes en porciones de cata', 'Ostiones y tostadas', 'Guía impresa para llevar']],
        ['id' => 'cata-itata', 'titulo' => 'Secano del Itata', 'dia' => 4, 'semana' => 2, 'hora' => '19:30', 'precio' => 16000, 'cupos' => 16, 'quedan' => 12, 'foto' => '1676024461918-ecf0b55674c7', 'texto' => 'País, Cinsault y Moscatel de parras centenarias sin riego. Vinos ligeros, honestos y con mucha historia campesina.', 'incluye' => ['5 vinos en porciones de cata', 'Pan amasado, pebre y queso de campo', 'Conversación con el productor (en línea)']],
    ],
    'preguntas' => [
        ['¿Tengo que ser mayor de edad para comprar?', 'Sí. Vendemos solo a mayores de 18 años y verificamos la edad con el carnet al entregar o al retirar en la tienda. Si quien recibe no lo muestra, el pedido vuelve a la cava.'],
        ['¿Cómo funcionan las catas?', 'Son grupos de 14 a 18 personas, de 90 minutos, en la sala del fondo de la tienda. Se sirven porciones de cata (30 ml de vino, 15 ml de destilado), siempre con agua, pan y algo para comer. Reservas desde la página de catas y confirmamos por WhatsApp.'],
        ['¿Puedo cancelar o cambiar la fecha de una cata?', 'Sí, avisando con 48 horas de anticipación te cambiamos la fecha o te devolvemos el valor. Con menos aviso, puedes enviar a otra persona en tu lugar.'],
        ['¿Hacen catas privadas?', 'Sí, para cumpleaños, equipos de trabajo o clientes, desde 8 personas. Elegimos los vinos contigo según el presupuesto. Escríbenos por WhatsApp.'],
        ['¿Despachan a domicilio?', 'Sí, en Santiago, Providencia, Recoleta, Independencia, Ñuñoa, Estación Central y Las Condes, al día siguiente hábil. Arma tu pedido en el catálogo y envíalo por WhatsApp con el costo de tu comuna.'],
        ['¿Arman regalos?', 'Sí: elige las botellas, agrega la caja de regalo y escribe el mensaje de la tarjeta al hacer el pedido. La escribimos a mano.'],
        ['¿Cuánto descuento tiene la caja de 6?', 'Un 10 % sobre el precio por botella. Puedes armar la caja con el mismo vino; si quieres mezclar, escríbenos y te hacemos el mismo descuento.'],
        ['¿Cómo pago?', 'En la tienda: efectivo, débito o crédito. En los despachos: transferencia antes del envío o pago al recibir con tarjeta.'],
    ],
];
