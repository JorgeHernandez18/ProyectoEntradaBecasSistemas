<?php
include "../modelo/conexion.php";

if ($conexion->connect_error) {
    die(json_encode(['error' => 'Error de conexión: ' . $conexion->connect_error]));
}

$query = "SELECT equipo FROM equipo WHERE estado = 'libre' ORDER BY equipo";

$resultado = $conexion->query($query);

if ($resultado === false) {
    die(json_encode(['error' => 'Error en la consulta: ' . $conexion->error]));
}

$equiposLibres = [];
while ($row = $resultado->fetch_assoc()) {
    $equiposLibres[] = $row['equipo'];
}

echo json_encode(['equiposLibres' => $equiposLibres]);

$conexion->close();
?>
