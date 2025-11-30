<?php
require_once 'data/db.php';

try {
    // Add 'estado' column to 'cursos' table
    // SQLite supports ADD COLUMN
    $sql = "ALTER TABLE cursos ADD COLUMN estado TEXT DEFAULT 'proximo'";
    $pdo->exec($sql);
    echo "Columna 'estado' agregada correctamente a la tabla 'cursos'.";
} catch (PDOException $e) {
    echo "Error al agregar columna (puede que ya exista): " . $e->getMessage();
}
