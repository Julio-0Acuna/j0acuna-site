<?php
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/setup_guard.php';

t('setup bloqueado si ya hay admin', function () {
    eq(admin_setup_allowed(true, '127.0.0.1'), false);
});
t('setup bloqueado desde internet aunque falte admin.json', function () {
    eq(admin_setup_allowed(false, '190.5.10.20'), false);
    eq(admin_setup_allowed(false, ''), false);
});
t('setup permitido solo local y sin admin', function () {
    eq(admin_setup_allowed(false, '127.0.0.1'), true);
    eq(admin_setup_allowed(false, '::1'), true);
});
