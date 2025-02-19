<?php
    // Consulta para el total de registros
    $consultaTotal = $conexion->query("SELECT COUNT(*) as total FROM becl_registro") or die($conexion->error);
    $totalRegistros = $consultaTotal->fetch_assoc()['total'];

    // Consulta para los registros del día
    $consultaDia = $conexion->query("SELECT COUNT(*) as totalDia FROM becl_registro WHERE DATE(entrada) = CURDATE()") or die($conexion->error);
    $registrosDia = $consultaDia->fetch_assoc()['totalDia'];

    // Determinar el semestre actual
    $mesActual = date('n');
    if ($mesActual >= 1 && $mesActual <= 6) {
        // Primer semestre
        $inicioSemestre = date('Y') . '-01-01';
        $finSemestre = date('Y') . '-06-30';
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'];
    } else {
        // Segundo semestre
        $inicioSemestre = date('Y') . '-07-01';
        $finSemestre = date('Y') . '-12-31';
        $meses = ['Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    }

    // Consulta para contar los registros del semestre actual
    $consultaSemestre = $conexion->query("SELECT COUNT(*) as totalSemestre FROM becl_registro WHERE entrada BETWEEN '$inicioSemestre' AND '$finSemestre'") or die($conexion->error);
    $registrosSemestre = $consultaSemestre->fetch_assoc()['totalSemestre'];

    // Consulta para contar los registros de los funcionarios del semestre actual
    $consultaFuncionariosSemestre = $conexion->query("SELECT COUNT(*) as totalFuncionariosSemestre FROM becl_registro WHERE entrada BETWEEN '$inicioSemestre' AND '$finSemestre' AND codigo REGEXP '^0[0-9]{4}$'") or die($conexion->error);
    $registrosFuncionariosSemestre = $consultaFuncionariosSemestre->fetch_assoc()['totalFuncionariosSemestre'];


///////---------------------CONSULTAS GRAFICA DE REGISTROS DE LAS 7 CARRERAS MAS FRECIENTES ----------------/////////
// Consulta para obtener los 7 programas más frecuentes en el semestre actual
$consultaProgramas = $conexion->query("
    SELECT programa, COUNT(*) as total
    FROM becl_registro
    WHERE entrada BETWEEN '$inicioSemestre' AND '$finSemestre'
    GROUP BY programa
    ORDER BY total DESC
    LIMIT 7
") or die($conexion->error);

$programas = [];
$totales = []; // Inicializa correctamente el array de totales
$totalesProgramas = 0; // Inicializa el acumulador de totales
$totalPrograma = 0; // Inicializa el valor máximo de programa

while ($row = $consultaProgramas->fetch_assoc()) {
    $programas[] = $row['programa'];  // Guarda el programa
    $totales[] = $row['total'];  // Guarda el total de cada programa
    $totalesProgramas += $row['total'];  // Suma el total al acumulador

    // Encontrar el total más alto
    if ($row['total'] > $totalPrograma) {
        $totalPrograma = $row['total'];
    }
}

// Convertir los arrays a formato JSON para usar en JavaScript
$programasJSON = json_encode($programas);
$totalesJSON = json_encode($totales);


///////---------------------CONSULTAS GRAFICA DE REGISTROS POR MES----------------/////////

    // Consulta para contar los registros por mes en el semestre actual
    $consultaMensual = $conexion->query("
    SELECT MONTH(entrada) as mes, COUNT(*) as total
    FROM becl_registro
    WHERE entrada BETWEEN '$inicioSemestre' AND '$finSemestre'
    GROUP BY MONTH(entrada)
    ORDER BY MONTH(entrada)
    ") or die($conexion->error);

    $datosMensuales = array_fill(0, 6, 0); // Inicializar array con ceros para los 6 meses
    $totalVisitasSemestre = 0;
    $mesMasFrecuente = '';
    $maxVisitas = 0;

    while ($row = $consultaMensual->fetch_assoc()) {
        $mesIndex = $row['mes'] - ($mesActual <= 6 ? 1 : 7); // Ajustar índice según el semestre
        $datosMensuales[$mesIndex] = $row['total'];
        $totalVisitasSemestre += $row['total'];
        if ($row['total'] > $maxVisitas) {
            $maxVisitas = $row['total'];
            $mesMasFrecuente = $meses[$mesIndex];
        }
    }

    // Convertir a formato JSON para usar en JavaScript
    $datosJSON = json_encode($datosMensuales);
    $mesesJSON = json_encode($meses);

    // Información adicional para mostrar en la tarjeta
    $periodoSemestre = ($mesActual <= 6 ? "Enero" : "Julio") . " - " . ($mesActual <= 6 ? "Junio" : "Diciembre") . " " . date('Y');



///////---------------------CONSULTAS GRAFICA DE REGISTROS POR DIAS DE LA SEMANA----------------/////////

    // Obtener el lunes de esta semana y el día actual
    $inicioSemana = date('Y-m-d', strtotime('monday this week'));
    $hoy = date('Y-m-d');

    // Consulta para obtener el conteo por día de la semana actual, incluyendo hoy
    $consultaSemanal = $conexion->query("
        SELECT 
            DATE(entrada) as fecha,
            DAYOFWEEK(entrada) as dia_semana, 
            COUNT(*) as total
        FROM becl_registro
        WHERE DATE(entrada) BETWEEN '$inicioSemana' AND '$hoy'
        GROUP BY DATE(entrada), DAYOFWEEK(entrada)
        ORDER BY fecha ASC
    ") or die($conexion->error);

    // Inicializar array con los días de la semana
    $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    $totalesSemana = array_fill(0, 7, 0); // Inicializa con 0 visitas para cada día

    // Llenar con los datos reales y acumular visitas por día
    while ($row = $consultaSemanal->fetch_assoc()) {
        $index = date('N', strtotime($row['fecha'])) - 1; // 0 para lunes, 6 para domingo
        $totalesSemana[$index] += $row['total']; // Sumar en lugar de sobrescribir
        error_log("Fecha: " . $row['fecha'] . ", Total: " . $row['total'] . ", Índice: " . $index);
    }

    // Obtener solo los días hasta hoy
    $diaActual = date('N') - 1; // 0 para lunes, 6 para domingo
    $diasSemana = array_slice($diasSemana, 0, $diaActual + 1);
    $totalesSemana = array_slice($totalesSemana, 0, $diaActual + 1);

    // Preparar los datos para JSON
    $diasSemanaJSON = json_encode($diasSemana);
    $totalesSemanaJSON = json_encode($totalesSemana);

    // Para depuración
    error_log("Días de la semana: " . $diasSemanaJSON);
    error_log("Totales de la semana: " . $totalesSemanaJSON);
    error_log("Día actual: " . $diaActual);
    error_log("Inicio de la semana: " . $inicioSemana);
    error_log("Hoy: " . $hoy);
?>