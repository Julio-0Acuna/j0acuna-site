<?php
declare(strict_types=1);

// Ferretería Las Acacias (Macul): negocio ficticio de la versión Simple-page.
// Productos genéricos, sin marcas. Fotos: IDs de Unsplash (ver demos/ferreteria/CREDITOS.md).
return [
    'nombre' => 'Ferretería Las Acacias',
    'nombre_corto' => 'Las Acacias',
    'categoria' => 'Ferretería',
    'comuna' => 'Macul',
    'desde' => 1988,
    'eslogan' => 'Si no lo tenemos, se lo conseguimos.',
    'bajada' => 'Ferretería de barrio atendida por don Hugo y su hija Carla. Gasfitería, electricidad, pinturas y herramientas, con el consejo justo para que compre solo lo que necesita.',
    'nota' => 4.8,
    'horario' => [
        1 => ['08:30', '19:30'],
        2 => ['08:30', '19:30'],
        3 => ['08:30', '19:30'],
        4 => ['08:30', '19:30'],
        5 => ['08:30', '19:30'],
        6 => ['09:00', '14:00'],
        7 => null,
    ],
    'fotos' => [
        'hero' => '1765744893064-dce3184289ef',
        'meson' => '1794063071331-ac0b7f60709d',
        'llaves' => '1756341782434-3020b9d17372',
        'cables' => '1761507320645-b11a00bfcc34',
        'tarugos' => '1785769587525-d74b0503c2e9',
    ],
    // Lo más vendido: id => [nombre, detalle, precio, foto, alt, sello]
    'productos' => [
        'taladro' => ['Taladro percutor 650 W', 'Con maletín, mango auxiliar y tope de profundidad. Perfora madera, metal y ladrillo.', 34990, '1684564577719-1e9944866a45', 'Broca perforando una tabla de madera, con aserrín alrededor', 'Garantía 1 año'],
        'destornilladores' => ['Set de destornilladores 6 piezas', 'Paleta y cruz en tres medidas, con puntas imantadas y mango de goma.', 6990, '1581621293775-7aac902d7032', 'Mano sosteniendo un destornillador de mango rojo', ''],
        'huincha' => ['Cinta métrica 5 m', 'Cinta de acero de 19 mm con freno y gancho imantado.', 3490, '1559647746-9b2f216d2dc3', 'Cinta métrica azul y amarilla con la cinta un poco estirada', ''],
        'latex' => ['Pintura látex blanca 1 galón', 'Látex lavable mate para interior. Rinde unos 35 m² por mano.', 12990, '1621685682093-3b8016dcb57d', 'Tarro de pintura blanca abierto con una brocha encima', 'Más vendido'],
        'llave-jardin' => ['Llave de jardín 1/2" de bronce', 'Con hilo para manguera y manilla metálica. Le explicamos cómo cambiarla.', 4590, '1669920282730-ab422e592f97', 'Llave de jardín de bronce con manilla roja y el agua corriendo', ''],
        'tornillos' => ['Tornillo volcanita 6 × 1¼" caja 100 un.', 'Punta fina para perfil metálico y yeso-cartón. También por unidad.', 2190, '1785769587815-a222f309e6cc', 'Montón de tornillos dorados para volcanita', ''],
        'ampolleta' => ['Ampolleta LED 9 W luz cálida', 'Equivale a una de 60 W, rosca normal E27. Pack de 3 a $3.290.', 1290, '1532007271951-c487760934ae', 'Ampolleta LED blanca sobre fondo oscuro', ''],
        'candado' => ['Candado de bronce 40 mm', 'Arco de acero templado y tres llaves. Para portones y bodegas.', 5990, '1586661615438-349a276d098b', 'Candado de bronce cerrando una cadena sobre una puerta de madera', ''],
    ],
    // Pasillos del local: [número, nombre, ejemplos]
    'pasillos' => [
        ['Gasfitería', 'Llaves, flexibles, teflón, sifones, PVC y fittings de cobre.'],
        ['Electricidad', 'Cables por metro, enchufes, interruptores, ampolletas y alargadores.'],
        ['Pinturas', 'Látex, esmaltes, barnices, brochas, rodillos y diluyente.'],
        ['Herramientas', 'Manuales y eléctricas, brocas, discos y repuestos.'],
        ['Fijaciones', 'Tornillos, tarugos, clavos y pernos, por unidad o por caja.'],
        ['Cerrajería', 'Candados, chapas, bisagras, picaportes y copias de llaves.'],
        ['Jardín', 'Mangueras, conectores, aspersores, palas y tijeras de podar.'],
        ['Construcción', 'Cemento, yeso, pasta muro, sacos de arena y carretillas.'],
    ],
    // Arriendo: id => [nombre, detalle, día, medio día, garantía, foto, alt]
    'arriendo' => [
        'rotomartillo' => ['Rotomartillo 800 W', 'Para perforar hormigón y picar cerámica. Incluye brocas de 6, 8 y 10 mm.', 9990, 6990, 50000, '1760376208640-2ece4c4a0adc', 'Tres brocas para concreto sobre una superficie de cemento'],
        'esmeril' => ['Esmeril angular 4½"', 'Con disco de corte y protector. Para fierro, perfiles y cerámica.', 5990, 3990, 30000, '1564182872950-978e194a05cd', 'Esmeril angular cortando metal con chispas'],
        'escalera' => ['Escalera tijera de aluminio 6 peldaños', 'Alcanza unos 3 metros de trabajo. Liviana y con bandeja para herramientas.', 4990, 2990, 40000, '1563611648711-0641cb8f7c4f', 'Escalera de aluminio apoyada en un muro naranjo con hojas'],
        'betonera' => ['Betonera eléctrica 150 L', 'Para radieres y pilares. Se la llevamos dentro de Macul por $5.000.', 14990, 0, 80000, '1786269691602-519fdaa112ea', 'Betonera naranja en un patio con sacos y baldes de obra'],
    ],
    // Servicios en el mesón: [nombre, detalle, precio]
    'servicios' => [
        ['Copia de llaves', 'Al momento, de puerta, candado y portón.', 'desde $1.500'],
        ['Corte a medida', 'Tubos de PVC y cobre, cadena y cable.', 'gratis con tu compra'],
        ['Afilado', 'Cuchillos, tijeras y herramientas de jardín. Listo al día siguiente.', '$2.000 c/u'],
        ['Despacho en el barrio', 'Lo llevamos el mismo día si pides antes de las 15:00.', 'desde $2.500'],
    ],
    // Despacho de la lista cotizada: comuna => costo
    'comunas_despacho' => [
        'Macul' => 2500,
        'Ñuñoa' => 3500,
        'San Joaquín' => 3500,
        'Peñalolén' => 3990,
        'La Florida' => 4990,
    ],
    'despacho_gratis' => 40000,
    // Consejos de don Hugo: [pregunta, respuesta, foto|null, alt]
    'consejos' => [
        ['¿Gotea la llave del lavaplatos?', 'Casi siempre es la gomita (empaquetadura) y cuesta $300. Cierre la llave de paso, traiga la vieja y se la igualamos al tiro.', null, ''],
        ['¿Qué tarugo uso?', 'En volcanita, tarugo paloma o mariposa. En ladrillo u hormigón, tarugo plástico de 8 mm con broca para concreto del mismo número.', 'tarugos', 'Tornillos dorados amontonados'],
        ['¿Cuánta pintura compro?', 'Un galón rinde unos 35 m² por mano. Para un dormitorio de 3 × 3 metros con dos manos, lleve dos galones y le sobra para retoques.', null, ''],
        ['¿Salta el automático al enchufar el hervidor?', 'Puede ser un enchufe suelto o un alargador muy delgado. No lo arregle a ciegas: tráiganos una foto del tablero y le decimos qué hacer.', 'cables', 'Cables eléctricos de colores'],
    ],
    'resenas' => [
        ['Paulina G.', 5, 'Fui con una foto de la llave que goteaba y don Hugo me vendió la gomita de $300 en vez de una llave nueva. Eso es confianza.', 'hace 1 semana'],
        ['Rodrigo M.', 5, 'Soy maestro: les mando la lista por WhatsApp en la mañana y a mediodía está todo separado. Arriendo el rotomartillo seguido.', 'hace 3 semanas'],
        ['Fernanda T.', 4, 'Tienen de todo en poco espacio. El sábado se llena, pero atienden rápido y te explican.', 'hace 2 meses'],
    ],
];
