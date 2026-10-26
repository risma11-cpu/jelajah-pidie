FROM php:8.2-apache

# Install dependency & extension PHP
RUN apt-get update && apt-get install -y \
    nodejs npm libpng-dev zip unzip git \
    && docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

# Copy composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project
COPY . /var/www/html
WORKDIR /var/www/html

# Install package & build Vite
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Set permission & document root ke folder public
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

EXPOSE 80