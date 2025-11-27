<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SICOSE - Inst. Evaluación</title>
    <link rel="shortcut icon" href="../icons/escudo.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="css_pages/css_instructores1.css">
    <link rel="stylesheet" href="css_pages/css_instructores_eval.css">
    <!-- Cargar TensorFlow.js -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.20.0/dist/tf.min.js"></script>

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
                <li><a href="voluntarios.html" class="menu-item"><img src="../icons/voluntarios.png" alt="Evaluación" class="icon" />
                        <span class="text">Voluntarios</span></a></li>
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

        <div class="container">
            <h2>Evaluación de Instructores</h2>

            <!-- Buscador de Instructor -->
            <form method="POST" action="" style="width: 100%; margin-bottom: 5px;">
                <div class="form-group" style="justify-content: center; gap: 10px;">
                    <label for="codigo_instructor" style="flex: 0 0 auto;">Código Instructor:</label>
                    <input type="text" name="codigo_instructor" id="codigo_instructor" required style="width: 150px;">
                    <button type="submit" name="buscar_instructor" style="width: auto; margin-top: 0; padding: 8px 15px;">Buscar</button>
                </div>
            </form>

            <?php
            // Lógica de búsqueda
            $instructor_encontrado = null;
            $mensaje_error = "";
            $codigo_buscado = "";

            if (isset($_POST['buscar_instructor'])) {
                $codigo_buscado = $_POST['codigo_instructor'];

                // Conexión a la base de datos (usando la ruta relativa correcta)
                $db_file = __DIR__ . '/data/db_sbrab.sqlite';

                try {
                    $pdo = new PDO('sqlite:' . $db_file);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $stmt = $pdo->prepare("SELECT * FROM instructores WHERE codigo = :codigo");
                    $stmt->execute([':codigo' => $codigo_buscado]);
                    $instructor = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($instructor) {
                        // Verificar si ya fue evaluado
                        $stmt_eval = $pdo->prepare("SELECT COUNT(*) FROM evaluaciones WHERE instructor_id = :id");
                        $stmt_eval->execute([':id' => $instructor['id']]);
                        $estado_evaluacion = $stmt_eval->fetchColumn() > 0 ? "Evaluado" : "No Evaluado";
                        $instructor['estado_evaluacion'] = $estado_evaluacion;

                        $instructor_encontrado = $instructor;
                    } else {
                        $mensaje_error = "Código erróneo";
                    }
                } catch (PDOException $e) {
                    $mensaje_error = "Error de base de datos: " . $e->getMessage();
                }
            }
            ?>

            <?php if ($instructor_encontrado): ?>
                <div style="text-align: center; margin-bottom: 10px; color: #27ae60; font-weight: bold;">
                    <p>Instructor: <?php echo htmlspecialchars($instructor_encontrado['nombres'] . ' ' . $instructor_encontrado['apellido_paterno']); ?> (<?php echo htmlspecialchars($instructor_encontrado['estado_evaluacion']); ?>)</p>
                    <input type="hidden" id="instructor_id" value="<?php echo $instructor_encontrado['id']; ?>">
                </div>
            <?php elseif ($mensaje_error): ?>
                <div style="text-align: center; margin-bottom: 10px; color: #e74c3c; font-weight: bold;">
                    <p><?php echo $mensaje_error; ?></p>
                </div>
            <?php endif; ?>

            <div id="form-container">
                <!-- Inputs de evaluación -->
                <div class="form-group"><label>EE (Estabilidad Emocional) 0 - 10</label><input min="0" max="10"
                        type="number" id="input-0" value="0">
                </div>
                <div class="form-group"><label>ME (Manejo del Estrés) 0 - 40</label><input min="0" max="40" type="number"
                        id="input-1" value="0">
                </div>
                <div class="form-group"><label>RE (Resiliencia) 0 - 76</label><input min="0" max="76" type="number"
                        id="input-2" value="0">
                </div>
                <div class="form-group"><label>TE (Trabajo en Equipo) 1 - 4</label><input min="1" max="4" type="number"
                        id="input-3" value="1">
                </div>
                <div class="form-group"><label>VS (Vocación de Servicio) 0 - 100</label><input min="0" max="100"
                        type="number" id="input-4" value="0"></div>
                <div class="form-group"><label>RS (Responsabilidad) 1 - 3</label><input min="1" max="3" type="number"
                        id="input-5" value="1">
                </div>
                <div class="form-group"><label>DI (Disciplina) 0 - 30</label><input min="0" max="30" type="number"
                        id="input-6" value="0">
                </div>
                <div class="form-group"><label>TD (Toma de Decisiones) 1 - 3</label><input min="1" max="3" type="number"
                        id="input-7" value="1">
                </div>
                <div class="form-group"><label>EM (Empatía) 0 - 28</label><input min="0" max="28" type="number" id="input-8"
                        value="0">
                </div>
            </div>
            <br>

            <button onclick="predecir()">Evaluar</button>
            <p id="resultado"></p>
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

        // Segmento del Modelo de Evaluación
        let modelo;

        // Cargar el modelo al iniciar
        async function cargarModelo() {
            try {
                console.log("Cargando modelo...");
                modelo = await tf.loadLayersModel('modelo/model.json?v=' + new Date().getTime());
                console.log("✅ Modelo cargado");
            } catch (error) {
                console.error("Error cargando el modelo:", error);
                document.getElementById("resultado").innerText = "❌ Error cargando el modelo. Ver consola.";
                document.getElementById("resultado").classList.add("error");
            }
        }

        // Función para predecir un nuevo perfil
        async function predecir() {
            const resultadoEl = document.getElementById("resultado");
            resultadoEl.classList.remove("error");

            if (!modelo) {
                resultadoEl.innerText = "⏳ Cargando modelo...";
                await cargarModelo();
                if (!modelo) return; // Si falló la carga
            }

            try {
                // Obtener valores de los inputs
                const inputs = [];
                for (let i = 0; i < 9; i++) {
                    const val = parseFloat(document.getElementById(`input-${i}`).value);
                    if (isNaN(val)) {
                        throw new Error(`Valor inválido en el campo ${i + 1}`);
                    }
                    inputs.push(val);
                }

                const nuevoPerfil = tf.tensor2d([inputs]);

                // Predecir
                const prediccion = modelo.predict(nuevoPerfil);
                const con = (await prediccion.data())[0];
                console.log(con)

                // Asegurar rango [1, 10]
                // const conAjustado = Math.min(Math.max(con, 1), 10);


                // Convertir a salida
                const conNewSalida = con / 62;
                console.log(conNewSalida)

                // Mostrar resultado
                resultadoEl.innerText = `Conclusión estimada: ${Math.trunc(conNewSalida)} (escala 1–10)`;
                resultadoEl.style.color = "#27ae60";

                // Liberar memoria
                nuevoPerfil.dispose();
                prediccion.dispose();
            } catch (error) {
                console.error(error);
                resultadoEl.innerText = "⚠️ " + error.message;
                resultadoEl.classList.add("error");
            }
        }

        cargarModelo();
    </script>
</body>

</html>