FROM php:8.3-fpm

RUN apt-get update && DEBIAN_FRONTEND=noninteractive \
    apt-get install -y --no-install-recommends libssl-dev zlib1g-dev curl git unzip netcat-traditional libxml2-dev libpq-dev libzip-dev && \
    pecl install apcu && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install -j$(nproc) zip opcache intl pdo_pgsql pgsql && \
    docker-php-ext-enable apcu pdo_pgsql sodium && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=composer:2.5.4 /usr/bin/composer /usr/bin/composer
RUN composer --version

RUN pecl install xdebug-3.3.1 && docker-php-ext-enable xdebug

ARG UID
ARG GID

RUN groupadd -g ${GID} docker-php-group
RUN useradd -m docker-php-user --uid=${UID} --gid=${GID}
