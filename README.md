# Sistema de Registro de Becarios - Ingeniería de Sistemas UFPS

![versión](https://img.shields.io/badge/versión-2.0.0-blue.svg)

## Descripción General

El Sistema de Registro de Becarios es una solución integral para gestionar y realizar seguimiento de las entradas y salidas de becarios del programa de Ingeniería de Sistemas de la UFPS. Este sistema proporciona una forma eficiente de registrar las horas trabajadas, gestionar la información de becarios y generar reportes estadísticos de seguimiento.

## Características Principales

1. **Autenticación de Usuarios**
   - Interfaz de inicio de sesión segura con dos niveles de acceso
   - Usuario administrador: Acceso completo al panel administrativo
   - Usuario becario: Acceso únicamente al formulario de registro de entrada/salida

2. **Panel de Registro de Entrada/Salida**
   - Visualización de reloj en tiempo real con fecha actual
   - Contador de registros del día actual
   - Registro rápido mediante código de becario
   - Sistema de entrada y salida automática con cálculo de horas trabajadas
   - Visualización inmediata de la información del becario registrado

3. **Gestión Completa de Becarios**
   - **Crear becarios**: Formulario completo con foto, datos personales y académicos
   - **Editar becarios**: Modificación de información incluyendo actualización de fotos
   - **Eliminar becarios**: Eliminación segura con confirmación (incluye registros y fotos)
   - **Carga masiva**: Importación de becarios desde archivos CSV/Excel
   - **Gestión de fotos**: Subida, visualización y eliminación de fotos de perfil

4. **Panel de Administración**
   - Vista general con estadísticas de uso del sistema
   - Representaciones visuales de datos a través de gráficos:
     - Registros diarios, semanales y mensuales
     - Estadísticas de horas trabajadas por período
     - Análisis de patrones de entrada/salida
   - Métricas en tiempo real:
     - Total de becarios activos
     - Registros del día/semana/mes
     - Horas trabajadas por semestre

5. **Sistema de Registros Detallados**
   - Vista tabular paginada de todos los registros
   - Filtrado avanzado por fechas y búsqueda
   - Fotos de perfil integradas en los listados
   - Exportación de datos a Excel
   - Historial completo de entrada/salida con horas calculadas

## Funcionalidades Técnicas

### Gestión de Archivos
- **Carga masiva CSV**: Formato estándar con validaciones de datos
- **Gestión de fotos**: Subida, redimensionamiento y validación de imágenes (JPG, PNG, GIF)
- **Exportación Excel**: Reportes completos con filtros personalizables

### Base de Datos
- **Estructura optimizada**: Tablas relacionales para becarios, registros y administradores
- **Integridad referencial**: Claves foráneas y validaciones de consistencia
- **Cálculo automático**: Horas trabajadas basadas en timestamps de entrada/salida

### Seguridad
- **Autenticación segura**: Passwords hasheados con bcrypt
- **Niveles de acceso**: Separación clara entre usuarios becarios y administradores
- **Validación de archivos**: Tipo, tamaño y contenido de uploads
- **Transacciones**: Operaciones de base de datos seguras con rollback

## Usuarios del Sistema

### Usuario Administrador
- **Credenciales**: `admin` / `password`
- **Acceso completo**: Dashboard, gestión de becarios, reportes
- **Funciones**: CRUD completo de becarios, análisis estadístico, exportación de datos

### Usuario Becario
- **Credenciales**: `becario` / `entrada123`
- **Acceso limitado**: Solo formulario de registro entrada/salida
- **Función**: Marcar entrada y salida para control de horas trabajadas


## Instalación y Configuración

### Requisitos del Sistema
- **Servidor Web**: Apache o Nginx
- **PHP**: Versión 7.4 o superior
- **Base de Datos**: MySQL 5.7+ o MariaDB 10.2+
- **Extensiones PHP**: mysqli, fileinfo, gd (para manejo de imágenes)

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/JorgeHernandez18/ProyectoEntradaBecasSistemas.git
   cd ProyectoEntradaBecasSistemas
   ```

2. **Configurar base de datos**
   - Crear base de datos MySQL: `becarios_sistemas`
   - Importar estructura inicial (archivo SQL proporcionado por el administrador)
   - Configurar conexión en `modelo/conexion.php`

3. **Configurar permisos**
   ```bash
   chmod 755 admin/assets/fotos_becarios/
   chmod 644 -R admin/assets/
   ```

4. **Acceder al sistema**
   - Navegar a la URL del proyecto en tu servidor web
   - Usar credenciales por defecto para primer acceso

### Estructura del Proyecto
```
├── admin/                  # Panel administrativo
├── controladores/         # Lógica de negocio
├── modelo/               # Conexión y configuración de BD
├── vistas/               # Interfaces de usuario
└── README.md            # Documentación
```

## Tecnologías Utilizadas

### Backend
- **PHP**: Lógica del servidor y procesamiento de datos
- **MySQL/MySQLi**: Base de datos relacional y conectividad
- **Sessions**: Manejo de autenticación y estado de usuario

### Frontend
- **HTML5**: Estructura semántica de páginas web
- **CSS3**: Diseño responsive y estilización avanzada
- **JavaScript (ES6+)**: Interactividad del lado del cliente
- **Bootstrap 5**: Framework CSS para interfaces responsivas
- **Material Dashboard**: Template profesional para panel administrativo

### Librerías y Herramientas
- **Chart.js**: Gráficos interactivos para estadísticas
- **Font Awesome**: Iconografía moderna
- **Material Icons**: Iconos de Google Material Design
- **DataTables**: Tablas dinámicas con paginación y filtros

### Características Técnicas
- **Responsive Design**: Adaptable a dispositivos móviles y desktop
- **AJAX**: Comunicación asíncrona para mejor UX
- **File Upload**: Manejo seguro de archivos e imágenes
- **CSV Processing**: Importación masiva de datos
- **Session Security**: Protección contra ataques comunes

## Contribuciones y Desarrollo

### Basado en
Este proyecto es una adaptación del sistema BECL (Biblioteca Eduardo Cote Lamus) para el contexto específico de becarios de Ingeniería de Sistemas.

### Desarrolladores
- **Proyecto Original**: endersonjoellg@ufps.edu.co
- **Adaptación Becarios**: Jorgekevinhl@ufps.edu.co

### Repositorio
- **URL**: [https://github.com/JorgeHernandez18/ProyectoEntradaBecasSistemas](https://github.com/JorgeHernandez18/ProyectoEntradaBecasSistemas)
- **Licencia**: MIT License

## Contacto

Para soporte técnico o consultas sobre el sistema:
- **Email**: Jorgekevinhl@ufps.edu.co
- **Programa**: Ingeniería de Sistemas UFPS
- **Sitio Web**: [https://ingsistemas.cloud.ufps.edu.co/](https://ingsistemas.cloud.ufps.edu.co/)
