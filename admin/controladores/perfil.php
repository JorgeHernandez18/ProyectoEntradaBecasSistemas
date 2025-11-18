<?php
if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];

    // Consulta para obtener los datos del becario desde la tabla usuarios
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE codigo = ?");
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $empleado = $resultado->fetch_assoc();
    } else {
        echo "Becario no encontrado.";
        exit;
    }
} else {
    echo "No se ha seleccionado un becario.";
    exit;
}
?>
