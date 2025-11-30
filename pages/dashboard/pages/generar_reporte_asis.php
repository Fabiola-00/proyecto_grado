<?php
require_once 'data/db.php';

// Obtener parámetros de filtro
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$instructor_id = $_GET['instructor_id'] ?? '';

// Construir consulta con filtros
$where = [];
$params = [];

if (!empty($fecha_inicio)) {
    $where[] = "a.fecha >= ?";
    $params[] = $fecha_inicio;
}

if (!empty($fecha_fin)) {
    $where[] = "a.fecha <= ?";
    $params[] = $fecha_fin;
}

if (!empty($instructor_id)) {
    $where[] = "a.instructor_id = ?";
    $params[] = $instructor_id;
}

// Obtener registros de asistencia
$registros = [];
try {
    $sql = "SELECT a.*, i.nombres, i.apellido_paterno, i.apellido_materno 
            FROM asistencia a 
            INNER JOIN instructores i ON a.instructor_id = i.id";

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY a.fecha DESC, i.apellido_paterno ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}

// Calcular estadísticas por instructor
$estadisticas = [];
if (!empty($registros)) {
    foreach ($registros as $registro) {
        $id = $registro['instructor_id'];
        $nombre_completo = $registro['apellido_paterno'] . ' ' . $registro['apellido_materno'] . ' ' . $registro['nombres'];

        if (!isset($estadisticas[$id])) {
            $estadisticas[$id] = [
                'nombre' => $nombre_completo,
                'Ordinaria' => 0,
                'Extraordinaria' => 0,
                'Operación' => 0,
                'Permiso' => 0,
                'Falta' => 0,
                'total' => 0
            ];
        }

        $estadisticas[$id][$registro['tipo']]++;
        $estadisticas[$id]['total']++;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Asistencia - SICOSE</title>
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

        .stats-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .stat-box {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
        }

        .stat-box h3 {
            margin: 0 0 5px 0;
            font-size: 12px;
            color: #666;
        }

        .stat-box .value {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>

<body>

    <div class="no-print" style="text-align: right;">
        <button onclick="window.print()" class="btn-print">Imprimir / Guardar como PDF</button>
    </div>

    <div class="header">
        <img src="../images/sbrab_escudo.png" alt="Logo SBRAB" class="logo">
        <h1>Reporte de Asistencia</h1>
        <p>Sistema de Control y Seguimiento (SICOSE)</p>
    </div>

    <?php if ($fecha_inicio || $fecha_fin || $instructor_id): ?>
        <div class="filter-info">
            <strong>Filtros aplicados:</strong>
            <?php if ($fecha_inicio): ?>
                Desde: <?= date('d/m/Y', strtotime($fecha_inicio)) ?>
            <?php endif; ?>
            <?php if ($fecha_fin): ?>
                | Hasta: <?= date('d/m/Y', strtotime($fecha_fin)) ?>
            <?php endif; ?>
            <?php if ($instructor_id): ?>
                <?php
                $stmtInst = $pdo->prepare("SELECT nombres, apellido_paterno, apellido_materno FROM instructores WHERE id = ?");
                $stmtInst->execute([$instructor_id]);
                $inst = $stmtInst->fetch(PDO::FETCH_ASSOC);
                if ($inst) {
                    echo " | Instructor: " . htmlspecialchars($inst['apellido_paterno'] . ' ' . $inst['apellido_materno'] . ' ' . $inst['nombres']);
                }
                ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($registros)): ?>
        <!-- Estadísticas Generales -->
        <div class="stats-summary">
            <div class="stat-box">
                <h3>Total Registros</h3>
                <div class="value"><?= count($registros) ?></div>
            </div>
            <div class="stat-box">
                <h3>Instructores</h3>
                <div class="value"><?= count($estadisticas) ?></div>
            </div>
            <div class="stat-box">
                <h3>Ordinarias</h3>
                <div class="value"><?= array_sum(array_column($estadisticas, 'Ordinaria')) ?></div>
            </div>
            <div class="stat-box">
                <h3>Faltas</h3>
                <div class="value" style="color: #dc3545;"><?= array_sum(array_column($estadisticas, 'Falta')) ?></div>
            </div>
        </div>

        <!-- Estadísticas por Instructor -->
        <div class="section">
            <h2>Estadísticas por Instructor</h2>
            <table>
                <thead>
                    <tr>
                        <th>Instructor</th>
                        <th style="text-align:center;">Ordinaria</th>
                        <th style="text-align:center;">Extraordinaria</th>
                        <th style="text-align:center;">Operación</th>
                        <th style="text-align:center;">Permiso</th>
                        <th style="text-align:center;">Falta</th>
                        <th style="text-align:center;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estadisticas as $stat): ?>
                        <tr>
                            <td><?= htmlspecialchars($stat['nombre']) ?></td>
                            <td style="text-align:center;"><?= $stat['Ordinaria'] ?></td>
                            <td style="text-align:center;"><?= $stat['Extraordinaria'] ?></td>
                            <td style="text-align:center;"><?= $stat['Operación'] ?></td>
                            <td style="text-align:center;"><?= $stat['Permiso'] ?></td>
                            <td style="text-align:center;"><?= $stat['Falta'] ?></td>
                            <td style="text-align:center;"><strong><?= $stat['total'] ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Detalle de Registros -->
        <div class="section">
            <h2>Detalle de Registros</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Instructor</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $registro): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($registro['fecha'])) ?></td>
                            <td><?= htmlspecialchars($registro['apellido_paterno'] . ' ' . $registro['apellido_materno'] . ' ' . $registro['nombres']) ?></td>
                            <td><?= htmlspecialchars($registro['tipo']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>No hay registros de asistencia para mostrar con los filtros aplicados.</p>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p>Generado el <?= date('d/m/Y H:i') ?></p>
        <p>SBRAB - La Paz, Bolivia</p>
    </div>

</body>

</html>