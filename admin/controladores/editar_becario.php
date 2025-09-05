<?php
session_start();
include "seguridad.php";
include "../../modelo/conexion.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Obtener datos del formulario
        $codigo = trim($_POST['codigo']);
        $nombre_completo = trim($_POST['nombre_completo']);
        $correo = trim($_POST['correo']);
        $telefono = trim($_POST['telefono']) ?: null;
        $semestre = intval($_POST['semestre']) ?: null;
        $horas_semanales = intval($_POST['horas_semanales']) ?: 20;
        $fecha_inicio = $_POST['fecha_inicio'];
        $estado = $_POST['estado'];
        
        // Validaciones básicas
        if (empty($codigo) || empty($nombre_completo) || empty($correo) || empty($fecha_inicio) || empty($estado)) {
            throw new Exception("Todos los campos obligatorios deben ser completados");
        }
        
        // Manejar la carga de nueva foto
        $nombreFoto = null;
        $actualizarFoto = false;
        
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
            $archivoTemporal = $_FILES['foto']['tmp_name'];
            $nombreOriginal = $_FILES['foto']['name'];
            $tipoArchivo = $_FILES['foto']['type'];
            $tamanoArchivo = $_FILES['foto']['size'];
            
            // Validar tipo de archivo
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($tipoArchivo, $tiposPermitidos)) {
                throw new Exception("Tipo de archivo no permitido. Use JPG, PNG o GIF");
            }
            
            // Validar tamaño (2MB máximo)
            if ($tamanoArchivo > 2 * 1024 * 1024) {
                throw new Exception("El archivo es muy grande. Máximo 2MB");
            }
            
            // Obtener foto actual para eliminarla
            $queryFotoActual = "SELECT foto FROM becarios_info WHERE codigo = ?";
            $stmtFotoActual = $conexion->prepare($queryFotoActual);
            $stmtFotoActual->bind_param("s", $codigo);
            $stmtFotoActual->execute();
            $resultadoFoto = $stmtFotoActual->get_result();
            
            if ($resultadoFoto->num_rows > 0) {
                $fotoActual = $resultadoFoto->fetch_assoc()['foto'];
                if ($fotoActual && file_exists('../assets/fotos_becarios/' . $fotoActual)) {
                    unlink('../assets/fotos_becarios/' . $fotoActual);
                }
            }
            
            // Crear nombre único para el archivo nuevo
            $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
            $nombreFoto = $codigo . '_' . time() . '.' . $extension;
            $rutaDestino = '../assets/fotos_becarios/' . $nombreFoto;
            
            // Mover archivo
            if (!move_uploaded_file($archivoTemporal, $rutaDestino)) {
                throw new Exception("Error al subir la foto");
            }
            
            $actualizarFoto = true;
        }
        
        // Verificar si el becario existe
        $queryVerificar = "SELECT codigo FROM becarios_info WHERE codigo = ?";
        $stmtVerificar = $conexion->prepare($queryVerificar);
        $stmtVerificar->bind_param("s", $codigo);
        $stmtVerificar->execute();
        $resultado = $stmtVerificar->get_result();
        
        if ($resultado->num_rows == 0) {
            throw new Exception("Becario no encontrado");
        }
        
        // Actualizar información del becario
        if ($actualizarFoto) {
            $query = "UPDATE becarios_info SET 
                      nombre_completo = ?, 
                      correo = ?, 
                      telefono = ?, 
                      semestre = ?, 
                      horas_semanales = ?, 
                      fecha_inicio = ?, 
                      estado = ?,
                      foto = ?
                      WHERE codigo = ?";
            
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("sssisssss", $nombre_completo, $correo, $telefono, $semestre, $horas_semanales, $fecha_inicio, $estado, $nombreFoto, $codigo);
        } else {
            $query = "UPDATE becarios_info SET 
                      nombre_completo = ?, 
                      correo = ?, 
                      telefono = ?, 
                      semestre = ?, 
                      horas_semanales = ?, 
                      fecha_inicio = ?, 
                      estado = ?
                      WHERE codigo = ?";
            
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("ssssisss", $nombre_completo, $correo, $telefono, $semestre, $horas_semanales, $fecha_inicio, $estado, $codigo);
        }
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Información del becario actualizada exitosamente";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            throw new Exception("Error al actualizar la información: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        $_SESSION['mensaje'] = $e->getMessage();
        $_SESSION['tipo_mensaje'] = "error";
    }
    
    // Redireccionar de vuelta a la página de gestión
    header("Location: ../pages/funcionarios.php");
    exit();
} else {
    // Si no es POST, redireccionar
    header("Location: ../pages/funcionarios.php");
    exit();
}
?>