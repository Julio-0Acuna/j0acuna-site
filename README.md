# Portafolio PHP

Sitio de portafolio personal con PHP, CSS, JavaScript y Bootstrap.

## Uso local

1. Copia esta carpeta a tu servidor PHP local, por ejemplo XAMPP `htdocs`.
2. Abre `http://localhost/tu-carpeta/`.
3. Entra a `http://localhost/tu-carpeta/admin/setup.php` para crear tu acceso inicial.
4. Luego administra el contenido desde `admin/login.php`.

## Seguridad del admin

El panel solicita:

- Email
- Password
- Codigo secreto de segundo acceso

El archivo `data/admin.json` guarda el hash de la password y del codigo secreto. No guarda los valores reales.
