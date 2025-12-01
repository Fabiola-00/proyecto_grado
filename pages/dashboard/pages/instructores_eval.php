<?php
if (isset($_POST['guardar_evaluacion'])) {
    header('Content-Type: application/json');
    $db_file = __DIR__ . '/data/db_sbrab.sqlite';

    try {
        $pdo = new PDO('sqlite:' . $db_file);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Crear tabla si no existe (por seguridad)
        $sql_create = "CREATE TABLE IF NOT EXISTS evaluaciones (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            instructor_id INTEGER,
            ee REAL, me REAL, re REAL, te REAL, vs REAL,
            rs REAL, di REAL, td REAL, em REAL,
            conclusion REAL, recomendacion TEXT,
            fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql_create);

        $stmt = $pdo->prepare("INSERT INTO evaluaciones (instructor_id, ee, me, re, te, vs, rs, di, td, em, con, reco) 
                              VALUES (:instructor_id, :ee, :me, :re, :te, :vs, :rs, :di, :td, :em, :con, :reco)");

        $stmt->execute([
            ':instructor_id' => $_POST['instructor_id'],
            ':ee' => $_POST['ee'],
            ':me' => $_POST['me'],
            ':re' => $_POST['re'],
            ':te' => $_POST['te'],
            ':vs' => $_POST['vs'],
            ':rs' => $_POST['rs'],
            ':di' => $_POST['di'],
            ':td' => $_POST['td'],
            ':em' => $_POST['em'],
            ':con' => $_POST['con'],
            ':reco' => $_POST['reco']
        ]);

        // Actualizar estado del instructor a "Evaluado"
        $stmt_update = $pdo->prepare("UPDATE instructores SET estado = 'Evaluado' WHERE id = :id");
        $stmt_update->execute([':id' => $_POST['instructor_id']]);

        echo json_encode(['status' => 'success', 'message' => 'Evaluación guardada y estado actualizado correctamente.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error al guardar: ' . $e->getMessage()]);
    }
    exit;
}
?>
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
                <li><a href="asistencia.html" class="menu-item"><img src="../icons/asistencia.png" alt="Asistencia" class="icon" />
                        <span class="text">Asistencia</span></a></li>
                <li><a href="reportes.html" class="menu-item"><img src="../icons/reportes.png" alt="Reportes" class="icon" />
                        <span class="text">Reportes</span></a></li>
                <li><a href="cursos.html" class="menu-item"><img src="../icons/cursos.png" alt="Cursos" class="icon" />
                        <span class="text">Cursos</span></a></li>
                <li><a href="operativos.html" class="menu-item"><img src="../icons/operativos.png" alt="Operativos" class="icon" />
                        <span class="text">Operativos</span></a></li>

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
                    <p id="instructor-info">Sr. <?php echo htmlspecialchars($instructor_encontrado['grado'] . ' ' . $instructor_encontrado['apellido_paterno'] . ' ' . $instructor_encontrado['nombres']); ?> (<?php echo htmlspecialchars($instructor_encontrado['estado']); ?>)</p>
                    <input type="hidden" id="instructor_id" value="<?php echo $instructor_encontrado['id']; ?>">
                </div>
            <?php elseif ($mensaje_error): ?>
                <div style="text-align: center; margin-bottom: 10px; color: #e74c3c; font-weight: bold;">
                    <p><?php echo $mensaje_error; ?></p>
                </div>
            <?php endif; ?>

            <div id="form-container">
                <!-- Inputs de evaluación -->
                <div class="form-group"><label>EE (estabilidad emocional) 0-3 baja, 4-6 media, 7-10 alta</label><input min="0" max="10"
                        type="number" id="input-0" value="0">
                </div>
                <div class="form-group"><label>ME (manejo del estrés) 0-13 bajo, 14-26 moderado, 27-40 alto</label><input min="0" max="40" type="number"
                        id="input-1" value="0">
                </div>
                <div class="form-group"><label>RE (resiliencia) 0-15 baja, 26-50 media, 51-76 alta</label><input min="0" max="76" type="number"
                        id="input-2" value="0">
                </div>
                <div class="form-group"><label>TE (trabajo en equipo) 1 líder, 2 coordinador, 3 observador, 4 ejecutor</label><input min="1" max="4" type="number"
                        id="input-3" value="1">
                </div>
                <div class="form-group"><label>VS (vocación de servicio) 0 a 100</label><input min="0" max="100"
                        type="number" id="input-4" value="0"></div>
                <div class="form-group"><label>RS (responsabilidad) 1 bajo, 2 medio, 3 alto</label><input min="1" max="3" type="number"
                        id="input-5" value="1">
                </div>
                <div class="form-group"><label>DI (disciplina) 0-10 baja, 11-20 media, 21-30 alta</label><input min="0" max="30" type="number"
                        id="input-6" value="0">
                </div>
                <div class="form-group"><label>TD (toma de decisiones) 1 bajo, 2 medio, 3 alto</label><input min="1" max="3" type="number"
                        id="input-7" value="1">
                </div>
                <div class="form-group"><label>EM (empatía) 0-8 baja, 9-18 media, 19-28 alta</label><input min="0" max="28" type="number" id="input-8"
                        value="0">
                </div>
            </div>
            <br>

            <button onclick="predecir()">Evaluar</button>
            <p id="resultado"></p>
            <button type="button" onclick="guardarEvaluacion()" style="margin-right:10px;">Guardar Evaluación</button>
            <button type="button" onclick="resetForm()">Reiniciar</button>
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
        let currentConclusion = null;
        let currentRecomendacion = "";

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
                1: "Perfil crítico: Requiere intervención urgente.",
                2: "Perfil en desarrollo: Necesita entrenamiento básico.",
                3: "Perfil incipiente: Potencial mejorable con apoyo.",
                4: "Perfil funcional: Cumple expectativas básicas.",
                5: "Perfil competente: Desempeño confiable y estable.",
                6: "Perfil sólido: Liderazgo efectivo en entornos estables.",
                7: "Perfil destacado: Excelente desempeño en equipos dinámicos.",
                8: "Perfil excepcional: Modelo de liderazgo y compromiso.",
                9: "Perfil estratégico: Con capacidad probada para gestionar crisis, motivar equipos y tomar decisiones complejas. Ideal para roles de alta responsabilidad.",
                10: "Perfil de excelencia integral: Candidato ideal para puestos directivos o de impacto organizacional."
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
                console.log(conNewSalida)

                // Mostrar resultado
                let conNewSalidaFinal = Math.trunc(conNewSalida);
                const recomendacion = getRecomendacion(conNewSalidaFinal);

                // Guardar en variables globales
                currentConclusion = conNewSalidaFinal;
                currentRecomendacion = recomendacion;

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

        async function guardarEvaluacion() {
            const instructorId = document.getElementById('instructor_id')?.value;

            if (!instructorId) {
                alert("Por favor, busque y seleccione un instructor primero.");
                return;
            }
            if (currentConclusion === null) {
                alert("Por favor, realice la evaluación primero.");
                return;
            }

            const formData = new FormData();
            formData.append('guardar_evaluacion', '1');
            formData.append('instructor_id', instructorId);

            // Agregar valores de los inputs
            for (let i = 0; i < 9; i++) {
                const val = document.getElementById(`input-${i}`).value;
                const labels = ['ee', 'me', 're', 'te', 'vs', 'rs', 'di', 'td', 'em'];
                formData.append(labels[i], val);
            }

            formData.append('con', currentConclusion);
            formData.append('reco', currentRecomendacion);

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.status === 'success') {
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al procesar la solicitud.');
            }
        }

        function resetForm() {
            // Reiniciar inputs a sus valores por defecto
            const defaults = [0, 0, 0, 1, 0, 1, 0, 1, 0];
            for (let i = 0; i < 9; i++) {
                document.getElementById(`input-${i}`).value = defaults[i];
            }

            // Limpiar resultado y variables globales
            document.getElementById('resultado').innerHTML = "";
            currentConclusion = null;
            currentRecomendacion = "";

            // Limpiar info instructor y busqueda
            const instructorInfo = document.getElementById('instructor-info');
            if (instructorInfo) {
                instructorInfo.innerHTML = "";
                instructorInfo.style.display = "none"; // Ocultar el div vacio
            }
            // Assuming 'codigo_instructor' is the ID of the search input for the instructor
            const searchInput = document.getElementById('codigo_instructor');
            if (searchInput) {
                searchInput.value = "";
            }
        }

        cargarModelo();
    </script>
</body>

</html>