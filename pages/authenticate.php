<?php
session_start();
require_once 'init_db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Usuario y contraseña son requeridos']);
    exit;
}

$db_file = __DIR__ . '/dashboard/pages/data/db_sbrab.sqlite';

try {
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE user = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['pass'])) {
        // Autenticación exitosa
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['user'];
        $_SESSION['nombre_completo'] = $user['Nombre'] . ' ' . $user['Ape_Pat'] . ' ' . $user['Ape_Mat'];
        $_SESSION['rol'] = $user['Rol'];

        echo json_encode([
            'success' => true,
            'message' => 'Autenticación exitosa',
            'rol' => $user['Rol']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Usuario o contraseña incorrectos'
        ]);
    }
} catch (PDOException $e) {
    error_log("Error en autenticación: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor'
    ]);
}
