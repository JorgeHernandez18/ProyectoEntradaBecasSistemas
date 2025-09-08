#!/bin/bash
# ==========================================
# Script de Instalación Simplificada
# Sistema de Becarios UFPS v1.0
# Conecta a PostgreSQL existente en el servidor
# ==========================================

set -e

echo "==========================================="
echo "🚀 Instalador Sistema de Becarios UFPS v1.0"
echo "Conecta a PostgreSQL existente en el servidor"
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

# Verificar PostgreSQL existente
echo ""
echo "🔍 Verificando PostgreSQL existente..."
echo "Configuración esperada:"
echo "  Host: localhost"
echo "  Base de datos: becarios_sistemas"
echo "  Usuario: becario"
echo "  Contraseña: becarios"

if command -v psql &> /dev/null; then
    if PGPASSWORD="becarios" psql -h localhost -U becario -d becarios_sistemas -c "SELECT version();" > /dev/null 2>&1; then
        echo "✅ Conexión a PostgreSQL verificada"
    else
        echo "⚠️ No se puede conectar a PostgreSQL con las credenciales esperadas"
        echo "💡 Verificar que:"
        echo "   - PostgreSQL esté ejecutándose"
        echo "   - Base de datos 'becarios_sistemas' exista"
        echo "   - Usuario 'becario' tenga permisos"
    fi
else
    echo "⚠️ Cliente psql no encontrado, se asume que PostgreSQL está configurado"
fi

# Solicitar configuración de puertos
echo ""
echo "🔧 Configuración de Puertos"
echo "El sistema requiere solo 2 puertos:"
echo "- PORT_0: Aplicación web principal"
echo "- PORT_1: pgAdmin (opcional)"

read -p "Puerto para aplicación web (PORT_0) [8080]: " PORT_0
PORT_0=${PORT_0:-8080}

read -p "Puerto para pgAdmin (PORT_1) [8081]: " PORT_1
PORT_1=${PORT_1:-8081}

# Crear archivo .env
echo "📝 Creando archivo de configuración..."
cat > .env << EOF
# Configuración de puertos generada por install_simple.sh
# Conecta a PostgreSQL existente en el servidor

PORT_0=$PORT_0
PORT_1=$PORT_1
PORT_2=
PORT_3=
PORT_4=
PORT_5=
PORT_6=
PORT_7=
PORT_8=
PORT_9=

COMPOSE_PROJECT_NAME=becarios-ufps
COMPOSE_FILE=docker-compose.yml

# PostgreSQL existente ya configurado:
# Host: host.containers.internal
# Base de datos: becarios_sistemas
# Usuario: becario
# Contraseña: becarios
EOF

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

for file in "\${required_files[@]}"; do
    if [[ ! -f "$file" ]]; then
        echo "❌ Error: Archivo requerido no encontrado: $file"
        exit 1
    fi
done

echo "✅ Todos los archivos necesarios están presentes"

# Hacer ejecutables los scripts
chmod +x deployment/scripts/*.sh

# Mostrar información pre-instalación
echo ""
echo "📋 Información del despliegue:"
echo "🌐 Aplicación web: http://localhost:$PORT_0"
echo "🔧 Panel admin: http://localhost:$PORT_0/admin"
echo "📊 pgAdmin: http://localhost:$PORT_1 (desarrollo)"
echo ""
echo "🗄️ Se conectará a tu PostgreSQL existente:"
echo "   Host: localhost (host.containers.internal desde contenedor)"
echo "   Base de datos: becarios_sistemas"
echo "   Usuario: becario"
echo ""

read -p "¿Continuar con la instalación? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Instalación cancelada"
    exit 1
fi

# Construir e iniciar contenedor
echo ""
echo "🏗️ Construyendo e iniciando contenedor..."
podman-compose up -d --build

# Esperar a que el servicio esté listo
echo ""
echo "⏳ Esperando a que la aplicación esté lista..."
sleep 15

# Verificar estado del contenedor
echo ""
echo "🔍 Verificando estado del servicio..."

if podman-compose ps | grep -q "becarios_app.*Up"; then
    echo "✅ Aplicación web: OK"
else
    echo "❌ Aplicación web: Error"
    echo "Ver logs con: podman-compose logs becarios_app"
fi

# Verificar conexión a BD
echo ""
echo "🔍 Verificando conexión a base de datos..."
if podman exec becarios_app pg_isready -h host.containers.internal -U becario -d becarios_sistemas -q 2>/dev/null; then
    echo "✅ Conexión a PostgreSQL: OK"
else
    echo "⚠️ Conexión a PostgreSQL: Verificar"
    echo "Ver logs con: podman-compose logs becarios_app"
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
echo "📊 pgAdmin: http://localhost:$PORT_1"
echo ""
echo "👤 Usuarios por defecto:"
echo "   Admin: admin / Admin123"
echo "   Entrada: entrada / Entrada123"
echo ""
echo "⚠️  IMPORTANTE:"
echo "   1. Cambiar contraseñas por defecto después del primer acceso"
echo "   2. Ir a Configuración > Cambiar Contraseñas en el panel admin"
echo ""
echo "🗄️ Base de Datos (tu PostgreSQL existente):"
echo "   Host: localhost"
echo "   Base de datos: becarios_sistemas"
echo "   Usuario: becario"
echo "   Contraseña: becarios"
echo ""
echo "📚 Si hay errores de base de datos:"
echo "   Ejecutar manualmente: deployment/database/init_postgresql.sql"
echo "   psql -h localhost -U becario -d becarios_sistemas < deployment/database/init_postgresql.sql"
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
Conecta a PostgreSQL existente
===========================================

Fecha de instalación: $(date)
Puerto aplicación web: $PORT_0
Puerto pgAdmin: $PORT_1

URLs de acceso:
- Aplicación: http://localhost:$PORT_0
- Admin: http://localhost:$PORT_0/admin  
- pgAdmin: http://localhost:$PORT_1

Usuarios por defecto:
- admin / Admin123
- entrada / Entrada123

Base de datos (tu PostgreSQL existente):
- Host: localhost
- DB: becarios_sistemas
- Usuario: becario
- Contraseña: becarios

Comandos útiles:
- podman-compose ps
- podman-compose logs -f
- podman-compose restart
- podman-compose down

IMPORTANTE: 
- Cambiar contraseñas por defecto
- Si hay errores de BD, ejecutar: deployment/database/init_postgresql.sql
===========================================
EOF

echo "💾 Información de instalación guardada en: installation_info.txt"
echo ""
echo "Sistema listo para usar! 🚀"