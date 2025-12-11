FROM php:7.4-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN mkdir -p /var/www/html/uploads \
    && mkdir -p /var/www/html/application/logs \
    && mkdir -p /var/www/html/application/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/application/logs \
    && chmod -R 777 /var/www/html/application/cache \
    && chmod -R 777 /var/www/html/uploads

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]

