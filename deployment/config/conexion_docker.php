<?php
/**
 * Archivo de conexión PostgreSQL para contenedor Docker
 * Utiliza variables de entorno para la configuración
 * Compatible con el adapter PostgreSQL existente
 */

// Configuración de zona horaria
date_default_timezone_set(getenv('PHP_TIMEZONE') ?: 'America/Bogota');

// Configuración de la base de datos desde variables de entorno
$host = getenv('DB_HOST') ?: 'host.containers.internal';
$port = getenv('DB_PORT') ?: '5432';
$database = getenv('DB_NAME') ?: 'becarios_sistemas';
$username = getenv('DB_USER') ?: 'becario';
$password = getenv('DB_PASSWORD') ?: 'becarios';
$charset = getenv('DB_CHARSET') ?: 'utf8';

// Configuración de errores según entorno
if (getenv('APP_ENV') === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Configuración PHP adicional
ini_set('memory_limit', getenv('PHP_MEMORY_LIMIT') ?: '256M');
ini_set('upload_max_filesize', getenv('PHP_UPLOAD_MAX_FILESIZE') ?: '10M');
ini_set('post_max_size', getenv('PHP_POST_MAX_SIZE') ?: '10M');

// Incluir el adapter PostgreSQL
require_once __DIR__ . '/../../modelo/conexion_postgresql.php';

try {
    // Crear conexión PDO PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$database";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Configurar zona horaria en PostgreSQL
    $pdo->exec("SET timezone = 'America/Bogota'");
    
    // Configurar charset
    $pdo->exec("SET client_encoding = 'UTF8'");
    
    // Crear adapter compatible con MySQLi
    $conexion = new PostgreSQLAdapter($pdo);
    
} catch (PDOException $e) {
    // Log del error
    error_log("Error de conexión a PostgreSQL: " . $e->getMessage());
    
    if (getenv('APP_ENV') !== 'production') {
        die("Error de conexión a base de datos: " . $e->getMessage());
    } else {
        die("Error de conexión a base de datos. Contacte al administrador.");
    }
} catch (Exception $e) {
    // Log del error
    error_log("Error general de conexión: " . $e->getMessage());
    
    if (getenv('APP_ENV') !== 'production') {
        die("Error de conexión a base de datos: " . $e->getMessage());
    } else {
        die("Error de conexión a base de datos. Contacte al administrador.");
    }
}

// Función para logging personalizado
function app_log($message, $level = 'info') {
    $log_path = getenv('LOG_PATH') ?: '/var/log/becarios/';
    
    if (!is_dir($log_path)) {
        mkdir($log_path, 0755, true);
    }
    
    $log_file = $log_path . 'app_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message" . PHP_EOL;
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Log de conexión exitosa
app_log("Conexión a base de datos establecida correctamente - Host: $host, DB: $database");

?>