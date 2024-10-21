<?php
  //si no es administrados con permiso de entrada lo envia al index
  if ($arregloUsuario['nivel']!='entrada') {
    header("location: index.php"); 
  }

  //si es un administrador lo envia al dashboard
  if ($arregloUsuario['nivel']=='admin') {
    header("location: ../../admin/pages/dashboard.php"); 
  }

  //si es un administrador lo envia al dashboard
  if ($arregloUsuario['nombre']=='computobecl') {
    header("location: registro_computo.php"); 
  }
?>