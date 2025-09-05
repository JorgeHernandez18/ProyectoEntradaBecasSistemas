-- Script para agregar usuario con nivel "entrada"
-- Este usuario solo podrá acceder al formulario de registro

USE becarios_sistemas;

-- Insertar usuario para registro de entradas
-- Usuario: becario
-- Password: entrada123
-- Nivel: entrada
INSERT INTO becarios_admin (usuario, password, nivel) VALUES
('becario', '$2y$12$xO8.4bnVrrZO2BAUvZmFv.q3NjhCNYNFcyoRw13JWW9l5cfvsiaC6', 'entrada');

-- Nota: La contraseña 'entrada123' está hasheada con bcrypt
-- Puedes usar este usuario para acceder solo al formulario de registro