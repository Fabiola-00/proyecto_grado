<?php
require_once 'data/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM cursos ORDER BY fecha_inicio DESC");
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p class='error-message'>Error al cargar cursos: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SICOSE - Gestión de Cursos</title>
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
                <li><a href="asistencia.html" class="menu-item"><img src="../icons/asistencia.png" alt="Asistencia" class="icon" />
                        <span class="text">Asistencia</span></a></li>
                <li><a href="reportes.html" class="menu-item"><img src="../icons/reportes.png" alt="Reportes" class="icon" />
                        <span class="text">Reportes</span></a></li>
                <li><a href="cursos.html" class="menu-item"><img src="../icons/cursos.png" alt="Cursos" class="icon" />
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

        <?php
        require_once 'data/db.php';

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $accion = $_POST['accion'] ?? '';

            if ($accion === 'editar') {
                $id = intval($_POST['id']);
                $tipo = $_POST['tipo'];
                $entidad = $_POST['entidad'];
                $nombre = $_POST['nombre'];
                $fecha_inicio = $_POST['fecha_inicio'];
                $fecha_fin = $_POST['fecha_fin'];
                $observaciones = $_POST['observaciones'] ?? '';

                try {
                    $stmt = $pdo->prepare("
                UPDATE cursos SET
                    tipo = ?,
                    entidad = ?,
                    nombre = ?,
                    fecha_inicio = ?,
                    fecha_fin = ?,
                    observaciones = ?
                WHERE id = ?
            ");
                    $stmt->execute([
                        $tipo,
                        $entidad,
                        $nombre,
                        $fecha_inicio,
                        $fecha_fin,
                        $observaciones,
                        $id
                    ]);

                    // Redirigir con mensaje de éxito
                    header("Location: cursos_ges.php?success=Datos actualizados correctamente");
                    exit();
                } catch (PDOException $e) {
                    header("Location: cursos_ges.php?error=" . urlencode($e->getMessage()));
                    exit();
                }
            } elseif ($accion === 'eliminar') {
                $id = intval($_POST['id']);

                try {
                    $stmt = $pdo->prepare("DELETE FROM cursos WHERE id = ?");
                    $stmt->execute([$id]);

                    header("Location: cursos_ges.php?success=Curso eliminado correctamente");
                    exit();
                } catch (PDOException $e) {
                    header("Location: cursos_ges.php?error=" . urlencode($e->getMessage()));
                    exit();
                }
            }
        }
        ?>

        <!--******** Contenedor del formulario **********-->

        <div class="form-container">
            <h2>Lista de Cursos</h2>

            <!-- Mensaje de éxito o error -->
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <!-- Formulario de filtros en una sola fila -->
            <form method="GET" action="<?= $_SERVER['PHP_SELF'] ?>" class="search-form">
                <div class="filter-row">
                    <div class="filter-item">
                        <label for="busqueda_nombre">Nombre:</label>
                        <input type="text" id="busqueda_nombre" name="nombre" value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>" placeholder="Buscar por nombre">
                    </div>
                    <div class="filter-item">
                        <label for="busqueda_tipo">Tipo:</label>
                        <select id="busqueda_tipo" name="tipo">
                            <option value="">Todos</option>
                            <option value="Capacitación" <?= ($_GET['tipo'] ?? '') == 'Capacitación' ? 'selected' : '' ?>>Capacitación</option>
                            <option value="Especialización" <?= ($_GET['tipo'] ?? '') == 'Especialización' ? 'selected' : '' ?>>Especialización</option>
                            <option value="Actualización" <?= ($_GET['tipo'] ?? '') == 'Actualización' ? 'selected' : '' ?>>Actualización</option>
                            <option value="Taller" <?= ($_GET['tipo'] ?? '') == 'Taller' ? 'selected' : '' ?>>Taller</option>
                            <option value="Seminario" <?= ($_GET['tipo'] ?? '') == 'Seminario' ? 'selected' : '' ?>>Seminario</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="busqueda_entidad">Entidad:</label>
                        <input type="text" id="busqueda_entidad" name="entidad" value="<?= htmlspecialchars($_GET['entidad'] ?? '') ?>" placeholder="Buscar por entidad">
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
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Entidad</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require_once 'data/db.php';

                    $where = [];
                    $params = [];

                    // Filtro por nombre
                    if (!empty($_GET['nombre'])) {
                        $nombre = trim($_GET['nombre']);
                        $where[] = "nombre LIKE ?";
                        $params[] = "%$nombre%";
                    }

                    // Filtro por tipo
                    if (!empty($_GET['tipo'])) {
                        $where[] = "tipo = ?";
                        $params[] = $_GET['tipo'];
                    }

                    // Filtro por entidad
                    if (!empty($_GET['entidad'])) {
                        $entidad = trim($_GET['entidad']);
                        $where[] = "entidad LIKE ?";
                        $params[] = "%$entidad%";
                    }

                    try {
                        $sql = "SELECT * FROM cursos";
                        if (!empty($where)) {
                            $sql .= " WHERE " . implode(" AND ", $where);
                        }
                        $sql .= " ORDER BY fecha_inicio DESC";

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($cursos as $curso) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($curso['nombre']) . "</td>";
                            echo "<td>" . htmlspecialchars($curso['tipo']) . "</td>";
                            echo "<td>" . htmlspecialchars($curso['entidad']) . "</td>";
                            echo "<td>" . date('d/m/Y', strtotime($curso['fecha_inicio'])) . "</td>";
                            echo "<td>" . date('d/m/Y', strtotime($curso['fecha_fin'])) . "</td>";
                            echo "<td>
                      <button onclick=\"abrirModal('editar', {$curso['id']})\" class='btn-edit'><i class='fas fa-edit'></i></button>
                      <button onclick=\"abrirModal('eliminar', {$curso['id']})\" class='btn-delete'><i class='fas fa-trash'></i></button>
                    </td>";
                            echo "</tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='6' class='error-message'>Error al cargar datos: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Modal de edición -->
            <dialog id="modalEditar" class="modal">
                <form method="POST" class="modal-content">
                    <h3>Editar Curso</h3>
                    <input type="hidden" id="curso_id" name="id">

                    <label for="tipo_edit">Tipo:</label>
                    <select id="tipo_edit" name="tipo" required>
                        <option value="">Seleccione...</option>
                        <option value="Capacitación">Capacitación</option>
                        <option value="Especialización">Especialización</option>
                        <option value="Actualización">Actualización</option>
                        <option value="Taller">Taller</option>
                        <option value="Seminario">Seminario</option>
                    </select>

                    <label for="entidad_edit">Entidad:</label>
                    <input type="text" id="entidad_edit" name="entidad" required>

                    <label for="nombre_edit">Nombre del Curso:</label>
                    <input type="text" id="nombre_edit" name="nombre" required>

                    <label for="fecha_inicio_edit">Fecha de Inicio:</label>
                    <input type="date" id="fecha_inicio_edit" name="fecha_inicio" required>

                    <label for="fecha_fin_edit">Fecha de Fin:</label>
                    <input type="date" id="fecha_fin_edit" name="fecha_fin" required>

                    <label for="observaciones_edit">Observaciones:</label>
                    <textarea id="observaciones_edit" name="observaciones" rows="4"></textarea>

                    <button type="submit" name="accion" value="editar">Guardar cambios</button>
                    <button type="button" onclick="cerrarModal()">Cancelar</button>
                </form>
            </dialog>

            <!-- Modal de eliminación -->
            <dialog id="modalEliminar" class="modal">
                <form method="POST" class="modal-content">
                    <h3>¿Está seguro de eliminar este curso?</h3>
                    <input type="hidden" id="curso_id_eliminar" name="id">
                    <button type="submit" name="accion" value="eliminar">Sí, eliminar</button>
                    <button type="button" onclick="cerrarModal()">No, cancelar</button>
                </form>
            </dialog>
        </div>

        <script>
            function abrirModal(tipo, id) {
                if (tipo === 'editar') {
                    fetch(`obtener_curso.php?id=${id}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('curso_id').value = data.id;
                            document.getElementById('tipo_edit').value = data.tipo;
                            document.getElementById('entidad_edit').value = data.entidad;
                            document.getElementById('nombre_edit').value = data.nombre;
                            document.getElementById('fecha_inicio_edit').value = data.fecha_inicio;
                            document.getElementById('fecha_fin_edit').value = data.fecha_fin;
                            document.getElementById('observaciones_edit').value = data.observaciones || '';

                            document.getElementById('modalEditar').showModal();
                        });
                } else if (tipo === 'eliminar') {
                    document.getElementById('curso_id_eliminar').value = id;
                    document.getElementById('modalEliminar').showModal();
                }
            }

            function cerrarModal() {
                document.getElementById('modalEditar').close();
                document.getElementById('modalEliminar').close();
            }
        </script>

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