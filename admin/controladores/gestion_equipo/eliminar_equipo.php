<?php
include "../../../modelo/conexion.php";

$id = $_POST['id'];

// Verifica si el equipo está ocupado
$checkQuery = "SELECT estado FROM equipo WHERE id = ?";
$checkStmt = $conexion->prepare($checkQuery);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$equipo = $result->fetch_assoc();

if ($equipo['estado'] == 'ocupado') {
    echo json_encode(['success' => false, 'message' => 'No se puede eliminar un equipo que está actualmente ocupado']);
} else {
    // Si no está ocupado, procede con la eliminación
    $query = "DELETE FROM equipo WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el equipo: ' . $conexion->error]);
    }
}
?>