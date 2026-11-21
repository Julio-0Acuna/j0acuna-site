<?php
declare(strict_types=1);

// Filo Barbería (Ñuñoa, Providencia y Maipú): negocio ficticio de la versión Plataforma web.
// Cadena de barberías con agenda por barbero (día, hora y barbero validados en el servidor por el motor:
// 'entrega' y 'checkout_campos'), tienda de productos, planes mensuales y cuentas "Empresa" para convenios corporativos.
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$horario = [
    1 => ['10:00', '21:00'],
    2 => ['10:00', '21:00'],
    3 => ['10:00', '21:00'],
    4 => ['10:00', '21:00'],
    5 => ['10:00', '21:00'],
    6 => ['10:00', '20:00'],
    7 => null,
];

// Sucursales y barberos: los muestran vistas/portada.php y vistas/layout.php, y arman las opciones
// del campo "Barbero" del checkout (por sucursal: primero "el primero disponible", después cada barbero).
$sucursales = [
    'nunoa' => ['nombre' => 'Ñuñoa', 'detalle' => 'Casa matriz · 6 sillones', 'foto' => '1792735207211-a96ca7e6ea77', 'alt' => 'Barbería luminosa con sillones de cuero negro y cromo frente a grandes espejos con marco de madera'],
    'providencia' => ['nombre' => 'Providencia', 'detalle' => 'A pasos del metro · 5 sillones', 'foto' => '1576168056582-0a851a87ab8e', 'alt' => 'Salón de barbería con sillones de cuero burdeos y pared de rayas blancas y negras'],
    'maipu' => ['nombre' => 'Maipú', 'detalle' => 'La más nueva · 4 sillones', 'foto' => '1536520002442-39764a41e987', 'alt' => 'Barbería larga con sillones en fila, lámparas colgantes y muro de ladrillo'],
];
$barberos = [
    'benjamin' => ['nombre' => 'Benjamín', 'sucursal' => 'nunoa', 'oficio' => 'Degradados y diseños', 'dias' => 'Lun a vie', 'foto' => '1676989859871-150f3579862a', 'alt' => 'Barbero de barba con camiseta blanca y brazos cruzados, sonriendo'],
    'matias' => ['nombre' => 'Matías', 'sucursal' => 'nunoa', 'oficio' => 'Barba y afeitado', 'dias' => 'Mar a sáb', 'foto' => '1562004760-aceed7bb0fe3', 'alt' => 'Barbero con jopo peinado y camisa azul frente a un muro de ladrillo'],
    'rodrigo' => ['nombre' => 'Rodrigo', 'sucursal' => 'providencia', 'oficio' => 'Cortes clásicos y ejecutivos', 'dias' => 'Lun a vie', 'foto' => '1544225917-1bf66feace19', 'alt' => 'Barbero calvo de barba larga con chaqueta gris'],
    'felipe' => ['nombre' => 'Felipe', 'sucursal' => 'providencia', 'oficio' => 'Barba larga y tinte', 'dias' => 'Mié a sáb', 'foto' => '1649058047388-b49c028df9ac', 'alt' => 'Barbero de barba tupida con gorro negro y chaqueta beige'],
    'cristobal' => ['nombre' => 'Cristóbal', 'sucursal' => 'maipu', 'oficio' => 'Fade y texturizados', 'dias' => 'Lun a sáb', 'foto' => '1587776535733-b4c80a99ef82', 'alt' => 'Retrato de perfil en blanco y negro de un barbero de barba corta'],
    'sebastian' => ['nombre' => 'Sebastián', 'sucursal' => 'maipu', 'oficio' => 'Cortes con tijera', 'dias' => 'Mar a sáb', 'foto' => '1577577815304-55481157868e', 'alt' => 'Barbero joven con jopo y barba corta en un local iluminado'],
];
$opcionesBarbero = [];
foreach ($sucursales as $sid => $s) {
    $opcionesBarbero['cualquiera-' . $sid] = 'El primero disponible (' . $s['nombre'] . ')';
    foreach ($barberos as $bid => $b) {
        if ($b['sucursal'] === $sid) {
            $opcionesBarbero[$bid] = $b['nombre'] . ' (' . $s['nombre'] . ')';
        }
    }
}

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/barberia/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_barberia_plataforma',
    'prefijo_pedido' => 'FB',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Filo Barbería',
        'eslogan' => 'Barberías con agenda online en Ñuñoa, Providencia y Maipú',
        'comuna' => 'Ñuñoa',
        'horario' => $horario,
    ],

    'sucursales' => $sucursales,
    'barberos' => $barberos,

    // Cuentas "Empresa": convenios corporativos con precios por volumen.
    'mayoristas' => true,
    // "Retiro" = te atiendes en la sucursal. "Despacho" = el barbero va a tu casa u oficina (costo por comuna).
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Ñuñoa' => 7990,
        'Providencia' => 7990,
        'Maipú' => 7990,
        'Macul' => 8990,
        'Santiago' => 8990,
        'Cerrillos' => 8990,
        'La Reina' => 9990,
        'Las Condes' => 9990,
    ],
    'despacho_gratis_desde' => 100000,
    'pedido_minimo' => 0,
    // Reserva de hora: desde mañana hasta dos semanas, sin domingos ni feriados, cada 30 minutos.
    'entrega' => [
        'modo' => 'fecha',
        'dias_min' => 1,
        'dias_max' => 14,
        'cerrado' => [7, '2026-12-08', '2026-12-25', '2027-01-01'],
        'horas' => ['10:00', '19:30'],
    ],
    // Barbero y sucursal: el motor lo valida contra la lista y lo muestra en el pedido, el panel, Mi cuenta y el
    // WhatsApp ("Barbero: Benjamín (Ñuñoa)"). "Reservar con…" de la portada lo deja elegido (?barbero=benjamin).
    'checkout_campos' => [
        [
            'nombre' => 'barbero', 'etiqueta' => 'Barbero', 'tipo' => 'opcion', 'opciones' => $opcionesBarbero,
            'ayuda' => 'Queda anotado en tu reserva. Si ese día no atiende, te ofrecemos otra hora por WhatsApp.',
            'error' => 'Elige con quién te atiendes (o el primero disponible de una sucursal).',
        ],
    ],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    // Barbería: sin menú QR ni modelos 3D.
    'menu_qr' => false,
    'modelos3d' => ['pomada', 'aceite-barba-30ml', 'frasco'],

    'textos' => [
        'titulo_inicio' => 'Reserva tu hora en Filo',
        'titulo_destacados' => 'Lo más reservado',
        'titulo_catalogo' => 'Servicios, planes y productos',
        'buscar_placeholder' => 'Busca degradado, barba, pomada…',
        'sello_destacado' => 'Más reservado',
        'ver_catalogo' => 'Ver servicios',
        // Clientes "Empresa" (femenino: se cambian también las claves con género).
        'mayorista' => 'Empresa',
        'mayoristas' => 'Empresas',
        'mayorista_titulo' => '¿Quieres Filo para tu equipo?',
        'mayorista_texto' => 'Con una cuenta para empresas ves precios de convenio, packs de cortes para tus colaboradores y jornadas de barbería en tu oficina. La aprobamos en el día.',
        'mayorista_quien' => 'Empresas con convenio para sus colaboradores.',
        'crear_cuenta_mayorista' => 'Crear cuenta para empresas',
        'precio_mayorista' => 'Precio empresa',
        'solo_mayoristas' => 'Solo empresas',
        'solo_para_mayoristas' => 'Solo para empresas',
        'cuenta_aprobada' => 'Aprobada: ves precios de convenio',
        'registro_pendiente' => 'Cuenta creada. Tu cuenta de empresa queda pendiente hasta que la aprobemos; mientras, ves precios normales.',
        'mayorista_aprobado' => 'Empresa aprobada: ya ve sus precios de convenio.',
        'mayoristas_aprobados' => 'Empresas aprobadas',
        'mayoristas_esperan' => '{n} empresa(s) esperan aprobación',
        // Dónde y cuándo.
        'retiro_titulo' => 'En la sucursal',
        'retiro_texto' => 'Llega 5 minutos antes. Los productos los retiras en caja.',
        'despacho_titulo' => 'Barbero a domicilio u oficina',
        'fecha_titulo' => 'Día de tu hora',
        'fecha_ayuda' => 'De lunes a sábado, con horas cada 30 minutos.',
        'hora_titulo' => 'Hora',
        'hora_cualquiera' => 'La primera hora libre',
        'hora_entrega_fecha' => 'A las {hora}',
        'pedido_titulo' => '¡Hora reservada!',
        'pedido_exito_prueba' => '¡Pago de prueba aprobado! Tu hora quedó reservada.',
        'pedido_exito_al_recibir' => '¡Reserva recibida! Pagas en la sucursal.',
        // Checkout de una reserva y el barbero.
        'checkout_titulo' => 'Reservar hora',
        'checkout_como_recibes' => '¿Dónde te atendemos?',
        'checkout_extra_opcion' => 'Elige barbero y sucursal',
        'checkout_tu_pedido' => 'Tu reserva',
        'checkout_resumen' => 'Resumen de la reserva',
        'checkout_confirmar' => 'Confirmar reserva',
        'checkout_despacho' => 'A domicilio',
        'carrito_gratis_retiro' => 'Sin costo',
        'checkout_comprando_como' => 'Reservando como {nombre}',
        'pago_al_recibir' => 'Pagar en la sucursal',
        'pago_al_recibir_texto' => 'Efectivo, débito o crédito, después del corte.',
        // Estados de una reserva: hora reservada → confirmada → en atención (en la sucursal o barbero en camino) → atendida.
        'pedido_paso_nuevo' => 'Hora reservada',
        'pedido_codigo' => 'Reserva {codigo}',
        'pedido_cancelado' => 'Reserva cancelada',
        'estado_nuevo' => 'Nueva',
        'estado_preparando' => 'Confirmada',
        'estado_listo' => 'En atención',
        'estado_listo_retiro' => 'En la sucursal',
        'estado_listo_despacho' => 'Barbero en camino',
        'estado_entregado' => 'Atendida',
        'estado_cancelado' => 'Cancelada',
        'panel_avanzar_nuevo' => 'Confirmar hora',
        'panel_avanzar_preparando' => 'Marcar en atención',
        'panel_avanzar_listo' => 'Marcar atendida',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Cliente frecuente de Ñuñoa: dos reservas anteriores para repetir y una hora agendada.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Empresa', 'detalle' => 'Convenio corporativo aprobado: ve precios de empresa, packs de cortes y jornadas en la oficina.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueño', 'detalle' => 'Panel: reservas por estado, servicios, cupos, empresas por aprobar y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que el dueño puede elegir al crear o editar un servicio o producto (no hay subida de archivos).
    'fotos' => [
        '1647140655214-e4a2d914971f' => 'Corte con tijera y peine',
        '1788328155052-e02abe9ba983' => 'Degradado con máquina',
        '1618049049816-43a00d5b0c3d' => 'Corte y barba terminados',
        '1693755807658-17ce5331aacb' => 'Arreglo de barba',
        '1653875700318-c235ce6eb128' => 'Afeitado con navaja',
        '1653875700315-9d7d5a3cfa5e' => 'Toalla caliente',
        '1607276252614-e874c044e09b' => 'Cejas y perfil',
        '1668621819576-e5a92bcd7e15' => 'Barba con canas',
        '1792735207211-a96ca7e6ea77' => 'Sucursal Ñuñoa',
        '1576168056582-0a851a87ab8e' => 'Sucursal Providencia',
        '1536520002442-39764a41e987' => 'Sucursal Maipú',
        '1779437141537-62577b216307' => 'Pomada en frasco',
        '1781344116880-029945a42b75' => 'Aceite con gotario',
        '1574670700790-fa314ab37787' => 'Botella ámbar',
        '1699885725103-2bf0a3c38cbc' => 'Pomos y envases blancos',
        '1793100123514-0396f06a0474' => 'Gotario sobre piedra',
        '1758518731706-be5d5230e5a5' => 'Equipo en la oficina',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Filo Barbería Ñuñoa',
        'categoria' => 'Barbería',
        'comuna' => 'Ñuñoa',
        'horario' => $horario,
        'nota' => 4.8,
        'resenas' => [
            ['Felipe A.', 5, 'Reservé con Benjamín desde el celular en dos minutos. Llegué, me atendieron a la hora y pagué en línea.', 'hace 5 días'],
            ['Javiera C.', 5, 'Contratamos la jornada en la oficina para el equipo: llegaron dos barberos a la hora y atendieron a once personas.', 'hace 3 semanas'],
            ['Nicolás P.', 4, 'Muy buen degradado y el plan mensual sale a cuenta. Los sábados conviene reservar con varios días.', 'hace 1 mes'],
        ],
    ],
];
