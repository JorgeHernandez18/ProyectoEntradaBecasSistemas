<?php
session_start();
include "seguridad.php";
include "../../modelo/conexion.php";

header('Content-Type: application/json');

if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    
    try {
        // Obtener horarios del becario
        $query = "SELECT bh.*, bi.nombre_completo 
                  FROM becarios_horarios bh 
                  JOIN becarios_info bi ON bh.codigo_becario = bi.codigo 
                  WHERE bh.codigo_becario = ? AND bh.activo = true 
                  ORDER BY bh.dia_semana, bh.hora_inicio";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $horarios = [];
        $totalHorasSemanales = 0;
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        
        while ($row = $resultado->fetch_assoc()) {
            $horarios[] = [
                'id' => $row['id'],
                'dia_semana' => $dias[$row['dia_semana']],
                'dia_numero' => $row['dia_semana'],
                'hora_inicio' => date('H:i', strtotime($row['hora_inicio'])),
                'hora_fin' => date('H:i', strtotime($row['hora_fin'])),
                'horas_asignadas' => $row['horas_asignadas'],
                'observaciones' => $row['observaciones']
            ];
            $totalHorasSemanales += $row['horas_asignadas'];
        }
        
        // Obtener configuración de horas del becario
        $queryConfig = "SELECT * FROM becarios_config_horas 
                        WHERE codigo_becario = ? AND activo = true 
                        ORDER BY fecha_creacion DESC LIMIT 1";
        $stmtConfig = $conexion->prepare($queryConfig);
        $stmtConfig->bind_param("s", $codigo);
        $stmtConfig->execute();
        $resultadoConfig = $stmtConfig->get_result();
        
        $config = null;
        if ($resultadoConfig->num_rows > 0) {
            $config = $resultadoConfig->fetch_assoc();
        }
        
        echo json_encode([
            'success' => true,
            'horarios' => $horarios,
            'total_horas_semanales' => $totalHorasSemanales,
            'config' => $config
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener horarios: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Código no proporcionado'
    ]);
}
?>