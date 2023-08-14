ARG TARGET=base
ARG PHP_VERSION=8.0
ARG BASE_IMAGE_TAG=1.144.0

FROM eu.gcr.io/amh-infrastructure/laravel/${PHP_VERSION}/${TARGET}:${BASE_IMAGE_TAG} as base

ARG TARGET

RUN echo "TARGET used is $TARGET" \
    && echo "PHP_VERSION used is $PHP_VERSION"

# Insert for a Laravel Worker
COPY .ops/docker/config/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

# Insert for kibana-log-fetcher
COPY .ops/docker/config/kibana-log-fetcher.conf /etc/supervisor/conf.d/kibana-log-fetcher.conf

# Insert for php-fpm-log-fetcher
COPY .ops/docker/config/php-fpm-log-fetcher.conf /etc/supervisor/conf.d/php-fpm-log-fetcher.conf

# Insert for Cron and add your command into ./ops/docker/config/crontab
COPY .ops/docker/config/dcron.conf /etc/supervisor/conf.d/dcron.conf
COPY .ops/docker/config/crontab /var/spool/cron/crontabs/www-data
RUN apk add --update --no-cache dcron

USER www-data

COPY --chown=www-data:www-data composer.json composer.lock ./

RUN composer install \
  --no-autoloader \
  --no-progress \
  --no-scripts \
  --no-suggest \
  --prefer-dist

USER root

FROM base as code

USER www-data

COPY --chown=www-data:www-data .env.example .env

COPY --chown=www-data:www-data . .

RUN composer install \
  --no-dev \
  --no-suggest \
  --optimize-autoloader \
  --prefer-dist

RUN php artisan key:generate
RUN php -r "if (function_exists('\\opcache_reset')) { opcache_reset(); }"
RUN php artisan route:cache

USER root

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]

FROM code as remote

RUN rm -rf ~/.composer/cache \
    && rm -f /var/www/html/auth.json /root/.composer/auth.json
