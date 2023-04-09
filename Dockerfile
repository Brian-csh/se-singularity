# Use official PHP image as the base
FROM php:7.2-apache

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy the application files into the container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader

# Expose port 80 for Apache
EXPOSE 80