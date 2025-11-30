<?php
require_once 'data/db.php';

// Obtener filtros
$instructor_id = isset($_GET['instructor_id']) ? intval($_GET['instructor_id']) : null;
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;

// Construir consulta con filtros
$where = [];
$params = [];

if ($instructor_id) {
    $where[] = "i.id = ?";
    $params[] = $instructor_id;
}

if ($tipo) {
    $where[] = "c.tipo = ?";
    $params[] = $tipo;
}

$sql = "
    SELECT 
        i.codigo,
        i.nombres,
        i.apellido_paterno,
        i.apellido_materno,
        c.nombre as curso_nombre,
        c.tipo,
        c.entidad,
        c.fecha_inicio,
        c.fecha_fin,
        c.estado,
        c.observaciones
    FROM instructor_cursos ic
    INNER JOIN instructores i ON ic.instructor_id = i.id
    INNER JOIN cursos c ON ic.curso_id = c.id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY i.apellido_paterno ASC, c.fecha_inicio DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}

// Obtener resumen por tipo
try {
    $sqlResumen = "
        SELECT 
            c.tipo,
            COUNT(DISTINCT c.id) as total_cursos,
            COUNT(DISTINCT ic.instructor_id) as total_instructores
        FROM cursos c
        LEFT JOIN instructor_cursos ic ON c.id = ic.curso_id
        GROUP BY c.tipo
        ORDER BY total_cursos DESC
    ";

    $stmtResumen = $pdo->query($sqlResumen);
    $resumen = $stmtResumen->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $resumen = [];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Cursos - SICOSE</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .logo {
            max-width: 100px;
            margin-bottom: 10px;
        }

        h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }

        h2 {
            font-size: 18px;
            color: #555;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-top: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        @media print {
            body {
                padding: 0;
                font-size: 11pt;
            }

            .no-print {
                display: none;
            }

            a {
                text-decoration: none;
                color: #333;
            }
        }

        .btn-print {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            cursor: pointer;
            border: none;
            font-size: 16px;
        }

        .btn-print:hover {
            background-color: #218838;
        }

        .filter-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .filter-info strong {
            color: #555;
        }
    </style>
</head>

<body>

    <div class="no-print" style="text-align: right;">
        <button onclick="window.print()" class="btn-print">Imprimir / Guardar como PDF</button>
    </div>

    <div class="header">
        <img src="../images/sbrab_escudo.png" alt="Logo SBRAB" class="logo">
        <h1>Reporte de Cursos</h1>
        <p>Sistema de Control y Seguimiento (SICOSE)</p>
    </div>

    <?php if ($instructor_id || $tipo): ?>
        <div class="filter-info">
            <strong>Filtros aplicados:</strong>
            <?php if ($instructor_id): ?>
                <?php
                $stmtInst = $pdo->prepare("SELECT codigo, nombres, apellido_paterno, apellido_materno FROM instructores WHERE id = ?");
                $stmtInst->execute([$instructor_id]);
                $inst = $stmtInst->fetch(PDO::FETCH_ASSOC);
                if ($inst) {
                    echo " Instructor: " . htmlspecialchars($inst['codigo'] . ' - ' . $inst['apellido_paterno'] . ' ' . $inst['apellido_materno'] . ' ' . $inst['nombres']);
                }
                ?>
            <?php endif; ?>
            <?php if ($tipo): ?>
                | Tipo: <?= htmlspecialchars($tipo) ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="section">
        <h2>Cursos por Instructor</h2>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Instructor</th>
                    <th>Curso</th>
                    <th>Tipo</th>
                    <th>Entidad</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($cursos) > 0): ?>
                    <?php foreach ($cursos as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['codigo']) ?></td>
                            <td><?= htmlspecialchars($row['apellido_paterno'] . ' ' . $row['apellido_materno'] . ' ' . $row['nombres']) ?></td>
                            <td><?= htmlspecialchars($row['curso_nombre']) ?></td>
                            <td><?= htmlspecialchars($row['tipo']) ?></td>
                            <td><?= htmlspecialchars($row['entidad']) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['fecha_inicio'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['fecha_fin'])) ?></td>
                            <td><?= htmlspecialchars($row['estado'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;">No se encontraron resultados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Resumen por Tipo de Curso</h2>
        <table>
            <thead>
                <tr>
                    <th>Tipo de Curso</th>
                    <th style="text-align:center;">Cantidad de Cursos</th>
                    <th style="text-align:center;">Instructores Asignados</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($resumen) > 0): ?>
                    <?php foreach ($resumen as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['tipo']) ?></td>
                            <td style="text-align:center;"><?= $row['total_cursos'] ?></td>
                            <td style="text-align:center;"><?= $row['total_instructores'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align:center;">No hay datos disponibles</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Generado el <?= date('d/m/Y H:i') ?></p>
        <p>SBRAB - La Paz, Bolivia</p>
    </div>

</body>

</html>