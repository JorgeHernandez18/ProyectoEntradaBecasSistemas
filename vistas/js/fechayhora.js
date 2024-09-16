function actualizarReloj() {
    var fecha = new Date();
    
    var diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    var meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    var diaSemana = diasSemana[fecha.getDay()];
    var dia = fecha.getDate();
    var mes = meses[fecha.getMonth()];
    var year = fecha.getFullYear();

    var horas = fecha.getHours();
    var minutos = fecha.getMinutes();
    var segundos = fecha.getSeconds();
    var ampm = horas >= 12 ? 'PM' : 'AM';
    
    // Formatear la hora
    horas = horas % 12;
    horas = horas ? horas : 12; // la hora 0 es 12 AM
    minutos = minutos < 10 ? '0' + minutos : minutos;
    segundos = segundos < 10 ? '0' + segundos : segundos;
    
    // Insertar en el DOM
    document.getElementById('diaSemana').textContent = diaSemana;
    document.getElementById('dia').textContent = dia;
    document.getElementById('mes').textContent = mes;
    document.getElementById('year').textContent = year;

    document.getElementById('horas').textContent = horas;
    document.getElementById('minutos').textContent = minutos;
    document.getElementById('segundos').textContent = segundos;
    document.getElementById('ampm').textContent = ampm;
}
// Actualizar cada segundo
setInterval(actualizarReloj, 1000);
actualizarReloj(); // Llamar inmediatamente para evitar espera de un segundo


