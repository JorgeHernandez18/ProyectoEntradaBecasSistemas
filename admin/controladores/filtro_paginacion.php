<?php
include "../controladores/seguridad.php";

// Inicializa la consulta base y el array de parámetros
// Nota: Ahora emparejamos registros de Ingreso con Salida
$baseQuery = "FROM registros ingreso
              LEFT JOIN registros salida
                ON ingreso.codigo = salida.codigo
                AND ingreso.fecha = salida.fecha
                AND salida.tipo = 'Salida'
                AND salida.id > ingreso.id
              LEFT JOIN usuarios u ON ingreso.codigo = u.codigo
              WHERE ingreso.tipo = 'Ingreso'";
$params = array();

// Maneja el filtrado por fecha
if(isset($_GET['from_date']) && isset($_GET['to_date']) && !empty($_GET['from_date']) && !empty($_GET['to_date']))
{
    $from_date = $_GET['from_date'];
    $to_date = $_GET['to_date'];

    $baseQuery .= " AND ingreso.fecha >= ? AND ingreso.fecha <= ?";
    $params[] = $from_date;
    $params[] = $to_date;
}

// Maneja la búsqueda por término
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
if (!empty($busqueda)) {
    $baseQuery .= " AND (ingreso.nombre LIKE ? OR ingreso.codigo LIKE ?)";
    $params = array_merge($params, array_fill(0, 2, "%$busqueda%"));
}

// Número de registros por página
$registrosPorPagina = 10;

// Obtén el número total de registros que coinciden con la búsqueda
$stmt = $conexion->prepare("SELECT COUNT(*) AS total $baseQuery");
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$totalRegistrosQuery = $stmt->get_result();
$rowTotal = $totalRegistrosQuery->fetch_assoc();
$totalRegistros = $rowTotal['total'] ?? 0;

// Guarda este valor en una variable de sesión para usarlo en la vista
$_SESSION['totalRegistros'] = $totalRegistros;

// Calcula el número total de páginas
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Obtén la página actual
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$paginaActual = max(1, min($paginaActual, $totalPaginas));

// Calcula el índice de inicio para la consulta SQL
$inicio = ($paginaActual - 1) * $registrosPorPagina;

// Consulta para obtener los registros de la página actual
// En este bloque, las horas trabajadas se calculan tomando la diferencia entre la fecha/hora de Ingreso y la de Salida.
// Se utiliza EXTRACT(EPOCH FROM (timestamp2 - timestamp1)) / 3600 para obtener los segundos transcurridos y luego dividir entre 3600 para obtener los decimales de horas.
// Por ejemplo, 6.89 horas equivale a 6 horas y 0.89*60 = 53 minutos aprox.
$stmt = $conexion->prepare("
    SELECT
        ingreso.id,
        ingreso.codigo,
        ingreso.nombre,
        ingreso.fecha as fecha_entrada,
        ingreso.hora as hora_entrada,
        salida.fecha as fecha_salida,
        salida.hora as hora_salida,
        salida.actividad,
        CASE
            WHEN salida.hora IS NOT NULL THEN
                EXTRACT(EPOCH FROM (
                    (salida.fecha || ' ' || salida.hora)::timestamp -
                    (ingreso.fecha || ' ' || ingreso.hora)::timestamp
                )) / 3600
            ELSE NULL
        END as horas_trabajadas
    " . $baseQuery . "
    ORDER BY ingreso.id DESC
    LIMIT ? OFFSET ?
");

if (!empty($params)) {
    $types = str_repeat('s', count($params)) . 'ii';
    $stmt->bind_param($types, ...[...$params, $registrosPorPagina, $inicio]);
} else {
    $stmt->bind_param('ii', $registrosPorPagina, $inicio);
}
$stmt->execute();
$resultado = $stmt->get_result();

// Si es una solicitud AJAX, devuelve solo los datos de la tabla
if(isset($_GET['ajax'])) {
    $output = '';
    while($f = $resultado->fetch_assoc()){
        // Formatear entrada
        $entradaFormateada = $f['fecha_entrada'] . ' ' . substr($f['hora_entrada'], 0, 5);

        // Formatear salida
        if ($f['hora_salida']) {
            $salidaFormateada = $f['fecha_salida'] . ' ' . substr($f['hora_salida'], 0, 5);
        } else {
            $salidaFormateada = 'En curso';
        }

        // Formatear horas trabajadas
        $horasFormateadas = $f['horas_trabajadas'] ? number_format($f['horas_trabajadas'], 2) . ' hrs' : '-';

        // Actividad
        $actividad = !empty($f['actividad']) ? htmlspecialchars($f['actividad']) : '-';

        $output .= "<tr>
            <td>
                <div class='d-flex px-2 py-1'>
                    <div class='d-flex flex-column justify-content-center'>
                        <h6 class='mb-0 text-sm'>{$f['nombre']}</h6>
                        <p class='text-xs text-secondary mb-0'>Código: {$f['codigo']}</p>
                    </div>
                </div>
            </td>
            <td>
                <p class='text-xs font-weight-bold mb-0'>{$f['codigo']}</p>
            </td>
            <td>
                <p class='text-xs font-weight-bold mb-0'>$entradaFormateada</p>
            </td>
            <td>
                <p class='text-xs font-weight-bold mb-0'>$salidaFormateada</p>
            </td>
            <td>
                <p class='text-xs font-weight-bold mb-0'>$horasFormateadas</p>
            </td>
            <td>
                <p class='text-xs mb-0' style='max-width: 200px; overflow: hidden; text-overflow: ellipsis;'>$actividad</p>
            </td>
        </tr>";
    }
    echo json_encode([
        'table' => $output,
        'totalPaginas' => $totalPaginas,
        'paginaActual' => $paginaActual,
        'totalRegistros' => $totalRegistros
    ]);
    exit;
}
?>
