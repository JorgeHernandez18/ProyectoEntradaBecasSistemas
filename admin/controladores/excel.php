<?php
include "seguridad.php";
include "../../modelo/conexion.php";
require '../../vendor/autoload.php'; // Asegúrate de tener bien la ruta

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
    $sheet->setCellValue('H1', 'Sede');

    // Obtén el término de búsqueda
    $busqueda = isset($_GET['busqueda']) ? $conexion->real_escape_string($_GET['busqueda']) : '';

    // Construye la consulta con el filtro de búsqueda
    $query = "SELECT * FROM registro WHERE 
              nombre LIKE '%$busqueda%' OR 
              codigo LIKE '%$busqueda%' OR 
              programa LIKE '%$busqueda%' OR 
              facultad LIKE '%$busqueda%' OR 
              correo LIKE '%$busqueda%' OR 
              sede LIKE '%$busqueda%'
              ORDER BY id DESC";

    $result = $conexion->query($query);

    // Agrega los datos a las filas
    $row = 2; // Empezamos desde la fila 2
    while ($f = mysqli_fetch_array($result)) {
        $sheet->setCellValue('A' . $row, $f['nombre']);
        $sheet->setCellValue('B' . $row, $f['correo']);
        $sheet->setCellValue('C' . $row, $f['codigo']);
        $sheet->setCellValue('D' . $row, $f['programa']);
        $sheet->setCellValue('E' . $row, $f['facultad']);
        $sheet->setCellValue('F' . $row, $f['entrada']);
        $sheet->setCellValue('G' . $row, $f['salida']);
        $sheet->setCellValue('H' . $row, $f['sede']);
        $row++;
    }

    // Descarga el archivo Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Estudiantes_Registrados.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>