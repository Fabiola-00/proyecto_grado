<?php
// registrar_instructor.php
// Iniciar sesión
session_start();


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recibir datos del formulario
    $codigo = $_POST['codigo'] ?? '';
    $grado = $_POST['grado'] ?? '';
    $nombres = $_POST['nombres'] ?? '';
    $apellido_paterno = $_POST['apellido_paterno'] ?? '';
    $apellido_materno = $_POST['apellido_materno'] ?? '';
    $cedula = $_POST['cedula'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $especialidad = $_POST['especialidad'] ?? '';
    $estado = $_POST['estado'] ?? '';

    // Validaciones básicas
    if (
        empty($codigo) || 
        empty($grado) || 
        empty($nombres) || 
        empty($apellido_paterno) || 
        empty($apellido_materno) || 
        empty($cedula) || 
        empty($fecha_nacimiento) || 
        empty($especialidad) || 
        empty($estado)
    ) {
        header("Location: instructor_reg.php?error=Campos incompletos");
        exit();
    }

    // Incluir archivo de conexión
    require_once 'data\db.php';

    try {
        // Preparar consulta
        $stmt = $pdo->prepare("
            INSERT INTO instructores (
                codigo, grado, nombres, apellido_paterno, apellido_materno, 
                cedula, fecha_nacimiento, especialidad, estado
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $codigo,
            $grado,
            $nombres,
            $apellido_paterno,
            $apellido_materno,
            $cedula,
            $fecha_nacimiento,
            $especialidad,
            $estado
        ]);

        // Redirigir con mensaje de éxito
        header("Location: instructores_reg.php?success=true");
        exit();

    } catch (PDOException $e) {
        error_log("Error al insertar datos: " . $e->getMessage());
        header("Location: instructores_reg.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: instructores_reg.php?error=Acceso no permitido");
    exit();
}