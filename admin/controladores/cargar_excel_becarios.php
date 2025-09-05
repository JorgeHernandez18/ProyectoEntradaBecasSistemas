<?php
session_start();
include "seguridad.php";
include "../../modelo/conexion.php";

// Incluir la librería PhpSpreadsheet (si está instalada)
// require '../../vendor/autoload.php';
// use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validar que se haya subido un archivo
        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("No se ha seleccionado un archivo válido");
        }
        
        $archivo = $_FILES['excel_file'];
        $tipoArchivo = $archivo['type'];
        $nombreArchivo = $archivo['name'];
        $rutaTemporal = $archivo['tmp_name'];
        
        // Validar tipo de archivo
        $tiposPermitidos = [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv'
        ];
        
        $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
        if (!in_array($tipoArchivo, $tiposPermitidos) && !in_array($extension, ['csv', 'xls', 'xlsx'])) {
            throw new Exception("Tipo de archivo no permitido. Use CSV, XLS o XLSX");
        }
        
        $becariosCreados = 0;
        $errores = [];
        
        // Procesar archivo CSV (implementación simple)
        if ($extension === 'csv' || $tipoArchivo === 'text/csv') {
            if (($handle = fopen($rutaTemporal, "r")) !== FALSE) {
                $primeraFila = true;
                $numeroFila = 0;
                
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $numeroFila++;
                    
                    // Saltar la primera fila (encabezados)
                    if ($primeraFila) {
                        $primeraFila = false;
                        continue;
                    }
                    
                    // Validar que tenga al menos los campos obligatorios
                    if (count($data) < 3) {
                        $errores[] = "Fila $numeroFila: Datos insuficientes";
                        continue;
                    }
                    
                    // Mapear datos del CSV
                    $codigo = trim($data[0] ?? '');
                    $nombre_completo = trim($data[1] ?? '');
                    $correo = trim($data[2] ?? '');
                    $telefono = trim($data[3] ?? '') ?: null;
                    $semestre = intval($data[4] ?? 0) ?: null;
                    $horas_semanales = intval($data[5] ?? 20) ?: 20;
                    $fecha_inicio = trim($data[6] ?? '') ?: date('Y-m-d');
                    
                    // Validaciones básicas
                    if (empty($codigo) || empty($nombre_completo) || empty($correo)) {
                        $errores[] = "Fila $numeroFila: Faltan datos obligatorios (código, nombre o correo)";
                        continue;
                    }
                    
                    // Verificar si el código ya existe
                    $queryVerificar = "SELECT codigo FROM becarios_info WHERE codigo = ?";
                    $stmtVerificar = $conexion->prepare($queryVerificar);
                    $stmtVerificar->bind_param("s", $codigo);
                    $stmtVerificar->execute();
                    $resultado = $stmtVerificar->get_result();
                    
                    if ($resultado->num_rows > 0) {
                        $errores[] = "Fila $numeroFila: El código $codigo ya existe";
                        continue;
                    }
                    
                    // Insertar becario
                    $query = "INSERT INTO becarios_info (codigo, nombre_completo, correo, telefono, semestre, horas_semanales, fecha_inicio, estado) VALUES (?, ?, ?, ?, ?, ?, ?, 'activo')";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param("sssssis", $codigo, $nombre_completo, $correo, $telefono, $semestre, $horas_semanales, $fecha_inicio);
                    
                    if ($stmt->execute()) {
                        $becariosCreados++;
                    } else {
                        $errores[] = "Fila $numeroFila: Error al insertar becario $codigo - " . $stmt->error;
                    }
                }
                fclose($handle);
            }
        } else {
            // Para archivos Excel se necesitaría PhpSpreadsheet
            throw new Exception("Para archivos Excel, por favor convierta el archivo a CSV. Formato esperado: codigo,nombre,correo,telefono,semestre,horas_semanales,fecha_inicio");
        }
        
        // Preparar mensaje de resultado
        $mensaje = "Proceso completado. ";
        $mensaje .= "Becarios creados: $becariosCreados. ";
        
        if (!empty($errores)) {
            $mensaje .= "Errores encontrados: " . count($errores) . ". ";
            $_SESSION['errores_carga'] = $errores;
        }
        
        $_SESSION['mensaje'] = $mensaje;
        $_SESSION['tipo_mensaje'] = $becariosCreados > 0 ? "success" : "warning";
        
    } catch (Exception $e) {
        $_SESSION['mensaje'] = "Error: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = "error";
    }
    
    // Redireccionar de vuelta
    header("Location: ../pages/funcionarios.php");
    exit();
} else {
    // Si no es POST, redireccionar
    header("Location: ../pages/funcionarios.php");
    exit();
}
?>