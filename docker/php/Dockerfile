FROM --platform=linux/arm64 php:8.4-fpm

# Composerインストールではデフォルトでは/var/www/htmlを指定されるため明示的に指定
WORKDIR /var/www/html/src

# 必要な依存関係をインストール
RUN apt-get update && apt-get install -y \
  git \
  unzip \
  libpq-dev \
  libzip-dev \
  cron \
  # Node.js と npm を追加
  nodejs \
  npm \
  && docker-php-ext-install pdo pdo_mysql zip bcmath pcntl

# Composerのインストール
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
  php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
  php -r "unlink('composer-setup.php');"

# アプリケーションファイルをコピー
COPY src/ /var/www/html/src/

# Composerのインストールを実行
RUN composer install --ignore-platform-reqs --no-interaction --no-plugins --no-scripts --prefer-dist

EXPOSE 9000
CMD ["php-fpm"]
