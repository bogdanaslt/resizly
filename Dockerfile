FROM dunglas/frankenphp:1.11-php8.5

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libvips-dev \
    libvips42 \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    libcap2-bin \
    zip \
    unzip \
    git \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions gd imagick pcntl bcmath mysqli pdo_mysql redis curl mbstring dom zip soap intl ffi

RUN groupadd -g 1000 app && useradd -u 1000 -g app -m -s /bin/bash app
RUN setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp
WORKDIR /app
RUN chown -R app:app /app /data /config

USER app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
