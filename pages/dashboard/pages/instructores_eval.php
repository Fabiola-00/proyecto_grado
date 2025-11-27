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
                    <input autocomplete="off" type="text" name="codigo_instructor" id="codigo_instructor" required style="width: 150px;">
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
        // Función para obtener la recomendación según el valor
        function getRecomendacion(valor) {
            const recomendaciones = {
                1: "Perfil crítico: El perfil presenta limitaciones significativas en estabilidad emocional, manejo del estrés y resiliencia, lo que compromete su capacidad para actuar con eficacia en situaciones de emergencia. Su rol pasivo y baja responsabilidad indican necesidad de intervención inmediata mediante capacitación psicológica, entrenamiento en gestión de crisis y acompañamiento estructurado. Sin intervención urgente, no se recomienda su participación en misiones activas.",
                2: "Perfil en desarrollo: Muestra potencial, pero requiere fortalecimiento en estabilidad emocional, resiliencia y toma de decisiones bajo presión. Presenta una vocación de servicio moderada y un rol observador, lo que sugiere necesidad de formación práctica en simulacros de rescate, trabajo en equipo y manejo de estrés. Con apoyo sistemático, puede alcanzar niveles funcionales.",
                3: "Perfil incipiente: Demuestra una base funcional en habilidades clave, con estabilidad emocional media y capacidad básica para colaborar en equipos. Aunque posee cierta disciplina y empatía, su rendimiento aún es inconsistente en escenarios críticos. Se recomienda entrenamiento intensivo en liderazgo situacional, gestión de riesgos y trabajo en condiciones adversas para potenciar su desempeño.",
                4: "Perfil funcional: Cumple con los requisitos mínimos para participar en operaciones de rescate. Muestra estabilidad emocional aceptable, capacidad de cooperación y responsabilidad media. Es apto para tareas asignadas dentro de un equipo, aunque requiere supervisión constante en situaciones complejas. Ideal para roles de apoyo en operaciones estructuradas.",
                5: "Perfil competente: Desempeña de manera confiable en entornos de rescate, con estabilidad emocional sólida, buena resiliencia y compromiso con la misión. Capacidad demostrada para tomar decisiones razonables bajo presión y colaborar efectivamente en equipo. Apto para funciones clave en misiones, con potencial para asumir mayores responsabilidades con experiencia adicional.",
                6: "Perfil sólido: Actúa como líder efectivo en contextos estables, mostrando alta responsabilidad, disciplina y capacidad de coordinación. Su perfil emocional equilibrado le permite mantener el control en situaciones tensas y guiar al equipo con firmeza. Recomendado para roles de liderazgo en operaciones de rescate planificadas y bajo supervisión directa.",
                7: "Perfil destacado: Destaca por su liderazgo proactivo, toma de decisiones ágil y alta empatía en contextos de crisis. Capaz de mantener el rendimiento en condiciones extremas, motivar al equipo y adaptarse dinámicamente a cambios imprevistos. Ideal para liderar unidades de rescate en escenarios complejos o multilaterales.",
                8: "Perfil excepcional: Representa un modelo de desempeño en el ámbito de rescate: integridad, compromiso absoluto y capacidad de inspirar confianza en momentos críticos. Su dominio del estrés, resiliencia y liderazgo son ejemplares. Recomendado para cargos estratégicos, entrenamiento de nuevos miembros y representación en operaciones de alto impacto.",
                9: "Perfil estratégico: Posee una visión sistémica del rescate, capaz de anticipar riesgos, gestionar recursos de forma óptima y liderar equipos multidisciplinarios en crisis complejas. Su capacidad para tomar decisiones estratégicas bajo presión, combinar eficiencia con humanidad y mantener la cohesión del equipo lo convierte en un recurso clave para la planificación y ejecución de operaciones de gran escala.",
                10: "Perfil de excelencia integral: Candidato ideal para puestos de dirección y transformación en organizaciones de rescate. Combina liderazgo inspirador, innovación en procesos de intervención, resiliencia extrema y un profundo compromiso ético con la vida humana. Su influencia trasciende el campo operativo, impulsando la cultura de seguridad, mejora continua y preparación ante emergencias a nivel institucional."
            };
            return recomendaciones[valor] || "Valor fuera de rango.";
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

                // Convertir a salida
                const conNewSalida = con / 62;

                // Mostrar resultado
                let conNewSalidaFinal = Math.trunc(conNewSalida);
                console.log(conNewSalidaFinal)
                console.log(parseInt(conNewSalidaFinal))
                const recomendacion = getRecomendacion(conNewSalidaFinal);
                resultadoEl.innerHTML = `Conclusión estimada: ${conNewSalidaFinal} (escala 1–10)<br><br><strong>Recomendación:</strong> ${recomendacion}`;
                resultadoEl.style.color = "#120c6bff";

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