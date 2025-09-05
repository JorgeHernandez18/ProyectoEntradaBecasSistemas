<?php
// Consulta para obtener TODOS los registros (sin filtrar por mes)
$queryRegistros = "SELECT entrada, salida 
                   FROM becarios_registro 
                   WHERE codigo = ? 
                   ORDER BY entrada ASC";

$stmtRegistros = $conexion->prepare($queryRegistros);
$stmtRegistros->bind_param("s", $cardnumber); // Solo un parámetro ahora
$stmtRegistros->execute();
$resultadoRegistros = $stmtRegistros->get_result();

// Preparar los datos para FullCalendar
$eventos = [];
while ($fila = $resultadoRegistros->fetch_assoc()) {
    $entrada = $fila['entrada'];
    $salida = $fila['salida'];

    // Evento para la entrada
    $eventos[] = [
        'title' => 'Entrada: ' . date('H:i', strtotime($entrada)), // Hora en el título
        'start' => $entrada,
        'allDay' => false,
        'color' => '#28a745' // Color verde para la entrada
    ];

    // Evento para la salida (si existe)
    if ($salida) {
        $eventos[] = [
            'title' => 'Salida: ' . date('H:i', strtotime($salida)), // Hora en el título
            'start' => $salida,
            'allDay' => false,
            'color' => '#dc3545' // Color rojo para la salida
        ];
    }
}

// Convertir a JSON
$eventosJson = json_encode($eventos);
?>