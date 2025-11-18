# 🚀 Guía de Despliegue - Sistema de Becarios UFPS

## 📋 Resumen

Sistema completo de gestión de becarios para Ingeniería de Sistemas UFPS, desplegado con Podman Compose y conexión a PostgreSQL externo.

### 🛠 Tecnologías
- **Frontend**: PHP 8.2 + Nginx + Material Dashboard
- **Backend**: PHP con PDO PostgreSQL
- **Base de datos**: PostgreSQL externo (ya existente en tu servidor)
- **Contenedores**: Podman Compose
- **Proxy**: Configurado mediante Ansible
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
El sistema se conecta a tu servidor PostgreSQL existente. Deberás configurar las credenciales en las variables de entorno.

### Puertos Requeridos
El sistema utiliza **1 puerto** con la nomenclatura automática:
- **${PORT_0}**: Aplicación web (puerto 80 interno del contenedor)

**Nota**: La variable `PORT_0` es asignada automáticamente por el sistema. El dominio y puerto público se configuran mediante Ansible.

---

## 📁 Estructura del Proyecto

```
ProyectoEntradaBecasSistemas/
├── 📄 docker-compose.yml           # Configuración principal Podman
├── 📄 Dockerfile                   # Imagen PHP-FPM + Nginx
├── 📄 nginx.conf                   # Configuración Nginx
├── 📄 .env.example                 # Ejemplo de variables de entorno
├── 📁 modelo/
│   └── conexion.php                # Conexión PostgreSQL con PDO
├── 📁 admin/                       # Panel de administración
├── 📁 vistas/                      # Frontend público
├── 📁 controladores/               # Lógica de negocio
└── 📄 README_DESPLIEGUE.md         # Esta guía
```

---

## 🚀 Proceso de Despliegue

### Paso 1: Clonar el Repositorio

```bash
# En tu servidor, clona el proyecto
git clone <tu-repositorio-github> becarios-sistemas
cd becarios-sistemas/
```

### Paso 2: Configurar Variables de Entorno

**IMPORTANTE**: Antes de desplegar, configura las credenciales de PostgreSQL.

1. Crea el archivo `.env` (la variable PORT_0 es asignada automáticamente):
```bash
# Este archivo puede estar vacío o contener:
# PORT_0 se asigna automáticamente por el sistema
```

2. Edita las variables de entorno en `docker-compose.yml`:
```yaml
environment:
  DB_HOST: tu_host_postgresql       # IP o hostname de tu PostgreSQL
  DB_PORT: 5432
  DB_NAME: tu_base_de_datos
  DB_USER: tu_usuario
  DB_PASS: tu_password
  TZ: America/Bogota
```

**O** usa un archivo `.env` local para sobreescribir:
```bash
# Crea .env con:
DB_HOST=ip_de_tu_postgres
DB_NAME=nombre_bd
DB_USER=usuario
DB_PASS=password
```

### Paso 3: Construir y Desplegar

```bash
# Construir la imagen
podman-compose build

# Iniciar el contenedor
podman-compose up -d

# Verificar estado
podman-compose ps
```

### Paso 4: Verificar Despliegue

```bash
# Ver logs del contenedor
podman-compose logs -f

# Verificar que el contenedor está corriendo
podman ps

# Probar la aplicación
curl http://localhost:${PORT_0}
```

### Paso 5: Configurar Dominio con Ansible

El dominio y puerto público se configuran mediante Ansible. Consulta con tu equipo de DevOps para:
- Asignar un dominio
- Configurar el proxy reverso
- Configurar certificado SSL si es necesario

---

## 🔐 Acceso al Sistema

### URLs de Acceso
Las URLs dependerán del dominio configurado por Ansible. Ejemplos:

```
# Aplicación principal
https://tu-dominio.com

# Panel de administración
https://tu-dominio.com/admin

# Registro de entrada/salida (público)
https://tu-dominio.com/vistas/formularios/registro.php
```

### Usuarios por Defecto
Consulta con tu equipo las credenciales de acceso inicial.

**⚠️ IMPORTANTE**: Cambiar las contraseñas inmediatamente después del primer acceso.

---

## 🗄️ Base de Datos

### Conexión a PostgreSQL Externo
La aplicación se conecta a tu servidor PostgreSQL existente usando las credenciales configuradas en las variables de entorno.

### Estructura Principal
- **becarios_admin**: Usuarios del sistema
- **becarios_info**: Información de becarios
- **becarios_registro**: Registros entrada/salida
- **becarios_horarios**: Horarios programados
- **becarios_config_horas**: Configuración de horas

### Verificar Conexión
```bash
# Desde dentro del contenedor
podman exec -it becarios-app php -r "require 'modelo/conexion.php'; echo 'Conexión exitosa';"
```

---

## ⚙️ Configuración Post-Despliegue

### 1. Cambiar Contraseñas
```bash
# Acceder al panel admin a través del dominio configurado
https://tu-dominio.com/admin

# Ir a: Perfil > Cambiar Contraseña
```

### 2. Verificar Funcionalidades
- Probar registro de entrada/salida de becarios
- Verificar generación de reportes
- Comprobar exportación a Excel
- Revisar cálculo de horas trabajadas

### 3. Configurar Respaldos (Recomendado)
Coordina con tu equipo de DevOps para configurar respaldos automáticos de la base de datos PostgreSQL.

---

## 🐛 Solución de Problemas

