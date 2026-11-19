---
titulo: Sistema de pedidos para restaurantes: web, WhatsApp y apps
descripcion: Sistema de pedidos para restaurantes: cómo ordenar lo que llega por tu web, WhatsApp y las apps de delivery, hasta tener todo en una sola pantalla.
palabra_clave: sistema de pedidos para restaurantes
palabras_clave: sistema de pedidos online, software de pedidos para restaurantes, pantalla de cocina, comanda digital, centralizar pedidos de delivery, integrar Uber Eats y PedidosYa, pedidos online para restaurante, pedidos por WhatsApp
tema: Restaurantes y delivery
fecha: 2026-11-18
imagen: demos/china/img/plataforma.webp
imagen_alt: Tienda online de Wokazo, restaurante chino ficticio con delivery nocturno, con carta, ingreso de clientes, carrito y tiempo estimado de entrega
resumen: Cómo ordenar los pedidos que llegan por tu web, por WhatsApp y por las apps de delivery, desde un carrito simple hasta una sola pantalla de cocina con comanda impresa.
borrador: false
---
Un viernes en la noche, en muchos locales, el mesón se ve así: una tablet por cada app de delivery, el celular del negocio sonando con mensajes de WhatsApp y alguien anotando en un cuaderno lo que pidieron por teléfono. Un sistema de pedidos para restaurantes sirve para que todo eso llegue ordenado, sin errores al anotar y sin que se pierda ningún pedido. Hay tres niveles, y no todos los locales necesitan el último.

## Nivel 1: pedidos por WhatsApp con carrito

Es el punto de partida y el que más rápido se nota. El cliente abre tu carta en la web, elige productos, tamaños y agregados, indica si retira o quiere despacho a su comuna, y al confirmar se abre WhatsApp con el pedido completo y el total calculado. A ti te llega listo para pasar a cocina.

No hay comisión por venta porque no hay intermediario. Tampoco hace falta instalar nada en el local: el pedido llega al WhatsApp que ya usas. Puedes probarlo en la [pizzería de varias páginas](/demos/pizzeria/multi/), un ejemplo con negocio ficticio. Cómo cobrar, cómo definir el despacho y cómo configurar WhatsApp Business lo expliqué en [pedidos por WhatsApp para restaurantes](/blog/pedidos-por-whatsapp-sin-comision).

Este nivel calza con locales que reciben una cantidad de pedidos que una persona puede atender desde el celular. Cuando eso deja de ser cierto, toca subir.

## Nivel 2: tienda online con panel del dueño

Acá el pedido ya no termina en un mensaje. Entra a un panel donde lo ves con su estado (nuevo, en preparación, listo, en camino), y el cliente puede seguirlo desde su celular. Se suman cosas que con WhatsApp solo no se pueden hacer:

- Cuentas de clientes con direcciones guardadas y botón para repetir el pedido.
- Pago al recibir o pago en línea con tu proveedor de pagos.
- Pedidos programados para otro día, útiles en colaciones y encargos.
- Precios distintos para empresas o clientes mayoristas.
- Un resumen de ventas del día sin sumar a mano.

El chino ficticio [Wokazo](/demos/china/plataforma/) funciona así: delivery hasta tarde, cuentas de clientes, seguimiento del pedido y un panel para el dueño. [Causa Común](/demos/peruana/plataforma/) agrega pedidos programados por día y colaciones para empresas. Los dos tienen una cuenta de prueba para que entres como cliente o como dueño. El detalle de este nivel está en [servicios](/servicios#tienda-online).

## Nivel 3: todos los pedidos en una sola pantalla

Es para locales con harto delivery que hoy atienden varias tablets a la vez. Los pedidos de las apps y de tu propia web entran a una pantalla de cocina, cada uno marcado con su canal de origen, con aviso sonoro, comanda impresa y un panel de ventas. Se confirman, se preparan y se despachan desde el mismo lugar.

Lo construí en Brasil para dos apps de delivery de allá, iFood y 99Food, conectado a sus integraciones oficiales: la conexión con iFood quedó homologada para recibir pedidos y la de 99Food estaba en homologación. Está descrito en [el proyecto del sistema de pedidos en una pantalla](/proyecto/sistema-pedidos-multi-app).

En Chile hay que ser honesto con un punto: para que los pedidos de Uber Eats, PedidosYa o Rappi entren a un sistema propio, cada app tiene que autorizar esa conexión con sus condiciones. No es algo que alguien pueda prometer antes de revisarlo con cada una. Mientras tanto, los pedidos de tu propia web sí entran a la pantalla desde el primer día, y cada app se sigue atendiendo en su tablet hasta que la conexión esté autorizada. Las opciones de este nivel están en [servicios](/servicios#pedidos-una-pantalla).

## Qué revisar antes de elegir un sistema de pedidos

Sea conmigo o con otra solución, estas preguntas filtran bastante:

1. ¿Cobra comisión por pedido o un monto fijo? Saca la cuenta con tus pedidos de un mes normal.
2. ¿De quién son los datos de tus clientes? Si mañana cambias de sistema, ¿te los puedes llevar?
3. ¿La carta vive en tu dominio o en una dirección del proveedor?
4. ¿Funciona bien en el celular del cliente con datos móviles?
5. ¿Qué pasa si se corta internet en el local? ¿Hay forma de seguir atendiendo?
6. ¿Imprime comandas en la impresora que ya tienes o hay que comprar otra?

## Errores que se repiten

El más común es tener precios distintos en cada canal sin decidirlo: subes un plato en la web y te olvidas de la app, o al revés. Define una regla y mantenla.

Otro es la tablet en silencio. Si la cocina no escucha el aviso, el pedido espera y la nota baja. Un sistema con aviso sonoro y comanda impresa evita justamente eso.

También pasa que el local no pausa los pedidos cuando la cocina se satura. Es mejor cerrar un rato que entregar tarde; las malas calificaciones quedan a la vista del próximo cliente. Y cuidado con los datos de tus clientes: pide solo lo necesario para entregar el pedido.

## Preguntas frecuentes

### ¿Se pueden conectar Uber Eats y PedidosYa a mi propio sistema?

Depende de que cada app autorice la conexión y de sus condiciones. Lo construí con apps de Brasil usando sus integraciones oficiales; en Chile se revisa caso a caso con cada una.

### ¿Un sistema de pedidos online cobra comisión?

Los pedidos que llegan por tu web o por WhatsApp no pagan comisión por venta. Si cobras en línea, el proveedor de pagos cobra su propia comisión por transacción, que conviene revisar en su sitio.

### ¿Necesito una impresora especial para la comanda?

Para imprimir comandas se usa una impresora de cocina. Antes de partir revisamos qué modelo tienes y si sirve.

### ¿Puedo partir solo con WhatsApp y crecer después?

Sí, y es lo que recomiendo en la mayoría de los casos. La carta y el carrito se reutilizan cuando pasas a tienda online.

Si hoy los pedidos te llegan por todos lados, cuéntame cuántos recibes en un día normal y por qué canales. Te digo qué nivel te sirve hoy y cuál puede esperar. Escríbeme desde [contacto](/contacto) o por WhatsApp.
