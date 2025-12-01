<?php
// Script para crear la tabla usuarios e insertar datos iniciales
$db_file = __DIR__ . '/dashboard/pages/data/db_sbrab.sqlite';

try {
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear tabla usuarios
    $sql_create = "CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user TEXT NOT NULL UNIQUE,
        pass TEXT NOT NULL,
        Nombre TEXT NOT NULL,
        Ape_Pat TEXT NOT NULL,
        Ape_Mat TEXT NOT NULL,
        Rol TEXT NOT NULL CHECK(Rol IN ('administrador', 'usuario'))
    )";

    $pdo->exec($sql_create);
    echo "✓ Tabla 'usuarios' creada exitosamente.\n";

    // Verificar si ya existe un usuario admin
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE user = 'admin'");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
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

        echo "✓ Usuario administrador creado (user: admin, pass: admin123).\n";
    } else {
        echo "ℹ Usuario administrador ya existe.\n";
    }

    echo "\n=== Configuración completada ===\n";
    echo "Base de datos: " . $db_file . "\n";
    echo "Tabla: usuarios\n";
    echo "Usuario inicial: admin / admin123\n";
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
