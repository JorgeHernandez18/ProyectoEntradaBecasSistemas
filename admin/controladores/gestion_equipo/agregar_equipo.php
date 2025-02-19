<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
include "../../../modelo/conexion.php";

$numero = $_POST['numero'];
$estado = 'libre'; // Establecemos el estado por defecto como 'libre'

// Verificar si el equipo ya existe
$checkQuery = "SELECT * FROM becl_equipo WHERE equipo = ?";
$checkStmt = $conexion->prepare($checkQuery);
$checkStmt->bind_param("s", $numero);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'El número de equipo ya existe']);
} else {
    // Modificamos la consulta para incluir el campo 'estado'
    $insertQuery = "INSERT INTO becl_equipo (equipo, estado) VALUES (?, ?)";
    $insertStmt = $conexion->prepare($insertQuery);
    $insertStmt->bind_param("ss", $numero, $estado);

    if ($insertStmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conexion->error]);
    }
}
?>