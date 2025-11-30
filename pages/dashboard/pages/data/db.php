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

    // Crear tabla de cursos
    $sql_cursos = "
        CREATE TABLE IF NOT EXISTS cursos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tipo TEXT NOT NULL,
            entidad TEXT NOT NULL,
            nombre TEXT NOT NULL,
            fecha_inicio DATE NOT NULL,
            fecha_fin DATE NOT NULL,
            observaciones TEXT
        )
    ";
    $pdo->exec($sql_cursos);

    // Crear tabla de relación instructor-cursos (uno a muchos)
    $sql_instructor_cursos = "
        CREATE TABLE IF NOT EXISTS instructor_cursos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            instructor_id INTEGER NOT NULL,
            curso_id INTEGER NOT NULL,
            FOREIGN KEY (instructor_id) REFERENCES instructores(id) ON DELETE CASCADE,
            FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
        )
    ";
    $pdo->exec($sql_instructor_cursos);

    // Sin salida visible
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
