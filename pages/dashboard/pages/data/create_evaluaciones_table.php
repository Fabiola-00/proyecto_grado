<?php
// data/create_evaluaciones_table.php

$dir = __DIR__;
$db_file = $dir . DIRECTORY_SEPARATOR . '../data/db_sbrab.sqlite';

try {
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Evaluaciones table
    $sql = "
        CREATE TABLE IF NOT EXISTS evaluaciones (
            cod INTEGER PRIMARY KEY AUTOINCREMENT,
            instructor_id INTEGER NOT NULL,
            EE INTEGER,
            ME INTEGER,
            RE INTEGER,
            TE INTEGER,
            VS INTEGER,
            RS INTEGER,
            DI INTEGER,
            TD INTEGER,
            EM INTEGER,
            CON INTEGER,
            Reco VARCHAR(255),
            FOREIGN KEY (instructor_id) REFERENCES instructores(id)
        )
    ";
    $pdo->exec($sql);
    echo "Table 'evaluaciones' created successfully.";
} catch (PDOException $e) {
    die("Error creating table: " . $e->getMessage());
}
