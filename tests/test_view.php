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

t('sin Bootstrap: estilos y JS propios, fuentes Geist', function () {
    ob_start();
    render_partial('head', ['title' => 'T', 'description' => 'D']);
    render_partial('footer', ['site' => portfolio_load_site()]);
    $h = ob_get_clean();
    contiene($h, 'href="/assets/css/styles.css"');
    contiene($h, 'src="/assets/js/main.js"');
    contiene($h, 'family=Geist');
    no_contiene($h, 'bootstrap');
    // Todo script de otro dominio lleva integridad (aquí no hay ninguno).
    ok(!preg_match('#<script[^>]+src="https://(?![^"]*integrity)#', $h), 'script externo sin integridad');
});

t('titulo_grad escapa y pone las últimas palabras en degradado', function () {
    eq(titulo_grad('Páginas web que «venden» & más', 3), 'Páginas web que <span class="grad">«venden» &amp; más</span>');
    eq(titulo_grad('Ideas para vender <b>más</b>', 1), 'Ideas para vender <span class="grad">&lt;b&gt;más&lt;/b&gt;</span>');
    eq(titulo_grad('Hola', 2), '<span class="grad">Hola</span>');
    eq(titulo_grad('Sin degradado', 0), 'Sin degradado');
});

t('rubros: los que tienen _rubro.php enlazan a su página; el resto queda "pronto"', function () {
    $dir = sys_get_temp_dir() . '/rubros-' . bin2hex(random_bytes(4));
    $rubro = static function (string $carpeta, string $nombre) use ($dir): void {
        mkdir($dir . '/' . $carpeta, 0777, true);
        file_put_contents($dir . '/' . $carpeta . '/_rubro.php', '<?php return ' . var_export([
            'rubro_nombre' => $nombre,
            'url_rubro' => '/demos/' . $carpeta . '/',
            'versiones' => ['simple' => ['plan' => 'Simple-page', 'captura' => 'simple'], 'multi' => ['plan' => 'Multi-page', 'captura' => 'multi']],
        ], true) . ';');
    };
    $rubro('pizzeria', 'pizzería');
    $rubro('peruano', 'comida peruana');   // alias de "peruana"
    $rubro('cafeteria', 'cafetería');      // no está en el catálogo: aparece igual
    $r = portfolio_rubros($dir);
    $por = array_column($r, null, 'slug');
    eq($por['pizzeria']['disponible'], true);
    eq($por['pizzeria']['url'], '/demos/pizzeria/');
    eq($por['pizzeria']['versiones'], ['Simple-page', 'Multi-page']);
    eq($por['peruana']['disponible'], true);
    eq($por['peruana']['url'], '/demos/peruano/');
    eq($por['cafeteria']['disponible'], true);
    eq($por['cafeteria']['nombre'], 'Cafetería');
    eq($por['cafeteria']['foto'], '/demos/cafeteria/img/simple.webp');
    eq($por['sushi']['disponible'], false);
    eq($por['sushi']['url'], '');
    // Los disponibles van primero y ningún rubro se repite.
    eq(array_slice(array_column($r, 'disponible'), 0, 3), [true, true, true]);
    eq(count($r), count(array_unique(array_column($r, 'slug'))));
    eq(count($r), count(portfolio_rubros_catalogo()) + 1);
    // Un rubro "pronto" abre WhatsApp con el rubro en el mensaje.
    contiene(rubro_wa(portfolio_load_site(), $por['sushi']), 'https://wa.me/56973537642?text=');
    contiene(rubro_wa(portfolio_load_site(), $por['sushi']), '%28sushi%29');
});

t('nav sin admin y con enlaces absolutos', function () {
    ob_start();
    render_partial('nav', ['site' => portfolio_load_site(), 'actual' => 'servicios']);
    $h = ob_get_clean();
    contiene($h, 'href="/servicios" aria-current="page"');
    contiene($h, 'href="/proyectos"');
    contiene($h, 'href="/restaurantes"');
    contiene($h, 'href="/contacto"');
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

t('catalogo: barberia, veterinaria y repuestos de auto como rubros propios', function () {
    $slugs = array_column(portfolio_rubros_catalogo(), 'slug');
    foreach (['peluqueria', 'barberia', 'veterinaria', 'repuestos'] as $s) {
        ok(in_array($s, $slugs, true), "falta $s");
    }
    $pelu = array_values(array_filter(portfolio_rubros_catalogo(), static fn ($r) => $r['slug'] === 'peluqueria'))[0];
    ok(!in_array('barberia', $pelu['alias'], true), 'barberia no debe ser alias de peluqueria');
});

t('pie: logo JA, redes con ícono y nombre accesible, sin nombre gigante', function () {
    ob_start();
    render_partial('head', ['title' => 'T', 'description' => 'D']);
    render_partial('footer', ['site' => portfolio_load_site()]);
    $h = ob_get_clean();
    contiene($h, '<symbol id="i-logo"');
    contiene($h, 'class="brand__mark"');
    foreach (['WhatsApp' => 'i-wa', 'Correo' => 'i-mail', 'LinkedIn' => 'i-li', 'GitHub' => 'i-gh'] as $nombre => $icono) {
        contiene($h, 'aria-label="' . $nombre . '"');
        contiene($h, '<use href="#' . $icono . '"/>');
        contiene($h, '<symbol id="' . $icono . '"');
    }
    contiene($h, 'href="mailto:');
    no_contiene($h, 'foot__big');
});
