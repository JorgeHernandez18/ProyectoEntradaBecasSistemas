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
    include "../modelo/conexion.php";

    // Verificar la conexión
    if ($conexion->connect_error) {
        handleError("Error de conexión: " . $conexion->connect_error);
    }

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
    if (!$stmt) {
        handleError("Error en la preparación de la consulta: " . $conexion->error);
    }
    $stmt->bind_param("s", $codigo);
    if (!$stmt->execute()) {
        handleError("Error al ejecutar la consulta: " . $stmt->error);
    }
    $resultado = $stmt->get_result();

    // Consulta para obtener el número de registros del día
    $consultaDia = "SELECT COUNT(*) as totalDia FROM becarios_registro WHERE DATE(entrada) = CURDATE()";
    $stmtDia = $conexion->prepare($consultaDia);
    if (!$stmtDia) {
        handleError("Error en la preparación de la consulta de registros del día: " . $conexion->error);
    }
    if (!$stmtDia->execute()) {
        handleError("Error al ejecutar la consulta de registros del día: " . $stmtDia->error);
    }
    $resultadoDia = $stmtDia->get_result();
    $registrosDia = $resultadoDia->fetch_assoc()['totalDia'];

    if ($resultado->num_rows > 0) {
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
            if (!$stmt) {
                handleError("Error en la preparación de la consulta de último registro: " . $conexion->error);
            }
            $stmt->bind_param("s", $codigo);
            if (!$stmt->execute()) {
                handleError("Error al ejecutar la consulta de último registro: " . $stmt->error);
            }
            $resultadoUltimoRegistro = $stmt->get_result();
            
            if ($resultadoUltimoRegistro->num_rows > 0) {
                $registro = $resultadoUltimoRegistro->fetch_assoc();
                $idRegistro = $registro['id'];
                $entrada = $registro['entrada'];
                
                // Calcular horas trabajadas
                $entradaTime = new DateTime($entrada);
                $salidaTime = new DateTime($fechaHoraActual);
                $diferencia = $entradaTime->diff($salidaTime);
                $horasTrabajadas = $diferencia->h + ($diferencia->i / 60);
                $horasTrabajadas = round($horasTrabajadas, 2);
                
                $queryActualizarSalida = "UPDATE becarios_registro SET salida = ?, horas_trabajadas = ? WHERE id = ?";
                $stmtActualizar = $conexion->prepare($queryActualizarSalida);
                if (!$stmtActualizar) {
                    handleError("Error en la preparación de la actualización de salida: " . $conexion->error);
                }
                $stmtActualizar->bind_param("sdi", $fechaHoraActual, $horasTrabajadas, $idRegistro);
                if (!$stmtActualizar->execute()) {
                    handleError("Error al ejecutar la actualización de salida: " . $stmtActualizar->error);
                }

                echo json_encode([
                    'success' => true,
                    'nombre' => $nombre,
                    'codigo' => $codigo,
                    'hora' => $fechaHoraActual,
                    'tipo' => 'salida',
                    'semestre' => $semestre,
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
            if (!$stmtInsertar) {
                handleError("Error en la preparación de la inserción de entrada: " . $conexion->error);
            }
            $stmtInsertar->bind_param("ssss", $nombre, $correo, $codigo, $fechaHoraActual);
            if (!$stmtInsertar->execute()) {
                handleError("Error al ejecutar la inserción de entrada: " . $stmtInsertar->error);
            }

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