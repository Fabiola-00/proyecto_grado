<?php
require_once 'data/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM instructores ORDER BY nombres ASC");
    $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p class='error-message'>Error al cargar instructores: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SICOSE - Reportes Instructores</title>
    <link rel="shortcut icon" href="../icons/escudo.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="css_pages/css_instructores1.css">
    <link rel="stylesheet" href="css_pages/css_instructores2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .table-list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }

        .table-list th,
        .table-list td {
            padding: 0.75rem;
            text-align: left;
            border: 1px solid #ddd;
        }

        .table-list thead {
            background-color: #f0f0f0;
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
                <li><a href="asistencia.html" class="menu-item"><img src="../icons/voluntarios.png" alt="Evaluación" class="icon" />
                        <span class="text">Asistencia</span></a></li>
                <li><a href="reportes.html" class="menu-item"><img src="../icons/reportes.png" alt="Reportes" class="icon" />
                        <span class="text">Reportes</span></a></li>
                <li><a href="#" class="menu-item"><img src="../icons/cursos.png" alt="Cursos" class="icon" />
                        <span class="text">Cursos</span></a></li>
                <li><a href="#" class="menu-item"><img src="../icons/operativos.png" alt="Operativos" class="icon" />
                        <span class="text">Operativos</span></a></li>
                <li><a href="#" class="menu-item"><img src="../icons/settings.png" alt="Configuración" class="icon" />
                        <span class="text">Configuración</span></a></li>
                <li><a href="../../login.html" class="menu-item"><img src="../icons/salir.png" alt="Salir" class="icon" />
                        <span class="text">Salir</span></a></li>
            </ul>
        </nav>
    </aside>

    <!--  *******************************************************************************************    -->
    <!-- Contenido Principal -->
    <main id="content" class="content">

        <div class="form-container">
            <h2>Reportes de Instructores</h2>

            <!-- Formulario de filtros en una sola fila -->
            <form method="GET" action="<?= $_SERVER['PHP_SELF'] ?>" class="search-form">
                <div class="filter-row">
                    <div class="filter-item">
                        <label for="busqueda_codigo">Código:</label>
                        <input type="text" id="busqueda_codigo" name="codigo" value="<?= htmlspecialchars($_GET['codigo'] ?? '') ?>" placeholder="ABC123456">
                    </div>
                    <div class="filter-item">
                        <label for="busqueda_nombre">Nombre:</label>
                        <input type="text" id="busqueda_nombre" name="nombre" value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>" placeholder="Buscar por nombre">
                    </div>
                    <div class="filter-item">
                        <label for="busqueda_especialidad">Especialidad:</label>
                        <select id="busqueda_especialidad" name="especialidad">
                            <option value="">Todas</option>
                            <option value="Primeros Auxilios" <?= ($_GET['especialidad'] ?? '') == 'Primeros Auxilios' ? 'selected' : '' ?>>Primeros Auxilios</option>
                            <option value="Rescate en Montaña" <?= ($_GET['especialidad'] ?? '') == 'Rescate en Montaña' ? 'selected' : '' ?>>Rescate en Montaña</option>
                            <option value="Rescate en Selva" <?= ($_GET['especialidad'] ?? '') == 'Rescate en Selva' ? 'selected' : '' ?>>Rescate en Selva</option>
                            <option value="Lucha contra incendios" <?= ($_GET['especialidad'] ?? '') == 'Lucha contra incendios' ? 'selected' : '' ?>>Lucha contra incendios</option>
                            <option value="Natación" <?= ($_GET['especialidad'] ?? '') == 'Natación' ? 'selected' : '' ?>>Natación</option>
                            <option value="K-9" <?= ($_GET['especialidad'] ?? '') == 'K-9' ? 'selected' : '' ?>>K-9</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="busqueda_estado">Estado:</label>
                        <select id="busqueda_estado" name="estado">
                            <option value="">Todos</option>
                            <option value="Evaluado" <?= ($_GET['estado'] ?? '') == 'Evaluado' ? 'selected' : '' ?>>Evaluado</option>
                            <option value="No evaluado" <?= ($_GET['estado'] ?? '') == 'No evaluado' ? 'selected' : '' ?>>No evaluado</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit">Filtrar...</button>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>">Limpiar filtros</a>
                    </div>
                </div>
            </form>

            <!-- Tabla ocupando el ancho total -->
            <table class="table-list full-width-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Grado</th>
                        <th>Apellido Paterno</th>
                        <th>Apellido Materno</th>
                        <th>Nombres</th>
                        <th>Especialidad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require_once 'data/db.php';

                    $where = [];
                    $params = [];

                    // Filtro por código
                    if (!empty($_GET['codigo'])) {
                        $where[] = "codigo LIKE ?";
                        $params[] = "%{$_GET['codigo']}%";
                    }

                    // Filtro por nombre
                    if (!empty($_GET['nombre'])) {
                        $nombre = trim($_GET['nombre']);
                        $where[] = "(nombres LIKE ? OR apellido_paterno LIKE ? OR apellido_materno LIKE ?)";
                        $params[] = "%$nombre%";
                        $params[] = "%$nombre%";
                        $params[] = "%$nombre%";
                    }

                    // Filtro por especialidad
                    if (!empty($_GET['especialidad'])) {
                        $where[] = "especialidad = ?";
                        $params[] = $_GET['especialidad'];
                    }

                    // Filtro por estado
                    if (!empty($_GET['estado'])) {
                        $where[] = "estado = ?";
                        $params[] = $_GET['estado'];
                    }

                    try {
                        $sql = "SELECT * FROM instructores";
                        if (!empty($where)) {
                            $sql .= " WHERE " . implode(" AND ", $where);
                        }
                        $sql .= " ORDER BY apellido_paterno ASC, apellido_materno ASC, nombres ASC";

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($instructores as $instructor) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($instructor['codigo']) . "</td>";
                            echo "<td>" . htmlspecialchars($instructor['grado']) . "</td>";
                            echo "<td>" . htmlspecialchars($instructor['apellido_paterno']) . "</td>";
                            echo "<td>" . htmlspecialchars($instructor['apellido_materno']) . "</td>";
                            echo "<td>" . htmlspecialchars($instructor['nombres']) . "</td>";
                            echo "<td>" . htmlspecialchars($instructor['especialidad']) . "</td>";
                            echo "<td>" . ucfirst(htmlspecialchars($instructor['estado'])) . "</td>";
                            echo "<td>
                      <a href='generar_reporte.php?id={$instructor['id']}' target='_blank' class='btn-report' style='background-color: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; display: inline-block;'><i class='fas fa-file-pdf'></i> Generar reporte</a>
                    </td>";
                            echo "</tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='8' class='error-message'>Error al cargar datos: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

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