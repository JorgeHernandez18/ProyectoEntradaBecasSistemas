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
    Configuración - Ingeniería de Sistemas UFPS
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
  <?php include "layouts/header.php"; ?>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Configuración</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Configuración</h6>
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
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Gestión de Contraseñas</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="container-fluid py-4">
                <div class="row">
                  <!-- Cambiar contraseña de Admin -->
                  <div class="col-lg-6 col-md-12 mb-4">
                    <div class="card h-100">
                      <div class="card-header pb-0">
                        <div class="row">
                          <div class="col-lg-8 col-md-7">
                            <h6>Cambiar Mi Contraseña</h6>
                            <p class="text-sm mb-0">
                              <i class="fa fa-check text-info" aria-hidden="true"></i>
                              <span class="font-weight-bold ms-1">Administrator</span> cambiar tu contraseña actual
                            </p>
                          </div>
                          <div class="col-lg-4 col-md-5 my-auto text-end">
                            <div class="dropdown float-lg-end pe-4">
                              <i class="material-icons cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                                more_vert
                              </i>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card-body px-0 pb-2">
                        <div class="px-4">
                          <form id="formCambiarAdmin">
                            <div class="input-group input-group-outline mb-3">
                              <label class="form-label">Contraseña Actual</label>
                              <input type="password" name="password_actual" class="form-control" required>
                            </div>
                            <div class="input-group input-group-outline mb-3">
                              <label class="form-label">Nueva Contraseña</label>
                              <input type="password" name="password_nueva" class="form-control" required minlength="6" id="passwordNuevaAdmin">
                            </div>
                            <div class="password-requirements mb-3" id="requirementsAdmin" style="display: none;">
                              <small class="text-muted">La contraseña debe contener:</small>
                              <ul class="text-sm">
                                <li id="lengthAdmin" class="text-danger">Al menos 6 caracteres</li>
                                <li id="uppercaseAdmin" class="text-danger">Al menos una mayúscula</li>
                                <li id="lowercaseAdmin" class="text-danger">Al menos una minúscula</li>
                                <li id="numberAdmin" class="text-danger">Al menos un número</li>
                              </ul>
                            </div>
                            <div class="input-group input-group-outline mb-3">
                              <label class="form-label">Confirmar Nueva Contraseña</label>
                              <input type="password" name="password_confirmar" class="form-control" required minlength="6">
                            </div>
                            <div class="text-center">
                              <button type="submit" class="btn btn-lg bg-gradient-primary btn-lg w-100 mt-4 mb-0">
                                <i class="material-icons">lock</i>&nbsp;&nbsp;Cambiar Mi Contraseña
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Cambiar contraseña de usuario entrada -->
                  <div class="col-lg-6 col-md-12 mb-4">
                    <div class="card h-100">
                      <div class="card-header pb-0">
                        <div class="row">
                          <div class="col-lg-8 col-md-7">
                            <h6>Cambiar Contraseña de Entrada</h6>
                            <p class="text-sm mb-0">
                              <i class="fa fa-check text-warning" aria-hidden="true"></i>
                              <span class="font-weight-bold ms-1">Usuario Entrada</span> cambiar contraseña del usuario de nivel entrada
                            </p>
                          </div>
                          <div class="col-lg-4 col-md-5 my-auto text-end">
                            <div class="dropdown float-lg-end pe-4">
                              <i class="material-icons cursor-pointer" id="dropdownTable2" data-bs-toggle="dropdown" aria-expanded="false">
                                more_vert
                              </i>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card-body px-0 pb-2">
                        <div class="px-4">
                          <form id="formCambiarEntrada">
                            <div class="input-group input-group-outline mb-3">
                              <label class="form-label">Nueva Contraseña para Usuario Entrada</label>
                              <input type="password" name="password_nueva_entrada" class="form-control" required minlength="6" id="passwordNuevaEntrada">
                            </div>
                            <div class="password-requirements mb-3" id="requirementsEntrada" style="display: none;">
                              <small class="text-muted">La contraseña debe contener:</small>
                              <ul class="text-sm">
                                <li id="lengthEntrada" class="text-danger">Al menos 6 caracteres</li>
                                <li id="uppercaseEntrada" class="text-danger">Al menos una mayúscula</li>
                                <li id="lowercaseEntrada" class="text-danger">Al menos una minúscula</li>
                                <li id="numberEntrada" class="text-danger">Al menos un número</li>
                              </ul>
                            </div>
                            <div class="input-group input-group-outline mb-3">
                              <label class="form-label">Confirmar Nueva Contraseña</label>
                              <input type="password" name="password_confirmar_entrada" class="form-control" required minlength="6">
                            </div>
                            <div class="alert alert-warning text-white" role="alert">
                              <strong>Importante:</strong> Esta acción cambiará la contraseña del usuario que registra las entradas y salidas de becarios.
                            </div>
                            <div class="text-center">
                              <button type="submit" class="btn btn-lg bg-gradient-warning btn-lg w-100 mt-4 mb-0">
                                <i class="material-icons">person</i>&nbsp;&nbsp;Cambiar Contraseña de Entrada
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
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
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/material-dashboard.min.js?v=3.1.0"></script>
  
  <script>
    function validatePassword(password) {
      const requirements = {
        length: password.length >= 6,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /\d/.test(password)
      };
      return requirements;
    }

    function updateRequirements(password, prefix) {
      const requirements = validatePassword(password);
      const requirementsDiv = document.getElementById('requirements' + prefix);
      
      if (password.length > 0) {
        requirementsDiv.style.display = 'block';
      } else {
        requirementsDiv.style.display = 'none';
      }
      
      Object.keys(requirements).forEach(key => {
        const element = document.getElementById(key + prefix);
        if (requirements[key]) {
          element.className = 'text-success';
        } else {
          element.className = 'text-danger';
        }
      });
    }

    function isPasswordStrong(password) {
      const requirements = validatePassword(password);
      return Object.values(requirements).every(req => req);
    }

    document.addEventListener('DOMContentLoaded', function() {
      // Validación en tiempo real para contraseña de admin
      document.getElementById('passwordNuevaAdmin').addEventListener('input', function() {
        updateRequirements(this.value, 'Admin');
      });

      // Validación en tiempo real para contraseña de entrada
      document.getElementById('passwordNuevaEntrada').addEventListener('input', function() {
        updateRequirements(this.value, 'Entrada');
      });
      // Formulario para cambiar contraseña del admin
      document.getElementById('formCambiarAdmin').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const passwordActual = this.querySelector('input[name="password_actual"]').value;
        const passwordNueva = this.querySelector('input[name="password_nueva"]').value;
        const passwordConfirmar = this.querySelector('input[name="password_confirmar"]').value;
        
        if (passwordNueva !== passwordConfirmar) {
          alert('Las contraseñas nuevas no coinciden.');
          return;
        }
        
        if (!isPasswordStrong(passwordNueva)) {
          alert('La nueva contraseña no cumple con todos los requisitos de seguridad.');
          return;
        }
        
        const formData = new FormData();
        formData.append('action', 'cambiar_admin');
        formData.append('password_actual', passwordActual);
        formData.append('password_nueva', passwordNueva);
        
        fetch('../controladores/cambiar_password.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Contraseña cambiada exitosamente.');
            this.reset();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          alert('Error de conexión: ' + error);
        });
      });
      
      // Formulario para cambiar contraseña del usuario entrada
      document.getElementById('formCambiarEntrada').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const passwordNuevaEntrada = this.querySelector('input[name="password_nueva_entrada"]').value;
        const passwordConfirmarEntrada = this.querySelector('input[name="password_confirmar_entrada"]').value;
        
        if (passwordNuevaEntrada !== passwordConfirmarEntrada) {
          alert('Las contraseñas no coinciden.');
          return;
        }
        
        if (!isPasswordStrong(passwordNuevaEntrada)) {
          alert('La nueva contraseña no cumple con todos los requisitos de seguridad.');
          return;
        }
        
        if (!confirm('¿Estás seguro de cambiar la contraseña del usuario de entrada? Esta acción afectará al sistema de registro.')) {
          return;
        }
        
        const formData = new FormData();
        formData.append('action', 'cambiar_entrada');
        formData.append('password_nueva', passwordNuevaEntrada);
        
        fetch('../controladores/cambiar_password.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Contraseña del usuario de entrada cambiada exitosamente.');
            this.reset();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          alert('Error de conexión: ' + error);
        });
      });
    });
  </script>
</body>

</html>