# Use official PHP image as the base
FROM php:7.2-apache

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install Nginx and supervisor
RUN apt-get update && apt-get install -y nginx
# \ supervisor

# Copy the application files into the container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Copy Nginx configuration file
# COPY nginx/default.conf /etc/nginx/conf.d/

# Copy supervisor configuration file
# COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port 80 for Nginx
EXPOSE 80

# Start supervisord to start Nginx and PHP-FPM
# CMD ["/usr/bin/supervisord"]
