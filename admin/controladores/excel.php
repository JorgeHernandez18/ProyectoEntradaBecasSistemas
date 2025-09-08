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
    $sheet->setCellValue('B1', 'Correo');
    $sheet->setCellValue('C1', 'Código');
    $sheet->setCellValue('D1', 'Fecha de Entrada');
    $sheet->setCellValue('E1', 'Hora de Entrada');
    $sheet->setCellValue('F1', 'Fecha de Salida');
    $sheet->setCellValue('G1', 'Hora de Salida');
    $sheet->setCellValue('H1', 'Horas Trabajadas');
    $sheet->setCellValue('I1', 'Observaciones');

    // Inicializa la consulta base y el array de parámetros
    $baseQuery = "SELECT * FROM becarios_registro WHERE 1=1";
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

    // Maneja el filtrado por becario específico
    $codigoBecario = isset($_GET['codigo_becario']) ? $_GET['codigo_becario'] : '';
    if (!empty($codigoBecario)) {
        $baseQuery .= " AND codigo = ?";
        $params[] = $codigoBecario;
    } else {
        // Maneja la búsqueda por término (solo si no es filtrado por becario específico)
        $busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
        if (!empty($busqueda)) {
            $baseQuery .= " AND (nombre LIKE ? OR 
                                 codigo LIKE ? OR 
                                 correo LIKE ?)";
            $params = array_merge($params, array_fill(0, 3, "%$busqueda%"));
        }
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
        
        // Separar fecha y hora de entrada
        $fechaEntrada = date('Y-m-d', strtotime($f['entrada']));
        $horaEntrada = date('H:i:s', strtotime($f['entrada']));
        $sheet->setCellValue('D' . $row, $fechaEntrada);
        $sheet->setCellValue('E' . $row, $horaEntrada);
        
        // Separar fecha y hora de salida (si existe)
        if ($f['salida']) {
            $fechaSalida = date('Y-m-d', strtotime($f['salida']));
            $horaSalida = date('H:i:s', strtotime($f['salida']));
            $sheet->setCellValue('F' . $row, $fechaSalida);
            $sheet->setCellValue('G' . $row, $horaSalida);
        } else {
            $sheet->setCellValue('F' . $row, '');
            $sheet->setCellValue('G' . $row, '');
        }
        
        $sheet->setCellValue('H' . $row, $f['horas_trabajadas'] ?? '0.00');
        $sheet->setCellValue('I' . $row, $f['observaciones'] ?? '');
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