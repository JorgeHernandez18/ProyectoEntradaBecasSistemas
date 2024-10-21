<?php
include "../modelo/conexion.php";

if ($conexion->connect_error) {
    die(json_encode(['error' => 'Error de conexión: ' . $conexion->connect_error]));
}

$query = "SELECT e.equipo, r.codigo 
          FROM equipo e 
          LEFT JOIN registro_computo r ON e.equipo = r.equipo AND r.salida IS NULL
          WHERE e.estado = 'ocupado'
          ORDER BY e.equipo";

$resultado = $conexion->query($query);

if ($resultado === false) {
    die(json_encode(['error' => 'Error en la consulta: ' . $conexion->error]));
}

$equiposOcupados = [];
while ($row = $resultado->fetch_assoc()) {
    $equiposOcupados[] = $row;
}

echo json_encode(['equiposOcupados' => $equiposOcupados]);

$conexion->close();
?>