
<?php
session_start();
include('db.php');

// Verificar si el usuario está logueado y tiene el rol de profesor
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: login.php");
    exit;
}

// Obtener el ID del profesor
$profesor_id = $_SESSION['usuario_id'];

// Consulta para obtener materias asignadas organizadas por nivel
$query_materias = "
    SELECT n.nivel_nombre, m.nombre AS materia_nombre, m.materia_id
    FROM profesor_materia pm
    JOIN materias m ON pm.materia_id = m.materia_id
    JOIN niveles n ON m.nivel_id = n.nivel_id
    WHERE pm.profesor_id = :profesor_id
    ORDER BY n.nivel_nombre, m.nombre";
$stmt_materias = $pdo->prepare($query_materias);
$stmt_materias->execute(['profesor_id' => $profesor_id]);
$materias_por_nivel = $stmt_materias->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Profesor</title>
    <link rel="stylesheet" href="CSS/dashboard_profesor.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Para los íconos -->
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Panel de Profesor</h1>
            <div class="navbar-right">
                <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        <!-- Sección de bienvenida -->
        <section class="welcome-section">
            <h2>Bienvenid@ al Panel de Profesor</h2>
            <p>Desde aquí puedes gestionar tu información personal, consultar tus materias asignadas y acceder a otros recursos para facilitar tu labor docente.</p>
        </section>

        <!-- Botones de control -->
        <div class="button-container">
            <a href="ver_perfil.php" class="control-button">
                <i class="bi bi-person"></i>
                <span>Mi Perfil</span>
            </a>
            <a href="consultar_materia_detalle.php" class="control-button">
                <i class="bi bi-book"></i>
                <span>Mis Materias</span>
            </a>
            <a href="consultar_calendario.php" class="control-button">
                <i class="bi bi-calendar-event"></i>
                <span>Mi Calendario</span>
            </a>
            <a href="reportes_profesor.php" class="control-button">
                <i class="bi bi-bar-chart"></i>
                <span>Mis Reportes</span>
            </a>
        </div>

        <!-- Sección de materias asignadas -->
        <section>
            <h2>Mis Materias</h2>
            <?php foreach ($materias_por_nivel as $nivel => $materias): ?>
                <h3><?php echo htmlspecialchars($nivel); ?>:</h3>
                <div class="materias-container">
                    <?php foreach ($materias as $materia): ?>
                        <button class="materia-button" data-materia-id="<?php echo $materia['materia_id']; ?>">
                            <?php echo htmlspecialchars($materia['materia_nombre']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </section>
    </main>

    <script>
        // Evento para los botones de materias
        document.querySelectorAll('.materia-button').forEach(button => {
            button.addEventListener('click', () => {
                const materiaId = button.getAttribute('data-materia-id');
                // Redirigir a una página para consultar más información de la materia
                window.location.href = `consultar_materia_detalle.php?materia_id=${materiaId}`;
            });
        });
    </script>
</body>
</html>
