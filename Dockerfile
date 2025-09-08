# ==========================================
# Dockerfile para Sistema de Becarios UFPS
# ==========================================

FROM php:8.2-apache

# Información del mantenedor
LABEL maintainer="Sistema de Becarios UFPS"
LABEL version="1.0"
LABEL description="Sistema de gestión de becarios para Ingeniería de Sistemas UFPS"

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libxml2-dev \
    libpq-dev \
    postgresql-client \
    cron \
    supervisor \
    curl \
    gettext-base \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        zip \
        intl \
        xml \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurar PHP
COPY deployment/config/php.ini /usr/local/etc/php/conf.d/custom.ini

# Habilitar mod_rewrite para Apache
RUN a2enmod rewrite

# Configurar Apache
COPY deployment/config/apache.conf /etc/apache2/sites-available/000-default.conf

# Crear directorios necesarios
RUN mkdir -p /var/log/becarios \
    && mkdir -p /app/admin/assets/fotos_becarios \
    && mkdir -p /app/logs \
    && mkdir -p /tmp/uploads \
    && chown -R www-data:www-data /var/log/becarios \
    && chown -R www-data:www-data /app \
    && chmod -R 755 /app

# Copiar código fuente
COPY . /app/

# Crear enlace simbólico para el archivo de conexión
RUN ln -sf /app/deployment/config/conexion_docker.php /app/modelo/conexion.php

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar dependencias PHP
WORKDIR /app
RUN if [ -f "composer.json" ]; then composer install --no-dev --optimize-autoloader; fi

# Configurar permisos
RUN chown -R www-data:www-data /app \
    && find /app -type d -exec chmod 755 {} \; \
    && find /app -type f -exec chmod 644 {} \; \
    && chmod +x /app/deployment/scripts/*.sh

# Configurar cron para auto-salidas
COPY deployment/config/crontab /etc/cron.d/becarios-cron
RUN chmod 0644 /etc/cron.d/becarios-cron \
    && crontab /etc/cron.d/becarios-cron

# Configurar supervisor
COPY deployment/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Script de inicialización
COPY deployment/scripts/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Exponer puerto
EXPOSE 80

# Variables de entorno por defecto
ENV APACHE_DOCUMENT_ROOT=/app
ENV APACHE_LOG_DIR=/var/log/apache2
ENV PHP_TIMEZONE=America/Bogota
ENV APP_ENV=production

# Punto de entrada
ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]