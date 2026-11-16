<?php
putenv('PORTFOLIO_SITE_FILE=' . __DIR__ . '/fixtures/site.json');
require_once __DIR__ . '/../includes/data.php';

t('guardar desde panel viejo conserva campos nuevos', function () {
    $site = portfolio_load_site();
    $post = [
        'profile' => ['name' => 'Julio Acuña'],
        'links' => ['email' => 'x@y.cl'],
        'skills' => 'PHP, CSS',
        'projects' => [[
            'id' => 'restaurante-a', 'title' => 'Nuevo título', 'category' => 'X', 'description' => 'd',
            'technologies' => 'PHP', 'image_url' => '', 'demo_url' => '', 'repo_url' => '', 'published' => '1',
        ]],
    ];
    $r = portfolio_apply_admin_post($site, $post);
    eq($r['projects'][0]['title'], 'Nuevo título');
    eq($r['projects'][0]['destacado'], true);
    eq($r['projects'][0]['en_restaurantes'], true);
    eq(count($r['services']), count($site['services']));
    eq($r['profile']['whatsapp_mensaje'], $site['profile']['whatsapp_mensaje']);
    eq($r['links']['email'], 'x@y.cl');
    eq($r['links']['whatsapp'], '+56 9 7353 7642');
    eq($r['skills'], ['PHP', 'CSS']);
});

t('casilla published desmarcada oculta el proyecto', function () {
    $post = ['projects' => [['id' => 'gestion-b', 'title' => 'B']]];
    eq(portfolio_apply_admin_post(portfolio_load_site(), $post)['projects'][0]['published'], false);
});

t('proyecto nuevo sin id recibe slug y defaults', function () {
    $post = ['projects' => [['title' => 'Mi Proyecto Nuevo', 'technologies' => 'PHP, JS', 'published' => '1']]];
    $p = portfolio_apply_admin_post(portfolio_load_site(), $post)['projects'][0];
    eq($p['id'], 'mi-proyecto-nuevo');
    eq($p['technologies'], ['PHP', 'JS']);
    eq($p['orden'], 999);
});

t('proyecto sin titulo se descarta', function () {
    $post = ['projects' => [['id' => 'gestion-b', 'title' => '  ']]];
    eq(portfolio_apply_admin_post(portfolio_load_site(), $post)['projects'], []);
});
