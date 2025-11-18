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
        $stmt = $conexion_pdo->prepare("SELECT clave FROM admin WHERE usuario = ?");
        $stmt->execute([$usuarioActual]);
        $datos = $stmt->fetch();

        if (!$datos) {
            handleError("Usuario no encontrado");
        }

        // Comparar directamente los strings de las contraseñas (sin hash)
        if ($passwordActual !== $datos['clave']) {
            handleError("La contraseña actual es incorrecta");
        }

        // Actualizar contraseña
        $passwordHasheada = password_hash($passwordNueva, PASSWORD_DEFAULT);
        $stmtUpdate = $conexion_pdo->prepare("UPDATE admin SET clave = ? WHERE usuario = ?");

        if ($stmtUpdate->execute([$passwordHasheada, $usuarioActual])) {
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

        // Buscar el usuario de entrada (puede ser un usuario específico o el primero que no sea admin)
        // Opción 1: Si tienes un usuario específico llamado "entrada"
        $usuarioEntrada = 'admin'; // Ajusta este valor según tu necesidad

        $stmt = $conexion_pdo->prepare("SELECT id FROM admin WHERE usuario = ?");
        $stmt->execute([$usuarioEntrada]);

        if (!$stmt->fetch()) {
            handleError("Usuario de entrada no encontrado");
        }

        // Actualizar contraseña del usuario de entrada
        $passwordHasheada = password_hash($passwordNueva, PASSWORD_DEFAULT);
        $stmtUpdate = $conexion_pdo->prepare("UPDATE admin SET clave = ? WHERE usuario = ?");

        if ($stmtUpdate->execute([$passwordHasheada, $usuarioEntrada])) {
            if ($stmtUpdate->rowCount() > 0) {
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