<?php 
session_start(); // Inicia la sesión o reanuda una sesión existente.

include "../modelo/conexion.php"; // Incluye el archivo de conexión a la base de datos.

if (isset($_POST['usuario']) && isset($_POST['password'])) {
    // Verifica que se hayan enviado los campos 'usuario' y 'password' a través de POST.

    // Consulta a la base de datos para verificar las credenciales del usuario.
    $resultado = $conexion->query(
        "SELECT * FROM admin WHERE usuario='" . $_POST['usuario'] . "' AND password='" . sha1($_POST['password']) . "'"
    ) or die($conexion->error); // Ejecuta la consulta y muestra el error en caso de fallo.

    // Verifica si la consulta devolvió algún resultado (usuario y contraseña correctos).
    if (mysqli_num_rows($resultado) > 0) {
        // Si hay resultados, obtiene la fila del usuario.
        $datos_usuario = mysqli_fetch_row($resultado);
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
} else {
    // Si los campos 'usuario' o 'password' no están definidos, redirige al usuario a la página de inicio de sesión.
    header("location: ../vistas/formularios/index.php");
}
?>