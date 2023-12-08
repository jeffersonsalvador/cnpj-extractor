FROM php:8.2-fpm

# Instalar dependências do sistema para pdo_pgsql, Redis e extensão zip
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install zip

# Define o diretório de trabalho
WORKDIR /app

# Copia os arquivos da aplicação para o container
COPY . /app