# /docker/Dockerfile.app

# Use an official PHP image with the required PHP extensions
FROM php:8.2-fpm

# Install dependencies for Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear out the local repository of retrieved package files
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY ../src /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
