document.getElementById('registroForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Evita la recarga de la página

      const codigo = document.getElementById('codigo').value;
      const tipoRegistro = document.querySelector('input[name="radioOpciones"]:checked').value;

      // Enviar los datos al servidor usando fetch o XMLHttpRequest
      fetch('../../controladores/registro_entrada.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: new URLSearchParams({
              'codigo': codigo,
              'radioOpciones': tipoRegistro
          })
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) { 
              // Actualizar la ventana flotante con la información
              document.getElementById('nombreEstudiante').textContent = data.nombre;
              document.getElementById('codigoEstudiante').textContent = data.codigo;
              document.getElementById('horaRegistro').textContent = data.hora;
              const tipoRegistroSpan = document.getElementById('tipoRegistro');
              tipoRegistroSpan.textContent = data.tipo;

              // Aplicar la clase correspondiente según el tipo de registro
              if (data.tipo === 'entrada') {
                  tipoRegistroSpan.classList.add('tipo-registro-entrada');
                  tipoRegistroSpan.classList.remove('tipo-registro-salida');
              } else {
                  tipoRegistroSpan.classList.add('tipo-registro-salida');
                  tipoRegistroSpan.classList.remove('tipo-registro-entrada');
              }
              document.getElementById('programaEstudiante').textContent = data.programa;
              document.getElementById('facultadEstudiante').textContent = data.facultad;
              document.getElementById('floatingWindow').classList.add('show');

              // Mostrar la ventana flotante
                document.getElementById('floatingWindow').classList.add('show');

                 // Mostrar el mensaje de éxito correspondiente
                var successMessage = (data.tipo === 'entrada') 
                    ? document.getElementById('successMessageEntrada')
                    : document.getElementById('successMessageSalida');
                
                successMessage.style.display = 'block';
                successMessage.style.opacity = '1';

                // Ocultar el mensaje después de 3 segundos
                setTimeout(function() {
                    successMessage.style.opacity = '0';
                    setTimeout(function() {
                        successMessage.style.display = 'none';
                    }, 500);
                }, 3000);
              
              // Limpiar el campo de texto
              document.getElementById('codigo').value = '';
          } else {
              // Mostrar el mensaje de error
            document.getElementById('errorMessage').textContent = 'Error en el registro: ' + data.error;
            document.getElementById('errorAlert').style.display = 'block';
          }
      })
      .catch(error => {
        console.error('Error:', error);
        // Manejo de errores en caso de fallo de la solicitud
    });
});
function cerrarAlerta() {
  document.getElementById('errorAlert').style.display = 'none';
}
