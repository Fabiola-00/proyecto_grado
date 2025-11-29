<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SICOSE - Inst. Registro</title>
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
                <li><a href="asistencia.html" class="menu-item"><img src="../icons/asistencia.png" alt="Asistencia" class="icon" />
                        <span class="text">Asistencia</span></a></li>
                <li><a href="#" class="menu-item"><img src="../icons/reportes.png" alt="Reportes" class="icon" />
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
        <br>
        <div class="form-container">
            <h2>Registro de Instructor</h2>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">Datos guardados correctamente.</div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <form action="registrar_instructor.php" method="POST">
                <!-- Código -->
                <label for="codigo">Código (3 letras + 6 números):</label>
                <input type="text" id="codigo" name="codigo" required pattern="[A-Za-z]{3}\d{6}" title="Ejemplo: ABC123456" autocomplete="off">

                <!-- Grado -->
                <label for="grado">Grado:</label>
                <select id="grado" name="grado" required>
                    <option value="">Seleccione...</option>
                    <option value="Inst. Inicial">Inst. Inicial</option>
                    <option value="Inst. Segundo">Inst. Segundo</option>
                    <option value="Inst. Primero">Inst. Primero</option>
                    <option value="Inst. Principal">Inst. Principal</option>
                    <option value="Inst. Mayor">Inst. Mayor</option>
                </select>

                <!-- Nombres -->
                <label for="nombres">Nombres:</label>
                <input type="text" id="nombres" name="nombres" required autocomplete="off">

                <!-- Apellido Paterno -->
                <label for="apellido_paterno">Apellido Paterno:</label>
                <input type="text" id="apellido_paterno" name="apellido_paterno" required autocomplete="off">

                <!-- Apellido Materno -->
                <label for="apellido_materno">Apellido Materno:</label>
                <input type="text" id="apellido_materno" name="apellido_materno" required autocomplete="off">

                <!-- Cédula -->
                <label for="cedula">Cédula de Identidad (7 dígitos):</label>
                <input type="text" id="cedula" name="cedula" required pattern="\d{7}" title="Ejemplo: 1234567" autocomplete="off">

                <!-- Fecha de Nacimiento -->
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>

                <!-- Especialidad -->
                <label for="especialidad">Especialidad:</label>
                <select id="especialidad" name="especialidad" required>
                    <option value="">Seleccione...</option>
                    <option value="Primeros Auxilios">Primeros Auxilios</option>
                    <option value="Rescate en Montaña">Rescate en Montaña</option>
                    <option value="Rescate en Selva">Rescate en Selva</option>
                    <option value="Lucha contra incendios">Lucha contra incendios</option>
                    <option value="Natación">Natación</option>
                    <option value="K-9">K-9</option>
                </select>

                <!-- Estado -->
                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="">Seleccione...</option>
                    <option value="Evaluado">Evaluado</option>
                    <option value="No evaluado">No evaluado</option>
                </select>

                <!-- Botón -->
                <button type="submit" class="btn-registro">Registrar Instructor</button>
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