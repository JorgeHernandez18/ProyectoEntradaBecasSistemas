# 🚀 Guía de Despliegue - Sistema de Becarios UFPS

## 📋 Resumen

Sistema completo de gestión de becarios para Ingeniería de Sistemas UFPS, desplegado con Podman Compose y PostgreSQL.

### 🛠 Tecnologías
- **Frontend**: PHP 8.2 + Apache + Material Dashboard
- **Backend**: PHP con adapter PostgreSQL  
- **Base de datos**: PostgreSQL 15
- **Contenedores**: Podman Compose
- **Características**: Auto-salidas, gestión de horarios, Excel export

---

## 🔧 Requisitos del Servidor

### Software Necesario
```bash
# Podman y Podman Compose
sudo dnf install podman podman-compose  # Para CentOS/RHEL/Fedora
# O
sudo apt install podman podman-compose  # Para Ubuntu/Debian

# Verificar instalación
podman --version
podman-compose --version
```

### Base de Datos PostgreSQL (Prerequisito)
El sistema se conecta a tu PostgreSQL existente con estas credenciales:
- **Host**: `host.containers.internal` (desde el contenedor)
- **Base de datos**: `becarios_sistemas`
- **Usuario**: `becario`
- **Contraseña**: `becarios`
- **Puerto**: `5432`

### Puertos Requeridos
El sistema utiliza solo **2 puertos** con la nomenclatura requerida:
- **${PORT_0}**: Aplicación web principal (puerto 80 interno)
- **${PORT_1}**: pgAdmin (opcional, solo desarrollo)

---

## 📁 Estructura del Proyecto

```
ProyectoEntradaBecasSistemas/
├── 📄 docker-compose.yml           # Configuración principal Podman
├── 📄 Dockerfile                   # Imagen de la aplicación PHP
├── 📁 deployment/                  # Archivos de despliegue
│   ├── 📁 config/                  # Configuraciones
│   │   ├── app.env                 # Variables de entorno de la app
│   │   ├── database.env            # Variables de PostgreSQL
│   │   ├── conexion_docker.php     # Conexión PostgreSQL
│   │   ├── php.ini                 # Configuración PHP
│   │   ├── apache.conf             # Configuración Apache
│   │   └── postgresql.conf         # Configuración PostgreSQL
│   ├── 📁 database/                # Base de datos
│   │   └── init_postgresql.sql     # Estructura completa PostgreSQL
│   └── 📁 scripts/                 # Scripts de instalación
│       ├── entrypoint.sh           # Script de inicialización
│       └── backup_db.sh            # Script de respaldo
├── 📁 admin/                       # Panel de administración
├── 📁 vistas/                      # Frontend público
└── 📄 README_DESPLIEGUE.md         # Esta guía
```

---

## 🚀 Proceso de Despliegue

### Paso 1: Preparar el Entorno

```bash
# 1. Clonar/subir el proyecto al servidor
cd /opt/
git clone <tu-repositorio> becarios-ufps
# O subir manualmente los archivos

cd becarios-ufps/
```

### Paso 2: Configurar Variables de Entorno

Crear archivo `.env` con los puertos asignados:
```bash
# Archivo: .env
PORT_0=8080    # Puerto de la aplicación web
PORT_1=8081    # Puerto de pgAdmin (opcional)
```

### Paso 3: Verificar Configuración de Base de Datos

La configuración ya está lista para tu PostgreSQL:
```bash
# Ya configurado en: deployment/config/database.env
POSTGRES_DB=becarios_sistemas
POSTGRES_USER=becario
POSTGRES_PASSWORD=becarios

# Ya configurado en: deployment/config/app.env  
DB_HOST=host.containers.internal
DB_NAME=becarios_sistemas
DB_USER=becario
DB_PASSWORD=becarios
```

**✅ No necesitas cambiar nada**, las credenciales ya están configuradas.

### Paso 4: Desplegar con Podman Compose

```bash
# Construir e iniciar contenedor (solo aplicación web)
podman-compose up -d

# Verificar estado
podman-compose ps

# Ver logs de la aplicación
podman-compose logs -f becarios_app
```

### Paso 5: Verificar Despliegue

```bash
# Verificar aplicación web
curl -I http://localhost:${PORT_0}

# Verificar conexión a tu PostgreSQL existente
podman exec becarios_app pg_isready -h host.containers.internal -U becario -d becarios_sistemas

# Verificar logs de inicialización
podman-compose logs becarios_app | grep "Sistema de Becarios UFPS listo"
```

---

## 🔐 Acceso al Sistema

### URLs de Acceso
```
# Aplicación principal
http://tu-servidor:${PORT_0}

# Panel de administración  
http://tu-servidor:${PORT_0}/admin

# Registro de entrada (público)
http://tu-servidor:${PORT_0}/vistas/formularios/registro.php
```

### Usuarios por Defecto
```
# Usuario Administrador
Usuario: admin
Contraseña: Admin123

# Usuario de Entrada (registro becarios)
Usuario: entrada  
Contraseña: Entrada123
```

**⚠️ IMPORTANTE**: Cambiar estas contraseñas inmediatamente después del primer acceso.

---

## 🗄️ Base de Datos

### Información de Conexión (Tu PostgreSQL existente)
```
Host: host.containers.internal (desde contenedor) o localhost (externo)
Puerto: 5432
Base de datos: becarios_sistemas
Usuario: becario
Contraseña: becarios
```

