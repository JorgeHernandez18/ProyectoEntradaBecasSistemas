<?php
$arregloUsuario = $_SESSION['datos_login'];
include "../../modelo/conexion.php";

// Si no existe sesión, redirige al index
if (!isset($_SESSION['datos_login'])) {
    header("location: ../../vistas/formularios/index.php");
    exit();
}

// Si no es administrador, redirige al index
if ($arregloUsuario['nivel'] != 'admin') {
    header("location: ../../vistas/formularios/index.php");
    exit();
}
?>