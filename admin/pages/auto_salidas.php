<?php
session_start();
include "../controladores/seguridad.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../../favicon.ico">
  <link rel="icon" type="image/ico" href="../../favicon.ico">
  <title>
    Auto Salidas - Ingeniería de Sistemas UFPS
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
</head>

<body class="g-sidenav-show  bg-gray-200">
  <?php include "layouts/header.php"; ?>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Auto Salidas</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Gestión de Auto Salidas</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center justify-content-end w-100">
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
        <!-- Panel de Control -->
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Control de Salidas Automáticas</h6>
              </div>
            </div>
            <div class="card-body px-4">
              <div class="row">
                <div class="col-md-6">
                  <p class="text-sm mb-3">
                    <i class="material-icons text-info">info</i>
                    El sistema marca automáticamente la salida de los becarios que no marcaron salida 30 minutos después de su horario programado.
                  </p>
                  <button class="btn btn-primary btn-sm" onclick="ejecutarVerificacionManual()">
                    <i class="material-icons">refresh</i> Ejecutar Verificación Manual
                  </button>
                </div>
                <div class="col-md-6">
                  <div class="alert alert-info">
                    <strong>Funcionamiento:</strong><br>
                    • Se ejecuta automáticamente cada 10 minutos<br>
                    • Solo procesa becarios con horarios programados<br>
                    • Marca salida automática a la hora programada + 30 min<br>
                    • Registra en observaciones que fue automática
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Registros con Salida Automática -->
        <div class="col-12">
          <div class="card">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-warning shadow-warning border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Registros con Salida Automática (Últimos 7 días)</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Becario</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Entrada</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Salida Auto</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Horas</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Observaciones</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                    // Consultar registros con salida automática de los últimos 7 días
                    $query = "SELECT br.*, bi.foto, bi.nombre_completo 
                              FROM becarios_registro br 
                              LEFT JOIN becarios_info bi ON br.codigo = bi.codigo 
                              WHERE br.salida_automatica = true 
                              AND br.entrada >= CURRENT_DATE - INTERVAL '7 days'
                              ORDER BY br.entrada DESC";
                    $resultado = $conexion->query($query);
                    
                    if ($resultado && $resultado->rowCount() > 0) {
                      while ($f = $resultado->fetch()) {
                        // Determinar la URL de la foto
                        if (!empty($f['foto']) && file_exists('../assets/fotos_becarios/' . $f['foto'])) {
                            $fotoUrl = '../assets/fotos_becarios/' . $f['foto'];
                        } else {
                            $fotoUrl = '../assets/img/user.jpg';
                        }
                  ?>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div>
                            <img src="<?php echo $fotoUrl; ?>" class="avatar avatar-sm me-3 border-radius-lg" alt="user">
                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($f['nombre'] ?? $f['nombre_completo'] ?? 'N/A'); ?></h6>
                            <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($f['codigo']); ?></p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0"><?php echo date('d/m H:i', strtotime($f['entrada'])); ?></p>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">
                          <?php echo date('d/m H:i', strtotime($f['salida'])); ?>
                          <span class="badge badge-sm bg-gradient-warning">AUTO</span>
                        </p>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0"><?php echo number_format($f['horas_trabajadas'], 2) . ' hrs'; ?></p>
                      </td>
                      <td style="max-width: 200px;">
                        <p class="text-xs mb-0" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" 
                           title="<?php echo htmlspecialchars($f['observaciones']); ?>">
                          <?php echo htmlspecialchars(substr($f['observaciones'], 0, 50)) . (strlen($f['observaciones']) > 50 ? '...' : ''); ?>
                        </p>
                      </td>
                    </tr>
                    <?php 
                      }
                    } else {
                      echo '<tr><td colspan="5" class="text-center">No hay registros con salidas automáticas en los últimos 7 días</td></tr>';
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php include 'layouts/footer.php'; ?>
    </div>
  </main>
  
  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
    
    function ejecutarVerificacionManual() {
      const btn = event.target;
      btn.disabled = true;
      btn.innerHTML = '<i class="material-icons">hourglass_empty</i> Procesando...';
      
      fetch('../../controladores/auto_salida.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(`Proceso completado:\nProcesados: ${data.data.procesados}\nMarcados: ${data.data.marcados}`);
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          alert('Error de conexión: ' + error);
        })
        .finally(() => {
          btn.disabled = false;
          btn.innerHTML = '<i class="material-icons">refresh</i> Ejecutar Verificación Manual';
        });
    }
  </script>
  <!-- Control Center for Material Dashboard -->
  <script src="../assets/js/material-dashboard.min.js?v=3.1.0"></script>
</body>

</html>