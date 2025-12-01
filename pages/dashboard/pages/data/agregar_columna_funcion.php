<?php
// Script temporal para agregar la columna funcion a instructor_operativos
try {
    $pdo = new PDO('sqlite:db_sbrab.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Intentar agregar la columna
    $pdo->exec('ALTER TABLE instructor_operativos ADD COLUMN funcion TEXT NOT NULL DEFAULT "Personal de rescate"');

    echo "✓ Columna 'funcion' agregada exitosamente a la tabla instructor_operativos\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'duplicate column name') !== false) {
        echo "✓ La columna 'funcion' ya existe en la tabla\n";
    } else {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}
