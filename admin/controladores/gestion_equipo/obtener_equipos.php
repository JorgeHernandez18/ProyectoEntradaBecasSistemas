<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
include "../../../modelo/conexion.php";

$query = "SELECT e.id, e.equipo, e.estado
          FROM becl_equipo e 
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