### Estructura Principal
- **becarios_admin**: Usuarios del sistema
- **becarios_info**: Información de becarios  
- **becarios_registro**: Registros entrada/salida
- **becarios_horarios**: Horarios programados
- **becarios_config_horas**: Configuración de horas

### Datos de Ejemplo
El sistema incluye datos de prueba:
- 3 becarios ejemplo
- Horarios programados
- Registros de ejemplo
- Usuarios administrador configurados

---

## ⚙️ Configuración Post-Despliegue

### 1. Cambiar Contraseñas
```bash
# Acceder al panel admin
http://tu-servidor:${PORT_0}/admin

# Ir a: Configuración > Cambiar Contraseñas
# Cambiar tanto la del admin como la del usuario entrada
```

### 2. Configurar Auto-Salidas
El sistema está configurado para marcar salidas automáticas:
- Se ejecuta cada 10 minutos automáticamente
- Marca salida 30 minutos después del horario programado
- Panel de control en: Admin > Auto Salidas

### 3. Configurar Respaldos (Opcional)
```bash
# Script de respaldo incluido
podman exec becarios_app /app/deployment/scripts/backup_db.sh

# Configurar cron en el host (recomendado)
0 2 * * * podman exec becarios_app /app/deployment/scripts/backup_db.sh
```

---

## 🐛 Solución de Problemas

### Error: No se puede conectar a PostgreSQL
```bash
# Verificar contenedor PostgreSQL
podman logs postgres

# Verificar configuración de red
podman network ls
podman network inspect becarios_network

# Verificar variables de entorno
podman exec becarios_app env | grep DB_
```

### Error: Aplicación muestra página en blanco
```bash
# Verificar logs PHP
podman logs becarios_app

# Verificar permisos
podman exec becarios_app ls -la /app/
podman exec becarios_app ls -la /app/logs/
```

### Error: No se pueden subir fotos
```bash
# Verificar permisos de directorio
podman exec becarios_app ls -la /app/admin/assets/fotos_becarios/

# Corregir permisos si es necesario
podman exec becarios_app chown -R www-data:www-data /app/admin/assets/fotos_becarios/
```

### PostgreSQL no inicia
```bash
# Verificar logs
podman logs postgres

# Verificar volumen de datos
podman volume ls | grep postgres

# Recrear volumen si es necesario (CUIDADO: elimina datos)
podman-compose down -v
podman volume rm becarios_postgres_data
podman-compose up -d
```

---

## 🔧 Comandos Útiles

### Gestión de Contenedores
```bash
# Ver estado
podman-compose ps

# Reiniciar servicios
podman-compose restart becarios_app
podman-compose restart postgres

# Ver logs en tiempo real
podman-compose logs -f

# Acceder a contenedor
podman exec -it becarios_app bash
podman exec -it postgres psql -U becarios_user -d becarios_ufps

# Parar sistema
podman-compose down

# Actualizar aplicación
podman-compose down
podman-compose build --no-cache
podman-compose up -d
```

### Base de Datos
```bash
# Respaldo manual
podman exec becarios_app /app/deployment/scripts/backup_db.sh

# Conectar a PostgreSQL
podman exec -it postgres psql -U becarios_user -d becarios_ufps

# Ver tablas
podman exec postgres psql -U becarios_user -d becarios_ufps -c "\dt"

# Ver usuarios del sistema
podman exec postgres psql -U becarios_user -d becarios_ufps -c "SELECT * FROM becarios_admin;"
```

---

## 🔒 Seguridad

### Recomendaciones de Producción

1. **Cambiar contraseñas por defecto**
2. **Configurar firewall para puertos específicos**
3. **Usar HTTPS con certificado SSL**
4. **Configurar respaldos automáticos**
5. **Monitorear logs regularmente**
6. **Mantener sistema actualizado**

### Archivos Sensibles
```bash
deployment/config/database.env    # Contraseñas DB
deployment/config/app.env         # Configuración aplicación
.env                             # Puertos del sistema
```

---

## 📊 Monitoreo

### Logs Importantes
```bash
# Logs de aplicación
podman logs becarios_app

# Logs de PostgreSQL  
podman logs postgres

# Logs del sistema (dentro del contenedor)
podman exec becarios_app tail -f /var/log/becarios/app_$(date +%Y-%m-%d).log
```

### Métricas de Rendimiento
```bash
# Uso de recursos
podman stats

# Espacio en disco
podman system df
podman volume ls

# Estado de la base de datos
podman exec postgres psql -U becarios_user -d becarios_ufps -c "SELECT version();"
```

---

## ✅ Lista de Verificación Post-Despliegue

- [ ] Contenedores iniciados correctamente
- [ ] Base de datos PostgreSQL funcionando
- [ ] Aplicación web accesible
- [ ] Login de administrador funcionando  
- [ ] Contraseñas por defecto cambiadas
- [ ] Sistema de auto-salidas activado
- [ ] Fotos de becarios se pueden subir
- [ ] Export a Excel funciona
- [ ] Respaldos configurados
- [ ] Logs monitoreándose
- [ ] Firewall configurado
- [ ] SSL configurado (producción)

---

## 📞 Soporte

Para soporte técnico:
1. Verificar logs según esta guía
2. Revisar sección de solución de problemas  
3. Contactar al equipo de desarrollo con:
   - Versión del sistema
   - Logs específicos del error
   - Pasos para reproducir el problema

---

**Sistema de Becarios UFPS v1.0**  
**Compatible con:** PostgreSQL 12+, Podman 3.0+  
**Licencia:** Uso interno UFPS