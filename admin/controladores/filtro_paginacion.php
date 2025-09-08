<?php
include "../controladores/seguridad.php";
// Inicializa la consulta base y el array de parámetros
$baseQuery = "FROM becarios_registro br LEFT JOIN becarios_info bi ON br.codigo = bi.codigo WHERE 1=1";
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
    $baseQuery .= " AND (br.nombre LIKE ? OR 
                         br.codigo LIKE ? OR 
                         br.correo LIKE ?)";
    $params = array_merge($params, array_fill(0, 3, "%$busqueda%"));
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
$stmt = $conexion->prepare("SELECT br.*, bi.foto " . $baseQuery . " ORDER BY br.id DESC LIMIT ?, ?");
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
    while($f = mysqli_fetch_array($resultado)){
        // Determinar la URL de la foto
        if (!empty($f['foto']) && file_exists('../assets/fotos_becarios/' . $f['foto'])) {
            $fotoUrl = '../assets/fotos_becarios/' . $f['foto'];
        } else {
            $fotoUrl = '../assets/img/user.jpg';
        }
        
        $salidaFormateada = $f['salida'] ? date('Y-m-d H:i', strtotime($f['salida'])) : 'En curso';
        if ($f['salida'] && isset($f['salida_automatica']) && $f['salida_automatica']) {
            $salidaFormateada .= ' <span class="badge badge-sm bg-gradient-warning">AUTO</span>';
        }
        $horasFormateadas = $f['horas_trabajadas'] ? number_format($f['horas_trabajadas'], 2) . ' hrs' : '-';
        
        $output .= "<tr>
            <td>
                <div class='d-flex px-2 py-1'>
                    <div>
                        <img src='$fotoUrl' class='avatar avatar-sm me-3 border-radius-lg' alt='user1'>
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
                <p class='text-xs font-weight-bold mb-0'>" . date('Y-m-d H:i', strtotime($f['entrada'])) . "</p>
            </td>
            <td>
                <p class='text-xs font-weight-bold mb-0'>$salidaFormateada</p>
            </td>
            <td>
                <p class='text-xs font-weight-bold mb-0'>$horasFormateadas</p>
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