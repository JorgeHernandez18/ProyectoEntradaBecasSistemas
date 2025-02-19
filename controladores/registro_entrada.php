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

    // Determinar la sede
    if ($arregloUsuario['nombre'] == 'entradabecl') {
        $sede = 'becl';
    } else {
        $sede = 'bcs';
    }

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

    // Consulta para verificar el código
    $query = "SELECT vista_borrowers.cardnumber, vista_borrowers.surname, vista_borrowers.firstname, vista_borrowers.email, 
              S.lib AS carrera, vista_authorised_values.lib AS departamento 
              FROM vista_borrowers 
              LEFT JOIN vista_authorised_values S ON vista_borrowers.sort2 = S.authorised_value 
              LEFT JOIN vista_authorised_values ON vista_borrowers.sort1 = vista_authorised_values.authorised_value 
              WHERE vista_borrowers.cardnumber = ?";

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
    $consultaDia = "SELECT COUNT(*) as totalDia FROM becl_registro WHERE DATE(entrada) = CURDATE()";
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
        $estudiante = $resultado->fetch_assoc();
        $nombre = $estudiante['firstname'] . ' ' . $estudiante['surname'];
        $correo = $estudiante['email'];
        $programa = $estudiante['carrera'];
        $facultad = $estudiante['departamento'];
        $fechaHoraActual = date('Y-m-d H:i:s');

        if ($tipoRegistro == 'salida') {
            // Lógica para registro de salida
            $queryUltimoRegistro = "SELECT * FROM becl_registro WHERE codigo = ? ORDER BY id DESC LIMIT 1";
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
                
                $queryActualizarSalida = "UPDATE becl_registro SET salida = ? WHERE id = ?";
                $stmtActualizar = $conexion->prepare($queryActualizarSalida);
                if (!$stmtActualizar) {
                    handleError("Error en la preparación de la actualización de salida: " . $conexion->error);
                }
                $stmtActualizar->bind_param("si", $fechaHoraActual, $idRegistro);
                if (!$stmtActualizar->execute()) {
                    handleError("Error al ejecutar la actualización de salida: " . $stmtActualizar->error);
                }

                echo json_encode([
                    'success' => true,
                    'nombre' => $nombre,
                    'codigo' => $codigo,
                    'hora' => $fechaHoraActual,
                    'tipo' => 'salida',
                    'programa' => $programa,
                    'facultad' => $facultad,
                    'registroDia' => $registrosDia,
                    'mensaje' => 'Salida registrada exitosamente',
                ]);
            } else {
                handleError('No se encontró una entrada sin salida para este estudiante');
            }
        } else {
            // Lógica para registro de entrada
            $queryInsertar = "INSERT INTO becl_registro (nombre, correo, codigo, programa, facultad, entrada, sede) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtInsertar = $conexion->prepare($queryInsertar);
            if (!$stmtInsertar) {
                handleError("Error en la preparación de la inserción de entrada: " . $conexion->error);
            }
            $stmtInsertar->bind_param("sssssss", $nombre, $correo, $codigo, $programa, $facultad, $fechaHoraActual, $sede);
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
                'programa' => $programa,
                'facultad' => $facultad,
                'sede' => $sede,
                'registroDia' => $registrosDia,
                'mensaje' => 'Entrada registrada exitosamente'
            ]);
        }
    } else {
        handleError('El código ingresado no existe en la base de datos');
    }
} catch (Exception $e) {
    handleError("Error inesperado: " . $e->getMessage());
}
?>