FROM php:8.4-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    zip \
    unzip \
    $PHPIZE_DEPS \
    linux-headers

# Install Xdebug and APCu
RUN pecl install xdebug apcu \
    && docker-php-ext-enable xdebug apcu

# Copy APCu configuration
COPY docker/php/apcu.ini /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini

# Configure Xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/var/log/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=VSCODE" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Create xdebug log file
RUN touch /var/log/xdebug.log && chmod 666 /var/log/xdebug.log

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json ./

RUN composer install --no-scripts --no-autoloader --prefer-dist

COPY . .

RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www/html