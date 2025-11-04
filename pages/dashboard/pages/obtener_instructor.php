<?php
require_once 'data/db.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    die(json_encode(['error' => 'ID no especificado']));
}

$id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("SELECT * FROM instructores WHERE id = ?");
    $stmt->execute([$id]);
    $instructor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$instructor) {
        http_response_code(404);
        die(json_encode(['error' => 'Instructor no encontrado']));
    }

    echo json_encode($instructor);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Error interno del servidor']));
}
?>