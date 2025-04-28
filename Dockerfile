FROM php:8.2-apache

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    unzip git curl libicu-dev libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite pour Symfony
RUN a2enmod rewrite

# Installer Composer proprement
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier **tout** le projet
COPY . .

# Variables d'environnement
ENV APP_ENV=prod
ENV SYMFONY_SKIP_AUTO_SCRIPTS=1
ENV APP_SECRET=changeme

# Installer les dépendances PHP (maintenant que tout est copié)
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Fixer les permissions pour Apache
RUN chown -R www-data:www-data /var/www/html

# Modifier la config Apache pour utiliser /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 8080

CMD ["apache2-foreground"]
