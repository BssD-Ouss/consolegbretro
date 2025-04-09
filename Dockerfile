FROM php:8.2-apache

# Installer les extensions nécessaires à Stripe PHP
RUN docker-php-ext-install pdo pdo_mysql

# Copier le code dans le container
COPY . /var/www/html/

# Activer mod_rewrite si besoin
RUN a2enmod rewrite

# Définir le dossier racine
WORKDIR /var/www/html

# Installer Composer et Stripe
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer install
