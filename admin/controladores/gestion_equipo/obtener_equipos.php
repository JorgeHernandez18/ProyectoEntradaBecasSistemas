<?php
include "../../../modelo/conexion.php";

$query = "SELECT e.id, e.equipo, e.estado
          FROM equipo e 
          ORDER BY e.equipo";
$resultado = $conexion->query($query);

$equipos = array();
while ($row = $resultado->fetch_assoc()) {
    $equipos[] = [
        'id' => $row['id'],
        'equipo' => $row['equipo'],
        'estado' => $row['estado'] == 'ocupado' ? true : false
    ];
}

echo json_encode($equipos);
?>