<?php
// data/list_instructores.php
$dir = __DIR__;
$db_file = $dir . DIRECTORY_SEPARATOR . '../data/db_sbrab.sqlite';

try {
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM instructores LIMIT 5");
    $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($instructores)) {
        echo "No instructors found.";
    } else {
        echo "Instructors found:<br>";
        foreach ($instructores as $inst) {
            $stmt_eval = $pdo->prepare("SELECT COUNT(*) FROM evaluaciones WHERE instructor_id = :id");
            $stmt_eval->execute([':id' => $inst['id']]);
            $is_evaluated = $stmt_eval->fetchColumn() > 0 ? "Evaluado" : "No Evaluado";
            echo "ID: " . $inst['id'] . " - Code: " . $inst['codigo'] . " - Name: " . $inst['nombres'] . " " . $inst['apellido_paterno'] . " - DB Estado: " . $inst['estado'] . " - Calc Status: " . $is_evaluated . "<br>";
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
