<?php
session_start();
include "../../modelo/conexion.php";
include "seguridad.php";

// Obtener datos del formulario
$cardnumber = $_POST['cardnumber'];
$fotoFile = $_FILES['fotoFile'];

// Verificar si se subió un archivo
if ($fotoFile['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../assets/foto_funcionario/'; // Carpeta donde se guardarán las imágenes
    $fileExtension = pathinfo($fotoFile['name'], PATHINFO_EXTENSION); // Obtener la extensión del archivo
    $fileName = $cardnumber . "." . $fileExtension; // Renombrar archivo con el código del funcionario
    $uploadFilePath = $uploadDir . $fileName; // Ruta completa del archivo

    // Mover el archivo subido a la carpeta de destino
    if (move_uploaded_file($fotoFile['tmp_name'], $uploadFilePath)) {
        // Verificar si el funcionario ya existe
        $query = "SELECT codigo FROM becl_funcionario WHERE codigo = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $cardnumber);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            // Si el funcionario existe, actualizar la foto sin mostrar mensaje
            $query = "UPDATE becl_funcionario SET foto = ? WHERE codigo = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("ss", $uploadFilePath, $cardnumber);
            $stmt->execute();
        } else {
            // Si el funcionario no existe, crear un nuevo registro con mensaje
            $query = "INSERT INTO becl_funcionario (codigo, foto) VALUES (?, ?)";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("ss", $cardnumber, $uploadFilePath);
            if ($stmt->execute()) {
                echo "Nuevo registro creado correctamente.";
            } else {
                echo "Error al crear el registro.";
            }
        }

        $stmt->close();
    }
}

$conexion->close();
?>