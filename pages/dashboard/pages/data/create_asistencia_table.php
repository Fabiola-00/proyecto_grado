<?php
require_once 'db.php';

try {
    // Crear tabla asistencia
    $sql = "CREATE TABLE IF NOT EXISTS asistencia (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        instructor_id INTEGER NOT NULL,
        fecha DATE NOT NULL,
        tipo VARCHAR(50) NOT NULL,
        FOREIGN KEY (instructor_id) REFERENCES instructores(id)
    )";

    $pdo->exec($sql);
    echo "Tabla 'asistencia' creada exitosamente.\n";
} catch (PDOException $e) {
    echo "Error al crear la tabla: " . $e->getMessage() . "\n";
}
