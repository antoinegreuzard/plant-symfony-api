FROM php:8.4-cli

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git unzip zip curl wget gnupg2 ca-certificates \
    libicu-dev libonig-dev libzip-dev libpq-dev \
    libjpeg-dev libpng-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache gd

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installer la Symfony CLI (version 5.11.0 par ex.)
ENV SYMFONY_CLI_VERSION=5.11.0
RUN wget https://github.com/symfony-cli/symfony-cli/releases/download/v${SYMFONY_CLI_VERSION}/symfony-cli_${SYMFONY_CLI_VERSION}_amd64.deb -O symfony.deb \
    && dpkg -i symfony.deb \
    && rm symfony.deb

# Définir le dossier de travail
WORKDIR /var/www

# Copier le code
COPY . .

# Installer les dépendances Symfony
RUN composer install --no-interaction --no-progress --optimize-autoloader

# Donner les bons droits
RUN chown -R www-data:www-data /var/www

# Exposer le port
EXPOSE 8000

# Lancer le serveur Symfony avec gestion des slashes (gérée automatiquement)
CMD ["symfony", "serve", "--no-tls", "--allow-http", "--port=8000", "--allow-all-ip", "--dir=public"]
