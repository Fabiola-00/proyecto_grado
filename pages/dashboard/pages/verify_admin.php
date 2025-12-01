<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$pin = $data['pin'] ?? '';

// PIN de administrador: 3690
if ($pin === '3690') {
    $_SESSION['admin_verified'] = true;
    echo json_encode(['success' => true, 'message' => 'PIN correcto']);
} else {
    echo json_encode(['success' => false, 'message' => 'PIN incorrecto']);
}
