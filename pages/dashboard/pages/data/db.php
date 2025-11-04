<?php
// data/db.php

$dir = __DIR__;
$db_file = $dir . DIRECTORY_SEPARATOR . '../data/db_sbrab.sqlite';

try {
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear tabla si no existe
    $sql = "
        CREATE TABLE IF NOT EXISTS instructores (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            codigo TEXT NOT NULL UNIQUE,
            grado TEXT NOT NULL,
            nombres TEXT NOT NULL,
            apellido_paterno TEXT NOT NULL,
            apellido_materno TEXT NOT NULL,
            cedula TEXT NOT NULL,
            fecha_nacimiento DATE NOT NULL,
            especialidad TEXT NOT NULL,
            estado TEXT NOT NULL
        )
    ";
    $pdo->exec($sql);

    // Sin salida visible
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}