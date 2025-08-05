FROM php:8.2-apache

# 必要なPHP拡張
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring bcmath gd zip

# Apacheのmod_rewriteを有効化
RUN a2enmod rewrite

# 作業ディレクトリ
WORKDIR /var/www/html

# 権限調整
RUN chown -R www-data:www-data /var/www/html

# composerインストール
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer