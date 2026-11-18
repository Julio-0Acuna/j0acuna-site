<?php
declare(strict_types=1);

// Datos del rubro comida variada (comida casera, chilena y rápida), compartidos por sus 3 versiones y por /demos/variada/.
return [
    'rubro' => 'variada',
    'rubro_nombre' => 'comida variada',
    'rubro_plural' => 'restaurantes de comida casera',
    'url_rubro' => '/demos/variada/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/variada/simple/',
            'negocio' => 'Picada Doña Ernestina',
            'comuna' => 'San Miguel',
            'estilo' => 'Picada de barrio con menú del día: greda de Pomaire, amarillo choclo y cartulinas escritas con plumón pegadas en la vitrina.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/variada/multi/',
            'negocio' => 'Doble Palta',
            'comuna' => 'Recoleta',
            'estilo' => 'Sanguchería y fuente de soda moderna: letras condensadas enormes, negro plancha, crema mayo y verde palta, carta numerada y pedido en forma de comanda.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/variada/plataforma/',
            'negocio' => 'Mediodía',
            'comuna' => 'Huechuraba',
            'estilo' => 'Cocina de colaciones para oficinas: azul petróleo y verde lima, serifa editorial, menú de la semana en calendario y pedidos con fecha para empresas.',
            'captura' => 'plataforma',
        ],
    ],
];
