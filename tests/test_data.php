<?php
putenv('PORTFOLIO_SITE_FILE=' . __DIR__ . '/fixtures/site.json');
require_once __DIR__ . '/../includes/data.php';

t('normaliza proyecto viejo', function () {
    $p = portfolio_find_project(portfolio_load_site(), 'viejo');
    eq($p['categoria_id'], 'formacion');
    eq($p['orden'], 999);
    eq($p['galeria'], []);
    eq($p['destacado'], false);
});

t('oculto no se encuentra ni se lista', function () {
    $s = portfolio_load_site();
    eq(portfolio_find_project($s, 'oculto'), null);
    ok(!in_array('oculto', array_column(portfolio_visible_projects($s), 'id')));
    ok(!in_array('oculto', array_column(portfolio_featured_projects($s), 'id')));
});

t('orden de visibles', function () {
    eq(array_column(portfolio_visible_projects(portfolio_load_site()), 'id')[0], 'restaurante-a');
});

t('categorias en uso omite vacias', function () {
    $ids = array_column(portfolio_categories_in_use(portfolio_load_site()), 'id');
    ok(!in_array('ejemplos', $ids), 'ejemplos solo tiene un oculto');
    ok(in_array('restaurantes', $ids));
});

t('servicios por publico', function () {
    $s = portfolio_load_site();
    eq(count(portfolio_visible_services($s, 'restaurantes')), 2);
    eq(count(portfolio_visible_services($s, 'empresas')), 2);
});

t('restaurantes limita a 3 y solo publicados', function () {
    eq(array_column(portfolio_restaurant_projects(portfolio_load_site()), 'id'), ['restaurante-a']);
});

t('categorias fijas en orden', function () {
    eq(array_column(portfolio_categories(), 'id'), ['restaurantes', 'ejemplos', 'gestion', 'web', 'ia', 'formacion']);
});

t('defaults de servicio y perfil', function () {
    $s = portfolio_load_site();
    $srv = portfolio_visible_services($s, 'empresas')[0];
    eq($srv['mostrar_precio'], false);
    eq($srv['puntos'], []);
    eq($s['profile']['whatsapp_mensaje'], 'Hola Julio, vi tu página y quiero cotizar.');
});
