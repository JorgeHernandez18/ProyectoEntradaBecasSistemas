<?php
// Función para manejar errores y devolverlos como JSON
function handleError($message, $sqlError = null) {
    $error = [
        'success' => false,
        'error' => $message
    ];
    if ($sqlError) {
        $error['sqlError'] = $sqlError;
    }
    echo json_encode($error);
    exit();
}

try {
    session_start();
    date_default_timezone_set('America/Bogota');
    
    // Incluir el archivo de conexión
    if (!file_exists("../modelo/conexion.php")) {
        handleError("Archivo de conexión no encontrado");
    }
    include_once "../modelo/conexion.php";

    // La verificación de conexión ya se maneja en el archivo de conexión

    // Verificar la sesión
    if (!isset($_SESSION['datos_login'])) {
        handleError("Sesión no iniciada");
    }
    $arregloUsuario = $_SESSION['datos_login'];

    // Sistema de becarios - no necesita sede

    // Verificar el método de la solicitud
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        handleError("Método no permitido");
    }

    // Obtener y validar los datos del POST
    $codigo = $_POST['codigo'] ?? '';
    $tipoRegistro = $_POST['radioOpciones'] ?? '';
    if (empty($codigo) || empty($tipoRegistro)) {
        handleError("Datos de formulario incompletos");
    }

    // Consulta para verificar el código del becario
    $query = "SELECT codigo, nombre_completo, correo, semestre, horas_semanales 
              FROM becarios_info 
              WHERE codigo = ? AND estado = 'activo'";

    $stmt = $conexion->prepare($query);
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Consulta para obtener el número de registros del día (usar CURRENT_DATE para PostgreSQL)
    $consultaDia = "SELECT COUNT(*) as totalDia FROM becarios_registro WHERE DATE(entrada) = CURRENT_DATE";
    $stmtDia = $conexion->prepare($consultaDia);
    $stmtDia->execute();
    $resultadoDia = $stmtDia->get_result();
    $rowDia = $resultadoDia->fetch_assoc();
    $registrosDia = $rowDia['totalDia'] ?? 0;

    if ($resultado->num_rows > 0) {
        $resultado->reset();
        $becario = $resultado->fetch_assoc();
        $nombre = $becario['nombre_completo'];
        $correo = $becario['correo'];
        $semestre = $becario['semestre'];
        $horasSemanales = $becario['horas_semanales'];
        $fechaHoraActual = date('Y-m-d H:i:s');

        if ($tipoRegistro == 'salida') {
            // Lógica para registro de salida
            $queryUltimoRegistro = "SELECT * FROM becarios_registro WHERE codigo = ? AND salida IS NULL ORDER BY id DESC LIMIT 1";
            $stmt = $conexion->prepare($queryUltimoRegistro);
            $stmt->bind_param("s", $codigo);
            $stmt->execute();
            $resultadoUltimoRegistro = $stmt->get_result();
            
            if ($resultadoUltimoRegistro->num_rows > 0) {
                $resultadoUltimoRegistro->reset();
                $registro = $resultadoUltimoRegistro->fetch_assoc();
                $idRegistro = $registro['id'];
                $entrada = $registro['entrada'];
                
                // Calcular horas trabajadas
                $entradaTime = new DateTime($entrada);
                $salidaTime = new DateTime($fechaHoraActual);
                $diferencia = $entradaTime->diff($salidaTime);
                
                // Calcular total de horas considerando días, horas, minutos y segundos
                $totalMinutos = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i + ($diferencia->s / 60);
                $horasTrabajadas = $totalMinutos / 60;
                $horasTrabajadas = round($horasTrabajadas, 2);
                
                $queryActualizarSalida = "UPDATE becarios_registro SET salida = ?, horas_trabajadas = ? WHERE id = ?";
                $stmtActualizar = $conexion->prepare($queryActualizarSalida);
                $stmtActualizar->bind_param("sdi", $fechaHoraActual, $horasTrabajadas, $idRegistro);
                $stmtActualizar->execute();

                echo json_encode([
                    'success' => true,
                    'nombre' => $nombre,
                    'codigo' => $codigo,
                    'correo' => $correo,
                    'semestre' => $semestre,
                    'horas_semanales' => $horasSemanales,
                    'hora' => $fechaHoraActual,
                    'tipo' => 'salida',
                    'horas_trabajadas' => $horasTrabajadas,
                    'registroDia' => $registrosDia,
                    'mensaje' => 'Salida registrada exitosamente - ' . $horasTrabajadas . ' horas trabajadas',
                ]);
            } else {
                handleError('No se encontró una entrada sin salida para este estudiante');
            }
        } else {
            // Lógica para registro de entrada
            $queryInsertar = "INSERT INTO becarios_registro (nombre, correo, codigo, entrada) VALUES (?, ?, ?, ?)";
            $stmtInsertar = $conexion->prepare($queryInsertar);
            $stmtInsertar->bind_param("ssss", $nombre, $correo, $codigo, $fechaHoraActual);
            $stmtInsertar->execute();

            echo json_encode([
                'success' => true,
                'nombre' => $nombre,
                'codigo' => $codigo,
                'hora' => $fechaHoraActual,
                'tipo' => 'entrada',
                'correo' => $correo,
                'semestre' => $semestre,
                'horas_semanales' => $horasSemanales,
                'registroDia' => $registrosDia,
                'mensaje' => 'Entrada registrada exitosamente'
            ]);
        }
    } else {
        handleError('El código de becario ingresado no existe o está inactivo');
    }
} catch (Exception $e) {
    handleError("Error inesperado: " . $e->getMessage());
}
?>