FROM php:8.2-apache

# Instalar dependencias de PHP
RUN apt-get update && \
    apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-install pdo pdo_mysql zip

EXPOSE 3306
CMD ["mysqld_safe"]
# Copiar archivos del proyecto Symfony
COPY . /var/www/html/

COPY myapp.conf /etc/apache2/sites-available/
RUN ln -s /etc/apache2/sites-available/myapp.conf /etc/apache2/sites-enabled/
CMD ["apache2-foreground"]

# Habilitar módulo Apache para reescritura de URL
RUN a2enmod rewrite

RUN service apache2 restart


# Establecer permisos adecuados
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 770 /var/www/html/var/cache
RUN chmod -R 770 /var/www/html/var/log
