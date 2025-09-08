<?php
// Conexión a la base de datos
include_once "../modelo/conexion.php";

try {
    // Consulta para obtener el número de estudiantes registrados (usar CURRENT_DATE para PostgreSQL)
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM becarios_registro WHERE DATE(entrada) = CURRENT_DATE");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $contador = $row['total'] ?? 0;
    
    // Devolver el contador en formato JSON
    echo json_encode([
        'success' => true,
        'contador' => $contador
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'No se pudo obtener el contador: ' . $e->getMessage()
    ]);
}
?>