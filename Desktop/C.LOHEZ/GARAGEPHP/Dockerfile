# Utiliser une image php officelle avec appache
FROM php:8.1-apache

# Installer les dependances et bibliotheques
RUN apt-get update && apt-get install-y --no-install-recommends \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
#Activer ServiceName
RUN echo "ServiceName localhost" >> /etc/apache2/apache2.conf
RUN sed -i '/<VirtualHost *:80>/a\ ServerName localhost' /etc/apache2/sites-available/000-default.conf
# Activer le mod_rewrite d'apache pour les URLs
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de dependance et les installer
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-scripts --prefer-dist

# cpier le reste du code de l'application
COPY . .

# Executer le dump de l'autoload de composer
RUN composer dump-autoload --optimize

# changer proprietaire des fichiers afin de donner les droit au serveur d'écrire dans les fichiers (ex: logs)
RUN mkdir -p storage/logs && \
    chown -R www-data:www-data /var/www/html/storage
