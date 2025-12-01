<?php
require_once 'data/db.php';

// Obtener todos los instructores
try {
    $stmt = $pdo->query("SELECT id, nombres, apellido_paterno, apellido_materno FROM instructores ORDER BY apellido_paterno ASC, apellido_materno ASC, nombres ASC");
    $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p class='error-message'>Error al cargar instructores: " . $e->getMessage() . "</p>";
    $instructores = [];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SICOSE - Reg. Asistencia</title>
    <link rel="shortcut icon" href="../icons/escudo.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="css_pages/css_instructores1.css">
    <style>
        .asistencia-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .fecha-selector {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .fecha-selector label {
            font-weight: bold;
            font-size: 16px;
        }

        .fecha-selector input[type="date"] {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .fecha-selector button {
            padding: 8px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .fecha-selector button:hover {
            background-color: #0056b3;
        }

        .asistencia-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .asistencia-table th,
        .asistencia-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .asistencia-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .asistencia-table select {
            width: 100%;
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .btn-guardar {
            display: block;
            margin: 20px auto;
            padding: 12px 40px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-guardar:hover {
            background-color: #218838;
        }

        .success-message,
        .error-message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
                <li><a href="#" class="menu-item"><img src="../icons/cursos.png" alt="Cursos" class="icon" />
                        <span class="text">Cursos</span></a></li>
                <li><a href="#" class="menu-item"><img src="../icons/operativos.png" alt="Operativos" class="icon" />
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

        <div class="asistencia-container">
            <h2>Asistencia</h2>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">Asistencia registrada correctamente.</div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <form method="POST" action="guardar_asistencia.php">
                <div class="fecha-selector">
                    <label for="fecha">Seleccionar Fecha:</label>
                    <input type="date" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>" required>
                    <button type="button" onclick="cargarAsistencia()">Buscar</button>
                </div>

                <table class="asistencia-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo de asistencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($instructores as $instructor): ?>
                            <tr>
                                <td><?= htmlspecialchars($instructor['apellido_paterno'] . ' ' . $instructor['apellido_materno'] . ' ' . $instructor['nombres']) ?></td>
                                <td>
                                    <select name="asistencia[<?= $instructor['id'] ?>]" required>
                                        <option value="">Seleccione...</option>
                                        <option value="Ordinaria">Ordinaria</option>
                                        <option value="Extraordinaria">Extraordinaria</option>
                                        <option value="Operación">Operación</option>
                                        <option value="Permiso">Permiso</option>
                                        <option value="Falta">Falta</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <button type="submit" class="btn-guardar">Guardar</button>
            </form>
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

        function cargarAsistencia() {
            const fecha = document.getElementById('fecha').value;
            if (fecha) {
                window.location.href = `asistencia_reg.php?fecha=${fecha}`;
            }
        }
    </script>
</body>

</html>