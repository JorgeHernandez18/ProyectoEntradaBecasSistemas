<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../../favicon.ico">
  <link rel="icon" type="image/ico" href="../../favicon.ico">
  <title>
    SISTEMA DE BECARIOS - INGENIERÍA DE SISTEMAS UFPS
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
  <!-- Nepcha Analytics (nepcha.com) -->
  <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
</head>

<body class="bg-gray-200">
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg blur border-radius-xl top-0 z-index-3 shadow position-absolute my-3 py-2 start-0 end-0 mx-4">
          <div class="container-fluid ps-2 pe-0">
            <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 ">
              <img src="../../favicon.ico" alt="Logo UFPS" class="me-2" style="height: 15px;">
              SISTEMA DE BECARIOS - INGENIERÍA DE SISTEMAS UFPS
            </a>
          </div>
        </nav>
        <!-- FIN DEL NAVBAR -->
      </div>
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
                  <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">INICIO DE SESION</h4>
                  <br>
                </div>
              </div>
              <div class="card-body">
                <br>
                <form action="../../controladores/check.php" method="post">
                  <div class="input-group input-group-outline my-3">
                    <input type="text" class="form-control" name="usuario" id="usuario" required="true" placeholder="Usuario" spellcheck="false" onkeypress="return valida(event)" onpaste="return false"">
                  </div>
                  <div class="input-group input-group-outline mb-3">
                    <input class="form-control" type="password" name="password" placeholder="Contraseña" id="password" required="true" autocomplete="off" spellcheck="false" onpaste="return false">
                  </div>
                  <div class="text-center">
                    <button type="submit" value="Iniciar sesión" name="iniciar" id="ini" class="btn bg-gradient-primary w-100 my-4 mb-2">Iniciar sesión</button>
                  </div>
                  <?php 
                    if (isset($_GET['error'])) {
                      // alerta de credenciales incorrectas
                      echo '<div class="alert alert-warning text-white col-12" role="alert">'.$_GET['error'].'</div>';
                    }
                  ?>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php include '../Estructuras/footer.php'; ?>
    </div>
  </main>
  <?php include '../../admin/pages/layouts/configuradorInterfaz.php'; ?>
  <!--   Core JS Files   -->
  <script src="../../admin/assets/js/core/popper.min.js"></script>
  <script src="../../admin/assets/js/core/bootstrap.min.js"></script>
  <script src="../../admin/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../../admin/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../js/sidenav.js"></script>
  <script src="../../admin/assets/js/material-dashboard.min.js?v=3.1.0"></script>
</body>

</html>