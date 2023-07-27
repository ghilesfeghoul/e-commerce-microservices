FROM php:8.2

RUN apt-get update && apt-get install -y \
    git \
    libc6-dev \
    libsasl2-dev \
    libsasl2-modules \
    libssl-dev \
    zip \
    unzip \
    libpq-dev

RUN docker-php-ext-install pdo pdo_mysql
RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install \
    pcntl

RUN docker-php-ext-install sockets

WORKDIR /app
COPY . .
RUN composer install
