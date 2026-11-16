<?php
putenv('PORTFOLIO_SITE_FILE=' . __DIR__ . '/fixtures/site.json');
require_once __DIR__ . '/../includes/view.php';

t('e escapa comillas y &', function () {
    eq(e('«Don Ramón» & "Cía"'), '«Don Ramón» &amp; &quot;Cía&quot;');
    eq(e(null), '');
});

t('wa_link limpia y codifica', function () {
    eq(
        wa_link('+56 9 7353 7642', 'Hola, quiero cotizar: Página web & menú'),
        'https://wa.me/56973537642?text=Hola%2C%20quiero%20cotizar%3A%20P%C3%A1gina%20web%20%26%20men%C3%BA'
    );
});

t('absolute_url', function () {
    eq(absolute_url('/restaurantes'), SITE_URL . '/restaurantes');
    eq(absolute_url('https://x.cl/a'), 'https://x.cl/a');
});

t('asset es absoluto desde raiz', function () {
    eq(asset('assets/css/styles.css'), '/assets/css/styles.css');
    eq(asset('/assets/js/main.js'), '/assets/js/main.js');
});

t('project_url y category_name', function () {
    eq(project_url(['id' => 'ridelsip-cl']), '/proyecto/ridelsip-cl');
    eq(category_name('gestion'), 'Comercio y gestión');
});

t('head emite og absolutos', function () {
    ob_start();
    render_partial('head', ['title' => 'T', 'description' => 'D', 'og_image' => 'assets/img/og/og-inicio.jpg', 'canonical' => '/']);
    $h = ob_get_clean();
    contiene($h, 'property="og:image" content="' . SITE_URL . '/assets/img/og/og-inicio.jpg"');
    contiene($h, 'property="og:url" content="' . SITE_URL . '/"');
    contiene($h, 'name="twitter:card" content="summary_large_image"');
    no_contiene($h, 'href="assets/');
});

t('bootstrap por CDN con integridad', function () {
    ob_start();
    render_partial('head', ['title' => 'T', 'description' => 'D']);
    render_partial('footer', ['site' => portfolio_load_site()]);
    $h = ob_get_clean();
    contiene($h, 'integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"');
    contiene($h, 'integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"');
});

t('nav sin admin y con enlaces absolutos', function () {
    ob_start();
    render_partial('nav', ['site' => portfolio_load_site()]);
    $h = ob_get_clean();
    contiene($h, 'href="/#servicios"');
    contiene($h, 'href="/restaurantes"');
    no_contiene($h, 'admin');
});

t('tarjeta de servicio cotiza por WhatsApp y oculta precio', function () {
    $site = portfolio_load_site();
    $srv = portfolio_visible_services($site, 'empresas')[0];
    $srv['precio_desde'] = '$150.000';
    ob_start();
    render_partial('service-card', ['service' => $srv, 'site' => $site]);
    $h = ob_get_clean();
    contiene($h, 'https://wa.me/56973537642?text=Hola%20Julio%2C%20quiero%20cotizar%3A%20');
    no_contiene($h, '$150.000');
});

t('tarjeta de proyecto escapa titulo y enlaza al detalle', function () {
    $p = portfolio_find_project(portfolio_load_site(), 'restaurante-a');
    ob_start();
    render_partial('project-card', ['project' => $p]);
    $h = ob_get_clean();
    contiene($h, 'Carnicería «Don Ramón» &amp; Cía');
    contiene($h, 'href="/proyecto/restaurante-a"');
    contiene($h, 'data-categoria="restaurantes"');
});
