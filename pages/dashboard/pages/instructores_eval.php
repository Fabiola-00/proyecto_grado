<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SICOSE - Inst. Registro</title>
    <link rel="shortcut icon" href="../icons/escudo.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="css_pages/css_instructores1.css">
    <!-- Cargar TensorFlow.js -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.20.0/dist/tf.min.js"></script>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            color: #333;
        }

        h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 2rem;
        }

        .container {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .form-group:last-child {
            border-bottom: none;
        }

        label {
            font-weight: 500;
            color: #555;
            margin-right: 15px;
            flex: 1;
        }

        input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            width: 80px;
            text-align: center;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input:focus {
            border-color: #3498db;
            outline: none;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
            transition: background 0.3s, transform 0.1s;
        }

        button:hover {
            background-color: #2980b9;
        }

        button:active {
            transform: scale(0.98);
        }

        #resultado {
            margin-top: 20px;
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
            min-height: 1.5em;
        }

        .error {
            color: #e74c3c;
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

            <div id="form-container">
                <!-- Inputs generated here or hardcoded -->
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

            <button onclick="predecir()">Predecir</button>
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