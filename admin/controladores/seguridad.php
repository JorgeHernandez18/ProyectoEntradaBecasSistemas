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

// Verificar salidas automáticas (se ejecuta cada 10 minutos automáticamente)
include_once "verificar_auto_salidas.php";
?>