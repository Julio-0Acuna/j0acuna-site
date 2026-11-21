<?php
declare(strict_types=1);

// Vía Rápida (La Florida): negocio ficticio de la versión Plataforma web.
// Centro de servicio automotriz con reserva de hora (día y hora validados en el servidor),
// tienda de servicios y accesorios, y cuentas para empresas con flota.
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$horario = [
    1 => ['08:30', '18:30'],
    2 => ['08:30', '18:30'],
    3 => ['08:30', '18:30'],
    4 => ['08:30', '18:30'],
    5 => ['08:30', '18:30'],
    6 => ['09:00', '14:00'],
    7 => null,
];

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/taller/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_taller_plataforma',
    'prefijo_pedido' => 'VR',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan'], 'Reserva'),

    'negocio' => [
        'nombre' => 'Vía Rápida',
        'eslogan' => 'Centro de servicio automotriz con hora reservada',
        'comuna' => 'La Florida',
        'horario' => $horario,
    ],

    // Clientes "flota": empresas con furgones, camionetas o autos de trabajo.
    'mayoristas' => true,
    // "Retiro" = el cliente trae el auto (o retira accesorios en el mesón).
    // "Despacho" = retiramos y devolvemos el auto en su casa u oficina (costo por comuna).
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'La Florida' => 4990,
        'Puente Alto' => 6990,
        'Peñalolén' => 5990,
        'Macul' => 5990,
        'La Granja' => 5990,
        'San Joaquín' => 6990,
        'Ñuñoa' => 7990,
    ],
    'despacho_gratis_desde' => 150000,
    'pedido_minimo' => 0,
    // Reserva de hora: desde mañana hasta 3 semanas, sin domingos ni feriados, cada 30 min.
    'entrega' => [
        'modo' => 'fecha',
        'dias_min' => 1,
        'dias_max' => 21,
        'cerrado' => [7, '2026-12-08', '2026-12-25', '2027-01-01'],
        'horas' => ['08:30', '17:30'],
    ],
    // Ficha del auto en el checkout: se valida en el servidor y sale en la reserva, el panel y el WhatsApp.
    'checkout_campos' => [
        ['nombre' => 'patente', 'etiqueta' => 'Patente', 'normalizar' => 'patente', 'patron' => '/^[A-Z]{2}[A-Z0-9]{2}\d{2}$/', 'max' => 6,
            'ayuda' => 'Ej.: ABCD12 o AB1234', 'error' => 'Escribe la patente con sus 6 caracteres, por ejemplo ABCD12 o AB1234.'],
        ['nombre' => 'vehiculo', 'etiqueta' => 'Vehículo', 'max' => 40, 'ayuda' => 'Marca y modelo, ej.: SUV mediano'],
        ['nombre' => 'anio', 'etiqueta' => 'Año', 'tipo' => 'numero', 'min' => 1970, 'max' => (int) date('Y') + 1],
        ['nombre' => 'km', 'etiqueta' => 'Kilometraje', 'tipo' => 'numero', 'min' => 0, 'max' => 1500000, 'ayuda' => 'Aproximado, ej.: 84.000'],
    ],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    // Taller: sin menú QR ni modelos 3D.
    'menu_qr' => false,
    'modelos3d' => ['neumatico', 'extintor'],

    'textos' => [
        'titulo_inicio' => 'Reserva tu hora en La Florida',
        'titulo_destacados' => 'Lo más reservado',
        'titulo_catalogo' => 'Servicios y accesorios',
        'buscar_placeholder' => 'Busca aceite, frenos, alineación…',
        'sello_destacado' => 'Más pedido',
        'ver_catalogo' => 'Ver servicios',
        // Clientes con flota (femenino: se cambian también las claves con género).
        'mayorista' => 'Flota',
        'mayoristas' => 'Flotas',
        'mayorista_titulo' => '¿Tu empresa tiene flota?',
        'mayorista_texto' => 'Furgones, camionetas o autos de trabajo: con una cuenta de flota ves precios por volumen, planes de mantención y revisiones antes de ruta. La aprobamos en el día.',
        'mayorista_quien' => 'Empresas con furgones, camionetas o autos de trabajo.',
        'crear_cuenta_mayorista' => 'Crear cuenta de flota',
        'precio_mayorista' => 'Precio flota',
        'cuenta_aprobada' => 'Aprobada: ves precios de flota',
        'registro_pendiente' => 'Cuenta creada. Tu cuenta de flota queda pendiente hasta que la aprobemos; mientras, ves precios normales.',
        'mayorista_aprobado' => 'Flota aprobada: ya ve sus precios.',
        'mayoristas_aprobados' => 'Flotas aprobadas',
        'mayoristas_esperan' => '{n} flota(s) esperan aprobación',
        // Cómo llega el auto y cuándo.
        'retiro_titulo' => 'Llevo mi auto al taller',
        'retiro_texto' => 'Sin costo. Llega a tu hora; los accesorios se retiran en el mesón.',
        'despacho_titulo' => 'Retiro y entrega a domicilio',
        'fecha_titulo' => 'Día de tu hora',
        'fecha_ayuda' => 'Atendemos de lunes a sábado.',
        'hora_titulo' => 'Hora',
        'hora_cualquiera' => 'Cualquier hora',
        'hora_entrega_fecha' => 'A las {hora}',
        'pedido_titulo' => '¡Hora reservada!',
        'pedido_exito_prueba' => '¡Pago de prueba aprobado! Tu hora quedó reservada.',
        'pedido_exito_al_recibir' => '¡Reserva recibida! Pagas en el taller.',
        // Carrito, checkout, reserva y cuenta: se habla de reservas, no de compras.
        'carrito_titulo' => 'Tu reserva',
        'carrito_vacio' => 'Aún no agregas servicios a tu reserva.',
        'carrito_agregado' => 'Agregaste «{producto}» a tu reserva.',
        'carrito_continuar' => 'Elegir día y hora',
        'carrito_vaciar' => 'Vaciar reserva',
        'carrito_gratis_retiro' => 'Sin costo (lo traes tú)',
        'carrito_despacho_nota' => 'El retiro a domicilio se calcula según tu comuna en el siguiente paso.',
        'carrito_despacho_nota_gratis' => 'El retiro a domicilio se calcula según tu comuna en el siguiente paso (gratis desde {gratis}).',
        'checkout_titulo' => 'Reservar hora',
        'checkout_como_recibes' => '¿Dónde dejas el auto?',
        'checkout_campos_titulo' => 'Tu auto',
        'checkout_nota' => '¿Qué le notas al auto?',
        'checkout_resumen' => 'Resumen de la reserva',
        'checkout_tu_pedido' => 'Tu reserva',
        'checkout_despacho' => 'Retiro a domicilio',
        'checkout_confirmar' => 'Confirmar reserva',
        'checkout_comprando_como' => 'Reservando como {nombre}',
        'pago_al_recibir' => 'Pagar en el taller',
        'pago_al_recibir_texto' => 'Efectivo, débito o crédito al retirar el auto.',
        'pedido_codigo' => 'Reserva {codigo}',
        'pedido_cancelado' => 'Reserva cancelada',
        'pedido_estado' => 'Estado de la reserva',
        'pedido_despacho' => 'Retiro a domicilio',
        'pedido_mis_pedidos' => 'Ver mis reservas',
        'pedido_seguir' => 'Ver más servicios',
        'cuenta_pedidos' => 'Mis reservas',
        'cuenta_sin_pedidos' => 'Aún no tienes reservas.',
        'cuenta_repetir' => 'Repetir reserva',
        'panel_pedidos' => 'Reservas',
        'panel_pedidos_hoy' => 'Reservas de hoy',
        'panel_pedidos_nuevos' => 'Reservas nuevas',
        'panel_sin_pedidos' => 'No hay reservas aquí.',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Particular con su auto, una dirección guardada y servicios anteriores para repetir.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Flota', 'detalle' => 'Empresa de reparto aprobada: ve precios de flota y planes para furgones.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueño', 'detalle' => 'Panel: reservas por estado, servicios, cupos, flotas y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que el dueño puede elegir al crear o editar un producto (no hay subida de archivos).
    'fotos' => [
        '1777118947168-b6e806cb80cf' => 'Filtro de aceite',
        '1643700973089-baa86a1ab9ee' => 'Mecánico bajo el auto revisando el aceite',
        '1764869427688-3e97480f4b82' => 'Motor y suspensión vistos desde abajo',
        '1613214150384-14921ff659b2' => 'Disco y caliper en el banco',
        '1747330038945-fb39f6e4b73c' => 'Llanta y disco de freno',
        '1760317890359-4e6bb111e501' => 'Suspensión delantera desarmada',
        '1599474151975-1f978922fa02' => 'Mecánico bajo un auto en el elevador',
        '1613214150333-53afb7561e6d' => 'Rueda en la alineadora',
        '1764699186616-8f707281e4f3' => 'Neumáticos apilados',
        '1571335746824-742511d49bce' => 'Banda de rodadura de neumáticos',
        '1599082267768-4815b2ea6bd2' => 'Llave de impacto en la rueda',
        '1625047509248-ec889cbff17f' => 'Mecánico revisando el motor',
        '1597766325363-f5576d851d6a' => 'Pinzas en la batería',
        '1587121892719-1711ec9cc798' => 'Rejilla del aire acondicionado',
        '1702146713858-8e7d1cc29fe8' => 'Mecánico bajo un vehículo',
        '1771491237209-b89bc7290588' => 'Limpieza del tablero',
        '1765959106936-851735565c12' => 'Cámara en el parabrisas',
        '1508786250378-b165238d6e8b' => 'Plumilla del parabrisas',
        '1625958936686-a9343dc35b5b' => 'Extintor',
        '1597766370173-a14200ec0b42' => 'Cables para batería',
        '1782885044133-27c4df887d96' => 'Bujías',
        '1764347923709-fc48487f2486' => 'Soporte de celular',
        '1761934658331-2e00b20dc6c6' => 'Paño de microfibra',
        '1775865937358-c7d2972e78ec' => 'Furgones estacionados',
        '1609682932589-5ef2bd85980d' => 'Maza y disco de freno',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Vía Rápida',
        'categoria' => 'Centro de servicio automotriz',
        'comuna' => 'La Florida',
        'horario' => $horario,
        'nota' => 4.7,
        'resenas' => [
            ['Macarena S.', 5, 'Reservé el cambio de aceite para las 9:00 y a las 9:40 estaba lista. Pagué en línea y no hice fila.', 'hace 6 días'],
            ['Cristián R.', 4, 'Buena atención y precios claros en la web. El sábado se llena: conviene reservar con dos o tres días.', 'hace 1 mes'],
            ['Logística Andina (flota)', 5, 'Llevamos nuestros cuatro furgones con la cuenta de flota: precios por volumen y nos avisan cuándo toca la siguiente mantención.', 'hace 2 meses'],
        ],
    ],
];
