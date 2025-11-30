<?php
require_once 'data/db.php';

header('Content-Type: application/json');

if (isset($_GET['instructor_id'])) {
    $instructor_id = intval($_GET['instructor_id']);

    try {
        $sql = "
            SELECT 
                c.nombre,
                c.tipo,
                c.fecha_inicio,
                c.fecha_fin
            FROM instructor_cursos ic
            INNER JOIN cursos c ON ic.curso_id = c.id
            WHERE ic.instructor_id = ?
            ORDER BY c.fecha_inicio DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$instructor_id]);
        $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($cursos);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode([]);
}
