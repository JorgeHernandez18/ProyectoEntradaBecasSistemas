<?php
// Conexión a la base de datos
include "../modelo/conexion.php";

// Consulta para obtener el número de estudiantes registrados
$query = "SELECT COUNT(*) as total FROM registro WHERE DATE(entrada) = CURDATE()"; 
$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $contador = $row['total'];
    
    // Devolver el contador en formato JSON
    echo json_encode([
        'success' => true,
        'contador' => $contador
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No se pudo obtener el contador'
    ]);
}
?>