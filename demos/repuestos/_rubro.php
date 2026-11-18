<?php
declare(strict_types=1);

// Datos del rubro tienda de repuestos de auto, compartidos por sus 3 versiones y por /demos/repuestos/.
return [
    'rubro' => 'repuestos',
    'rubro_nombre' => 'repuestos de auto',
    'rubro_plural' => 'tiendas de repuestos',
    'url_rubro' => '/demos/repuestos/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/repuestos/simple/',
            'negocio' => 'Repuestos La Rotonda',
            'comuna' => 'Conchalí',
            'estilo' => 'Repuestos de barrio con atención en el mesón y por WhatsApp: cortina metálica pintada con antióxido, letras de señalética vial, la patente chilena como buscador y etiquetas de caja de repuesto con su código.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/repuestos/multi/',
            'negocio' => 'Calibre, frenos y suspensión',
            'comuna' => 'Santiago',
            'estilo' => 'Tienda especialista en frenos y suspensión con aire de ficha técnica: gris aluminio y rojo pinza de freno, letras anchas, cotas en milímetros y un buscador por vehículo que deja la cotización lista con la patente.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/repuestos/plataforma/',
            'negocio' => 'Cardán Distribuidora',
            'comuna' => 'Cerrillos',
            'estilo' => 'Distribuidora con tienda online, stock a la vista y cuentas para talleres: negro neumático y verde refrigerante, banda de rodado, códigos CD en letra técnica y búsqueda por patente o por vehículo.',
            'captura' => 'plataforma',
        ],
    ],
];
