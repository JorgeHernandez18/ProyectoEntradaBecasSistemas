<?php
session_start();
include "../controladores/seguridad.php";
include "../controladores/excel.php";
include "../controladores/filtro_paginacion.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../../favicon.ico">
  <link rel="icon" type="image/ico" href="../../favicon.ico">
  <title>
    Registros de Becarios - Ingeniería de Sistemas UFPS
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
  <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
</head>

<body class="g-sidenav-show  bg-gray-200">
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main">
      <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="https://ingsistemas.cloud.ufps.edu.co/" target="_blank">
          <img src="../../favicon.ico" class="navbar-brand-img h-200" alt="main_logo">
          <span class="ms-1 font-weight-bold text-white">Becarios Sistemas</span>
        </a>
      </div>
      <hr class="horizontal light mt-0 mb-2">
      <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link text-white" href="../pages/dashboard.php">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="material-icons opacity-10">dashboard</i>
              </div>
              <span class="nav-link-text ms-1">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white active bg-gradient-primary" href="../pages/registros.php">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="material-icons opacity-10">table_view</i>
              </div>
              <span class="nav-link-text ms-1">Registros de Becarios</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="../pages/funcionarios.php">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="material-icons opacity-10">school</i>
              </div>
              <span class="nav-link-text ms-1">Gestión de Becarios</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="../pages/horarios.php">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="material-icons opacity-10">schedule</i>
              </div>
              <span class="nav-link-text ms-1">Gestión de Horarios</span>
            </a>
          </li>
          <li class="nav-item mt-3">
            <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Cuenta</h6>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white " href="../../controladores/cerrar_sesion.php">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="material-icons opacity-10">login</i>
              </div>
              <span class="nav-link-text ms-1">Salir</span>
            </a>
          </li>
        </ul>
      </div>
  </aside>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Registro</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Registro</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center justify-content-end w-100">
            <form action="" method="GET" class="d-flex align-items-center">
              <div class="input-group input-group-outline me-2" style="width: 200px;">
                <input type="text" id="searchInput" name="busqueda" class="form-control" placeholder="Buscar.." value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>">
              </div>
              <div class="d-flex align-items-center me-2">
                <input type="date" name="from_date" class="btn btn-outline-secondary btn-sm mb-0 me-3" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?>" placeholder="Fecha de Inicio">
                <input type="date" name="to_date" class="btn btn-outline-secondary btn-sm mb-0 me-3" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?>" placeholder="Fecha Final">
              </div>
              <button type="submit" class="btn btn-sm bg-gradient-primary btn-sm mb-0 me-3">Filtrar</button>
            </form>
            <ul class="navbar-nav  justify-content-end">
              <li class="nav-item d-flex align-items-center">
                <a href="../../controladores/cerrar_sesion.php" class="nav-link text-body font-weight-bold px-0">
                  <i class="fa fa-sign-out me-sm-1"></i>
                  <span class="d-sm-inline d-none">Salir</span>
                </a>
              </li>
            </ul>
          </div>  
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <div class="d-flex justify-content-between align-items-center px-3">
                  <h6 class="text-white text-capitalize mb-0">REGISTRO DE ENTRADA</h6><h6 class="text-white text-capitalize mb-0">TOTAL REGISTROS: <span id="totalRegistros" class="text-white"></span></h6>
                  <button id="btnExportar" class="btn btn-success" onclick="descargarExcel()">
                    <i class="fas fa-file-excel fa-lg me-2" style="color: white;"></i>
                    <span class="d-none d-md-inline">Exportar a Excel</span>
                  </button>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table id="tabla" class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nombre</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Código</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Entrada</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Salida</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Horas Trabajadas</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php  
                    while($f = $resultado->fetch_assoc()){
                  ?>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div>
                            <?php
                            // Determinar la URL de la foto
                            if (!empty($f['foto']) && file_exists('../assets/fotos_becarios/' . $f['foto'])) {
                                $fotoUrl = '../assets/fotos_becarios/' . $f['foto'];
                            } else {
                                $fotoUrl = '../assets/img/user.jpg';
                            }
                            ?>
                            <img src="<?php echo $fotoUrl; ?>" class="avatar avatar-sm me-3 border-radius-lg" alt="user1">
                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?php echo $f['nombre']; ?></h6>
                            <p class="text-xs text-secondary mb-0"><?php echo $f['correo']; ?></p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0"><?php echo $f['codigo']; ?></p>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0"><?php echo date('Y-m-d H:i', strtotime($f['entrada'])); ?></p>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">
                          <?php 
                          if ($f['salida']) {
                            echo date('Y-m-d H:i', strtotime($f['salida']));
                            if (isset($f['salida_automatica']) && $f['salida_automatica']) {
                              echo ' <span class="badge badge-sm bg-gradient-warning">AUTO</span>';
                            }
                          } else {
                            echo 'En curso';
                          }
                          ?>
                        </p>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0"><?php echo $f['horas_trabajadas'] ? number_format($f['horas_trabajadas'], 2) . ' hrs' : '-'; ?></p>
                      </td>
                    </tr>
                    <?php  
                    }
                    ?>
                  </tbody>
                </table>
              </div>
              <!-- "total registros de la busqueda" -->
                    
              <!-- Paginación -->
              <div class="row">
              </span>
                <div class="col-12">
                
                  <nav aria-label="Page navigation example">
                  
                  
                    <ul class="pagination justify-content-center">
                      <!-- Botón de "Anterior" -->
                      <li class="page-item <?php if ($paginaActual <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="?pagina=<?php echo max(1, $paginaActual - 1); ?>" tabindex="-1">
                          <span class="material-icons">keyboard_arrow_left</span>
                          <span class="sr-only"></span>
                        </a>
                      </li>

                      <!-- Páginas -->
                      <?php
                      // Rango de páginas a mostrar
                      $rango = 2; // Cambia este valor si quieres mostrar más o menos páginas

                      // Página inicial y final para mostrar
                      $paginaInicio = max(1, $paginaActual - $rango);
                      $paginaFin = min($totalPaginas, $paginaActual + $rango);

                      for ($i = $paginaInicio; $i <= $paginaFin; $i++) {
                        echo '<li class="page-item ' . ($i == $paginaActual ? 'active' : '') . '"><a class="page-link" href="?pagina=' . $i . '">' . $i . '</a></li>';
                      }
                      ?>

                      <!-- Botón de "Siguiente" -->
                      <li class="page-item <?php if ($paginaActual >= $totalPaginas) echo 'disabled'; ?>">
                        <a class="page-link" href="?pagina=<?php echo min($totalPaginas, $paginaActual + 1); ?>">
                          <span class="material-icons">keyboard_arrow_right</span>
                          <span class="sr-only"></span>
                        </a>
                      </li>
                    </ul>
                  </nav>
                </div>
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
  <script src="../assets/js/busqueda.js"></script>
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

  <!-- links para exportar a excel -->
  <script src="https://unpkg.com/xlsx@0.16.9/dist/xlsx.full.min.js"></script>
  <script src="https://unpkg.com/file-saverjs@latest/FileSaver.min.js"></script>
  <script src="https://unpkg.com/tableexport@latest/dist/js/tableexport.min.js"></script>
  
  <script>
    // Llenar el total de registros desde PHP
    document.addEventListener('DOMContentLoaded', function() {
      const totalRegistros = <?php echo $_SESSION['totalRegistros'] ?? 0; ?>;
      document.getElementById('totalRegistros').textContent = totalRegistros;
    });
    
    // Función para exportar a Excel
    function descargarExcel() {
      // Obtener parámetros de filtro actuales
      const urlParams = new URLSearchParams(window.location.search);
      const params = new URLSearchParams();
      params.append('action', 'downloadExcel');
      
      // Agregar filtros si existen
      if (urlParams.get('from_date')) params.append('from_date', urlParams.get('from_date'));
      if (urlParams.get('to_date')) params.append('to_date', urlParams.get('to_date'));
      if (urlParams.get('busqueda')) params.append('busqueda', urlParams.get('busqueda'));
      
      // Redirigir al controlador de Excel
      window.open('../controladores/excel.php?' + params.toString(), '_blank');
    }
  </script>
</body>
</html>