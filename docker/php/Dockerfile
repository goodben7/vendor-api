FROM php:8.4-cli-bookworm

WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
        bcmath \
        intl \
        pdo_mysql \
        zip

RUN curl -fsSL https://getcomposer.org/installer -o /tmp/composer-installer.php \
    && php /tmp/composer-installer.php --install-dir=/usr/local/bin --filename=composer \
    && rm /tmp/composer-installer.php

RUN curl -fsSL https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

EXPOSE 8000

CMD ["symfony", "server:start", "--no-tls", "--allow-http", "--allow-all-ip", "--listen-ip=0.0.0.0", "--port=8000"]
