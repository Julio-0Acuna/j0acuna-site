<?php
declare(strict_types=1);

/*
 * Datos iniciales de Filo Barbería (se cargan al crear la base y en cada reinicio por hora).
 * Servicios con "stock" = cupos de la semana, planes mensuales, productos genéricos sin marcas y
 * servicios solo para empresas. Precios en CLP. Fotos: IDs de Unsplash (ver demos/barberia/CREDITOS.md).
 * Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 */
return [
    'categorias' => [
        ['slug' => 'cortes', 'nombre' => 'Cortes'],
        ['slug' => 'barba', 'nombre' => 'Barba y afeitado'],
        ['slug' => 'detalles', 'nombre' => 'Cejas y tinte'],
        ['slug' => 'planes', 'nombre' => 'Planes mensuales'],
        ['slug' => 'productos', 'nombre' => 'Productos'],
        ['slug' => 'empresas', 'nombre' => 'Para empresas'],
    ],

    'productos' => [
        ['slug' => 'corte-clasico', 'categoria' => 'cortes', 'nombre' => 'Corte clásico', 'descripcion' => 'Tijera y máquina, lavado y peinado con pomada. 45 minutos.', 'precio' => 13990, 'precio_mayorista' => 11990, 'stock' => 40, 'foto' => '1647140655214-e4a2d914971f', 'destacado' => true, 'modelo3d' => ''],
        ['slug' => 'degradado', 'categoria' => 'cortes', 'nombre' => 'Degradado / fade', 'descripcion' => 'Bajo, medio o alto, con terminación a navaja. 50 minutos.', 'precio' => 15990, 'precio_mayorista' => 13490, 'stock' => 40, 'foto' => '1788328155052-e02abe9ba983', 'destacado' => true],
        ['slug' => 'corte-barba', 'categoria' => 'cortes', 'nombre' => 'Corte + barba', 'descripcion' => 'Corte a elección y barba perfilada con toalla caliente. 75 minutos.', 'precio' => 21990, 'precio_mayorista' => 18990, 'stock' => 30, 'foto' => '1618049049816-43a00d5b0c3d', 'destacado' => true],

        ['slug' => 'perfilado', 'categoria' => 'barba', 'nombre' => 'Perfilado y diseño de barba', 'descripcion' => 'Forma, largo parejo y contorno a navaja, con aceite y bálsamo. 30 minutos.', 'precio' => 10990, 'precio_mayorista' => 9490, 'stock' => 30, 'foto' => '1693755807658-17ce5331aacb', 'destacado' => true],
        ['slug' => 'afeitado', 'categoria' => 'barba', 'nombre' => 'Afeitado con toalla caliente', 'descripcion' => 'Toallas calientes, espuma a brocha, navaja de hoja única y bálsamo. 40 minutos.', 'precio' => 14990, 'precio_mayorista' => 12990, 'stock' => 20, 'foto' => '1653875700318-c235ce6eb128', 'modelo3d' => ''],
        ['slug' => 'ritual', 'categoria' => 'barba', 'nombre' => 'Ritual toalla caliente', 'descripcion' => 'Afeitado completo, limpieza facial y toalla fría para cerrar. 60 minutos.', 'precio' => 19990, 'precio_mayorista' => 16990, 'stock' => 12, 'foto' => '1653875700315-9d7d5a3cfa5e'],

        ['slug' => 'cejas', 'categoria' => 'detalles', 'nombre' => 'Cejas', 'descripcion' => 'Limpieza y forma natural con navaja o máquina chica. 15 minutos.', 'precio' => 4990, 'precio_mayorista' => 3990, 'stock' => 30, 'foto' => '1607276252614-e874c044e09b'],
        ['slug' => 'tinte', 'categoria' => 'detalles', 'nombre' => 'Tinte de barba', 'descripcion' => 'Cubre canas en tono natural. Prueba de alergia 48 horas antes. 30 minutos.', 'precio' => 12990, 'precio_mayorista' => 10990, 'stock' => 4, 'foto' => '1668621819576-e5a92bcd7e15'],

        ['slug' => 'plan-filo', 'categoria' => 'planes', 'nombre' => 'Plan Filo: 2 cortes al mes', 'descripcion' => 'Hora fija cada 15 días en tu sucursal, con el barbero que elijas. Se renueva cada mes.', 'precio' => 24990, 'precio_mayorista' => 21990, 'stock' => 25, 'foto' => '1792735207211-a96ca7e6ea77', 'destacado' => true],
        ['slug' => 'plan-barba', 'categoria' => 'planes', 'nombre' => 'Plan Barba: 4 perfilados al mes', 'descripcion' => 'Una visita a la semana para mantener la barba en forma. 30 minutos cada una.', 'precio' => 34990, 'precio_mayorista' => 29990, 'stock' => 20, 'foto' => '1576168056582-0a851a87ab8e'],
        ['slug' => 'plan-total', 'categoria' => 'planes', 'nombre' => 'Plan Total: 2 cortes y 2 barbas', 'descripcion' => 'Corte y barba dos veces al mes, con prioridad en la agenda y 10 % en productos.', 'precio' => 39990, 'precio_mayorista' => 34990, 'stock' => 20, 'foto' => '1536520002442-39764a41e987'],

        ['slug' => 'pomada', 'categoria' => 'productos', 'nombre' => 'Pomada mate fijación media 100 g', 'descripcion' => 'Base de agua, sin brillo y fácil de lavar. La que usamos en el sillón.', 'precio' => 12990, 'precio_mayorista' => 9990, 'stock' => 30, 'foto' => '1779437141537-62577b216307', 'destacado' => true, 'modelo3d' => 'pomada'],
        ['slug' => 'aceite', 'categoria' => 'productos', 'nombre' => 'Aceite para barba 30 ml', 'descripcion' => 'Suaviza la barba y la piel de abajo. Aroma a cedro y cítricos.', 'precio' => 11990, 'precio_mayorista' => 8990, 'stock' => 30, 'foto' => '1781344116880-029945a42b75', 'modelo3d' => 'aceite-barba-30ml'],
        ['slug' => 'balsamo', 'categoria' => 'productos', 'nombre' => 'Bálsamo after shave 100 ml', 'descripcion' => 'Sin alcohol. Calma la piel después del afeitado o del perfilado.', 'precio' => 10990, 'precio_mayorista' => 8490, 'stock' => 25, 'foto' => '1574670700790-fa314ab37787', 'modelo3d' => 'frasco'],
        ['slug' => 'kit-barba', 'categoria' => 'productos', 'nombre' => 'Kit shampoo y acondicionador para barba', 'descripcion' => 'Dos pomos de 150 ml para lavar y desenredar la barba sin resecarla.', 'precio' => 15990, 'precio_mayorista' => 12490, 'stock' => 18, 'foto' => '1699885725103-2bf0a3c38cbc'],
        ['slug' => 'locion', 'categoria' => 'productos', 'nombre' => 'Loción tónica después del corte 50 ml', 'descripcion' => 'Refresca la nuca y el contorno después de la máquina.', 'precio' => 8990, 'precio_mayorista' => 6990, 'stock' => 22, 'foto' => '1793100123514-0396f06a0474'],

        ['slug' => 'jornada', 'categoria' => 'empresas', 'nombre' => 'Jornada de barbería en tu oficina', 'descripcion' => 'Dos barberos con sus sillones por 4 horas: hasta 12 cortes o arreglos de barba para tu equipo.', 'precio' => 299990, 'precio_mayorista' => 249990, 'stock' => 6, 'foto' => '1758518731706-be5d5230e5a5', 'solo_mayorista' => true],
        ['slug' => 'pack-10', 'categoria' => 'empresas', 'nombre' => 'Pack 10 cortes para colaboradores', 'descripcion' => 'Diez cortes canjeables en cualquier sucursal durante tres meses, con informe de uso.', 'precio' => 139900, 'precio_mayorista' => 114900, 'stock' => 20, 'foto' => '1647140655214-e4a2d914971f', 'solo_mayorista' => true],
        ['slug' => 'giftcards', 'categoria' => 'empresas', 'nombre' => 'Gift cards corporativas (10 × corte + barba)', 'descripcion' => 'Para regalar a clientes o al equipo, con el logo de tu empresa en el sobre. Vencen en seis meses.', 'precio' => 219900, 'precio_mayorista' => 179900, 'stock' => 15, 'foto' => '1618049049816-43a00d5b0c3d', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Diego Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Av. Ejemplo 4321, depto. 52', 'Ñuñoa', 'Conserjería 24 horas'], ['Oficina', 'Calle Ficticia 55, of. 302', 'Providencia', '']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Paula Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Consultora Andes (ficticio)',
            'direcciones' => [['Oficina', 'Pasaje Inventado 900, piso 4', 'Providencia', 'Sala de reuniones grande']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueño de Filo Barbería', 'rol' => 'dueno'],
        // Solicitud de empresa por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Sofía Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Agencia Puerto (ficticio)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Reservas de ejemplo: hace_min = minutos antes del reinicio; fecha_entrega = días desde el reinicio (el motor
    // salta los días cerrados); extras = el campo "Barbero" del checkout, con el texto de su opción en config.php.
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 64800, 'estado' => 'entregado', 'entrega' => 'retiro', 'fecha_entrega' => -44, 'cuando' => '18:00', 'pago' => 'prueba', 'extras' => [['Barbero', 'Benjamín (Ñuñoa)']], 'nota' => 'Degradado bajo, dejar largo arriba.', 'items' => [['degradado', 1], ['pomada', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 20160, 'estado' => 'entregado', 'entrega' => 'retiro', 'fecha_entrega' => -14, 'cuando' => '19:00', 'pago' => 'al_recibir', 'extras' => [['Barbero', 'Benjamín (Ñuñoa)']], 'items' => [['corte-barba', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 4320, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Providencia', 'direccion' => 'Pasaje Inventado 900, piso 4 (Sala de reuniones grande)', 'fecha_entrega' => -2, 'cuando' => '15:00', 'pago' => 'al_recibir', 'extras' => [['Barbero', 'El primero disponible (Providencia)']], 'nota' => 'Jornada para el equipo comercial.', 'items' => [['jornada', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 90, 'estado' => 'nuevo', 'entrega' => 'retiro', 'fecha_entrega' => 3, 'cuando' => '12:00', 'pago' => 'al_recibir', 'extras' => [['Barbero', 'Rodrigo (Providencia)']], 'nota' => 'Primeros dos cortes del pack para gerencia.', 'items' => [['pack-10', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 35, 'estado' => 'preparando', 'entrega' => 'retiro', 'fecha_entrega' => 1, 'cuando' => '18:30', 'pago' => 'prueba', 'extras' => [['Barbero', 'Matías (Ñuñoa)']], 'nota' => 'Afeitado antes de un matrimonio.', 'items' => [['afeitado', 1], ['aceite', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 8, 'estado' => 'nuevo', 'entrega' => 'retiro', 'fecha_entrega' => 2, 'cuando' => '11:00', 'pago' => 'prueba', 'extras' => [['Barbero', 'Cristóbal (Maipú)']], 'items' => [['corte-clasico', 1], ['cejas', 1]]],
    ],
];
