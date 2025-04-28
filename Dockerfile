FROM php:8.2-apache

# Installe les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install intl pdo pdo_mysql zip

# Active mod_rewrite pour Symfony
RUN a2enmod rewrite

# Copie ton code dans le conteneur
COPY . /var/www/html/

# Change DocumentRoot d'Apache vers /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Donne les bonnes permissions
RUN chown -R www-data:www-data /var/www/html

# Expose le port 80
EXPOSE 80

# Commande de lancement d'Apache
CMD ["apache2-foreground"]
