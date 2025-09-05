<?php
include "../controladores/seguridad.php";
include "../../modelo/conexion.php";

// Inicializa la consulta base para becarios
$baseQuery = "FROM becarios_info WHERE 1=1";
$params = array();

// Maneja el filtrado por estado
if(isset($_GET['estado']) && !empty($_GET['estado'])) {
    $estado = $_GET['estado'];
    $baseQuery .= " AND estado = ?";
    $params[] = $estado;
}

// Maneja el filtrado por fecha de inicio
if(isset($_GET['from_date']) && isset($_GET['to_date']) && !empty($_GET['from_date']) && !empty($_GET['to_date']))
{
    $from_date = $_GET['from_date'];
    $to_date = $_GET['to_date'];

    $baseQuery .= " AND fecha_inicio >= ? AND fecha_inicio <= ?";
    $params[] = $from_date;
    $params[] = $to_date;
}

// Maneja la búsqueda por término
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
if (!empty($busqueda)) {
    $baseQuery .= " AND (nombre_completo LIKE ? OR 
                         codigo LIKE ? OR 
                         correo LIKE ?)";
    $params = array_merge($params, array_fill(0, 3, "%$busqueda%"));
}

// Número de registros por página
$registrosPorPagina = 12;

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

// Obtener el tipo de orden desde la URL (si existe)
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'nombre';

// Definir la columna de ordenación
if ($orden === 'codigo') {
    $orderBy = "codigo DESC"; // Ordenar por código
} else {
    $orderBy = "nombre_completo ASC"; // Ordenar por nombre (por defecto)
}

// Consulta para obtener los registros de la página actual
$stmt = $conexion->prepare("SELECT * $baseQuery ORDER BY $orderBy LIMIT ?, ?");
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

    while($f = mysqli_fetch_array($resultado)){
    }
    echo json_encode([
        'totalPaginas' => $totalPaginas,
        'paginaActual' => $paginaActual,
        'totalRegistros' => $totalRegistros
    ]);
    exit;
}
?>