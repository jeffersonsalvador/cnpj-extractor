FROM php:8.2.11-fpm

# Install composer
RUN echo "\e[1;33mInstall COMPOSER\e[0m" \
    && cd /tmp \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Install PHP extensions and useful tools
RUN apt-get update \
    && apt-get install -y apt-utils nano wget dialog vim \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql \
    && apt-get install -y libzip-dev zip \
    && docker-php-ext-install zip

# Install important libraries
RUN echo "\e[1;33mInstall important libraries\e[0m" \
    && apt-get install -y build-essential git curl libcurl4 libcurl4-openssl-dev zlib1g-dev libbz2-dev locales libmcrypt-dev libicu-dev libonig-dev libxml2-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www
