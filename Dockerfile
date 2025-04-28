FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install intl pdo pdo_mysql zip

RUN a2enmod rewrite

# Copie Composer depuis une image composer officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copie ton code
COPY . /var/www/html/

# Change le DocumentRoot pour Symfony
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Utiliser le port 8080
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Permissions et config Git pour éviter erreur "dubious ownership"
WORKDIR /var/www/html
RUN git config --global --add safe.directory /var/www/html

# Désactive les auto-scripts Symfony et autorise Composer en root
ENV SYMFONY_SKIP_AUTO_SCRIPTS=1
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installe les dépendances sans exécuter les scripts
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

EXPOSE 8080
