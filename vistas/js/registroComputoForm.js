document.addEventListener('DOMContentLoaded', function() {
    actualizarEstadoEquipos();
    actualizarListaEquipos();

    // Agregar event listener para el cambio de tipo de registro
    document.querySelectorAll('input[name="radioOpciones"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const esEntrada = this.value === 'entrada';
            document.getElementById('equipo').disabled = !esEntrada;
            if (!esEntrada) {
                document.getElementById('equipo').value = ''; // Limpiar selección al cambiar a salida
            }
        });
    });

    document.getElementById('liberarEquipos').addEventListener('click', function(event) {
        event.preventDefault();
        liberarTodosLosEquipos();
    });
});

document.getElementById('registroComputoForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const equipo = document.getElementById('equipo').value;
    const codigo = document.getElementById('codigo').value;
    const tipoRegistro = document.querySelector('input[name="radioOpciones"]:checked').value;

    console.log('Enviando datos:', { equipo, codigo, tipoRegistro }); // Agregar para depuración

    fetch('../../controladores/registro_entrada_computo.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            'equipo': equipo,
            'codigo': codigo,
            'radioOpciones': tipoRegistro
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) { 
            document.getElementById('registroDia').textContent = data.registroDia;
            actualizarVentanaFlotante(data);
            mostrarMensajeExito(data.tipo);
            document.getElementById('codigo').value = '';
            if (tipoRegistro === 'entrada') {
                document.getElementById('equipo').value = '';
            }
            actualizarEstadoEquipos();
            actualizarListaEquipos();
        } else {
            mostrarError(data.error);
            // Si el error es por préstamo activo, podrías manejarlo de forma especial
            if (data.error.includes("ya tiene un equipo prestado")) {
                // Por ejemplo, podrías resaltar el botón de salida
                document.getElementById('btnSalida').classList.add('destacado');
                setTimeout(() => {
                    document.getElementById('btnSalida').classList.remove('destacado');
                }, 3000);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError('Error en la solicitud');
    });
});

function actualizarVentanaFlotante(data) {
    document.getElementById('nombreEstudiante').textContent = data.nombre;
    document.getElementById('codigoEstudiante').textContent = data.codigo;
    document.getElementById('horaRegistro').textContent = data.hora;
    const tipoRegistroSpan = document.getElementById('tipoRegistro');
    tipoRegistroSpan.textContent = data.tipo;
    tipoRegistroSpan.className = data.tipo === 'entrada' ? 'tipo-registro-entrada' : 'tipo-registro-salida';
    document.getElementById('correoEstudiante').textContent = data.correo;
    document.getElementById('semestreEstudiante').textContent = data.semestre + '° Semestre';
    document.getElementById('horasSemanalesEstudiante').textContent = data.horas_semanales + ' horas/semana';
    document.getElementById('floatingWindow').classList.add('show');
}

function mostrarMensajeExito(tipo) {
    const successMessage = tipo === 'entrada' ? document.getElementById('successMessageEntrada') : document.getElementById('successMessageSalida');
    successMessage.style.display = 'block';
    successMessage.style.opacity = '1';
    setTimeout(() => {
        successMessage.style.opacity = '0';
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 500);
    }, 3000);
}

function mostrarError(mensaje) {
    const errorAlertFlow = document.getElementById('errorAlert');
    document.getElementById('errorMessage').textContent = 'Error en el registro: ' + mensaje;
    errorAlertFlow.style.display = 'block';
    errorAlertFlow.style.opacity = '1';
    setTimeout(() => {
        errorAlertFlow.style.opacity = '0';
        setTimeout(() => {
            errorAlertFlow.style.display = 'none';
        }, 600);
    }, 5000);
}

function actualizarEstadoEquipos() {
    fetch('../../controladores/obtener_estado_equipos.php')
        .then(response => response.json())
        .then(data => {
            const listaEquiposOcupados = document.getElementById('listaEquiposOcupados');
            listaEquiposOcupados.innerHTML = '';
            data.equiposOcupados.forEach(equipo => {
                const li = document.createElement('li');
                li.innerHTML = `<span class="equipo-numero">Equipo ${equipo.equipo}:</span> <span class="equipo-codigo">${equipo.codigo}</span>`;
                listaEquiposOcupados.appendChild(li);
            });
            document.getElementById('equiposOcupadosWindow').style.display = 'block';
        })
        .catch(error => console.error('Error al obtener estado de equipos:', error));
}


function actualizarListaEquipos() {
    console.log('Actualizando lista de equipos...');
    fetch('../../controladores/obtener_equipos_libres.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Respuesta recibida:', data);
            const selectEquipo = document.getElementById('equipo');
            selectEquipo.innerHTML = '<option value="" disabled selected>Seleccione un equipo</option>';
            if (data.equiposLibres && Array.isArray(data.equiposLibres)) {
                data.equiposLibres.forEach(equipo => {
                    const option = document.createElement('option');
                    option.value = equipo;
                    option.textContent = `Equipo ${equipo}`;
                    selectEquipo.appendChild(option);
                });
            } else {
                console.error('Formato de datos incorrecto:', data);
                mostrarError('Datos recibidos en formato incorrecto');
            }            
        })
        .catch(error => {
            console.error('Error al obtener equipos libres:', error);
            mostrarError('Error al actualizar la lista de equipos');
        });
}


function liberarTodosLosEquipos() {
    fetch('../../controladores/liberar_equipos.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarMensajeExito2(data.mensaje2);
                actualizarEstadoEquipos();
                actualizarListaEquipos();
            } else {
                mostrarError(data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error al liberar equipos');
        });
}

function mostrarMensajeExito2(mensaje2) {
    const successMessage = document.getElementById('successMessageSalida2');
    successMessage.textContent = mensaje2;
    successMessage.style.display = 'block';
    successMessage.style.opacity = '1';
    setTimeout(() => {
        successMessage.style.opacity = '0';
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 500);
    }, 3000);
}

function cerrarAlerta() {
    document.getElementById('errorAlert').style.display = 'none';
}