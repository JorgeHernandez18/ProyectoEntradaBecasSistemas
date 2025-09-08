<?php
// Configuración de conexión a PostgreSQL
$host = 'host.containers.internal';  // o host.containers.internal en contenedor
$port = '5432';
$dbname = 'becarios_sistemas';
$user = 'becario';
$password = 'becarios';

try {
    // Crear conexión PDO para PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $conexion_pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    // Para compatibilidad con código existente, crear objeto que simule mysqli
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
            
            return $query;
        }
    }
    
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
        
        public $error;
    }
    
    class PostgreSQLResult {
        private $stmt;
        private $data;
        private $index = 0;
        
        public function __construct($stmt) {
            $this->stmt = $stmt;
            $this->data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        public $num_rows;
        
        public function fetch_assoc() {
            if ($this->index < count($this->data)) {
                return $this->data[$this->index++];
            }
            return null;
        }
        
        public function __get($name) {
            if ($name === 'num_rows') {
                return count($this->data);
            }
        }
    }
    
    // Función para simular mysqli_fetch_array
    function mysqli_fetch_array($result) {
        return $result->fetch_assoc();
    }
    
    $conexion = new PostgreSQLAdapter($conexion_pdo);
    
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>