FROM php:8.2-fpm

# Instalar dependências do sistema para pdo_pgsql
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Define o diretório de trabalho
WORKDIR /app

# Copia os arquivos da aplicação para o container
COPY . /app