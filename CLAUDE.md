# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

This is a PHP-based scholarship management system for "Ingeniería de Sistemas" at UFPS. The system tracks scholarship student entry/exit times, calculates worked hours, and provides administrative dashboards with statistics and reports.

## Architecture

### Core Structure
- **MVC Pattern**: Controllers handle business logic, views manage presentation, models handle data
- **Session-based Authentication**: Security handled via `controladores/seguridad.php` and `admin/controladores/seguridad.php`
- **Database Connection**: Centralized in `modelo/conexion.php` using MySQLi
- **Dual Interface**: Scholarship entry/exit registration + admin panel for reports

### Key Directories
- `vistas/formularios/` - Main forms (login, scholarship student registration)
- `admin/pages/` - Administrative interface (dashboard, records management)
- `controladores/` - Business logic for public interface
- `admin/controladores/` - Business logic for admin interface
- `admin/assets/` - CSS/JS assets using Material Dashboard theme

### Database
- Database name: `becarios_sistemas`
- Uses MySQLi with UTF-8 encoding
- SQL dump available in `becarios.sql`
- Default connection: localhost, root user, no password

## Development Setup

### Dependencies
```bash
composer install
```
Main dependency: PHPOffice/PhpSpreadsheet for Excel exports

### Database Setup
```bash
mysql -u root -p < becarios.sql
```

### Local Development
- Requires PHP 8+ and MySQL/MariaDB
- Entry point: `index.php` redirects to `vistas/formularios/index.php`
- Admin access through login (default: admin/password)
- Scholarship students register with their student code

## Key Functionality

### Public Interface
- `vistas/formularios/index.php` - Login page
- `vistas/formularios/registro.php` - Scholarship student entry/exit registration

### Admin Interface
- `admin/pages/dashboard.php` - Statistics and charts (most active students, worked hours)
- `admin/pages/registros.php` - Entry records with filtering/export to Excel
- `admin/pages/funcionarios.php` - Scholarship student management
- `admin/pages/profile.php` - Individual student profiles and hours

### Controllers
- Entry/Exit registration: `controladores/registro_entrada.php` (calculates worked hours automatically)
- Data queries: `admin/controladores/consultas_graficas.php` (dashboard statistics)
- Excel exports: `admin/controladores/excel.php`
- Student filtering: `admin/controladores/filtro_funcionarios.php`

## Database Tables

### Main Tables
- `becarios_admin` - System administrators
- `becarios_info` - Scholarship student information (active status, weekly hours, contact info)
- `becarios_registro` - Entry/exit records with calculated worked hours

### Key Fields
- Entry/exit tracking with automatic hour calculation
- Student status management (active/inactive/finished)
- Weekly hour limits and semester tracking

## Common Development Tasks

### Testing Changes
- Test public interface at `vistas/formularios/index.php`
- Test admin interface through admin login (admin/password)
- Verify database connections in `modelo/conexion.php`
- Test with sample student codes: 1151234, 1157890, 1155678

### Adding New Features
- Follow existing MVC structure
- Place controllers in appropriate directory (public/admin)
- Use existing security patterns from `seguridad.php` files
- Maintain consistent styling with Material Dashboard theme
- All references to "biblioteca" have been changed to "becarios/sistema de becarios"