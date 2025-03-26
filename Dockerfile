FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    gnupg2 ca-certificates unzip zip git wget curl \
    libicu-dev libonig-dev libzip-dev libpq-dev \
    libjpeg-dev libpng-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache gd

# Symfony CLI installation (version stable connue)
ENV SYMFONY_CLI_VERSION=5.11.0

RUN wget https://github.com/symfony-cli/symfony-cli/releases/download/v${SYMFONY_CLI_VERSION}/symfony-cli_${SYMFONY_CLI_VERSION}_amd64.deb -O symfony.deb \
    && dpkg -i symfony.deb \
    && rm symfony.deb

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction --no-progress

RUN chown -R www-data:www-data /var/www

EXPOSE 8000

CMD ["symfony", "serve", "--no-tls", "--dir=public", "--allow-http", "--port=8000", "--allow-all-ip"]
