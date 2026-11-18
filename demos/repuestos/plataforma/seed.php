<?php
declare(strict_types=1);

/*
 * Datos iniciales de Cardán Distribuidora (se cargan al crear la base y en cada reinicio por hora).
 * Repuestos genéricos, sin marcas de autos ni de fabricantes. La descripción empieza con el código y la
 * compatibilidad genérica ("CD-1101 · City car, hatchback · 1.2–1.6 · 2012–2020 · …" o "CD-2201 · Universal · …"),
 * que la tienda usa para buscar por código, por patente o por vehículo. precio_mayorista = precio taller.
 * Fotos: IDs de Unsplash (ver ../CREDITOS.md). Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 */
return [
    'categorias' => [
        ['slug' => 'frenos', 'nombre' => 'Frenos'],
        ['slug' => 'filtros', 'nombre' => 'Filtros y lubricantes'],
        ['slug' => 'suspension', 'nombre' => 'Suspensión y dirección'],
        ['slug' => 'encendido', 'nombre' => 'Encendido y baterías'],
        ['slug' => 'embrague', 'nombre' => 'Embrague y distribución'],
        ['slug' => 'iluminacion', 'nombre' => 'Iluminación y plumillas'],
        ['slug' => 'talleres', 'nombre' => 'Para talleres'],
    ],

    'productos' => [
        ['slug' => 'pastillas-ciudad', 'categoria' => 'frenos', 'nombre' => 'Pastillas de freno delanteras cerámicas', 'descripcion' => 'CD-1101 · City car, hatchback · 1.2–1.6 · 2012–2020 · Juego de 4 pastillas para el eje delantero, con sensor de desgaste.', 'precio' => 24990, 'precio_mayorista' => 20990, 'stock' => 38, 'foto' => '1613214150384-14921ff659b2', 'destacado' => true],
        ['slug' => 'pastillas-suv', 'categoria' => 'frenos', 'nombre' => 'Pastillas de freno delanteras para SUV', 'descripcion' => 'CD-1102 · SUV, camioneta · 2.0–2.5 · 2015–2023 · Cerámicas, bajo polvo y bajo ruido. Juego de un eje.', 'precio' => 36990, 'precio_mayorista' => 30990, 'stock' => 22, 'foto' => '1760317890314-e964ffd7e6a6'],
        ['slug' => 'discos-280', 'categoria' => 'frenos', 'nombre' => 'Par de discos de freno ventilados 280 mm', 'descripcion' => 'CD-1110 · Sedán, SUV · 1.6–2.0 · 2013–2022 · Espesor 25 mm, mínimo 23 mm. 5 pernos.', 'precio' => 65990, 'precio_mayorista' => 55990, 'stock' => 9, 'foto' => '1792955103949-6aa8a8ef8b66', 'destacado' => true],
        ['slug' => 'discos-256', 'categoria' => 'frenos', 'nombre' => 'Par de discos de freno ventilados 256 mm', 'descripcion' => 'CD-1111 · City car, hatchback · 1.2–1.6 · 2010–2020 · Espesor 22 mm, mínimo 20 mm. 4 pernos.', 'precio' => 52990, 'precio_mayorista' => 44990, 'stock' => 14, 'foto' => '1609682932589-5ef2bd85980d'],
        ['slug' => 'zapatas', 'categoria' => 'frenos', 'nombre' => 'Zapatas de freno traseras', 'descripcion' => 'CD-1120 · City car, hatchback · 1.0–1.4 · 2010–2019 · Juego para tambor de 200 mm.', 'precio' => 21990, 'precio_mayorista' => 17990, 'stock' => 3, 'foto' => '1756365470839-f0cf808990ba'],
        ['slug' => 'dot4', 'categoria' => 'frenos', 'nombre' => 'Líquido de frenos DOT 4, 1 litro', 'descripcion' => 'CD-1130 · Universal · Cámbialo cada dos años o cuando se vea oscuro.', 'precio' => 9990, 'precio_mayorista' => 7990, 'stock' => 60, 'foto' => '1626886548072-55473923af51'],

        ['slug' => 'filtro-aceite', 'categoria' => 'filtros', 'nombre' => 'Filtro de aceite rosca 3/4-16', 'descripcion' => 'CD-2101 · City car, hatchback, sedán · 1.2–1.8 · 2010–2022 · Con válvula antirretorno. Cámbialo en cada cambio de aceite.', 'precio' => 4990, 'precio_mayorista' => 3690, 'stock' => 120, 'foto' => ''],
        ['slug' => 'filtro-aire', 'categoria' => 'filtros', 'nombre' => 'Filtro de aire de panel', 'descripcion' => 'CD-2110 · Hatchback, sedán · 1.4–1.6 · 2012–2021 · Papel plisado de alto flujo.', 'precio' => 8990, 'precio_mayorista' => 6990, 'stock' => 45, 'foto' => '1522598140461-ec9911e01c53'],
        ['slug' => 'filtro-polen', 'categoria' => 'filtros', 'nombre' => 'Filtro de cabina con carbón activado', 'descripcion' => 'CD-2111 · City car, hatchback, sedán, SUV · 1.2–2.0 · 2014–2023 · Retiene polvo, polen y olores.', 'precio' => 7990, 'precio_mayorista' => 5990, 'stock' => 30, 'foto' => ''],
        ['slug' => 'aceite-5w30', 'categoria' => 'filtros', 'nombre' => 'Aceite sintético 5W-30, 4 litros', 'descripcion' => 'CD-2201 · Universal · Para motores bencineros modernos. Revisa en el manual cuántos litros lleva tu motor.', 'precio' => 34990, 'precio_mayorista' => 29990, 'stock' => 48, 'foto' => '1768689033119-c3ac1e437d20', 'destacado' => true],
        ['slug' => 'aceite-10w40', 'categoria' => 'filtros', 'nombre' => 'Aceite semisintético 10W-40, 4 litros', 'descripcion' => 'CD-2202 · Universal · Para motores con más kilometraje.', 'precio' => 24990, 'precio_mayorista' => 20990, 'stock' => 36, 'foto' => '1766933181278-16bf0bb27e13'],
        ['slug' => 'refrigerante', 'categoria' => 'filtros', 'nombre' => 'Refrigerante 50/50 listo para usar, 4 litros', 'descripcion' => 'CD-2301 · Universal · Protege hasta −35 °C y 129 °C.', 'precio' => 9990, 'precio_mayorista' => 7490, 'stock' => 40, 'foto' => ''],

        ['slug' => 'amortiguador-sedan', 'categoria' => 'suspension', 'nombre' => 'Amortiguador delantero a gas', 'descripcion' => 'CD-3101 · Sedán · 1.6–2.0 · 2014–2021 · Precio por unidad; se cambian de a par en el mismo eje.', 'precio' => 39990, 'precio_mayorista' => 33990, 'stock' => 16, 'foto' => '1772308005714-976160554238', 'destacado' => true],
        ['slug' => 'amortiguador-suv', 'categoria' => 'suspension', 'nombre' => 'Amortiguador delantero reforzado', 'descripcion' => 'CD-3102 · SUV, camioneta · 2.0–2.8 · 2015–2023 · Para uso con carga. Precio por unidad.', 'precio' => 54990, 'precio_mayorista' => 46990, 'stock' => 8, 'foto' => '1720796112186-91995737c017'],
        ['slug' => 'bandeja', 'categoria' => 'suspension', 'nombre' => 'Bandeja inferior con rótula', 'descripcion' => 'CD-3201 · Sedán, hatchback · 1.4–1.8 · 2012–2020 · Con bujes instalados. Indica lado en la nota.', 'precio' => 44990, 'precio_mayorista' => 37990, 'stock' => 6, 'foto' => '1769218402167-b0ef15eaf7cc'],
        ['slug' => 'terminal', 'categoria' => 'suspension', 'nombre' => 'Terminal de dirección', 'descripcion' => 'CD-3202 · City car, hatchback, sedán · 1.2–1.8 · 2010–2020 · Rosca M14 × 1,5. Precio por unidad.', 'precio' => 12990, 'precio_mayorista' => 9990, 'stock' => 25, 'foto' => ''],
        ['slug' => 'bieleta', 'categoria' => 'suspension', 'nombre' => 'Bieleta de barra estabilizadora', 'descripcion' => 'CD-3203 · Sedán, SUV · 1.6–2.0 · 2014–2022 · Largo 285 mm. Precio por unidad.', 'precio' => 9990, 'precio_mayorista' => 7990, 'stock' => 30, 'foto' => '1760317890283-540a468b86f6'],

        ['slug' => 'bujias-iridio', 'categoria' => 'encendido', 'nombre' => 'Juego de 4 bujías de iridio', 'descripcion' => 'CD-4101 · City car, hatchback, sedán, SUV · 1.4–2.0 · 2012–2023 · Hasta 60.000 km. Calibradas de fábrica.', 'precio' => 27990, 'precio_mayorista' => 23490, 'stock' => 40, 'foto' => '1782885044249-863562f7e93e'],
        ['slug' => 'bujias-niquel', 'categoria' => 'encendido', 'nombre' => 'Juego de 4 bujías de níquel', 'descripcion' => 'CD-4102 · City car, hatchback · 1.0–1.6 · 2008–2018 · Para cambio cada 20.000 km.', 'precio' => 11990, 'precio_mayorista' => 9490, 'stock' => 50, 'foto' => '1782885044133-27c4df887d96'],
        ['slug' => 'bateria-60', 'categoria' => 'encendido', 'nombre' => 'Batería 12 V 60 Ah', 'descripcion' => 'CD-4201 · Sedán, SUV · 1.6–2.4 · 2010–2023 · Borne positivo a la derecha. Recibimos tu batería usada.', 'precio' => 79990, 'precio_mayorista' => 69990, 'stock' => 12, 'foto' => '1742899273038-67ff67477663', 'destacado' => true],
        ['slug' => 'bateria-45', 'categoria' => 'encendido', 'nombre' => 'Batería 12 V 45 Ah', 'descripcion' => 'CD-4202 · City car, hatchback · 1.0–1.6 · 2008–2022 · Libre de mantención.', 'precio' => 64990, 'precio_mayorista' => 56990, 'stock' => 10, 'foto' => ''],
        ['slug' => 'bateria-90', 'categoria' => 'encendido', 'nombre' => 'Batería 12 V 90 Ah para diésel', 'descripcion' => 'CD-4203 · Camioneta, furgón · 2.2–3.0 · 2010–2023 · Alta corriente de arranque en frío.', 'precio' => 129990, 'precio_mayorista' => 114990, 'stock' => 4, 'foto' => ''],

        ['slug' => 'embrague-city', 'categoria' => 'embrague', 'nombre' => 'Kit de embrague 3 piezas', 'descripcion' => 'CD-5101 · City car, hatchback · 1.2–1.4 · 2012–2020 · Disco, prensa y rodamiento.', 'precio' => 119990, 'precio_mayorista' => 104990, 'stock' => 5, 'foto' => '1603925246538-193f96ec9acd', 'destacado' => true],
        ['slug' => 'embrague-suv', 'categoria' => 'embrague', 'nombre' => 'Kit de embrague 3 piezas reforzado', 'descripcion' => 'CD-5102 · SUV, camioneta · 2.0–2.5 · 2015–2023 · Disco, prensa y rodamiento para carga.', 'precio' => 189990, 'precio_mayorista' => 169990, 'stock' => 2, 'foto' => ''],
        ['slug' => 'distribucion', 'categoria' => 'embrague', 'nombre' => 'Kit de distribución', 'descripcion' => 'CD-5201 · Sedán, hatchback · 1.6–2.0 · 2012–2021 · Correa, tensor y polea loca. Cambio cada 60.000 km.', 'precio' => 79990, 'precio_mayorista' => 68990, 'stock' => 7, 'foto' => '1786028924283-cd732c036531'],
        ['slug' => 'correa-accesorios', 'categoria' => 'embrague', 'nombre' => 'Correa de accesorios 6 canales', 'descripcion' => 'CD-5202 · City car, hatchback, sedán · 1.2–1.8 · 2010–2020 · Para alternador y aire acondicionado.', 'precio' => 14990, 'precio_mayorista' => 11990, 'stock' => 18, 'foto' => '1564144236928-03b5bf87f1b4'],

        ['slug' => 'ampolletas-h4', 'categoria' => 'iluminacion', 'nombre' => 'Par de ampolletas halógenas H4', 'descripcion' => 'CD-6101 · Universal · 12 V, 60/55 W. Luz alta y baja.', 'precio' => 8990, 'precio_mayorista' => 6490, 'stock' => 70, 'foto' => '1777213003360-0419fd2fbfdf'],
        ['slug' => 'plumillas', 'categoria' => 'iluminacion', 'nombre' => 'Plumillas 22" + 18"', 'descripcion' => 'CD-6201 · Universal · Par de plumillas de silicona, con adaptadores.', 'precio' => 12990, 'precio_mayorista' => 9990, 'stock' => 55, 'foto' => '1508786250378-b165238d6e8b'],

        ['slug' => 'caja-filtros', 'categoria' => 'talleres', 'nombre' => 'Caja de 24 filtros de aceite rosca 3/4-16', 'descripcion' => 'CD-9101 · Universal · Para lubricentros y talleres. Precio por caja.', 'precio' => 99990, 'precio_mayorista' => 79990, 'stock' => 15, 'foto' => '1755941707723-838af363a994', 'solo_mayorista' => true],
        ['slug' => 'pack-pastillas', 'categoria' => 'talleres', 'nombre' => 'Pack de 10 juegos de pastillas delanteras', 'descripcion' => 'CD-9102 · City car, hatchback · 1.2–1.6 · 2012–2020 · Cerámicas, con sensor. Precio por pack.', 'precio' => 229990, 'precio_mayorista' => 189990, 'stock' => 6, 'foto' => '1632496498058-a3bb5998a4f6', 'solo_mayorista' => true],
        ['slug' => 'bidon-aceite', 'categoria' => 'talleres', 'nombre' => 'Bidón de aceite sintético 5W-30, 20 litros', 'descripcion' => 'CD-9201 · Universal · Para lubricentros. Con llave de salida.', 'precio' => 159990, 'precio_mayorista' => 134990, 'stock' => 8, 'foto' => '1759419281480-bacc913c9606', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Diego Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Av. Ejemplo 1234', 'Maipú', 'Portón negro'], ['Trabajo', 'Calle Ficticia 77', 'Estación Central', '']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Rodrigo Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Taller Mecánico El Cruce (ficticio)',
            'direcciones' => [['Taller', 'Pasaje Inventado 450', 'Cerrillos', 'Portón 2, al fondo']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueño de Cardán', 'rol' => 'dueno'],
        // Solicitud de taller por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Sofía Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Lubricentro La Vuelta (ficticio)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Pedidos de ejemplo: hace_min = minutos antes del reinicio; fecha_entrega = días desde hoy.
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 4320, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Maipú', 'direccion' => 'Av. Ejemplo 1234 (Portón negro)', 'pago' => 'prueba', 'items' => [['aceite-5w30', 1], ['filtro-aceite', 1], ['filtro-aire', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 20160, 'estado' => 'entregado', 'entrega' => 'retiro', 'pago' => 'al_recibir', 'items' => [['plumillas', 1], ['ampolletas-h4', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 2880, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Cerrillos', 'direccion' => 'Pasaje Inventado 450 (Portón 2)', 'pago' => 'al_recibir', 'items' => [['pastillas-ciudad', 4], ['dot4', 2]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 50, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Cerrillos', 'direccion' => 'Pasaje Inventado 450 (Portón 2)', 'cuando' => '10:00', 'fecha_entrega' => 1, 'pago' => 'al_recibir', 'nota' => 'Patente BB·CD·12, sedán 1.6 2016. Recibe Rodrigo.', 'items' => [['amortiguador-sedan', 2], ['bieleta', 2]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 25, 'estado' => 'preparando', 'entrega' => 'retiro', 'cuando' => '', 'fecha_entrega' => 1, 'pago' => 'prueba', 'items' => [['bateria-60', 1]]],
    ],
];
