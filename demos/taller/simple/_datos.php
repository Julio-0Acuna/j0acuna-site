<?php
declare(strict_types=1);

// Taller Marisol (San Miguel): negocio ficticio de la versión Simple-page.
// Fotos: IDs de Unsplash (ver demos/taller/CREDITOS.md). Sin marcas de autos ni de repuestos.
return [
    'nombre' => 'Taller Marisol',
    'categoria' => 'Taller mecánico',
    'comuna' => 'San Miguel',
    'desde' => 1998,
    'nota' => 4.8,
    'horario' => [
        1 => ['08:30', '18:30'],
        2 => ['08:30', '18:30'],
        3 => ['08:30', '18:30'],
        4 => ['08:30', '18:30'],
        5 => ['08:30', '18:30'],
        6 => ['09:00', '14:00'],
        7 => null,
    ],
    'fotos' => [
        'hero' => '1687462970787-61d953508926',
        'explica' => '1628577478162-d4d00467c627',
        'taller' => '1760310936486-4dd450aab2a8',
        'manos' => '1619642751034-765dfdf7c58e',
    ],
    'promesas' => [
        'Presupuesto por escrito antes de tocar tu auto',
        'Te mostramos las piezas que cambiamos',
        'Garantía de 3 meses o 5.000 km',
        'Boleta o factura en todos los trabajos',
    ],
    // Lo más pedido: tarjetas con foto (id del servicio => foto).
    'destacados' => [
        'aceite' => ['foto' => '1642075223291-f9ec545889fa', 'alt' => 'Mecánico con guante vaciando aceite nuevo de un bidón', 'sello' => 'En 40 minutos'],
        'frenos' => ['foto' => '1761040100230-8c8e6fc64638', 'alt' => 'Caliper de freno rojo y disco perforado detrás de los rayos de una llanta', 'sello' => 'Revisión gratis'],
        'scanner' => ['foto' => '1632733711679-529326f6db12', 'alt' => 'Mecánico revisando la caja de fusibles del motor con un probador', 'sello' => 'Se descuenta del arreglo'],
    ],
    // Servicios de la orden de trabajo: id => [nombre, detalle, precio desde].
    'servicios' => [
        'aceite' => ['Cambio de aceite y filtro', 'Aceite semisintético hasta 4 litros, filtro nuevo y revisión de niveles.', 34990],
        'mantencion' => ['Mantención por kilometraje', 'Según la pauta de tu auto: 10.000, 20.000, 30.000 km y más.', 49990],
        'frenos' => ['Frenos', 'Pastillas, discos, líquido de frenos y freno de mano.', 39990],
        'alineacion' => ['Alineación y balanceo', 'Alineación computarizada y balanceo de las cuatro ruedas.', 19990],
        'scanner' => ['Scanner y diagnóstico', 'Lectura de fallas y luz de motor en el tablero. Se descuenta si reparas aquí.', 15000],
        'embrague' => ['Embrague', 'Cambio de kit de embrague: disco, prensa y rodamiento.', 219990],
        'suspension' => ['Suspensión y tren delantero', 'Amortiguadores, bujes, terminales y rótulas.', 89990],
        'revision' => ['Revisión técnica sin vueltas', 'Pre-revisión en el taller y llevamos tu auto a la planta.', 24990],
        'neumaticos' => ['Neumáticos', 'Montaje, balanceo y reparación de pinchazos.', 6000],
        'bateria' => ['Batería y partida', 'Prueba de carga, alternador y cambio de batería.', 4990],
        'aire' => ['Aire acondicionado', 'Carga de gas, revisión de fugas y filtro de polen.', 34990],
        'distribucion' => ['Correa de distribución', 'Kit de correa, tensor y bomba de agua, según el motor.', 189990],
    ],
    // Pauta de mantención: kilómetros => [lo que incluye, precio desde].
    'pauta' => [
        10000 => [['Aceite y filtro de aceite', 'Revisión de niveles y luces', 'Presión y desgaste de neumáticos'], 49990],
        20000 => [['Todo lo de los 10.000 km', 'Filtro de aire y filtro de polen', 'Rotación de neumáticos'], 79990],
        30000 => [['Todo lo de los 20.000 km', 'Bujías', 'Cambio de líquido de frenos'], 119990],
        40000 => [['Todo lo de los 20.000 km', 'Filtro de combustible', 'Revisión de suspensión y dirección'], 129990],
        60000 => [['Todo lo de los 30.000 km', 'Refrigerante del motor', 'Revisión del embrague y alineación'], 169990],
        100000 => [['Todo lo de los 60.000 km', 'Correa de distribución y bomba de agua (según el motor)', 'Revisión completa con informe'], 289990],
    ],
    'pasos' => [
        ['Nos escribes', 'Por WhatsApp, con la patente, el modelo y lo que notas en el auto. Si no sabes qué tiene, mejor: para eso estamos.'],
        ['Revisamos y cotizamos', 'Te mandamos el presupuesto por escrito, con foto de lo que encontramos. Sin tu «sí», no se toca nada.'],
        ['Reparamos', 'Con repuestos de calidad y boleta. Si aparece algo más, te llamamos antes.'],
        ['Te lo entregamos', 'Con las piezas cambiadas en una bolsa, para que las veas, y la garantía anotada en la orden.'],
    ],
    'resenas' => [
        ['Patricia G.', 5, 'Me explicaron con paciencia qué tenía el auto y el presupuesto fue exactamente lo que pagué. Ya les llevo los autos de toda la familia.', 'hace 1 semana'],
        ['Rodrigo A.', 5, 'Cambio de pastillas y discos en la mañana, listo a la hora de almuerzo. Me mostraron las piezas viejas sin que se las pidiera.', 'hace 3 semanas'],
        ['Fernanda L.', 4, 'Muy honestos: me dijeron que el embrague aún aguantaba y que no lo cambiara todavía. Hay que pedir hora con anticipación.', 'hace 2 meses'],
    ],
];
