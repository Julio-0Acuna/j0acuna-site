<?php
declare(strict_types=1);

/*
 * Datos iniciales de Vía Rápida (se cargan al crear la base y en cada reinicio por hora).
 * Servicios con precio fijo para auto mediano y "stock" = cupos de la semana; accesorios genéricos,
 * sin marcas. Precios en CLP. Fotos: IDs de Unsplash (ver demos/taller/CREDITOS.md).
 * Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 */
return [
    'categorias' => [
        ['slug' => 'mantencion', 'nombre' => 'Mantención y aceite'],
        ['slug' => 'frenos', 'nombre' => 'Frenos y suspensión'],
        ['slug' => 'neumaticos', 'nombre' => 'Neumáticos y alineación'],
        ['slug' => 'diagnostico', 'nombre' => 'Diagnóstico y electricidad'],
        ['slug' => 'cuidado', 'nombre' => 'Revisión técnica y cuidado'],
        ['slug' => 'accesorios', 'nombre' => 'Accesorios'],
        ['slug' => 'flotas', 'nombre' => 'Para flotas'],
    ],

    'productos' => [
        ['slug' => 'aceite-express', 'categoria' => 'mantencion', 'nombre' => 'Cambio de aceite y filtro', 'descripcion' => 'Aceite sintético 5W-30 hasta 4 litros, filtro nuevo y revisión de niveles. Listo en 40 minutos.', 'precio' => 39990, 'precio_mayorista' => 34990, 'stock' => 24, 'foto' => '1777118947168-b6e806cb80cf', 'destacado' => true],
        ['slug' => 'mantencion-10', 'categoria' => 'mantencion', 'nombre' => 'Mantención 10.000 km', 'descripcion' => 'Aceite y filtro, revisión de 25 puntos, rotación de neumáticos e informe por WhatsApp. 1 hora.', 'precio' => 64990, 'precio_mayorista' => 56990, 'stock' => 18, 'foto' => '1643700973089-baa86a1ab9ee', 'destacado' => true],
        ['slug' => 'mantencion-30', 'categoria' => 'mantencion', 'nombre' => 'Mantención 30.000 km', 'descripcion' => 'Lo de los 10.000 km más filtros de aire y polen, bujías y líquido de frenos. 2 horas y media.', 'precio' => 139990, 'precio_mayorista' => 124990, 'stock' => 10, 'foto' => '1764869427688-3e97480f4b82'],

        ['slug' => 'pastillas', 'categoria' => 'frenos', 'nombre' => 'Pastillas de freno delanteras', 'descripcion' => 'Pastillas nuevas, limpieza de calipers y prueba de frenado. 1 hora.', 'precio' => 49990, 'precio_mayorista' => 43990, 'stock' => 16, 'foto' => '1613214150384-14921ff659b2', 'destacado' => true],
        ['slug' => 'discos', 'categoria' => 'frenos', 'nombre' => 'Discos y pastillas delanteras', 'descripcion' => 'Discos nuevos, pastillas y purga del sistema de frenos. 2 horas.', 'precio' => 129990, 'precio_mayorista' => 114990, 'stock' => 8, 'foto' => '1747330038945-fb39f6e4b73c'],
        ['slug' => 'amortiguadores', 'categoria' => 'frenos', 'nombre' => 'Amortiguadores delanteros (par)', 'descripcion' => 'Par de amortiguadores con instalación y alineación incluida. 3 horas.', 'precio' => 159990, 'precio_mayorista' => 139990, 'stock' => 6, 'foto' => '1760317890359-4e6bb111e501'],
        ['slug' => 'tren-delantero', 'categoria' => 'frenos', 'nombre' => 'Revisión de tren delantero', 'descripcion' => 'Rótulas, terminales, bujes y dirección, con informe y presupuesto. 45 minutos.', 'precio' => 19990, 'precio_mayorista' => 14990, 'stock' => 20, 'foto' => '1599474151975-1f978922fa02'],

        ['slug' => 'alineacion', 'categoria' => 'neumaticos', 'nombre' => 'Alineación y balanceo', 'descripcion' => 'Alineación computarizada y balanceo de las cuatro ruedas. 1 hora.', 'precio' => 29990, 'precio_mayorista' => 24990, 'stock' => 20, 'foto' => '1613214150333-53afb7561e6d', 'destacado' => true],
        ['slug' => 'montaje', 'categoria' => 'neumaticos', 'nombre' => 'Montaje de 4 neumáticos', 'descripcion' => 'Desmontaje, montaje, válvulas nuevas y balanceo. 45 minutos.', 'precio' => 24990, 'precio_mayorista' => 19990, 'stock' => 20, 'foto' => '1764699186616-8f707281e4f3'],
        ['slug' => 'neumatico-185', 'categoria' => 'neumaticos', 'nombre' => 'Neumático 185/65 R15 (unidad)', 'descripcion' => 'Neumático para auto de ciudad, de una marca disponible en bodega. Montaje aparte.', 'precio' => 54990, 'precio_mayorista' => 47990, 'stock' => 32, 'foto' => '1571335746824-742511d49bce'],
        ['slug' => 'pinchazo', 'categoria' => 'neumaticos', 'nombre' => 'Reparación de pinchazo', 'descripcion' => 'Parche por dentro, revisión de la válvula y balanceo de la rueda. 20 minutos.', 'precio' => 7990, 'stock' => 30, 'foto' => '1599082267768-4815b2ea6bd2'],

        ['slug' => 'scanner', 'categoria' => 'diagnostico', 'nombre' => 'Scanner y diagnóstico', 'descripcion' => 'Lectura de fallas en todos los módulos e informe con presupuesto. Se descuenta si reparas aquí.', 'precio' => 19990, 'precio_mayorista' => 14990, 'stock' => 20, 'foto' => '1625047509248-ec889cbff17f', 'destacado' => true],
        ['slug' => 'bateria', 'categoria' => 'diagnostico', 'nombre' => 'Cambio de batería 60 Ah', 'descripcion' => 'Batería nueva, prueba de alternador, instalación y retiro de la batería usada.', 'precio' => 89990, 'precio_mayorista' => 79990, 'stock' => 10, 'foto' => '1597766325363-f5576d851d6a'],
        ['slug' => 'aire', 'categoria' => 'diagnostico', 'nombre' => 'Carga de aire acondicionado', 'descripcion' => 'Revisión de fugas, vacío del sistema y carga de gas por peso. 1 hora.', 'precio' => 39990, 'precio_mayorista' => 34990, 'stock' => 4, 'foto' => '1587121892719-1711ec9cc798'],

        ['slug' => 'revision-tecnica', 'categoria' => 'cuidado', 'nombre' => 'Pre-revisión técnica', 'descripcion' => 'Luces, frenos, gases y neumáticos revisados antes de que vayas a la planta. 30 minutos.', 'precio' => 14990, 'precio_mayorista' => 9990, 'stock' => 3, 'foto' => '1702146713858-8e7d1cc29fe8'],
        ['slug' => 'limpieza-interior', 'categoria' => 'cuidado', 'nombre' => 'Limpieza de interior', 'descripcion' => 'Aspirado, tablero, vidrios y tapiz con vapor. 2 horas.', 'precio' => 34990, 'precio_mayorista' => 29990, 'stock' => 8, 'foto' => '1771491237209-b89bc7290588'],

        ['slug' => 'camara', 'categoria' => 'accesorios', 'nombre' => 'Cámara para auto Full HD', 'descripcion' => 'Cámara para el parabrisas con visión nocturna y tarjeta de 32 GB. Instalación incluida.', 'precio' => 49990, 'precio_mayorista' => 42990, 'stock' => 12, 'foto' => '1765959106936-851735565c12', 'destacado' => true],
        ['slug' => 'plumillas', 'categoria' => 'accesorios', 'nombre' => 'Plumillas limpiaparabrisas (par)', 'descripcion' => 'Goma de silicona, medidas 22" y 18". Te las instalamos gratis en el mesón.', 'precio' => 14990, 'precio_mayorista' => 11990, 'stock' => 40, 'foto' => '1508786250378-b165238d6e8b'],
        ['slug' => 'extintor', 'categoria' => 'accesorios', 'nombre' => 'Extintor 1 kg con soporte', 'descripcion' => 'Extintor de polvo químico seco certificado, con soporte para fijarlo en el auto.', 'precio' => 17990, 'precio_mayorista' => 14990, 'stock' => 25, 'foto' => '1625958936686-a9343dc35b5b'],
        ['slug' => 'cables', 'categoria' => 'accesorios', 'nombre' => 'Cables para batería 400 A', 'descripcion' => 'Cables de 3 metros con pinzas aisladas y bolso para guardarlos.', 'precio' => 19990, 'precio_mayorista' => 16990, 'stock' => 18, 'foto' => '1597766370173-a14200ec0b42'],
        ['slug' => 'bujias', 'categoria' => 'accesorios', 'nombre' => 'Bujías de iridio (juego de 4)', 'descripcion' => 'Para motores bencineros de 4 cilindros. Instalación aparte.', 'precio' => 39990, 'precio_mayorista' => 33990, 'stock' => 14, 'foto' => '1782885044133-27c4df887d96'],
        ['slug' => 'soporte', 'categoria' => 'accesorios', 'nombre' => 'Soporte de celular para rejilla', 'descripcion' => 'Soporte magnético con giro de 360°, para teléfonos de hasta 7 pulgadas.', 'precio' => 9990, 'stock' => 35, 'foto' => '1764347923709-fc48487f2486'],
        ['slug' => 'pano', 'categoria' => 'accesorios', 'nombre' => 'Paños de microfibra (pack de 3)', 'descripcion' => 'Paños de 40 × 40 cm que no rayan la pintura ni dejan pelusa.', 'precio' => 6990, 'precio_mayorista' => 5490, 'stock' => 60, 'foto' => '1761934658331-2e00b20dc6c6'],

        ['slug' => 'plan-flota', 'categoria' => 'flotas', 'nombre' => 'Mantención de furgón 10.000 km', 'descripcion' => 'Para furgones y camionetas de trabajo: aceite, filtros, revisión de 25 puntos e informe por vehículo.', 'precio' => 79990, 'precio_mayorista' => 69990, 'stock' => 30, 'foto' => '1775865937358-c7d2972e78ec', 'solo_mayorista' => true],
        ['slug' => 'pre-ruta', 'categoria' => 'flotas', 'nombre' => 'Revisión antes de ruta', 'descripcion' => 'Frenos, neumáticos, luces y niveles en 30 minutos por vehículo, con informe para la empresa.', 'precio' => 24990, 'precio_mayorista' => 18990, 'stock' => 40, 'foto' => '1702146713858-8e7d1cc29fe8', 'solo_mayorista' => true],
        ['slug' => 'frenos-furgon', 'categoria' => 'flotas', 'nombre' => 'Frenos de furgón (eje delantero)', 'descripcion' => 'Pastillas y discos para furgones de hasta 3,5 toneladas, con prueba de frenado.', 'precio' => 169990, 'precio_mayorista' => 149990, 'stock' => 10, 'foto' => '1609682932589-5ef2bd85980d', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Camila Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Av. Ejemplo 4321', 'La Florida', 'Casa esquina, portón verde'], ['Oficina', 'Calle Ficticia 55, of. 302', 'Macul', '']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Rodrigo Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Reparto Express Sur (ficticio)',
            'direcciones' => [['Bodega', 'Pasaje Inventado 900', 'San Joaquín', 'Entrada de camiones']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueño de Vía Rápida', 'rol' => 'dueno'],
        // Solicitud de flota por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Sofía Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Transportes La Pampa (ficticio)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Reservas de ejemplo: hace_min = minutos antes del reinicio; fecha_entrega = días desde el reinicio;
    // extras = ficha del auto (los mismos campos que pide el checkout, ver 'checkout_campos' en config.php).
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 64800, 'estado' => 'entregado', 'entrega' => 'retiro', 'fecha_entrega' => -44, 'cuando' => '09:00', 'pago' => 'prueba', 'extras' => [['Patente', 'ABCD12'], ['Vehículo', 'City car'], ['Año', '2018'], ['Kilometraje', '58000']], 'items' => [['mantencion-10', 1], ['plumillas', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 20160, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'La Florida', 'direccion' => 'Av. Ejemplo 4321 (Casa esquina, portón verde)', 'fecha_entrega' => -12, 'cuando' => '10:00', 'pago' => 'al_recibir', 'extras' => [['Patente', 'ABCD12'], ['Vehículo', 'City car'], ['Año', '2018'], ['Kilometraje', '61000']], 'nota' => 'Suena al frenar.', 'items' => [['pastillas', 1], ['alineacion', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 4320, 'estado' => 'entregado', 'entrega' => 'retiro', 'fecha_entrega' => -2, 'cuando' => '08:30', 'pago' => 'al_recibir', 'extras' => [['Patente', 'FGHJ34'], ['Vehículo', 'Furgón de reparto'], ['Año', '2019'], ['Kilometraje', '92000']], 'nota' => 'También el furgón KLMN56, 110.000 km.', 'items' => [['plan-flota', 2], ['pre-ruta', 2]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 90, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'San Joaquín', 'direccion' => 'Pasaje Inventado 900 (Entrada de camiones)', 'fecha_entrega' => 2, 'cuando' => '08:30', 'pago' => 'al_recibir', 'extras' => [['Patente', 'PQRS78'], ['Vehículo', 'Furgón de reparto'], ['Año', '2017'], ['Kilometraje', '134000']], 'nota' => 'Frena desparejo.', 'items' => [['frenos-furgon', 1], ['pre-ruta', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 35, 'estado' => 'preparando', 'entrega' => 'retiro', 'fecha_entrega' => 1, 'cuando' => '09:30', 'pago' => 'prueba', 'extras' => [['Patente', 'ABCD12'], ['Vehículo', 'City car'], ['Año', '2018'], ['Kilometraje', '64000']], 'nota' => 'Luz de motor encendida.', 'items' => [['scanner', 1], ['aceite-express', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 8, 'estado' => 'nuevo', 'entrega' => 'retiro', 'fecha_entrega' => 3, 'cuando' => '11:00', 'pago' => 'prueba', 'extras' => [['Patente', 'ABCD12'], ['Vehículo', 'City car'], ['Año', '2018'], ['Kilometraje', '64200']], 'nota' => 'Instalar la cámara en el parabrisas.', 'items' => [['camara', 1], ['soporte', 1]]],
    ],
];
