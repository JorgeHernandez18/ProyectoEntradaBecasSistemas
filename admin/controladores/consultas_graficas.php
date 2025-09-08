<?php
    // Consulta para el total de registros
    $stmtTotal = $conexion->prepare("SELECT COUNT(*) as total FROM becarios_registro");
    $stmtTotal->execute();
    $resultTotal = $stmtTotal->get_result();
    $rowTotal = $resultTotal->fetch_assoc();
    $totalRegistros = $rowTotal['total'] ?? 0;

    // Consulta para los registros del día (usar CURRENT_DATE en lugar de CURDATE)
    $stmtDia = $conexion->prepare("SELECT COUNT(*) as totalDia FROM becarios_registro WHERE DATE(entrada) = CURRENT_DATE");
    $stmtDia->execute();
    $resultDia = $stmtDia->get_result();
    $rowDia = $resultDia->fetch_assoc();
    $registrosDia = $rowDia['totalDia'] ?? 0;

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
    $stmtSemestre = $conexion->prepare("SELECT COUNT(*) as totalSemestre FROM becarios_registro WHERE entrada BETWEEN ? AND ?");
    $stmtSemestre->bind_param("ss", $inicioSemestre, $finSemestre);
    $stmtSemestre->execute();
    $resultSemestre = $stmtSemestre->get_result();
    $rowSemestre = $resultSemestre->fetch_assoc();
    $registrosSemestre = $rowSemestre['totalSemestre'] ?? 0;

    // Consulta para total de horas trabajadas en el semestre
    $stmtHorasSemestre = $conexion->prepare("SELECT SUM(horas_trabajadas) as totalHoras FROM becarios_registro WHERE entrada BETWEEN ? AND ? AND horas_trabajadas IS NOT NULL");
    $stmtHorasSemestre->bind_param("ss", $inicioSemestre, $finSemestre);
    $stmtHorasSemestre->execute();
    $resultHorasSemestre = $stmtHorasSemestre->get_result();
    $rowHoras = $resultHorasSemestre->fetch_assoc();
    $horasSemestre = $rowHoras['totalHoras'] ?? 0;


///////---------------------CONSULTAS GRAFICA DE REGISTROS POR BECARIO MAS ACTIVO ----------------/////////
// Consulta para obtener los 7 becarios más activos en el semestre actual
$stmtProgramas = $conexion->prepare("
    SELECT nombre, COUNT(*) as total
    FROM becarios_registro
    WHERE entrada BETWEEN ? AND ?
    GROUP BY codigo, nombre
    ORDER BY total DESC
    LIMIT 7
");
$stmtProgramas->bind_param("ss", $inicioSemestre, $finSemestre);
$stmtProgramas->execute();
$resultProgramas = $stmtProgramas->get_result();

$programas = [];
$totales = []; // Inicializa correctamente el array de totales
$totalesProgramas = 0; // Inicializa el acumulador de totales
$totalPrograma = 0; // Inicializa el valor máximo de programa

while ($row = $resultProgramas->fetch_assoc()) {
    $programas[] = $row['nombre'];  // Guarda el nombre del becario
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

    // Consulta para sumar horas trabajadas por mes en el semestre actual
    $stmtMensual = $conexion->prepare("
    SELECT EXTRACT(MONTH FROM entrada) as mes, SUM(horas_trabajadas) as total
    FROM becarios_registro
    WHERE entrada BETWEEN ? AND ? AND horas_trabajadas IS NOT NULL
    GROUP BY EXTRACT(MONTH FROM entrada)
    ORDER BY EXTRACT(MONTH FROM entrada)
    ");
    $stmtMensual->bind_param("ss", $inicioSemestre, $finSemestre);
    $stmtMensual->execute();
    $resultMensual = $stmtMensual->get_result();

    $datosMensuales = array_fill(0, 6, 0); // Inicializar array con ceros para los 6 meses
    $totalVisitasSemestre = 0;
    $mesMasFrecuente = '';
    $maxVisitas = 0;

    while ($row = $resultMensual->fetch_assoc()) {
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

    // Consulta para obtener las horas trabajadas por día de la semana actual, incluyendo hoy
    $stmtSemanal = $conexion->prepare("
        SELECT 
            DATE(entrada) as fecha,
            EXTRACT(DOW FROM entrada) as dia_semana, 
            SUM(horas_trabajadas) as total
        FROM becarios_registro
        WHERE DATE(entrada) BETWEEN ? AND ? AND horas_trabajadas IS NOT NULL
        GROUP BY DATE(entrada), EXTRACT(DOW FROM entrada)
        ORDER BY fecha ASC
    ");
    $stmtSemanal->bind_param("ss", $inicioSemana, $hoy);
    $stmtSemanal->execute();
    $resultSemanal = $stmtSemanal->get_result();

    // Inicializar array con los días de la semana
    $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    $totalesSemana = array_fill(0, 7, 0); // Inicializa con 0 visitas para cada día

    // Llenar con los datos reales y acumular visitas por día
    while ($row = $resultSemanal->fetch_assoc()) {
        // PostgreSQL DOW: 0=domingo, 1=lunes, ... 6=sábado
        // Convertir a: 0=lunes, 1=martes, ... 6=domingo
        $dia_pg = $row['dia_semana']; // 0=domingo, 1=lunes, ... 6=sábado
        $index = ($dia_pg == 0) ? 6 : ($dia_pg - 1); // 0=lunes, 6=domingo
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