FROM php:8.2-apache

RUN apt-get update && \ 
    apt-get install -y \   
    unzip \
    libzip-dev \
    libpq-dev \    
    && rm -rf /var/lib/apt/lists/*
    
RUN docker-php-ext-install mysqli pdo pdo_mysql
   
COPY myapp.conf /etc/apache2/sites-available/000-default.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN a2enmod rewrite