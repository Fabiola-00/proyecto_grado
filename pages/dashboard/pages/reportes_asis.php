<?php
require_once 'data/db.php';

// Obtener parámetros de filtro
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$instructor_id = $_GET['instructor_id'] ?? '';

// Obtener lista de instructores para el filtro
try {
    $stmt = $pdo->query("SELECT id, nombres, apellido_paterno, apellido_materno FROM instructores ORDER BY apellido_paterno ASC");
    $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $instructores = [];
}

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
    $error = $e->getMessage();
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SICOSE - Reportes Asistencia</title>
    <link rel="shortcut icon" href="../icons/escudo.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="css_pages/css_instructores1.css">
    <link rel="stylesheet" href="css_pages/css_instructores2.css">
    <style>
        .report-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .filter-row {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-item {
            flex: 1;
            min-width: 200px;
        }

        .filter-item label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .filter-item input,
        .filter-item select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
        }

        .filter-actions button,
        .filter-actions a {
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .filter-actions button {
            background-color: #007bff;
            color: white;
        }

        .filter-actions a {
            background-color: #6c757d;
            color: white;
        }

        .stats-section {
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
        }

        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
        }

        .table-section table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table-section th,
        .table-section td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .table-section th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .tipo-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .tipo-ordinaria {
            background-color: #d4edda;
            color: #155724;
        }

        .tipo-extraordinaria {
            background-color: #cce5ff;
            color: #004085;
        }

        .tipo-operacion {
            background-color: #fff3cd;
            color: #856404;
        }

        .tipo-permiso {
            background-color: #f8d7da;
            color: #721c24;
        }

        .tipo-falta {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>

<body class="sidebar-expanded">

    <!-- Barra Superior -->
    <header class="top-bar">
        <div class="logo-container">
            <button id="toggleSidebar" aria-label="Alternar menú lateral">
                <img src="../images/sbrab_escudo.png" alt="Logo" class="logo" />
                <img src="../icons/navi.png" class="menu-toggle-icon">
            </button>
        </div>
    </header>

    <!-- Menú Lateral -->
    <aside id="sidebar" class="sidebar">
        <nav class="sidebar-nav">
            <ul>
                <li><a href="../inicio.html" class="menu-item"><img src="../icons/home.png" alt="Inicio" class="icon" />
                        <span class="text">Inicio</span></a></li>
                <li><a href="instructores.html" class="menu-item"><img src="../icons/instructores.png" alt="Registro"
                            class="icon" />
                        <span class="text">Instructores</span></a></li>
                <li><a href="asistencia.html" class="menu-item"><img src="../icons/asistencia.png" alt="Asistencia"
                            class="icon" />
                        <span class="text">Asistencia</span></a></li>
                <li><a href="reportes.html" class="menu-item"><img src="../icons/reportes.png" alt="Reportes"
                            class="icon" />
                        <span class="text">Reportes</span></a></li>
                <li><a href="#" class="menu-item"><img src="../icons/cursos.png" alt="Cursos" class="icon" />
                        <span class="text">Cursos</span></a></li>
                <li><a href="#" class="menu-item"><img src="../icons/operativos.png" alt="Operativos" class="icon" />
                        <span class="text">Operativos</span></a></li>
                <li><a href="#" class="menu-item"><img src="../icons/settings.png" alt="Configuración" class="icon" />
                        <span class="text">Configuración</span></a></li>
                <li><a href="../../login.html" class="menu-item"><img src="../icons/salir.png" alt="Salir"
                            class="icon" />
                        <span class="text">Salir</span></a></li>
            </ul>
        </nav>
    </aside>

    <!--  *******************************************************************************************    -->
    <!-- Contenido Principal -->
    <main id="content" class="content">

        <div class="report-container">
            <h2>Reportes de Asistencia</h2>

            <!-- Sección de Filtros -->
            <div class="filter-section">
                <form method="GET" action="<?= $_SERVER['PHP_SELF'] ?>">
                    <div class="filter-row">
                        <div class="filter-item">
                            <label for="fecha_inicio">Fecha Inicio:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>">
                        </div>
                        <div class="filter-item">
                            <label for="fecha_fin">Fecha Fin:</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>">
                        </div>
                        <div class="filter-item">
                            <label for="instructor_id">Instructor:</label>
                            <select id="instructor_id" name="instructor_id">
                                <option value="">Todos</option>
                                <?php foreach ($instructores as $inst): ?>
                                    <option value="<?= $inst['id'] ?>" <?= $instructor_id == $inst['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($inst['apellido_paterno'] . ' ' . $inst['apellido_materno'] . ' ' . $inst['nombres']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-actions">
                            <button type="submit">Filtrar</button>
                            <a href="<?= $_SERVER['PHP_SELF'] ?>">Limpiar</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Estadísticas Generales -->
            <?php if (!empty($registros)): ?>
                <div class="stats-section">
                    <h3>Estadísticas Generales</h3>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3>Total Registros</h3>
                            <div class="value"><?= count($registros) ?></div>
                        </div>
                        <div class="stat-card">
                            <h3>Instructores</h3>
                            <div class="value"><?= count($estadisticas) ?></div>
                        </div>
                        <div class="stat-card">
                            <h3>Ordinarias</h3>
                            <div class="value"><?= array_sum(array_column($estadisticas, 'Ordinaria')) ?></div>
                        </div>
                        <div class="stat-card">
                            <h3>Faltas</h3>
                            <div class="value" style="color: #dc3545;"><?= array_sum(array_column($estadisticas, 'Falta')) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas por Instructor -->
                <div class="stats-section">
                    <h3>Estadísticas por Instructor</h3>
                    <div class="table-section">
                        <table>
                            <thead>
                                <tr>
                                    <th>Instructor</th>
                                    <th>Ordinaria</th>
                                    <th>Extraordinaria</th>
                                    <th>Operación</th>
                                    <th>Permiso</th>
                                    <th>Falta</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estadisticas as $stat): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($stat['nombre']) ?></td>
                                        <td><?= $stat['Ordinaria'] ?></td>
                                        <td><?= $stat['Extraordinaria'] ?></td>
                                        <td><?= $stat['Operación'] ?></td>
                                        <td><?= $stat['Permiso'] ?></td>
                                        <td><?= $stat['Falta'] ?></td>
                                        <td><strong><?= $stat['total'] ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Detalle de Registros -->
                <div class="stats-section">
                    <h3>Detalle de Registros</h3>
                    <div class="table-section">
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
                                        <td>
                                            <span class="tipo-badge tipo-<?= strtolower($registro['tipo']) ?>">
                                                <?= htmlspecialchars($registro['tipo']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <p>No hay registros de asistencia para mostrar.</p>
                    <p>Utiliza los filtros para buscar o <a href="asistencia_reg.php">registra nueva asistencia</a>.</p>
                </div>
            <?php endif; ?>

        </div>

    </main>
    <!--  *******************************************************************************************    -->
    <!-- Script para funcionalidad del menú -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            const body = document.body;

            toggleBtn.addEventListener('click', function() {
                body.classList.toggle('sidebar-collapsed');
                // Accesibilidad
                const isCollapsed = body.classList.contains('sidebar-collapsed');
                this.setAttribute('aria-expanded', !isCollapsed);
            });
        });
    </script>
</body>

</html>