### Error: No se puede conectar a PostgreSQL
```bash
# Verificar variables de entorno del contenedor
podman exec becarios-app env | grep DB_

# Probar conexión desde el contenedor
podman exec -it becarios-app psql -h $DB_HOST -U $DB_USER -d $DB_NAME

# Verificar logs del contenedor
podman-compose logs -f
```

### Error: Aplicación muestra página en blanco
```bash
# Verificar logs de nginx y PHP-FPM
podman logs becarios-app

# Verificar permisos
podman exec becarios-app ls -la /var/www/html/
podman exec becarios-app ls -la /var/www/html/logs/

# Verificar que nginx y php-fpm están corriendo
podman exec becarios-app ps aux | grep nginx
podman exec becarios-app ps aux | grep php-fpm
```

### Error: No se pueden subir fotos
```bash
# Verificar permisos del directorio de fotos
podman exec becarios-app ls -la /var/www/html/admin/assets/fotos_becarios/

# Corregir permisos si es necesario
podman exec becarios-app chown -R www-data:www-data /var/www/html/admin/assets/fotos_becarios/
podman exec becarios-app chmod -R 777 /var/www/html/admin/assets/fotos_becarios/
```

### Error: Contenedor no inicia
```bash
# Ver logs detallados
podman-compose logs

# Reconstruir imagen
podman-compose down
podman-compose build --no-cache
podman-compose up -d
```

---

## 🔧 Comandos Útiles

### Gestión del Contenedor
```bash
# Ver estado
podman-compose ps
podman ps

# Reiniciar aplicación
podman-compose restart

# Ver logs en tiempo real
podman-compose logs -f

# Acceder al contenedor
podman exec -it becarios-app bash

# Parar aplicación
podman-compose down

# Actualizar aplicación
podman-compose down
podman-compose build --no-cache
podman-compose up -d

# Ver uso de recursos
podman stats becarios-app
```

### Verificación de Servicios
```bash
# Ver procesos dentro del contenedor
podman exec becarios-app ps aux

# Verificar nginx
podman exec becarios-app nginx -t

# Ver logs de nginx
podman exec becarios-app tail -f /var/log/nginx/error.log

# Ver logs de PHP
podman exec becarios-app tail -f /var/www/html/logs/
```

### Base de Datos (PostgreSQL Externo)
```bash
# Conectar a PostgreSQL desde el servidor
psql -h localhost -U tu_usuario -d tu_base_datos

# Verificar tablas
psql -h localhost -U tu_usuario -d tu_base_datos -c "\dt"

# Hacer respaldo
pg_dump -h localhost -U tu_usuario tu_base_datos > backup_$(date +%Y%m%d).sql
```

---

## 🔒 Seguridad

### Recomendaciones de Producción

1. **Cambiar contraseñas por defecto** inmediatamente después del despliegue
2. **Configurar HTTPS con certificado SSL** (mediante Ansible/proxy)
3. **Configurar respaldos automáticos** de la base de datos
4. **Monitorear logs regularmente**
5. **Mantener el sistema actualizado** (imagen base PHP y dependencias)
6. **Restringir acceso a archivos sensibles** (ya configurado en nginx.conf)

### Archivos Sensibles (NO subir a GitHub)
```bash
.env                             # Variables de entorno locales
deployment/config/app.env.local  # Configuración con credenciales reales
```

**Nota**: Los archivos con valores de ejemplo están en el repositorio, pero las credenciales reales deben configurarse en el servidor.

---

## 📊 Monitoreo

### Logs Importantes
```bash
# Logs del contenedor
podman logs becarios-app

# Logs de nginx (dentro del contenedor)
podman exec becarios-app tail -f /var/log/nginx/access.log
podman exec becarios-app tail -f /var/log/nginx/error.log

# Logs de la aplicación
podman exec becarios-app tail -f /var/www/html/logs/
```

### Métricas de Rendimiento
```bash
# Uso de recursos del contenedor
podman stats becarios-app

# Espacio en disco
podman system df
podman volume ls

# Estado de volúmenes
podman volume inspect fotos_becarios
podman volume inspect logs_app
```

---

## ✅ Lista de Verificación Post-Despliegue

- [ ] Contenedor iniciado correctamente (`podman ps`)
- [ ] Conexión a PostgreSQL funcionando
- [ ] Aplicación web accesible a través del dominio configurado
- [ ] Login de administrador funcionando
- [ ] Contraseñas por defecto cambiadas
- [ ] Registro de entrada/salida funcionando
- [ ] Fotos de becarios se pueden subir
- [ ] Exportación a Excel funciona
- [ ] Cálculo de horas trabajadas correcto
- [ ] Respaldos de base de datos configurados
- [ ] Logs monitoreándose
- [ ] SSL configurado (mediante Ansible/proxy)

---

## 📞 Soporte

Para soporte técnico:
1. Verificar logs según esta guía (`podman logs becarios-app`)
2. Revisar sección de solución de problemas
3. Contactar al equipo de desarrollo con:
   - Logs específicos del error
   - Configuración de variables de entorno (sin credenciales)
   - Pasos para reproducir el problema

---

## 🔄 Actualización del Sistema

```bash
# 1. En el servidor, obtener cambios del repositorio
git pull origin main

# 2. Reconstruir la imagen
podman-compose down
podman-compose build --no-cache

# 3. Iniciar con la nueva versión
podman-compose up -d

# 4. Verificar que todo funciona
podman-compose logs -f
```

---

**Sistema de Becarios UFPS v2.0**
**Compatible con:** PostgreSQL 12+, Podman 3.0+, Nginx
**Licencia:** Uso interno UFPS