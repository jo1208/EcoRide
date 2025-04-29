# Étape 1 : base PHP + Apache
FROM php:8.2-apache

# Étape 2 : installer librairies systèmes
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Étape 3 : configurer Apache
RUN a2enmod rewrite

# Étape 4 : installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Étape 5 : définir dossier de travail
WORKDIR /var/www/html

# Étape 6 : copier composer.json uniquement (pas tout)
COPY composer.json composer.lock symfony.lock ./

# Étape 7 : installer les dépendances sans scripts
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-scripts

# Étape 8 : copier tout le code projet
COPY . .

# Étape 9 : donner les droits à Apache
RUN chown -R www-data:www-data /var/www/html

# Étape 10 : configurer Apache pour Symfony dans /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Étape 11 : exposer le port
EXPOSE 8080

# Étape 12 : lancer Apache
CMD ["/bin/sh", "-c", "composer run-script auto-scripts || true && apache2-foreground"]
