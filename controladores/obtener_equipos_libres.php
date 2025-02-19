<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

include "../modelo/conexion.php";

if ($conexion->connect_error) {
    error_log("Error de conexión: " . $conexion->connect_error);
    die(json_encode(['error' => 'Error de conexión: ' . $conexion->connect_error]));
}

$query = "SELECT equipo FROM becl_equipo WHERE estado = 'libre' ORDER BY equipo";
error_log("Ejecutando query: " . $query);

$resultado = $conexion->query($query);

if ($resultado === false) {
    error_log("Error en la consulta: " . $conexion->error);
    die(json_encode(['error' => 'Error en la consulta: ' . $conexion->error]));
}

$equiposLibres = [];
while ($row = $resultado->fetch_assoc()) {
    // Asegurarse de que equipo es tratado como un entero
    $equiposLibres[] = intval($row['equipo']);
}

error_log("Equipos libres encontrados: " . json_encode($equiposLibres));
echo json_encode(['equiposLibres' => $equiposLibres]);

$conexion->close();
?>
