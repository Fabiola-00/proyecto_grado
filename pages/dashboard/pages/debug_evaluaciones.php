<?php
$pdo = new PDO('sqlite:c:/laragon/www/sistema_sbrab/pages/dashboard/pages/data/db_sbrab.sqlite');

echo "=== TABLA EVALUACIONES - ESTRUCTURA ===\n";
$stmt = $pdo->query('PRAGMA table_info(evaluaciones)');
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo $col['name'] . ' (' . $col['type'] . ')' . "\n";
}

echo "\n=== TOTAL DE REGISTROS ===\n";
$stmt = $pdo->query('SELECT COUNT(*) as count FROM evaluaciones');
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo 'Total evaluaciones: ' . $result['count'] . "\n";

echo "\n=== PRIMEROS 3 REGISTROS ===\n";
$stmt = $pdo->query('SELECT * FROM evaluaciones LIMIT 3');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);

echo "\n=== CÓDIGOS DE INSTRUCTOR EN EVALUACIONES ===\n";
$stmt = $pdo->query('SELECT DISTINCT instructor_id FROM evaluaciones');
$codigos = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($codigos);
