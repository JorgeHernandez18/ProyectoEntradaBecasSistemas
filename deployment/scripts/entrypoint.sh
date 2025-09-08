#!/bin/bash
# ==========================================
# Script de inicialización del contenedor
# ==========================================

set -e

echo "==========================================="
echo "Iniciando Sistema de Becarios UFPS v1.0"
echo "==========================================="

# La base de datos se configura manualmente via Adminer
echo "✓ La base de datos será configurada manualmente via Adminer"

# Crear directorios necesarios
echo "Configurando directorios..."
mkdir -p /var/log/becarios
mkdir -p /app/admin/assets/fotos_becarios
mkdir -p /app/logs
mkdir -p /tmp/uploads

# Configurar permisos
echo "Configurando permisos..."
chown -R www-data:www-data /var/log/becarios
chown -R www-data:www-data /app/admin/assets/fotos_becarios
chown -R www-data:www-data /app/logs
chown -R www-data:www-data /tmp/uploads
chmod -R 755 /app/admin/assets/fotos_becarios
chmod -R 755 /app/logs

# Instrucciones para configuración manual de BD
echo "📋 Para configurar la base de datos:"
echo "   1. Acceder a Adminer en puerto 8080"
echo "   2. Conectar a PostgreSQL con tus credenciales"
echo "   3. Importar archivo: deployment/database/init_postgresql.sql"

# Configurar zona horaria
echo "Configurando zona horaria: ${PHP_TIMEZONE}"
echo "${PHP_TIMEZONE}" > /etc/timezone
dpkg-reconfigure -f noninteractive tzdata

# Configurar Apache para usar el puerto dinámico
echo "Configurando Apache en puerto: ${APACHE_PORT:-80}"
if [ -n "${APACHE_PORT}" ] && [ "${APACHE_PORT}" != "80" ]; then
    echo "Listen ${APACHE_PORT}" >> /etc/apache2/ports.conf
    envsubst < /etc/apache2/sites-available/000-default.conf > /tmp/000-default.conf
    mv /tmp/000-default.conf /etc/apache2/sites-available/000-default.conf
fi

# Crear archivo de estado
echo "Sistema iniciado correctamente - $(date)" > /app/logs/sistema_status.log

echo "✓ Inicialización completada"
echo "==========================================="
echo "Sistema de Becarios UFPS listo"
echo "URL: http://localhost:8080"
echo "Admin: http://localhost:8080/admin"
echo "Usuario por defecto: admin / Admin123"
echo "==========================================="

# Ejecutar comando principal
exec "$@"