FROM php:8.2-apache

# Note: Using PHP 8.2 for better CodeIgniter 3 compatibility
# PHP 8.3+ has many deprecation warnings with CodeIgniter 3
# PHP 8.4 is not yet available in official Docker images
# For production, consider PHP 7.4 or 8.0 if needed

# Set working directory
WORKDIR /var/www/html

# Enable Apache mod_rewrite for CodeIgniter
RUN a2enmod rewrite headers

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    mysqli \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    xml \
    dom \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# PHP configuration
RUN echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# Suppress PHP 8.2+ deprecation warnings for CodeIgniter 3 compatibility
RUN echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING" >> /usr/local/etc/php/conf.d/ci3-compat.ini \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/ci3-compat.ini \
    && echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/ci3-compat.ini

# Configure Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Expose port 80 for Apache
EXPOSE 80

CMD ["apache2-foreground"]

