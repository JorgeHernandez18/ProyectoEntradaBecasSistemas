# Guía de Despliegue - Sistema de Becarios

## Despliegue con Podman Compose (Usando PostgreSQL y Adminer Existentes)

### Prerequisitos
- Podman y Podman Compose instalados en el servidor
- PostgreSQL Server instalado y corriendo en el servidor host
- Adminer instalado para gestión de base de datos
- Acceso a los puertos configurados en el servidor

### Pasos para el Despliegue

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/JorgeHernandez18/ProyectoEntradaBecasSistemas.git
   cd ProyectoEntradaBecasSistemas
   ```

2. **Configurar base de datos en PostgreSQL del servidor**
   ```bash
   psql -U postgres -f setup-database.sql
   ```

3. **Configurar variables de entorno**
   ```bash
   cp .env.example .env
   # Editar .env con la configuración de tu servidor
   nano .env
   ```
   
   Configurar en `.env`:
   ```
   PORT_0=tu_puerto_asignado
   DB_HOST=localhost
   DB_NAME=becarios_sistemas
   DB_USER=becarios_user
   DB_PASSWORD=becarios_pass_2025
   DB_PORT=5432
   DB_TYPE=postgresql
   ```

4. **Configurar archivo de conexión**
   ```bash
   # Reemplazar el archivo de conexión MySQL por PostgreSQL
   cp modelo/conexion_postgresql.php modelo/conexion.php
   ```

5. **Crear directorio para fotos**
   ```bash
   mkdir -p admin/assets/fotos_becarios
   chmod 777 admin/assets/fotos_becarios
   ```

6. **Verificar conexión a base de datos**
   La aplicación usará `host.containers.internal` para conectarse al PostgreSQL del servidor host.

6. **Iniciar servicios**
   ```bash
   podman-compose up -d
   ```

7. **Verificar estado**
   ```bash
   podman-compose ps
   podman-compose logs web
   ```

### Servicios Disponibles

| Servicio | Puerto | Descripción |
|----------|---------|-------------|
| Web App | `${PORT_0}` | Aplicación principal |
| PostgreSQL | 5432 (host) | Base de datos del servidor |
| Adminer | (existente) | Gestión de BD del servidor |

### Credenciales por Defecto

**Aplicación Web:**
- Admin: `admin` / `password`
- Becario: `becario` / `entrada123`

**Base de Datos PostgreSQL (servidor host):**
- Usuario aplicación: `becarios_user` / `becarios_pass_2025`
- Base de datos: `becarios_sistemas`

### Comandos Útiles

```bash
# Ver logs
podman-compose logs -f web
podman-compose logs -f database

# Reiniciar servicios
podman-compose restart

# Detener servicios
podman-compose down

# Actualizar aplicación
git pull
podman-compose up -d --build web

# Backup de base de datos (usando PostgreSQL del host)
pg_dump -U becarios_user -h localhost becarios_sistemas > backup.sql

# Restaurar base de datos
psql -U becarios_user -h localhost becarios_sistemas < backup.sql
```

### Resolución de Problemas

1. **Error de permisos en fotos:**
   ```bash
   podman exec becarios_web chmod -R 777 /var/www/html/admin/assets/fotos_becarios
   ```

2. **Error de conexión a BD:**
   - Verificar que PostgreSQL del host esté ejecutándose: `systemctl status postgresql`
   - Verificar que el usuario `becarios_user` tenga permisos
   - Revisar logs del contenedor: `podman-compose logs web`
   - Verificar conectividad: `psql -U becarios_user -h localhost becarios_sistemas`

3. **Módulos PHP faltantes:**
   ```bash
   podman exec becarios_web php -m | grep -E "(pdo_pgsql|gd)"
   ```

### Seguridad en Producción

- Cambiar todas las contraseñas por defecto
- Configurar SSL/HTTPS
- Restringir acceso a phpMyAdmin
- Configurar backups automáticos
- Monitorear logs regularmente