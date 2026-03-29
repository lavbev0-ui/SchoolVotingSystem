FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    libpq-dev \
    curl \
    unzip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install --optimize-autoloader --no-dev --no-scripts

COPY . .

RUN mkdir -p bootstrap/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs

EXPOSE 8000

CMD php -r "file_put_contents('.env', implode(\"\n\", array_map(fn(\$k) => \"\$k=\" . getenv(\$k), array_keys(getenv()))));" \
    && php artisan config:clear \
    && php artisan migrate --force \
    && php artisan serve --host=0.0.0.0 --port=$PORT