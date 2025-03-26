FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    gnupg2 ca-certificates unzip zip git wget curl \
    libicu-dev libonig-dev libzip-dev libpq-dev \
    libjpeg-dev libpng-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction --no-progress --optimize-autoloader

RUN chown -R www-data:www-data /var/www

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "router.php"]
