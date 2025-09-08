<?php
date_default_timezone_set('America/Bogota');
include_once "../modelo/conexion.php";

function log_auto_salida($mensaje) {
    $fecha = date('Y-m-d H:i:s');
    error_log("[$fecha] AUTO_SALIDA: $mensaje\n", 3, "../logs/auto_salida.log");
}

function marcarSalidaAutomatica($conexion, $registro, $horario) {
    try {
        // Calcular la hora de salida programada + 30 minutos
        $horaSalidaProgramada = new DateTime($horario['hora_fin']);
        $horaActual = new DateTime();
        $fechaHoy = $horaActual->format('Y-m-d');
        
        // Combinar fecha de hoy con hora de salida programada
        $fechaHoraSalidaProgramada = new DateTime($fechaHoy . ' ' . $horario['hora_fin']);
        $fechaHoraSalidaLimite = clone $fechaHoraSalidaProgramada;
        $fechaHoraSalidaLimite->add(new DateInterval('PT30M')); // +30 minutos
        
        // Verificar si ya pasaron 30 minutos desde la hora programada de salida
        if ($horaActual >= $fechaHoraSalidaLimite) {
            $entrada = new DateTime($registro['entrada']);
            
            // Calcular horas trabajadas hasta la hora programada de salida
            $diferencia = $entrada->diff($fechaHoraSalidaProgramada);
            $totalMinutos = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i + ($diferencia->s / 60);
            $horasTrabajadas = round($totalMinutos / 60, 2);
            
            // Preparar observaciones
            $observaciones = "SALIDA AUTOMÁTICA: No marcó salida. Sistema registró automáticamente a las " . 
                           $fechaHoraSalidaProgramada->format('H:i') . " (horario programado) + 30min de tolerancia. " . 
                           "Horario programado: " . $horario['hora_inicio'] . " - " . $horario['hora_fin'] . ". ";
            
            if ($registro['observaciones']) {
                $observaciones = $registro['observaciones'] . " | " . $observaciones;
            }
            
            // Actualizar registro con salida automática
            $queryUpdate = "UPDATE becarios_registro 
                           SET salida = ?, 
                               horas_trabajadas = ?, 
                               observaciones = ?,
                               salida_automatica = true
                           WHERE id = ?";
            $stmtUpdate = $conexion->prepare($queryUpdate);
            $stmtUpdate->bind_param("sdsi", 
                                   $fechaHoraSalidaProgramada->format('Y-m-d H:i:s'), 
                                   $horasTrabajadas, 
                                   $observaciones, 
                                   $registro['id']);
            
            if ($stmtUpdate->execute()) {
                log_auto_salida("Salida automática registrada para becario {$registro['codigo']} - ID registro: {$registro['id']} - Horas: $horasTrabajadas");
                return true;
            } else {
                log_auto_salida("ERROR: No se pudo registrar salida automática para becario {$registro['codigo']} - ID: {$registro['id']}");
                return false;
            }
        }
        
        return false; // Aún no es tiempo de marcar salida automática
        
    } catch (Exception $e) {
        log_auto_salida("ERROR en marcarSalidaAutomatica: " . $e->getMessage());
        return false;
    }
}

function obtenerHorarioBecario($conexion, $codigo, $diaSemana) {
    try {
        $query = "SELECT * FROM becarios_horarios 
                  WHERE codigo_becario = ? AND dia_semana = ? AND activo = true";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("si", $codigo, $diaSemana);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        
        return null;
    } catch (Exception $e) {
        log_auto_salida("ERROR en obtenerHorarioBecario: " . $e->getMessage());
        return null;
    }
}

function procesarSalidasAutomaticas($conexion) {
    try {
        $horaActual = new DateTime();
        $fechaHoy = $horaActual->format('Y-m-d');
        $diaSemanaActual = (int)$horaActual->format('w'); // 0=domingo, 1=lunes, etc.
        
        // Buscar registros de entrada sin salida del día actual
        $query = "SELECT br.*, bi.nombre_completo 
                  FROM becarios_registro br
                  JOIN becarios_info bi ON br.codigo = bi.codigo
                  WHERE DATE(br.entrada) = ? 
                  AND br.salida IS NULL 
                  AND br.salida_automatica IS NOT TRUE";
        
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $fechaHoy);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $procesados = 0;
        $marcados = 0;
        
        while ($registro = $resultado->fetch_assoc()) {
            $procesados++;
            
            // Obtener horario del becario para el día actual
            $horario = obtenerHorarioBecario($conexion, $registro['codigo'], $diaSemanaActual);
            
            if ($horario) {
                log_auto_salida("Procesando becario {$registro['codigo']} - Horario encontrado: {$horario['hora_inicio']} - {$horario['hora_fin']}");
                
                if (marcarSalidaAutomatica($conexion, $registro, $horario)) {
                    $marcados++;
                }
            } else {
                log_auto_salida("Becario {$registro['codigo']} no tiene horario programado para hoy (día $diaSemanaActual)");
            }
        }
        
        log_auto_salida("Proceso completado: $procesados registros procesados, $marcados salidas automáticas marcadas");
        
        return [
            'procesados' => $procesados,
            'marcados' => $marcados
        ];
        
    } catch (Exception $e) {
        log_auto_salida("ERROR en procesarSalidasAutomaticas: " . $e->getMessage());
        return [
            'error' => $e->getMessage(),
            'procesados' => 0,
            'marcados' => 0
        ];
    }
}

// Ejecutar el proceso si se ejecuta directamente
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    log_auto_salida("Iniciando proceso de salidas automáticas");
    
    $resultado = procesarSalidasAutomaticas($conexion);
    
    // Si se ejecuta vía web, devolver JSON
    if (isset($_SERVER['HTTP_HOST'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => !isset($resultado['error']),
            'message' => isset($resultado['error']) ? $resultado['error'] : 'Proceso completado',
            'data' => $resultado
        ]);
    } else {
        // Si se ejecuta desde consola/cron, mostrar resultado
        echo "Proceso completado:\n";
        echo "Procesados: " . $resultado['procesados'] . "\n";
        echo "Marcados: " . $resultado['marcados'] . "\n";
        if (isset($resultado['error'])) {
            echo "Error: " . $resultado['error'] . "\n";
        }
    }
}
?>