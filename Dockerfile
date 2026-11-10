FROM php:8.2-apache

# Instalar extensiones necesarias para PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite de Apache para URLs amigables
RUN a2enmod rewrite

# Copiar todos los archivos del proyecto al directorio web de Apache
COPY . /var/www/html/

# Dar permisos correctos a la carpeta
RUN chown -R www-data:www-data /var/www/html