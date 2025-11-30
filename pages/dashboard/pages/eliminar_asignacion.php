<?php
require_once 'data/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $asignacion_id = intval($_POST['asignacion_id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM instructor_cursos WHERE id = ?");
        $stmt->execute([$asignacion_id]);

        header("Location: cursos_asig.php?success=" . urlencode("Asignación eliminada correctamente"));
        exit();
    } catch (PDOException $e) {
        header("Location: cursos_asig.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: cursos_asig.php");
    exit();
}
