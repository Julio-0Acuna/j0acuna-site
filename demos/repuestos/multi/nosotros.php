<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = cb();
$cifras = [
    ['24', 'años en el barrio de los repuestos'],
    ['1.900', 'códigos de frenos y suspensión'],
    ['0,01 mm', 'precisión del micrómetro del mesón'],
    ['40', 'talleres que nos compran cada semana'],
];

cb_abrir('nosotros', 'Nosotros', 'Quiénes somos en ' . $n['nombre_largo'] . ': una tienda especialista en frenos y suspensión en Santiago, con banco de medición y asesoría para particulares y talleres.');
?>
<main id="contenido">
  <?php cb_titular('Nosotros', 'Una tienda que mide antes de vender', 'Calibre nació en 2002 como un mesón de frenos en el barrio de los repuestos de Santiago. Hoy atendemos a particulares y a talleres, siempre con el pie de metro a mano.'); ?>

  <section class="cb-seccion cb-seccion--sin-arriba">
    <div class="cb-ancho cb-nosotros">
      <figure class="cb-nosotros__foto">
        <?= demo_img($n['fotos']['banco'], 'Mecánico revisando la suspensión desde abajo del auto', ['ancho' => 900, 'alto' => 1000, 'anchos' => [480, 900, 1300], 'sizes' => '(max-width: 900px) 92vw, 45vw']) ?>
      </figure>
      <div class="cb-nosotros__texto">
        <h2>El banco de medición</h2>
        <p>Trae el repuesto viejo. Lo ponemos en el banco, lo medimos con pie de metro y micrómetro, y lo comparamos con la ficha del nuevo: diámetro, espesor, pernos, largo del amortiguador. Así no hay vueltas ni devoluciones.</p>
        <p>No somos taller: vendemos repuestos y te decimos la verdad sobre lo que necesitas. Si hace falta instalar, te recomendamos un taller de confianza del barrio y les despachamos directo.</p>
        <dl class="cb-cifras">
          <?php foreach ($cifras as [$cifra, $texto]): ?>
          <div><dt><?= demo_e($cifra) ?></dt><dd><?= demo_e($texto) ?></dd></div>
          <?php endforeach; ?>
        </dl>
      </div>
    </div>
  </section>

  <section class="cb-seccion cb-seccion--blanca" aria-labelledby="talleres-titulo">
    <div class="cb-ancho cb-talleres">
      <div>
        <p class="cb-sobre">Para talleres</p>
        <h2 id="talleres-titulo">Despacho al taller, dos rutas al día</h2>
        <p>Si tienes un taller, mándanos la patente del auto que tienes en la rampa y te despachamos los repuestos en la ruta de la mañana o de la tarde. Precio para talleres conversado por WhatsApp.</p>
        <a class="cb-btn" href="<?= demo_e(cb_wa('Hola, tengo un taller y quiero comprarles repuestos de frenos y suspensión.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> Hablar como taller</a>
      </div>
      <figure class="cb-talleres__foto">
        <?= demo_img($n['fotos']['suspension'], 'Torre de suspensión con resorte neumático y frenos a la vista', ['ancho' => 900, 'alto' => 700, 'anchos' => [480, 900, 1300], 'sizes' => '(max-width: 900px) 92vw, 45vw']) ?>
      </figure>
    </div>
  </section>
</main>
<?php cb_cerrar();
