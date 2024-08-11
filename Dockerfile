FROM php:8.3.7-apache

# Copy the Apache configuration file
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite if needed
RUN a2enmod rewrite