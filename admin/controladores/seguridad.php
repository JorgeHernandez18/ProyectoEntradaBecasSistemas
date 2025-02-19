<?php
// Verificar si existe la sesión 'datos_login'
if (!isset($_SESSION['datos_login'])) {
    header("location: ../../vistas/formularios/index.php");
    exit();
}

$arregloUsuario = $_SESSION['datos_login'];

include "../../modelo/conexion.php";

// Si el usuario no es administrador, redirigir
if ($arregloUsuario['nivel'] != 'admin') {
    header("location: ../../vistas/formularios/index.php");
    exit();
}
?>