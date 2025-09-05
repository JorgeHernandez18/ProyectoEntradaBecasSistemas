<?php
session_start();
include "seguridad.php";
include "../../modelo/conexion.php";

header('Content-Type: application/json');

if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    
    try {
        $query = "SELECT * FROM becarios_info WHERE codigo = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $becario = $resultado->fetch_assoc();
            echo json_encode([
                'success' => true,
                'becario' => $becario
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Becario no encontrado'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener datos: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Código no proporcionado'
    ]);
}
?>