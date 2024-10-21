<?php
include "../controladores/seguridad.php";
// Inicializa la consulta base y el array de parámetros
$baseQuery = "FROM registro_computo WHERE 1=1";
$params = array();

// Maneja el filtrado por fecha
if(isset($_GET['from_date']) && isset($_GET['to_date']) && !empty($_GET['from_date']) && !empty($_GET['to_date']))
{
    $from_date = $_GET['from_date'];
    $to_date = $_GET['to_date'];

    // Ajusta la fecha final para incluir todo el día
    $to_date = date('Y-m-d', strtotime($to_date . ' +1 day'));

    $baseQuery .= " AND entrada >= ? AND entrada < ?";
    $params[] = $from_date;
    $params[] = $to_date;
}

// Maneja la búsqueda por término
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
if (!empty($busqueda)) {
    $baseQuery .= " AND (nombre LIKE ? OR 
                         codigo LIKE ? OR 
                         programa LIKE ? OR 
                         facultad LIKE ? OR 
                         correo LIKE ? OR 
                         equipo LIKE ?)";
    $params = array_merge($params, array_fill(0, 6, "%$busqueda%"));
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
$totalRegistros = mysqli_fetch_assoc($totalRegistrosQuery)['total'];

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
$stmt = $conexion->prepare("SELECT * $baseQuery ORDER BY id DESC LIMIT ?, ?");
if (!empty($params)) {
    $types = str_repeat('s', count($params)) . 'ii';
    $stmt->bind_param($types, ...[...$params, $inicio, $registrosPorPagina]);
} else {
    $stmt->bind_param('ii', $inicio, $registrosPorPagina);
}
$stmt->execute();
$resultado = $stmt->get_result();

// Si es una solicitud AJAX, devuelve solo los datos de la tabla
if(isset($_GET['ajax'])) {
    $output = '';
    while($f = mysqli_fetch_array($resultado)){
        $output .= "<tr>
            <td>
                <div class='d-flex px-2 py-1'>
                    <div>
                        <img src='../assets/img/user.jpg' class='avatar avatar-sm me-3 border-radius-lg' alt='user1'>
                    </div>
                    <div class='d-flex flex-column justify-content-center'>
                        <h6 class='mb-0 text-sm'>{$f['nombre']}</h6>
                        <p class='text-xs text-secondary mb-0'>{$f['correo']}</p>
                    </div>
                </div>
            </td>
            <td>
                <p class='text-xs font-weight-bold mb-0'>{$f['codigo']}</p>
            </td>
            <td>
                <p class='text-xs font-weight-bold mb-0'>{$f['programa']}</p>
            </td>
            <td>
                <p class='text-xs font-weight-bold mb-0'>{$f['facultad']}</p>
            </td>
            <td>
                <p class='text-xs font-weight-bold mb-0'>{$f['equipo']}</p>
            </td>
            <td>
                <p class='text-xs font-weight-bold mb-0'>{$f['entrada']}</p>
            </td>
            <td>
                <p class='text-xs font-weight-bold mb-0'>{$f['salida']}</p>
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