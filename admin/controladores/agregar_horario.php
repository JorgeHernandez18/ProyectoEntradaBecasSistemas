<?php
session_start();
include "seguridad.php";
include "../../modelo/conexion.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $codigo_becario = trim($_POST['codigo_becario']);
        $dia_semana = intval($_POST['dia_semana']);
        $hora_inicio = trim($_POST['hora_inicio']);
        $hora_fin = trim($_POST['hora_fin']);
        $observaciones = trim($_POST['observaciones']) ?: null;
        
        // Validaciones básicas
        if (empty($codigo_becario) || empty($hora_inicio) || empty($hora_fin)) {
            throw new Exception("Todos los campos obligatorios deben ser completados");
        }
        
        // Validar que la hora de inicio sea menor que la hora de fin
        if ($hora_inicio >= $hora_fin) {
            throw new Exception("La hora de inicio debe ser menor que la hora de fin");
        }
        
        // Calcular horas asignadas
        $inicio = new DateTime($hora_inicio);
        $fin = new DateTime($hora_fin);
        $diferencia = $inicio->diff($fin);
        $horas_asignadas = $diferencia->h + ($diferencia->i / 60);
        
        // Verificar que el becario existe
        $queryVerificar = "SELECT codigo FROM becarios_info WHERE codigo = ?";
        $stmtVerificar = $conexion->prepare($queryVerificar);
        $stmtVerificar->bind_param("s", $codigo_becario);
        $stmtVerificar->execute();
        $resultado = $stmtVerificar->get_result();
        
        if ($resultado->num_rows == 0) {
            throw new Exception("Becario no encontrado");
        }
        
        // Verificar si ya existe un horario para ese día y becario
        $queryConflicto = "SELECT id FROM becarios_horarios 
                          WHERE codigo_becario = ? AND dia_semana = ? AND activo = true 
                          AND ((hora_inicio <= ? AND hora_fin > ?) OR (hora_inicio < ? AND hora_fin >= ?))";
        $stmtConflicto = $conexion->prepare($queryConflicto);
        $stmtConflicto->bind_param("sissss", $codigo_becario, $dia_semana, $hora_inicio, $hora_inicio, $hora_fin, $hora_fin);
        $stmtConflicto->execute();
        $resultadoConflicto = $stmtConflicto->get_result();
        
        if ($resultadoConflicto->num_rows > 0) {
            throw new Exception("Ya existe un horario que se superpone con este en el mismo día");
        }
        
        // Insertar nuevo horario
        $query = "INSERT INTO becarios_horarios (codigo_becario, dia_semana, hora_inicio, hora_fin, horas_asignadas, observaciones) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("sissds", $codigo_becario, $dia_semana, $hora_inicio, $hora_fin, $horas_asignadas, $observaciones);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Horario agregado exitosamente";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            throw new Exception("Error al agregar el horario");
        }
        
    } catch (Exception $e) {
        $_SESSION['mensaje'] = $e->getMessage();
        $_SESSION['tipo_mensaje'] = "error";
    }
    
    header("Location: ../pages/horarios.php");
    exit();
} else {
    header("Location: ../pages/horarios.php");
    exit();
}
?>