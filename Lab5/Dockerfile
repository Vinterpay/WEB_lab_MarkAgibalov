FROM php:8.2-fpm

# Устанавливаем системные зависимости и расширения MySQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mysqli

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копируем файлы зависимостей Composer из корня проекта (где запускается docker build)
COPY composer.json composer.lock* ./
# Устанавливаем зависимости Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction
COPY ./www /var/www/html

# Настраиваем права
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

CMD [
# Установка зависимостей для php-rdkafka
RUN apt-get update && apt-get install -y \
    librdkafka-dev \
    && pecl install rdkafka \
    && docker-php-ext-enable rdkafka

CMD ["php-fpm"]

