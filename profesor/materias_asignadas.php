<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de profesor
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: ../login.php");
    exit;
}

// Obtener el nombre del profesor desde la sesión
$nombre_profesor = $_SESSION['nombre'];
$profesor_id = $_SESSION['usuario_id'];

// Incluir la conexión a la base de datos
require_once '../db.php';

// Obtener el periodo activo
$sqlPeriodo = "SELECT periodo_id, nombre FROM periodos WHERE activo = 1 LIMIT 1";
$stmtPeriodo = $pdo->query($sqlPeriodo);
$periodo = $stmtPeriodo->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die("No hay un periodo activo en este momento.");
}
$periodo_id = $periodo['periodo_id'];

// Obtener las materias asignadas al profesor en el periodo activo
$sqlMaterias = "
    SELECT m.nombre AS materia, g.grado AS grupo, g.turno, n.nivel_nombre AS nivel
    FROM profesor_materia pm
    JOIN materias m ON pm.materia_id = m.materia_id
    JOIN materia_grupo mg ON m.materia_id = mg.materia_id
    JOIN grupos g ON mg.grupo_id = g.id_grupo
    JOIN niveles n ON m.nivel_id = n.nivel_id
    WHERE pm.profesor_id = ? AND pm.periodo_id = ?
";
$stmtMaterias = $pdo->prepare($sqlMaterias);
$stmtMaterias->execute([$profesor_id, $periodo_id]);
$materias = $stmtMaterias->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materias Asignadas</title>
    <link rel="stylesheet" href="CSS/dashboard_profesor.css">
</head>
<body>
    <header class="navbar">
        <div class="navbar-left">
            <span>Panel de Control del Profesor</span>
        </div>
        <div class="navbar-right">
            <span>Bienvenido, <?php echo htmlspecialchars($nombre_profesor); ?></span>
            <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
        </div>
    </header>

    <main class="main-container">
        <section class="welcome-section">
            <h1>Materias Asignadas</h1>
            <p>Estas son las materias que tienes asignadas en el periodo activo: <strong><?php echo htmlspecialchars($periodo['nombre']); ?></strong></p>
        </section>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Grupo</th>
                        <th>Turno</th>
                        <th>Nivel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($materias as $materia): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($materia['materia']); ?></td>
                            <td><?php echo htmlspecialchars($materia['grupo']); ?></td>
                            <td><?php echo htmlspecialchars($materia['turno']); ?></td>
                            <td><?php echo htmlspecialchars($materia['nivel']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($materias)): ?>
                        <tr>
                            <td colspan="4">No tienes materias asignadas en este periodo.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
