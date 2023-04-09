# Use official PHP image as the base
FROM php:7.2-apache

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN composer install

# Copy the application files into the container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose port 80 for Apache
EXPOSE 80