<?php 
session_start();
//Cierra la sesion y redirige al index
unset($_SESSION['datos_login']);
header("location: ../vistas/formularios/index.php");
?>