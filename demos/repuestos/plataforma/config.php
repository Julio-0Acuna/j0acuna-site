<?php
declare(strict_types=1);

// Cardán Distribuidora (Cerrillos): negocio ficticio de la versión Plataforma web.
// Distribuidora de repuestos con tienda online: búsqueda por patente (patentes de ejemplo), por vehículo
// (tipo, año y motor genéricos, sin marcas) o por código; stock a la vista; cuentas de taller asociado
// con precio taller (aprobadas por el dueño) y despacho con día y hora validados en el servidor.
require_once dirname(__DIR__, 2) . '/_comun/comun.php';
require_once dirname(__DIR__) . '/_patente.php';

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
    'url_base' => '/demos/repuestos/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_repuestos_plataforma',
    'prefijo_pedido' => 'CR',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Cardán Distribuidora',
        'nombre_corto' => 'Cardán',
        'eslogan' => 'Repuestos para autos y talleres, con stock a la vista',
        'comuna' => 'Cerrillos',
        'horario' => $horario,
    ],

    // Talleres asociados (mayoristas del motor): precio taller y packs por caja.
    'mayoristas' => true,
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Cerrillos' => 2990,
        'Maipú' => 3990,
        'Estación Central' => 3990,
        'Pedro Aguirre Cerda' => 3990,
        'Lo Espejo' => 3990,
        'Lo Prado' => 4490,
        'Santiago' => 4990,
        'San Miguel' => 4990,
        'Quinta Normal' => 4990,
        'Pudahuel' => 4990,
        'La Cisterna' => 5990,
    ],
    'despacho_gratis_desde' => 60000,
    'pedido_minimo' => 0,
    // Despacho en camioneta o retiro en bodega: desde mañana hasta 10 días, sin domingos ni feriados.
    'entrega' => [
        'modo' => 'fecha',
        'dias_min' => 1,
        'dias_max' => 10,
        'cerrado' => [7, '2026-12-08', '2026-12-25', '2027-01-01'],
        'horas' => ['09:00', '18:00'],
    ],
    // Datos del vehículo en el checkout: con la patente o el VIN revisamos que todo calce antes de despachar.
    // Se validan en el servidor y salen en el pedido, el panel y el WhatsApp ("Patente: BBCD12").
    'checkout_campos' => [
        ['nombre' => 'patente', 'etiqueta' => 'Patente', 'requerido' => false, 'normalizar' => 'patente', 'max' => 6,
            'patron' => '/^([A-Z]{2}\d{4}|[BCDFGHJKLPRSTVWXYZ]{4}\d{2})$/', 'ayuda' => 'Ej.: BBCD12 o AB1234',
            'error' => 'Esa patente no tiene el formato chileno: 4 letras y 2 números (BBCD12) o 2 letras y 4 números (AB1234).'],
        ['nombre' => 'vehiculo', 'etiqueta' => 'Vehículo', 'max' => 40, 'ayuda' => 'Marca y modelo, como aparecen en el padrón'],
        ['nombre' => 'anio', 'etiqueta' => 'Año', 'tipo' => 'numero', 'min' => 1970, 'max' => (int) date('Y') + 1],
        ['nombre' => 'motor', 'etiqueta' => 'Motor', 'max' => 20, 'ayuda' => 'Cilindrada y combustible, ej.: 1.6 bencina'],
        ['nombre' => 'vin', 'etiqueta' => 'VIN', 'requerido' => false, 'normalizar' => 'patente', 'max' => 17,
            'patron' => '/^[A-HJ-NPR-Z0-9]{17}$/', 'ayuda' => '17 caracteres: está en el padrón y al pie del parabrisas',
            'error' => 'El VIN tiene 17 letras y números (sin I, O ni Q).'],
    ],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    // Repuestos: sin menú QR ni modelos 3D.
    'menu_qr' => false,
    'modelos3d' => ['disco-freno-ventilado-280', 'disco-freno-ventilado-256', 'bujias-iridio-juego-4', 'bujias-niquel-juego-4', 'bateria', 'bateria-12v-45ah', 'bateria-12v-90ah-diesel'],

    // Patentes de ejemplo (inventadas) que la demo reconoce. En una tienda real, la búsqueda por patente
    // se conecta a un servicio de consulta de patentes. Perfil genérico: tipo, año y motor, sin marcas.
    'patentes_demo' => [
        'KXPL47' => ['tipo' => 'suv', 'anio' => 2017, 'cc' => '2.0', 'texto' => 'SUV · 2.0 bencina · 2017'],
        'HJTR21' => ['tipo' => 'city', 'anio' => 2015, 'cc' => '1.2', 'texto' => 'City car · 1.2 bencina · 2015'],
        'BBCD12' => ['tipo' => 'sedan', 'anio' => 2016, 'cc' => '1.6', 'texto' => 'Sedán · 1.6 bencina · 2016'],
        'GZWX38' => ['tipo' => 'camioneta', 'anio' => 2019, 'cc' => '2.4', 'texto' => 'Camioneta · 2.4 diésel · 2019'],
        'FKRS55' => ['tipo' => 'furgon', 'anio' => 2018, 'cc' => '2.2', 'texto' => 'Furgón · 2.2 diésel · 2018'],
    ],

    'textos' => [
        'titulo_inicio' => 'Repuestos para autos y talleres en Cerrillos',
        'titulo_destacados' => 'Lo que más sale de bodega',
        'titulo_catalogo' => 'Catálogo',
        'buscar_placeholder' => 'Código o repuesto: CD-1101, pastillas…',
        'sello_destacado' => 'Más vendido',
        'ver_catalogo' => 'Ver catálogo',
        // Talleres asociados (cambian también las claves con género o adjetivo).
        'mayorista' => 'Taller asociado',
        'mayoristas' => 'Talleres asociados',
        'mayorista_titulo' => '¿Tienes un taller?',
        'mayorista_texto' => 'Con una cuenta de taller asociado ves el precio taller en todo el catálogo, packs por caja y despacho en la ruta del día. La aprobamos en un día hábil.',
        'mayorista_quien' => 'Talleres mecánicos, lubricentros y flotas con RUT.',
        'crear_cuenta_mayorista' => 'Crear cuenta de taller',
        'precio_mayorista' => 'Precio taller',
        'solo_mayoristas' => 'Solo talleres asociados',
        'solo_para_mayoristas' => 'Solo para talleres asociados',
        'cuenta_aprobada' => 'Aprobada: ves precio taller',
        'registro_pendiente' => 'Cuenta creada. Tu cuenta de taller queda pendiente hasta que la aprobemos; mientras, ves precios normales.',
        'mayorista_aprobado' => 'Taller aprobado: ya ve el precio taller.',
        'mayoristas_aprobados' => 'Talleres asociados aprobados',
        'mayoristas_esperan' => '{n} taller(es) esperan aprobación',
        // Retiro en bodega o despacho en camioneta, con día y hora.
        'retiro_titulo' => 'Retiro en bodega',
        'retiro_texto' => 'Sin costo. Te avisamos cuando esté listo en el mesón de retiro.',
        'despacho_titulo' => 'Despacho en camioneta',
        'fecha_titulo' => 'Día de despacho o retiro',
        'fecha_ayuda' => 'Despachamos de lunes a sábado en dos rutas, mañana y tarde.',
        'hora_titulo' => 'Desde qué hora',
        'hora_cualquiera' => 'Cualquier hora (09:00 a 18:00)',
        'hora_entrega_fecha' => 'Desde las {hora}',
        'fecha_hora' => '{fecha}, desde las {hora}',
        'pedido_titulo' => '¡Pedido en bodega!',
        'pedido_exito_prueba' => '¡Pago de prueba aprobado! Ya estamos preparando tu pedido en bodega.',
        'pedido_exito_al_recibir' => '¡Pedido recibido! Pagas al recibir, en tu taller, tu casa o en bodega.',
        // Tarjeta de producto propia (vistas/_producto.php) y checkout. El código va solo en su sello ("CD-1101").
        'producto_codigo' => '{codigo}',
        'producto_agregar' => 'Agregar al carro',
        'producto_agregar_aria' => 'Agregar {producto} al carro',
        'producto_agotado' => 'Agotado · consúltanos',
        'checkout_campos_titulo' => 'Tu vehículo',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Particular con su auto: una dirección guardada y pedidos anteriores para repetir.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Taller asociado', 'detalle' => 'Taller aprobado: ve precio taller, packs por caja y su dirección de despacho.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueño', 'detalle' => 'Panel: pedidos por estado, productos y stock, talleres por aprobar y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que el dueño puede elegir al crear o editar un producto (no hay subida de archivos).
    'fotos' => [
        '1613214150384-14921ff659b2' => 'Disco y caliper',
        '1760317890314-e964ffd7e6a6' => 'Disco perforado con caliper',
        '1609682932589-5ef2bd85980d' => 'Disco con caliper gastado',
        '1792955103949-6aa8a8ef8b66' => 'Disco perforado de cerca',
        '1756365470839-f0cf808990ba' => 'Tambores de freno',
        '1772308005714-976160554238' => 'Amortiguador con espiral',
        '1720796112186-91995737c017' => 'Espirales rojos',
        '1760317890283-540a468b86f6' => 'Suspensión neumática',
        '1769218402167-b0ef15eaf7cc' => 'Suspensión y dirección',
        '1522598140461-ec9911e01c53' => 'Filtro de aire',
        '1768689033119-c3ac1e437d20' => 'Aceite de motor',
        '1626886548072-55473923af51' => 'Gotas de aceite',
        '1766933181278-16bf0bb27e13' => 'Bidones de líquido',
        '1782885044249-863562f7e93e' => 'Bujía',
        '1782885044133-27c4df887d96' => 'Bujías',
        '1742899273038-67ff67477663' => 'Baterías',
        '1603925246538-193f96ec9acd' => 'Prensa de embrague',
        '1564144236928-03b5bf87f1b4' => 'Polea y correa',
        '1786028924283-cd732c036531' => 'Cadena de distribución',
        '1777213003360-0419fd2fbfdf' => 'Focos y micas',
        '1508786250378-b165238d6e8b' => 'Plumilla',
        '1755941707723-838af363a994' => 'Piezas sueltas',
        '1632496498058-a3bb5998a4f6' => 'Kit de piezas',
        '1759419281480-bacc913c9606' => 'Repuestos en muro',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Cardán Distribuidora',
        'categoria' => 'Distribuidora de repuestos de auto',
        'comuna' => 'Cerrillos',
        'horario' => $horario,
        'nota' => 4.7,
        'resenas' => [
            ['Mecánica Los Aromos (ficticio)', 5, 'Pedimos en la noche desde el panel del taller y a las 10 está la camioneta. El precio taller y el stock a la vista nos ahorran llamadas.', 'hace 6 días'],
            ['Javiera P.', 5, 'Puse la patente y me mostró solo lo que le sirve a mi auto. Retiré en bodega al otro día, sin fila.', 'hace 3 semanas'],
            ['Héctor S.', 4, 'Buenos precios en baterías y aceite. El despacho del sábado es solo en la mañana, hay que pedir con tiempo.', 'hace 2 meses'],
        ],
    ],
];
