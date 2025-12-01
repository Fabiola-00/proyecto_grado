<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SICOSE - Registro de Operativos</title>
    <link rel="shortcut icon" href="../icons/escudo.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="css_pages/css_instructores1.css">
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
            <h2>Registro de Operativo</h2>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">Operativo registrado correctamente.</div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <form action="registrar_operativo.php" method="POST">
                <!-- Tipo -->
                <label for="tipo">Tipo:</label>
                <select id="tipo" name="tipo" required>
                    <option value="">Seleccione...</option>
                    <option value="Preventivo">Preventivo</option>
                    <option value="Reactivo">Reactivo</option>
                    <option value="Especial">Especial</option>
                    <option value="Emergencia">Emergencia</option>
                </select>

                <!-- Entidad Responsable -->
                <label for="entidad_responsable">Entidad Responsable:</label>
                <input type="text" id="entidad_responsable" name="entidad_responsable" required autocomplete="off" placeholder="Nombre de la entidad responsable">

                <!-- Estado -->
                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="">Seleccione...</option>
                    <option value="Planificado">Planificado</option>
                    <option value="En ejecución">En ejecución</option>
                    <option value="Finalizado">Finalizado</option>
                    <option value="Suspendido">Suspendido</option>
                </select>

                <!-- Departamento -->
                <label for="departamento">Departamento:</label>
                <input type="text" id="departamento" name="departamento" required autocomplete="off" placeholder="Departamento">

                <!-- Zona -->
                <label for="zona">Zona:</label>
                <input type="text" id="zona" name="zona" required autocomplete="off" placeholder="Zona del operativo">

                <!-- Fecha de Inicio -->
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>

                <!-- Fecha Final -->
                <label for="fecha_final">Fecha Final:</label>
                <input type="date" id="fecha_final" name="fecha_final" required>

                <br><br>
                <!-- Botón -->
                <button type="submit" class="btn-registro">Registrar Operativo</button>
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
    </script>
</body>

</html>