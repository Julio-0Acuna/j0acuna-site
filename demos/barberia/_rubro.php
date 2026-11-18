<?php
declare(strict_types=1);

// Datos del rubro barbería, compartidos por sus 3 versiones y por /demos/barberia/.
return [
    'rubro' => 'barberia',
    'rubro_nombre' => 'barbería',
    'rubro_plural' => 'barberías',
    'url_rubro' => '/demos/barberia/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/barberia/simple/',
            'negocio' => 'Barbería Don Lalo',
            'comuna' => 'Quinta Normal',
            'estilo' => 'Barbería de barrio de toda la vida: azulejo verde agua, sillón rojo, piso de ajedrez, pizarra de precios pintada a mano y letras de letrero de vidriera.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/barberia/multi/',
            'negocio' => 'El Gabinete',
            'comuna' => 'Santiago Centro',
            'estilo' => 'Barbería de caballeros old school con sala de espera: verde botella, bronce y cuero, letras grabadas como placa de bronce, filetes art déco y fotos en penumbra.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/barberia/plataforma/',
            'negocio' => 'Filo Barbería',
            'comuna' => 'Ñuñoa',
            'estilo' => 'Cadena de tres sucursales con agenda por barbero: blanco hueso y tinta, acento cobalto, letras altas condensadas, esquinas rectas y la franja del poste de barbero.',
            'captura' => 'plataforma',
        ],
    ],
];
