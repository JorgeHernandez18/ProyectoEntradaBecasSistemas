<?php
function handleError($message, $sqlError = null) {
    $error = [
        'success' => false,
        'message' => $message
    ];
    if ($sqlError) {
        $error['sqlError'] = $sqlError;
    }
    echo json_encode($error);
    exit();
}

function handleSuccess($message, $data = []) {
    $response = [
        'success' => true,
        'message' => $message
    ];
    if (!empty($data)) {
        $response = array_merge($response, $data);
    }
    echo json_encode($response);
    exit();
}

function validatePasswordStrength($password) {
    // Al menos 6 caracteres
    if (strlen($password) < 6) {
        return false;
    }
    
    // Al menos una mayúscula
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    
    // Al menos una minúscula
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    
    // Al menos un número
    if (!preg_match('/\d/', $password)) {
        return false;
    }
    
    return true;
}

try {
    session_start();
    include_once "../../modelo/conexion.php";
    include_once "seguridad.php";

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        handleError("Método no permitido");
    }

    if (!isset($_SESSION['datos_login'])) {
        handleError("Sesión no iniciada");
    }

    $arregloUsuario = $_SESSION['datos_login'];
    $usuarioActual = $arregloUsuario['nombre'];
    $nivelActual = $arregloUsuario['nivel'];

    // Solo admins pueden cambiar contraseñas
    if ($nivelActual != 'admin') {
        handleError("No tienes permisos para realizar esta acción");
    }

    $action = $_POST['action'] ?? '';

    if ($action == 'cambiar_admin') {
        // Cambiar contraseña del admin actual
        $passwordActual = $_POST['password_actual'] ?? '';
        $passwordNueva = $_POST['password_nueva'] ?? '';

        if (empty($passwordActual) || empty($passwordNueva)) {
            handleError("Todos los campos son obligatorios");
        }

        if (!validatePasswordStrength($passwordNueva)) {
            handleError("La nueva contraseña debe tener al menos 6 caracteres, incluyendo mayúsculas, minúsculas y números");
        }

        // Verificar contraseña actual
        $stmt = $conexion->prepare("SELECT password FROM becarios_admin WHERE usuario = ? AND nivel = 'admin'");
        $stmt->bind_param("s", $usuarioActual);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 0) {
            handleError("Usuario no encontrado");
        }

        $resultado->reset();
        $datos = $resultado->fetch_assoc();
        
        if (!password_verify($passwordActual, $datos['password'])) {
            handleError("La contraseña actual es incorrecta");
        }

        // Actualizar contraseña
        $passwordHasheada = password_hash($passwordNueva, PASSWORD_DEFAULT);
        $stmtUpdate = $conexion->prepare("UPDATE becarios_admin SET password = ? WHERE usuario = ? AND nivel = 'admin'");
        $stmtUpdate->bind_param("ss", $passwordHasheada, $usuarioActual);
        
        if ($stmtUpdate->execute()) {
            handleSuccess("Contraseña de administrador cambiada exitosamente");
        } else {
            handleError("Error al actualizar la contraseña");
        }

    } elseif ($action == 'cambiar_entrada') {
        // Cambiar contraseña del usuario de entrada
        $passwordNueva = $_POST['password_nueva'] ?? '';

        if (empty($passwordNueva)) {
            handleError("La nueva contraseña es obligatoria");
        }

        if (!validatePasswordStrength($passwordNueva)) {
            handleError("La nueva contraseña debe tener al menos 6 caracteres, incluyendo mayúsculas, minúsculas y números");
        }

        // Actualizar contraseña del usuario de entrada
        $passwordHasheada = password_hash($passwordNueva, PASSWORD_DEFAULT);
        $stmtUpdate = $conexion->prepare("UPDATE becarios_admin SET password = ? WHERE nivel = 'entrada'");
        $stmtUpdate->bind_param("s", $passwordHasheada);
        
        if ($stmtUpdate->execute()) {
            if ($stmtUpdate->affected_rows > 0) {
                handleSuccess("Contraseña del usuario de entrada cambiada exitosamente");
            } else {
                handleError("No se encontró el usuario de entrada o no se realizaron cambios");
            }
        } else {
            handleError("Error al actualizar la contraseña del usuario de entrada");
        }

    } else {
        handleError("Acción no válida");
    }

} catch (Exception $e) {
    handleError("Error inesperado: " . $e->getMessage());
}
?>