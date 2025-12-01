<?php
require_once 'data/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tipo = trim($_POST['tipo']);
    $entidad_responsable = trim($_POST['entidad_responsable']);
    $estado = trim($_POST['estado']);
    $departamento = trim($_POST['departamento']);
    $zona = trim($_POST['zona']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_final = $_POST['fecha_final'];

    // Validar que la fecha final no sea anterior a la fecha de inicio
    if (strtotime($fecha_final) < strtotime($fecha_inicio)) {
        header("Location: operativos_reg.php?error=" . urlencode("La fecha final no puede ser anterior a la fecha de inicio"));
        exit();
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO operativos (tipo, entidad_responsable, estado, departamento, zona, fecha_inicio, fecha_final)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $tipo,
            $entidad_responsable,
            $estado,
            $departamento,
            $zona,
            $fecha_inicio,
            $fecha_final
        ]);

        header("Location: operativos_reg.php?success=1");
        exit();
    } catch (PDOException $e) {
        header("Location: operativos_reg.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: operativos_reg.php");
    exit();
}
