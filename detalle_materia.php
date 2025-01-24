<?php
session_start();
include('db.php');

// Verificar si el usuario está logueado y tiene el rol de profesor
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: login.php");
    exit;
}

try {
    // Validar que se recibió un ID de materia válido
    if (!isset($_GET['materia_id']) || !is_numeric($_GET['materia_id'])) {
        throw new Exception("ID de materia inválido.");
    }

    $materia_id = $_GET['materia_id'];

    // Obtener la información general de la materia
    $stmt_materia = $pdo->prepare("
        SELECT m.nombre AS materia_nombre, n.nivel_nombre
        FROM materias m
        JOIN niveles n ON m.nivel_id = n.nivel_id
        WHERE m.materia_id = :materia_id
    ");
    $stmt_materia->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
    $stmt_materia->execute();
    $materia = $stmt_materia->fetch(PDO::FETCH_ASSOC);

    if (!$materia) {
        throw new Exception("Materia no encontrada.");
    }

    // Obtener los rasgos de evaluación de la materia
    $stmt_rasgos = $pdo->prepare("
        SELECT r.nombre AS rasgo_nombre, mr.porcentaje
        FROM materia_rasgo mr
        JOIN rasgos r ON mr.rasgo_id = r.rasgo_id
        WHERE mr.materia_id = :materia_id
    ");
    $stmt_rasgos->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
    $stmt_rasgos->execute();
    $rasgos = $stmt_rasgos->fetchAll(PDO::FETCH_ASSOC);

    // Obtener los alumnos inscritos en la materia y sus calificaciones
    $stmt_alumnos = $pdo->prepare("
        SELECT a.alumno_id, a.nombres, a.apellidos, c.calificacion, r.nombre AS rasgo_nombre
        FROM calificaciones c
        JOIN alumnos a ON c.alumno_id = a.alumno_id
        JOIN rasgos r ON c.rasgo_id = r.rasgo_id
        WHERE c.materia_id = :materia_id
        ORDER BY a.apellidos, a.nombres, r.nombre
    ");
    $stmt_alumnos->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
    $stmt_alumnos->execute();
    $alumnos = $stmt_alumnos->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Materia</title>
    <link rel="stylesheet" href="CSS/detalle_materia.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Detalle de Materia</h1>
            <div class="navbar-right">
                <a href="materias_profesor.php" class="back-button">
                    <i class="bi bi-arrow-left-circle"></i> Regresar
                </a>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        <!-- Información de la Materia -->
        <section class="materia-info">
            <h2>Materia: <?php echo htmlspecialchars($materia['materia_nombre']); ?></h2>
            <p><strong>Nivel:</strong> <?php echo htmlspecialchars($materia['nivel_nombre']); ?></p>
        </section>

        <!-- Rasgos de Evaluación -->
        <section class="rasgos-info">
            <h3>Rasgos de Evaluación</h3>
            <?php if (count($rasgos) > 0): ?>
                <ul>
                    <?php foreach ($rasgos as $rasgo): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($rasgo['rasgo_nombre']); ?>:</strong>
                            <?php echo htmlspecialchars($rasgo['porcentaje']); ?>%
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No hay rasgos definidos para esta materia.</p>
            <?php endif; ?>
        </section>

        <!-- Alumnos Inscritos -->
        <section class="alumnos-info">
            <h3>Alumnos Inscritos</h3>
            <?php if (count($alumnos) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Rasgo</th>
                            <th>Calificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alumnos as $alumno): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($alumno['apellidos'] . ", " . $alumno['nombres']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['rasgo_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['calificacion']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay alumnos inscritos en esta materia.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
