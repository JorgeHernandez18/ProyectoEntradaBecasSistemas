<?php
session_start();
include "../controladores/seguridad.php";
include "../controladores/perfil.php";
// Obtener el código del becario desde la URL
$codigo = $_GET['codigo'];

include "../controladores/calendario.php";

// Usar imagen por defecto (ya no tenemos fotos en la BD)
$urlFoto = "https://img.freepik.com/vector-gratis/gradiente-azul-usuario_78370-4692.jpg?semt=ais_hybrid";


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
   <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <!-- Material Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
  <!-- FullCalendar CSS -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
</head>

<body class="g-sidenav-show bg-gray-100">
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main">
      <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="http://biblioteca.ufps.edu.co/" target="_blank">
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
            <a class="nav-link text-white active bg-gradient-primary" href="../pages/funcionarios.php">
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
  <div class="main-content position-relative max-height-vh-100 h-100">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Perfil de Becario</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">INFORMACIÓN</h6>
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
    <div class="container-fluid px-2 px-md-4">
      <div class="page-header min-height-300 border-radius-xl mt-4" style="background-image: url('https://i0.wp.com/www.srg.com.co/wp-content/uploads/2017/12/UFPS-CUCUTA.png?resize=1200%2C439&ssl=1');">
        <span class="mask  bg-gradient-dark  opacity-6"></span>
      </div>
      <div class="card card-body mx-2 mx-md-2 mt-n6">
        <div class="row gx-4 mb-2">
          <div class="col-auto">
              <div class="avatar avatar-xl position-relative" style="width: 100px; height: 100px; overflow: hidden; border-radius: 10px;">
                  <img src="<?php echo $urlFoto; ?>" alt="profile_image" 
                      class="w-100 h-100 border-radius-lg shadow-sm" 
                      style="object-fit: cover;">
              </div>
          </div>
          <div class="col-auto my-auto">
            <div class="h-100">
              <h5 class="mb-1">
                <?php echo ucwords(strtolower($empleado['nombre'])); ?>
              </h5>
              <p class="mb-0 font-weight-normal text-sm">
                Becario - Ingeniería de Sistemas
              </p>
            </div>
          </div>
          <!-- Botón de configuración deshabilitado (no hay gestión de fotos en BD externa) -->

          <!-- Modal para la configuración -->
          <div class="modal fade" id="configModal" tabindex="-1" aria-labelledby="configModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="configModalLabel">Configuración de foto</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form id="fotoForm" enctype="multipart/form-data">
                    <div class="mb-3">
                      <label for="fotoFile" class="form-label">Subir foto</label>
                      <div class="file-upload">
                        <input type="file" class="file-upload-input" id="fotoFile" name="fotoFile" accept="image/*" required>
                        <label for="fotoFile" class="file-upload-label">
                          <i class="material-icons">cloud_upload</i>
                          <span>Arrastra o selecciona una imagen</span>
                        </label>
                      </div>
                      <div id="imagePreview" class="mt-3" style="display: none;">
                        <img id="previewImage" src="#" alt="Previsualización" style="max-width: 100%; border-radius: 10px;">
                      </div>
                    </div>

                    <style>
  .file-upload {
    position: relative;
    display: inline-block;
    width: 100%;
  }

  .file-upload-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
  }

  .file-upload-label {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    width: 100%;
    padding: 20px;
    border: 2px dashed #ced4da;
    border-radius: 10px;
    background-color: #f8f9fa;
    text-align: center;
    color: #495057;
    transition: border-color 0.3s ease;
  }

  .file-upload-label:hover {
    border-color: #80bdff;
  }

  .file-upload-label i {
    font-size: 2rem;
    margin-bottom: 10px;
  }

  .file-upload-label span {
    font-size: 1rem;
  }
</style>
   
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                  <button type="button" class="btn btn-primary" id="guardarFoto">Guardar</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-4">
            <div class="card card-plain h-100">
                <div class="card-body p-3">
                  <h5 class="mb-0">Información</h5>
                  <br>
                  <ul class="list-group">
                    <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Código:</strong> &nbsp; <?php echo $empleado['codigo']; ?></li>
                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Nombre:</strong> &nbsp; <?php echo ucwords(strtolower($empleado['nombre'])); ?></li>
                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Programa:</strong> &nbsp; INGENIERÍA DE SISTEMAS</li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-12 col-xl-8">
              <h5 class="mb-0">Registro de Entrada y Salida</h5>
              <br>
              <div id="calendar">
                
              </div> <!-- Contenedor del calendario -->
            </div>  
          </div>
        </div>
      </div>
    </div>
    <?php include 'layouts/footer.php'; ?>
  </div>
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
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
  <!-- FullCalendar JS -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales-all.min.js"></script> <!-- Para soporte de idiomas -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var eventos = <?php echo $eventosJson; ?>; // Datos de PHP

      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth', // Vista inicial (mes)
          locale: 'es', // Idioma español
          headerToolbar: {
              left: 'prev,next today',
              center: 'title',
              right: 'dayGridMonth,timeGridWeek,timeGridDay'
          },
          events: eventos, // Eventos cargados desde PHP
          eventContent: function(arg) {
              // Personaliza el contenido del evento
              return {
                  html: `<div style="background-color: ${arg.event.backgroundColor}; color: white; padding: 5px; border-radius: 3px;">
                          <b>${arg.event.title}</b>
                      </div>`
              };
          },
          eventTimeFormat: { // Oculta la hora automática de FullCalendar
              hour: '2-digit',
              minute: '2-digit',
              hour12: false,
              omitZeroMinute: false,
              meridiem: false
          }
      });

        calendar.render(); // Renderizar el calendario
    });
  </script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#guardarFoto').click(function() {
        const cardnumber = "<?php echo $cardnumber; ?>"; // Obtener el código del funcionario desde PHP
        const fotoFile = $('#fotoFile')[0].files[0]; // Obtener el archivo de imagen

        if (!fotoFile) {
          alert('Por favor, selecciona una imagen.');
          return;
        }

        // Crear un objeto FormData para enviar el archivo
        const formData = new FormData();
        formData.append('cardnumber', cardnumber);
        formData.append('fotoFile', fotoFile);

        // Enviar datos al servidor usando AJAX
        $.ajax({
          url: '../controladores/agregar_foto.php', // Archivo PHP que manejará la lógica
          type: 'POST',
          data: formData,
          processData: false, // No procesar los datos
          contentType: false, // No establecer el tipo de contenido
          success: function(response) {
            $('#configModal').modal('hide'); // Cerrar el modal
            location.reload(); // Recargar la página para ver los cambios
          },
          error: function(xhr, status, error) {
            alert('Error al guardar la foto: ' + error);
          }
        });
      });
    });
  </script>
  <script>
    document.getElementById('fotoFile').addEventListener('change', function(event) {
      const file = event.target.files[0];
      const previewImage = document.getElementById('previewImage');
      const imagePreview = document.getElementById('imagePreview');

      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          previewImage.src = e.target.result;
          imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
      } else {
        previewImage.src = '#';
        imagePreview.style.display = 'none';
      }
    });
  </script>
</body>

</html>