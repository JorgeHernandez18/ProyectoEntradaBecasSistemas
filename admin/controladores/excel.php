<?php
include_once "../../modelo/conexion.php";
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Función para descargar Excel del registro de becarios
if (isset($_GET['action']) && $_GET['action'] == 'downloadExcel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Configura los encabezados para el modelo de becarios
    $sheet->setCellValue('A1', 'Nombre');
    $sheet->setCellValue('B1', 'Código');
    $sheet->setCellValue('C1', 'Fecha de Entrada');
    $sheet->setCellValue('D1', 'Hora de Entrada');
    $sheet->setCellValue('E1', 'Fecha de Salida');
    $sheet->setCellValue('F1', 'Hora de Salida');
    $sheet->setCellValue('G1', 'Horas Trabajadas');
    $sheet->setCellValue('H1', 'Actividad');

    // Inicializa la consulta base y el array de parámetros
    $baseQuery = "FROM registros ingreso
                  LEFT JOIN registros salida
                    ON ingreso.codigo = salida.codigo
                    AND ingreso.fecha = salida.fecha
                    AND salida.tipo = 'Salida'
                    AND salida.id > ingreso.id
                  WHERE ingreso.tipo = 'Ingreso'";
    $params = array();

    // Maneja el filtrado por fecha
    if(isset($_GET['from_date']) && isset($_GET['to_date']) && !empty($_GET['from_date']) && !empty($_GET['to_date']))
    {
        $from_date = $_GET['from_date'];
        $to_date = $_GET['to_date'];

        $baseQuery .= " AND ingreso.fecha >= ? AND ingreso.fecha <= ?";
        $params[] = $from_date;
        $params[] = $to_date;
    }

    // Maneja el filtrado por becario específico
    $codigoBecario = isset($_GET['codigo_becario']) ? $_GET['codigo_becario'] : '';
    if (!empty($codigoBecario)) {
        $baseQuery .= " AND ingreso.codigo = ?";
        $params[] = $codigoBecario;
    } else {
        // Maneja la búsqueda por término (solo si no es filtrado por becario específico)
        $busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
        if (!empty($busqueda)) {
            $baseQuery .= " AND (ingreso.nombre LIKE ? OR ingreso.codigo LIKE ?)";
            $params = array_merge($params, array_fill(0, 2, "%$busqueda%"));
        }
    }

    // Consulta completa
    $fullQuery = "SELECT
        ingreso.nombre,
        ingreso.codigo,
        ingreso.fecha as fecha_entrada,
        ingreso.hora as hora_entrada,
        salida.fecha as fecha_salida,
        salida.hora as hora_salida,
        salida.actividad,
        CASE
            WHEN salida.hora IS NOT NULL THEN
                EXTRACT(EPOCH FROM (
                    (salida.fecha || ' ' || salida.hora)::timestamp -
                    (ingreso.fecha || ' ' || ingreso.hora)::timestamp
                )) / 3600
            ELSE NULL
        END as horas_trabajadas
    " . $baseQuery . " ORDER BY ingreso.id DESC";

    // Prepara y ejecuta la consulta
    $stmt = $conexion->prepare($fullQuery);
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // Agrega los datos a las filas
    $row = 2; // Empezamos desde la fila 2
    while ($f = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $f['nombre']);
        $sheet->setCellValue('B' . $row, $f['codigo']);
        $sheet->setCellValue('C' . $row, $f['fecha_entrada']);
        $sheet->setCellValue('D' . $row, $f['hora_entrada']);

        // Salida (si existe)
        if ($f['fecha_salida'] && $f['hora_salida']) {
            $sheet->setCellValue('E' . $row, $f['fecha_salida']);
            $sheet->setCellValue('F' . $row, $f['hora_salida']);
        } else {
            $sheet->setCellValue('E' . $row, '');
            $sheet->setCellValue('F' . $row, '');
        }

        $sheet->setCellValue('G' . $row, $f['horas_trabajadas'] ? number_format($f['horas_trabajadas'], 2) : '0.00');
        $sheet->setCellValue('H' . $row, $f['actividad'] ?? '');
        $row++;
    }

    // Descarga el archivo Excel
    $fileName = "Becarios_Registros.xlsx";
    if (!empty($codigoBecario)) {
        $nombreBecario = isset($_GET['nombre_becario']) ? $_GET['nombre_becario'] : $codigoBecario;
        $nombreBecario = preg_replace('/[^A-Za-z0-9_\-]/', '_', $nombreBecario);
        $fileName = "Registro_" . $nombreBecario . "_" . $codigoBecario . ".xlsx";
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>
