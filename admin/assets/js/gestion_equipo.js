    function abrirModalGestionEquipos() {
        var modal = document.getElementById('modalGestionEquipos');
        var bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
        cargarEquipos();
    }

    function cargarEquipos() {
        fetch('../controladores/gestion_equipo/obtener_equipos.php')
        .then(response => response.json())
        .then(equipos => {
        const listaEquipos = document.getElementById('listaEquipos');
        listaEquipos.innerHTML = '';
        equipos.forEach(equipo => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.textContent = `Equipo ${equipo.equipo} - ${equipo.estado ? 'Ocupado' : 'Libre'}`;
            const botonEliminar = document.createElement('button');
            botonEliminar.className = 'btn btn-danger btn-sm';
            botonEliminar.textContent = 'Eliminar';
            botonEliminar.onclick = () => confirmarEliminacion(equipo.id);
            if (equipo.estado) {
            botonEliminar.disabled = true;
            botonEliminar.title = 'No se puede eliminar un equipo ocupado';
            }
            li.appendChild(botonEliminar);
            listaEquipos.appendChild(li);
        });
        });
    }

    // Actualizar esta función también para reflejar el nuevo estado
    document.getElementById('formAgregarEquipo').onsubmit = function(e) {
        e.preventDefault();
        const numeroEquipo = document.getElementById('numeroEquipo').value;
        fetch('../controladores/gestion_equipo/agregar_equipo.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `numero=${numeroEquipo}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            mostrarMensaje('¡Éxito!', 'Equipo agregado correctamente', 'success');
            document.getElementById('numeroEquipo').value = '';
            cargarEquipos();
          } else {
            mostrarMensaje('Error', 'Error al agregar el equipo: ' + data.message, 'error');
          }
        });
    };

    function eliminarEquipo(id) {
        if (confirm('¿Estás seguro de que quieres eliminar este equipo?')) {
            fetch('../controladores/gestion_equipo/eliminar_equipo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}`
            })
            .then(response => response.json())
            .then(data => {
            if (data.success) {
                alert('Equipo eliminado correctamente');
                cargarEquipos();
            } else {
                alert(data.message); // Mostrará el mensaje de error del servidor
            }
            });
        }
    }
    document.querySelector('[data-bs-dismiss="modal"]').addEventListener('click', function() {
        var modal = bootstrap.Modal.getInstance(document.getElementById('modalGestionEquipos'));
        modal.hide();
    });
    function mostrarMensaje(titulo, texto, tipo) {
        Swal.fire({
          title: titulo,
          text: texto,
          icon: tipo,
          confirmButtonText: 'OK',
          confirmButtonColor: '#3085d6'
        });
    }
    function confirmarEliminacion(id) {
        Swal.fire({
          title: '¿Estás seguro?',
          text: "No podrás revertir esta acción",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
            eliminarEquipo(id);
          }
        });
      }

      function eliminarEquipo(id) {
        fetch('../controladores/gestion_equipo/eliminar_equipo.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `id=${id}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            mostrarMensaje('¡Éxito!', 'Equipo eliminado correctamente', 'success');
            cargarEquipos();
          } else {
            mostrarMensaje('Error', data.message, 'error');
          }
        });
      }