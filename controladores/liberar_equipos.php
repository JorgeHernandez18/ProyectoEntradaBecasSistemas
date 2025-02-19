<?php
session_start();
include "../modelo/conexion.php";
date_default_timezone_set('America/Bogota');

if (!isset($_SESSION['datos_login'])) {
    echo json_encode(['success' => false, 'error' => 'Sesión no iniciada']);
    exit();
}

try {
    $conexion->begin_transaction();

    // Obtener todos los equipos ocupados
    $queryEquiposOcupados = "SELECT id, codigo, equipo FROM becl_registro_computo WHERE salida IS NULL";
    $stmtEquiposOcupados = $conexion->prepare($queryEquiposOcupados);
    $stmtEquiposOcupados->execute();
    $resultadoEquiposOcupados = $stmtEquiposOcupados->get_result();

    $equiposLiberados = 0;
    $now = new DateTime();
    $fechaHoraActual = $now->format('Y-m-d H:i:s');

    while ($registro = $resultadoEquiposOcupados->fetch_assoc()) {
        // Registrar salida
        $queryRegistrarSalida = "UPDATE becl_registro_computo SET salida = ? WHERE id = ?";
        $stmtRegistrarSalida = $conexion->prepare($queryRegistrarSalida);
        $stmtRegistrarSalida->bind_param("si", $fechaHoraActual, $registro['id']);
        $stmtRegistrarSalida->execute();

        // Actualizar estado del equipo a 'libre'
        $queryActualizarEquipo = "UPDATE becl_equipo SET estado = 'libre' WHERE equipo = ?";
        $stmtActualizarEquipo = $conexion->prepare($queryActualizarEquipo);
        $stmtActualizarEquipo->bind_param("i", $registro['equipo']);
        $stmtActualizarEquipo->execute();

        $equiposLiberados++;
    }

    $conexion->commit();
    echo json_encode(['success' => true, 'mensaje2' => "Se liberaron $equiposLiberados equipos."]);
} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['success' => false, 'error' => 'Error al liberar equipos: ' . $e->getMessage()]);
}
?>