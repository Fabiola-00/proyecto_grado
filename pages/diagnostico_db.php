<?php
// Script de diagnóstico para verificar la base de datos
$db_file = __DIR__ . '/dashboard/pages/data/db_sbrab.sqlite';

echo "<h2>Diagnóstico de Base de Datos</h2>";
echo "<p><strong>Ruta de BD:</strong> " . $db_file . "</p>";

// Verificar si el archivo existe
if (!file_exists($db_file)) {
    echo "<p style='color: red;'>❌ El archivo de base de datos NO existe</p>";
    echo "<p>Creando base de datos...</p>";

    // Crear directorio si no existe
    $dir = dirname($db_file);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
        echo "<p>✓ Directorio creado: $dir</p>";
    }
} else {
    echo "<p style='color: green;'>✓ El archivo de base de datos existe</p>";
}

try {
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p style='color: green;'>✓ Conexión a base de datos exitosa</p>";

    // Verificar si existe la tabla usuarios
    $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='usuarios'");
    $table_exists = $result->fetchColumn();

    if (!$table_exists) {
        echo "<p style='color: orange;'>⚠ La tabla 'usuarios' NO existe. Creándola...</p>";

        // Crear tabla
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
        echo "<p style='color: green;'>✓ Tabla 'usuarios' creada</p>";

        // Insertar usuario admin
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

        echo "<p style='color: green;'>✓ Usuario administrador creado (user: admin, pass: admin123)</p>";
    } else {
        echo "<p style='color: green;'>✓ La tabla 'usuarios' existe</p>";
    }

    // Mostrar usuarios existentes
    $stmt = $pdo->query("SELECT id, user, Nombre, Ape_Pat, Ape_Mat, Rol FROM usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Usuarios en la base de datos:</h3>";
    if (count($usuarios) > 0) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Usuario</th><th>Nombre Completo</th><th>Rol</th></tr>";
        foreach ($usuarios as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['user'] . "</td>";
            echo "<td>" . $user['Nombre'] . " " . $user['Ape_Pat'] . " " . $user['Ape_Mat'] . "</td>";
            echo "<td>" . $user['Rol'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠ No hay usuarios en la base de datos</p>";
    }

    // Verificar hash de contraseña del admin
    $stmt = $pdo->prepare("SELECT pass FROM usuarios WHERE user = 'admin'");
    $stmt->execute();
    $admin_pass = $stmt->fetchColumn();

    if ($admin_pass) {
        echo "<h3>Verificación de contraseña:</h3>";
        echo "<p>Hash almacenado: " . substr($admin_pass, 0, 50) . "...</p>";

        // Probar verificación
        if (password_verify('admin123', $admin_pass)) {
            echo "<p style='color: green;'>✓ La contraseña 'admin123' es correcta</p>";
        } else {
            echo "<p style='color: red;'>❌ La contraseña 'admin123' NO coincide</p>";
        }
    }

    echo "<hr>";
    echo "<h3>✅ Diagnóstico completado</h3>";
    echo "<p><a href='login.php'>Volver al login</a></p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
