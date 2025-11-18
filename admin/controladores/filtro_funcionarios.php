<?php
include "../controladores/seguridad.php";
include "../../modelo/conexion.php";

// Inicializa la consulta base para becarios
$baseQuery = "FROM usuarios WHERE 1=1";
$params = array();

// Maneja la búsqueda por término
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
if (!empty($busqueda)) {
    $baseQuery .= " AND (nombre LIKE ? OR codigo LIKE ?)";
    $params = array_merge($params, array_fill(0, 2, "%$busqueda%"));
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

// Obtener el tipo de orden desde la URL (si existe)
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'nombre';

// Definir la columna de ordenación
if ($orden === 'codigo') {
    $orderBy = "codigo DESC"; // Ordenar por código
} else {
    $orderBy = "nombre ASC"; // Ordenar por nombre (por defecto)
}

// Consulta para obtener los registros de la página actual
$stmt = $conexion->prepare("SELECT * " . $baseQuery . " ORDER BY " . $orderBy . " LIMIT ? OFFSET ?");
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
    while($f = $resultado->fetch_assoc()){
    }
    echo json_encode([
        'totalPaginas' => $totalPaginas,
        'paginaActual' => $paginaActual,
        'totalRegistros' => $totalRegistros
    ]);
    exit;
}
?>
