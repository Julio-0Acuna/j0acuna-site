<?php
declare(strict_types=1);

// Veterinaria Cascabel (Conchalí): negocio ficticio de la versión Simple-page.
// Fotos: IDs de Unsplash (ver demos/veterinaria/CREDITOS.md). Sin marcas ni logos.
// Los textos no recetan ni dan dosis: los medicamentos siempre los indica la doctora después de revisar.
return [
    'nombre' => 'Veterinaria Cascabel',
    'categoria' => 'Veterinaria',
    'comuna' => 'Conchalí',
    'desde' => 2008,
    'doctora' => 'Dra. Paula Riquelme',
    'nota' => 4.9,
    'horario' => [
        1 => ['10:00', '20:00'],
        2 => ['10:00', '20:00'],
        3 => ['10:00', '20:00'],
        4 => ['10:00', '20:00'],
        5 => ['10:00', '20:00'],
        6 => ['10:00', '14:00'],
        7 => null,
    ],
    // Bloques de atención para la reserva. Los sábados solo hay mañana.
    'bloques' => [
        'manana' => 'Mañana · 10:00 a 13:30',
        'tarde' => 'Tarde · 15:00 a 19:30',
    ],
    'especies' => ['perro' => 'Perro', 'gato' => 'Gato', 'otro' => 'Otra especie'],
    'fotos' => [
        'hero' => '1629990941644-45c40aac3d68',
        'cachorro' => '1555988483-a3bab1230203',
        'gatito' => '1529778873920-4da4926a72c2',
        'esterilizacion' => '1759164955427-14ca448a839d',
        'equipo' => '1700665537604-412e89a285c3',
        'caricia' => '1776880952098-1cc6dbbda513',
        'cierre' => '1657110605851-ebd3a5fcf650',
    ],
    'promesas' => [
        'Precios a la vista, antes de empezar',
        'Te explicamos todo, sin apuro',
        'Urgencias sin hora en nuestro horario',
        'Recordatorio de vacunas por WhatsApp',
    ],
    // Lo más pedido: tarjetas con foto (id del servicio => foto).
    'destacados' => [
        'consulta' => ['foto' => '1654895716780-b4664497420d', 'alt' => 'Perrito chihuahua parado en la camilla de la consulta, sostenido por la doctora', 'sello' => 'Con ficha clínica'],
        'vacuna-perro' => ['foto' => '1770836037275-38b44e4b101f', 'alt' => 'Veterinaria con guantes vacunando a un perro salchicha en la camilla', 'sello' => 'Sticker en el carnet'],
        'peluqueria' => ['foto' => '1719464454959-9cf304ef4774', 'alt' => 'Perrito blanco recién bañado mientras le recortan el pelo de la cara con tijeras', 'sello' => 'Con cita'],
    ],
    // Servicios: id => [nombre, detalle, precio desde].
    'servicios' => [
        'consulta' => ['Consulta general', 'Examen completo, peso y temperatura, y una conversación sin apuro sobre lo que le pasa.', 18000],
        'control' => ['Control sano', 'Para cachorros, gatitos y mascotas mayores: peso, piel, oídos, dientes y carnet al día.', 14000],
        'vacuna-perro' => ['Vacuna anual del perro', 'Séxtuple u óctuple, según lo que la doctora recomiende para tu perro.', 19000],
        'vacuna-gato' => ['Vacuna triple felina', 'La vacuna anual del gato, con revisión previa incluida.', 18000],
        'antirrabica' => ['Vacuna antirrábica', 'Para perros y gatos. Queda registrada en el carnet y en su ficha.', 12000],
        'desparasitacion' => ['Desparasitación', 'Interna y externa. El producto lo indica la doctora según la edad y el peso.', 9000],
        'esterilizacion' => ['Esterilización', 'Gatos, gatas, perros y perras, con control a los 10 días. Precios abajo.', 45000],
        'microchip' => ['Microchip y Ley Cholito', 'Implantamos el microchip y te ayudamos con la inscripción en el registro nacional.', 18000],
        'peluqueria' => ['Peluquería canina', 'Baño, corte, uñas y limpieza de oídos. Raza pequeña; mediana y grande, a consultar.', 16000],
        'urgencia' => ['Atención de urgencia', 'Sin hora, dentro de nuestro horario. Si llegas con una urgencia, pasas primero.', 28000],
    ],
    // Carnet de vacunas: especie => [[nombre, para qué, precio]].
    'vacunas' => [
        'perro' => [
            ['Séxtuple u óctuple', 'Protege contra las enfermedades virales más comunes del perro.', 19000],
            ['Antirrábica', 'Obligatoria y anual. Queda en el carnet.', 12000],
            ['Tos de las perreras', 'Recomendada si va a guardería, peluquería o parques.', 17000],
        ],
        'gato' => [
            ['Triple felina', 'Protege contra los virus respiratorios y digestivos más comunes del gato.', 18000],
            ['Leucemia felina', 'Para gatos que salen o conviven con otros gatos.', 22000],
            ['Antirrábica', 'También para gatos que no salen de la casa.', 12000],
        ],
    ],
    // El primer año de un cachorro o gatito, sin fechas ni dosis: las anota la doctora en el carnet.
    'primer_anio' => [
        ['Llega a la casa', 'Primer control: lo pesamos, revisamos piel, oídos y dientes, y le armamos su carnet.'],
        ['Vacunas de cachorro', 'Son varias visitas con unas semanas de diferencia. Las fechas quedan escritas en el carnet.'],
        ['Desparasitación', 'Según la edad y el peso. Te avisamos por WhatsApp cuando toque la siguiente.'],
        ['Microchip', 'Obligatorio para perros y gatos por la Ley Cholito. Lo inscribimos contigo.'],
        ['Esterilización', 'Conversamos la edad ideal según la especie, el tamaño y su salud.'],
    ],
    // Esterilización: [quién, precio].
    'esterilizacion' => [
        ['Gato', 45000],
        ['Gata', 65000],
        ['Perro hasta 15 kg', 75000],
        ['Perro de más de 15 kg', 95000],
        ['Perra hasta 15 kg', 95000],
        ['Perra de más de 15 kg', 125000],
    ],
    'resenas' => [
        ['Javiera M.', 5, 'La doctora Paula atendió a mi gata con una paciencia enorme y me explicó todo con dibujos. El precio fue exactamente el de la página.', 'hace 1 semana'],
        ['Don Héctor R.', 5, 'Llevo a mis perros hace diez años. Cuando el Tobi se enfermó un sábado en la mañana lo atendieron al tiro, sin hora.', 'hace 1 mes'],
        ['Constanza P.', 4, 'Muy cariñosos y ordenados con el carnet; me avisan por WhatsApp cuando toca la vacuna. La sala es chica y a veces hay que esperar.', 'hace 2 meses'],
    ],
];
