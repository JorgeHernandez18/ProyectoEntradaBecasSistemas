<?php
  // Si no es administrador o con permiso de entrada lo envía al index
  if ($arregloUsuario['nivel'] != 'entrada' && $arregloUsuario['nivel'] != 'admin') {
    header("location: ../../vistas/formularios/index.php");
    exit();
  }

  // Si es administrador y está intentando acceder al registro, redirigir al dashboard
  if ($arregloUsuario['nivel'] == 'admin' && strpos($_SERVER['REQUEST_URI'], 'vistas/formularios/registro.php') !== false) {
    header("location: ../../admin/pages/dashboard.php");
    exit();
  }
?>