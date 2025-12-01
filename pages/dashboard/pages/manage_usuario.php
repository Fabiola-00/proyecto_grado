<?php
session_start();
header('Content-Type: application/json');

// Verificar que el usuario tenga acceso de administrador
if (!isset($_SESSION['admin_verified']) || $_SESSION['admin_verified'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

$db_file = __DIR__ . '/data/db_sbrab.sqlite';

try {
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    switch ($action) {
        case 'create':
            $user = $data['user'] ?? '';
            $pass = $data['pass'] ?? '';
            $nombre = $data['nombre'] ?? '';
            $ape_pat = $data['ape_pat'] ?? '';
            $ape_mat = $data['ape_mat'] ?? '';
            $rol = $data['rol'] ?? 'usuario';

            if (empty($user) || empty($pass) || empty($nombre) || empty($ape_pat) || empty($ape_mat)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
                exit;
            }

            $password_hash = password_hash($pass, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("INSERT INTO usuarios (user, pass, Nombre, Ape_Pat, Ape_Mat, Rol) 
                                  VALUES (:user, :pass, :nombre, :ape_pat, :ape_mat, :rol)");

            $stmt->execute([
                ':user' => $user,
                ':pass' => $password_hash,
                ':nombre' => $nombre,
                ':ape_pat' => $ape_pat,
                ':ape_mat' => $ape_mat,
                ':rol' => $rol
            ]);

            echo json_encode(['success' => true, 'message' => 'Usuario creado exitosamente']);
            break;

        case 'update':
            $id = $data['id'] ?? 0;
            $user = $data['user'] ?? '';
            $nombre = $data['nombre'] ?? '';
            $ape_pat = $data['ape_pat'] ?? '';
            $ape_mat = $data['ape_mat'] ?? '';
            $rol = $data['rol'] ?? 'usuario';
            $pass = $data['pass'] ?? '';

            if (empty($id) || empty($user) || empty($nombre) || empty($ape_pat) || empty($ape_mat)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
                exit;
            }

            if (!empty($pass)) {
                // Actualizar con nueva contraseña
                $password_hash = password_hash($pass, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE usuarios SET user = :user, pass = :pass, Nombre = :nombre, 
                                      Ape_Pat = :ape_pat, Ape_Mat = :ape_mat, Rol = :rol WHERE id = :id");
                $stmt->execute([
                    ':id' => $id,
                    ':user' => $user,
                    ':pass' => $password_hash,
                    ':nombre' => $nombre,
                    ':ape_pat' => $ape_pat,
                    ':ape_mat' => $ape_mat,
                    ':rol' => $rol
                ]);
            } else {
                // Actualizar sin cambiar contraseña
                $stmt = $pdo->prepare("UPDATE usuarios SET user = :user, Nombre = :nombre, 
                                      Ape_Pat = :ape_pat, Ape_Mat = :ape_mat, Rol = :rol WHERE id = :id");
                $stmt->execute([
                    ':id' => $id,
                    ':user' => $user,
                    ':nombre' => $nombre,
                    ':ape_pat' => $ape_pat,
                    ':ape_mat' => $ape_mat,
                    ':rol' => $rol
                ]);
            }

            echo json_encode(['success' => true, 'message' => 'Usuario actualizado exitosamente']);
            break;

        case 'delete':
            $id = $data['id'] ?? 0;

            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID de usuario requerido']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->execute([':id' => $id]);

            echo json_encode(['success' => true, 'message' => 'Usuario eliminado exitosamente']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }
} catch (PDOException $e) {
    error_log("Error en gestión de usuarios: " . $e->getMessage());

    if ($e->getCode() == 23000) {
        echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya existe']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
    }
}
