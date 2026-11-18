<?php
declare(strict_types=1);

// Filo Barbería · Plataforma web: cadena con agenda por barbero, tienda y cuentas para empresas,
// sobre el motor común (demos/_motor). El barbero elegido se valida aquí y va en la nota (ver _barberos.php).
require dirname(__DIR__, 2) . '/_motor/motor.php';
require __DIR__ . '/_barberos.php';

$cfg = require __DIR__ . '/config.php';
fb_post_checkout($cfg);
m_ejecutar($cfg);
