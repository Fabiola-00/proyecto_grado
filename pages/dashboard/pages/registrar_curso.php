<?php
require_once 'data/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tipo = trim($_POST['tipo']);
    $entidad = trim($_POST['entidad']);
    $nombre = trim($_POST['nombre']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $observaciones = trim($_POST['observaciones'] ?? '');

    // Validar que la fecha de fin no sea anterior a la fecha de inicio
    if (strtotime($fecha_fin) < strtotime($fecha_inicio)) {
        header("Location: cursos_reg.php?error=" . urlencode("La fecha de fin no puede ser anterior a la fecha de inicio"));
        exit();
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO cursos (tipo, entidad, nombre, fecha_inicio, fecha_fin, observaciones)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $tipo,
            $entidad,
            $nombre,
            $fecha_inicio,
            $fecha_fin,
            $observaciones
        ]);

        header("Location: cursos_reg.php?success=1");
        exit();
    } catch (PDOException $e) {
        header("Location: cursos_reg.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: cursos_reg.php");
    exit();
}
