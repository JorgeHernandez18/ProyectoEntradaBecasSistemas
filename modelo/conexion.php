<?php
/**
 * Archivo de conexión para desarrollo local
 * En Docker se reemplaza por enlace simbólico a conexion_docker.php
 */

// Configuración para base de datos externa PostgreSQL
$config = parse_ini_file( __DIR__ . '/../.env');

$host = $config['DB_HOST'];
$port = $config['DB_PORT'];
$dbname = $config['DB_NAME'];
$user = $config['DB_USER'];
$password = $config['DB_PASS'];

try {
    // Crear conexión PDO para PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $conexion_pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    // Para compatibilidad con código existente, crear objeto que simule mysqli
    if (!class_exists('PostgreSQLAdapter')) {
    class PostgreSQLAdapter {
        private $pdo;
        
        public function __construct($pdo) {
            $this->pdo = $pdo;
        }
        
        public function prepare($query) {
            // Convertir sintaxis MySQL a PostgreSQL
            $query = $this->convertMySQLToPostgreSQL($query);
            return new PostgreSQLStatement($this->pdo->prepare($query));
        }
        
        public function query($query) {
            $query = $this->convertMySQLToPostgreSQL($query);
            return $this->pdo->query($query);
        }
        
        public function real_escape_string($string) {
            return $this->pdo->quote($string);
        }
        
        public function autocommit($mode) {
            if (!$mode) {
                $this->pdo->beginTransaction();
            }
        }
        
        public function commit() {
            return $this->pdo->commit();
        }
        
        public function rollback() {
            return $this->pdo->rollback();
        }
        
        private function convertMySQLToPostgreSQL($query) {
            // Conversiones básicas de sintaxis
            $query = str_replace('`', '"', $query); // Comillas para nombres de columnas
            $query = preg_replace('/AUTO_INCREMENT/i', '', $query);
            $query = preg_replace('/INT\([\d]+\)/i', 'INTEGER', $query);
            $query = preg_replace('/DATETIME/i', 'TIMESTAMP', $query);
            $query = preg_replace('/ENGINE=\w+/i', '', $query);
            $query = preg_replace('/DEFAULT CHARSET=\w+/i', '', $query);
            $query = preg_replace('/COLLATE=\w+/i', '', $query);
            
            // Conversión de LIMIT offset, count a LIMIT count OFFSET offset
            $query = preg_replace('/LIMIT\s+(\?),\s*(\?)/i', 'LIMIT $2 OFFSET $1', $query);
            $query = preg_replace('/LIMIT\s+(\d+),\s*(\d+)/i', 'LIMIT $2 OFFSET $1', $query);
            
            return $query;
        }
    }
    }
    
    if (!class_exists('PostgreSQLStatement')) {
    class PostgreSQLStatement {
        private $stmt;
        private $result;
        
        public function __construct($stmt) {
            $this->stmt = $stmt;
        }
        
        public function bind_param($types, ...$params) {
            for ($i = 0; $i < count($params); $i++) {
                $this->stmt->bindValue($i + 1, $params[$i]);
            }
        }
        
        public function execute() {
            $result = $this->stmt->execute();
            $this->result = $this->stmt;
            return $result;
        }
        
        public function get_result() {
            return new PostgreSQLResult($this->result);
        }
        
        public function close() {
            // PDO no necesita cierre explícito, pero lo agregamos para compatibilidad
            $this->stmt = null;
            return true;
        }
        
        public $error;
    }
    }
    
    if (!class_exists('PostgreSQLResult')) {
    class PostgreSQLResult {
        private $stmt;
        private $data;
        private $index = 0;
        
        public function __construct($stmt) {
            $this->stmt = $stmt;
            $this->data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->num_rows = count($this->data);
        }
        
        public $num_rows;
        
        public function fetch_assoc() {
            if ($this->index < count($this->data)) {
                return $this->data[$this->index++];
            }
            return null;
        }
        
        public function reset() {
            $this->index = 0;
        }
        
        public function __get($name) {
            if ($name === 'num_rows') {
                return $this->num_rows;
            }
        }
    }
    }
    
    // mysqli_fetch_array será manejado por el adaptador
    
    $conexion = new PostgreSQLAdapter($conexion_pdo);
    
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>