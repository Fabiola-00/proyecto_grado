<?php
require_once 'data/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $instructor_id = intval($_POST['instructor_id']);
    $operativo_id = intval($_POST['operativo_id']);
    $funcion = trim($_POST['funcion']);

    // Verificar que no exista ya esta asignación
    try {
        $stmt = $pdo->prepare("SELECT id FROM instructor_operativos WHERE instructor_id = ? AND operativo_id = ?");
        $stmt->execute([$instructor_id, $operativo_id]);

        if ($stmt->fetch()) {
            header("Location: operativos_asig.php?error=" . urlencode("Esta asignación ya existe"));
            exit();
        }
    } catch (PDOException $e) {
        header("Location: operativos_asig.php?error=" . urlencode($e->getMessage()));
        exit();
    }

    // Insertar la asignación
    try {
        $stmt = $pdo->prepare("INSERT INTO instructor_operativos (instructor_id, operativo_id, funcion) VALUES (?, ?, ?)");
        $stmt->execute([$instructor_id, $operativo_id, $funcion]);

        header("Location: operativos_asig.php?success=" . urlencode("Operativo asignado correctamente"));
        exit();
    } catch (PDOException $e) {
        header("Location: operativos_asig.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: operativos_asig.php");
    exit();
}
