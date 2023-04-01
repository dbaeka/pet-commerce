FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

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

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# create document root, fix permissions for www-data user and change owner to www-data
#RUN mkdir -p $APP_HOME/public && \
 #   mkdir -p /home/$USERNAME && chown $USERNAME:$USERNAME /home/$USERNAME \
  #  && usermod -o -u $HOST_UID $USERNAME -d /home/$USERNAME \
   # && groupmod -o -g $HOST_GID $USERNAME \
   # && chown -R ${USERNAME}:${USERNAME} $APP_HOME


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# add supervisor
#RUN mkdir -p /var/log/supervisor
#COPY --chown=root:root ./docker/general/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
#COPY --chown=root:crontab ./docker/general/cron /var/spool/cron/crontabs/root
#RUN chmod 0600 /var/spool/cron/crontabs/root

WORKDIR /var/www

USER $user

# copy source files and config file
#COPY --chown=$user:$user . /var/www/#
#COPY --chown=$user:$user .env.dev /var/www/.env


#RUN COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-interaction --no-progress;

#RUN ["chmod", "+x", "./start_script.sh"]

#CMD ./start_script.sh

#USER root
