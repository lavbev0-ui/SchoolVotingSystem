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

# 1. Gumawa ng required directories
RUN mkdir -p bootstrap/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs

# 2. I-install muna ang dependencies
RUN composer install --optimize-autoloader --no-dev

# 3. Tapos na ngayon gamitin ang artisan
RUN cp .env.example .env && php artisan key:generate

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=$PORT
```

Ang pagbabago:
- `mkdir -p` para sa lahat ng kailangan ng Laravel
- `composer install` muna bago gamitin ang `artisan`
- `cp .env` at `key:generate` pagkatapos ma-install ang framework

Para sa Render.com, siguraduhing may **database environment variables** ka sa Environment tab (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) at idagdag mo rin sa Start Command ang migration kung kailangan:
```
php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT