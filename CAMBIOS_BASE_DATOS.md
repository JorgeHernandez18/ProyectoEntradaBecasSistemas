# Cambios Realizados - Adaptación a Nueva Base de Datos PostgreSQL

## Fecha de Actualización
12 de Noviembre de 2025

## Resumen de Cambios

Este documento detalla todos los cambios realizados para adaptar el sistema de becarios de Ingeniería de Sistemas UFPS a la nueva base de datos externa PostgreSQL.

---

## 1. Conexión a Base de Datos

### Archivo: `modelo/conexion.php`

**Nota**: El archivo ya tenía soporte para PostgreSQL con PDO y adaptadores de compatibilidad.

---

## 2. Cambios en Estructura de Tablas

### Mapeo de Tablas

| Tabla Antigua (MySQL) | Tabla Nueva (PostgreSQL) | Cambios |
|----------------------|--------------------------|---------|
| `becarios_admin` | `admin` | Campo `password` → `clave`, eliminado campo `nivel` |
| `becarios_info` | `usuarios` + `estado` | Información dividida en dos tablas |
| `becarios_registro` | `registros` | Estructura completamente rediseñada |

### Tabla `registros` - Cambio Crítico

**ANTES (MySQL):**
```sql
Una fila por día con entrada y salida:
id | codigo | nombre | entrada (DATETIME) | salida (DATETIME) | horas_trabajadas
1  | 1152281 | Juan  | 2025-09-12 08:00  | 2025-09-12 17:00 | 9.0
```

**AHORA (PostgreSQL):**
```sql
Dos filas separadas por transacción:
id | codigo | nombre | tipo | fecha | hora | actividad
1  | 1152281 | Juan  | Ingreso | 2025-09-12 | 08:00:00 |
2  | 1152281 | Juan  | Salida  | 2025-09-12 | 17:00:00 | Trabajo realizado
```

**Impacto**: Todas las consultas deben emparejar registros de "Ingreso" con "Salida" para calcular horas trabajadas.

---

## 3. Archivos Actualizados

### 3.1 Sistema de Autenticación

#### `controladores/check.php`
- Actualizado para usar tabla `admin` en lugar de `becarios_admin`
- Campo `password` → `clave`
- Soporte para contraseñas en texto plano y bcrypt (compatibilidad)
- Redirección: `registro.php` → `admin/pages/dashboard.php`
- Todos los usuarios de tabla `admin` tienen nivel 'admin' (no hay campo nivel en la BD)

### 3.2 Dashboard

#### `admin/controladores/consultas_graficas.php`
- **Tabla**: `becarios_registro` → `registros`
- **Total registros**: Cuenta solo registros de tipo "Ingreso" para evitar duplicados
- **Registros del día**: Filtro por `fecha = CURRENT_DATE` y `tipo = 'Ingreso'`
- **Cálculo de horas trabajadas**: Implementado emparejamiento de registros Ingreso/Salida:
  ```sql
  EXTRACT(EPOCH FROM (
      (salida.fecha || ' ' || salida.hora)::timestamp -
      (ingreso.fecha || ' ' || ingreso.hora)::timestamp
  )) / 3600
  ```
- **Becarios más activos**: Agrupa por código y nombre, cuenta solo ingresos
- **Horas mensuales**: Suma horas calculadas agrupadas por mes
- **Horas semanales**: Suma horas calculadas agrupadas por día de semana

### 3.3 Visualización de Registros

#### `admin/controladores/filtro_paginacion.php`
- **JOIN**: Empareja registros de Ingreso con Salida del mismo día
- **Condiciones**:
  - `ingreso.tipo = 'Ingreso'`
  - `salida.tipo = 'Salida'`
  - `ingreso.fecha = salida.fecha`
  - `ingreso.codigo = salida.codigo`
  - `salida.id > ingreso.id` (para evitar duplicados)
- **LEFT JOIN con usuarios**: Para obtener información adicional del becario
- **Cálculo de horas**: En tiempo real usando EXTRACT(EPOCH FROM ...)
- **Nueva columna**: Muestra el campo `actividad` (descripción del trabajo realizado)

### 3.4 Exportación a Excel

#### `admin/controladores/excel.php`
- Misma lógica de emparejamiento que filtro_paginacion.php
- **Columnas del Excel**:
  1. Nombre
  2. Código
  3. Fecha de Entrada
  4. Hora de Entrada
  5. Fecha de Salida
  6. Hora de Salida
  7. Horas Trabajadas (calculadas)
  8. Actividad (nueva columna)
- Eliminada columna "Correo" (no existe en nueva BD)
- Eliminada columna "Observaciones" → reemplazada por "Actividad"

---

## 4. Campos Eliminados

Los siguientes campos ya NO están disponibles en la nueva base de datos:

### De `becarios_info`:
- ❌ `correo` - Email del becario
- ❌ `telefono` - Teléfono
- ❌ `semestre` - Semestre cursado
- ❌ `horas_semanales` - Horas asignadas por semana
- ❌ `fecha_inicio` - Fecha de inicio de beca
- ❌ `fecha_fin` - Fecha fin de beca
- ❌ `estado` - Estado del becario (activo/inactivo)
- ❌ `foto` - Foto del becario

### De `becarios_registro`:
- ❌ `horas_trabajadas` - Ahora se calcula dinámicamente
- ❌ `observaciones` - Reemplazado por `actividad`
- ❌ `salida_automatica` - Campo de auto-salidas

---

## 5. Nuevos Campos Disponibles

### En tabla `registros`:
- ✅ `tipo` - "Ingreso" o "Salida"
- ✅ `actividad` - Descripción del trabajo realizado (solo en salidas)

### En tabla `estado`:
- ✅ `estado` - Estado genérico del usuario (Ej: "entrada")

---

## 6. Consideraciones Técnicas

### Sintaxis PostgreSQL vs MySQL

**Concatenación de strings:**
- MySQL: `CONCAT(fecha, ' ', hora)`
- PostgreSQL: `fecha || ' ' || hora`

**Conversión a timestamp:**
- MySQL: `STR_TO_DATE()`
- PostgreSQL: `::timestamp`

**Extracción de tiempo:**
- MySQL: `TIMESTAMPDIFF()`
- PostgreSQL: `EXTRACT(EPOCH FROM ...)`

**Día de la semana:**
- MySQL: `DAYOFWEEK()` (1=domingo)
- PostgreSQL: `EXTRACT(DOW FROM ...)` (0=domingo)

**Funciones de fecha:**
- MySQL: `CURDATE()`, `NOW()`
- PostgreSQL: `CURRENT_DATE`, `CURRENT_TIMESTAMP`

---

## 7. Funcionalidades Removidas

Las siguientes funcionalidades fueron eliminadas porque los datos ahora vienen de una base de datos externa:

### ❌ Módulo de Registro Manual
- `vistas/formularios/registro.php` - Formulario de entrada/salida
- `controladores/registro_entrada.php` - Controlador de registro
- `controladores/obtenerContador.php` - Contador de registros

### ❌ Gestión de Becarios (pendiente de actualización)
- Las páginas de agregar/editar/eliminar becarios necesitan actualizarse para usar tabla `usuarios` en lugar de `becarios_info`
- Campos de formulario necesitan ajustarse a la nueva estructura (eliminar campos que ya no existen)

### ❌ Auto-salidas (pendiente de verificación)
- Sistema de salidas automáticas necesita revisión
- Tabla `registros` no tiene campo `salida_automatica`

---

## 8. Credenciales de Acceso

### Base de Datos PostgreSQL
- **Host**: 3.93.129.86
- **Puerto**: 5432
- **Base de datos**: bd_sistemas_becario
- **Usuario**: dptosistemas
- **Contraseña**: sistemas

### Login Admin
- **Usuario**: admin
- **Contraseña**: 1234
- **URL**: http://[tu-servidor]/vistas/formularios/index.php

---

## 9. Pruebas Recomendadas

Antes de poner en producción, verificar:

1. ✅ **Dashboard**
   - [x] Total de registros se muestra correctamente
   - [x] Registros del día actualizados
   - [x] Gráfica de becarios más activos
   - [x] Gráfica de horas mensuales
   - [x] Gráfica de horas semanales

2. ⏳ **Registros**
   - [ ] Lista de registros con paginación
   - [ ] Filtro por fecha
   - [ ] Búsqueda por nombre/código
   - [ ] Exportación a Excel

3. ⏳ **Autenticación**
   - [ ] Login con usuario admin
   - [ ] Redirección correcta al dashboard
   - [ ] Cierre de sesión

4. ⚠️ **Pendientes de Actualización**
   - [ ] Gestión de becarios (agregar/editar/eliminar)
   - [ ] Perfiles individuales de becarios
   - [ ] Sistema de horarios
   - [ ] Auto-salidas

---

## 10. Próximos Pasos

### Alta Prioridad
1. Actualizar módulo de gestión de becarios para usar tabla `usuarios`
2. Adaptar perfiles individuales (`admin/pages/profile.php`)
3. Verificar sistema de auto-salidas

### Media Prioridad
4. Eliminar código obsoleto del módulo de registro manual
5. Actualizar formularios para remover campos inexistentes
6. Adaptar sistema de horarios (si aplica)

### Baja Prioridad
7. Optimizar consultas de emparejamiento Ingreso/Salida
8. Agregar índices en base de datos para mejorar rendimiento
9. Documentar nuevos procedimientos para administradores

---

## 11. Notas Importantes

### ⚠️ Datos Históricos
- Los registros antiguos en la base de datos anterior NO se migraron automáticamente
- Si necesitas datos históricos, debes migrarlos manualmente ajustando la estructura

### ⚠️ Compatibilidad con Contraseñas
- El sistema soporta temporalmente contraseñas en texto plano para facilitar la transición
- **Recomendación**: Cambiar contraseñas a bcrypt en producción por seguridad

### ⚠️ Permisos de Base de Datos
- Verificar que el usuario `dptosistemas` tenga permisos suficientes
- Permisos necesarios: SELECT, INSERT, UPDATE (si se implementa gestión de usuarios)

---

## 12. Soporte

Para cualquier problema o duda sobre estos cambios:
- Revisar este documento primero
- Verificar logs de error de PHP y PostgreSQL
- Consultar archivo `CLAUDE.md` para arquitectura general del proyecto

---

**Última actualización**: 2025-11-12
**Responsable de cambios**: Claude Code
**Estado del proyecto**: ✅ Dashboard funcional | ⏳ Otras funcionalidades en revisión
