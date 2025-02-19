<?php
session_start();
include "../controladores/seguridad.php";
include "../controladores/filtro_funcionarios.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../../favicon.ico">
  <link rel="icon" type="image/ico" href="../../favicon.ico">
  <title>
    BECL - UFPS
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
        <a class="navbar-brand m-0" href="http://biblioteca.ufps.edu.co/" target="_blank">
          <img src="../../favicon.ico" class="navbar-brand-img h-200" alt="main_logo">
          <span class="ms-1 font-weight-bold text-white">Biblioteca UFPS</span>
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
            <a class="nav-link text-white" href="../pages/registros.php">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="material-icons opacity-10">table_view</i>
              </div>
              <span class="nav-link-text ms-1">Registro Entrada</span>
            </a>
          </li>
          <li class="nav-item">
          <a class="nav-link text-white" href="../pages/registros_computo.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">computer</i>
            </div>
            <span class="nav-link-text ms-1">Registro Computo</span>
          </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white active bg-gradient-primary" href="../pages/funcionarios.php">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="material-icons opacity-10">face</i>
              </div>
              <span class="nav-link-text ms-1">Funcionarios BECL</span>
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
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Funcionarios BECL</li>
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
                  <h6 class="text-white text-capitalize mb-0">FUNCIONARIOS BECL</h6><h6 class="text-white text-capitalize mb-0">TOTAL REGISTROS: <span id="totalRegistros" class="text-white"><?php echo $_SESSION['totalRegistros']; ?></span></h6>
                  <!-- Botón para cambiar el orden -->
                  <button id="ordenarBtn" class="btn btn-success me-2">
                    <i class="material-icons">arrow_downward</i> Ordenar por Código
                  </button>
                </div>
              </div>
            </div>
            <div class="col-12 mt-4">
            <div class="row">
                <?php  
                while ($f = mysqli_fetch_array($resultado)) {
                    // Obtener la foto del funcionario desde la tabla becl_funcionario
                    $codigoFuncionario = $f['cardnumber']; // Código del funcionario

                    // Primero, buscar la foto del funcionario actual
                    $queryFoto = "SELECT foto FROM becl_funcionario WHERE codigo = ?";
                    $stmtFoto = $conexion->prepare($queryFoto);
                    $stmtFoto->bind_param("s", $codigoFuncionario);
                    $stmtFoto->execute();
                    $resultadoFoto = $stmtFoto->get_result();

                    // Verificar si el código existe en la base de datos
                    if ($resultadoFoto->num_rows > 0) {
                        // Si existe, obtener la foto
                        $fotoFuncionario = $resultadoFoto->fetch_assoc();
                        $urlFoto = $fotoFuncionario['foto']; // Usar la foto del funcionario
                    } else {
                        // Si no existe, buscar la foto predeterminada con código 11111
                        $queryFotoPredeterminada = "SELECT foto FROM becl_funcionario WHERE codigo = '11111'";
                        $stmtFotoPredeterminada = $conexion->prepare($queryFotoPredeterminada);
                        $stmtFotoPredeterminada->execute();
                        $resultadoFotoPredeterminada = $stmtFotoPredeterminada->get_result();

                        if ($resultadoFotoPredeterminada->num_rows > 0) {
                            // Si existe la foto predeterminada, obtenerla
                            $fotoPredeterminada = $resultadoFotoPredeterminada->fetch_assoc();
                            $urlFoto = $fotoPredeterminada['foto']; // Usar la foto predeterminada
                        } else {
                            // Si no existe la foto predeterminada, usar una imagen por defecto
                            $urlFoto = "https://img.freepik.com/vector-gratis/gradiente-azul-usuario_78370-4692.jpg?semt=ais_hybrid";
                        }
                    }
                ?>
                    <div class="col-xl-2 col-md-2 mb-xl-2 mb-2">
                        <div class="card card-blog card-plain">
                            <div class="card-header p-0 m-2">
                                <!-- Enlace alrededor de la foto -->
                                <a href="profile.php?cardnumber=<?php echo $f['cardnumber']; ?>" class="d-block shadow-xl border-radius-xl">
                                    <!-- Foto con efecto hover -->
                                    <img src="<?php echo $urlFoto; ?>" 
                                        alt="Foto de <?php echo $f['firstname'] . ' ' . $f['surname']; ?>" 
                                        style="width: 100%; height: 300px; object-fit: cover; border-radius: 8px; transition: transform 0.3s ease;">
                                </a>
                            </div>
                            <div class="card-body p-3">
                                <p class="mb-0 text-sm">Funcionario BECL</p>
                                <!-- Enlace alrededor del nombre -->
                                <a href="profile.php?cardnumber=<?php echo $f['cardnumber']; ?>" style="text-decoration: none; color: inherit;">
                                    <h5>
                                      <?php echo ucwords(strtolower($f['firstname'])); ?>
                                      <?php echo ucwords(strtolower($f['surname'])); ?>
                                    </h5>
                                </a>
                                <p class="mb-4 text-sm">
                                    Codigo: <?php echo $f['cardnumber']; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php  
                }
                ?>
            </div>
        </div>

        <!-- Estilos para el efecto hover -->
        <style>
            .card-header img:hover {
                transform: scale(1.05); /* Expande la imagen un 5% */
            }
        </style>
            <div class="col-12 mt-4">
              
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
      <style>
            #ordenarBtn {
          display: flex;
          align-items: center;
          gap: 5px;
          transition: background-color 0.3s ease;
        }

        #ordenarBtn:hover {
          background-color: #0056b3; /* Cambia el color al pasar el mouse */
        }
      </style>
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
    document.getElementById('ordenarBtn').addEventListener('click', function() {
      // Obtener el parámetro de orden actual desde la URL
      const urlParams = new URLSearchParams(window.location.search);
      const ordenActual = urlParams.get('orden');

      // Cambiar el orden entre 'nombre' y 'codigo'
      const nuevoOrden = ordenActual === 'codigo' ? 'nombre' : 'codigo';

      // Actualizar la URL con el nuevo orden y recargar la página
      urlParams.set('orden', nuevoOrden);
      window.location.search = urlParams.toString();
    });

    document.addEventListener('DOMContentLoaded', function() {
      const ordenarBtn = document.getElementById('ordenarBtn');
      const urlParams = new URLSearchParams(window.location.search);
      const ordenActual = urlParams.get('orden');

      if (ordenActual === 'codigo') {
        ordenarBtn.innerHTML = '<i class="material-icons">arrow_upward</i> Ordenar por Nombre';
      } else {
        ordenarBtn.innerHTML = '<i class="material-icons">arrow_downward</i> Ordenar por Código';
      }
    });
  </script>
</body>
</html>