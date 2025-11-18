# ==========================================
# Dockerfile para Sistema de Becarios UFPS
# Usa PHP-FPM + Nginx
# ==========================================

FROM php:8.2-fpm

# Información del mantenedor
LABEL maintainer="Sistema de Becarios UFPS"
LABEL version="2.0"
LABEL description="Sistema de gestión de becarios para Ingeniería de Sistemas UFPS"

# Instalar dependencias del sistema y nginx
RUN apt-get update && apt-get install -y \
    nginx \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    postgresql-client \
    curl \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        zip \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurar PHP
RUN echo "date.timezone = America/Bogota" > /usr/local/etc/php/conf.d/timezone.ini \
    && echo "upload_max_filesize = 10M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 10M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/memory.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar código fuente
COPY . /var/www/html/

# Crear directorios necesarios
RUN mkdir -p /var/www/html/admin/assets/fotos_becarios \
    && mkdir -p /var/www/html/logs \
    && mkdir -p /var/run/php \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/admin/assets/fotos_becarios \
    && chmod -R 777 /var/www/html/logs

# Instalar dependencias PHP
WORKDIR /var/www/html
RUN if [ -f "composer.json" ]; then composer install --no-dev --optimize-autoloader --no-interaction; fi

# Configurar nginx
COPY nginx.conf /etc/nginx/sites-available/default

# Configurar supervisor para manejar nginx + php-fpm
RUN echo "[supervisord]\n\
nodaemon=true\n\
\n\
[program:php-fpm]\n\
command=/usr/local/sbin/php-fpm\n\
autostart=true\n\
autorestart=true\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0\n\
\n\
[program:nginx]\n\
command=/usr/sbin/nginx -g 'daemon off;'\n\
autostart=true\n\
autorestart=true\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0" > /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

# Iniciar supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]