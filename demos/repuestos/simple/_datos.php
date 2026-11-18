<?php
declare(strict_types=1);

// Repuestos La Rotonda (Conchalí): negocio ficticio de la versión Simple-page.
// Repuestos genéricos, sin marcas de autos ni de fabricantes. Fotos: IDs de Unsplash (ver ../CREDITOS.md).
return [
    'nombre' => 'Repuestos La Rotonda',
    'nombre_corto' => 'La Rotonda',
    'categoria' => 'Tienda de repuestos de auto',
    'comuna' => 'Conchalí',
    'eslogan' => 'Dame la patente y te digo qué le sirve',
    'bajada' => 'Frenos, filtros, embragues, baterías y suspensión para autos, camionetas y furgones. Cotiza por WhatsApp con la patente y retira en el mesón, o te lo llevamos en moto por Conchalí y alrededores.',
    'nota' => 4.8,
    'horario' => [
        1 => ['09:00', '19:00'],
        2 => ['09:00', '19:00'],
        3 => ['09:00', '19:00'],
        4 => ['09:00', '19:00'],
        5 => ['09:00', '19:00'],
        6 => ['09:30', '14:00'],
        7 => null,
    ],
    'fotos' => [
        'hero' => '1702146715471-ae6b10689969',   // muro con repuestos colgados
        'meson' => '1648815546048-6da4f0083a6d',  // el dueño trabajando en el banco
    ],
    // Despacho en moto (lista fija): comuna => costo.
    'comunas_despacho' => [
        'Conchalí' => 2500,
        'Independencia' => 3000,
        'Recoleta' => 3000,
        'Huechuraba' => 3500,
        'Renca' => 3500,
        'Quilicura' => 4000,
    ],
    // Lo más vendido: código interno, compatibilidad genérica (sin marcas ni modelos reales) y precio.
    'destacados' => [
        ['codigo' => 'RP-0101', 'nombre' => 'Pastillas de freno delanteras cerámicas', 'calza' => 'City car y hatchback 1.2–1.6 · 2012–2020', 'detalle' => 'Juego para eje delantero, con sensor de desgaste.', 'precio' => 24990, 'foto' => '1613214150384-14921ff659b2', 'alt' => 'Disco de freno con caliper montado en la maza', 'sello' => 'La más pedida'],
        ['codigo' => 'RP-0230', 'nombre' => 'Aceite sintético 5W-30 4 L', 'calza' => 'Motores bencineros modernos', 'detalle' => 'Bidón de 4 litros. Te decimos cuántos litros lleva tu motor.', 'precio' => 34990, 'foto' => '1768689033119-c3ac1e437d20', 'alt' => 'Aceite de motor dorado cayendo de un bidón', 'sello' => ''],
        ['codigo' => 'RP-0315', 'nombre' => 'Juego de 4 bujías de iridio', 'calza' => 'Motores bencineros 1.4–2.0', 'detalle' => 'Duran hasta 60.000 km. Calibradas de fábrica.', 'precio' => 27990, 'foto' => '1782885044249-863562f7e93e', 'alt' => 'Bujía nueva sobre fondo gris', 'sello' => ''],
        ['codigo' => 'RP-0420', 'nombre' => 'Batería 12 V 60 Ah', 'calza' => 'Sedán y SUV · borne positivo a la derecha', 'detalle' => 'Instalación gratis en el local y retiro de la batería vieja.', 'precio' => 79990, 'foto' => '1742899273038-67ff67477663', 'alt' => 'Bornes rojos y azules de baterías en fila', 'sello' => 'Instalación gratis'],
        ['codigo' => 'RP-0512', 'nombre' => 'Amortiguador delantero a gas', 'calza' => 'Sedán 1.6–2.0 · 2014–2021', 'detalle' => 'Precio por unidad. Se recomienda cambiar el par.', 'precio' => 39990, 'foto' => '1772308005714-976160554238', 'alt' => 'Amortiguador con espiral montado en la suspensión', 'sello' => ''],
        ['codigo' => 'RP-0607', 'nombre' => 'Kit de embrague 3 piezas', 'calza' => 'City car 1.2–1.4 · 2012–2020', 'detalle' => 'Disco, prensa y rodamiento. A pedido en 24 horas si no hay en el local.', 'precio' => 119990, 'foto' => '1603925246538-193f96ec9acd', 'alt' => 'Prensa de embrague roja con números escritos a mano', 'sello' => ''],
        ['codigo' => 'RP-0701', 'nombre' => 'Plumillas 22" + 18"', 'calza' => 'Universal, con adaptadores', 'detalle' => 'Par de plumillas de silicona. Te las ponemos al tiro.', 'precio' => 12990, 'foto' => '1508786250378-b165238d6e8b', 'alt' => 'Plumilla negra sobre un parabrisas mojado', 'sello' => ''],
        ['codigo' => 'RP-0218', 'nombre' => 'Filtro de aire', 'calza' => 'Hatchback y sedán 1.4–1.6', 'detalle' => 'Cámbialo cada 10.000 km o en cada cambio de aceite.', 'precio' => 8990, 'foto' => '1522598140461-ec9911e01c53', 'alt' => 'Filtro de aire rojo plisado en el motor', 'sello' => ''],
    ],
    // Letrero de la cortina: categorías del mesón.
    'categorias' => ['Frenos', 'Filtros', 'Lubricantes', 'Encendido', 'Baterías', 'Suspensión', 'Embrague', 'Correas', 'Eléctrico y focos', 'Refrigeración', 'Plumillas'],
    // Original, alternativo o económico (ejemplo con pastillas delanteras de city car).
    'calidades' => [
        ['titulo' => 'Original', 'subtitulo' => 'La pieza de fábrica', 'texto' => 'La misma que trae el auto desde la concesionaria. La conseguimos a pedido en 24 a 72 horas.', 'precio' => 49990, 'nivel' => 3],
        ['titulo' => 'Alternativo', 'subtitulo' => 'Lo que más vendemos', 'texto' => 'Misma medida y especificación, de fabricantes con años en el mercado. Rinde igual y cuesta casi la mitad.', 'precio' => 24990, 'nivel' => 3],
        ['titulo' => 'Económico', 'subtitulo' => 'Para salir del paso', 'texto' => 'Sirve si vas a vender el auto o lo usas poco. Te decimos con honestidad cuánto dura.', 'precio' => 14990, 'nivel' => 1],
    ],
    'resenas' => [
        ['Marcela T.', 5, 'Mandé la patente por WhatsApp y en diez minutos tenía el precio de las pastillas en dos calidades. Las retiré a la vuelta del trabajo.', 'hace 1 semana'],
        ['Luis A.', 5, 'Me cambiaron la batería ahí mismo, sin cobrar la instalación, y se llevaron la vieja. Precio justo.', 'hace 3 semanas'],
        ['Patricio R.', 4, 'No tenían el kit de embrague, pero lo pidieron y al otro día estaba. Atención de barrio de verdad.', 'hace 2 meses'],
    ],
];
