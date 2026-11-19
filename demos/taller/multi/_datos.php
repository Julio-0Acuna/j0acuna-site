<?php
declare(strict_types=1);

// Vértice Garage (Vitacura): negocio ficticio de la versión Multi-page.
// Fotos: IDs de Unsplash (ver demos/taller/CREDITOS.md). Sin marcas de autos ni de repuestos.
return [
    'nombre' => 'Vértice Garage',
    'categoria' => 'Taller mecánico especializado',
    'comuna' => 'Vitacura',
    'eslogan' => 'Diagnóstico y mantención de precisión',
    'nota' => 4.9,
    'horario' => [
        1 => ['08:00', '19:00'],
        2 => ['08:00', '19:00'],
        3 => ['08:00', '19:00'],
        4 => ['08:00', '19:00'],
        5 => ['08:00', '19:00'],
        6 => ['09:00', '14:00'],
        7 => null,
    ],
    // Bloques de atención para la reserva (los sábados solo los de la mañana).
    'bloques' => ['08:30 – 10:30', '10:30 – 12:30', '14:00 – 16:00', '16:00 – 18:00'],
    'resenas' => [
        ['Ignacio M.', 5, 'Reservé por la web con la patente y el kilometraje; al llegar ya sabían qué tocaba. Me mandaron fotos de las pastillas gastadas antes de cambiarlas.', 'hace 5 días'],
        ['Carolina V.', 5, 'Usé el retiro a domicilio: se llevaron el auto en la mañana y volvió en la tarde, lavado y con el informe en el WhatsApp.', 'hace 3 semanas'],
        ['Andrés P.', 4, 'Muy prolijos y claros con los precios. No son los más baratos, pero el diagnóstico encontró una falla que otro taller no vio.', 'hace 2 meses'],
    ],
    // Retiro y entrega del auto en casa u oficina (costo por comuna, ida y vuelta).
    'comunas_despacho' => [
        'Vitacura' => 4990,
        'Las Condes' => 6990,
        'Lo Barnechea' => 7990,
        'Providencia' => 7990,
        'La Reina' => 8990,
        'Huechuraba' => 8990,
        'Ñuñoa' => 9990,
    ],
    'categorias' => [
        'mantencion' => ['Mantención', 'Según la pauta del fabricante, con informe de 40 puntos y fotos.'],
        'frenos' => ['Frenos y suspensión', 'Lo que te detiene y lo que te mantiene pegado al camino.'],
        'diagnostico' => ['Diagnóstico y electricidad', 'Escáner multimarca y técnicos que entienden lo que lee.'],
        'neumaticos' => ['Neumáticos y alineación', 'Equipo de alineación 3D con informe antes y después.'],
        'clima' => ['Climatización', 'Aire acondicionado listo antes del verano.'],
        'estetica' => ['Cuidado y estética', 'Para que el auto vuelva mejor de lo que llegó.'],
    ],
    // id => datos. variantes: [nombre => precio desde]. firma: aparece en el inicio.
    'servicios' => [
        'mantencion-10' => ['cat' => 'mantencion', 'nombre' => 'Mantención 10.000 km', 'desc' => 'Aceite sintético según norma, filtro, revisión de 40 puntos con informe y fotos.', 'foto' => '1768689033119-c3ac1e437d20', 'alt' => 'Aceite dorado cayendo en un chorro fino', 'duracion' => '1 h 30 min', 'variantes' => ['Auto' => 79990, 'SUV' => 94990, 'Camioneta' => 99990], 'firma' => true],
        'mantencion-mayor' => ['cat' => 'mantencion', 'nombre' => 'Mantención mayor 40.000 km', 'desc' => 'Filtros de aire, polen y combustible, bujías, líquido de frenos y revisión completa.', 'foto' => '1747511578112-3d21e8b3d5d7', 'alt' => 'Mecánico trabajando en las piezas del motor', 'duracion' => '4 h', 'variantes' => ['Auto' => 189990, 'SUV' => 229990, 'Camioneta' => 249990], 'firma' => false],
        'aceite' => ['cat' => 'mantencion', 'nombre' => 'Cambio de aceite express', 'desc' => 'Aceite sintético y filtro nuevo mientras esperas en la sala, con café.', 'foto' => '1429772011165-0c2e054367b8', 'alt' => 'Motor de un auto con el capó abierto', 'duracion' => '45 min', 'variantes' => ['Auto' => 59990, 'SUV' => 69990, 'Camioneta' => 74990], 'firma' => false],
        'frenos' => ['cat' => 'frenos', 'nombre' => 'Pastillas y discos', 'desc' => 'Pastillas cerámicas, discos nuevos o rectificados, sensor de desgaste y prueba en ruta.', 'foto' => '1760317890314-e964ffd7e6a6', 'alt' => 'Disco de freno perforado y caliper montados en la rueda', 'duracion' => '2 h 30 min', 'variantes' => ['Eje delantero' => 129990, 'Ambos ejes' => 239990], 'firma' => true, 'modelo' => 'disco-freno'],
        'liquido-frenos' => ['cat' => 'frenos', 'nombre' => 'Cambio de líquido de frenos', 'desc' => 'Purgado completo con líquido DOT 4 y medición de humedad antes y después.', 'foto' => '1760317890322-364a810cd4da', 'alt' => 'Sistema de frenos de alto rendimiento visto de cerca', 'duracion' => '1 h', 'variantes' => ['Única' => 49990], 'firma' => false],
        'suspension' => ['cat' => 'frenos', 'nombre' => 'Amortiguadores', 'desc' => 'Par de amortiguadores con topes y cazoletas nuevos, y alineación incluida.', 'foto' => '1764869427626-f15802570098', 'alt' => 'Amortiguador y brazos de suspensión vistos desde abajo del auto', 'duracion' => '3 h', 'variantes' => ['Eje delantero' => 249990, 'Eje trasero' => 219990], 'firma' => true],
        'scanner' => ['cat' => 'diagnostico', 'nombre' => 'Diagnóstico computarizado', 'desc' => 'Escáner multimarca en todos los módulos e informe con prioridades. Se descuenta si reparas aquí.', 'foto' => '1791508725103-c0c3c4185721', 'alt' => 'Mecánico usando un computador portátil en el mesón del taller', 'duracion' => '1 h', 'variantes' => ['Única' => 39990], 'firma' => true],
        'electrico' => ['cat' => 'diagnostico', 'nombre' => 'Batería y sistema de carga', 'desc' => 'Prueba de batería, alternador y motor de partida. El cambio incluye registrar la batería nueva.', 'foto' => '1775590766345-c117265f0c1b', 'alt' => 'Mecánico ajustando una pieza del motor con una llave de copa', 'duracion' => '45 min', 'variantes' => ['Revisión' => 24990, 'Cambio de batería' => 149990], 'firma' => false, 'modelo' => 'bateria'],
        'embrague' => ['cat' => 'diagnostico', 'nombre' => 'Embrague', 'desc' => 'Kit de embrague completo, revisión del volante del motor y prueba en ruta.', 'foto' => '1774977866250-89aec8731911', 'alt' => 'Tres pedales de un auto: embrague, freno y acelerador', 'duracion' => '1 día', 'variantes' => ['Auto' => 389990, 'SUV' => 459990, 'Camioneta' => 489990], 'firma' => false],
        'alineacion' => ['cat' => 'neumaticos', 'nombre' => 'Alineación 3D y balanceo', 'desc' => 'Alineación con equipo 3D, informe impreso antes y después y balanceo de las cuatro ruedas.', 'foto' => '1613214036979-1c6a7e0cf677', 'alt' => 'Neumático de un auto en penumbra, de cerca', 'duracion' => '1 h', 'variantes' => ['Auto' => 34990, 'SUV' => 39990, 'Camioneta' => 44990], 'firma' => false],
        'neumaticos' => ['cat' => 'neumaticos', 'nombre' => 'Montaje de neumáticos', 'desc' => 'Desmontaje, montaje, válvulas nuevas y balanceo. Los neumáticos se cotizan aparte.', 'foto' => '1657307913178-97cb5a4d0cd7', 'alt' => 'Dibujo de la banda de rodadura de un neumático', 'duracion' => '1 h', 'variantes' => ['Juego de 4' => 39990], 'firma' => false, 'modelo' => 'neumatico'],
        'aire' => ['cat' => 'clima', 'nombre' => 'Mantención de aire acondicionado', 'desc' => 'Vacío del sistema, carga de gas por peso, aceite del compresor y filtro de polen.', 'foto' => '1778938525731-0a254548f58f', 'alt' => 'Panel de climatización de un auto con pantalla digital', 'duracion' => '1 h 30 min', 'variantes' => ['Única' => 69990], 'firma' => false],
        'pre-revision' => ['cat' => 'mantencion', 'nombre' => 'Revisión técnica asistida', 'desc' => 'Pre-revisión de gases, luces y frenos; llevamos el auto a la planta y lo devolvemos aprobado.', 'foto' => '1643701079732-3b1c7a797e3d', 'alt' => 'Mecánico revisando la parte de abajo de un auto en el elevador', 'duracion' => 'Medio día', 'variantes' => ['Única' => 49990], 'firma' => false],
        'detalle' => ['cat' => 'estetica', 'nombre' => 'Lavado de detalle', 'desc' => 'Lavado a mano, descontaminado de llantas e interior con vapor.', 'foto' => '1565689876697-e467b6c54da2', 'alt' => 'Llanta y neumático lavados con cepillo y espuma', 'duracion' => '3 h', 'variantes' => ['Auto' => 59990, 'SUV' => 69990], 'firma' => false],
        'pulido' => ['cat' => 'estetica', 'nombre' => 'Pulido y sellado de pintura', 'desc' => 'Corrección de pintura en una etapa y sellante cerámico con duración de 12 meses.', 'foto' => '1780558852670-3f49298ecc1f', 'alt' => 'Pulidora orbital trabajando sobre el capó azul de un auto', 'duracion' => '1 día', 'variantes' => ['Auto' => 189990, 'SUV' => 219990], 'firma' => false],
    ],
    'fotos' => [
        'hero' => '1656232976683-7b688560e427',
        'taller' => '1530046614490-89e6f776b83b',
        'mecanico' => '1615906655593-ad0386982a0f',
        'llaves' => '1602529079830-c217d8f2eda5',
        'faros' => '1532268116505-8c59cc37d2e6',
    ],
    'equipamiento' => [
        ['Escáner multimarca', 'Lee todos los módulos del auto, no solo el motor: frenos, airbags, caja y carrocería.'],
        ['Alineadora 3D', 'Cámaras que miden las cuatro ruedas a la vez, con informe impreso antes y después.'],
        ['Cuatro elevadores', 'Dos de ellos para camionetas y SUV de hasta 4 toneladas.'],
        ['Estación de aire acondicionado', 'Recupera, recicla y carga el gas por peso exacto, sin pérdidas.'],
    ],
    'preguntas' => [
        ['¿Cómo reservo una hora?', 'En «Servicios» agrega lo que necesitas, y en «Tu reserva» completa la patente, el modelo, el kilometraje, el día y el bloque. Se abre WhatsApp con todo escrito y te confirmamos en menos de una hora hábil.'],
        ['¿Los precios son finales?', 'Son precios «desde» para cada tipo de vehículo, con mano de obra incluida. Si el diagnóstico muestra algo distinto, te enviamos el presupuesto por WhatsApp y no avanzamos sin tu aprobación.'],
        ['¿Atienden todas las marcas?', 'Sí. Trabajamos con autos, SUV y camionetas bencineras, diésel e híbridas, de todas las marcas. Para eléctricos hacemos frenos, suspensión, neumáticos y climatización.'],
        ['¿Usan repuestos originales?', 'Tú eliges: originales o alternativos de primera línea con garantía equivalente. Siempre te mostramos las piezas que salen del auto.'],
        ['¿Cómo funciona el retiro a domicilio?', 'Un chofer con seguro retira el auto en tu casa u oficina y te lo devuelve el mismo día. El costo depende de la comuna y se muestra al hacer la reserva.'],
        ['¿Qué garantía tienen los trabajos?', 'Seis meses o 10.000 km, lo que ocurra primero, en mano de obra y repuestos que instalamos nosotros.'],
        ['¿Puedo esperar mientras trabajan?', 'Para servicios de hasta dos horas, sí: tenemos sala con café, wifi y vista al taller.'],
    ],
];
