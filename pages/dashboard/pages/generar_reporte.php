<?php
require_once 'data/db.php';

if (!isset($_GET['id'])) {
    die("ID de instructor no especificado.");
}

$id = intval($_GET['id']);

try {
    // Obtener datos del instructor
    $stmt = $pdo->prepare("SELECT * FROM instructores WHERE id = ?");
    $stmt->execute([$id]);
    $instructor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$instructor) {
        die("Instructor no encontrado.");
    }

    // Obtener evaluaciones del instructor
    // La tabla evaluaciones usa instructor_id para relacionar con instructores
    $evaluaciones = [];
    try {
        // Buscar evaluaciones por instructor_id
        $stmtEval = $pdo->prepare("SELECT * FROM evaluaciones WHERE instructor_id = ?");
        $stmtEval->execute([$id]);
        $evaluaciones = $stmtEval->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Si falla (por ejemplo, tabla no existe), simplemente no mostramos evaluaciones
        $errorEvaluaciones = $e->getMessage();
    }
} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Instructor - <?= htmlspecialchars($instructor['nombres'] . ' ' . $instructor['apellido_paterno']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .logo {
            max-width: 100px;
            margin-bottom: 10px;
        }

        h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }

        h2 {
            font-size: 18px;
            color: #555;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-top: 25px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-item {
            margin-bottom: 5px;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        @media print {
            body {
                padding: 0;
                font-size: 12pt;
            }

            .no-print {
                display: none;
            }

            a {
                text-decoration: none;
                color: #333;
            }
        }

        .btn-print {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            cursor: pointer;
            border: none;
            font-size: 16px;
        }

        .btn-print:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>

    <div class="no-print" style="text-align: right;">
        <button onclick="window.print()" class="btn-print">Imprimir / Guardar como PDF</button>
    </div>

    <div class="header">
        <img src="../images/sbrab_escudo.png" alt="Logo SBRAB" class="logo">
        <h1>Reporte de Instructor</h1>
        <p>Sistema de Control y Seguimiento (SICOSE)</p>
    </div>

    <div class="section">
        <h2>Datos Personales</h2>
        <div class="info-grid">
            <div class="info-item"><span class="label">Código:</span> <?= htmlspecialchars($instructor['codigo']) ?></div>
            <div class="info-item"><span class="label">Estado:</span> <?= ucfirst(htmlspecialchars($instructor['estado'])) ?></div>
            <div class="info-item"><span class="label">Grado:</span> <?= htmlspecialchars($instructor['grado']) ?></div>
            <div class="info-item"><span class="label">Cédula:</span> <?= htmlspecialchars($instructor['cedula']) ?></div>
            <div class="info-item"><span class="label">Nombres:</span> <?= htmlspecialchars($instructor['nombres']) ?></div>
            <div class="info-item"><span class="label">Apellidos:</span> <?= htmlspecialchars($instructor['apellido_paterno'] . ' ' . $instructor['apellido_materno']) ?></div>
            <div class="info-item"><span class="label">Fecha Nacimiento:</span> <?= htmlspecialchars($instructor['fecha_nacimiento']) ?></div>
            <div class="info-item"><span class="label">Especialidad:</span> <?= htmlspecialchars($instructor['especialidad']) ?></div>
        </div>
    </div>

    <div class="section">
        <h2>Historial de Evaluaciones</h2>
        <?php
        function getEstabilidadEmocionalDescription($value)
        {
            if ($value === null || $value === '') return 'N/A';
            $value = (int)$value;
            if ($value >= 0 && $value <= 3) return 'Baja';
            if ($value >= 4 && $value <= 6) return 'Media';
            if ($value >= 7 && $value <= 10) return 'Alta';
            return 'Desconocido';
        }

        function getManejoEstresDescription($value)
        {
            if ($value === null || $value === '') return 'N/A';
            $value = (int)$value;
            if ($value >= 0 && $value <= 13) return 'Bajo';
            if ($value >= 14 && $value <= 26) return 'Moderado';
            if ($value >= 27 && $value <= 40) return 'Alto';
            return 'Desconocido';
        }

        function getResilienciaDescription($value)
        {
            if ($value === null || $value === '') return 'N/A';
            $value = (int)$value;
            if ($value >= 0 && $value <= 15) return 'Baja';
            if ($value >= 26 && $value <= 50) return 'Media';
            if ($value >= 51 && $value <= 76) return 'Alta';
            return 'Desconocido';
        }

        function getTrabajoEquipoDescription($value)
        {
            if ($value === null || $value === '') return 'N/A';
            $value = (int)$value;
            switch ($value) {
                case 1:
                    return 'Líder';
                case 2:
                    return 'Coordinador';
                case 3:
                    return 'Observador';
                case 4:
                    return 'Ejecutor';
                default:
                    return 'Desconocido';
            }
        }

        function getResponsabilidadDescription($value)
        {
            if ($value === null || $value === '') return 'N/A';
            $value = (int)$value;
            switch ($value) {
                case 1:
                    return 'Bajo';
                case 2:
                    return 'Medio';
                case 3:
                    return 'Alto';
                default:
                    return 'Desconocido';
            }
        }

        function getDisciplinaDescription($value)
        {
            if ($value === null || $value === '') return 'N/A';
            $value = (int)$value;
            if ($value >= 0 && $value <= 10) return 'Baja';
            if ($value >= 11 && $value <= 20) return 'Media';
            if ($value >= 21 && $value <= 30) return 'Alta';
            return 'Desconocido';
        }

        function getTomaDecisionesDescription($value)
        {
            if ($value === null || $value === '') return 'N/A';
            $value = (int)$value;
            switch ($value) {
                case 1:
                    return 'Bajo';
                case 2:
                    return 'Medio';
                case 3:
                    return 'Alto';
                default:
                    return 'Desconocido';
            }
        }

        function getEmpatiaDescription($value)
        {
            if ($value === null || $value === '') return 'N/A';
            $value = (int)$value;
            if ($value >= 0 && $value <= 8) return 'Baja';
            if ($value >= 9 && $value <= 18) return 'Media';
            if ($value >= 19 && $value <= 28) return 'Alta';
            return 'Desconocido';
        }
        ?>

        <?php if (!empty($evaluaciones)): ?>
            <?php foreach ($evaluaciones as $eval): ?>
                <table>
                    <tr>
                        <th>Estabilidad Emocional</th>
                        <th>Manejo del Estrés</th>
                        <th>Resiliencia</th>
                        <th>Trabajo en Equipo</th>
                        <th>Vocación de Servicio</th>
                    </tr>
                    <tr>
                        <td><?= htmlspecialchars(getEstabilidadEmocionalDescription($eval['EE'] ?? null)) ?></td>
                        <td><?= htmlspecialchars(getManejoEstresDescription($eval['ME'] ?? null)) ?></td>
                        <td><?= htmlspecialchars(getResilienciaDescription($eval['RE'] ?? null)) ?></td>
                        <td><?= htmlspecialchars(getTrabajoEquipoDescription($eval['TE'] ?? null)) ?></td>
                        <td><?= htmlspecialchars($eval['VS'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Responsabilidad</th>
                        <th>Disciplina</th>
                        <th>Toma de decisiones</th>
                        <th>Empatía</th>
                        <th>Conclusión</th>
                    </tr>
                    <tr>
                        <td><?= htmlspecialchars(getResponsabilidadDescription($eval['RS'] ?? null)) ?></td>
                        <td><?= htmlspecialchars(getDisciplinaDescription($eval['DI'] ?? null)) ?></td>
                        <td><?= htmlspecialchars(getTomaDecisionesDescription($eval['TD'] ?? null)) ?></td>
                        <td><?= htmlspecialchars(getEmpatiaDescription($eval['EM'] ?? null)) ?></td>
                        <td><?= htmlspecialchars($eval['CON'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th colspan="5" style="text-align: left;">Recomendación:</th>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: left;"><?= htmlspecialchars($eval['Reco'] ?? '') ?></td>
                    </tr>
                </table>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No Evaluado.</p>
            <?php if (isset($errorEvaluaciones)): ?>
                <p class="no-print" style="color: red; font-size: 0.8em;">Debug: <?= htmlspecialchars($errorEvaluaciones) ?></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>Generado el <?= date('d/m/Y') ?></p>
        <p>SBRAB - La Paz, Bolivia</p>
    </div>

    <script>
        // Opcional: imprimir automáticamente al cargar
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>