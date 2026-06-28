FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl git unzip libzip-dev libpng-dev libonig-dev libxml2-dev libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring zip exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copy composer files first (cache layer)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy package files
COPY package.json package-lock.json ./
RUN npm ci

# Copy all app files
COPY . .

# Build frontend
RUN npm run build

# Run composer scripts after all files are copied
RUN composer dump-autoload --optimize

# Set permissions
RUN mkdir -p /var/data \
    storage/logs \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache /var/data

EXPOSE 8000

CMD ["sh", "-c", "\
    cp /app/.env.production /app/.env && \
    touch /var/data/database.sqlite && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000} \
"]
