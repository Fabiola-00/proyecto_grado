<?php
// data/verify_table.php

$dir = __DIR__;
$db_file = $dir . DIRECTORY_SEPARATOR . '../data/db_sbrab.sqlite';

try {
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get table info
    $stmt = $pdo->query("PRAGMA table_info(evaluaciones)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($columns)) {
        echo "Tabla 'evaluaciones' no existe.";
    } else {
        echo "Tabla 'evaluaciones' existe. Columnas:<br>";
        foreach ($columns as $col) {
            echo $col['name'] . " (" . $col['type'] . ")<br>";
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
