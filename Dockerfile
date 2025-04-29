FROM php:8.2-apache

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    unzip git curl libicu-dev libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite
RUN a2enmod rewrite

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définir dossier de travail
WORKDIR /var/www/html

# Copier tout le projet
COPY . .

# Installer dépendances PHP correctement
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Fixer les permissions Apache
RUN chown -R www-data:www-data /var/www/html

# Configurer Apache pour Symfony (répertoire public)
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Exposer le port
EXPOSE 8080

# Lancer Apache
CMD ["apache2-foreground"]
