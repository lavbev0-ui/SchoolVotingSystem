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

RUN echo "APP_NAME=SchoolVotingSystem" > .env \
    && echo "APP_ENV=production" >> .env \
    && echo "APP_KEY=base64:Pi69KBPofkosb5ELja5NHUtSaG/00rNL8JqzkJB8SRE=" >> .env \
    && echo "APP_DEBUG=false" >> .env \
    && echo "APP_URL=http://localhost" >> .env \
    && echo "DB_CONNECTION=mysql" >> .env \
    && echo "DB_HOST=127.0.0.1" >> .env \
    && echo "DB_PORT=3306" >> .env \
    && echo "DB_DATABASE=laravel" >> .env \
    && echo "DB_USERNAME=root" >> .env \
    && echo "DB_PASSWORD=" >> .env \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && composer install --optimize-autoloader --no-dev --no-scripts

EXPOSE 8000

CMD php artisan config:cache && php artisan route:cache && php artisan serve --host=0.0.0.0 --port=$PORT