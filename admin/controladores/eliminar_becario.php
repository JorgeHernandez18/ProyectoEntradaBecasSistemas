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
        
        // Verificar si el becario existe
        $queryVerificar = "SELECT codigo, foto FROM becarios_info WHERE codigo = ?";
        $stmtVerificar = $conexion->prepare($queryVerificar);
        $stmtVerificar->bind_param("s", $codigo);
        $stmtVerificar->execute();
        $resultado = $stmtVerificar->get_result();
        
        if ($resultado->num_rows == 0) {
            throw new Exception("Becario no encontrado");
        }
        
        $becario = $resultado->fetch_assoc();
        
        // Iniciar transacción
        $conexion->autocommit(FALSE);
        
        // Eliminar registros de entrada/salida del becario
        $queryRegistros = "DELETE FROM becarios_registro WHERE codigo = ?";
        $stmtRegistros = $conexion->prepare($queryRegistros);
        $stmtRegistros->bind_param("s", $codigo);
        
        if (!$stmtRegistros->execute()) {
            throw new Exception("Error al eliminar los registros del becario");
        }
        
        // Eliminar información del becario
        $queryBecario = "DELETE FROM becarios_info WHERE codigo = ?";
        $stmtBecario = $conexion->prepare($queryBecario);
        $stmtBecario->bind_param("s", $codigo);
        
        if (!$stmtBecario->execute()) {
            throw new Exception("Error al eliminar la información del becario");
        }
        
        // Eliminar foto si existe
        if (!empty($becario['foto']) && file_exists('../assets/fotos_becarios/' . $becario['foto'])) {
            unlink('../assets/fotos_becarios/' . $becario['foto']);
        }
        
        // Confirmar transacción
        $conexion->commit();
        $conexion->autocommit(TRUE);
        
        echo json_encode([
            'success' => true,
            'message' => 'Becario eliminado exitosamente'
        ]);
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conexion->rollback();
        $conexion->autocommit(TRUE);
        
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