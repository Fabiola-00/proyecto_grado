<?php
require_once 'data/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'editar') {
        $id = intval($_POST['id']);
        $tipo = $_POST['tipo'];
        $entidad_responsable = $_POST['entidad_responsable'];
        $estado = $_POST['estado'];
        $departamento = $_POST['departamento'];
        $zona = $_POST['zona'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_final = $_POST['fecha_final'];

        try {
            $stmt = $pdo->prepare("
                UPDATE operativos SET
                    tipo = ?,
                    entidad_responsable = ?,
                    estado = ?,
                    departamento = ?,
                    zona = ?,
                    fecha_inicio = ?,
                    fecha_final = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $tipo,
                $entidad_responsable,
                $estado,
                $departamento,
                $zona,
                $fecha_inicio,
                $fecha_final,
                $id
            ]);

            // Redirigir con mensaje de éxito
            header("Location: operativos_ges.php?success=Datos actualizados correctamente");
            exit();
        } catch (PDOException $e) {
            header("Location: operativos_ges.php?error=" . urlencode($e->getMessage()));
            exit();
        }
    } elseif ($accion === 'eliminar') {
        $id = intval($_POST['id']);

        try {
            $stmt = $pdo->prepare("DELETE FROM operativos WHERE id = ?");
            $stmt->execute([$id]);

            header("Location: operativos_ges.php?success=Operativo eliminado correctamente");
            exit();
        } catch (PDOException $e) {
            header("Location: operativos_ges.php?error=" . urlencode($e->getMessage()));
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SICOSE - Gestión de Operativos</title>
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

        .btn-generar-pdf {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            margin-left: 10px;
            display: inline-block;
        }

        .btn-generar-pdf:hover {
            background-color: #218838;
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
                <li><a href="asistencia.html" class="menu-item"><img src="../icons/asistencia.png" alt="Asistencia" class="icon" />
                        <span class="text">Asistencia</span></a></li>
                <li><a href="reportes.html" class="menu-item"><img src="../icons/reportes.png" alt="Reportes" class="icon" />
                        <span class="text">Reportes</span></a></li>
                <li><a href="cursos.html" class="menu-item"><img src="../icons/cursos.png" alt="Cursos" class="icon" />
                        <span class="text">Cursos</span></a></li>
                <li><a href="operativos.html" class="menu-item"><img src="../icons/operativos.png" alt="Operativos" class="icon" />
                        <span class="text">Operativos</span></a></li>
                <li><a href="../logout.php" class="menu-item"><img src="../icons/salir.png" alt="Salir" class="icon" />
                        <span class="text">Salir</span></a></li>
            </ul>
        </nav>
    </aside>

    <!--  *******************************************************************************************    -->
    <!-- Contenido Principal -->
    <main id="content" class="content">

        <!--******** Contenedor del formulario **********-->

        <div class="form-container">
            <h2>Lista de Operativos</h2>

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
                        <label for="busqueda_tipo">Tipo:</label>
                        <select id="busqueda_tipo" name="tipo">
                            <option value="">Todos</option>
                            <option value="Preventivo" <?= ($_GET['tipo'] ?? '') == 'Preventivo' ? 'selected' : '' ?>>Preventivo</option>
                            <option value="Reactivo" <?= ($_GET['tipo'] ?? '') == 'Reactivo' ? 'selected' : '' ?>>Reactivo</option>
                            <option value="Especial" <?= ($_GET['tipo'] ?? '') == 'Especial' ? 'selected' : '' ?>>Especial</option>
                            <option value="Emergencia" <?= ($_GET['tipo'] ?? '') == 'Emergencia' ? 'selected' : '' ?>>Emergencia</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="busqueda_estado">Estado:</label>
                        <select id="busqueda_estado" name="estado">
                            <option value="">Todos</option>
                            <option value="Planificado" <?= ($_GET['estado'] ?? '') == 'Planificado' ? 'selected' : '' ?>>Planificado</option>
                            <option value="En ejecución" <?= ($_GET['estado'] ?? '') == 'En ejecución' ? 'selected' : '' ?>>En ejecución</option>
                            <option value="Finalizado" <?= ($_GET['estado'] ?? '') == 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
                            <option value="Suspendido" <?= ($_GET['estado'] ?? '') == 'Suspendido' ? 'selected' : '' ?>>Suspendido</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="busqueda_departamento">Departamento:</label>
                        <input type="text" id="busqueda_departamento" name="departamento" value="<?= htmlspecialchars($_GET['departamento'] ?? '') ?>" placeholder="Buscar por departamento">
                    </div>
                    <div class="filter-actions">
                        <button type="submit">Filtrar...</button>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>">Limpiar filtros</a>
                        <?php
                        $filtros = [];
                        if (!empty($_GET['tipo'])) $filtros[] = 'tipo=' . urlencode($_GET['tipo']);
                        if (!empty($_GET['estado'])) $filtros[] = 'estado=' . urlencode($_GET['estado']);
                        if (!empty($_GET['departamento'])) $filtros[] = 'departamento=' . urlencode($_GET['departamento']);
                        $queryString = !empty($filtros) ? '?' . implode('&', $filtros) : '';
                        ?>
                        <a href="generar_reporte_operativos.php<?= $queryString ?>" target="_blank" class="btn-generar-pdf" style="text-decoration: none;">Generar PDF</a>
                    </div>
                </div>
            </form>

            <!-- Tabla ocupando el ancho total -->
            <table class="table-list full-width-table">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Entidad Responsable</th>
                        <th>Estado</th>
                        <th>Departamento</th>
                        <th>Zona</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Final</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $where = [];
                    $params = [];

                    // Filtro por tipo
                    if (!empty($_GET['tipo'])) {
                        $where[] = "tipo = ?";
                        $params[] = $_GET['tipo'];
                    }

                    // Filtro por estado
                    if (!empty($_GET['estado'])) {
                        $where[] = "estado = ?";
                        $params[] = $_GET['estado'];
                    }

                    // Filtro por departamento
                    if (!empty($_GET['departamento'])) {
                        $departamento = trim($_GET['departamento']);
                        $where[] = "departamento LIKE ?";
                        $params[] = "%$departamento%";
                    }

                    try {
                        $sql = "SELECT * FROM operativos";
                        if (!empty($where)) {
                            $sql .= " WHERE " . implode(" AND ", $where);
                        }
                        $sql .= " ORDER BY fecha_inicio DESC";

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        $operativos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($operativos as $operativo) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($operativo['tipo']) . "</td>";
                            echo "<td>" . htmlspecialchars($operativo['entidad_responsable']) . "</td>";
                            echo "<td>" . htmlspecialchars($operativo['estado']) . "</td>";
                            echo "<td>" . htmlspecialchars($operativo['departamento']) . "</td>";
                            echo "<td>" . htmlspecialchars($operativo['zona']) . "</td>";
                            echo "<td>" . date('d/m/Y', strtotime($operativo['fecha_inicio'])) . "</td>";
                            echo "<td>" . date('d/m/Y', strtotime($operativo['fecha_final'])) . "</td>";
                            echo "<td>
                      <button onclick=\"abrirModal('editar', {$operativo['id']})\" class='btn-edit'><i class='fas fa-edit'></i></button>
                      <button onclick=\"abrirModal('eliminar', {$operativo['id']})\" class='btn-delete'><i class='fas fa-trash'></i></button>
                    </td>";
                            echo "</tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='8' class='error-message'>Error al cargar datos: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Modal de edición -->
            <dialog id="modalEditar" class="modal">
                <form method="POST" class="modal-content">
                    <h3>Editar Operativo</h3>
                    <input type="hidden" id="operativo_id" name="id">

                    <label for="tipo_edit">Tipo:</label>
                    <select id="tipo_edit" name="tipo" required>
                        <option value="">Seleccione...</option>
                        <option value="Preventivo">Preventivo</option>
                        <option value="Reactivo">Reactivo</option>
                        <option value="Especial">Especial</option>
                        <option value="Emergencia">Emergencia</option>
                    </select>

                    <label for="entidad_responsable_edit">Entidad Responsable:</label>
                    <input type="text" id="entidad_responsable_edit" name="entidad_responsable" required>

                    <label for="estado_edit">Estado:</label>
                    <select id="estado_edit" name="estado" required>
                        <option value="Planificado">Planificado</option>
                        <option value="En ejecución">En ejecución</option>
                        <option value="Finalizado">Finalizado</option>
                        <option value="Suspendido">Suspendido</option>
                    </select>

                    <label for="departamento_edit">Departamento:</label>
                    <input type="text" id="departamento_edit" name="departamento" required>

                    <label for="zona_edit">Zona:</label>
                    <input type="text" id="zona_edit" name="zona" required>

                    <label for="fecha_inicio_edit">Fecha de Inicio:</label>
                    <input type="date" id="fecha_inicio_edit" name="fecha_inicio" required>

                    <label for="fecha_final_edit">Fecha Final:</label>
                    <input type="date" id="fecha_final_edit" name="fecha_final" required>

                    <button type="submit" name="accion" value="editar">Guardar cambios</button>
                    <button type="button" onclick="cerrarModal()">Cancelar</button>
                </form>
            </dialog>

            <!-- Modal de eliminación -->
            <dialog id="modalEliminar" class="modal">
                <form method="POST" class="modal-content">
                    <h3>¿Está seguro de eliminar este operativo?</h3>
                    <input type="hidden" id="operativo_id_eliminar" name="id">
                    <button type="submit" name="accion" value="eliminar">Sí, eliminar</button>
                    <button type="button" onclick="cerrarModal()">No, cancelar</button>
                </form>
            </dialog>
        </div>

        <script>
            function abrirModal(tipo, id) {
                if (tipo === 'editar') {
                    fetch(`obtener_operativo.php?id=${id}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('operativo_id').value = data.id;
                            document.getElementById('tipo_edit').value = data.tipo;
                            document.getElementById('entidad_responsable_edit').value = data.entidad_responsable;
                            document.getElementById('estado_edit').value = data.estado;
                            document.getElementById('departamento_edit').value = data.departamento;
                            document.getElementById('zona_edit').value = data.zona;
                            document.getElementById('fecha_inicio_edit').value = data.fecha_inicio;
                            document.getElementById('fecha_final_edit').value = data.fecha_final;

                            document.getElementById('modalEditar').showModal();
                        });
                } else if (tipo === 'eliminar') {
                    document.getElementById('operativo_id_eliminar').value = id;
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