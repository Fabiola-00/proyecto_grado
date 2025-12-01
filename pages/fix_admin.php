<?php
// Script para verificar y crear el usuario admin si no existe
$db_file = __DIR__ . '/dashboard/pages/data/db_sbrab.sqlite';

try {
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Usuarios en la base de datos:</h2>";

    // Mostrar todos los usuarios
    $stmt = $pdo->query("SELECT id, user, Nombre, Ape_Pat, Ape_Mat, Rol FROM usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($usuarios) > 0) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Usuario</th><th>Nombre Completo</th><th>Rol</th></tr>";
        foreach ($usuarios as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td><strong>" . $user['user'] . "</strong></td>";
            echo "<td>" . $user['Nombre'] . " " . $user['Ape_Pat'] . " " . $user['Ape_Mat'] . "</td>";
            echo "<td>" . $user['Rol'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay usuarios en la base de datos</p>";
    }

    // Verificar si existe el usuario admin
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE user = 'admin'");
    $stmt->execute();
    $admin_exists = $stmt->fetchColumn();

    echo "<hr>";

    if (!$admin_exists) {
        echo "<h3 style='color: orange;'>⚠ El usuario 'admin' NO existe</h3>";
        echo "<p>Creando usuario admin...</p>";

        // Crear usuario admin
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

        echo "<p style='color: green;'><strong>✓ Usuario 'admin' creado exitosamente</strong></p>";
        echo "<p>Credenciales:</p>";
        echo "<ul>";
        echo "<li><strong>Usuario:</strong> admin</li>";
        echo "<li><strong>Contraseña:</strong> admin123</li>";
        echo "</ul>";
    } else {
        echo "<h3 style='color: green;'>✓ El usuario 'admin' existe</h3>";

        // Verificar la contraseña
        $stmt = $pdo->prepare("SELECT pass FROM usuarios WHERE user = 'admin'");
        $stmt->execute();
        $admin_pass = $stmt->fetchColumn();

        if (password_verify('admin123', $admin_pass)) {
            echo "<p style='color: green;'>✓ La contraseña 'admin123' es correcta</p>";
        } else {
            echo "<p style='color: red;'>❌ La contraseña 'admin123' NO es correcta</p>";
            echo "<p>Actualizando contraseña...</p>";

            $password_hash = password_hash('admin123', PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE usuarios SET pass = :pass WHERE user = 'admin'");
            $stmt->execute([':pass' => $password_hash]);

            echo "<p style='color: green;'>✓ Contraseña actualizada a 'admin123'</p>";
        }
    }

    echo "<hr>";
    echo "<p><a href='login.php'>← Volver al login</a></p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
