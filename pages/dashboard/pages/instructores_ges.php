<?php
require_once 'data/db.php';

try {
  $stmt = $pdo->query("SELECT * FROM instructores ORDER BY nombres ASC");
  $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "<p class='error-message'>Error al cargar instructores: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SICOSE - Inst. Gestión</title>
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

    <?php
    require_once 'data/db.php';

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
      $accion = $_POST['accion'] ?? '';

      if ($accion === 'editar') {
        $id = intval($_POST['id']);
        $codigo = $_POST['codigo'];
        $grado = $_POST['grado'];
        $nombres = $_POST['nombres'];
        $apellido_paterno = $_POST['apellido_paterno'];
        $apellido_materno = $_POST['apellido_materno'];
        $cedula = $_POST['cedula'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $especialidad = $_POST['especialidad'];
        $estado = $_POST['estado'];

        try {
          $stmt = $pdo->prepare("
                UPDATE instructores SET
                    codigo = ?,
                    grado = ?,
                    nombres = ?,
                    apellido_paterno = ?,
                    apellido_materno = ?,
                    cedula = ?,
                    fecha_nacimiento = ?,
                    especialidad = ?,
                    estado = ?
                WHERE id = ?
            ");
          $stmt->execute([
            $codigo,
            $grado,
            $nombres,
            $apellido_paterno,
            $apellido_materno,
            $cedula,
            $fecha_nacimiento,
            $especialidad,
            $estado,
            $id
          ]);

          // Redirigir con mensaje de éxito
          header("Location: instructores_ges.php?success=Datos actualizados correctamente");
          exit();
        } catch (PDOException $e) {
          header("Location: instructores_ges.php?error=" . urlencode($e->getMessage()));
          exit();
        }
      } elseif ($accion === 'eliminar') {
        $id = intval($_POST['id']);

        try {
          $stmt = $pdo->prepare("DELETE FROM instructores WHERE id = ?");
          $stmt->execute([$id]);

          header("Location: instructores_ges.php?success=Instructor eliminado correctamente");
          exit();
        } catch (PDOException $e) {
          header("Location: instructores_ges.php?error=" . urlencode($e->getMessage()));
          exit();
        }
      }
    }
    ?>

    <!--******** Contenedor del formulario **********-->

    <div class="form-container">
      <h2>Lista de Instructores</h2>

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
            <label for="busqueda_codigo">Código:</label>
            <input type="text" id="busqueda_codigo" name="codigo" value="<?= htmlspecialchars($_GET['codigo'] ?? '') ?>" placeholder="ABC123456">
          </div>
          <div class="filter-item">
            <label for="busqueda_nombre">Nombre:</label>
            <input type="text" id="busqueda_nombre" name="nombre" value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>" placeholder="Buscar por nombre">
          </div>
          <div class="filter-item">
            <label for="busqueda_especialidad">Especialidad:</label>
            <select id="busqueda_especialidad" name="especialidad">
              <option value="">Todas</option>
              <option value="Primeros Auxilios" <?= ($_GET['especialidad'] ?? '') == 'Primeros Auxilios' ? 'selected' : '' ?>>Primeros Auxilios</option>
              <option value="Rescate en Montaña" <?= ($_GET['especialidad'] ?? '') == 'Rescate en Montaña' ? 'selected' : '' ?>>Rescate en Montaña</option>
              <option value="Rescate en Selva" <?= ($_GET['especialidad'] ?? '') == 'Rescate en Selva' ? 'selected' : '' ?>>Rescate en Selva</option>
              <option value="Lucha contra incendios" <?= ($_GET['especialidad'] ?? '') == 'Lucha contra incendios' ? 'selected' : '' ?>>Lucha contra incendios</option>
              <option value="Natación" <?= ($_GET['especialidad'] ?? '') == 'Natación' ? 'selected' : '' ?>>Natación</option>
              <option value="K-9" <?= ($_GET['especialidad'] ?? '') == 'K-9' ? 'selected' : '' ?>>K-9</option>
            </select>
          </div>
          <div class="filter-item">
            <label for="busqueda_estado">Estado:</label>
            <select id="busqueda_estado" name="estado">
              <option value="">Todos</option>
              <option value="Evaluado" <?= ($_GET['estado'] ?? '') == 'Evaluado' ? 'selected' : '' ?>>Evaluado</option>
              <option value="No evaluado" <?= ($_GET['estado'] ?? '') == 'No evaluado' ? 'selected' : '' ?>>No evaluado</option>
            </select>
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
            <th>Código</th>
            <th>Nombres</th>
            <th>Grado</th>
            <th>Especialidad</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php
          require_once 'data/db.php';

          $where = [];
          $params = [];

          // Filtro por código
          if (!empty($_GET['codigo'])) {
            $where[] = "codigo LIKE ?";
            $params[] = "%{$_GET['codigo']}%";
          }

          // Filtro por nombre
          if (!empty($_GET['nombre'])) {
            $nombre = trim($_GET['nombre']);
            $where[] = "(nombres LIKE ? OR apellido_paterno LIKE ? OR apellido_materno LIKE ?)";
            $params[] = "%$nombre%";
            $params[] = "%$nombre%";
            $params[] = "%$nombre%";
          }

          // Filtro por especialidad
          if (!empty($_GET['especialidad'])) {
            $where[] = "especialidad = ?";
            $params[] = $_GET['especialidad'];
          }

          // Filtro por estado
          if (!empty($_GET['estado'])) {
            $where[] = "estado = ?";
            $params[] = $_GET['estado'];
          }

          try {
            $sql = "SELECT * FROM instructores";
            if (!empty($where)) {
              $sql .= " WHERE " . implode(" AND ", $where);
            }
            $sql .= " ORDER BY nombres ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($instructores as $instructor) {
              echo "<tr>";
              echo "<td>" . htmlspecialchars($instructor['codigo']) . "</td>";
              echo "<td>" . htmlspecialchars($instructor['nombres'] . " " . $instructor['apellido_paterno'] . " " . $instructor['apellido_materno']) . "</td>";
              echo "<td>" . htmlspecialchars($instructor['grado']) . "</td>";
              echo "<td>" . htmlspecialchars($instructor['especialidad']) . "</td>";
              echo "<td>" . ucfirst(htmlspecialchars($instructor['estado'])) . "</td>";
              echo "<td>
                      <button onclick=\"abrirModal('editar', {$instructor['id']})\" class='btn-edit'><i class='fas fa-edit'></i></button>
                      <button onclick=\"abrirModal('eliminar', {$instructor['id']})\" class='btn-delete'><i class='fas fa-trash'></i></button>
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
          <h3>Editar Instructor</h3>
          <input type="hidden" id="instructor_id" name="id">
          <label for="codigo_edit">Código:</label>
          <input type="text" id="codigo_edit" name="codigo" required pattern="[A-Za-z]{3}\d{6}" title="Ejemplo: ABC123456">

          <label for="grado_edit">Grado:</label>
          <select id="grado_edit" name="grado" required>
            <option value="">Seleccione...</option>
            <option value="Inst. Inicial">Inst. Inicial</option>
            <option value="Inst. Segundo">Inst. Segundo</option>
            <option value="Inst. Primero">Inst. Primero</option>
            <option value="Inst. Principal">Inst. Principal</option>
            <option value="Inst. Mayor">Inst. Mayor</option>
          </select>

          <label for="nombres_edit">Nombres:</label>
          <input type="text" id="nombres_edit" name="nombres" required>

          <label for="apellido_paterno_edit">Apellido Paterno:</label>
          <input type="text" id="apellido_paterno_edit" name="apellido_paterno" required>

          <label for="apellido_materno_edit">Apellido Materno:</label>
          <input type="text" id="apellido_materno_edit" name="apellido_materno" required>

          <label for="cedula_edit">Cédula:</label>
          <input type="text" id="cedula_edit" name="cedula" required pattern="\d{7}" title="Ejemplo: 1234567">

          <label for="fecha_nacimiento_edit">Fecha de Nacimiento:</label>
          <input type="date" id="fecha_nacimiento_edit" name="fecha_nacimiento" required>

          <label for="especialidad_edit">Especialidad:</label>
          <select id="especialidad_edit" name="especialidad" required>
            <option value="">Seleccione...</option>
            <option value="Primeros Auxilios">Primeros Auxilios</option>
            <option value="Rescate en Montaña">Rescate en Montaña</option>
            <option value="Rescate en Selva">Rescate en Selva</option>
            <option value="Lucha contra incendios">Lucha contra incendios</option>
            <option value="Natación">Natación</option>
            <option value="K-9">K-9</option>
          </select>

          <label for="estado_edit">Estado:</label>
          <select id="estado_edit" name="estado" required>
            <option value="">Seleccione...</option>
            <option value="Evaluado">Evaluado</option>
            <option value="No evaluado">No evaluado</option>
          </select>

          <button type="submit" name="accion" value="editar">Guardar cambios</button>
          <button type="button" onclick="cerrarModal()">Cancelar</button>
        </form>
      </dialog>

      <!-- Modal de eliminación -->
      <dialog id="modalEliminar" class="modal">
        <form method="POST" class="modal-content">
          <h3>¿Está seguro de eliminar este instructor?</h3>
          <input type="hidden" id="instructor_id_eliminar" name="id">
          <button type="submit" name="accion" value="eliminar">Sí, eliminar</button>
          <button type="button" onclick="cerrarModal()">No, cancelar</button>
        </form>
      </dialog>
    </div>

    <script>
      function abrirModal(tipo, id) {
        if (tipo === 'editar') {
          fetch(`obtener_instructor.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
              document.getElementById('instructor_id').value = data.id;
              document.getElementById('codigo_edit').value = data.codigo;
              document.getElementById('grado_edit').value = data.grado;
              document.getElementById('nombres_edit').value = data.nombres;
              document.getElementById('apellido_paterno_edit').value = data.apellido_paterno;
              document.getElementById('apellido_materno_edit').value = data.apellido_materno;
              document.getElementById('cedula_edit').value = data.cedula;
              document.getElementById('fecha_nacimiento_edit').value = data.fecha_nacimiento;
              document.getElementById('especialidad_edit').value = data.especialidad;
              document.getElementById('estado_edit').value = data.estado;

              document.getElementById('modalEditar').showModal();
            });
        } else if (tipo === 'eliminar') {
          document.getElementById('instructor_id_eliminar').value = id;
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