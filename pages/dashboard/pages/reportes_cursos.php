<?php
require_once 'data/db.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SICOSE - Reportes de Cursos</title>
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
                <li><a href="../inicio.php" class="menu-item"><img src="../icons/home.png" alt="Inicio" class="icon" />
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
                <li><a href="../logout.php" class="menu-item"><img src="../icons/salir.png" alt="Salir"
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
            <h2>Reportes de Cursos</h2>

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
                        <label for="tipo">Tipo de Curso:</label>
                        <select id="tipo" name="tipo">
                            <option value="">Todos</option>
                            <option value="Capacitación" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'Capacitación') ? 'selected' : '' ?>>Capacitación</option>
                            <option value="Especialización" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'Especialización') ? 'selected' : '' ?>>Especialización</option>
                            <option value="Actualización" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'Actualización') ? 'selected' : '' ?>>Actualización</option>
                            <option value="Taller" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'Taller') ? 'selected' : '' ?>>Taller</option>
                            <option value="Seminario" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'Seminario') ? 'selected' : '' ?>>Seminario</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit">Filtrar...</button>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>">Limpiar filtros</a>
                        <?php
                        $filtros = [];
                        if (!empty($_GET['instructor_id'])) $filtros[] = 'instructor_id=' . urlencode($_GET['instructor_id']);
                        if (!empty($_GET['tipo'])) $filtros[] = 'tipo=' . urlencode($_GET['tipo']);
                        $queryString = !empty($filtros) ? '?' . implode('&', $filtros) : '';
                        ?>
                        <a href="generar_reporte_cursos.php<?= $queryString ?>" target="_blank" class="btn-generar-reporte" style="text-decoration: none;">Generar Reporte PDF</a>
                    </div>
                </div>
            </form>

            <!-- Reporte de Cursos por Instructor -->
            <div class="report-section">
                <h3>Cursos por Instructor</h3>
                <table class="table-list">
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
                        <?php
                        try {
                            $where = [];
                            $params = [];

                            if (!empty($_GET['instructor_id'])) {
                                $where[] = "i.id = ?";
                                $params[] = $_GET['instructor_id'];
                            }

                            if (!empty($_GET['tipo'])) {
                                $where[] = "c.tipo = ?";
                                $params[] = $_GET['tipo'];
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
                                    c.estado
                                FROM instructor_cursos ic
                                INNER JOIN instructores i ON ic.instructor_id = i.id
                                INNER JOIN cursos c ON ic.curso_id = c.id
                            ";

                            if (!empty($where)) {
                                $sql .= " WHERE " . implode(" AND ", $where);
                            }

                            $sql .= " ORDER BY i.apellido_paterno ASC, c.fecha_inicio DESC";

                            $stmt = $pdo->prepare($sql);
                            $stmt->execute($params);
                            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($resultados) > 0) {
                                foreach ($resultados as $row) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['codigo']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['apellido_paterno'] . ' ' . $row['apellido_materno'] . ' ' . $row['nombres']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['curso_nombre']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['entidad']) . "</td>";
                                    echo "<td>" . date('d/m/Y', strtotime($row['fecha_inicio'])) . "</td>";
                                    echo "<td>" . date('d/m/Y', strtotime($row['fecha_fin'])) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['estado'] ?? '') . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' style='text-align:center;'>No se encontraron resultados</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='8' class='error-message'>Error al cargar datos: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Resumen por Tipo de Curso -->
            <div class="report-section">
                <h3>Resumen por Tipo de Curso</h3>
                <table class="table-list">
                    <thead>
                        <tr>
                            <th>Tipo de Curso</th>
                            <th>Cantidad de Cursos</th>
                            <th>Instructores Asignados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $sql = "
                                SELECT 
                                    c.tipo,
                                    COUNT(DISTINCT c.id) as total_cursos,
                                    COUNT(DISTINCT ic.instructor_id) as total_instructores
                                FROM cursos c
                                LEFT JOIN instructor_cursos ic ON c.id = ic.curso_id
                                GROUP BY c.tipo
                                ORDER BY total_cursos DESC
                            ";

                            $stmt = $pdo->query($sql);
                            $resumen = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($resumen as $row) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
                                echo "<td style='text-align:center;'>" . $row['total_cursos'] . "</td>";
                                echo "<td style='text-align:center;'>" . $row['total_instructores'] . "</td>";
                                echo "</tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='3' class='error-message'>Error al cargar resumen</td></tr>";
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