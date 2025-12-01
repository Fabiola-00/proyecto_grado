<?php
// Inicializar base de datos si no existe la tabla usuarios
function initDatabase()
{
    $db_file = __DIR__ . '/dashboard/pages/data/db_sbrab.sqlite';

    try {
        $pdo = new PDO('sqlite:' . $db_file);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verificar si existe la tabla usuarios
        $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='usuarios'");

        if ($result->fetchColumn() === false) {
            // Crear tabla usuarios
            $sql_create = "CREATE TABLE usuarios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user TEXT NOT NULL UNIQUE,
                pass TEXT NOT NULL,
                Nombre TEXT NOT NULL,
                Ape_Pat TEXT NOT NULL,
                Ape_Mat TEXT NOT NULL,
                Rol TEXT NOT NULL CHECK(Rol IN ('administrador', 'usuario'))
            )";

            $pdo->exec($sql_create);

            // Insertar usuario administrador inicial
            $password_hash = password_hash('admin123', PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("INSERT INTO usuarios (user, pass, Nombre, Ape_Pat, Ape_Mat, Rol) 
                                  VALUES (:user, :pass, :nombre, :ape_pat, :ape_mat, :rol)");

            $stmt->execute([
                ':user' => 'admin',
                ':pass' => $password_hash,
                ':nombre' => 'Administrador',
                ':ape_pat' => 'Sistema',
                ':ape_mat' => 'SBRAB',
                ':rol' => 'administrador'
            ]);

            return true;
        }

        return false;
    } catch (PDOException $e) {
        error_log("Error inicializando base de datos: " . $e->getMessage());
        return false;
    }
}

// Ejecutar inicialización
initDatabase();
