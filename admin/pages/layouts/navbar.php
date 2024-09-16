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
          
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">

              <div class="col">
                <input type="date" name="fecha_ingreso" class="btn btn-outline-secondary btn-sm mb-0 me-3"  placeholder="Fecha de Inicio" required>
              </div>
              <div class="col">
                <input type="date" name="fechaFin" class="btn btn btn-outline-secondary btn-sm mb-0 me-3" placeholder="Fecha Final" required>
              </div>
              <div class="col">
                <span class="btn bg-gradient-danger btn-sm mb-0 me-3" id="filtro">Filtrar</span>
              </div>

            <div class="input-group input-group-outline">
              <input type="text" id="searchInput" class="form-control" placeholder="Buscar..">
            </div>
            
          </div>
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
    </nav>