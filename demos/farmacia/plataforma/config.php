<?php
declare(strict_types=1);

// Farmacia Lucero (Puente Alto): negocio ficticio de la versión Plataforma web.
// Farmacia abierta las 24 horas con tienda online: retiro a cualquier hora y despacho hoy a la hora que elijas
// (de 08:00 a 23:30), pago de prueba o al recibir, y cuentas para empresas en convenio (reposición de botiquines).
// En el carrito solo hay productos de venta directa y de cuidado; los medicamentos con receta se cotizan aparte
// (campo extra del checkout o WhatsApp) y la receta se presenta al recibir. Sin productos en 3D (pedido de Julio).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$todoElDia = ['00:00', '24:00'];
$horario = array_fill(1, 7, $todoElDia);
$qf = 'Andrés Pizarro Leiva';

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/farmacia/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_farmacia_plataforma',
    'prefijo_pedido' => 'LU',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Farmacia Lucero',
        'eslogan' => 'Farmacia abierta las 24 horas, con despacho a domicilio',
        'comuna' => 'Puente Alto',
        'horario' => $horario,
        'qf' => $qf,
    ],

    // Empresas en convenio (colegios, jardines, oficinas, clubes): reposición de botiquines con precio convenio.
    'mayoristas' => true,
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Puente Alto' => 1990,
        'La Florida' => 2990,
        'La Pintana' => 2990,
        'San José de Maipo' => 4990,
        'Pirque' => 3990,
    ],
    'despacho_gratis_desde' => 25000,
    'pedido_minimo' => 6000,
    'pedido_minimo_solo_despacho' => true,
    // Hoy, a la hora que elijas: el local atiende las 24 horas y el despacho sale de 08:00 a 23:30.
    'entrega' => ['modo' => 'hoy', 'horas' => ['08:00', '23:30']],
    // Medicamento con receta para cotizar (no se cobra en el carrito) y orden de compra de las empresas.
    'checkout_campos' => [
        [
            'nombre' => 'receta', 'etiqueta' => 'Medicamento con receta para cotizar', 'requerido' => false, 'max' => 120,
            'placeholder' => 'Nombre y dosis como aparecen en tu receta',
            'ayuda' => 'No se suma al total: te escribimos con el precio. La receta se presenta al recibir o al retirar y la revisa el químico farmacéutico.',
        ],
        ['nombre' => 'orden', 'etiqueta' => 'Orden de compra', 'requerido' => false, 'max' => 20, 'solo_tipo' => 'mayorista', 'placeholder' => 'Ej.: OC-2291', 'ayuda' => 'Sale en la factura de tu empresa.'],
    ],
    // Categoría con medicamentos de venta directa: sus tarjetas llevan el sello y el aviso de leer las instrucciones.
    'categorias_venta_directa' => ['sin-receta'],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    // Farmacia: sin menú QR ni productos en 3D.
    'menu_qr' => false,

    'textos' => [
        'titulo_inicio' => 'Farmacia 24 horas en Puente Alto',
        'titulo_destacados' => 'Lo que más se pide de noche',
        'titulo_catalogo' => 'Toda la farmacia',
        'buscar_placeholder' => 'Paracetamol, protector, termómetro o un código (LU-101)…',
        'sello_destacado' => 'Más pedido',
        'ver_catalogo' => 'Ver la farmacia',
        'inicio_sin_resultados' => 'No encontramos ese producto. Si es un medicamento con receta, cotízalo por WhatsApp o al finalizar la compra.',
        // Empresas en convenio
        'mayorista' => 'Empresa en convenio',
        'mayoristas' => 'Empresas en convenio',
        'mayorista_titulo' => '¿Tu empresa o colegio repone botiquines?',
        'mayorista_texto' => 'Con una cuenta de empresa en convenio ves precios convenio, botiquines completos e insumos por caja. La aprobamos en el día.',
        'mayorista_quien' => 'Empresas, colegios, jardines infantiles y clubes deportivos que reponen botiquines.',
        'crear_cuenta_mayorista' => 'Crear cuenta de empresa',
        'precio_mayorista' => 'Precio convenio',
        'solo_mayoristas' => 'Solo convenio',
        'solo_para_mayoristas' => 'Solo para empresas en convenio',
        'cuenta_aprobada' => 'Convenio activo: ves precios convenio',
        'registro_pendiente' => 'Cuenta creada. Tu convenio queda pendiente hasta que lo aprobemos; mientras, ves los precios normales.',
        'mayorista_aprobado' => 'Empresa aprobada: ya ve sus precios convenio.',
        'mayoristas_aprobados' => 'Empresas en convenio',
        'mayoristas_esperan' => '{n} empresa(s) esperan aprobación del convenio',
        'registro_empresa' => 'Empresa o institución',
        'registro_particular_texto' => 'Para pedir para ti o tu familia.',
        // Entrega
        'retiro_titulo' => 'Retiro en el local',
        'retiro_texto' => 'A cualquier hora, las 24 horas. Lo dejamos listo en 15 minutos.',
        'despacho_titulo' => 'Despacho a domicilio',
        'checkout_costo_comuna' => 'Hoy, de 08:00 a 23:30. Costo según comuna.',
        'cuando_titulo' => '¿Para cuándo?',
        'cuando_pronto' => 'Lo antes posible (despacho en 60 a 90 min)',
        'cuando_pronto_cerrado' => 'Mañana desde las 08:00 (retiro: cuando quieras)',
        'checkout_campos_titulo' => '',
        'checkout_nota_placeholder' => 'Ej.: dejar en conserjería, el timbre no suena',
        'checkout_confirmar' => 'Confirmar pedido',
        'pago_al_recibir_texto' => 'Débito, crédito o efectivo al recibir o al retirar.',
        'pedido_titulo' => '¡Pedido recibido!',
        'pedido_exito_prueba' => '¡Pago de prueba aprobado! Ya estamos preparando tu pedido.',
        'pedido_exito_al_recibir' => '¡Pedido recibido! Pagas al recibir o al retirar.',
        'estado_listo_despacho' => 'En camino',
        'estado_listo_retiro' => 'Listo para retirar',
        'carrito_despacho_nota_gratis' => 'El despacho se calcula según tu comuna en el siguiente paso (gratis desde {gratis}).',
        'carrito_falta_minimo_despacho' => 'Para despacho, el pedido mínimo es {minimo}: te faltan {falta}. El retiro en el local no tiene mínimo.',
        'carrito_falta_gratis' => 'Te faltan {falta} para el despacho gratis.',
        'resumen_saludo' => 'Hola, hice el pedido {codigo} en {negocio}:',
        'panel_titulo' => 'Panel de la farmacia',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Compra para la casa: dos direcciones guardadas y pedidos anteriores para repetir.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Empresa en convenio', 'detalle' => 'Colegio aprobado: ve precios convenio, botiquines completos e insumos por caja.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueño', 'detalle' => 'Panel: pedidos por estado, productos, stock, empresas por aprobar y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que el dueño puede elegir al crear o editar un producto (no hay subida de archivos).
    'fotos' => [
        '1512069772995-ec65ed45afd6' => 'Comprimidos y cápsulas de colores',
        '1573883430697-4c3479aae6b9' => 'Comprimidos blancos sobre fondo azul',
        '1631549916768-4119b2e5f926' => 'Blísteres de comprimidos',
        '1587854692152-cbe660dbde88' => 'Cápsulas naranjas y blancas',
        '1628771065518-0d82f1938462' => 'Comprimidos y frasco sobre fondo azul',
        '1783935795030-fea312b9d08c' => 'Comprimidos de colores',
        '1707129785947-ddc627a8bab9' => 'Suplementos y cítricos',
        '1624362772755-4d5843e67047' => 'Cápsulas blandas doradas',
        '1565071783280-719b01b29912' => 'Cápsula blanda sobre fondo amarillo',
        '1778036086729-ef4bd0d460c3' => 'Comprimidos en la mano',
        '1505679428249-ebe9559320a0' => 'Guagua tapada con una manta blanca',
        '1537673156864-5d2c72de7824' => 'Masaje a una guagua',
        '1470116945706-e6bf5d5a53ca' => 'Mano de una guagua',
        '1780329927263-b544c4c415fc' => 'Guagua en el baño',
        '1559743344-950d2d9458cc' => 'Botella de loción blanca',
        '1632127579802-ff69469091a4' => 'Crema sobre fondo naranjo',
        '1585945037805-5fd82c2e60b1' => 'Crema sobre fondo beige',
        '1619451427882-6aaaded0cc61' => 'Manos con crema',
        '1785581778868-79100f7fe56c' => 'Frasco de crema y sérum',
        '1607613009820-a29f7bb81c04' => 'Cepillos de dientes en un frasco',
        '1625834319124-345137437603' => 'Cepillos de dientes de colores',
        '1583947582886-f40ec95dd752' => 'Dosificador de jabón o alcohol gel',
        '1609725236589-d987ffc8133a' => 'Termómetro digital',
        '1594790628624-9e563bea851d' => 'Termómetro en la mano',
        '1615486511484-92e172cc4fe0' => 'Tensiómetro digital de brazo',
        '1584707824245-f67bad2c62d5' => 'Mascarilla desechable',
        '1585435557343-3b092031a831' => 'Mascarilla, termómetro y comprimidos',
        '1603398938378-e54eab446dde' => 'Botiquín: pastillero, termómetro y apósitos',
        '1789983361946-1dd2258c7470' => 'Pastillero semanal naranjo',
        '1609840534195-e6385ca0d10a' => 'Apósito adhesivo',
        '1599412227383-b7d4751c8765' => 'Guante de nitrilo azul',
        '1609840112990-4265448268d1' => 'Pañuelos desechables',
        '1773747608435-0fa80d11a32f' => 'Bolsa de agua caliente con funda tejida',
        '1764694187667-f28a05a52c0e' => 'Frasco con gotero',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Farmacia Lucero',
        'categoria' => 'Farmacia 24 horas',
        'comuna' => 'Puente Alto',
        'horario' => $horario,
        'nota' => 4.7,
        'resenas' => [
            ['Javiera M.', 5, 'A las 3 de la mañana necesitaba un termómetro: lo pedí por la página y lo retiré en 10 minutos por la ventanilla. Me atendió el químico farmacéutico.', 'hace 4 días'],
            ['Colegio de ejemplo (empresa)', 5, 'Reponemos los botiquines de las salas con el convenio. Llega todo junto, con la orden de compra en la factura.', 'hace 2 semanas'],
            ['Héctor S.', 4, 'El despacho a La Florida llegó en una hora. La receta de mi señora la cotizaron por WhatsApp y la revisaron al entregar.', 'hace 1 mes'],
        ],
    ],
];
