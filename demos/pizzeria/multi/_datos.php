<?php
declare(strict_types=1);

// Forno Ottavia (Providencia): negocio ficticio de la versión Multi-page.
// Fotos: IDs de Unsplash (ver demos/CREDITOS.md). Modelos 3D: demos/_comun/3d/<modelo>.glb.
return [
    'nombre' => 'Forno Ottavia',
    'categoria' => 'Pizzería napolitana',
    'comuna' => 'Providencia',
    'eslogan' => 'Pizza napolitana de masa madre, horneada a leña',
    'nota' => 4.8,
    'horario' => [
        1 => ['19:00', '23:00'],
        2 => ['13:00', '23:00'],
        3 => ['13:00', '23:00'],
        4 => ['13:00', '23:00'],
        5 => ['13:00', '00:00'],
        6 => ['13:00', '00:00'],
        7 => ['13:00', '17:00'],
    ],
    'resenas' => [
        ['Francisca L.', 5, 'La Margherita es igual a la que comí en Nápoles: borde inflado, tomate dulce y fior di latte. El tiramisú, imperdible.', 'hace 1 semana'],
        ['Matías P.', 5, 'Muy buena atención y el horno a la vista es un espectáculo. Pedimos la Diavola para llevar y llegó perfecta.', 'hace 3 semanas'],
        ['Daniela V.', 4, 'Rica masa, liviana. Los fines de semana hay que esperar mesa, conviene reservar por WhatsApp.', 'hace 2 meses'],
    ],
    'comunas_despacho' => [
        'Providencia' => 1990,
        'Ñuñoa' => 2490,
        'Las Condes' => 2990,
        'Santiago' => 2990,
        'Vitacura' => 3490,
        'La Reina' => 3490,
    ],
    'categorias' => [
        'pizze' => ['Pizze', 'Masa madre de 48 horas, horneada 90 segundos a 450 °C.'],
        'antipasti' => ['Antipasti', 'Para abrir la mesa.'],
        'dolci' => ['Dolci', 'Hechos en casa cada mañana.'],
        'bebidas' => ['Bebidas', 'Sin alcohol.'],
    ],
    // id => datos. variantes: [nombre => precio]. modelo: nombre del .glb (vacío = sin 3D).
    'platos' => [
        'margherita' => ['cat' => 'pizze', 'nombre' => 'Margherita', 'desc' => 'Tomate San Marzano, fior di latte, albahaca y aceite de oliva.', 'foto' => '1564936281291-294551497d81', 'variantes' => ['Individual 30 cm' => 10900, 'Grande 40 cm' => 15900], 'modelo' => 'pizza', 'firma' => true],
        'bufala' => ['cat' => 'pizze', 'nombre' => 'Bufala', 'desc' => 'Tomate, mozzarella de búfala, albahaca y pimienta negra.', 'foto' => '1595854341625-f33ee10dbf94', 'variantes' => ['Individual 30 cm' => 13900, 'Grande 40 cm' => 19900], 'modelo' => 'pizza', 'firma' => false],
        'diavola' => ['cat' => 'pizze', 'nombre' => 'Diavola', 'desc' => 'Tomate, fior di latte, salame picante y orégano.', 'foto' => '1628840042765-356cda07504e', 'variantes' => ['Individual 30 cm' => 12900, 'Grande 40 cm' => 18400], 'modelo' => 'pizza', 'firma' => true],
        'crudo' => ['cat' => 'pizze', 'nombre' => 'Crudo e rúcula', 'desc' => 'Fior di latte, jamón crudo, rúcula y lascas de parmesano.', 'foto' => '1600628421055-4d30de868b8f', 'variantes' => ['Individual 30 cm' => 14500, 'Grande 40 cm' => 20500], 'modelo' => 'pizza', 'firma' => true],
        'verde' => ['cat' => 'pizze', 'nombre' => 'Verde', 'desc' => 'Pesto de albahaca, fior di latte, rúcula y nueces tostadas.', 'foto' => '1593560708920-61dd98c46a4e', 'variantes' => ['Individual 30 cm' => 12500, 'Grande 40 cm' => 17900], 'modelo' => 'pizza', 'firma' => false],
        'bismarck' => ['cat' => 'pizze', 'nombre' => 'Bismarck', 'desc' => 'Tomate, fior di latte, aceitunas, huevo al horno y orégano.', 'foto' => '1571066811602-716837d681de', 'variantes' => ['Individual 30 cm' => 13200, 'Grande 40 cm' => 18900], 'modelo' => 'pizza', 'firma' => false],
        'caprese' => ['cat' => 'antipasti', 'nombre' => 'Caprese', 'desc' => 'Tomates de estación, mozzarella, albahaca y aceite de oliva.', 'foto' => '1592417817098-8fd3d9eb14a5', 'variantes' => ['Única' => 8900], 'modelo' => 'ensalada', 'firma' => false],
        'focaccia' => ['cat' => 'antipasti', 'nombre' => 'Focaccia al romero', 'desc' => 'Recién salida del horno, con sal de mar y aceite de oliva.', 'foto' => '', 'variantes' => ['Única' => 5500], 'modelo' => '', 'firma' => false],
        'tiramisu' => ['cat' => 'dolci', 'nombre' => 'Tiramisú', 'desc' => 'Mascarpone, café de grano y cacao amargo.', 'foto' => '1571877227200-a0d98ea607e9', 'variantes' => ['Única' => 5900], 'modelo' => 'tiramisu', 'firma' => false],
        'copa' => ['cat' => 'dolci', 'nombre' => 'Copa fior di latte', 'desc' => 'Helado artesanal de leche con crumble de galleta y caramelo.', 'foto' => '1586040140378-b5634cb4c8fc', 'variantes' => ['Única' => 4900], 'modelo' => 'helado', 'firma' => false],
        'limonada' => ['cat' => 'bebidas', 'nombre' => 'Limonada de menta', 'desc' => 'Limón exprimido, menta fresca y un toque de jengibre. 500 cc.', 'foto' => '1599021456807-25db0f974333', 'variantes' => ['Única' => 3900], 'modelo' => '', 'firma' => false],
        'espresso' => ['cat' => 'bebidas', 'nombre' => 'Espresso', 'desc' => 'Café de especialidad, tueste medio.', 'foto' => '1511920170033-f8396924c348', 'variantes' => ['Simple' => 2200, 'Doble' => 2900], 'modelo' => '', 'firma' => false],
        'agua' => ['cat' => 'bebidas', 'nombre' => 'Agua mineral', 'desc' => 'Con o sin gas. 500 cc.', 'foto' => '', 'variantes' => ['Sin gas' => 2300, 'Con gas' => 2300], 'modelo' => '', 'firma' => false],
    ],
    'fotos' => [
        'hero' => '1579751626657-72bc17010498',
        'masa' => '1586444248902-2f64eddc13df',
        'salon' => '1590846406792-0adc7f938f1d',
        'terraza' => '1600093463592-8e36ae95ef56',
    ],
    'preguntas' => [
        ['¿Hacen despacho a domicilio?', 'Sí, en Providencia, Ñuñoa, Las Condes, Santiago, Vitacura y La Reina. Arma tu pedido en la web y se envía por WhatsApp con el costo de despacho de tu comuna.'],
        ['¿Puedo retirar en el local?', 'Claro. Elige «Retiro en el local» y la hora que te acomode. La pizza napolitana se hornea en 90 segundos, así que la tendrás lista a tiempo.'],
        ['¿Tienen opciones vegetarianas o veganas?', 'La Margherita, la Verde y la Caprese son vegetarianas. Para una opción vegana pide una Marinara (tomate, ajo y orégano, sin queso) en el mensaje.'],
        ['¿Tienen masa sin gluten?', 'No. Nuestra masa es de trigo y se trabaja en el mismo mesón, así que no podemos garantizar un plato sin gluten.'],
        ['¿Se puede reservar mesa?', 'Sí, por WhatsApp. Los viernes y sábados en la noche te recomendamos reservar con un día de anticipación.'],
        ['¿Qué es «Ver en 3D»?', 'En el menú puedes girar algunos platos en 3D y, desde el celular, ponerlos sobre tu mesa con la cámara antes de pedir.'],
        ['¿Cómo pago?', 'En el local aceptamos efectivo, débito y crédito. En los despachos, pagas al recibir o por transferencia.'],
    ],
];
