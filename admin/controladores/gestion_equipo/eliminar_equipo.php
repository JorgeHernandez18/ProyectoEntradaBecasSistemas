<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
include "../../../modelo/conexion.php";

$id = $_POST['id'];

// Verifica si el equipo está ocupado
$checkQuery = "SELECT estado FROM becl_equipo WHERE id = ?";
$checkStmt = $conexion->prepare($checkQuery);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$equipo = $result->fetch_assoc();

if ($equipo['estado'] == 'ocupado') {
    echo json_encode(['success' => false, 'message' => 'No se puede eliminar un equipo que está actualmente ocupado']);
} else {
    // Si no está ocupado, procede con la eliminación
    $query = "DELETE FROM becl_equipo WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el equipo: ' . $conexion->error]);
    }
}
?>