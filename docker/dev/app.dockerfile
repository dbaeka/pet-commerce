FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG gid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libjpeg-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    supervisor \
    libzip-dev \
    cron \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql sockets mbstring exif pcntl bcmath

RUN docker-php-ext-configure gd --enable-gd --with-jpeg

RUN docker-php-ext-install gd

# setup redis
RUN pecl install redis \
        && docker-php-ext-enable redis

# Get latest Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $gid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

WORKDIR /var/www

COPY docker/start.sh /usr/local/bin/start
RUN chown -R $user: /var/www \
    && chmod u+x /usr/local/bin/start
COPY docker/supervisord.conf /etc/supervisor/conf.d/

COPY docker/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod u+x /usr/local/bin/wait-for-it

CMD ["/usr/local/bin/start"]
