<?php
session_start();
include "seguridad.php";
include "../../modelo/conexion.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $codigo = $input['codigo'] ?? null;
        
        if (empty($codigo)) {
            throw new Exception("Código de becario no proporcionado");
        }
        
        // Obtener foto actual del becario
        $query = "SELECT foto FROM becarios_info WHERE codigo = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows == 0) {
            throw new Exception("Becario no encontrado");
        }
        
        $becario = $resultado->fetch_assoc();
        $fotoActual = $becario['foto'];
        
        // Eliminar archivo físico si existe
        if ($fotoActual && file_exists('../assets/fotos_becarios/' . $fotoActual)) {
            if (!unlink('../assets/fotos_becarios/' . $fotoActual)) {
                throw new Exception("Error al eliminar el archivo de foto");
            }
        }
        
        // Actualizar base de datos para remover referencia a la foto
        $queryUpdate = "UPDATE becarios_info SET foto = NULL WHERE codigo = ?";
        $stmtUpdate = $conexion->prepare($queryUpdate);
        $stmtUpdate->bind_param("s", $codigo);
        
        if ($stmtUpdate->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Foto eliminada exitosamente'
            ]);
        } else {
            throw new Exception("Error al actualizar la base de datos");
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