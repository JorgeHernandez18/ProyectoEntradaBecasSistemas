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


</head>

<body class="bg-gray-200">
  <!-- mensaje de entrada y salida exitosa-->
  <div id="successMessageEntrada" class="success-message">
    <p>Entrada registrada exitosamente</p>
  </div>
  <div id="successMessageSalida" class="success-message">
    <p>Salida registrada exitosamente</p>
  </div>
  <div id="successMessageSalida2" class="success-message">
    <p>Salida registrada exitosamente</p>
  </div>
  <!-- incluye el header -->  
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg blur border-radius-xl top-0 z-index-3 shadow position-absolute my-3 py-2 start-0 end-0 mx-4">
          <div class="container-fluid ps-2 pe-0">
            <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 ">
              <img src="../../favicon.ico" alt="Logo Biblioteca" class="me-2" style="height: 15px;">
              BIBLIOTECA EDUARDO COTE LAMUS - UFPS
            </a>
            <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon mt-2">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </span>
            </button>
            <div class="collapse navbar-collapse" id="navigation">
              <ul class="navbar-nav mx-auto">
                
              </ul>
              <ul class="navbar-nav d-lg-flex">
                <li class="nav-item d-flex align-items-center">
                  <!-- Cierra la sesion y lo envia al index -->
                  <a class="btn bg-gradient-success btn-sm mb-0 me-2" href="#" id="liberarEquipos">Liberar Equipos</a>
                </li>
                <li class="nav-item d-flex align-items-center">
                  <!-- Cierra la sesion y lo envia al index -->
                  <a class="btn btn-outline-primary btn-sm mb-0 me-2" href="../../controladores/cerrar_sesion.php">salir</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
        <!-- fin navbar -->
      </div>
    </div>
  </div>
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
                <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">REGISTRO SALA DE COMPUTO</h4>
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
                <p class="text-white text-center">Registros Del Día: <span id="registroDia"></span></p>
              </div>
              </div>
              <div class="card-body">
              <form id="registroComputoForm" method="post">
                <div class="nav-wrapper position-relative end-0">
                  <div class="btn-group nav nav-pills nav-fill p-1" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" name="radioOpciones" id="btnEntrada" value="entrada" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="btnEntrada">Entrada</label>

                    <input type="radio" class="btn-check" name="radioOpciones" id="btnSalida" value="salida" autocomplete="off">
                    <label class="btn btn-outline-primary" for="btnSalida">Salida</label>
                  </div>

                    <div class="btn-group nav  nav-fill p-1">
                      <select class="btn btn-outline-primary" id="equipo" name="equipo" required>
                          <option value="" disabled selected>Seleccione un equipo</option>
                      </select>
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
          <!-- Ventana flotante de información de equipos -->
          <div id="equiposOcupadosWindow" class="floating-window equipos-ocupados">
            <h5>Equipos Ocupados</h5>
            <ul id="listaEquiposOcupados"></ul>
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
  <script src="../js/registroComputoForm.js"></script>
  <script src="../js/fechayhora.js"></script>
  <script src="../js/sidenav.js"></script>
</body>
</html>