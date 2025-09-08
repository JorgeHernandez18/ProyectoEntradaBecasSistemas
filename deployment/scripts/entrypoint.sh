#!/bin/bash
# ==========================================
# Script de inicialización del contenedor
# ==========================================

set -e

echo "==========================================="
echo "Iniciando Sistema de Becarios UFPS v1.0"
echo "==========================================="

# Esperar a que la base de datos esté lista
echo "Conectando a PostgreSQL existente en el servidor..."
until PGPASSWORD="${DB_PASSWORD}" pg_isready -h"${DB_HOST}" -p"${DB_PORT}" -U"${DB_USER}" -d"${DB_NAME}" -q; do
    echo "Esperando conexión a PostgreSQL existente..."
    sleep 2
done

echo "✓ Base de datos conectada"

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

# Verificar si las tablas del sistema existen en la BD
echo "Verificando estructura de base de datos PostgreSQL existente..."
PGPASSWORD="${DB_PASSWORD}" psql -h"${DB_HOST}" -p"${DB_PORT}" -U"${DB_USER}" -d"${DB_NAME}" -c "\dt" > /tmp/tables.txt 2>/dev/null || {
    echo "⚠ Error al verificar tablas en la base de datos existente"
    echo "💡 La base de datos 'becarios_sistemas' debe estar creada previamente"
    echo "💡 Ejecutar manualmente: deployment/database/init_postgresql.sql"
}

# Verificar si existen las tablas principales del sistema
if ! grep -q "becarios_admin" /tmp/tables.txt 2>/dev/null; then
    echo "ℹ️ Sistema detecta que es la primera instalación"
    echo "📋 Instalando estructura de base de datos..."
    
    # Intentar crear la estructura
    if PGPASSWORD="${DB_PASSWORD}" psql -h"${DB_HOST}" -p"${DB_PORT}" -U"${DB_USER}" -d"${DB_NAME}" < /app/deployment/database/init_postgresql.sql; then
        echo "✅ Estructura de base de datos instalada correctamente"
    else
        echo "❌ Error al instalar estructura de base de datos"
        echo "💡 Verificar permisos del usuario 'becario' en la base de datos"
        echo "💡 O ejecutar manualmente el archivo: deployment/database/init_postgresql.sql"
    fi
else
    echo "✅ Estructura de base de datos ya existe"
fi

# Ejecutar migraciones adicionales si existen
if [ -d "/app/deployment/database/migrations" ]; then
    echo "Verificando migraciones PostgreSQL..."
    for migration in /app/deployment/database/migrations/*.sql; do
        if [ -f "$migration" ]; then
            echo "Ejecutando migración: $(basename "$migration")"
            PGPASSWORD="${DB_PASSWORD}" psql -h"${DB_HOST}" -p"${DB_PORT}" -U"${DB_USER}" -d"${DB_NAME}" < "$migration" || {
                echo "⚠ Error en migración: $(basename "$migration")"
            }
        fi
    done
fi

# Limpiar archivos temporales
rm -f /tmp/tables.txt

# Configurar zona horaria
echo "Configurando zona horaria: ${PHP_TIMEZONE}"
echo "${PHP_TIMEZONE}" > /etc/timezone
dpkg-reconfigure -f noninteractive tzdata

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