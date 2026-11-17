<?php
declare(strict_types=1);

// Merengue Club · Plataforma web: tienda online sobre el motor común (demos/_motor).
// _encargo.php agrega la fecha de entrega y el mensaje en la torta al checkout (ver ahí).
require dirname(__DIR__, 2) . '/_motor/motor.php';
require_once __DIR__ . '/_encargo.php';

$cfg = require __DIR__ . '/config.php';
mc_preparar_checkout($cfg);
m_ejecutar($cfg);
