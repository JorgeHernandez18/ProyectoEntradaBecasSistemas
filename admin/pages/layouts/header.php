
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="#">
        <img src="../../favicon.ico" class="navbar-brand-img h-200" alt="main_logo">
        <span class="ms-1 font-weight-bold text-white">Becarios Sistemas</span>
      </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
<?php
// Obtener el nombre de la página actual para activar el menú correspondiente
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
// Debug: verificar la detección de página
// echo "<!-- DEBUG: currentPage = $currentPage -->";
?>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-white <?php echo ($currentPage == 'dashboard') ? 'active bg-gradient-primary' : ''; ?>" href="../pages/dashboard.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white <?php echo ($currentPage == 'registros') ? 'active bg-gradient-primary' : ''; ?>" href="../pages/registros.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">table_view</i>
            </div>
            <span class="nav-link-text ms-1">Registros de Becarios</span>
          </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo ($currentPage == 'funcionarios') ? 'active bg-gradient-primary' : ''; ?>" href="../pages/funcionarios.php">
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
          <a class="nav-link text-white <?php echo ($currentPage == 'configuracion') ? 'active bg-gradient-primary' : ''; ?>" href="../pages/configuracion.php">
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


