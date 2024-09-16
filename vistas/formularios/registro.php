<?php
  // inicia sesion
  session_start(); 
  // incluye la base de datos
  include "../../modelo/conexion.php";
  // extrae los datos de la sesion
  $arregloUsuario = $_SESSION['datos_login'];
  
  //si no existe sesion lo manda al index
  if (!isset($_SESSION['datos_login'])) {
    header("location: index.php");
    exit();
  }

  //si no es administrados con permiso de entrada lo envia al index
  if ($arregloUsuario['nivel']!='entrada') {
    header("location: index.php"); 
  }

  //si es un administrador lo envia al dashboard
  if ($arregloUsuario['nivel']=='admin') {
    header("location: ../../admin/pages/dashboard.php"); 
  }
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../../favicon.ico">
  <link rel="icon" type="image/ico" href="../../favicon.ico">
  <title>
    ENTRADA BIBLIOTECA UFPS
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
  <!-- Nucleo Icons -->
  <link href="../../admin/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../../admin/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="../../admin/assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />

  <!-- estilos css especificos utilizados en el formulario -->
  <link id="pagestyle" href="../css/style.css" rel="stylesheet" />

  <!-- Font Awesome icons (free version)-->
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
  <!-- Google fonts-->
  <link rel="preconnect" href="https://fonts.gstatic.com" />
  <link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&amp;display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&amp;display=swap" rel="stylesheet" />

  <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
</head>

<body class="bg-gray-200">
  <!-- mensaje de entrada y salida exitosa-->
  <div id="successMessageEntrada" class="success-message">
    <p>Entrada registrada exitosamente</p>
  </div>
  <div id="successMessageSalida" class="success-message">
    <p>Salida registrada exitosamente</p>
  </div>
  <!-- incluye el header -->  
  <?php include '../Estructuras/header.php'; ?>
  <!-- Cuadro de alerta de error -->
  <div id="errorAlert" class="error-alert">
    <p id="errorMessage"></p>
    <button onclick="cerrarAlerta()">Cerrar</button>
  </div>
  </div>
  <main class="main-content  mt-0">
    <div class="page-header align-items-start min-vh-100" style="background-image: url('../../admin/assets/img/fondo.jpg');">
      <span class="mask bg-gradient-dark opacity-6"></span>
      <div class="container my-auto">
        <div class="row">
          <div class="col-lg-4 col-md-8 col-12 mx-auto">
            <div class="card z-index-0 fadeIn3 fadeInBottom">
              <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">REGISTRO DE ENTRADA</h4>
                <div class="widget">
                  <div class="fecha">
                    <p id="diaSemana" class="diaSemana"></p>,
                    <p id="dia" class="dia"></p>
                    <p>de</p>
                    <p id="mes" class="mes"></p>
                    <p>del</p>
                    <p id="year" class="year"></p>
                  </div>
                  <div class="reloj">
                    <span id="horas" class="horas"></span>
                    <span>:</span>
                    <span id="minutos" class="minutos"></span>
                    <span>:</span>
                    <div class="caja-segundos">
                      <span id="segundos" class="segundos"></span>
                      <span id="ampm" class="ampm"></span>
                    </div>
                  </div>
                </div>
              </div>
              </div>
              <div class="card-body">
              <form id="registroForm" method="post">
                <div class="nav-wrapper position-relative end-0">
                  <div class="btn-group nav nav-pills nav-fill p-1" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" name="radioOpciones" id="btnEntrada" value="entrada" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="btnEntrada">Entrada</label>

                    <input type="radio" class="btn-check" name="radioOpciones" id="btnSalida" value="salida" autocomplete="off">
                    <label class="btn btn-outline-primary" for="btnSalida">Salida</label>
                  </div>
                </div>
                <div class="row input-group-newsletter">
                  <div class="col">
                    <input class="form-control" id="codigo" name="codigo" type="text" required="true" placeholder="Ingrese código" />
                  </div>
                  <div class="col-auto">
                    <button class="btn btn-primary" id="submitButton" type="submit">Registrar</button>
                  </div>
                </div>
              </form>
              </div>
            </div>
          </div>
          <!-- Ventana flotante de información -->
          <div id="floatingWindow" class="floating-window">
            <h5>Información del Estudiante</h5>
            <p><span>Nombre:</span> <strong id="nombreEstudiante"></strong></p>
            <p><span>Código:</span> <strong id="codigoEstudiante"></strong></p>
            <p><span>Hora de Registro:</span> <strong id="horaRegistro"></strong></p>
            <p><span>Tipo de Registro:</span> <strong id="tipoRegistro" class="registro-tipo"></strong></p>
            <p><span>Programa:</span> <strong id="programaEstudiante"></strong></p>
            <p><span>Facultad:</span> <strong id="facultadEstudiante"></strong></p>
          </div>
        </div>
      </div>
      <!-- incluye el footer -->
      <?php include '../Estructuras/footer.php'; ?>
    </div>
  </main>
  <!--   Core JS Files   -->
  <script src="../../admin/assets/js/core/popper.min.js"></script>
  <script src="../../admin/assets/js/core/bootstrap.min.js"></script>
  <script src="../../admin/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../../admin/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../js/registroForm.js"></script>
  <script src="../js/fechayhora.js"></script>
  <script src="../js/sidenav.js"></script>
</body>
</html>