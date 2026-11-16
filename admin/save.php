<?php
require __DIR__ . '/../includes/auth.php';
admin_require_login();
admin_verify_csrf();

// Mezcla lo enviado con lo guardado: no borra servicios, categorías ni campos nuevos.
portfolio_save_site(portfolio_apply_admin_post(portfolio_load_site(), $_POST));

header('Location: dashboard.php?saved=1');
exit;
