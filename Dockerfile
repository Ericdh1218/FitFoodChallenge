# Dockerfile
FROM php:8.2-apache

# Habilitar mod_rewrite y extensiones necesarias
RUN a2enmod rewrite \
  && docker-php-ext-install pdo pdo_mysql

# Configurar DocumentRoot en /public
RUN set -eux; \
  sed -ri 's!DocumentRoot /var/www/html!DocumentRoot /var/www/html/public!g' /etc/apache2/sites-available/000-default.conf; \
  printf '%s\n' \
  '<Directory "/var/www/html/public">' \
  '  AllowOverride All' \
  '  Require all granted' \
  '</Directory>' \
  >> /etc/apache2/apache2.conf

# Copiar la app
COPY . /var/www/html

# Permisos (opcional)
RUN chown -R www-data:www-data /var/www/html
