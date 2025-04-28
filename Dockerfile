FROM php:8.2-apache

# Installe les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install intl pdo pdo_mysql zip

# Installe Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Active mod_rewrite pour Symfony
RUN a2enmod rewrite

# Copie ton code dans le conteneur
COPY . /var/www/html/

# Change DocumentRoot d'Apache vers /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Configure Apache pour écouter sur 8080
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Va dans ton projet et installe les dépendances Symfony
WORKDIR /var/www/html/
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Donne les bonnes permissions
RUN chown -R www-data:www-data /var/www/html

# Expose le port 8080
EXPOSE 8080

# Commande de lancement d'Apache
