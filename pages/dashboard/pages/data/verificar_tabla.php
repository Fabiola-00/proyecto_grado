<?php
// Script para verificar y agregar la columna funcion
header('Content-Type: text/html; charset=utf-8');
echo "<h2>Verificación y actualización de la tabla instructor_operativos</h2>";

try {
    $pdo = new PDO('sqlite:db_sbrab.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h3>1. Verificando estructura actual de la tabla...</h3>";

    // Obtener información de la tabla
    $stmt = $pdo->query("PRAGMA table_info(instructor_operativos)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Tipo</th><th>Not Null</th><th>Default</th></tr>";

    $funcionExists = false;
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . $col['cid'] . "</td>";
        echo "<td>" . $col['name'] . "</td>";
        echo "<td>" . $col['type'] . "</td>";
        echo "<td>" . $col['notnull'] . "</td>";
        echo "<td>" . $col['dflt_value'] . "</td>";
        echo "</tr>";

        if ($col['name'] === 'funcion') {
            $funcionExists = true;
        }
    }
    echo "</table>";

    echo "<h3>2. Estado de la columna 'funcion':</h3>";

    if ($funcionExists) {
        echo "<p style='color: green;'>✓ La columna 'funcion' YA EXISTE en la tabla</p>";
    } else {
        echo "<p style='color: orange;'>⚠ La columna 'funcion' NO EXISTE. Intentando agregarla...</p>";

        try {
            $pdo->exec('ALTER TABLE instructor_operativos ADD COLUMN funcion TEXT NOT NULL DEFAULT "Personal de rescate"');
            echo "<p style='color: green;'>✓ Columna 'funcion' agregada exitosamente</p>";

            // Verificar nuevamente
            $stmt = $pdo->query("PRAGMA table_info(instructor_operativos)");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<h3>3. Nueva estructura de la tabla:</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Tipo</th><th>Not Null</th><th>Default</th></tr>";
            foreach ($columns as $col) {
                echo "<tr>";
                echo "<td>" . $col['cid'] . "</td>";
                echo "<td>" . $col['name'] . "</td>";
                echo "<td>" . $col['type'] . "</td>";
                echo "<td>" . $col['notnull'] . "</td>";
                echo "<td>" . $col['dflt_value'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>✗ Error al agregar columna: " . $e->getMessage() . "</p>";
        }
    }

    echo "<h3>4. Verificando registros existentes:</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM instructor_operativos");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total de registros en instructor_operativos: " . $result['total'] . "</p>";

    if ($funcionExists && $result['total'] > 0) {
        echo "<h3>5. Muestra de registros:</h3>";
        $stmt = $pdo->query("SELECT * FROM instructor_operativos LIMIT 5");
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($records) > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Instructor ID</th><th>Operativo ID</th><th>Función</th></tr>";
            foreach ($records as $rec) {
                echo "<tr>";
                echo "<td>" . $rec['id'] . "</td>";
                echo "<td>" . $rec['instructor_id'] . "</td>";
                echo "<td>" . $rec['operativo_id'] . "</td>";
                echo "<td>" . ($rec['funcion'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }

    echo "<hr>";
    echo "<p><strong>Proceso completado.</strong> Puedes cerrar esta ventana y recargar la página de asignaciones.</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>Error de conexión:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Ruta de la base de datos: " . realpath('db_sbrab.sqlite') . "</p>";
}
