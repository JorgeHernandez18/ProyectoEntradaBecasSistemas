#!/bin/bash
# ==========================================
# Script de Instalación Rápida
# Sistema de Becarios UFPS v1.0
# ==========================================

set -e

echo "==========================================="
echo "🚀 Instalador Sistema de Becarios UFPS v1.0"
echo "==========================================="

# Verificar Podman
if ! command -v podman &> /dev/null; then
    echo "❌ Error: Podman no está instalado"
    echo "Instalar con: sudo dnf install podman (RHEL/Fedora) o sudo apt install podman (Ubuntu/Debian)"
    exit 1
fi

if ! command -v podman-compose &> /dev/null; then
    echo "❌ Error: Podman Compose no está instalado"
    echo "Instalar con: sudo dnf install podman-compose o sudo apt install podman-compose"
    exit 1
fi

echo "✅ Podman y Podman Compose detectados"

# Solicitar configuración de puertos
echo ""
echo "🔧 Configuración de Puertos"
echo "El sistema requiere los siguientes puertos:"
echo "- PORT_0: Aplicación web principal"
echo "- PORT_1: PostgreSQL (acceso externo)"
echo "- PORT_2: pgAdmin (opcional)"

read -p "Puerto para aplicación web (PORT_0) [8080]: " PORT_0
PORT_0=${PORT_0:-8080}

read -p "Puerto para PostgreSQL (PORT_1) [5433]: " PORT_1
PORT_1=${PORT_1:-5433}

read -p "Puerto para pgAdmin (PORT_2) [8081]: " PORT_2
PORT_2=${PORT_2:-8081}

# Crear archivo .env
echo "📝 Creando archivo de configuración..."
cat > .env << EOF
# Configuración de puertos generada por install.sh
PORT_0=$PORT_0
PORT_1=$PORT_1
PORT_2=$PORT_2
PORT_3=
PORT_4=
PORT_5=
PORT_6=
PORT_7=
PORT_8=
PORT_9=

COMPOSE_PROJECT_NAME=becarios-ufps
COMPOSE_FILE=docker-compose.yml
EOF

# Configurar contraseñas seguras
echo ""
echo "🔐 Configuración de Seguridad"
echo "Se generarán contraseñas seguras automáticamente"

# Generar contraseñas aleatorias
DB_PASSWORD=$(openssl rand -base64 32 | tr -d "=+/" | cut -c1-25)

# Actualizar contraseñas en archivos de configuración
sed -i "s/becarios_pass_2025!/${DB_PASSWORD}/g" deployment/config/database.env
sed -i "s/becarios_pass_2025!/${DB_PASSWORD}/g" deployment/config/app.env

echo "✅ Contraseñas de base de datos actualizadas"

# Verificar archivos necesarios
echo ""
echo "🔍 Verificando archivos del proyecto..."

required_files=(
    "docker-compose.yml"
    "Dockerfile"
    "deployment/database/init_postgresql.sql"
    "deployment/config/database.env"
    "deployment/config/app.env"
    "deployment/scripts/entrypoint.sh"
)

for file in "${required_files[@]}"; do
    if [[ ! -f "$file" ]]; then
        echo "❌ Error: Archivo requerido no encontrado: $file"
        exit 1
    fi
done

echo "✅ Todos los archivos necesarios están presentes"

# Hacer ejecutables los scripts
chmod +x deployment/scripts/*.sh

# Construir e iniciar contenedores
echo ""
echo "🏗️ Construyendo e iniciando contenedores..."
podman-compose up -d --build

# Esperar a que los servicios estén listos
echo ""
echo "⏳ Esperando a que los servicios estén listos..."
sleep 10

# Verificar estado de los contenedores
echo ""
echo "🔍 Verificando estado de los servicios..."

if podman-compose ps | grep -q "becarios_app.*Up"; then
    echo "✅ Aplicación web: OK"
else
    echo "❌ Aplicación web: Error"
    echo "Ver logs con: podman-compose logs becarios_app"
fi

if podman-compose ps | grep -q "postgres.*Up"; then
    echo "✅ Base de datos PostgreSQL: OK"  
else
    echo "❌ Base de datos PostgreSQL: Error"
    echo "Ver logs con: podman-compose logs postgres"
fi

# Mostrar información de acceso
echo ""
echo "==========================================="
echo "🎉 Instalación Completada!"
echo "==========================================="
echo ""
echo "📋 Información de Acceso:"
echo "🌐 Aplicación web: http://localhost:$PORT_0"
echo "🔧 Panel admin: http://localhost:$PORT_0/admin"
echo "📊 pgAdmin (desarrollo): http://localhost:$PORT_2"
echo ""
echo "👤 Usuarios por defecto:"
echo "   Admin: admin / Admin123"
echo "   Entrada: entrada / Entrada123"
echo ""
echo "⚠️  IMPORTANTE:"
echo "   1. Cambiar contraseñas por defecto después del primer acceso"
echo "   2. Ir a Configuración > Cambiar Contraseñas en el panel admin"
echo ""
echo "🗄️ Base de Datos:"
echo "   Host: localhost:$PORT_1"
echo "   Base de datos: becarios_ufps"
echo "   Usuario: becarios_user"
echo "   Contraseña: (generada automáticamente)"
echo ""
echo "📚 Comandos útiles:"
echo "   Ver logs: podman-compose logs -f"
echo "   Parar sistema: podman-compose down" 
echo "   Reiniciar: podman-compose restart"
echo ""
echo "📖 Documentación completa: README_DESPLIEGUE.md"
echo "==========================================="

# Guardar información de instalación
cat > installation_info.txt << EOF
===========================================
Sistema de Becarios UFPS v1.0 - Información de Instalación
===========================================

Fecha de instalación: $(date)
Puerto aplicación web: $PORT_0
Puerto PostgreSQL: $PORT_1
Puerto pgAdmin: $PORT_2

URLs de acceso:
- Aplicación: http://localhost:$PORT_0
- Admin: http://localhost:$PORT_0/admin  
- pgAdmin: http://localhost:$PORT_2

Usuarios por defecto:
- admin / Admin123
- entrada / Entrada123

Base de datos:
- Host: localhost:$PORT_1
- DB: becarios_ufps
- Usuario: becarios_user
- Contraseña: Ver deployment/config/database.env

Comandos útiles:
- podman-compose ps
- podman-compose logs -f
- podman-compose restart
- podman-compose down

IMPORTANTE: Cambiar contraseñas por defecto
===========================================
EOF

echo "💾 Información de instalación guardada en: installation_info.txt"
echo ""
echo "Sistema listo para usar! 🚀"