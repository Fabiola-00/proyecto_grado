<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SICOSE - Registro de Cursos</title>
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
        <br>
        <div class="form-container">
            <h2>Registro de Curso</h2>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">Curso registrado correctamente.</div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <form action="registrar_curso.php" method="POST">
                <!-- Tipo -->
                <label for="tipo">Tipo:</label>
                <select id="tipo" name="tipo" required>
                    <option value="">Seleccione...</option>
                    <option value="Capacitación">Capacitación</option>
                    <option value="Especialización">Especialización</option>
                    <option value="Actualización">Actualización</option>
                    <option value="Taller">Taller</option>
                    <option value="Seminario">Seminario</option>
                </select>

                <!-- Entidad -->
                <label for="entidad">Entidad:</label>
                <input type="text" id="entidad" name="entidad" required autocomplete="off" placeholder="Nombre de la entidad organizadora">

                <!-- Nombre del Curso -->
                <label for="nombre">Nombre del Curso:</label>
                <input type="text" id="nombre" name="nombre" required autocomplete="off" placeholder="Nombre completo del curso">

                <!-- Fecha de Inicio -->
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>

                <!-- Fecha de Fin -->
                <label for="fecha_fin">Fecha de Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required>

                <!-- Estado -->
                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="">Seleccione...</option>
                    <option value="Próximo">Próximo</option>
                    <option value="En curso">En curso</option>
                    <option value="Finalizado">Finalizado</option>
                </select>

                <!-- Observaciones -->
                <label for="observaciones">Observaciones:</label>
                <textarea id="observaciones" name="observaciones" rows="4" cols="60" placeholder="Observaciones adicionales (opcional)"></textarea>
                <br><br>
                <!-- Botón -->
                <button type="submit" class="btn-registro">Registrar Curso</button>
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