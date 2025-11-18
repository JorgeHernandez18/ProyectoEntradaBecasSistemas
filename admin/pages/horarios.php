<?php
session_start();
include "../controladores/seguridad.php";
include "../../modelo/conexion.php";

// Obtener todos los becarios activos
$stmtBecarios = $conexion->prepare("SELECT codigo, nombre_completo FROM becarios_info WHERE estado = ? ORDER BY nombre_completo");
$stmtBecarios->bind_param("s", $estado = 'activo');
$stmtBecarios->execute();
$resultadoBecarios = $stmtBecarios->get_result();

// Obtener horarios existentes con información del becario
$stmtHorarios = $conexion->prepare("SELECT bh.*, bi.nombre_completo 
                  FROM becarios_horarios bh 
                  JOIN becarios_info bi ON bh.codigo_becario = bi.codigo 
                  WHERE bh.activo = ? 
                  ORDER BY bi.nombre_completo, bh.dia_semana, bh.hora_inicio");
$stmtHorarios->bind_param("i", $activo = 1);
$stmtHorarios->execute();
$resultadoHorarios = $stmtHorarios->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../../favicon.ico">
  <link rel="icon" type="image/ico" href="../../favicon.ico">
  <title>
    Gestión de Horarios - Ingeniería de Sistemas UFPS
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
            <a class="nav-link text-white" href="../pages/registros.php">
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
          <li class="nav-item mt-3">
            <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Cuenta</h6>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="../pages/configuracion.php">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="material-icons opacity-10">settings</i>
              </div>
              <span class="nav-link-text ms-1">Configuración</span>
            </a>
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
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Horarios</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Gestión de Horarios</h6>
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
      <?php
      // Mostrar mensajes de éxito o error
      if (isset($_SESSION['mensaje'])) {
          $tipoMensaje = $_SESSION['tipo_mensaje'] ?? 'info';
          echo "<div class='alert alert-{$tipoMensaje} alert-dismissible fade show' role='alert'>";
          echo $_SESSION['mensaje'];
          echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
          echo "</div>";
          unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
      }
      ?>
      
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <div class="d-flex justify-content-between align-items-center px-3">
                  <h6 class="text-white text-capitalize mb-0">GESTIÓN DE HORARIOS</h6>
                  <div class="d-flex align-items-center">
                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalAgregarHorario">
                      <i class="material-icons">add_alarm</i> Nuevo Horario
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Becario</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Día</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Horario</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Horas</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                  $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                  
                  while($h = $resultadoHorarios->fetch_assoc()) {
                  ?>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?php echo $h['nombre_completo']; ?></h6>
                            <p class="text-xs text-secondary mb-0"><?php echo $h['codigo_becario']; ?></p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0"><?php echo $dias[$h['dia_semana']]; ?></p>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">
                          <?php echo date('H:i', strtotime($h['hora_inicio'])); ?> - 
                          <?php echo date('H:i', strtotime($h['hora_fin'])); ?>
                        </p>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0"><?php echo $h['horas_asignadas']; ?> hrs</p>
                      </td>
                      <td>
                        <span class="badge bg-<?php echo $h['activo'] ? 'success' : 'secondary'; ?>">
                          <?php echo $h['activo'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                      </td>
                      <td>
                        <div class="d-flex">
                          <button class="btn btn-warning btn-sm me-2" onclick="editarHorario(<?php echo $h['id']; ?>)">
                            <i class="material-icons">edit</i>
                          </button>
                          <button class="btn btn-danger btn-sm" onclick="eliminarHorario(<?php echo $h['id']; ?>, '<?php echo addslashes($h['nombre_completo']); ?>')">
                            <i class="material-icons">delete</i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  <?php
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
  
  <?php include 'layouts/configuradorInterfaz.php'; ?>
  
  <!-- Modal para Agregar Horario -->
  <div class="modal fade" id="modalAgregarHorario" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Agregar Nuevo Horario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="../controladores/agregar_horario.php" method="POST">
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <div class="input-group input-group-outline mb-3">
                  <label class="form-label">Becario</label>
                  <select name="codigo_becario" class="form-control" required>
                    <option value="">Seleccionar becario</option>
                    <?php
                    while($b = $resultadoBecarios->fetch_assoc()) {
                        echo "<option value='{$b['codigo']}'>{$b['nombre_completo']} ({$b['codigo']})</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group input-group-outline mb-3">
                  <label class="form-label">Día de la Semana</label>
                  <select name="dia_semana" class="form-control" required>
                    <option value="">Seleccionar día</option>
                    <option value="1">Lunes</option>
                    <option value="2">Martes</option>
                    <option value="3">Miércoles</option>
                    <option value="4">Jueves</option>
                    <option value="5">Viernes</option>
                    <option value="6">Sábado</option>
                    <option value="0">Domingo</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="input-group input-group-outline mb-3">
                  <label class="form-label">Hora Inicio</label>
                  <input type="time" name="hora_inicio" class="form-control" required>
                </div>
              </div>
              <div class="col-md-3">
                <div class="input-group input-group-outline mb-3">
                  <label class="form-label">Hora Fin</label>
                  <input type="time" name="hora_fin" class="form-control" required>
                </div>
              </div>
              <div class="col-12">
                <div class="input-group input-group-outline mb-3">
                  <label class="form-label">Observaciones</label>
                  <textarea name="observaciones" class="form-control" rows="3"></textarea>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Horario</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  
  <script>
    function editarHorario(id) {
      // TODO: Implementar modal de edición
      alert('Funcionalidad de edición en desarrollo');
    }
    
    function eliminarHorario(id, nombre) {
      if (confirm(`¿Estás seguro de que deseas eliminar este horario de ${nombre}?`)) {
        fetch('../controladores/eliminar_horario.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error al conectar con el servidor');
        });
      }
    }
    
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="../assets/js/material-dashboard.min.js?v=3.1.0"></script>
</body>
</html>