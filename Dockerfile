FROM php:8.4-fpm

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && apt-get purge -y --auto-remove

COPY --from=composer /usr/bin/composer /usr/bin/composer

# RUN chown -R www-data:www-data /var/www/html

# USER www-data
