<?php
include "../../modelo/conexion.php";
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Función para descargar Excel del registro de estudiantes
if (isset($_GET['action']) && $_GET['action'] == 'downloadExcel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Configura los encabezados
    $sheet->setCellValue('A1', 'Nombre');
    $sheet->setCellValue('B1', 'Correo');
    $sheet->setCellValue('C1', 'Código');
    $sheet->setCellValue('D1', 'Programa Académico');
    $sheet->setCellValue('E1', 'Facultad');
    $sheet->setCellValue('F1', 'Entrada');
    $sheet->setCellValue('G1', 'Salida');
    $sheet->setCellValue('H1', 'Equipo');

    // Inicializa la consulta base y el array de parámetros
    $baseQuery = "SELECT * FROM registro_computo WHERE 1=1";
    $params = array();

    // Maneja el filtrado por fecha
    if(isset($_GET['from_date']) && isset($_GET['to_date']) && !empty($_GET['from_date']) && !empty($_GET['to_date']))
    {
        $from_date = $_GET['from_date'];
        $to_date = $_GET['to_date'];

        // Ajusta la fecha final para incluir todo el día
        $to_date = date('Y-m-d', strtotime($to_date . ' +1 day'));

        $baseQuery .= " AND entrada >= ? AND entrada < ?";
        $params[] = $from_date;
        $params[] = $to_date;
    }

    // Maneja la búsqueda por término
    $busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
    if (!empty($busqueda)) {
        $baseQuery .= " AND (nombre LIKE ? OR 
                             codigo LIKE ? OR 
                             programa LIKE ? OR 
                             facultad LIKE ? OR 
                             correo LIKE ? OR 
                             equipo LIKE ?)";
        $params = array_merge($params, array_fill(0, 6, "%$busqueda%"));
    }

    $baseQuery .= " ORDER BY id DESC";

    // Prepara y ejecuta la consulta
    $stmt = $conexion->prepare($baseQuery);
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
        $sheet->setCellValue('B' . $row, $f['correo']);
        $sheet->setCellValue('C' . $row, $f['codigo']);
        $sheet->setCellValue('D' . $row, $f['programa']);
        $sheet->setCellValue('E' . $row, $f['facultad']);
        $sheet->setCellValue('F' . $row, $f['entrada']);
        $sheet->setCellValue('G' . $row, $f['salida']);
        $sheet->setCellValue('H' . $row, $f['equipo']);
        $row++;
    }

    // Descarga el archivo Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Estudiantes_Registrados_computo.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>