<?php
session_start(); // Inicia la sesión o reanuda una sesión existente.

include_once "../modelo/conexion.php"; // Incluye el archivo de conexión a la base de datos.

if (isset($_POST['usuario']) && isset($_POST['password'])) {
    // Verifica que se hayan enviado los campos 'usuario' y 'password' a través de POST.

    // Prepara la consulta SQL para obtener el usuario
    $stmt = $conexion->prepare("SELECT * FROM becarios_admin WHERE usuario = ?");
    
    // Vincula el parámetro del usuario
    $stmt->bind_param("s", $_POST['usuario']);
    
    // Ejecuta la consulta
    $stmt->execute();
    
    // Obtiene el resultado
    $resultado = $stmt->get_result();
    
    // Verifica si el usuario existe y la contraseña es correcta
    if ($resultado->num_rows > 0) {
        $resultado->reset(); // Resetear el índice para PostgreSQL adapter
        $datos_usuario = $resultado->fetch_assoc();
        
        // Verifica la contraseña usando password_verify para bcrypt
        if (password_verify($_POST['password'], $datos_usuario['password'])) {
            // Si la contraseña es correcta, obtiene los datos del usuario
            $nombre = $datos_usuario['usuario'];
            $id_usuario = $datos_usuario['id'];
            $nivel = $datos_usuario['nivel'];

            // Guarda la información del usuario en la sesión.
            $_SESSION['datos_login'] = array(
                'nombre' => $nombre,
                'id_usuario' => $id_usuario,
                'nivel' => $nivel
            );

            // Redirige al usuario a la página de registro.
            header("location: ../vistas/formularios/registro.php");
        } else {
            // Si la contraseña es incorrecta
            header("location: ../vistas/formularios/index.php?error=Credenciales incorrectas");
        }
    } else {
        // Si el usuario no existe
        header("location: ../vistas/formularios/index.php?error=Credenciales incorrectas");
    }

    // Cierra la declaración
    $stmt->close();
} else {
    // Si los campos 'usuario' o 'password' no están definidos, redirige al usuario a la página de inicio de sesión.
    header("location: ../vistas/formularios/index.php");
}
?>
