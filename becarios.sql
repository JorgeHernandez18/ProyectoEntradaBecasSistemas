-- Base de datos para Sistema de Registro de Becarios - Ingeniería de Sistemas
-- Creado: 2025

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Base de datos: becarios_sistemas
CREATE DATABASE IF NOT EXISTS becarios_sistemas;
USE becarios_sistemas;

-- Tabla de administradores del sistema
CREATE TABLE becarios_admin (
  id int(11) NOT NULL AUTO_INCREMENT,
  usuario varchar(50) NOT NULL,
  password varchar(255) NOT NULL,
  nivel varchar(20) NOT NULL DEFAULT 'admin',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuario administrador por defecto
INSERT INTO becarios_admin (usuario, password, nivel) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); -- password

-- Tabla principal de registro de becarios
CREATE TABLE becarios_registro (
  id int(11) NOT NULL AUTO_INCREMENT,
  nombre varchar(100) NOT NULL,
  correo varchar(100) NOT NULL,
  codigo varchar(20) NOT NULL,
  entrada datetime NOT NULL,
  salida datetime DEFAULT NULL,
  horas_trabajadas decimal(4,2) DEFAULT NULL,
  observaciones text DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_codigo (codigo),
  KEY idx_fecha (entrada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de información de becarios
CREATE TABLE becarios_info (
  codigo varchar(20) NOT NULL,
  nombre_completo varchar(100) NOT NULL,
  correo varchar(100) NOT NULL,
  telefono varchar(20) DEFAULT NULL,
  semestre int(2) DEFAULT NULL,
  horas_semanales int(2) DEFAULT 20,
  fecha_inicio date NOT NULL,
  fecha_fin date DEFAULT NULL,
  estado enum('activo','inactivo','finalizado') DEFAULT 'activo',
  foto varchar(255) DEFAULT NULL,
  PRIMARY KEY (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de ejemplo
INSERT INTO becarios_info (codigo, nombre_completo, correo, telefono, semestre, fecha_inicio, estado) VALUES
('1151234', 'Juan Carlos Pérez López', 'juan.perez@ufps.edu.co', '3001234567', 8, '2025-01-15', 'activo'),
('1157890', 'María Fernanda García Ruiz', 'maria.garcia@ufps.edu.co', '3009876543', 6, '2025-01-15', 'activo'),
('1155678', 'Carlos Andrés Rodríguez Sánchez', 'carlos.rodriguez@ufps.edu.co', '3001112233', 7, '2025-01-15', 'activo');

-- Ejemplos de registros
INSERT INTO becarios_registro (nombre, correo, codigo, entrada, salida, horas_trabajadas) VALUES
('Juan Carlos Pérez López', 'juan.perez@ufps.edu.co', '1151234', '2025-01-20 08:00:00', '2025-01-20 12:00:00', 4.00),
('María Fernanda García Ruiz', 'maria.garcia@ufps.edu.co', '1157890', '2025-01-20 14:00:00', '2025-01-20 18:00:00', 4.00),
('Juan Carlos Pérez López', 'juan.perez@ufps.edu.co', '1151234', '2025-01-21 08:30:00', '2025-01-21 12:30:00', 4.00);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;