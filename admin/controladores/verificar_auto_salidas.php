<?php
// Este archivo se incluye automáticamente para verificar salidas automáticas
// Solo se ejecuta cada 10 minutos para no sobrecargar el sistema

function debeEjecutarVerificacion() {
    $archivo_ultima_ejecucion = __DIR__ . '/../../logs/ultima_verificacion_auto_salida.txt';
    
    if (!file_exists($archivo_ultima_ejecucion)) {
        return true;
    }
    
    $ultima_ejecucion = file_get_contents($archivo_ultima_ejecucion);
    $tiempo_transcurrido = time() - (int)$ultima_ejecucion;
    
    // Ejecutar cada 10 minutos (600 segundos)
    return $tiempo_transcurrido >= 600;
}

function marcarUltimaEjecucion() {
    $archivo_ultima_ejecucion = __DIR__ . '/../../logs/ultima_verificacion_auto_salida.txt';
    $logs_dir = dirname($archivo_ultima_ejecucion);
    
    if (!is_dir($logs_dir)) {
        mkdir($logs_dir, 0755, true);
    }
    
    file_put_contents($archivo_ultima_ejecucion, time());
}

// Solo ejecutar si han pasado más de 10 minutos desde la última verificación
if (debeEjecutarVerificacion()) {
    try {
        // Incluir y ejecutar el proceso de auto salida
        include_once __DIR__ . '/../../controladores/auto_salida.php';
        
        // No mostrar output aquí ya que puede interferir con las páginas
        ob_start();
        $resultado = procesarSalidasAutomaticas($conexion);
        ob_end_clean();
        
        // Marcar que se ejecutó
        marcarUltimaEjecucion();
        
    } catch (Exception $e) {
        // Log error silencioso
        error_log("Error en verificación automática de salidas: " . $e->getMessage());
    }
}
?>