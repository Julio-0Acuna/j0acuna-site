<?php
declare(strict_types=1);

// Tina Rodante (Peñalolén): negocio ficticio de la versión Plataforma web.
// Peluquería canina en furgón: el furgón (con tina, agua tibia y energía propia) se estaciona afuera de la casa.
// Agenda con día y hora de llegada validados en el servidor ('entrega' en modo fecha), datos del perro como campos
// extra del checkout ('checkout_campos'), tienda de shampoo, colonias y accesorios, y cuentas "Convenio" para
// condominios, criaderos y guarderías. Sin modelos 3D ni menú QR.
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$horario = [
    1 => ['09:00', '19:00'],
    2 => ['09:00', '19:00'],
    3 => ['09:00', '19:00'],
    4 => ['09:00', '19:00'],
    5 => ['09:00', '19:00'],
    6 => ['09:00', '15:00'],
    7 => null,
];
// Tamaños del checkout (clave => texto). Las vistas los usan para la ficha de cada perro.
$tamanos = [
    'pequeno' => 'Pequeño (hasta 10 kg)',
    'mediano' => 'Mediano (10 a 25 kg)',
    'grande' => 'Grande (25 a 40 kg)',
    'gigante' => 'Gigante (más de 40 kg)',
    'gato' => 'Gato',
    'productos' => 'Solo compro productos',
];

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/peluqueria-canina/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_peluqueria_canina_plataforma',
    'prefijo_pedido' => 'TR',
    // La nota de prueba del WhatsApp dice "(Reserva de prueba desde la demo de peluquería canina – Plataforma web)".
    'nota_tipo' => 'Reserva',

    'negocio' => [
        'nombre' => 'Tina Rodante',
        'eslogan' => 'Peluquería canina a domicilio en furgón',
        'comuna' => 'Peñalolén',
        'horario' => $horario,
    ],
    'tamanos' => $tamanos,
    // Ruta de la semana del furgón (la muestran la portada y la cinta del layout). Día 1..7 => comunas.
    'ruta' => [
        1 => ['Peñalolén', 'La Reina'],
        2 => ['Ñuñoa', 'Providencia'],
        3 => ['Las Condes', 'La Reina'],
        4 => ['Macul', 'La Florida'],
        5 => ['Peñalolén', 'Ñuñoa'],
        6 => ['Peñalolén', 'La Reina', 'Las Condes'],
    ],

    // Convenios: condominios, criaderos y guarderías (jornadas del furgón y productos por volumen).
    'mayoristas' => true,
    // "Retiro" = traes a tu perro a la base de Peñalolén (o retiras productos). "Despacho" = el furgón va a tu casa.
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Peñalolén' => 2990,
        'La Reina' => 3990,
        'Ñuñoa' => 4990,
        'Macul' => 4990,
        'Las Condes' => 5990,
        'La Florida' => 5990,
        'Providencia' => 6990,
    ],
    'despacho_gratis_desde' => 45000,
    'pedido_minimo' => 0,
    // Agenda: desde mañana hasta tres semanas, sin domingos ni feriados; hora de llegada del furgón cada 30 min.
    'entrega' => [
        'modo' => 'fecha',
        'dias_min' => 1,
        'dias_max' => 21,
        'cerrado' => [7, '2026-12-08', '2026-12-25', '2027-01-01'],
        'horas' => ['09:00', '17:30'],
        'fecha_defecto' => 1,
    ],
    // Ficha del perro: la valida el motor y sale en la reserva, el panel, Mi cuenta (con la ficha de cada perro)
    // y el WhatsApp ("Tamaño: Mediano (10 a 25 kg)"). Quien solo compra productos elige "Solo compro productos".
    'checkout_campos' => [
        ['nombre' => 'perro', 'etiqueta' => 'Mascota', 'requerido' => false, 'max' => 30, 'placeholder' => 'Su nombre, ej.: Rocco', 'ayuda' => 'Déjalo en blanco si solo compras productos.'],
        [
            'nombre' => 'tamano', 'etiqueta' => 'Tamaño', 'tipo' => 'opcion', 'opciones' => $tamanos, 'placeholder' => 'Elige el tamaño',
            'error' => 'Cuéntanos el tamaño de tu perro o elige «Solo compro productos».',
        ],
        ['nombre' => 'raza', 'etiqueta' => 'Raza', 'requerido' => false, 'max' => 40, 'placeholder' => 'Ej.: quiltro, schnauzer, golden'],
        [
            'nombre' => 'pelo', 'etiqueta' => 'Pelo', 'tipo' => 'opcion', 'requerido' => false, 'placeholder' => 'No sé: que lo vean ustedes',
            'opciones' => ['corto' => 'Corto', 'largo' => 'Largo', 'rizado' => 'Rizado o lanudo', 'doble' => 'Doble manto'],
        ],
        [
            'nombre' => 'estacionar', 'etiqueta' => 'Dónde estacionar', 'tipo' => 'opcion', 'solo' => 'despacho', 'placeholder' => 'Elige dónde',
            'opciones' => ['calle' => 'En la calle, frente a la casa', 'condominio' => 'Dentro del condominio (aviso en conserjería)', 'pasaje' => 'En la esquina (vivo en un pasaje)'],
            'ayuda' => '¿Dónde se puede estacionar el furgón? Mide 5,4 m y lleva agua tibia y energía propia: no necesitamos nada de tu casa.',
            'error' => 'Cuéntanos dónde se puede estacionar el furgón.',
        ],
    ],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    // Peluquería canina: sin menú QR ni modelos 3D.
    'menu_qr' => false,

    'textos' => [
        'titulo_inicio' => 'Peluquería canina a domicilio',
        'titulo_destacados' => 'Lo más agendado',
        'titulo_catalogo' => 'Servicios y tienda',
        'buscar_placeholder' => 'Busca baño, corte, shampoo, pañoleta…',
        'sello_destacado' => 'Favorito',
        'ver_catalogo' => 'Ver servicios',
        'producto_agregar' => 'Agregar',
        'producto_quedan' => 'Quedan {n} cupos',
        // Convenios: condominios, criaderos y guarderías.
        'mayorista' => 'Convenio',
        'mayoristas' => 'Convenios',
        'mayorista_titulo' => '¿Administras un condominio, un criadero o una guardería?',
        'mayorista_texto' => 'Con una cuenta de convenio agendas jornadas del furgón para varios perros y compras shampoo y toallas por volumen a precio convenio. La aprobamos en el día.',
        'mayorista_quien' => 'Condominios, criaderos, guarderías y hoteles caninos.',
        'crear_cuenta_mayorista' => 'Crear cuenta de convenio',
        'precio_mayorista' => 'Precio convenio',
        'solo_mayoristas' => 'Solo convenios',
        'solo_para_mayoristas' => 'Solo para convenios',
        'cuenta_aprobada' => 'Aprobada: ves precios de convenio',
        'registro_pendiente' => 'Cuenta creada. Tu cuenta de convenio queda pendiente hasta que la aprobemos; mientras, ves precios normales.',
        'mayorista_aprobado' => 'Convenio aprobado: ya ve sus precios.',
        'mayoristas_aprobados' => 'Convenios aprobados',
        'mayoristas_esperan' => '{n} convenio(s) esperan aprobación',
        'registro_empresa' => 'Condominio, criadero o guardería',
        'registro_particular_texto' => 'Para agendar el furgón en tu casa.',
        // Dónde y cuándo.
        'retiro_titulo' => 'Lo llevo a la base de Peñalolén',
        'retiro_texto' => 'Sin costo de traslado. También para retirar productos.',
        'despacho_titulo' => 'El furgón va a mi casa',
        'checkout_costo_comuna' => 'Traslado según tu comuna.',
        'fecha_titulo' => 'Día',
        'fecha_ayuda' => 'Atendemos de lunes a sábado. Si compras solo productos, es el día en que te los llevamos.',
        'hora_titulo' => 'Hora de llegada del furgón',
        'hora_cualquiera' => 'La primera hora libre',
        'hora_entrega_fecha' => 'A las {hora}',
        'pedido_titulo' => '¡Furgón agendado!',
        'pedido_exito_prueba' => '¡Pago de prueba aprobado! El furgón quedó agendado.',
        'pedido_exito_al_recibir' => '¡Listo, quedó agendado! Pagas al terminar el baño o al recibir.',
        // Checkout de una reserva del furgón.
        'checkout_titulo' => 'Agendar el furgón',
        'checkout_como_recibes' => '¿Dónde lo atendemos?',
        'checkout_campos_titulo' => 'Cuéntanos de tu perro',
        'checkout_tu_pedido' => 'Tu reserva',
        'checkout_resumen' => 'Resumen de la reserva',
        'checkout_despacho' => 'Traslado del furgón',
        'checkout_confirmar' => 'Confirmar y agendar',
        'checkout_comprando_como' => 'Agendando como {nombre}',
        'checkout_nota_placeholder' => 'Ej.: le tiene miedo al secador, el timbre no funciona, corte más corto en las patas…',
        'carrito_titulo' => 'Tu reserva',
        'carrito_vacio' => 'Aún no eliges un servicio ni un producto.',
        'carrito_continuar' => 'Elegir día y hora',
        'carrito_gratis_retiro' => 'Sin traslado',
        'carrito_despacho_nota' => 'El traslado del furgón se calcula según tu comuna en el siguiente paso.',
        'carrito_despacho_nota_gratis' => 'El traslado del furgón se calcula según tu comuna en el siguiente paso (gratis desde {gratis}).',
        'carrito_falta_gratis' => 'Te faltan {falta} para que el traslado del furgón sea gratis.',
        'pago_al_recibir' => 'Pagar al terminar',
        'pago_al_recibir_texto' => 'Efectivo, débito, crédito o transferencia cuando te devolvemos a tu perro.',
        // Estados: agendado → confirmado → en el furgón (o en camino) → listo.
        'pedido_paso_nuevo' => 'Agendado',
        'pedido_codigo' => 'Reserva {codigo}',
        'pedido_cancelado' => 'Reserva cancelada',
        'estado_nuevo' => 'Agendado',
        'estado_preparando' => 'Confirmado',
        'estado_listo' => 'En el furgón',
        'estado_listo_retiro' => 'En la base',
        'estado_listo_despacho' => 'El furgón va en camino',
        'estado_entregado' => 'Listo y regio',
        'estado_cancelado' => 'Cancelado',
        'panel_avanzar_nuevo' => 'Confirmar',
        'panel_avanzar_preparando' => 'Marcar en el furgón',
        'panel_avanzar_listo' => 'Marcar listo',
        'cuenta_pedidos' => 'Mis reservas y compras',
        'cuenta_sin_pedidos' => 'Aún no agendas el furgón.',
        'cuenta_repetir' => 'Agendar de nuevo',
        // Resumen de WhatsApp.
        'resumen_saludo' => 'Hola, agendé la reserva {codigo} en {negocio}:',
        'resumen_entrega_retiro' => 'Dónde: en la base de Peñalolén',
        'resumen_entrega_despacho' => 'Dónde: el furgón va a {comuna}',
        'resumen_despacho' => 'Traslado del furgón: {costo}',
        'resumen_pago_al_recibir' => 'Pago: al terminar',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Dueña de Rocco y Luna: fichas de sus perros con su historial, direcciones guardadas y «agendar de nuevo».', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Convenio', 'detalle' => 'Condominio aprobado: jornadas del furgón para seis perros y productos a precio convenio.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueña', 'detalle' => 'Panel: reservas por estado con la ficha de cada perro, cupos, productos, convenios por aprobar y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que la dueña puede elegir al crear o editar un servicio o producto (no hay subida de archivos).
    'fotos' => [
        '1597603413826-cd1c06b05222' => 'Perrito mojado en la tina',
        '1784563704510-82a1d9e7c0a8' => 'Perro sacudiéndose el agua',
        '1597852075145-6e31afc637a7' => 'Perro mojado en blanco y negro',
        '1776429011293-b46885b813ac' => 'Samoyedo sonriente',
        '1694372550361-e523409fb194' => 'Perrito peinado con cepillo',
        '1789846959243-0fa05d02d55f' => 'Caniche con lazos rojos',
        '1680374642639-67176e8e70ee' => 'Golden con pañoleta',
        '1534628526458-a8de087b1123' => 'Shih tzu caminando',
        '1785941643612-c5b1ab580297' => 'Samoyedo en brazos',
        '1778072831257-fa0338457329' => 'Pomerania mojado en el pasto',
        '1767363592636-6ec7ecd3c06b' => 'Patita de cachorro',
        '1709777571247-39ad71a2d86e' => 'Perrito blanco sobre fondo rosado',
        '1675430423594-57012c82cdfc' => 'Perrito en una manta',
        '1513360371669-4adf3dd7dff8' => 'Gato acariciado',
        '1568051131683-700fd7420b5a' => 'Gato en la alfombra',
        '1771535626784-7de36a960c1a' => 'Botellas rosada y amarilla',
        '1771519902689-0a5c7be8efe6' => 'Tres botellas de colores',
        '1772037533346-203fc4d2dca0' => 'Tres botellas con bomba',
        '1623143445418-40c192fa3d11' => 'Frasco sobre fondo naranjo',
        '1783268287883-527863dac66a' => 'Golden con pañoleta azul',
        '1667716705760-233650f8f3fe' => 'Collar rojo de cuero',
        '1708062270783-0e9fa7f4c1c1' => 'Correa roja',
        '1682969651476-6fb48aba8b03' => 'Placa con nombre',
        '1552995557-265a81653db9' => 'Perro con pelota',
        '1676440150226-62a0b20105c7' => 'Galleta con forma de hueso',
        '1766947910763-5a9465ff3f21' => 'Cachorro mojado en las manos',
        '1508814437933-f0c7d18a9217' => 'Dos perros con pañoleta',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Tina Rodante · peluquería canina a domicilio',
        'categoria' => 'Peluquería canina a domicilio',
        'comuna' => 'Peñalolén',
        'horario' => $horario,
        'nota' => 4.8,
        'resenas' => [
            ['Francisca O.', 5, 'Agendé en la web, el furgón llegó a la hora y en una hora y media me devolvieron a la Luna regia. Ni se dio cuenta de que no salió de la casa.', 'hace 5 días'],
            ['Tomás V.', 5, 'Mi golden tiene 12 años y ya no aguanta el auto. Con el furgón afuera de la casa es otra cosa. En la ficha de mi cuenta veo cuándo le toca el próximo baño.', 'hace 2 semanas'],
            ['Condominio Las Brisas (ficticio)', 4, 'Con el convenio el furgón viene un sábado al mes y los vecinos se inscriben en conserjería. Ojo que los cupos se llenan rápido.', 'hace 1 mes'],
        ],
    ],
];
