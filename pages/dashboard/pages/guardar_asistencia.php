<?php
require_once 'data/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'] ?? '';
    $asistencias = $_POST['asistencia'] ?? [];

    if (empty($fecha)) {
        header("Location: asistencia_reg.php?error=Debe seleccionar una fecha");
        exit();
    }

    if (empty($asistencias)) {
        header("Location: asistencia_reg.php?error=Debe registrar al menos una asistencia");
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Primero, eliminar registros existentes para esta fecha (si se está actualizando)
        $stmt = $pdo->prepare("DELETE FROM asistencia WHERE fecha = ?");
        $stmt->execute([$fecha]);

        // Insertar nuevos registros
        $stmt = $pdo->prepare("INSERT INTO asistencia (instructor_id, fecha, tipo) VALUES (?, ?, ?)");

        foreach ($asistencias as $instructor_id => $tipo) {
            if (!empty($tipo)) {
                $stmt->execute([$instructor_id, $fecha, $tipo]);
            }
        }

        $pdo->commit();
        header("Location: asistencia_reg.php?success=1");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        header("Location: asistencia_reg.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: asistencia_reg.php");
    exit();
}
