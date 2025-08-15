# Gunakan PHP 7.4 FPM
FROM php:7.4-fpm

# Install system dependencies dan ekstensi PHP yang dibutuhkan Laravel 7
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libonig-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www

# Salin file composer
COPY composer.json composer.lock ./

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies Laravel
RUN composer install --no-dev --optimize-autoloader

# Salin seluruh project
COPY . .

# Set permission untuk storage dan cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port PHP-FPM
EXPOSE 9000

# Jalankan PHP-FPM
CMD ["php-fpm"]
