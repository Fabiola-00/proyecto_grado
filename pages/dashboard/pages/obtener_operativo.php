<?php
require_once 'data/db.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM operativos WHERE id = ?");
        $stmt->execute([$id]);
        $operativo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($operativo) {
            echo json_encode($operativo);
        } else {
            echo json_encode(['error' => 'Operativo no encontrado']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'ID no proporcionado']);
}
