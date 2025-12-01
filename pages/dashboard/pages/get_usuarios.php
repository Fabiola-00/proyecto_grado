<?php
session_start();
header('Content-Type: application/json');

// Verificar que el usuario tenga acceso de administrador
if (!isset($_SESSION['admin_verified']) || $_SESSION['admin_verified'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

$db_file = __DIR__ . '/data/db_sbrab.sqlite';

try {
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, user, Nombre, Ape_Pat, Ape_Mat, Rol FROM usuarios ORDER BY id");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'usuarios' => $usuarios]);
} catch (PDOException $e) {
    error_log("Error obteniendo usuarios: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
}
