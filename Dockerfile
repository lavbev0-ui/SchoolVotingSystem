FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    curl \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN cp .env.example .env \
    && php artisan key:generate \
    && composer install --optimize-autoloader --no-dev

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=$PORT