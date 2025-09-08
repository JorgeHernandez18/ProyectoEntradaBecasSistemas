<?php
// Script para agregar el campo salida_automatica a la tabla becarios_registro
include_once "../modelo/conexion.php";

try {
    echo "Iniciando actualización de base de datos...\n";
    
    // Verificar si el campo ya existe
    $checkQuery = "SELECT COLUMN_NAME 
                   FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_NAME = 'becarios_registro' 
                   AND COLUMN_NAME = 'salida_automatica'";
    
    $result = $conexion->query($checkQuery);
    
    if ($result->num_rows > 0) {
        echo "El campo 'salida_automatica' ya existe en la tabla 'becarios_registro'.\n";
    } else {
        // Agregar el campo
        $alterQuery = "ALTER TABLE becarios_registro 
                       ADD COLUMN salida_automatica BOOLEAN DEFAULT FALSE";
        
        if ($conexion->query($alterQuery)) {
            echo "Campo 'salida_automatica' agregado exitosamente a la tabla 'becarios_registro'.\n";
        } else {
            throw new Exception("Error al agregar el campo: " . $conexion->error);
        }
    }
    
    // También crear la carpeta de logs si no existe
    $logsDir = "../logs";
    if (!is_dir($logsDir)) {
        if (mkdir($logsDir, 0755, true)) {
            echo "Carpeta 'logs' creada exitosamente.\n";
        } else {
            echo "Advertencia: No se pudo crear la carpeta 'logs'. Verifique los permisos.\n";
        }
    } else {
        echo "La carpeta 'logs' ya existe.\n";
    }
    
    echo "Actualización completada exitosamente.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>