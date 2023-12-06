FROM php:latest
# Instala as dependências necessárias para pdo_pgsql
RUN apt-get update && apt-get install -y libpq-dev

# Instala as extensões pdo e pdo_pgsql
RUN docker-php-ext-install pdo pdo_pgsql

WORKDIR /app
COPY . /app