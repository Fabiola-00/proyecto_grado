<?php
session_start();
require_once 'init_db.php';

header('Content-Type: application/json');

// Log para debugging
$log_file = __DIR__ . '/auth_log.txt';
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Inicio de autenticación\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

file_put_contents($log_file, "Usuario recibido: $username\n", FILE_APPEND);

if (empty($username) || empty($password)) {
    file_put_contents($log_file, "Error: Usuario o contraseña vacíos\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Usuario y contraseña son requeridos']);
    exit;
}

$db_file = __DIR__ . '/dashboard/pages/data/db_sbrab.sqlite';
file_put_contents($log_file, "Ruta BD: $db_file\n", FILE_APPEND);
file_put_contents($log_file, "BD existe: " . (file_exists($db_file) ? 'SI' : 'NO') . "\n", FILE_APPEND);

try {
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    file_put_contents($log_file, "Conexión a BD exitosa\n", FILE_APPEND);

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE user = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    file_put_contents($log_file, "Usuario encontrado: " . ($user ? 'SI' : 'NO') . "\n", FILE_APPEND);

    if ($user && password_verify($password, $user['pass'])) {
        // Autenticación exitosa
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user['user'];
        $_SESSION['nombre_completo'] = $user['Nombre'] . ' ' . $user['Ape_Pat'] . ' ' . $user['Ape_Mat'];
        $_SESSION['rol'] = $user['Rol'];

        file_put_contents($log_file, "Autenticación EXITOSA\n", FILE_APPEND);

        echo json_encode([
            'success' => true,
            'message' => 'Autenticación exitosa',
            'rol' => $user['Rol'],
            'redirect' => 'dashboard/inicio.php'
        ]);
    } else {
        file_put_contents($log_file, "Autenticación FALLIDA - Contraseña incorrecta\n", FILE_APPEND);
        echo json_encode([
            'success' => false,
            'message' => 'Usuario o contraseña incorrectos'
        ]);
    }
} catch (PDOException $e) {
    $error_msg = "Error en autenticación: " . $e->getMessage();
    file_put_contents($log_file, $error_msg . "\n", FILE_APPEND);
    error_log($error_msg);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor: ' . $e->getMessage()
    ]);
}
