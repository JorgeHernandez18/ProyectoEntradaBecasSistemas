<?php
	$servidor="127.0.0.1";
	$nombreBd="becarios_sistemas";
	$usuario="root";
	$pass="";
	$conexion = new mysqli($servidor,$usuario,$pass,$nombreBd);
	if ($conexion -> connect_error) {
		die("no se pudo conectar");	
	}
	// Establecer el conjunto de caracteres a UTF-8
	$conexion->set_charset(charset: "utf8mb4");
?>