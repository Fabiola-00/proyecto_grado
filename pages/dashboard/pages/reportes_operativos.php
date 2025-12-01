<?php
require_once 'data/db.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SICOSE - Reportes de Operativos</title>
    <link rel="shortcut icon" href="../icons/escudo.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="css_pages/css_instructores1.css">
    <link rel="stylesheet" href="css_pages/css_instructores2.css">

    <style>
        .btn-generar-reporte {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            margin-left: 10px;
            display: inline-block;
        }

        .btn-generar-reporte:hover {
            background-color: #218838;
        }

        .report-section {
            margin: 30px 0;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .report-section h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .table-list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table-list th,
        .table-list td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .table-list thead {
            background-color: #f0f0f0;
        }

        .table-list tbody tr:hover {
            background-color: #f8f9fa;
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
                <li><a href="cursos.html" class="menu-item"><img src="../icons/cursos.png" alt="Cursos" class="icon" />
                        <span class="text">Cursos</span></a></li>
                <li><a href="operativos.html" class="menu-item"><img src="../icons/operativos.png" alt="Operativos"
                            class="icon" />
                        <span class="text">Operativos</span></a></li>
                <li><a href="../../login.html" class="menu-item"><img src="../icons/salir.png" alt="Salir"
                            class="icon" />
                        <span class="text">Salir</span></a></li>
            </ul>
        </nav>
    </aside>

    <!--  *******************************************************************************************    -->
    <!-- Contenido Principal -->
    <main id="content" class="content">
        <br>
        <div class="form-container">
            <h2>Reportes de Operativos</h2>

            <!-- Filtros -->
            <form method="GET" action="<?= $_SERVER['PHP_SELF'] ?>" class="search-form">
                <div class="filter-row">
                    <div class="filter-item">
                        <label for="instructor_id">Instructor:</label>
                        <select id="instructor_id" name="instructor_id">
                            <option value="">Todos</option>
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT id, codigo, nombres, apellido_paterno, apellido_materno FROM instructores ORDER BY apellido_paterno ASC");
                                $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($instructores as $instructor) {
                                    $selected = (isset($_GET['instructor_id']) && $_GET['instructor_id'] == $instructor['id']) ? 'selected' : '';
                                    echo "<option value='{$instructor['id']}' $selected>" .
                                        htmlspecialchars($instructor['codigo'] . ' - ' . $instructor['apellido_paterno'] . ' ' . $instructor['apellido_materno'] . ' ' . $instructor['nombres']) .
                                        "</option>";
                                }
                            } catch (PDOException $e) {
                                echo "<option value=''>Error al cargar instructores</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="tipo">Tipo de Operativo:</label>
                        <select id="tipo" name="tipo">
                            <option value="">Todos</option>
                            <option value="Preventivo" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'Preventivo') ? 'selected' : '' ?>>Preventivo</option>
                            <option value="Reactivo" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'Reactivo') ? 'selected' : '' ?>>Reactivo</option>
                            <option value="Especial" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'Especial') ? 'selected' : '' ?>>Especial</option>
                            <option value="Emergencia" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'Emergencia') ? 'selected' : '' ?>>Emergencia</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="estado">Estado:</label>
                        <select id="estado" name="estado">
                            <option value="">Todos</option>
                            <option value="Planificado" <?= (isset($_GET['estado']) && $_GET['estado'] == 'Planificado') ? 'selected' : '' ?>>Planificado</option>
                            <option value="En ejecución" <?= (isset($_GET['estado']) && $_GET['estado'] == 'En ejecución') ? 'selected' : '' ?>>En ejecución</option>
                            <option value="Finalizado" <?= (isset($_GET['estado']) && $_GET['estado'] == 'Finalizado') ? 'selected' : '' ?>>Finalizado</option>
                            <option value="Suspendido" <?= (isset($_GET['estado']) && $_GET['estado'] == 'Suspendido') ? 'selected' : '' ?>>Suspendido</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit">Filtrar...</button>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>">Limpiar filtros</a>
                        <?php
                        $filtros = [];
                        if (!empty($_GET['instructor_id'])) $filtros[] = 'instructor_id=' . urlencode($_GET['instructor_id']);
                        if (!empty($_GET['tipo'])) $filtros[] = 'tipo=' . urlencode($_GET['tipo']);
                        if (!empty($_GET['estado'])) $filtros[] = 'estado=' . urlencode($_GET['estado']);
                        $queryString = !empty($filtros) ? '?' . implode('&', $filtros) : '';
                        ?>
                        <a href="generar_reporte_operativos.php<?= $queryString ?>" target="_blank" class="btn-generar-reporte" style="text-decoration: none;">Generar Reporte PDF</a>
                    </div>
                </div>
            </form>

            <!-- Reporte de Operativos por Instructor -->
            <div class="report-section">
                <h3>Operativos por Instructor</h3>
                <table class="table-list">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Instructor</th>
                            <th>Función</th>
                            <th>Tipo</th>
                            <th>Entidad Responsable</th>
                            <th>Departamento</th>
                            <th>Zona</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Final</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $where = [];
                            $params = [];

                            if (!empty($_GET['instructor_id'])) {
                                $where[] = "i.id = ?";
                                $params[] = $_GET['instructor_id'];
                            }

                            if (!empty($_GET['tipo'])) {
                                $where[] = "o.tipo = ?";
                                $params[] = $_GET['tipo'];
                            }

                            if (!empty($_GET['estado'])) {
                                $where[] = "o.estado = ?";
                                $params[] = $_GET['estado'];
                            }

                            $sql = "
                                SELECT 
                                    i.codigo,
                                    i.nombres,
                                    i.apellido_paterno,
                                    i.apellido_materno,
                                    io.funcion,
                                    o.tipo,
                                    o.entidad_responsable,
                                    o.departamento,
                                    o.zona,
                                    o.fecha_inicio,
                                    o.fecha_final,
                                    o.estado
                                FROM instructor_operativos io
                                INNER JOIN instructores i ON io.instructor_id = i.id
                                INNER JOIN operativos o ON io.operativo_id = o.id
                            ";

                            if (!empty($where)) {
                                $sql .= " WHERE " . implode(" AND ", $where);
                            }

                            $sql .= " ORDER BY i.apellido_paterno ASC, o.fecha_inicio DESC";

                            $stmt = $pdo->prepare($sql);
                            $stmt->execute($params);
                            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($resultados) > 0) {
                                foreach ($resultados as $row) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['codigo']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['apellido_paterno'] . ' ' . $row['apellido_materno'] . ' ' . $row['nombres']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['funcion'] ?? 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['entidad_responsable']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['departamento']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['zona']) . "</td>";
                                    echo "<td>" . date('d/m/Y', strtotime($row['fecha_inicio'])) . "</td>";
                                    echo "<td>" . date('d/m/Y', strtotime($row['fecha_final'])) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['estado']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='10' style='text-align:center;'>No se encontraron resultados</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='10' class='error-message'>Error al cargar datos: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Resumen por Tipo de Operativo -->
            <div class="report-section">
                <h3>Resumen por Tipo de Operativo</h3>
                <table class="table-list">
                    <thead>
                        <tr>
                            <th>Tipo de Operativo</th>
                            <th>Cantidad de Operativos</th>
                            <th>Instructores Asignados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $sql = "
                                SELECT 
                                    o.tipo,
                                    COUNT(DISTINCT o.id) as total_operativos,
                                    COUNT(DISTINCT io.instructor_id) as total_instructores
                                FROM operativos o
                                LEFT JOIN instructor_operativos io ON o.id = io.operativo_id
                                GROUP BY o.tipo
                                ORDER BY total_operativos DESC
                            ";

                            $stmt = $pdo->query($sql);
                            $resumen = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($resumen) > 0) {
                                foreach ($resumen as $row) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
                                    echo "<td style='text-align:center;'>" . $row['total_operativos'] . "</td>";
                                    echo "<td style='text-align:center;'>" . $row['total_instructores'] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' style='text-align:center;'>No hay datos disponibles</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='3' class='error-message'>Error al cargar resumen: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Resumen por Estado -->
            <div class="report-section">
                <h3>Resumen por Estado</h3>
                <table class="table-list">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Cantidad de Operativos</th>
                            <th>Instructores Asignados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $sql = "
                                SELECT 
                                    o.estado,
                                    COUNT(DISTINCT o.id) as total_operativos,
                                    COUNT(DISTINCT io.instructor_id) as total_instructores
                                FROM operativos o
                                LEFT JOIN instructor_operativos io ON o.id = io.operativo_id
                                GROUP BY o.estado
                                ORDER BY total_operativos DESC
                            ";

                            $stmt = $pdo->query($sql);
                            $resumen_estado = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($resumen_estado) > 0) {
                                foreach ($resumen_estado as $row) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['estado']) . "</td>";
                                    echo "<td style='text-align:center;'>" . $row['total_operativos'] . "</td>";
                                    echo "<td style='text-align:center;'>" . $row['total_instructores'] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' style='text-align:center;'>No hay datos disponibles</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='3' class='error-message'>Error al cargar resumen: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
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