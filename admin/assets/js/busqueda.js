function buscarYPaginar(pagina = 1) {
    var busqueda = document.getElementById("searchInput").value;
    var fromDate = document.querySelector('input[name="from_date"]').value;
    var toDate = document.querySelector('input[name="to_date"]').value;
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var respuesta = JSON.parse(this.responseText);
            document.querySelector("#tabla tbody").innerHTML = respuesta.table;
            actualizarPaginacion(respuesta.totalPaginas, respuesta.paginaActual);
            // Actualizar el contador de registros
            document.querySelector("#totalRegistros").textContent = respuesta.totalRegistros;
        }
    };
    xhr.open("GET", "?ajax=1&busqueda=" + encodeURIComponent(busqueda) + 
             "&from_date=" + encodeURIComponent(fromDate) + 
             "&to_date=" + encodeURIComponent(toDate) + 
             "&pagina=" + pagina, true);
    xhr.send();
}

function actualizarPaginacion(totalPaginas, paginaActual) {
    var paginacion = document.querySelector(".pagination");
    paginacion.innerHTML = '';

    // Botón "Anterior"
    var liAnterior = document.createElement('li');
    liAnterior.className = "page-item " + (paginaActual <= 1 ? 'disabled' : '');
    liAnterior.innerHTML = `<a class="page-link" href="#" onclick="buscarYPaginar(${Math.max(1, paginaActual - 1)}); return false;">
        <span class="material-icons">keyboard_arrow_left</span>
        <span class="sr-only">Previous</span>
    </a>`;
    paginacion.appendChild(liAnterior);

    // Páginas
    var rango = 2;
    var paginaInicio = Math.max(1, paginaActual - rango);
    var paginaFin = Math.min(totalPaginas, paginaActual + rango);

    for (var i = paginaInicio; i <= paginaFin; i++) {
        var li = document.createElement('li');
        li.className = "page-item " + (i == paginaActual ? 'active' : '');
        li.innerHTML = `<a class="page-link" href="#" onclick="buscarYPaginar(${i}); return false;">${i}</a>`;
        paginacion.appendChild(li);
    }

    // Botón "Siguiente"
    var liSiguiente = document.createElement('li');
    liSiguiente.className = "page-item " + (paginaActual >= totalPaginas ? 'disabled' : '');
    liSiguiente.innerHTML = `<a class="page-link" href="#" onclick="buscarYPaginar(${Math.min(totalPaginas, paginaActual + 1)}); return false;">
        <span class="material-icons">keyboard_arrow_right</span>
        <span class="sr-only">Next</span>
    </a>`;
    paginacion.appendChild(liSiguiente);
}

// Agregar evento de escucha al campo de búsqueda
document.getElementById("searchInput").addEventListener("keyup", function() {
    buscarYPaginar();
});

// Agregar eventos de escucha a los campos de fecha
document.querySelector('input[name="from_date"]').addEventListener("change", function() {
    buscarYPaginar();
});
document.querySelector('input[name="to_date"]').addEventListener("change", function() {
    buscarYPaginar();
});

// Cargar los datos iniciales
buscarYPaginar();

// Función para descargar Excel
function descargarExcel() {
    var busqueda = document.getElementById("searchInput").value;
    var from_date = document.querySelector('input[name="from_date"]').value;
    var to_date = document.querySelector('input[name="to_date"]').value;
    
    var url = '../controladores/excel.php?action=downloadExcel' + 
              '&busqueda=' + encodeURIComponent(busqueda) + 
              '&from_date=' + encodeURIComponent(from_date) + 
              '&to_date=' + encodeURIComponent(to_date);
    
    window.location.href = url;
}