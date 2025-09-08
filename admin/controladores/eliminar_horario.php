<?php
session_start();
include "seguridad.php";
include "../../modelo/conexion.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
        
        if (empty($id)) {
            throw new Exception("ID del horario no proporcionado");
        }
        
        // Verificar si el horario existe
        $queryVerificar = "SELECT id FROM becarios_horarios WHERE id = ?";
        $stmtVerificar = $conexion->prepare($queryVerificar);
        $stmtVerificar->bind_param("i", $id);
        $stmtVerificar->execute();
        $resultado = $stmtVerificar->get_result();
        
        if ($resultado->num_rows == 0) {
            throw new Exception("Horario no encontrado");
        }
        
        // Desactivar el horario en lugar de eliminarlo completamente
        $query = "UPDATE becarios_horarios SET activo = false WHERE id = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Horario eliminado exitosamente'
            ]);
        } else {
            throw new Exception("Error al eliminar el horario");
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>