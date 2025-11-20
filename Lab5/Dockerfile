FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    librdkafka-dev \
    libzip-dev \
    default-mysql-client

# Установка расширений PHP
RUN docker-php-ext-install pdo pdo_mysql zip

# Установка rdkafka
RUN pecl install rdkafka && docker-php-ext-enable rdkafka

WORKDIR /var/www/html

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

COPY composer.json /var/www/html/
RUN composer install --no-dev --optimize-autoloader

COPY ./www /var/www/html

# Создаем директорию для логов
RUN mkdir -p /var/www/html/logs && chmod 777 /var/www/html/logs

CMD ["php-fpm"]
