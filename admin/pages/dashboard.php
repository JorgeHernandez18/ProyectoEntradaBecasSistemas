<?php
  session_start();
  include "../controladores/seguridad.php";
  include "../../modelo/conexion.php";
  include "../controladores/consultas_graficas.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../../favicon.ico">
  <link rel="icon" type="image/ico" href="../../favicon.ico">
  <title>
    ADMIN SISTEMA BECARIOS - INGENIERÍA DE SISTEMAS UFPS
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />
  <!-- Nepcha Analytics (nepcha.com) -->
</head>

<body class="g-sidenav-show  bg-gray-200">
  <?php include "layouts/header.php"; ?>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php include "layouts/navbar.php"; ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
              <i class="material-icons opacity-10">person</i>
              </div>
              <div class="text-end pt-1">
                <p class="text-lg mb-0 text-capitalize">Total Registros</p>
                <h4 class="mb-0"><?php echo number_format($totalRegistros ?? 0); ?></h4>
              </div>
            </div>
            <div class="card-footer p-3"></div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">person</i>
              </div>
              <div class="text-end pt-1">
                <p class="text-lg mb-0 text-capitalize">Registros del día</p>
                <h4 class="mb-0"><?php echo number_format($registrosDia ?? 0); ?></h4>
              </div>
            </div>
            <div class="card-footer p-3"></div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">person</i>
              </div>
              <div class="text-end pt-1">
                <p class="text-lg mb-0 text-capitalize">Registros del semestre</p>
                <h4 class="mb-0"><?php echo number_format($registrosSemestre ?? 0); ?></h4>
              </div>
            </div>
            <div class="card-footer p-3"></div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">person</i>
              </div>
              <div class="text-end pt-1">
                <p class="text-lg mb-0 text-capitalize">Horas trabajadas</p>
                <h4 class="mb-0"><?php echo number_format($horasSemestre ?? 0, 1); ?> hrs</h4>
              </div>
            </div>
            <div class="card-footer p-3"></div>
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-lg-12 col-md-6 mt-4 mb-4">
          <div class="card z-index-2 ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-bars" class="chart-canvas" height="300"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0">Becarios Más Activos</h6>
              <p class="text-sm">Estadísticas del semestre actual</p>
              <hr class="dark horizontal">
              <div class="d-flex">
                <i class="material-icons text-sm my-auto me-1">group</i>
                <p class="mb-0 text-sm">Total de registros: <span id="totalVisitas"></span></p>
              </div>
              <div class="d-flex mt-2">
                <i class="material-icons text-sm my-auto me-1">person</i>
                <p class="mb-0 text-sm">Becario más activo: <span id="programaMasFrecuente"></span></p>
              </div>
              <div class="d-flex mt-2">
                <i class="material-icons text-sm my-auto me-1">date_range</i>
                <p class="mb-0 text-sm">Período: <span id="periodoSemestre"></span></p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6 mt-4 mb-4">
          <div class="card z-index-2  ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-info shadow-info border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-line" class="chart-canvas" height="270"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0">Horas Trabajadas Mensuales</h6>
              <p class="text-sm">Estadísticas del semestre actual</p>
              <hr class="dark horizontal">
              <div class="d-flex">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm">Total horas del semestre: <span id="totalVisitasSemestre"></span> hrs</p>
              </div>
              <div class="d-flex mt-2">
                <i class="material-icons text-sm my-auto me-1">calendar_today</i>
                <p class="mb-0 text-sm">Mes con más horas: <span id="mesMasFrecuente"></span></p>
              </div>
              <div class="d-flex mt-2">
                <i class="material-icons text-sm my-auto me-1">date_range</i>
                <p class="mb-0 text-sm">Período: <span id="periodoSemestre2"></span></p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 mt-4 mb-3">
          <div class="card z-index-2 ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-line-tasks" class="chart-canvas" height="270"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0">Horas Trabajadas Semanales</h6>
              <p class="text-sm">Estadísticas de la semana actual</p>
              <hr class="dark horizontal">
              <div class="d-flex">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm">Total horas de la semana: <span id="totalVisitasSemana"></span> hrs</p>
              </div>
              <div class="d-flex mt-2">
                <i class="material-icons text-sm my-auto me-1">calendar_today</i>
                <p class="mb-0 text-sm">Día con más horas: <span id="diaMasFrecuente"></span></p>
              </div>
              <div class="d-flex mt-2">
                <i class="material-icons text-sm my-auto me-1">date_range</i>
                <p class="mb-0 text-sm">Período: <span id="periodoSemana"></span></p>
              </div>
            </div>
          </div>
        </div>
      </div>
 
      <?php include 'layouts/footer.php'; ?>
    </div>
  </main>
  <?php include 'layouts/configuradorInterfaz.php'; ?>
  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/chartjs.min.js"></script>
   <!--   Core JS Files graficas.php  -->
  <?php include '../assets/js/graficas.php'; ?>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/material-dashboard.min.js?v=3.1.0"></script>
</body>

</html>