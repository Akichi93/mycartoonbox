FROM php:7.4-fpm

WORKDIR /var/www/html/mycartoonboxbackend

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev

RUN docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo_mysql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY . .

RUN php artisan key:generate

RUN chown -R www-data:www-data /var/www/html/mycartoonboxbackend/storage /var/www/html/mycartoonboxbackend/bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "optimize:clear"]

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]