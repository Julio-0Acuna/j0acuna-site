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
        eq(array_column(portfolio_featured_projects($s), 'id'), ['ridelsip-cl', 'intranet-agencia', 'saturnus-aura', 'suitechile-cl', 'obrascivilesmys-cl']);
        eq(count(portfolio_restaurant_projects($s)), 3);
        eq(count(portfolio_visible_services($s, 'restaurantes')), 7);
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

t('contenido real: servicios con imagen, tecnologias y ejemplo', function () {
    con_sitio_real(function (array $s) {
        $ids = array_column($s['services'], 'id');
        foreach (['web-menu', 'pedidos-directos', 'apps-delivery', 'pedidos-una-pantalla', 'a-medida', 'menu-qr-3d', 'google-perfil', 'tienda-online', 'inteligencia-artificial'] as $id) {
            ok(in_array($id, $ids, true), "falta el servicio $id");
        }
        foreach (portfolio_visible_services($s, 'todos') as $srv) {
            ok($srv['imagen'] !== '', $srv['id'] . ' sin imagen');
            $ruta = __DIR__ . '/../' . $srv['imagen'];
            ok(is_file($ruta), $srv['id'] . ' imagen no existe');
            ok(filesize($ruta) <= 200 * 1024, $srv['id'] . ' imagen pesada');
            ok(count($srv['tecnologias']) >= 2, $srv['id'] . ' sin tecnologias');
            ok($srv['ejemplo_url'] !== '' && $srv['ejemplo_texto'] !== '', $srv['id'] . ' sin ejemplo');
            ok($srv['para_quien'] !== '', $srv['id'] . ' sin para quien');
            ok(count($srv['puntos']) >= 3, $srv['id'] . ' con pocos puntos');
            ok(mb_strlen($srv['nombre_corto']) <= 24, $srv['id'] . ' nombre corto largo');
            foreach (['integración directa', 'Webpay', 'Mercado Pago'] as $prohibida) {
                ok(stripos(json_encode($srv, JSON_UNESCAPED_UNICODE), $prohibida) === false, $srv['id'] . " contiene $prohibida");
            }
        }
    });
});

t('contenido real: ejemplos de servicios apuntan a paginas que existen', function () {
    con_sitio_real(function (array $s) {
        foreach (portfolio_visible_services($s, 'todos') as $srv) {
            $url = parse_url($srv['ejemplo_url'], PHP_URL_PATH);
            if (preg_match('#^/proyecto/([a-z0-9-]+)$#', $url, $m)) {
                ok(portfolio_find_project($s, $m[1]) !== null, $srv['id'] . ' enlaza a un proyecto oculto');
            } elseif (preg_match('#^/proyectos/([a-z0-9-]+)$#', $url, $m)) {
                ok(portfolio_find_section($s, $m[1]) !== null, $srv['id'] . ' enlaza a una seccion vacia');
            } else {
                $archivo = __DIR__ . '/..' . $url;
                ok(is_file($archivo) || is_file(rtrim($archivo, '/') . '/index.php'), $srv['id'] . ' enlaza a ' . $url . ' que no existe');
            }
        }
    });
});

t('contenido real: FAQ del dominio dice que el hosting es de Julio', function () {
    con_sitio_real(function (array $s) {
        $faq = array_values(array_filter($s['faqs'], static fn ($f) => $f['pregunta'] === '¿El dominio queda a mi nombre?'))[0] ?? null;
        ok($faq !== null, 'falta la pregunta del dominio');
        contiene($faq['respuesta'], 'El dominio .cl se registra a tu nombre');
        contiene($faq['respuesta'], 'mi hosting');
        foreach ($s['faqs'] as $f) {
            no_contiene($f['pregunta'] . ' ' . $f['respuesta'], 'hosting a tu nombre');
        }
    });
});

t('miniaturas de mosaicos pesan poco', function () {
    $minis = glob(__DIR__ . '/../assets/img/mini/{projects,demos/pizzeria/img}/*.webp', GLOB_BRACE) ?: [];
    ok(count($minis) >= 20, 'faltan miniaturas');
    foreach ($minis as $m) {
        ok(filesize($m) <= 40 * 1024, basename($m) . ' miniatura pesada');
        eq(getimagesize($m)[0], 600, basename($m) . ' ancho');
    }
});
