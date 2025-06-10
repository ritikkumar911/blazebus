# Use official PHP image with Apache
FROM php:8.1-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# Copy all files into container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libonig-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html
