<?php
require_once 'data/db.php';

// Obtener todos los instructores
try {
    $stmt = $pdo->query("SELECT id, codigo, nombres, apellido_paterno, apellido_materno, especialidad FROM instructores ORDER BY apellido_paterno ASC");
    $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p class='error-message'>Error al cargar instructores: " . $e->getMessage() . "</p>";
    $instructores = [];
}

// Obtener todos los cursos
try {
    $stmt = $pdo->query("SELECT id, nombre, tipo, fecha_inicio, fecha_fin, estado FROM cursos ORDER BY fecha_inicio DESC");
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p class='error-message'>Error al cargar cursos: " . $e->getMessage() . "</p>";
    $cursos = [];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SICOSE - Asignación de Cursos</title>
    <link rel="shortcut icon" href="../icons/escudo.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="css_pages/css_instructores1.css">
    <link rel="stylesheet" href="css_pages/css_instructores2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .assignment-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .assignment-form h3 {
            margin-top: 0;
            color: #333;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 15px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .form-group select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .btn-assign {
            padding: 10px 30px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-assign:hover {
            background-color: #218838;
        }

        .table-list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

        .btn-remove {
            padding: 6px 12px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-remove:hover {
            background-color: #c82333;
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
            <h2>Asignación de Cursos a Instructores</h2>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-message"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <!-- Formulario de asignación -->
            <div class="assignment-form">
                <h3>Asignar Curso a Instructor</h3>
                <form action="asignar_curso.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="instructor_id">Instructor:</label>
                            <select id="instructor_id" name="instructor_id" required>
                                <option value="">Seleccione un instructor...</option>
                                <?php foreach ($instructores as $instructor): ?>
                                    <option value="<?= $instructor['id'] ?>">
                                        <?= htmlspecialchars($instructor['codigo'] . ' - ' . $instructor['apellido_paterno'] . ' ' . $instructor['apellido_materno'] . ' ' . $instructor['nombres'] . ' (' . $instructor['especialidad'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="curso_id">Curso:</label>
                            <select id="curso_id" name="curso_id" required>
                                <option value="">Seleccione un curso...</option>
                                <?php foreach ($cursos as $curso): ?>
                                    <option value="<?= $curso['id'] ?>">
                                        <?= htmlspecialchars($curso['nombre'] . ' (' . $curso['tipo'] . ') - ' . ($curso['estado'] ?? 'proximo')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn-assign">Asignar</button>
                    </div>
                </form>
            </div>

            <!-- Tabla de asignaciones -->
            <h3>Asignaciones Actuales</h3>
            <table class="table-list">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Instructor</th>
                        <th>Curso</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $sql = "
                            SELECT 
                                ic.id as asignacion_id,
                                i.codigo,
                                i.nombres,
                                i.apellido_paterno,
                                i.apellido_materno,
                                c.nombre as curso_nombre,
                                c.tipo,
                                c.fecha_inicio,
                                c.fecha_fin,
                                c.estado
                            FROM instructor_cursos ic
                            INNER JOIN instructores i ON ic.instructor_id = i.id
                            INNER JOIN cursos c ON ic.curso_id = c.id
                            ORDER BY i.apellido_paterno ASC, c.fecha_inicio DESC
                        ";

                        $stmt = $pdo->query($sql);
                        $asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (count($asignaciones) > 0) {
                            foreach ($asignaciones as $asig) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($asig['codigo']) . "</td>";
                                echo "<td>" . htmlspecialchars($asig['apellido_paterno'] . ' ' . $asig['apellido_materno'] . ' ' . $asig['nombres']) . "</td>";
                                echo "<td>" . htmlspecialchars($asig['curso_nombre']) . "</td>";
                                echo "<td>" . htmlspecialchars($asig['tipo']) . "</td>";
                                echo "<td>" . htmlspecialchars($asig['estado'] ?? '') . "</td>";
                                echo "<td>" . date('d/m/Y', strtotime($asig['fecha_inicio'])) . "</td>";
                                echo "<td>" . date('d/m/Y', strtotime($asig['fecha_fin'])) . "</td>";
                                echo "<td>
                                        <form method='POST' action='eliminar_asignacion.php' style='display:inline;'>
                                            <input type='hidden' name='asignacion_id' value='{$asig['asignacion_id']}'>
                                            <button type='submit' class='btn-remove' onclick='return confirm(\"¿Está seguro de eliminar esta asignación?\")'>
                                                <i class='fas fa-trash'></i> Eliminar
                                            </button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' style='text-align:center;'>No hay asignaciones registradas</td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='8' class='error-message'>Error al cargar asignaciones: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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