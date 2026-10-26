FROM php:8.5.12-cli-alpine

RUN apk add --no-cache git unzip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-progress --no-scripts --no-autoloader

COPY . .
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod 755 docker/entrypoint.sh \
    && composer dump-autoload --optimize --no-interaction

EXPOSE 8000

CMD ["/app/docker/entrypoint.sh"]
