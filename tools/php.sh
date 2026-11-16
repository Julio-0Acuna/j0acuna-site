#!/usr/bin/env bash
# PHP de XAMPP sin su php.ini (apunta a una ruta vieja en Downloads).
exec /c/xampp/php/php.exe -n -d extension_dir=C:/xampp/php/ext -d extension=gd -d extension=mbstring -d extension=fileinfo -d extension=openssl -d display_errors=stderr -d error_reporting=E_ALL "$@"
