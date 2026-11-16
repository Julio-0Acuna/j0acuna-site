<?php
// Revisa el contenido real (data/site.json) contra las reglas del diseño.
require_once __DIR__ . '/../includes/view.php';

function con_sitio_real(callable $fn): void
{
    $antes = getenv('PORTFOLIO_SITE_FILE');
    putenv('PORTFOLIO_SITE_FILE=' . __DIR__ . '/../data/site.json');
    try {
        $fn(portfolio_load_site());
    } finally {
        putenv('PORTFOLIO_SITE_FILE' . ($antes === false ? '' : '=' . $antes));
    }
}

t('contenido real: contacto y ocultos', function () {
    con_sitio_real(function (array $s) {
        eq($s['links']['whatsapp'], '+56 9 7353 7642');
        eq($s['links']['email'], 'Julio0acuna123@gmail.com');
        eq(portfolio_find_project($s, 'alertaoficial'), null);
        eq(portfolio_find_project($s, 'mibusca-brasil'), null);
    });
});

t('contenido real: destacados y pruebas para restaurantes', function () {
    con_sitio_real(function (array $s) {
        eq(count(portfolio_featured_projects($s)), 5);
        eq(count(portfolio_restaurant_projects($s)), 3);
        eq(count(portfolio_visible_services($s, 'restaurantes')), 4);
    });
});

t('contenido real: textos e imagenes de cada proyecto', function () {
    con_sitio_real(function (array $s) {
        foreach (portfolio_visible_projects($s) as $p) {
            ok($p['description'] !== '', $p['id'] . ' sin descripcion');
            if ($p['image_url'] !== '') {
                $ruta = __DIR__ . '/../' . $p['image_url'];
                ok(is_file($ruta), $p['id'] . ' imagen no existe');
                ok(filesize($ruta) <= 200 * 1024, $p['id'] . ' imagen pesada');
            }
            foreach (['scraping', 'robot', 'Keeta', 'CNPJ'] as $prohibida) {
                ok(stripos(json_encode($p, JSON_UNESCAPED_UNICODE), $prohibida) === false, $p['id'] . " contiene $prohibida");
            }
        }
    });
});

t('contenido real: regla MAVT en proyectos sin nombre', function () {
    con_sitio_real(function (array $s) {
        foreach (['intranet-agencia', 'marca-propia-agencia', 'paneles-duenos-restaurantes'] as $id) {
            $p = portfolio_find_project($s, $id);
            ok($p !== null, "$id no existe");
            ok(stripos(json_encode($p, JSON_UNESCAPED_UNICODE), 'MAVT') === false, "$id nombra a MAVT");
            ok(stripos(json_encode($p, JSON_UNESCAPED_UNICODE), 'A1') === false, "$id nombra a A1");
        }
    });
});

t('contenido real: servicio premium con texto honesto', function () {
    con_sitio_real(function (array $s) {
        $premium = array_values(array_filter($s['services'], static fn ($x) => $x['id'] === 'pedidos-una-pantalla'))[0] ?? null;
        ok($premium !== null, 'falta pedidos-una-pantalla');
        contiene($premium['resumen'], 'Ya lo construí en Brasil para iFood y 99Food; en Chile se conecta según lo que permita cada app.');
    });
});

t('imagenes OG existen y pesan poco', function () {
    foreach (['og-inicio.jpg', 'og-restaurantes.jpg'] as $og) {
        $ruta = __DIR__ . '/../assets/img/og/' . $og;
        ok(is_file($ruta), "$og no existe");
        $tam = getimagesize($ruta);
        eq([$tam[0], $tam[1]], [1200, 630], "$og tamaño");
        ok(filesize($ruta) <= 300 * 1024, "$og pesada");
    }
});

t('sitemap sin ocultos', function () {
    $x = (string) @file_get_contents(__DIR__ . '/../sitemap.xml');
    contiene($x, SITE_URL . '/restaurantes');
    contiene($x, SITE_URL . '/proyecto/ridelsip-cl');
    no_contiene($x, 'alertaoficial');
    no_contiene($x, 'mibusca');
});
