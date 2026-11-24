# j0acuna.site

Código de mi portafolio profesional y de los 54 sitios de ejemplo que muestro a negocios en Chile.
Sitio en línea: **https://j0acuna.site**

Desarrollado por **Julio César Acuña Santos**, Analista Programador (Santiago, Chile).
[LinkedIn](https://www.linkedin.com/in/julio-0acuna) · julio0acuna123@gmail.com

## Qué incluye

- **Portafolio administrable en PHP** (`admin/`, `includes/`): proyectos, blog y datos del sitio se editan desde un panel propio. El acceso pide contraseña y un código secreto, y solo se guardan sus hashes.
- **18 rubros con 3 versiones cada uno** (`demos/`): una página, varias páginas y tienda con cuentas. Rubros: almacén, barbería, botillería, carnicería, comida china, comida peruana, farmacia, ferretería, panadería, pastelería, peluquería, peluquería canina, pizzería, repuestos de auto, restaurante, sushi, taller mecánico y veterinaria.
- **Motor común de tienda** (`demos/_motor/`): catálogo, carrito, checkout, cuentas de cliente y de mayorista, y panel del dueño con pedidos, stock y ventas del día. Cada plataforma usa SQLite y sus datos de prueba se recrean solos cada hora.
- **Menú con modelos 3D**: en iPhone se abre con Quick Look y en Android con un modo cámara propio (cámara del teléfono + giroscopio sobre model-viewer), sin que el cliente tenga que instalar nada.
- **Blog** con artículos para negocios locales, `sitemap.xml`, `robots.txt` y `llms.txt`.
- **Pruebas** en PHP (`tests/`, 44 archivos).

## Tecnologías

PHP 8 (`strict_types`), SQLite con PDO, JavaScript, CSS, Bootstrap 5.3 y model-viewer 4.

## Probarlo en local

1. Copia la carpeta a un servidor PHP local (por ejemplo `htdocs` de XAMPP) o usa `php -S localhost:8000`.
2. Abre `http://localhost:8000/`.
3. Entra a `/admin/setup.php` para crear el acceso inicial y luego administra el contenido desde `/admin/login.php`.

Los usuarios de las tiendas de ejemplo son ficticios y se muestran en cada demo (por ejemplo `cliente@demo.cl` / `cliente123`).

## Modelos 3D

Los modelos 3D (`.glb`) son de terceros (Kenney, Poly Pizza, Sketchfab y otros) y no están en este repositorio para respetar sus licencias. Los créditos están en `demos/CREDITOS.md` y en `demos/creditos.php`. En el sitio publicado sí se pueden ver.
