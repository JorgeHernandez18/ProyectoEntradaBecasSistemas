<?php 
	$servidor="localhost";
	$nombreBd="ingreso";
	$usuario="root";
	$pass="";
	$conexion = new mysqli($servidor,$usuario,$pass,$nombreBd);
	if ($conexion -> connect_error) {
		die("no se pudo conectar");	
	}
?>