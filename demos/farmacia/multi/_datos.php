<?php
declare(strict_types=1);

// Nácar, farmacia y dermocosmética (Providencia): negocio ficticio de la versión Multi-page.
// Productos genéricos sin marcas: dermocosmética, bienestar y botiquín de venta directa. Los medicamentos
// con receta no van al carrito: se cotizan en receta.php (ver ../_receta.php). Sin consejos médicos ni promesas
// de resultados: cada ficha describe textura, tamaño y para quién está pensada.
// Fotos: IDs de Unsplash (ver demos/farmacia/CREDITOS.md). Las fichas de producto no usan fotos: llevan una
// muestra de la textura dibujada con CSS (así no aparece ninguna marca).
$semana = ['09:00', '20:30'];

return [
    'nombre' => 'Nácar, farmacia y dermocosmética',
    'nombre_corto' => 'Nácar',
    'categoria' => 'Farmacia y dermocosmética',
    'comuna' => 'Providencia',
    'eslogan' => 'Farmacia y dermocosmética, con calma',
    'nota' => 4.9,
    'horario' => [
        1 => $semana,
        2 => $semana,
        3 => $semana,
        4 => $semana,
        5 => $semana,
        6 => ['10:00', '19:00'],
        7 => ['11:00', '16:00'],
    ],
    'qf' => 'Josefina Ureta Lagos',
    'despacho_minutos' => '2 a 3 horas',
    'minimo_despacho' => 10000,
    'gratis_desde' => 35000,
    'comunas_despacho' => [
        'Providencia' => 2490,
        'Ñuñoa' => 2990,
        'Santiago' => 3490,
        'Las Condes' => 3490,
        'La Reina' => 3990,
        'Vitacura' => 3990,
    ],
    'fotos' => [
        'hero' => '1581182800629-7d90925ad072',
        'texturas' => '1603401712778-ab0575182f12',
        'gotero' => '1608571423902-eed4a5ad8108',
        'atencion' => '1580281657527-47f249e8f4df',
        'receta' => '1563213126-a4273aed2016',
    ],
    'resenas' => [
        ['Antonia V.', 5, 'Me ayudaron a elegir un protector que no me deja la cara blanca y me dieron una muestra para probar. Cero presión por comprar.', 'hace 5 días'],
        ['Matías R.', 5, 'Pedí por la web en la mañana y en la tarde lo tenía en la oficina. La receta de mi papá la cotizaron por WhatsApp en un rato.', 'hace 3 semanas'],
        ['Carolina F.', 4, 'Linda farmacia y muy ordenada. Los fines de semana hay más gente, pero la asesoría vale la espera.', 'hace 2 meses'],
    ],

    // Categorías: slug => [número, nombre, descripción, foto, alt]
    'categorias' => [
        'solar' => ['01', 'Protección solar', 'Protectores para la cara, el cuerpo y los niños, para todos los días del año.', '1632127579802-ff69469091a4', 'Una muestra de crema blanca extendida sobre un fondo naranjo'],
        'limpieza' => ['02', 'Limpieza', 'Agua micelar, geles y leches limpiadoras para la mañana y la noche.', '1608068811588-3a67006b7489', 'Una pincelada de crema cosmética blanca sobre fondo blanco'],
        'hidratacion' => ['03', 'Hidratación y sérums', 'Cremas y sérums de texturas livianas o ricas, según tu piel.', '1748543668687-624e058c367c', 'Gotas de gel transparente y dorado sobre un fondo rosado'],
        'cuerpo' => ['04', 'Cuerpo y manos', 'Cremas corporales, aceites y lo de todos los días.', '1619451427882-6aaaded0cc61', 'Manos aplicándose crema de un frasco con dosificador'],
        'bienestar' => ['05', 'Bienestar', 'Vitaminas y suplementos alimentarios. No reemplazan una alimentación variada.', '1707129785947-ddc627a8bab9', 'Cápsulas y perlas de suplemento junto a rodajas de naranja y limón'],
        'botiquin' => ['06', 'Farmacia sin receta', 'Medicamentos de venta directa e insumos para el botiquín de la casa.', '1631549916768-4119b2e5f926', 'Blísteres de comprimidos y cápsulas de distintos colores sobre una mesa'],
    ],

    // id => cat, codigo, nombre, desc, textura (muestra CSS), datos [etiqueta => valor], variantes [nombre => precio],
    //       medicamento (venta directa: lleva el aviso de leer las instrucciones), destacado
    'productos' => [
        'protector-facial' => ['cat' => 'solar', 'codigo' => 'NC-101', 'nombre' => 'Protector solar facial FPS 50+', 'desc' => 'Fluido de terminación seca, sin perfume, para usar todos los días.', 'textura' => 'fluido', 'datos' => ['Textura' => 'Fluido', 'Para' => 'Rostro'], 'variantes' => ['50 ml' => 18990], 'destacado' => true],
        'protector-color' => ['cat' => 'solar', 'codigo' => 'NC-102', 'nombre' => 'Protector solar con color FPS 50', 'desc' => 'Tono universal para piel clara a media, con terminación natural.', 'textura' => 'base', 'datos' => ['Textura' => 'Crema con color', 'Para' => 'Rostro'], 'variantes' => ['40 ml' => 21990]],
        'protector-corporal' => ['cat' => 'solar', 'codigo' => 'NC-103', 'nombre' => 'Protector solar corporal FPS 50', 'desc' => 'Loción que se absorbe rápido, para el cuerpo y la cara.', 'textura' => 'crema', 'datos' => ['Textura' => 'Loción', 'Para' => 'Cuerpo'], 'variantes' => ['200 ml' => 16990, '400 ml' => 27990]],
        'protector-infantil' => ['cat' => 'solar', 'codigo' => 'NC-104', 'nombre' => 'Protector solar infantil FPS 50+', 'desc' => 'Loción suave y sin perfume, pensada para la piel de los niños.', 'textura' => 'crema', 'datos' => ['Textura' => 'Loción', 'Para' => 'Niños'], 'variantes' => ['150 ml' => 15990]],
        'balsamo-labial' => ['cat' => 'solar', 'codigo' => 'NC-105', 'nombre' => 'Bálsamo labial FPS 30', 'desc' => 'Barra con manteca de karité, para llevar en el bolsillo.', 'textura' => 'balsamo', 'datos' => ['Textura' => 'Bálsamo', 'Tamaño' => '4 g'], 'variantes' => ['Única' => 4490]],

        'agua-micelar' => ['cat' => 'limpieza', 'codigo' => 'NC-201', 'nombre' => 'Agua micelar', 'desc' => 'Desmaquilla y limpia la cara sin necesidad de enjuague.', 'textura' => 'agua', 'datos' => ['Textura' => 'Líquida', 'Para' => 'Todo tipo de piel'], 'variantes' => ['100 ml' => 4990, '400 ml' => 10990], 'destacado' => true],
        'gel-limpiador' => ['cat' => 'limpieza', 'codigo' => 'NC-202', 'nombre' => 'Gel limpiador para piel mixta', 'desc' => 'Hace una espuma suave; se usa en la mañana y en la noche.', 'textura' => 'gel', 'datos' => ['Textura' => 'Gel', 'Para' => 'Piel mixta a grasa'], 'variantes' => ['200 ml' => 12990]],
        'leche-limpiadora' => ['cat' => 'limpieza', 'codigo' => 'NC-203', 'nombre' => 'Leche limpiadora', 'desc' => 'Limpieza cremosa, se retira con algodón o con agua.', 'textura' => 'crema', 'datos' => ['Textura' => 'Leche', 'Para' => 'Piel seca o sensible'], 'variantes' => ['200 ml' => 9990]],
        'espuma-limpiadora' => ['cat' => 'limpieza', 'codigo' => 'NC-204', 'nombre' => 'Espuma limpiadora suave', 'desc' => 'Sale lista del dosificador, sin perfume.', 'textura' => 'espuma', 'datos' => ['Textura' => 'Espuma', 'Para' => 'Todo tipo de piel'], 'variantes' => ['150 ml' => 11490]],

        'serum-niacinamida' => ['cat' => 'hidratacion', 'codigo' => 'NC-301', 'nombre' => 'Sérum de niacinamida 10 %', 'desc' => 'Textura acuosa; se aplica antes de la crema hidratante.', 'textura' => 'gel', 'datos' => ['Textura' => 'Sérum acuoso', 'Tamaño' => '30 ml'], 'variantes' => ['Única' => 19990], 'destacado' => true],
        'serum-hialuronico' => ['cat' => 'hidratacion', 'codigo' => 'NC-302', 'nombre' => 'Sérum de ácido hialurónico', 'desc' => 'Gel ligero que se absorbe en segundos, de mañana o de noche.', 'textura' => 'gel', 'datos' => ['Textura' => 'Gel ligero', 'Tamaño' => '30 ml'], 'variantes' => ['Única' => 18990]],
        'crema-ligera' => ['cat' => 'hidratacion', 'codigo' => 'NC-303', 'nombre' => 'Crema hidratante ligera', 'desc' => 'Gel crema fresco, de terminación mate.', 'textura' => 'fluido', 'datos' => ['Textura' => 'Gel crema', 'Para' => 'Piel mixta a grasa'], 'variantes' => ['50 ml' => 16990], 'destacado' => true],
        'crema-noche' => ['cat' => 'hidratacion', 'codigo' => 'NC-304', 'nombre' => 'Crema nutritiva de noche', 'desc' => 'Textura rica y envolvente para la rutina de la noche.', 'textura' => 'crema', 'datos' => ['Textura' => 'Crema rica', 'Para' => 'Piel seca'], 'variantes' => ['50 ml' => 21990]],
        'contorno' => ['cat' => 'hidratacion', 'codigo' => 'NC-305', 'nombre' => 'Contorno de ojos', 'desc' => 'Crema fresca con punta de metal para aplicar a toquecitos.', 'textura' => 'crema', 'datos' => ['Textura' => 'Crema fresca', 'Tamaño' => '15 ml'], 'variantes' => ['Única' => 15990]],

        'urea' => ['cat' => 'cuerpo', 'codigo' => 'NC-401', 'nombre' => 'Crema corporal con urea 10 %', 'desc' => 'Para codos, talones y zonas muy secas del cuerpo.', 'textura' => 'crema', 'datos' => ['Textura' => 'Crema', 'Tamaño' => '400 ml'], 'variantes' => ['Única' => 13990]],
        'aceite-seco' => ['cat' => 'cuerpo', 'codigo' => 'NC-402', 'nombre' => 'Aceite corporal seco', 'desc' => 'Se absorbe rápido y no deja la piel grasosa.', 'textura' => 'aceite', 'datos' => ['Textura' => 'Aceite seco', 'Tamaño' => '100 ml'], 'variantes' => ['Única' => 12990]],
        'crema-manos' => ['cat' => 'cuerpo', 'codigo' => 'NC-403', 'nombre' => 'Crema de manos', 'desc' => 'Con glicerina, en pomo para llevar en la cartera.', 'textura' => 'crema', 'datos' => ['Textura' => 'Crema', 'Tamaño' => '75 ml'], 'variantes' => ['Única' => 5990]],
        'desodorante' => ['cat' => 'cuerpo', 'codigo' => 'NC-404', 'nombre' => 'Desodorante sin perfume', 'desc' => 'Roll-on sin alcohol, para piel sensible.', 'textura' => 'fluido', 'datos' => ['Formato' => 'Roll-on', 'Tamaño' => '50 ml'], 'variantes' => ['Única' => 5490]],

        'vitamina-d' => ['cat' => 'bienestar', 'codigo' => 'NC-501', 'nombre' => 'Vitamina D3 1.000 UI', 'desc' => 'Suplemento alimentario en cápsulas blandas.', 'textura' => 'capsula', 'datos' => ['Formato' => 'Cápsulas blandas', 'Contenido' => '60'], 'variantes' => ['Única' => 8990]],
        'omega' => ['cat' => 'bienestar', 'codigo' => 'NC-502', 'nombre' => 'Omega 3', 'desc' => 'Suplemento de aceite de pescado en cápsulas blandas.', 'textura' => 'capsula', 'datos' => ['Formato' => 'Cápsulas blandas', 'Contenido' => '60'], 'variantes' => ['Única' => 11990]],
        'magnesio' => ['cat' => 'bienestar', 'codigo' => 'NC-503', 'nombre' => 'Citrato de magnesio', 'desc' => 'Suplemento alimentario en comprimidos.', 'textura' => 'comprimido', 'datos' => ['Formato' => 'Comprimidos', 'Contenido' => '60'], 'variantes' => ['Única' => 9990]],
        'colageno' => ['cat' => 'bienestar', 'codigo' => 'NC-504', 'nombre' => 'Colágeno hidrolizado en polvo', 'desc' => 'Sabor neutro, para disolver en agua, jugo o yogur.', 'textura' => 'polvo', 'datos' => ['Formato' => 'Polvo', 'Tamaño' => '300 g'], 'variantes' => ['Única' => 19990]],

        'paracetamol' => ['cat' => 'botiquin', 'codigo' => 'NC-601', 'nombre' => 'Paracetamol 500 mg', 'desc' => 'Caja de 16 comprimidos.', 'textura' => 'comprimido', 'datos' => ['Formato' => 'Comprimidos', 'Contenido' => '16'], 'variantes' => ['Única' => 1290], 'medicamento' => true],
        'ibuprofeno' => ['cat' => 'botiquin', 'codigo' => 'NC-602', 'nombre' => 'Ibuprofeno 400 mg', 'desc' => 'Caja de 20 comprimidos recubiertos.', 'textura' => 'comprimido', 'datos' => ['Formato' => 'Comprimidos', 'Contenido' => '20'], 'variantes' => ['Única' => 2190], 'medicamento' => true],
        'loratadina' => ['cat' => 'botiquin', 'codigo' => 'NC-603', 'nombre' => 'Loratadina 10 mg', 'desc' => 'Caja de 30 comprimidos.', 'textura' => 'comprimido', 'datos' => ['Formato' => 'Comprimidos', 'Contenido' => '30'], 'variantes' => ['Única' => 3490], 'medicamento' => true],
        'suero' => ['cat' => 'botiquin', 'codigo' => 'NC-604', 'nombre' => 'Suero fisiológico 0,9 %', 'desc' => 'Ampollas de 5 ml para lavado nasal o de heridas.', 'textura' => 'agua', 'datos' => ['Formato' => 'Ampollas de 5 ml', 'Contenido' => '20'], 'variantes' => ['Única' => 3990]],
        'termometro' => ['cat' => 'botiquin', 'codigo' => 'NC-605', 'nombre' => 'Termómetro digital', 'desc' => 'Punta flexible, con pantalla y estuche.', 'textura' => 'instrumento', 'datos' => ['Formato' => 'Digital', 'Pila' => 'Incluida'], 'variantes' => ['Única' => 5990]],
    ],

    'preguntas' => [
        ['¿Puedo comprar medicamentos con receta en la página?', 'No por el carrito. Los cotizas en la página Recetas o en la nota del pedido: te escribimos con el precio y el stock. La receta se presenta al retirar o al recibir, y la revisa nuestra química farmacéutica.'],
        ['¿La asesoría de piel tiene costo?', 'No. Son unos 20 minutos en la farmacia para ver qué texturas te acomodan y armar una rutina simple con lo que ya usas. No es una consulta médica ni dermatológica.'],
        ['¿Cuánto demora el despacho?', 'En Providencia, entre 2 y 3 horas si pides antes de las 17:00. En las otras comunas, el mismo día o el siguiente. Gratis desde $35.000.'],
        ['¿Puedo probar las texturas?', 'Sí: en la farmacia hay probadores de casi todo y, si nos dices tu tipo de piel en el pedido, te incluimos muestras cuando las tenemos.'],
        ['¿Cómo pago?', 'Al retirar o al recibir: débito, crédito, efectivo o transferencia.'],
    ],
];
