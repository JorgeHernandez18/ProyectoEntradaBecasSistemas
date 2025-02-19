<?php
session_start(); // Inicia la sesión o reanuda una sesión existente.

include "../modelo/conexion.php"; // Incluye el archivo de conexión a la base de datos.

if (isset($_POST['usuario']) && isset($_POST['password'])) {
    // Verifica que se hayan enviado los campos 'usuario' y 'password' a través de POST.

    // Prepara la consulta SQL para evitar inyección SQL
    $stmt = $conexion->prepare("SELECT * FROM becl_admin WHERE usuario = ? AND password = ?");
    
    // Genera el hash de la contraseña
    $passwordHash = sha1($_POST['password']);
    
    // Vincula los parámetros a la consulta
    $stmt->bind_param("ss", $_POST['usuario'], $passwordHash);
    
    // Ejecuta la consulta
    $stmt->execute();
    
    // Obtiene el resultado
    $resultado = $stmt->get_result();
    
    // Verifica si la consulta devolvió algún resultado (usuario y contraseña correctos)
    if ($resultado->num_rows > 0) {
        // Si hay resultados, obtiene la fila del usuario.
        $datos_usuario = $resultado->fetch_row();
        $nombre = $datos_usuario[1]; // Nombre del usuario (suponiendo que es el segundo campo en la fila).
        $id_usuario = $datos_usuario[0]; // ID del usuario (suponiendo que es el primer campo en la fila).
        $nivel = $datos_usuario[3]; // Nivel del usuario (suponiendo que es el cuarto campo en la fila).

        // Guarda la información del usuario en la sesión.
        $_SESSION['datos_login'] = array(
            'nombre' => $nombre,
            'id_usuario' => $id_usuario,
            'nivel' => $nivel
        );

        // Redirige al usuario a la página de registro.
        header("location: ../vistas/formularios/registro.php");
    } else {
        // Si no hay resultados, redirige al usuario a la página de inicio de sesión con un mensaje de error.
        header("location: ../vistas/formularios/index.php?error=Credenciales incorrectas");
    }

    // Cierra la declaración
    $stmt->close();
} else {
    // Si los campos 'usuario' o 'password' no están definidos, redirige al usuario a la página de inicio de sesión.
    header("location: ../vistas/formularios/index.php");
}
?>
