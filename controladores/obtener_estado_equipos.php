<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
include "../modelo/conexion.php";

if ($conexion->connect_error) {
    die(json_encode(['error' => 'Error de conexión: ' . $conexion->connect_error]));
}

$query = "SELECT e.equipo, r.codigo 
          FROM becl_equipo e 
          LEFT JOIN becl_registro_computo r ON e.equipo = r.equipo AND r.salida IS NULL
          WHERE e.estado = 'ocupado'
          ORDER BY e.equipo";

$resultado = $conexion->query($query);

if ($resultado === false) {
    die(json_encode(['error' => 'Error en la consulta: ' . $conexion->error]));
}

$equiposOcupados = [];
while ($row = $resultado->fetch_assoc()) {
    // Convertir 'equipo' a entero para asegurarse de que el tipo de dato sea correcto
    $row['equipo'] = intval($row['equipo']);
    $equiposOcupados[] = $row;
}

echo json_encode(['equiposOcupados' => $equiposOcupados]);

$conexion->close();
?>