<?php
declare(strict_types=1);

// Hocico (Maipú): negocio ficticio de la versión Plataforma web.
// Pet shop con clínica veterinaria: tienda de alimentos, arena, accesorios y farmacia con despacho,
// reserva de hora (día y hora validados en el servidor; los datos de la mascota son campos extra del checkout)
// y cuentas para refugios y criaderos.
// Los productos de farmacia dicen "Requiere evaluación veterinaria" y ningún texto indica dosis.
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$horario = [
    1 => ['10:00', '20:00'],
    2 => ['10:00', '20:00'],
    3 => ['10:00', '20:00'],
    4 => ['10:00', '20:00'],
    5 => ['10:00', '20:00'],
    6 => ['10:00', '20:00'],
    7 => ['11:00', '17:00'],
];

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/veterinaria/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_veterinaria_plataforma',
    'prefijo_pedido' => 'HO',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Hocico',
        'eslogan' => 'Pet shop y clínica veterinaria',
        'comuna' => 'Maipú',
        'horario' => $horario,
    ],

    // Refugios, fundaciones y criaderos: precios por volumen tras la aprobación del dueño.
    'mayoristas' => true,
    // "Retiro" = vas a la tienda (con tu mascota si reservaste una hora).
    // "Despacho" = te llevamos los productos; las vacunas y controles también se pueden hacer a domicilio.
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Maipú' => 2990,
        'Cerrillos' => 3990,
        'Estación Central' => 4990,
        'Pudahuel' => 4990,
        'Lo Prado' => 4990,
        'Padre Hurtado' => 5990,
        'Santiago' => 5990,
    ],
    'despacho_gratis_desde' => 35000,
    'pedido_minimo' => 0,
    // Día de la hora o del despacho: desde mañana hasta dos semanas, sin domingos ni feriados, cada 30 min.
    'entrega' => [
        'modo' => 'fecha',
        'dias_min' => 1,
        'dias_max' => 14,
        'cerrado' => [7, '2026-12-08', '2026-12-25', '2027-01-01'],
        'horas' => ['10:00', '19:00'],
    ],
    // Datos de la mascota cuando se reserva una hora (opcionales: también se compra solo alimento o arena).
    // El motor los valida y los muestra en el pedido, el panel, Mi cuenta y el WhatsApp ("Mascota: Luna").
    'checkout_campos' => [
        ['nombre' => 'mascota', 'etiqueta' => 'Mascota', 'requerido' => false, 'max' => 30, 'ayuda' => 'Su nombre.'],
        [
            'nombre' => 'especie', 'etiqueta' => 'Especie', 'tipo' => 'opcion', 'requerido' => false,
            'opciones' => ['perro' => 'Perro', 'gato' => 'Gato', 'conejo' => 'Conejo', 'cuy' => 'Cuy', 'huron' => 'Hurón', 'ave' => 'Ave', 'otra' => 'Otra especie'],
        ],
        ['nombre' => 'edad', 'etiqueta' => 'Edad', 'requerido' => false, 'max' => 20, 'ayuda' => 'Ej.: 3 años o 4 meses.'],
    ],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    // Veterinaria: sin menú QR ni modelos 3D.
    'menu_qr' => false,
    'modelos3d' => ['saco-alimento', 'alimento-cachorro-3kg', 'alimento-perro-senior-10kg', 'alimento-perro-control-peso-12kg', 'hueso', 'cama-mascota', 'alimento-perro-adulto-20kg-pack-3'],

    'textos' => [
        'titulo_inicio' => 'Pet shop y clínica en Maipú',
        'titulo_destacados' => 'Lo más pedido',
        'titulo_catalogo' => 'Tienda y clínica',
        'buscar_placeholder' => 'Busca alimento, arena, vacuna…',
        'sello_destacado' => 'Favorito',
        'ver_catalogo' => 'Ver la tienda',
        // Refugios y criaderos (nombre compuesto: se cambian también las claves con género).
        'mayorista' => 'Refugio o criadero',
        'mayoristas' => 'Refugios y criaderos',
        'mayorista_titulo' => '¿Tienes un refugio o un criadero?',
        'mayorista_texto' => 'Con una cuenta de refugio o criadero ves precios por volumen en sacos de alimento y arena, y jornadas de vacunación y esterilización con precio social. La aprobamos en el día.',
        'mayorista_quien' => 'Refugios, fundaciones y criaderos registrados.',
        'crear_cuenta_mayorista' => 'Crear cuenta de refugio o criadero',
        'precio_mayorista' => 'Precio por volumen',
        'cuenta_aprobada' => 'Aprobada: ves precios por volumen',
        'registro_pendiente' => 'Cuenta creada. Tu cuenta de refugio o criadero queda pendiente hasta que la aprobemos; mientras, ves precios normales.',
        'mayorista_aprobado' => 'Cuenta aprobada: ya ve los precios por volumen.',
        'mayoristas_aprobados' => 'Refugios y criaderos aprobados',
        'mayoristas_esperan' => '{n} refugio(s) o criadero(s) esperan aprobación',
        // Cómo y cuándo: retiro en tienda (y hora en la clínica) o despacho a domicilio.
        'retiro_titulo' => 'Voy a la tienda',
        'retiro_texto' => 'Sin costo. Si reservaste una hora, llega con tu mascota; tus productos te esperan en la caja.',
        'despacho_titulo' => 'Despacho o visita a domicilio',
        'fecha_titulo' => 'Día de tu hora o del despacho',
        'fecha_ayuda' => 'Atendemos y despachamos de lunes a sábado.',
        'hora_titulo' => 'Hora',
        'hora_cualquiera' => 'Cualquier hora del día',
        'hora_entrega_fecha' => 'A las {hora}',
        'pedido_titulo' => '¡Todo agendado!',
        'pedido_exito_prueba' => '¡Pago de prueba aprobado! Tu pedido y tu hora quedaron agendados.',
        'pedido_exito_al_recibir' => '¡Listo, quedó agendado! Pagas en la tienda o al recibir.',
        // Checkout: pedido de la tienda y hora en la clínica en un mismo paso.
        'checkout_como_recibes' => '¿Vienes a Hocico o vamos a tu casa?',
        'checkout_campos_titulo' => '¿Reservaste una hora? Cuéntanos de tu mascota',
        'checkout_confirmar' => 'Confirmar y agendar',
        'pedido_paso_nuevo' => 'Agendado',
        'estado_listo_retiro' => 'Te esperamos',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Dueña de una gata y un perro, con direcciones guardadas y pedidos anteriores para repetir el alimento de siempre.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Refugio', 'detalle' => 'Refugio aprobado: ve precios por volumen, packs de sacos y jornadas de vacunación.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueña', 'detalle' => 'Panel: horas y pedidos por estado, productos, cupos, refugios por aprobar y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que la dueña puede elegir al crear o editar un producto (no hay subida de archivos).
    'fotos' => [
        '1764249453874-46864677b10e' => 'Alimento seco para perro',
        '1764249453850-faace6e57444' => 'Alimento seco de cerca',
        '1676193866128-03a926df76ef' => 'Plato con alimento',
        '1714068691210-073dc52c6c1d' => 'Perro comiendo de su plato',
        '1596854331442-3cf47265cefb' => 'Gato comiendo sobre fondo rosado',
        '1764576504536-dbdfa4cb1d9a' => 'Gato naranjo comiendo',
        '1580238169544-86bf7cd8c84c' => 'Gato junto a su plato',
        '1568640347023-a616a30bc3bd' => 'Galletas con forma de hueso',
        '1757670919588-1fe3b3df3dfa' => 'Arena sanitaria',
        '1760615303386-d025db9d6ae2' => 'Cachorro durmiendo en su cama',
        '1545249390-6bdfa286032f' => 'Gatito en un rascador',
        '1787561375245-efc541f1ee87' => 'Perro con arnés morado',
        '1708062270853-ee7c66b69f07' => 'Correa roja',
        '1761614282055-29e039aac354' => 'Gato en transportadora',
        '1591946614720-90a587da4a36' => 'Perro con un peluche',
        '1561037404-61cd46aa615b' => 'Perro sobre fondo rosado',
        '1537151608828-ea2b11777ee8' => 'Cachorro sobre fondo naranjo',
        '1592194996308-7b43878e84a6' => 'Gatito sobre fondo turquesa',
        '1652342516868-50eac8c9af0d' => 'Caniche recién peinado',
        '1597595735781-6a57fb8e3e3d' => 'Perro en el baño',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Hocico · pet shop y clínica',
        'categoria' => 'Tienda de mascotas y veterinaria',
        'comuna' => 'Maipú',
        'horario' => $horario,
        'nota' => 4.8,
        'resenas' => [
            ['Valentina R.', 5, 'Pido el saco de alimento por la web cada mes y llega al día siguiente. Además agendé la vacuna de mi gata y la atendieron a la hora exacta.', 'hace 5 días'],
            ['Felipe A.', 4, 'Buenos precios y la peluquería quedó impecable. Los sábados se llena: conviene reservar en la web con un par de días.', 'hace 3 semanas'],
            ['Refugio Patas Arriba (ficticio)', 5, 'Con la cuenta de refugio compramos los sacos por volumen y nos hicieron una jornada de vacunación en el refugio.', 'hace 2 meses'],
        ],
    ],
];
