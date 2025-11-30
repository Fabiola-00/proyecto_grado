<?php
require_once 'data/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $instructor_id = intval($_POST['instructor_id']);
    $curso_id = intval($_POST['curso_id']);

    // Verificar que no exista ya esta asignación
    try {
        $stmt = $pdo->prepare("SELECT id FROM instructor_cursos WHERE instructor_id = ? AND curso_id = ?");
        $stmt->execute([$instructor_id, $curso_id]);

        if ($stmt->fetch()) {
            header("Location: cursos_asig.php?error=" . urlencode("Esta asignación ya existe"));
            exit();
        }
    } catch (PDOException $e) {
        header("Location: cursos_asig.php?error=" . urlencode($e->getMessage()));
        exit();
    }

    // Insertar la asignación
    try {
        $stmt = $pdo->prepare("INSERT INTO instructor_cursos (instructor_id, curso_id) VALUES (?, ?)");
        $stmt->execute([$instructor_id, $curso_id]);

        header("Location: cursos_asig.php?success=" . urlencode("Curso asignado correctamente"));
        exit();
    } catch (PDOException $e) {
        header("Location: cursos_asig.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: cursos_asig.php");
    exit();
}
