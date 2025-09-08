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
    GESTIÓN DE BECARIOS - INGENIERÍA DE SISTEMAS UFPS
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
          <li class="nav-item">
            <a class="nav-link text-white" href="../pages/horarios.php">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="material-icons opacity-10">schedule</i>
              </div>
              <span class="nav-link-text ms-1">Gestión de Horarios</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="../pages/auto_salidas.php">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="material-icons opacity-10">timer</i>
              </div>
              <span class="nav-link-text ms-1">Auto Salidas</span>
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
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Gestión de Becarios</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Gestión de Becarios</h6>
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
      
      <!-- Mensajes de éxito o error -->
      <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="row">
          <div class="col-12">
            <div class="alert alert-<?php echo $_SESSION['tipo_mensaje'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
              <?php echo $_SESSION['mensaje']; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          </div>
        </div>
        <?php 
          unset($_SESSION['mensaje']);
          unset($_SESSION['tipo_mensaje']); 
        ?>
      <?php endif; ?>
      
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <div class="d-flex justify-content-between align-items-center px-3">
                  <h6 class="text-white text-capitalize mb-0">GESTIÓN DE BECARIOS</h6>
                  <div class="d-flex align-items-center">
                    <h6 class="text-white text-capitalize mb-0 me-3">TOTAL: <span id="totalRegistros" class="text-white"><?php echo $_SESSION['totalRegistros']; ?></span></h6>
                    <!-- Botón para agregar nuevo becario -->
                    <button class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#modalAgregarBecario">
                      <i class="material-icons">add</i> Nuevo Becario
                    </button>
                    <!-- Botón para cargar Excel -->
                    <button class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#modalCargarExcel">
                      <i class="material-icons">upload_file</i> Cargar Excel
                    </button>
                    <!-- Botón para cambiar el orden -->
                    <button id="ordenarBtn" class="btn btn-success">
                      <i class="material-icons">arrow_downward</i> Ordenar por Código
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 mt-4">
            <div class="row">
                <?php  
                while ($f = $resultado->fetch_assoc()) {
                    // Obtener la foto del becario o usar imagen por defecto
                    if (!empty($f['foto']) && file_exists('../assets/fotos_becarios/' . $f['foto'])) {
                        $urlFoto = '../assets/fotos_becarios/' . $f['foto'];
                    } else {
                        $urlFoto = "https://img.freepik.com/vector-gratis/gradiente-azul-usuario_78370-4692.jpg?semt=ais_hybrid";
                    }
                ?>
                    <div class="col-xl-2 col-md-2 mb-xl-2 mb-2">
                        <div class="card card-blog card-plain">
                            <div class="card-header p-0 m-2">
                                <!-- Enlace alrededor de la foto -->
                                <a href="profile.php?codigo=<?php echo $f['codigo']; ?>" class="d-block shadow-xl border-radius-xl">
                                    <!-- Foto con efecto hover -->
                                    <img src="<?php echo $urlFoto; ?>" 
                                        alt="Foto de <?php echo $f['nombre_completo']; ?>" 
                                        style="width: 100%; height: 300px; object-fit: cover; border-radius: 8px; transition: transform 0.3s ease;">
                                </a>
                            </div>
                            <div class="card-body p-3">
                                <p class="mb-0 text-sm">Becario - Ingeniería de Sistemas</p>
                                <!-- Enlace alrededor del nombre -->
                                <a href="profile.php?codigo=<?php echo $f['codigo']; ?>" style="text-decoration: none; color: inherit;">
                                    <h5><?php echo ucwords(strtolower($f['nombre_completo'])); ?></h5>
                                </a>
                                <p class="mb-2 text-sm">
                                    Código: <?php echo $f['codigo']; ?>
                                </p>
                                <p class="mb-2 text-sm">
                                    Estado: <span class="badge bg-<?php echo $f['estado'] == 'activo' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($f['estado']); ?></span>
                                </p>
                                <!-- Botones para editar, ver horarios y eliminar becario -->
                                <div class="d-flex gap-1 mb-2">
                                    <button class="btn btn-warning btn-sm flex-fill" onclick="editarBecario('<?php echo $f['codigo']; ?>')">
                                        <i class="material-icons">edit</i> Editar
                                    </button>
                                    <button class="btn btn-info btn-sm flex-fill" onclick="verHorarios('<?php echo $f['codigo']; ?>', '<?php echo addslashes($f['nombre_completo']); ?>')">
                                        <i class="material-icons">schedule</i> Horarios
                                    </button>
                                </div>
                                <div class="d-flex gap-1 mb-2">
                                    <button class="btn btn-success btn-sm flex-fill" onclick="exportarRegistrosBecario('<?php echo $f['codigo']; ?>', '<?php echo addslashes($f['nombre_completo']); ?>')">
                                        <i class="material-icons">download</i> Excel
                                    </button>
                                </div>
                                <button class="btn btn-danger btn-sm w-100" onclick="eliminarBecario('<?php echo $f['codigo']; ?>', '<?php echo addslashes($f['nombre_completo']); ?>')">
                                    <i class="material-icons">delete</i> Eliminar
                                </button>
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

    // Función para editar becario
    function editarBecario(codigo) {
      // Cargar datos del becario
      fetch(`../controladores/obtener_becario.php?codigo=${codigo}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('editCodigo').value = data.becario.codigo;
            document.getElementById('editCodigoDisplay').value = data.becario.codigo;
            document.getElementById('editNombre').value = data.becario.nombre_completo;
            document.getElementById('editCorreo').value = data.becario.correo;
            document.getElementById('editTelefono').value = data.becario.telefono || '';
            document.getElementById('editSemestre').value = data.becario.semestre || '';
            document.getElementById('editHorasSemanales').value = data.becario.horas_semanales || 20;
            document.getElementById('editFechaInicio').value = data.becario.fecha_inicio;
            document.getElementById('editEstado').value = data.becario.estado;
            
            // Manejar foto actual
            var fotoActual = document.getElementById('fotoActual');
            var eliminarBtn = document.getElementById('eliminarFoto');
            
            if (data.becario.foto && data.becario.foto !== '') {
              fotoActual.src = '../assets/fotos_becarios/' + data.becario.foto;
              fotoActual.style.display = 'block';
              eliminarBtn.style.display = 'inline-block';
            } else {
              fotoActual.style.display = 'none';
              eliminarBtn.style.display = 'none';
            }
            
            // Mostrar modal
            var modal = new bootstrap.Modal(document.getElementById('modalEditarBecario'));
            modal.show();
          } else {
            alert('Error al cargar datos del becario');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error al conectar con el servidor');
        });
    }

    // Función para vista previa de imagen
    function previewImage(input, previewId) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
          var preview = document.getElementById(previewId);
          preview.src = e.target.result;
          preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
      }
    }

    // Función para eliminar foto actual
    function eliminarFotoActual() {
      if (confirm('¿Estás seguro de que deseas eliminar la foto actual?')) {
        var codigo = document.getElementById('editCodigo').value;
        
        fetch('../controladores/eliminar_foto.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ codigo: codigo })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Ocultar foto actual y botón eliminar
            document.getElementById('fotoActual').style.display = 'none';
            document.getElementById('eliminarFoto').style.display = 'none';
            alert('Foto eliminada exitosamente');
          } else {
            alert('Error al eliminar la foto: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error al conectar con el servidor');
        });
      }
    }

    // Función para ver horarios del becario
    function verHorarios(codigo, nombre) {
      fetch(`../controladores/obtener_horarios_becario.php?codigo=${codigo}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('horariosNombreBecario').textContent = nombre;
            document.getElementById('horariosTotalHoras').textContent = data.total_horas_semanales;
            
            const tbody = document.getElementById('horariosTableBody');
            tbody.innerHTML = '';
            
            if (data.horarios.length === 0) {
              tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay horarios programados</td></tr>';
            } else {
              data.horarios.forEach(horario => {
                const row = `
                  <tr>
                    <td class="text-sm">${horario.dia_semana}</td>
                    <td class="text-sm">${horario.hora_inicio} - ${horario.hora_fin}</td>
                    <td class="text-sm">${horario.horas_asignadas} hrs</td>
                    <td class="text-sm">${horario.observaciones || '-'}</td>
                  </tr>
                `;
                tbody.innerHTML += row;
              });
            }
            
            var modal = new bootstrap.Modal(document.getElementById('modalVerHorarios'));
            modal.show();
          } else {
            alert('Error al cargar horarios del becario');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error al conectar con el servidor');
        });
    }

    // Función para eliminar becario
    function eliminarBecario(codigo, nombre) {
      if (confirm(`¿Estás seguro de que deseas eliminar al becario ${nombre}?\n\nEsta acción eliminará:\n- La información del becario\n- Todos sus registros de entrada/salida\n- Su foto (si tiene)\n\nEsta acción NO se puede deshacer.`)) {
        fetch('../controladores/eliminar_becario.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ codigo: codigo })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Becario eliminado exitosamente');
            window.location.reload(); // Recargar página para actualizar la lista
          } else {
            alert('Error al eliminar el becario: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error al conectar con el servidor');
        });
      }
    }
    
    // Función para exportar registros de un becario específico
    function exportarRegistrosBecario(codigo, nombre) {
      const params = new URLSearchParams();
      params.append('action', 'downloadExcel');
      params.append('codigo_becario', codigo);
      params.append('nombre_becario', nombre);
      
      // Redirigir al controlador de Excel con los parámetros del becario
      window.open('../controladores/excel.php?' + params.toString(), '_blank');
    }
  </script>

  <!-- Modal para Agregar Nuevo Becario -->
  <div class="modal fade" id="modalAgregarBecario" tabindex="-1" role="dialog" aria-labelledby="modalAgregarBecarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAgregarBecarioLabel">Agregar Nuevo Becario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="../controladores/agregar_becario.php" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="input-group input-group-outline mb-3">
                  <label class="form-label">Código</label>
                  <input type="text" name="codigo" class="form-control" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group input-group-outline mb-3">
                  <label class="form-label">Semestre</label>
                  <input type="number" name="semestre" class="form-control" min="1" max="10">
                </div>
              </div>
            </div>
            <div class="input-group input-group-outline mb-3">
              <label class="form-label">Nombre Completo</label>
              <input type="text" name="nombre_completo" class="form-control" required>
            </div>
            <div class="input-group input-group-outline mb-3">
              <label class="form-label">Correo Electrónico</label>
              <input type="email" name="correo" class="form-control" required>
            </div>
            <div class="input-group input-group-outline mb-3">
              <label class="form-label">Teléfono</label>
              <input type="tel" name="telefono" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Foto del Becario</label>
              <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(this, 'previewAgregar')">
              <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB.</small>
              <div class="mt-2">
                <img id="previewAgregar" src="#" alt="Vista previa" style="max-width: 150px; max-height: 150px; display: none; border-radius: 8px;">
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="input-group input-group-outline mb-3">
                  <label class="form-label">Horas Semanales</label>
                  <input type="number" name="horas_semanales" class="form-control" value="20" min="1" max="40">
                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group input-group-outline mb-3">
                  <label class="form-label">Fecha de Inicio</label>
                  <input type="date" name="fecha_inicio" class="form-control" required>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Agregar Becario</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal para Editar Becario -->
  <div class="modal fade" id="modalEditarBecario" tabindex="-1" role="dialog" aria-labelledby="modalEditarBecarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarBecarioLabel">Editar Becario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="../controladores/editar_becario.php" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <input type="hidden" id="editCodigo" name="codigo">
            <div class="row">
              <div class="col-md-6">
                <div class="input-group input-group-outline mb-3 is-filled">
                  <label class="form-label">Código</label>
                  <input type="text" id="editCodigoDisplay" class="form-control" readonly>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group input-group-outline mb-3 is-filled">
                  <label class="form-label">Semestre</label>
                  <input type="number" id="editSemestre" name="semestre" class="form-control" min="1" max="10">
                </div>
              </div>
            </div>
            <div class="input-group input-group-outline mb-3 is-filled">
              <label class="form-label">Nombre Completo</label>
              <input type="text" id="editNombre" name="nombre_completo" class="form-control" required>
            </div>
            <div class="input-group input-group-outline mb-3 is-filled">
              <label class="form-label">Correo Electrónico</label>
              <input type="email" id="editCorreo" name="correo" class="form-control" required>
            </div>
            <div class="input-group input-group-outline mb-3 is-filled">
              <label class="form-label">Teléfono</label>
              <input type="tel" id="editTelefono" name="telefono" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Foto del Becario</label>
              <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(this, 'previewEditar')">
              <small class="form-text text-muted">Dejar vacío para mantener la foto actual. Formatos: JPG, PNG, GIF. Max: 2MB.</small>
              <div class="mt-2">
                <img id="previewEditar" src="#" alt="Vista previa" style="max-width: 150px; max-height: 150px; display: none; border-radius: 8px;">
                <img id="fotoActual" src="#" alt="Foto actual" style="max-width: 150px; max-height: 150px; border-radius: 8px; margin-right: 10px;">
                <button type="button" id="eliminarFoto" class="btn btn-danger btn-sm" style="display: none;" onclick="eliminarFotoActual()">
                  <i class="material-icons">delete</i> Eliminar Foto
                </button>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="input-group input-group-outline mb-3 is-filled">
                  <label class="form-label">Horas Semanales</label>
                  <input type="number" id="editHorasSemanales" name="horas_semanales" class="form-control" min="1" max="40">
                </div>
              </div>
              <div class="col-md-4">
                <div class="input-group input-group-outline mb-3 is-filled">
                  <label class="form-label">Fecha de Inicio</label>
                  <input type="date" id="editFechaInicio" name="fecha_inicio" class="form-control">
                </div>
              </div>
              <div class="col-md-4">
                <div class="input-group input-group-outline mb-3 is-filled">
                  <label class="form-label">Estado</label>
                  <select id="editEstado" name="estado" class="form-control">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                    <option value="finalizado">Finalizado</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-warning">Guardar Cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal para Cargar Excel -->
  <div class="modal fade" id="modalCargarExcel" tabindex="-1" role="dialog" aria-labelledby="modalCargarExcelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCargarExcelLabel">Cargar Becarios desde Excel/CSV</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="../controladores/cargar_excel_becarios.php" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Archivo Excel/CSV</label>
              <input type="file" name="excel_file" class="form-control" accept=".csv,.xls,.xlsx" required>
              <div class="form-text">
                Formatos soportados: CSV, XLS, XLSX<br>
                <strong>Orden de columnas esperado:</strong><br>
                1. Código (obligatorio)<br>
                2. Nombre Completo (obligatorio)<br>
                3. Correo (obligatorio)<br>
                4. Teléfono (opcional)<br>
                5. Semestre (opcional)<br>
                6. Horas Semanales (opcional, por defecto 20)<br>
                7. Fecha de Inicio (opcional, por defecto hoy)
              </div>
            </div>
            
            <div class="alert alert-info">
              <i class="material-icons">info</i>
              <strong>Instrucciones:</strong>
              <ul class="mb-0 mt-2">
                <li>La primera fila debe contener encabezados</li>
                <li>Los códigos duplicados serán ignorados</li>
                <li>Se recomienda usar formato CSV para mejor compatibilidad</li>
                <li>Máximo 500 becarios por archivo</li>
              </ul>
            </div>
            
            <div class="alert alert-warning">
              <i class="material-icons">warning</i>
              <strong>Nota:</strong> Para archivos Excel (.xls, .xlsx), por favor conviértalos a CSV primero.
            </div>
            
            <div class="text-center">
              <a href="../../ejemplo_becarios.csv" download="ejemplo_becarios.csv" class="btn btn-outline-primary btn-sm">
                <i class="material-icons">download</i> Descargar Ejemplo CSV
              </a>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">
              <i class="material-icons">upload</i> Cargar Becarios
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal para Ver Horarios -->
  <div class="modal fade" id="modalVerHorarios" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Horarios de <span id="horariosNombreBecario"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <strong>Total de horas semanales programadas:</strong> <span id="horariosTotalHoras"></span> horas
          </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Día</th>
                  <th>Horario</th>
                  <th>Horas</th>
                  <th>Observaciones</th>
                </tr>
              </thead>
              <tbody id="horariosTableBody">
                <!-- Los horarios se cargan dinámicamente -->
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <a href="../pages/horarios.php" class="btn btn-primary">
            <i class="material-icons">edit</i> Gestionar Horarios
          </a>
        </div>
      </div>
    </div>
  </div>

</body>
</html>