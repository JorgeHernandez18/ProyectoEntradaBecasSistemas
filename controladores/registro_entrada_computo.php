<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
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
    //if ($arregloUsuario['nombre'] == 'entradabecl') {
    //    $sede = 'becl';
    //} elseif ($arregloUsuario['nombre'] == 'entradabecle') {
    //    $sede = 'bcs';
    //} else {
    //    $sede = 'desconocida';
    //}

    // Verificar el método de la solicitud
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        handleError("Método no permitido");
    }

    // Obtener y validar los datos del POST
    $equipo= $_POST['equipo'] ?? '';
    $codigo = $_POST['codigo'] ?? '';
    $tipoRegistro = $_POST['radioOpciones'] ?? '';
    if (empty($codigo) || empty($tipoRegistro)) {
        handleError("Datos de formulario incompletos");
    }
    if ($tipoRegistro === 'entrada' && empty($equipo)) {
        handleError("Datos de formulario incompletos: Se requiere seleccionar un equipo para la entrada");
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
    $consultaDia = "SELECT COUNT(*) as totalDia FROM becl_registro_computo WHERE DATE(entrada) = CURDATE()";
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
            $queryUltimoRegistro = "SELECT * FROM becl_registro_computo WHERE codigo = ? ORDER BY id DESC LIMIT 1";
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
                $equipoUsado = $registro['equipo'];
                
                $queryActualizarSalida = "UPDATE becl_registro_computo SET salida = ? WHERE id = ?";
                $stmtActualizar = $conexion->prepare($queryActualizarSalida);
                if (!$stmtActualizar) {
                    handleError("Error en la preparación de la actualización de salida: " . $conexion->error);
                }
                $stmtActualizar->bind_param("si", $fechaHoraActual, $idRegistro);
                if (!$stmtActualizar->execute()) {
                    handleError("Error al ejecutar la actualización de salida: " . $stmtActualizar->error);
                }

                // Actualizar el estado del equipo a 'libre'
                $queryActualizarEquipo = "UPDATE becl_equipo SET estado = 'libre' WHERE equipo = ?";
                $stmtActualizarEquipo = $conexion->prepare($queryActualizarEquipo);
                if (!$stmtActualizarEquipo) {
                    handleError("Error en la preparación de la actualización del estado del equipo: " . $conexion->error);
                }
                $stmtActualizarEquipo->bind_param("i", $equipoUsado);
                if (!$stmtActualizarEquipo->execute()) {
                    error_log("Error al actualizar estado del equipo: " . $stmtActualizarEquipo->error);
                    handleError("Error al ejecutar la actualización del estado del equipo: " . $stmtActualizarEquipo->error);
                }
                error_log("Estado del equipo actualizado correctamente: Equipo " . $equipo . " marcado como ocupado");

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
                    'equipoLiberado' => $equipoUsado
                ]);
            } else {
                handleError('No se encontró una entrada sin salida para este estudiante');
            }
        } else {
            // Verificar si el equipo está libre
            $queryVerificarEquipo = "SELECT estado FROM becl_equipo WHERE equipo = ?";
            $stmtVerificarEquipo = $conexion->prepare($queryVerificarEquipo);

            if (!$stmtVerificarEquipo) {
                handleError("Error en la preparación de la consulta de verificación de equipo: " . $conexion->error);
            }
            $stmtVerificarEquipo->bind_param("i", $equipo);
            if (!$stmtVerificarEquipo->execute()) {
                handleError("Error al ejecutar la consulta de verificación de equipo: " . $stmtVerificarEquipo->error);
            }
            $resultadoVerificarEquipo = $stmtVerificarEquipo->get_result();
            $estadoEquipo = $resultadoVerificarEquipo->fetch_assoc()['estado'];

            if ($estadoEquipo !== 'libre') {
                handleError("El equipo seleccionado no está disponible");
            }
            
            if ($tipoRegistro === 'entrada') {
                // Verificar si el usuario ya tiene un préstamo activo
                $queryPrestamoActivo = "SELECT * FROM becl_registro_computo WHERE codigo = ? AND salida IS NULL";
                $stmtPrestamoActivo = $conexion->prepare($queryPrestamoActivo);
                if (!$stmtPrestamoActivo) {
                    handleError("Error en la preparación de la consulta de préstamo activo: " . $conexion->error);
                }
                $stmtPrestamoActivo->bind_param("s", $codigo);
                if (!$stmtPrestamoActivo->execute()) {
                    handleError("Error al ejecutar la consulta de préstamo activo: " . $stmtPrestamoActivo->error);
                }
                $resultadoPrestamoActivo = $stmtPrestamoActivo->get_result();
                
                if ($resultadoPrestamoActivo->num_rows > 0) {
                    handleError("El usuario ya tiene un equipo prestado. Debe registrar la salida antes de prestar otro equipo.");
                }
            }
            // Lógica para registro de entrada
            $queryInsertar = "INSERT INTO becl_registro_computo (nombre, correo, codigo, programa, facultad, entrada, equipo) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtInsertar = $conexion->prepare($queryInsertar);
            if (!$stmtInsertar) {
                handleError("Error en la preparación de la inserción de entrada: " . $conexion->error);
            }
            $stmtInsertar->bind_param("ssssssi", $nombre, $correo, $codigo, $programa, $facultad, $fechaHoraActual, $equipo);
            if (!$stmtInsertar->execute()) {
                handleError("Error al ejecutar la inserción de entrada: " . $stmtInsertar->error);
            }

            // Actualizar el estado del equipo a 'ocupado'
            $queryActualizarEquipo = "UPDATE becl_equipo SET estado = 'ocupado' WHERE equipo = ?";
            $stmtActualizarEquipo = $conexion->prepare($queryActualizarEquipo);
            if (!$stmtActualizarEquipo) {
                handleError("Error en la preparación de la actualización del estado del equipo: " . $conexion->error);
            }
            $stmtActualizarEquipo->bind_param("i", $equipo);
            if (!$stmtActualizarEquipo->execute()) {
                error_log("Error al actualizar estado del equipo: " . $stmtActualizarEquipo->error);
                handleError("Error al ejecutar la actualización del estado del equipo: " . $stmtActualizarEquipo->error);
            }
            error_log("Estado del equipo actualizado correctamente: Equipo " . $equipo . " marcado como ocupado");

            echo json_encode([
                'success' => true,
                'nombre' => $nombre,
                'codigo' => $codigo,
                'hora' => $fechaHoraActual,
                'tipo' => 'entrada',
                'correo' => $correo,
                'programa' => $programa,
                'facultad' => $facultad,
                'equipo' => $equipo,
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