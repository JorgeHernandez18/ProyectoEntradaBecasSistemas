<?php
session_start();
date_default_timezone_set('America/Bogota');
include "../modelo/conexion.php";

  // extrae los datos de la sesion
$arregloUsuario = $_SESSION['datos_login'];
 //si no existe sesion lo manda al index
 if (!isset($_SESSION['datos_login'])) {
    header("location: index.php");
    exit();
  }

// Determinar la sede basada en el usuario
if ($arregloUsuario['nombre']=='entradabecl') {
    $sede = 'becl'; 
}elseif ($arregloUsuario['nombre']=='entradabecle') {
    $sede = 'bcs';
} else {
    $sede = 'desconocida';
}



// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = $_POST['codigo'];
    $tipoRegistro = $_POST['radioOpciones']; // Puede ser 'entrada' o 'salida'

    // Verificar si el código existe en la tabla 'borrowers'
    $query = "SELECT borrowers.cardnumber, borrowers.surname, borrowers.firstname, borrowers.email, S.lib AS carrera, authorised_values.lib AS departamento 
              FROM borrowers 
              LEFT JOIN authorised_values S ON borrowers.sort2 = S.authorised_value 
              LEFT JOIN authorised_values ON borrowers.sort1 = authorised_values.authorised_value 
              WHERE borrowers.cardnumber = '$codigo'";
    $resultado = $conexion->query($query);


    // Consulta para obtener el número de registros del día
    $consultaDia = $conexion->query("SELECT COUNT(*) as totalDia FROM registro WHERE DATE(entrada) = CURDATE()");
    $registrosDia = $consultaDia->fetch_assoc()['totalDia'];



    if ($resultado->num_rows > 0) {
        // Obtener los datos del estudiante
        $estudiante = $resultado->fetch_assoc();
        $nombre = $estudiante['firstname'] . ' ' . $estudiante['surname'];
        $correo = $estudiante['email'];
        $programa = $estudiante['carrera'];
        $facultad = $estudiante['departamento'];
        
        // Registrar la hora actual
        $fechaHoraActual = date('Y-m-d H:i:s');

        if ($tipoRegistro == 'salida') {
            // Buscar el último registro de entrada sin salida
            $queryUltimoRegistro = "SELECT * FROM registro WHERE codigo = '$codigo' ORDER BY id DESC LIMIT 1";
            $resultadoUltimoRegistro = $conexion->query($queryUltimoRegistro);
            
            if ($resultadoUltimoRegistro->num_rows > 0) {
                $registro = $resultadoUltimoRegistro->fetch_assoc();
                $idRegistro = $registro['id'];
                    
                // Actualizar salida
                $queryActualizarSalida = "UPDATE registro SET salida = '$fechaHoraActual' WHERE id = '$idRegistro'";
                $conexion->query($queryActualizarSalida);

                // Devolver respuesta en JSON
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
                exit();
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'No se encontró una entrada sin salida para este estudiante'
                ]);
                exit();
            }
        } else {
            // Registrar nueva entrada
            $queryInsertar = "INSERT INTO registro (nombre, correo, codigo, programa, facultad, entrada, sede)
                              VALUES ('$nombre', '$correo', '$codigo', '$programa', '$facultad', '$fechaHoraActual', '$sede')";
            $conexion->query($queryInsertar);

            // Devolver respuesta en JSON
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
            exit();
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'El código ingresado no existe en la base de datos'
        ]);
        exit();
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido'
    ]);
}
?>