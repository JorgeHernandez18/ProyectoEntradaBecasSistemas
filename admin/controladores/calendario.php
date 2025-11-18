<?php
// Consulta para obtener registros emparejando Ingreso con Salida
$queryRegistros = "SELECT
    ingreso.fecha as fecha_entrada,
    ingreso.hora as hora_entrada,
    salida.fecha as fecha_salida,
    salida.hora as hora_salida
FROM registros ingreso
LEFT JOIN registros salida
    ON ingreso.codigo = salida.codigo
    AND ingreso.fecha = salida.fecha
    AND ingreso.tipo = 'Ingreso'
    AND salida.tipo = 'Salida'
    AND salida.id > ingreso.id
WHERE ingreso.codigo = ? AND ingreso.tipo = 'Ingreso'
ORDER BY ingreso.fecha ASC, ingreso.hora ASC";

$stmtRegistros = $conexion->prepare($queryRegistros);
$stmtRegistros->bind_param("s", $codigo);
$stmtRegistros->execute();
$resultadoRegistros = $stmtRegistros->get_result();

// Preparar los datos para FullCalendar
$eventos = [];
while ($fila = $resultadoRegistros->fetch_assoc()) {
    $entrada = $fila['fecha_entrada'] . ' ' . $fila['hora_entrada'];

    // Evento para la entrada
    $eventos[] = [
        'title' => 'Entrada: ' . substr($fila['hora_entrada'], 0, 5),
        'start' => $entrada,
        'allDay' => false,
        'color' => '#28a745' // Color verde para la entrada
    ];

    // Evento para la salida (si existe)
    if ($fila['fecha_salida'] && $fila['hora_salida']) {
        $salida = $fila['fecha_salida'] . ' ' . $fila['hora_salida'];
        $eventos[] = [
            'title' => 'Salida: ' . substr($fila['hora_salida'], 0, 5),
            'start' => $salida,
            'allDay' => false,
            'color' => '#dc3545' // Color rojo para la salida
        ];
    }
}

// Convertir a JSON
$eventosJson = json_encode($eventos);
?>
