<?php
if (isset($_GET['cardnumber'])) {
    $cardnumber = $_GET['cardnumber'];

    // Consulta para obtener los datos del empleado
    $stmt = $conexion->prepare("SELECT * FROM vista_borrowers WHERE cardnumber = ?");
    $stmt->bind_param("s", $cardnumber);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $empleado = $resultado->fetch_assoc();
    } else {
        echo "Empleado no encontrado.";
        exit;
    }
} else {
    echo "No se ha seleccionado un empleado.";
    exit;
}
?>